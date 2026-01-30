<?php
// master_key_server.php
header('Content-Type: application/json');

class BasicAuthAPI {
    private $master_key = 'my_super_secret_master_key_12345';
    
    public function __construct() {
        // You can set master key from environment in production
        // $this->master_key = getenv('API_MASTER_KEY');
    }
    
    public function authenticate() {
        $auth_header = $this->getAuthorizationHeader();
        
        if (!$auth_header) {
            $this->sendAuthRequired('Authorization header missing');
            return false;
        }
        
        if (strpos($auth_header, 'Basic ') !== 0) {
            $this->sendAuthRequired('Invalid authentication format');
            return false;
        }
        
        $base64_credentials = substr($auth_header, 6);
        $credentials = base64_decode($base64_credentials);
        
        if ($credentials === false) {
            $this->sendAuthRequired('Invalid base64 encoding');
            return false;
        }
        
        list($client_id, $client_secret) = explode(':', $credentials, 2);
        
        // Validate using master key derivation
        if ($this->validateWithMasterKey($client_id, $client_secret)) {
            return [
                'client_id' => $client_id,
                'authenticated' => true,
                'timestamp' => time(),
                'permissions' => $this->getClientPermissions($client_id)
            ];
        } else {
            $this->sendAuthRequired('Invalid client ID or secret');
            return false;
        }
    }
    
    private function validateWithMasterKey($client_id, $client_secret) {
        // Derive expected secret for this client from master key
        $expected_secret = $this->deriveClientSecret($client_id);
        
        // Use hash_equals to prevent timing attacks
        return hash_equals($expected_secret, $client_secret);
    }
    
    private function deriveClientSecret($client_id) {
        // Use HMAC to derive client-specific secret from master key
        return hash_hmac('sha256', $client_id, $this->master_key);
    }
    
    private function getClientPermissions($client_id) {
        // Define permissions based on client ID pattern or specific rules
        $permissions = [
            'read_data' => true,
            'write_data' => false,
            'delete_data' => false,
            'admin_access' => false
        ];
        
        // Example: clients with "admin" in ID get more permissions
        if (strpos($client_id, 'admin') !== false) {
            $permissions['write_data'] = true;
            $permissions['admin_access'] = true;
        }
        
        // Example: clients with "write" in ID get write permissions
        if (strpos($client_id, 'write') !== false) {
            $permissions['write_data'] = true;
        }
        
        return $permissions;
    }
    
    private function getAuthorizationHeader() {
        $headers = null;
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }
    
    private function sendAuthRequired($message = 'Authentication required') {
        header('WWW-Authenticate: Basic realm="API Access", charset="UTF-8"');
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode([
            'error' => true,
            'message' => $message,
            'code' => 401,
            'timestamp' => time()
        ]);
        exit;
    }
    
    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'error' => true,
            'message' => $message,
            'code' => $code,
            'timestamp' => time()
        ]);
        exit;
    }
    
    private function sendSuccess($data = [], $message = 'Success') {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ]);
        exit;
    }
    
    // API Endpoints
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Authenticate the request
        $auth_result = $this->authenticate();
        
        if (!$auth_result) {
            return; // Authentication failed
        }
        
        // Route the request
        switch ($path) {
            case '/api/data':
                $this->handleDataEndpoint($method, $auth_result);
                break;
                
            case '/api/users':
                $this->handleUsersEndpoint($method, $auth_result);
                break;
                
            case '/api/admin':
                $this->handleAdminEndpoint($method, $auth_result);
                break;
                
            case '/api/health':
                $this->handleHealthEndpoint($auth_result);
                break;
                
            case '/api/client-info':
                $this->handleClientInfoEndpoint($auth_result);
                break;
                
            default:
                $this->sendError('Endpoint not found', 404);
        }
    }
    
    private function handleDataEndpoint($method, $auth) {
        switch ($method) {
            case 'GET':
                if (!$auth['permissions']['read_data']) {
                    $this->sendError('Insufficient permissions', 403);
                }
                
                $data = [
                    'protected_data' => 'This is sensitive information',
                    'access_time' => date('c'),
                    'accessed_by' => $auth['client_id'],
                    'records' => [
                        ['id' => 1, 'name' => 'Record 1', 'value' => 100],
                        ['id' => 2, 'name' => 'Record 2', 'value' => 200],
                        ['id' => 3, 'name' => 'Record 3', 'value' => 300]
                    ]
                ];
                
                $this->sendSuccess($data, 'Data retrieved successfully');
                break;
                
            case 'POST':
                if (!$auth['permissions']['write_data']) {
                    $this->sendError('Insufficient permissions', 403);
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['name']) || !isset($input['value'])) {
                    $this->sendError('Name and value are required', 400);
                }
                
                $new_record = [
                    'id' => rand(1000, 9999),
                    'name' => $input['name'],
                    'value' => $input['value'],
                    'created_by' => $auth['client_id'],
                    'created_at' => time()
                ];
                
                $this->sendSuccess($new_record, 'Record created successfully');
                break;
                
            default:
                $this->sendError('Method not allowed', 405);
        }
    }
    
    private function handleUsersEndpoint($method, $auth) {
        if ($method !== 'GET') {
            $this->sendError('Method not allowed', 405);
        }
        
        if (!$auth['permissions']['read_data']) {
            $this->sendError('Insufficient permissions', 403);
        }
        
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'user'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'user'],
            ['id' => 3, 'name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'admin']
        ];
        
        $this->sendSuccess(['users' => $users], 'Users retrieved successfully');
    }
    
    private function handleAdminEndpoint($method, $auth) {
        if (!$auth['permissions']['admin_access']) {
            $this->sendError('Admin access required', 403);
        }
        
        if ($method !== 'GET') {
            $this->sendError('Method not allowed', 405);
        }
        
        $admin_data = [
            'server_status' => 'online',
            'active_clients' => 5,
            'memory_usage' => '45%',
            'uptime' => '7 days, 3 hours',
            'last_backup' => '2024-01-15 02:00:00'
        ];
        
        $this->sendSuccess($admin_data, 'Admin data retrieved');
    }
    
    private function handleHealthEndpoint($auth) {
        $health = [
            'status' => 'healthy',
            'timestamp' => time(),
            'version' => '1.0.0',
            'authenticated_client' => $auth['client_id']
        ];
        
        $this->sendSuccess($health, 'API is healthy');
    }
    
    private function handleClientInfoEndpoint($auth) {
        $client_info = [
            'client_id' => $auth['client_id'],
            'permissions' => $auth['permissions'],
            'authentication_time' => $auth['timestamp'],
            'session_duration' => time() - $auth['timestamp']
        ];
        
        $this->sendSuccess($client_info, 'Client information');
    }
    
    // Utility method to generate client secrets (for setup)
    public function generateClientSecret($client_id) {
        return $this->deriveClientSecret($client_id);
    }
    
    // Method to verify a client secret (for testing)
    public function verifyClientSecret($client_id, $client_secret) {
        return $this->validateWithMasterKey($client_id, $client_secret);
    }
}

// Client code for testing
class BasicAuthMasterKeyClient {
    private $base_url;
    private $client_id;
    private $client_secret;
    private $master_key;
    
    public function __construct($base_url, $client_id, $master_key) {
        $this->base_url = rtrim($base_url, '/');
        $this->client_id = $client_id;
        $this->master_key = $master_key;
        $this->client_secret = $this->deriveClientSecret();
    }
    
    private function deriveClientSecret() {
        return hash_hmac('sha256', $this->client_id, $this->master_key);
    }
    
    public function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->base_url . $endpoint;
        
        $credentials = $this->client_id . ':' . $this->client_secret;
        $auth_header = 'Basic ' . base64_encode($credentials);
        
        $options = [
            'http' => [
                'header' => [
                    'Authorization: ' . $auth_header,
                    'Content-Type: application/json',
                    'User-Agent: MasterKeyClient/1.0'
                ],
                'method' => $method,
                'timeout' => 10
            ]
        ];
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options['http']['content'] = json_encode($data);
        }
        
        $context = stream_context_create($options);
        
        try {
            $response = file_get_contents($url, false, $context);
            return [
                'success' => true,
                'data' => json_decode($response, true)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function getClientInfo() {
        return [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'secret_derived' => true
        ];
    }
}

// Demo and setup
class MasterKeyDemo {
    public static function run() {
        echo "=== Master Key Basic Auth Demo ===\n\n";
        
        $master_key = 'my_super_secret_master_key_12345';
        $api_url = 'http://localhost/master_key_server.php';
        
        // Test different clients
        $test_clients = [
            'web_app' => 'web_app_client',
            'mobile_app' => 'mobile_app_client', 
            'admin_panel' => 'admin_panel_client',
            'write_service' => 'write_service_client'
        ];
        
        foreach ($test_clients as $name => $client_id) {
            echo "Testing client: {$name} ({$client_id})\n";
            
            $client = new BasicAuthMasterKeyClient($api_url, $client_id, $master_key);
            $client_info = $client->getClientInfo();
            
            echo "Client Secret: " . substr($client_info['client_secret'], 0, 16) . "...\n";
            
            // Test health endpoint
            $result = $client->makeRequest('/api/health');
            
            if ($result['success']) {
                echo "✅ Health check: {$result['data']['message']}\n";
                
                // Test client info
                $result = $client->makeRequest('/api/client-info');
                if ($result['success']) {
                    $permissions = $result['data']['data']['permissions'];
                    echo "Permissions: " . json_encode($permissions) . "\n";
                }
            } else {
                echo "❌ Failed: {$result['error']}\n";
            }
            
            echo str_repeat("-", 50) . "\n\n";
        }
        
        // Test with invalid client
        echo "Testing with INVALID client:\n";
        $bad_client = new BasicAuthMasterKeyClient($api_url, 'invalid_client', 'wrong_master_key');
        $result = $bad_client->makeRequest('/api/health');
        
        if (!$result['success']) {
            echo "✅ Expected failure: {$result['error']}\n";
        } else {
            echo "❌ Unexpected success!\n";
        }
    }
    
    public static function generateClientSecrets() {
        $master_key = 'my_super_secret_master_key_12345';
        $api = new BasicAuthAPI();
        
        $clients = ['web_app', 'mobile_app', 'admin_panel', 'write_service'];
        
        echo "=== Client Secrets Generation ===\n\n";
        foreach ($clients as $client_id) {
            $secret = $api->generateClientSecret($client_id);
            echo "Client: {$client_id}\n";
            echo "Secret: {$secret}\n\n";
        }
    }
}

// Handle web requests or run demo
if (php_sapi_name() === 'cli') {
    // Command line usage
    if (isset($argv[1]) && $argv[1] === 'generate') {
        MasterKeyDemo::generateClientSecrets();
    } else {
        MasterKeyDemo::run();
    }
} else {
    // Web server usage
    $api = new BasicAuthAPI();
    $api->handleRequest();
}
?>
