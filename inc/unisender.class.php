<?
class unisender {
	var $client=false;
	var $headers=false;
	var $from_email=false;
	var $from_name=false;
	var $res="";
	var $post_data="";
	function __construct($api_key,$from_email,$from_name) {
		if(!filter_var($from_email, FILTER_VALIDATE_EMAIL))
			$from_email=false;
		if(empty($from_name))
			$from_name=false;
		$this->from_email=$from_email;
		$this->from_name=$from_name;
		$this->headers=['Content-Type: application/json',
			'Accept: application/json',
			'X-API-KEY: '.$api_key,
			];
	}
	function check_email($email) {
		$post_data = ["email" => trim($email)];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://go1.unisender.ru/ru/transactional/api/v1/email-validation/single.json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => $this->headers,
		  CURLOPT_POSTFIELDS => json_encode($post_data)
		));

		$response = json_decode(curl_exec($curl),true);
		curl_close($curl);
		//print_r($response); exit;
		if($response['status']=='success' && ($response['result']=='valid') ) {
			return true;
		} else {
			print_r($response);
			return false;
		}
	}
	function email($email,$subj,$body) {
		$post_data = [
		  "message" => [
			"recipients" => [  ["email" => $email,]
			],
			"skip_unsubscribe" => 0,
			"global_language" => "ru",
			"from_email" => $this->from_email,
			"from_name" => $this->from_name,
			"reply_to" => $this->from_email,
			"body" => [
			  "html" => "<html><head></head><body>".($body),"</body></html>",
			  "plaintext" => $body,
			],
			"subject" => $subj,
			"track_links" => 0,
			"track_read" => 0,
			"bypass_global" => 1,
			"bypass_unavailable" => 1,
			"bypass_unsubscribed" => 0,
			"bypass_complained" => 0,
		  ]
		];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://go1.unisender.ru/ru/transactional/api/v1/email/send.json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => $this->headers,
		  CURLOPT_POSTFIELDS => json_encode($post_data)
		));

		$response = json_decode(curl_exec($curl),true);
		curl_close($curl);
		if($response['status']=='success') {
			return true;
		} else {
			print_r($response);
			return false;
		}
	}
	function email_by_template($email,$template_id,$vars) {
		foreach($vars AS $key=>$val) {
			if(empty($val))
				$vars[$key]="-";
		}
		$post_data = [
		  "message" => [
			"recipients" => [
				["email" => $email,"substitutions" => $vars]
							],
			"skip_unsubscribe" => 0,
			"global_language" => "ru",
			"template_engine" => "simple",
			"template_id" => $template_id,
			"track_links" => 0,
			"track_read" => 0,
			"bypass_global" => 1,
			"bypass_unavailable" => 1,
			"bypass_unsubscribed" => 0,
			"bypass_complained" => 0,
		  ]
		];
		//print_r($post_data); exit;
		
		if($this->from_email)
			$post_data['message']['from_email']=$this->from_email;
		if($this->from_name)
			$post_data['message']['from_name']=$this->from_name;

		$this->post_data=$post_data;


	//print_r(json_encode($post_data)); exit;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://go1.unisender.ru/ru/transactional/api/v1/email/send.json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_HTTPHEADER => $this->headers,
		  CURLOPT_POSTFIELDS => json_encode($post_data)
		));

		$response = json_decode(curl_exec($curl),true);
		curl_close($curl);
		$this->res=$response;
	//print_r($response);
		if($response['status']=='success') {
			return true;
		} else {
			return false;
		}
	}
	function __construct___($api_key,$from_email,$from_name) {
		require 'guzzle/vendor/autoload.php';

		$this->headers = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'X-API-KEY' => $api_key,
		);

		$this->client = new \GuzzleHttp\Client([
			'base_uri' => 'https://go1.unisender.ru/ru/transactional/api/v1/'
		]);

		$this->from_email=$from_email;
		$this->from_name=$from_name;
	}
	function email__($email,$subj,$body) {
		$requestBody = [
		  "message" => [
			"recipients" => [  ["email" => $email,]
			],
			"skip_unsubscribe" => 0,
			"global_language" => "ru",
			"template_engine" => "simple",
			"from_email" => $this->from_email,
			"from_name" => $this->from_name,
			"reply_to" => $this->from_email,
			"body" => [
			  "html" => "<html><head></head><body>".($body),"</body></html>",
			  "plaintext" => $body,
			],
			"subject" => $subj,
			"track_links" => 0,
			"track_read" => 0,
			"bypass_global" => 1,
			"bypass_unavailable" => 1,
			"bypass_unsubscribed" => 0,
			"bypass_complained" => 0,
		  ]
		];

		try {
			$response = $this->client->request('POST','email/send.json', array(
				'headers' => $this->headers,
				'json' => $requestBody,
			   )
			);
			//print_r(json_decode($response->getBody()->getContents(),true));
			return true;
		 }
		 catch (\GuzzleHttp\Exception\BadResponseException $e) {
			// handle exception or api errors.
			print_r($e->getMessage());
			return false;
		 }
	}
	function email_by_template__($email,$template_id,$vars) { //$vars=['test'=>12345]  {{test}} inside template
		$requestBody = [
		  "message" => [
			"recipients" => [
				["email" => $email,"substitutions" => $vars]
			],
			"skip_unsubscribe" => 0,
			"global_language" => "ru",
			"template_engine" => "simple",
			"template_id" => $template_id,
			"from_email" => $this->from_email,
			"from_name" => $this->from_name,
			"reply_to" => $this->from_email,
			"track_links" => 0,
			"track_read" => 0,
			"bypass_global" => 1,
			"bypass_unavailable" => 1,
			"bypass_unsubscribed" => 0,
			"bypass_complained" => 0,
		  ]
		];

		try {
			$response = $this->client->request('POST','email/send.json', array(
				'headers' => $this->headers,
				'json' => $requestBody,
			   )
			);
			//print_r(json_decode($response->getBody()->getContents(),true));
			return true;
		 }
		 catch (\GuzzleHttp\Exception\BadResponseException $e) {
			// handle exception or api errors.
			print_r($e->getMessage());
			return false;
		 }
	}
}
?>
