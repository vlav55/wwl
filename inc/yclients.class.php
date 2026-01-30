<?
include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
class yclients extends vkt {
	private $application_id;
	private $user_token;
	private $partner_token;
	private $company_id;
	function __construct($salon_id) { //0ctrl_tools tool='yclients' ctrl_id='$ctrl_id' tool_key='salon_id' tool_val=val
		$this->application_id=28143;
		$this->user_token='8b981a105ce0dcf93c7ab0d429cf688b';
		$this->partner_token='6zpzztnajb842aaubpxm';
		$this->connect('vkt');
		$this->company_id=$salon_id; //$this->get_company_id();
	}
	function cashback_withdraw($phone,$amount,$card_number) {
		$phone=$this->check_mob($phone);
		if(!$card_type_id=$this->get_card_type_id())
			return ['success'=>false,'msg'=>'YCLIENTS: Нет подходящих типов карт. Необходимо создать тип карты с разрешением для всех товаров и услуг в yclients раздел-Лояльность и поставить галочку, что действует в вашем филиале'];
		if(!$client_id=$this->get_client_id($phone))
			return ['success'=>false,'msg'=>'YCLIENTS: Не найден клиент с номером телефона: '.$phone];
		if(!$card_id=$this->get_card_by_client_id($client_id,$card_type_id)) {
			if(!$card_id=$this->issue_card($phone, $card_number, $card_type_id))
				return ['success'=>false,'msg'=>'YCLIENTS: Не удалось выпустить карту '.$card_number.' для клиента: '.$phone];
		}
		if(!$res=$this->manual_card_transaction($card_id, $amount, $comm = "Начислен кэшбэк WinWinLand Лояльность 2.0"))
			return ['success'=>false,'msg'=>'YCLIENTS: Не удалось выполнить начисление суммы '.$amount.'р. на  карту '.$card_number.' для клиента: '.$phone];

		return ['success'=>true,'msg'=>json_encode($res)];
	}
	function log($msg) {
		file_put_contents("yclients.log", date("d.m.Y H:i:s")."\n".$msg, FILE_APPEND);
	}
	function err($arr) {
		$this->notify_me(print_r($arr,true));
		$this->log(print_r($arr,true));
		if(mb_stripos($arr['meta']['message'],"Нет прав")!==false) {
			if($this->install($this->company_id)) {
				$this->notify_me("yclients $this->company_id reinstalled. Now work");
			}
		}
		return $arr;
	}
	function user_data_decode($user_data_encoded, $user_data_sign) {
		// 1. Декодируем user_data из base64
		$user_data_json = base64_decode($user_data_encoded);
		
		if ($user_data_json === false) {
			return false;
		}
		
		// 2. Проверяем валидность подписи
		$calculated_sign = hash_hmac('sha256', $user_data_json, $this->partner_token);
		$is_sign_valid = hash_equals($calculated_sign, $user_data_sign);
		
		if (!$is_sign_valid) {
			return false;
		}
		
		// 3. Декодируем JSON
		$user_data_array = json_decode($user_data_json, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			return false;
		}
		
		return $user_data_array;
	}
	function install($salon_id,$webhook_urls=[]) {
		$url = 'https://api.yclients.com/marketplace/partner/callback';

        // Prepare the data for the request body
        $data = [
            'salon_id' => $salon_id,
            'application_id' => $this->application_id,
            'webhook_urls'=>$webhook_urls
        ];
        

        //$this->notify_me($url."\n".print_r($data,true));

        // Initialize cURL
        $ch = curl_init($url);
        
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
			CURLOPT_POSTFIELDS => json_encode($data)
		]);

        // Execute the request
        $response = json_decode(curl_exec($ch),true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //$this->notify_me("yclients install \n".print_r($response,true));    
        // Close the cURL session
        curl_close($ch);
        
        if ($http_code != 201 && $http_code != 403 ) {
			$response['http_code']=$http_code;
            $this->err($response);
            return false;
        } else
			return $http_code;
    }
    function get_company_id() {
		return $this->get_companies(['my'=>1])['data'][0]['id'];
	}
	function get_companies($params = ['my'=>1])
	{
		$url = 'https://api.yclients.com/api/v1/companies';
		
		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		return $response;
	}

	function get_company($company_id) {
		$url = 'https://api.yclients.com/api/v1/company/' . $company_id;
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		return $response;
	}
	
	function get_card_type_id() {
		$res=$this->get_card_types();
		foreach($res['data'] AS $r) {
			if($r['service_item_type']=='any_allowed' && $r['good_item_type']=='any_allowed')
				return $r['id'];
		}
		return false;
	}

	function get_cards_by_phone($phone, $card_type_id) {
		$url = 'https://api.yclients.com/api/v1/loyalty/cards/' . $phone . '/' . $card_type_id . '/' . $this->company_id;
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		//print $url;
	$this->print_r($response);
		if(isset($response['data'][0]['id']))
			return $response['data'][0]['id']; 
		return $false;
	}

	function get_card_by_client_id($client_id, $card_type_id) { //returns card_id
		$url = 'https://api.yclients.com/api/v1/loyalty/client_cards/' . $client_id;
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}

		foreach($response['data'] AS $r) {
			if($r['type']['id']==$card_type_id)
				return $r['id'];
		}
		
		return false;
	}

	function get_card_types() {
		$url = 'https://api.yclients.com/api/v1/loyalty/card_types/salon/' . $this->company_id;
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		
		return $response;
	}
	
	function issue_card($phone, $card_number, $card_type_id) {
		$url = 'https://api.yclients.com/api/v1/loyalty/cards/' . $this->company_id;
		$data = [
			'loyalty_card_number' => $card_number,
			'loyalty_card_type_id' => $card_type_id,
			'phone' => $phone
		];
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: '.'Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
			CURLOPT_POSTFIELDS => json_encode($data)
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		
		return $response['data']['id'];
	}
	function manual_card_transaction($card_id, $amount, $comm = "") {
		$url = 'https://api.yclients.com/api/v1/company/' . $this->company_id . '/loyalty/cards/' . $card_id . '/manual_transaction';
		$data = [
			'amount' => $amount,
			'title' => $comm
		];
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
			CURLOPT_POSTFIELDS => json_encode($data)
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		
		return $response;
	}	
	function get_client_id($quick_search="") {
		$r=$this->get_clients($quick_search);
		return isset($r['data'][0]['id']) ? $r['data'][0]['id'] : false;
	}
	function get_clients($quick_search="") {
		$url = 'https://api.yclients.com/api/v1/company/' . $this->company_id . '/clients/search';
		
		$data = [
        'page' => 1,
        'page_size' => 50, // 
        'fields' => ['id','phone','name'] 
		];
		
		if (!empty($fields)) {
			//$data['fields'] = $fields;
		}
		
		if (!empty($quick_search)) {
			$data['filters']  = [
				[
					'type' => 'quick_search',
					'state' => [
						'value' => strval($quick_search)
					]
				]
			];		
		}
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->partner_token . ', User ' . $this->user_token
			],
			CURLOPT_POSTFIELDS => json_encode($data)
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		//$this->notify_me("HERE_$http_code\n".print_r($response,true));
		if ($http_code != 200) {
			//$this->err($response);
			return false;
		}
		
		return $response;
	}
	function get_ctrl_id_yclients() {
		return $this->dlookup("ctrl_id","0ctrl_tools","tool='yclients' AND tool_key='salon_id' AND tool_val='$this->company_id'");
	}
	function get_uid_yclients() { //previous setup in cards if app installed but continue installation is not confirmed
		$uid=$this->fetch_assoc($this->query("SELECT cards_add.uid AS uid FROM cards_add
				JOIN cards ON cards.uid=cards_add.uid
				WHERE cards.del=0 AND cards_add.uid!=0 AND cards_add.par='yclients' AND cards_add.val='$this->company_id'"))['uid'];
		return $uid;
	}
	function set_uid_yclients($uid,$user_data) { //previous setup in cards if app installed but continue installation is not confirmed 
		$this->cards_add_par($uid,'yclients',$this->company_id,json_encode($user_data));
	}
	function get_wwl_acc($uid) {
		if($this->database=='vkt') {
			if(!$ctrl_id=$this->dlookup("id","0ctrl","del=0 AND uid='$uid'"))
				return false;
			if(!$salon_id=$this->ctrl_tool_get_key($ctrl_id,'yclients'))
				return false;
			return $ctrl_id;
		} else
			$this->notify_me("yclinets.class.php get_wwl_acc error - not in vkt database. ".$this->database);
	}
	function is_paid($ctrl_id) {
		$tm_end=$this->tm_end_licence($ctrl_id);
		//$this->notify_me(date("d.m.Y",$tm_end));
		return $tm_end>time() ? $tm_end : false;
	}
	function send_payment_webhook($salon_id, $payment_sum, $tm_period_to, $currency_iso = "RUB", $tm_payment = null, $tm_period_from = null) {
		$url = 'https://api.yclients.com/marketplace/partner/payment';
		
		// Устанавливаем значения по умолчанию
		if ($tm_payment === null) {
			$tm_payment = time();
		}
		
		if ($tm_period_from === null) {
			$tm_period_from = time();
		}
		
		// Преобразуем timestamp в нужный формат даты
		$payment_date = date('Y-m-d H:i:s', $tm_payment);
		$period_from = date('Y-m-d H:i:s', $tm_period_from);
		$period_to = date('Y-m-d H:i:s', $tm_period_to);
		
		$data = [
			'salon_id' => $salon_id,
			'application_id' => $this->application_id,
			'payment_sum' => $payment_sum,
			'currency_iso' => $currency_iso,
			'payment_date' => $payment_date,
			'period_from' => $period_from,
			'period_to' => $period_to
		];
		//$this->print_r($data); exit;
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_HTTPHEADER => [
				'Accept: application/vnd.yclients.v2+json',
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->partner_token
			],
			CURLOPT_POSTFIELDS => json_encode($data)
		]);

		$response = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($http_code != 200 || !$response['success']) {
			$this->err($response);
			return false;
		}
		
		return $response;
	}
}
?>
