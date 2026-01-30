<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
class insales extends vkt {
	var $id_app="winwinland";
	var $secret_key='0b42aed65f8bb4fa4685a0a99768d1cf';
	var $credentials="";
	var $shop;
	var $insales_id;
	var $token;
	var $ctrl_id;
	var $error_code=0;

	function __construct($insales_id,$shop) {
		$this->insales_id=$insales_id;
		$this->shop=$shop;
		if(@!$this->token=trim(file_get_contents("$insales_id.token"))) {
			$this->connect('vkt');
			$this->token=$this->dlookup("insales_token","0ctrl","del=0 AND insales_shop_id='$insales_id'");
			$database=$this->get_ctrl_database($this->dlookup("id","0ctrl","del=0 AND insales_shop_id='$insales_id'"));
			$this->connect($database);
		}
		$this->get_credentials();
	}
	function get_credentials() {
		$passw=md5($this->token.$this->secret_key);
		$this->credentials = base64_encode("$this->id_app:$passw");
		//~ $this->notify_me( "
		//~ insales_id=$this->insales_id <br>
		//~ shop=$this->shop <br>
		//~ token=$this->token <br>
		//~ passw=$passw <br>
		//~ secret_key=$this->secret_key <br>
		//~ id_app=$this->id_app <br>
		//~ cr=$this->credentials <br>");
	}
	function err($arr) {
		global $db,$insales_id;
		if(!in_array($arr['http_code'],[422]))
			$this->notify_me("INSALES ERROR in insales_id=$insales_id\n".print_r($arr,true));
		//print_r($arr);
		$this->error_code=$arr['http_code'];
		return $arr;
	}
	function webhook_create($url, $event) {
		// Prepare the API endpoint for creating a webhook
		$apiUrl = "https://$this->shop/admin/webhooks.json"; // Replace with your InSales store URL
		// Prepare the webhook data
		$webhookData = [
			'webhook' => [
				'address' => $url, // The URL to receive the webhook notifications
				'topic' => $event, // The event to subscribe to
				"format_type" => "json"
			],
		];

		// Initialize cURL
		$ch = curl_init($apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $this->credentials",
			"Content-Type: application/json",
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));

		// Execute the request
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Check if the webhook was created successfully
		if ($httpCode == 201) {
			return true;
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to create webhook.\n'.print_r($webhookData,true),
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function webhook_del($webhook_id) {

		// Формируем URL для удаления вебхука
		$url = "https://$this->shop/admin/webhooks/$webhook_id.json";

		// Инициализируем cURL для удаления вебхука
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $this->credentials",
			"Content-Type: application/json",
		]);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		// Выполняем запрос на удаление
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Проверяем код ответа
		if ($httpCode == 200) {
			return true; // Вебхук успешно удалён
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to delete webhook. '.$webhook_id,
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function get_webhooks() {
		// Формируем URL для запроса всех вебхуков
		$url = "https://$this->shop/admin/webhooks.json";
		//$credentials=$this->credentials;
		$credentials="";

		// Инициализируем cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $credentials",
			"Content-Type: application/json",
		]);

		// Выполняем запрос
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Проверяем успешность выполнения запроса
		if ($httpCode == 200) {
			// Декодируем полученный JSON в ассоциативный массив
			return json_decode($response, true); // Возвращаем ассоциативный массив
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to get webhooks.',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function check_webhooks($insales_id) {
		if(!$insales_id)
			return false;
		if(!$ctrl_dir=$this->get_ctrl_dir($this->ctrl_id)) {
			$this->notify_me("insales.class.php check_webhook error ctrl_id=$this->ctrl_id insalses_id=$insales_id");
			return false;
		}
		$res=$this->get_webhooks();
		$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
		//$db->notify_me($insales_id."\n".print_r($res,true));
		if(isset($res['error'])) {
			$this->error_code=$res['http_code'];
			return false;
		}
		$fl1=false; $fl2=false;
		foreach($res AS $r) {
			if($r['address']==$url) {
				if($r['topic']=='orders/update')
					$fl1=true;
				if($r['topic']=='orders/create')
					$fl2=true;
			}
			if($fl1 && $fl2)
				return true;
		}
		return false;
	}
	function get_webhook($insales_id) {
		if(!$insales_id)
			return false;
		if(!$ctrl_dir=$this->get_ctrl_dir($this->ctrl_id)) {
			$this->notify_me("insales.class.php get_webhook error ctrl_id=$this->ctrl_id");
			return false;
		}
		$res=$this->get_webhooks();
		$url="https://for16.ru/d/$ctrl_dir/insales_webhook.php";
		//$db->notify_me($insales_id."\n".print_r($res,true));
		foreach($res AS $r) {
			if($r['address']==$url)
				return $r['id'];
		}
		return false;
	}
	function get_order($order_id) {
		$url = "https://$this->shop/admin/orders/$order_id.json"; // пример URL
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $this->credentials"
		]);

		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response,true);
	}
	
	
	//~ discount_code[code] * required	code
	//~ discount_code[description]	description
	//~ discount_code[disabled]	disabled
	//~ discount_code[act_once]	act once
	//~ discount_code[act_once_for_client]	act once for client
	//~ discount_code[expired_at]	expired at
	//~ discount_code[type_id] * required	type_id (1 - percent, 2 - money)
	//~ discount_code[discount] * required	discount
	//~ discount_code[min_price]	min price
	function create_promocode($r) {
		// API endpoint
		$url = "https://$this->shop/admin/discount_codes.json";

		// Discount code data to send in the request
		$data = [
			'discount_code' => [
				'code' => $r['code'], // e.g. "BIRTHDAY"
				'type_id' => $r['type_id'], // e.g. 1 for percentage
				'discount' => $r['discount'], // e.g. 10
				'disabled' => false,
				'act_once_for_client'=>false,
				'act_once'=>false,
			]
		];
		if(isset($r['description']))
				$data['discount_code']['description'] = $r['description'];
		if(isset($r['act_once']))
				$data['discount_code']['act_once'] = $r['act_once'];
		if(isset($r['act_once_for_client']))
				$data['discount_code']['act_once_for_client'] = $r['act_once_for_client'];
		if(isset($r['expired_at']))
				$data['discount_code']['expired_at'] = $r['expired_at'];
		if(isset($r['min_price']))
				$data['discount_code']['min_price'] = $r['min_price'];
		if(isset($r['disabled']))
				$data['discount_code']['disabled'] = $r['disabled'];
		
		//$this->print_r($data);
		// Initialize cURL
		$ch = curl_init($url);
		
		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Basic '.$this->credentials
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		// Execute the request
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close cURL resource
		curl_close($ch);

		if ($httpCode == 201) { // Created successfully
			return json_decode($response, true); // Return associative array of the response
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to create promocode.',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function create_client($uid) {
		if($res=$this->search_client($uid))
			return $res;
		// Insales API endpoint for creating a client
		$url = "https://$this->shop/admin/clients.json";
		$r=$this->fetch_assoc($this->query("SELECT * FROM cards WHERE uid='$uid'"));
		$fio=$this->split_fio($r['surname']." ".$r['name']);
		if(empty($fio['l_name']))
			$fio['l_name']='-';
		if(empty($fio['m_name']))
			$fio['m_name']='-';
		
		// Prepare the client data
		$client_data = [
			'client' => [
				'name' => $fio['f_name'],
				'surname' => $fio['l_name'],
				'middlename' => $fio['m_name'],
				'subscribe' => true,
				'email' => $r['email'],
				'phone' => $r['mob_search'],
				'type' => 'Client::Individual',
				'password' => null,
				'registered' => false,
			]
		];

		// Initialize cURL
		$ch = curl_init($url);
		
		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Basic ' . $this->credentials
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($client_data));

		// Execute the cURL request
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close the cURL session
		curl_close($ch);

		// Check the HTTP response code
		if ($httpCode == 201) {
			$res=json_decode($response, true);
			$client_id=$res['id'];
			$this->query("DELETE FROM cards2other WHERE uid='$uid' AND tool='insales'");
			$this->query("INSERT INTO cards2other SET uid='$uid',tool_uid='$client_id',tool='insales'");
			return $res; // Return associative array
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to create client.',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function get_clients($updated_since = null, $from_id = null, $per_page = 10, $search_arr=[]) {
		// Build the Insales API endpoint URL
		$url = "https://$this->shop/admin/clients.json";

		// Prepare query parameters
		$query_params = [];

		if(isset($search_arr['q']))
			$query_params['q'] = $search_arr['q'];
		if(isset($search_arr['phone']))
			$query_params['phone'] = $search_arr['phone'];
		if(isset($search_arr['email']))
			$query_params['email'] = $search_arr['email'];

		if ($updated_since) {
			$query_params['updated_since'] = $updated_since;
		}
		
		if ($from_id) {
			$query_params['from_id'] = $from_id;
		}

		// Always include the per_page parameter
		$query_params['per_page'] = $per_page;

		// Append query parameters to the URL
		if (!empty($query_params)) {
			$url .= '?' . http_build_query($query_params);
		}

		// Initialize cURL
		$ch = curl_init($url);

		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Basic ' . $this->credentials
		]);

		// Execute the cURL request
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Close the cURL session
		curl_close($ch);

		// Check the HTTP response code
		if ($httpCode == 200) {
			// Decode the received JSON into an associative array
			return json_decode($response, true); // Return associative array
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to get clients.',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function get_all_clients($per_page=100) {
		$all_clients = [];
		$from_id = null;  // Start with no ID to get the first batch
		//$per_page = 100;  // Maximum number of clients to fetch per request

		do {
			// Fetch clients using the specified from_id
			$clients = $this->get_clients(null, $from_id, $per_page);

			// Check if there are clients returned
			if (isset($clients) && !isset($clients['error'])) {
				// Extract the details we need (name, phone, email)
				foreach ($clients as $client) {
					$mob=$this->check_mob($client['phone']);
					$email=$this->validate_email($client['email']) ? trim($client['email']) : null;
					$all_clients[] = [
						'id' => $client['id'],
						'name' => $client['name'],
						'phone' => $mob,
						'email' => $email
					];
					// Update from_id to the last client ID for the next request
					$from_id = $client['id']; // Assuming 'id' field exists for each client
				}
			} else {
				// No more clients found, exit the loop
				break;
			}

		} while (count($clients) == $per_page); // Continue while the last call returned the maximum number

		return $all_clients; // Return an array containing all clients' data
	}
	function search_client($uid) { //['phone'=>'79119841012','email'=>'vlav@mail.ru']
		$mob=$this->dlookup("mob_search","cards","uid='$uid'");
		if(!sizeof($res=$this->get_clients(null,0,10,['phone'=>$mob]))) {
			$email=$this->dlookup("email","cards","uid='$uid'");
			if(!sizeof($res=$this->get_clients(null,0,10,['email'=>$email])))
				return false;
		}
		$in_id=$res[0]['id'];
		if(!$this->dlookup("id","cards2other","uid='$uid' AND tool='insales'"))
			$this->query("INSERT INTO cards2other SET uid='$uid',tool='insales',tool_uid='$in_id'");
		else
			$this->query("UPDATE cards2other SET tool_uid='$in_id' WHERE uid='$uid' AND tool='insales'");
		return $res[0];
	}
	function get_account() {
		// Формируем URL для запроса данных аккаунта
		$url = "https://$this->shop/admin/account.json";
		// Инициализируем cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $this->credentials",
			"Content-Type: application/json",
		]);

		// Выполняем запрос
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Проверяем успешность выполнения запроса
		if ($httpCode == 200) {
			// Декодируем ответ JSON
			return json_decode($response, true); // Возвращаем ассоциативный массив
		} else {
			return $this->err([
				'error' => true,
				'message' => 'Failed to get account info',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
	function bonus_create($client_id, $amount, $descr='Бонус при регистрации') {
		$url = "https://$this->shop/admin/clients/$client_id/bonus_system_transactions.json";
		//$this->notify_me("$url");

		// Подготовка данных бонуса
		$bonusData = [
			'bonus_system_transaction' => [
				'bonus_points' => intval($amount),
				'description' => addslashes($descr),
			],
		];
		$this->notify_me("HERE_$client_id $amount");

		// Инициализируем cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Basic $this->credentials",
			"Content-Type: application/json",
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bonusData));

		// Выполняем запрос
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Проверяем успешность выполнения запроса
		if ($httpCode == 201) {
			// Возвращаем данные о созданном бонусе
			return json_decode($response, true);
		} else {
			// Обработка ошибок
			return $this->err([
				'error' => true,
				'message' => 'Failed to create bonus.',
				'http_code' => $httpCode,
				'response' => $response,
			]);
		}
	}
}
?>
