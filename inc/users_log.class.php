<?
class users_log extends db {
    
    // Status codes as array inside class
    private $status_codes = [
        'success' => [
            'description' => 'Login successful',
            'is_success' => true,
            'requires_action' => false,
            'security_level' => 0
        ],
        'success_direct_code' => [
            'description' => 'Login successful by direct_code',
            'is_success' => true,
            'requires_action' => false,
            'security_level' => 0
        ],
        'wrong_direct_code' => [
            'description' => 'Incorrect direct_code',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 1
        ],
        'wrong_password' => [
            'description' => 'Incorrect password',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 1
        ],
        'user_not_found' => [
            'description' => 'Username does not exist',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 1
        ],
        'fl_login_off' => [
            'description' => 'fl_login in CRM is off',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 1
        ],
        'account_locked' => [
            'description' => 'Account is locked',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 2
        ],
        'account_inactive' => [
            'description' => 'Account is inactive',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 1
        ],
        '2fa_required' => [
            'description' => '2FA required',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 0
        ],
        '2fa_failed' => [
            'description' => '2FA code incorrect',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 1
        ],
        'password_expired' => [
            'description' => 'Password needs reset',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 1
        ],
        'ip_blocked' => [
            'description' => 'IP address blocked',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 2
        ],
        'rate_limited' => [
            'description' => 'Too many attempts',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 2
        ],
        'maintenance' => [
            'description' => 'System maintenance',
            'is_success' => false,
            'requires_action' => true,
            'security_level' => 0
        ],
        'session_exists' => [
            'description' => 'Already logged in',
            'is_success' => false,
            'requires_action' => false,
            'security_level' => 0
        ]
    ];
    
    public function __construct($database) {
        //parent::__construct(); // Call parent db constructor
        $this->connect($database);
        $this->create_log_table();
    }
    
    private function create_log_table() {
        // Create users_log table with all necessary columns
        $this->connect($this->database,true);
        $this->query("
            CREATE TABLE IF NOT EXISTS users_log (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL DEFAULT 0,
                username VARCHAR(50) NOT NULL,
                attempt_at DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
                attempt_ip VARCHAR(45) DEFAULT NULL,
                auth_method VARCHAR(20) DEFAULT 'password',
                success BOOLEAN NOT NULL DEFAULT false,
                status_code VARCHAR(20) NOT NULL,
                status_message VARCHAR(255) DEFAULT NULL,
                user_agent TEXT DEFAULT NULL,
                device_hash VARCHAR(64) DEFAULT NULL,
                session_id VARCHAR(128) DEFAULT NULL,
                session_created BOOLEAN DEFAULT false,
                response_time_ms INT UNSIGNED DEFAULT NULL,
                brute_force_flag BOOLEAN DEFAULT false,
                redirect_to VARCHAR(255) DEFAULT NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_username (username),
                INDEX idx_attempt_at (attempt_at),
                INDEX idx_ip (attempt_ip(15)),
                INDEX idx_success_status (success, status_code),
                INDEX idx_brute_force (brute_force_flag, attempt_at),
                INDEX idx_session (session_id(32))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            COMMENT='Log of all user login attempts'
        ");
        $this->connect($this->database,true);
    }
    
    // Main logging method
	// Main logging method with integrated brute force detection
	public function log_attempt($username, $user_id = null, $success = false, $status = '', $message = '', $auth_method='password', $session_id = null) {
		global $ctrl_id;
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		
		$device_hash = $this->generate_device_hash($user_agent, $ip);
		
		// Validate status code
		if (empty($status) || !$this->is_valid_status($status)) {
			$status = $success ? 'success' : 'wrong_password';
		}
		
		// Get status message if not provided
		if (empty($message)) {
			$message = $this->get_status_description($status);
		}
		
		// Check for brute force BEFORE logging (for failed attempts only)
		$brute_force_flag = false;
		$should_block = false;
		
		if (!$success) {
			$brute_force_check = $this->check_brute_force_enhanced($username, $ip);
			$brute_force_flag = $brute_force_check['is_brute_force'];
			$should_block = $brute_force_check['should_block'];
			
			// If brute force detected, override status
			if ($brute_force_flag && $status != 'rate_limited') {
				$status = 'rate_limited';
				$message = 'Brute force protection triggered - too many failed attempts';
			}
		}
		
		// Use parent's escape() method
		$username_esc = $this->escape($username);
		$message_esc = $this->escape($message);
		$user_agent_esc = $this->escape($user_agent);
		
		$sql = "INSERT INTO users_log 
				(user_id, username, attempt_ip, auth_method, success, status_code, status_message, 
				 user_agent, device_hash, session_id, session_created, brute_force_flag) 
				VALUES 
				('" . ($user_id ?: 0) . "', 
				 '$username_esc', 
				 '$ip',
				 '$auth_method', 
				 " . ($success ? 1 : 0) . ", 
				 '$status', 
				 '$message_esc',
				 '$user_agent_esc', 
				 '$device_hash',
				 " . ($session_id ? "'$session_id'" : "NULL") . ", 
				 " . ($session_id ? 1 : 0) . ", 
				 " . ($brute_force_flag ? 1 : 0) . ")";
		
		$this->query($sql);
		$log_id = $this->insert_id();
		
		// Auto-lock account if too many failed attempts for a specific user
		if (!$success && $user_id && $brute_force_flag) {
			$this->check_and_lock_account($user_id, $username);
			$this->notify_me("log_attempt - check_and_lock_account - account locked. ctrl_id=$ctrl_id log_id=$log_id status=$status message=$message");
		}
		if($brute_force_check['ip_attempts']>10) {
			$this->notify_me("log_attempt - ip_attempts>10 - account locked by ip. ctrl_id=$ctrl_id log_id=$log_id status=$status message=$message");
		}

		if($brute_force_flag) {
			//~ header('HTTP/1.1 429 Too Many Requests');
			//~ die("Too many failed attempts. Please wait 15 minutes.");
			$this->notify_me("log_attempt - brut_force detected. ctrl_id=$ctrl_id log_id=$log_id status=$status message=$message");
		}
		if($should_block) {
			//~ header('HTTP/1.1 429 Too Many Requests');
			//~ die("Too many failed attempts. Please wait 15 minutes.");
			//$this->notify_me("log_attempt - brut_force detected. SHOULD BLOCK $log_id $status $message");
		}

		$this->clean_old_logs(90);

		return [
			'log_id' => $log_id,
			'brute_force_detected' => $brute_force_flag,
			'should_block' => $should_block,
			'status_code' => $status,
			'message' => $message
		];
	}

	// Enhanced brute force check method with multiple thresholds
	private function check_brute_force_enhanced($username, $ip, $time_window_minutes = 5, $max_attempts = 5) {
		$username_esc = $this->escape($username);
		
		// Check 1: Username-specific attempts in last 5 minutes
		$sql_username = "SELECT COUNT(*) as attempts 
				FROM users_log 
				WHERE username = '$username_esc'
				  AND success = 0 
				  AND attempt_at > DATE_SUB(NOW(), INTERVAL $time_window_minutes MINUTE)
				  AND status_code NOT IN ('rate_limited', 'ip_blocked')";
		
		$result_username = $this->fetch_assoc($this->query($sql_username));
		$username_attempts = $result_username['attempts'] ?? 0;
		
		// Check 2: IP-specific attempts in last 5 minutes
		$sql_ip = "SELECT COUNT(*) as attempts 
				FROM users_log 
				WHERE attempt_ip = '$ip'
				  AND success = 0 
				  AND attempt_at > DATE_SUB(NOW(), INTERVAL $time_window_minutes MINUTE)
				  AND status_code NOT IN ('rate_limited', 'ip_blocked')";
		
		$result_ip = $this->fetch_assoc($this->query($sql_ip));
		$ip_attempts = $result_ip['attempts'] ?? 0;
		
		// Check 3: IP targeting multiple users in last 10 minutes
		$sql_ip_users = "SELECT COUNT(DISTINCT username) as unique_users
				   FROM users_log 
				   WHERE attempt_ip = '$ip'
					 AND success = 0 
					 AND attempt_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
		
		$result_ip_users = $this->fetch_assoc($this->query($sql_ip_users));
		$unique_users = $result_ip_users['unique_users'] ?? 0;
		
		// Check 4: Username attempts in last hour
		$sql_hour = "SELECT COUNT(*) as hour_attempts 
					 FROM users_log 
					 WHERE username = '$username_esc'
					   AND success = 0 
					   AND attempt_at > DATE_SUB(NOW(), INTERVAL 60 MINUTE)";
		
		$result_hour = $this->fetch_assoc($this->query($sql_hour));
		$hour_attempts = $result_hour['hour_attempts'] ?? 0;
		
		// Determine brute force based on multiple thresholds
		$is_brute_force = false;
		$should_block = false;
		
		// Threshold 1: Rapid attempts on single username (5 in 5 minutes)
		if ($username_attempts >= $max_attempts) {
			$is_brute_force = true;
			$should_block = true;
		}
		
		// Threshold 2: Rapid attempts from single IP (10 in 5 minutes)
		if ($ip_attempts >= 10) {
			$is_brute_force = true;
			$should_block = true;
		}
		
		// Threshold 3: IP attacking multiple users (3+ users in 10 minutes)
		if ($unique_users >= 3) {
			$is_brute_force = true;
			$should_block = true;
		}
		
		// Threshold 4: Sustained attacks on username (15 in 1 hour)
		if ($hour_attempts >= 15) {
			$is_brute_force = true;
			$should_block = true;
		}
		
		return [
			'is_brute_force' => $is_brute_force,
			'should_block' => $should_block,
			'username_attempts' => $username_attempts,
			'ip_attempts' => $ip_attempts,
			'unique_users' => $unique_users,
			'hour_attempts' => $hour_attempts
		];
	}

	// Auto-lock account method for excessive failed attempts
	private function check_and_lock_account($user_id, $username) {
		$username_esc = $this->escape($username);
		
		// Check failed attempts for this user in last 30 minutes
		$sql = "SELECT COUNT(*) as attempts 
				FROM users_log 
				WHERE username = '$username_esc'
				  AND user_id = '$user_id'
				  AND success = 0 
				  AND attempt_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
				  AND status_code NOT IN ('rate_limited', 'ip_blocked')";
		
		$result = $this->fetch_assoc($this->query($sql));
		$attempts = $result['attempts'] ?? 0;
		
		// Auto-lock after 20 failed attempts in 30 minutes
		if ($attempts >= 20) {
			$tm=time()+(15*60*60);
			$this->query("UDATE users SET tm_locked='$tm' WHERE id='$user_id'");
			// Log the auto-lock as a separate entry
			$this->log_attempt(
				$username,
				$user_id,
				false,
				'account_locked',
				'Account auto-locked due to excessive failed attempts',
				'system',
				null
			);
			
			return true;
		}
		
		return false;
	}

	// Keep the old check_brute_force method for backward compatibility
	private function check_brute_force($username, $ip) {
		$check = $this->check_brute_force_enhanced($username, $ip);
		return $check['is_brute_force'];
	}
    
    // Helper logging methods
    public function log_login_success($user_id, $username, $session_id = null) {
        return $this->log_attempt($username, $user_id, true, 'success', 'Login successful', $session_id);
    }
    
    public function log_login_failure($username, $status = 'wrong_password', $message = '') {
        // Try to get user_id if username exists
        $username_esc = $this->escape($username);
        $user = $this->fetch_assoc($this->query("SELECT id FROM users WHERE username = '$username_esc'"));
        $user_id = $user['id'] ?? null;
        
        return $this->log_attempt($username, $user_id, false, $status, $message);
    }
    
    public function log_login_failure_by_user_id($user_id, $username, $status = 'wrong_password', $message = '') {
        return $this->log_attempt($username, $user_id, false, $status, $message);
    }
    
    // Status management methods
    public function is_valid_status($status_code) {
        return isset($this->status_codes[$status_code]);
    }
    
    public function get_status_info($status_code) {
        return $this->status_codes[$status_code] ?? null;
    }
    
    public function get_status_description($status_code) {
        return $this->status_codes[$status_code]['description'] ?? 'Unknown status';
    }
    
    public function get_security_level($status_code) {
        return $this->status_codes[$status_code]['security_level'] ?? 2;
    }
    
    public function is_success_status($status_code) {
        return $this->status_codes[$status_code]['is_success'] ?? false;
    }
    
    public function requires_action($status_code) {
        return $this->status_codes[$status_code]['requires_action'] ?? true;
    }
    
    public function get_all_status_codes() {
        return array_keys($this->status_codes);
    }
    
    public function get_success_status_codes() {
        $success_codes = [];
        foreach ($this->status_codes as $code => $info) {
            if ($info['is_success']) {
                $success_codes[] = $code;
            }
        }
        return $success_codes;
    }
    
    public function get_high_security_status_codes($level = 2) {
        $high_security_codes = [];
        foreach ($this->status_codes as $code => $info) {
            if ($info['security_level'] >= $level) {
                $high_security_codes[] = $code;
            }
        }
        return $high_security_codes;
    }
    
    // Add new status code dynamically
    public function add_status_code($code, $description, $is_success = false, $requires_action = false, $security_level = 0) {
        $this->status_codes[$code] = [
            'description' => $description,
            'is_success' => $is_success,
            'requires_action' => $requires_action,
            'security_level' => $security_level
        ];
        return true;
    }
    
    // Remove status code
    public function remove_status_code($code) {
        if (isset($this->status_codes[$code]) && !in_array($code, ['success', 'wrong_password', 'user_not_found'])) {
            unset($this->status_codes[$code]);
            return true;
        }
        return false;
    }
    
    // Formatting helpers
    public function get_status_color($status_code) {
        $security_level = $this->get_security_level($status_code);
        
        switch ($security_level) {
            case 0: return 'green';
            case 1: return 'orange';
            case 2: return 'red';
            default: return 'gray';
        }
    }
    
    public function format_login_status($status_code) {
        $info = $this->get_status_info($status_code);
        if (!$info) {
            return '<span class="status unknown">' . htmlspecialchars($status_code) . '</span>';
        }
        
        $color = $this->get_status_color($status_code);
        $icon = $info['is_success'] ? '✓' : ($info['security_level'] >= 2 ? '⚠' : '✗');
        
        return '<span class="status ' . $color . '" title="' . htmlspecialchars($info['description']) . '">' . 
               $icon . ' ' . htmlspecialchars($status_code) . '</span>';
    }
    
    public function get_status_badge_html($status_code) {
        $color = $this->get_status_color($status_code);
        $description = $this->get_status_description($status_code);
        
        return '<span class="badge badge-' . $color . '" title="' . htmlspecialchars($description) . '">' . 
               htmlspecialchars($status_code) . '</span>';
    }
    
    // Security helpers
    public function check_ip_threat_level($ip) {
        $ip_esc = $this->escape($ip);
        $sql = "SELECT 
                    COUNT(*) as total_attempts,
                    SUM(success = 1) as successful_logins,
                    SUM(success = 0) as failed_attempts,
                    COUNT(DISTINCT username) as unique_users
                FROM users_log 
                WHERE attempt_ip = '$ip_esc'
                  AND attempt_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        
        return $this->fetch_assoc($this->query($sql));
    }
    
    public function is_ip_suspicious($ip, $threshold = 10) {
        $stats = $this->check_ip_threat_level($ip);
        return ($stats['failed_attempts'] ?? 0) > $threshold;
    }
    
    public function get_user_attempts_count($username, $minutes = 5) {
        $username_esc = $this->escape($username);
        $sql = "SELECT COUNT(*) as attempts 
                FROM users_log 
                WHERE username = '$username_esc'
                  AND attempt_at > DATE_SUB(NOW(), INTERVAL $minutes MINUTE)";
        
        $result = $this->fetch_assoc($this->query($sql));
        return $result['attempts'] ?? 0;
    }
    
    // Original methods
    private function generate_device_hash($user_agent, $ip) {
        $fingerprint = $user_agent . $ip;
        return hash('sha256', $fingerprint);
    }
    

    public function get_recent_attempts($username, $limit = 10) {
        $username_esc = $this->escape($username);
        $sql = "SELECT * FROM users_log 
                WHERE username = '$username_esc'
                ORDER BY attempt_at DESC 
                LIMIT $limit";
        
        $result = $this->query($sql);
        $attempts = [];
        while ($row = $this->fetch_assoc($result)) {
            $attempts[] = $row;
        }
        return $attempts;
    }
    
    public function get_user_login_stats($user_id, $days = 30) {
        $sql = "SELECT 
                    DATE(attempt_at) as login_date,
                    COUNT(*) as total_attempts,
                    SUM(success = 1) as successful_logins,
                    SUM(success = 0) as failed_attempts
                FROM users_log 
                WHERE user_id = $user_id 
                  AND attempt_at > DATE_SUB(CURDATE(), INTERVAL $days DAY)
                GROUP BY DATE(attempt_at)
                ORDER BY login_date DESC";
        
        $result = $this->query($sql);
        $stats = [];
        while ($row = $this->fetch_assoc($result)) {
            $stats[] = $row;
        }
        return $stats;
    }
    
    public function clean_old_logs($days_to_keep = 90) {
        $sql = "DELETE FROM users_log 
                WHERE attempt_at < DATE_SUB(NOW(), INTERVAL $days_to_keep DAY)";
        $this->query($sql);
        return $this->affected_rows;
    }
    
    // Additional utility methods
    public function get_daily_login_stats($start_date = null, $end_date = null) {
        $where = '';
        if ($start_date) {
            $start_date_esc = $this->escape($start_date);
            $where .= " AND attempt_at >= '$start_date_esc'";
        }
        if ($end_date) {
            $end_date_esc = $this->escape($end_date);
            $where .= " AND attempt_at <= '$end_date_esc'";
        }
        
        $sql = "SELECT 
                    DATE(attempt_at) as date,
                    COUNT(*) as total_attempts,
                    SUM(success = 1) as successful_logins,
                    SUM(success = 0) as failed_logins,
                    COUNT(DISTINCT user_id) as unique_users
                FROM users_log 
                WHERE 1=1 $where
                GROUP BY DATE(attempt_at)
                ORDER BY date DESC";
        
        $result = $this->query($sql);
        $stats = [];
        while ($row = $this->fetch_assoc($result)) {
            $stats[] = $row;
        }
        return $stats;
    }
    
    public function get_top_failed_ips($limit = 10, $hours = 24) {
        $sql = "SELECT 
                    attempt_ip,
                    COUNT(*) as failed_attempts,
                    COUNT(DISTINCT username) as unique_users
                FROM users_log 
                WHERE success = 0 
                  AND attempt_at > DATE_SUB(NOW(), INTERVAL $hours HOUR)
                GROUP BY attempt_ip
                ORDER BY failed_attempts DESC
                LIMIT $limit";
        
        $result = $this->query($sql);
        $ips = [];
        while ($row = $this->fetch_assoc($result)) {
            $ips[] = $row;
        }
        return $ips;
    }
    
    public function export_logs_to_csv($start_date, $end_date) {
        $start_date_esc = $this->escape($start_date);
        $end_date_esc = $this->escape($end_date);
        
        $sql = "SELECT * FROM users_log 
                WHERE attempt_at BETWEEN '$start_date_esc' AND '$end_date_esc'
                ORDER BY attempt_at";
        
        $result = $this->query($sql);
        
        $csv = "id,user_id,username,attempt_at,attempt_ip,success,status_code,status_message,user_agent\n";
        
        while ($row = $this->fetch_assoc($result)) {
            $csv .= sprintf(
                '%d,%d,"%s","%s","%s",%d,"%s","%s","%s"' . "\n",
                $row['id'],
                $row['user_id'],
                $row['username'],
                $row['attempt_at'],
                $row['attempt_ip'],
                $row['success'],
                $row['status_code'],
                str_replace('"', '""', $row['status_message']),
                str_replace('"', '""', $row['user_agent'])
            );
        }
        
        return $csv;
    }
}
?>
