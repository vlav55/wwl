<?php

class wa_boom {
    private string $access_token;
    private string $base_url = 'https://wa-boom.ru/api';
    private string $instance_id='';
    private string $phone;
    
    public function __construct(string $access_token,string $phone='') {
        $this->access_token = $access_token;
        $res=$this->get_accounts();
        print_r($res);
        if(!$phone) {
			$this->instance_id=$res[0]['instance_id'];
			$this->phone=$res[0]['phone'];
		} else {
			foreach($res as $r) {
				if($r['phone']==$phone) {
					$this->instance_id=$r['instance_id'];
					$this->phone=$r['phone'];
				}
			}
		}
    }
    public function get_accounts(): array {
		$url = $this->base_url . '/get_accounts?access_token=' . $this->access_token;
		
		$ch = curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_POST => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30
		]);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($error) {
			throw new Exception('curl error: ' . $error);
		}
		
		if ($http_code !== 200) {
			throw new Exception('http error: ' . $http_code . ' - ' . $response);
		}
		
		$data = json_decode($response, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('json decode error: ' . json_last_error_msg());
		}
		
		// Проверка на ошибку API
		if (isset($data['status']) && $data['status'] === 'error') {
			$error_code = $data['code'] ?? 'unknown';
			$error_message = $data['message'] ?? 'unknown error';
			throw new Exception('api error ' . $error_code . ': ' . $error_message);
		}
		
		return $data['data'];
	}
	
    public function create_instance(): array {
        $url = $this->base_url . '/create_instance?access_token=' . $this->access_token;
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('curl error: ' . $error);
        }
        
        if ($http_code !== 200) {
            throw new Exception('http error: ' . $http_code . ' - ' . $response);
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('json decode error: ' . json_last_error_msg());
        }
        
        // Проверка на ошибку API
        if (isset($data['status']) && $data['status'] === 'error') {
            $error_code = $data['code'] ?? 'unknown';
            $error_message = $data['message'] ?? 'unknown error';
            throw new Exception('api error ' . $error_code . ': ' . $error_message);
        }
        
        return $data;
    }
	public function send_msg(string $phone, string $message): array {
		$url = $this->base_url . '/send';
		
		$post_data = [
			'number' => $phone,
			'type' => 'text',
			'message' => $message,
			'instance_id' => $this->instance_id,
			'access_token' => $this->access_token
		];
		
		$ch = curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($post_data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json'
			]
		]);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($error) {
			throw new Exception('curl error: ' . $error);
		}
		
		if ($http_code !== 200) {
			throw new Exception('http error: ' . $http_code . ' - ' . $response);
		}
		
		$data = json_decode($response, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('json decode error: ' . json_last_error_msg());
		}
		
		// Проверка на ошибку API
		if (isset($data['status']) && $data['status'] === 'error') {
			$error_code = $data['code'] ?? 'unknown';
			$error_message = $data['message'] ?? 'unknown error';
			throw new Exception('api error ' . $error_code . ': ' . $error_message);
		}
		
		return $data;
	}
	public function send_media(string $phone, string $message, string $media_url, ?string $filename = null): array {
		$url = $this->base_url . '/send';
		
		$post_data = [
			'number' => $phone,
			'type' => 'media',
			'message' => $message,
			'media_url' => $media_url,
			'instance_id' => $this->instance_id,
			'access_token' => $this->access_token
		];
		
		// Добавляем filename только если он указан (для документов)
		if ($filename !== null) {
			$post_data['filename'] = $filename;
		}
		
		$ch = curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($post_data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json'
			]
		]);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($error) {
			throw new Exception('curl error: ' . $error);
		}
		
		if ($http_code !== 200) {
			throw new Exception('http error: ' . $http_code . ' - ' . $response);
		}
		
		$data = json_decode($response, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('json decode error: ' . json_last_error_msg());
		}
		
		// Проверка на ошибку API
		if (isset($data['status']) && $data['status'] === 'error') {
			$error_code = $data['code'] ?? 'unknown';
			$error_message = $data['message'] ?? 'unknown error';
			throw new Exception('api error ' . $error_code . ': ' . $error_message);
		}
		
		return $data;
	}

	public function set_webhook(string $webhook_url, bool $enable = true): array {
		$url = $this->base_url . '/set_webhook';
		
		$post_data = [
			'webhook_url' => $webhook_url,
			'enable' => $enable ? 'true' : 'false',
			'instance_id' => $this->instance_id,
			'access_token' => $this->access_token
		];
		
		$ch = curl_init();
		
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($post_data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json'
			]
		]);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		
		if ($error) {
			throw new Exception('curl error: ' . $error);
		}
		
		if ($http_code !== 200) {
			throw new Exception('http error: ' . $http_code . ' - ' . $response);
		}
		
		$data = json_decode($response, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new Exception('json decode error: ' . json_last_error_msg());
		}
		
		// Проверка на ошибку API
		if (isset($data['status']) && $data['status'] === 'error') {
			$error_code = $data['code'] ?? 'unknown';
			$error_message = $data['message'] ?? 'unknown error';
			throw new Exception('api error ' . $error_code . ': ' . $error_message);
		}
		
		return $data;
	}
}
?>
