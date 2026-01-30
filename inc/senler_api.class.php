<?
class senler_api {
	static function GetHash($params, $secret) 
	{ 
		$values = "";  
		foreach ($params as $value) {  
			$values .= (is_array($value) ? implode("", $value) : $value);  
		} 
		return md5($values . $secret); 
	}

	var $callback_key = "b335c32b0895d026a4d908caf9ce9ff8affa1ac2";
	function __construct($callback_key=false) {
		if($callback_key) {
			$this->callback_key=$callback_key;
		}
	}

	
	var $vk_group_id="160316160";
	var $subscriptions=['webinar_visited'=>521488,
						'webinar_not_visited'=>521540,
						'webinar_info'=>483986,
						'webinar-info'=>483986,
						'webinar-registered'=>474092,
						'marafon-registered'=>495643,
						'customer_1'=>554751,
						'B'=>588656,
						'C'=>588659,
						'webinar_repeat'=>591392,
						'week0'=>605253,
						'seminar_in_record'=>607391,
						'seminar-registered-5days'=>740948,
						'seminar-registered-today'=>753738,
						'free_course'=>858678,
						];
	var $subscriptions_rus=[521488=>'БЫЛ НА СЕМИНАРЕ',
						521540=>'НЕ БЫЛ НА СЕМИНАРЕ',
						483986=>'ИНФО',
						474092=>'РЕГИСТР НА СЕМИНАР',
						554751=>'КЛИЕНТ КУРСА ДЛЯ НАЧ',
						564884=>'СЕМИНАР 20:00',
						564878=>'СЕМИНАР 10:00',
						495643=>'МАРАФОН',
						588656=>'B',
						588659=>'C',
						591392=>'НА СЕМИНАР ПОВТОРНО',
						605253=>'НЕДЕЛЯ 0',
						607391=>'СМОТРЕЛ СЕМИНАР В ЗАПИСИ',
						480097=>'TEST',
						858678=>'РЕГИСТР НА БЕСПЛ КУРС',
						921188=>'PPL-1',
						921192=>'PPL-2',
						921194=>'PPL-3',
						921195=>'PPL-4',
						926077=>'PPL-finished',
						934478=>'PL start on Sunday',
						940215=>'L-доступ 7 практик',
						938278=>'L',
						];


	function date2tm($date) {
		list($dt,$tm)=explode(" ",$date);
		list($d,$m,$y)=explode(".",$dt);
		list($h,$min,$s)=explode(":",$tm);
		$tm=mktime($h,$min,$s,$m,$d,$y);
		if(date("d.m.Y H:i:s",$tm)!=$date)
			return false; else return $tm;
	}
	function ban($uid) {
		return $this->subscribers_add($uid, "916623");
	}
	function subscribers_add($uid, $subscription_id) {
		if($uid==1)
			return true;
		$params = [ 
			'vk_group_id' => $this->vk_group_id, 
			'vk_user_id' => $uid,
			'subscription_id' => $subscription_id,
		];  
		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/subscribers/add', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$response = json_decode(curl_exec($myCurl),true); 
		curl_close($myCurl);
		//print_r($response);
		//return ($response);
		return ($response['success']);
	}

	function subscribers_del($uid, $subscription_id) {
		$params = [ 
			'vk_group_id' => $this->vk_group_id, 
			'vk_user_id' => $uid,
			'subscription_id' => $subscription_id,
		];  
		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/subscribers/del', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$response = json_decode(curl_exec($myCurl),true); 
		curl_close($myCurl);
		return ($response['success']);
	}

	var $date_subscription_from=false; //формат: d.m.Y H:i:s пример: 27.11.2018 10:00:00
	var $date_subscription_to=false;
	var $date_first_from=false;
	var $date_first_to=false;
	var $delivery_id=false;
	var $date_delivery_from=false;
	var $date_delivery_to=false;
	
	function subscribers_get_100($subscription_id,$cnt,$offset, $vk_user_id=false) {
		$params = [ 
			'vk_group_id' => $this->vk_group_id, 
			'count'=>$cnt,
			'offset'=>$offset,
		];
		if($this->delivery_id)
			$params['delivery_id']=$this->delivery_id;
		if($this->date_delivery_from)
			$params['date_delivery_from']=$this->date_delivery_from;
		if($this->date_delivery_to)
			$params['date_delivery_to']=$this->date_delivery_to;
		if($this->date_subscription_from)
			$params['date_subscription_from']=$this->date_subscription_from;
		if($this->date_subscription_to)
			$params['date_subscription_to']=$this->date_subscription_to;
		if($this->date_first_from)
			$params['date_first_from']=$this->date_first_from;
		if($this->date_first_to)
			$params['date_first_to']=$this->date_first_to;
		if($subscription_id!==false)
			$params['subscription_id'] =$subscription_id;
		if($vk_user_id)
			$params['vk_user_id'] =$vk_user_id;
		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/subscribers/get', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$response = json_decode(curl_exec($myCurl),true); 
		curl_close($myCurl);
		//print "count=".$response['count']."<br>";
		if(!$response['success'])
			return false;
		else
			return $response['items'];
	}
	function subscribers_get($subscription_id, $vk_user_id=false) {
		$offset=0;
		$cnt=100;
		$res=array();
		$r=array();
		while($r=$this->subscribers_get_100($subscription_id,$cnt,$offset,$vk_user_id)) {
			$offset+=sizeof($r);
			$res=array_merge($res,$r);
			if(sizeof($r)<$cnt)
				break;
			usleep(100000);
		}
		return $res;
	}
	function subscribers_get_uids($subscription_id) {
		$res=$this->subscribers_get($subscription_id, $vk_user_id=false);
		if(!$res)
			return false;
		$arr=array();
		foreach($res AS $r)
			$arr[]=$r['vk_user_id'];
		return $arr;
	}
	function subscribers_getinfo ($uid) {
		$res= $this->subscribers_get_100(false,1,0, $uid);
		if(!$res)
			return false;
		$arr=array();
		//print_r($res);
		foreach($res[0]['subscriptions'] AS $r) {
			$arr[]=$r['subscription_id'];
		}
		return $arr;
	}
	function get_tagname($utm_id) {
		$params = [ 
		'vk_group_id' => $this->vk_group_id, 
		'utm_id' => [$utm_id] 
		];  

		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/utms/Get', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$res=json_decode(curl_exec($myCurl),true);
		curl_close($myCurl);
		if(isset($res['success'])) {
			foreach($res['items'] AS $r)
				if($r['utm_id']==$utm_id)
					return $r['name'];
			return false;
		} else
			return false;
	}
	function subscribers_gettag_names ($uid) {
		//$uid=101598098;
		$res= $this->subscribers_get_100(false,1,0, $uid);
		if(!$res)
			return false;
		$arr=array();
		//print_r($res); exit;
		foreach($res[0]['utms'] AS $r) {
			$tag=$this->get_tagname($r['utm_id']);
			$arr[]=($tag)?$tag:"notag";
		}
		return $arr;
	}
	function subscribers_getinfo_names ($uid) {
		$res=$this->subscribers_getinfo ($uid);
		$arr=array();
		foreach($res AS $r) {
			if(isset($this->subscriptions_rus[$r]))
				$arr[]=$this->subscriptions_rus[$r];
			else
				$arr[]="$r";
		}
		return $arr;
	}
	function unsubscribe_all($uid,$exclude=array()) {
		$res=$this->subscribers_getinfo ($uid);
		foreach($res AS $r) {
			if(in_array($r,$exclude))
				continue;
			if(!$this->subscribers_del($uid, $r)) {
				print "senler_api:unsubscribe_all:subscribers_del($uid,$r):error <br> \n";
				return false;
			}
		}
		return true;
	}
	function groups_list() {
		$params = [ 
			'vk_group_id' => $this->vk_group_id, 
		];  
		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/subscriptions/get', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$r = json_decode(curl_exec($myCurl),true); 
		curl_close($myCurl);
		if(!isset($r['success']))
			return false;
		return($r['items']); // Array ( [subscription_id] => 480097 [name] => Тест ) 
	}
	
	function promo_subscribe($uid,$db,$cnt=0) { //yogacenter_vkt
		$uid=intval($uid);
		if(!$uid)
			return false;
		if(!$db->dlookup("id","promo_send","uid='$uid'")) {
			$cnt=intval($cnt);
			$db->query("INSERT INTO promo_send SET uid='$uid',tm_reg='".time()."',cnt='$cnt',stop=0");
			return true;
		} else
			return false;
	}
	function promo_unsubscribe($uid,$db) { //yogacenter_vkt
		$db->query("UPDATE promo_send SET stop=2 WHERE uid='$uid'");
		return true;
	}
	function promo_is_subscribed($uid,$db) { //yogacenter_vkt
		if(!$db->dlookup("id","promo_send","uid='$uid' AND stop=0",0))
			return false;
		else
			return true;
	}
	function promo_is_exists($uid,$db) { //yogacenter_vkt
		if(!$db->dlookup("id","promo_send","uid='$uid'",0))
			return false;
		else
			return true;
	}
	function promo_get_cnt($uid,$db) { //yogacenter_vkt
	//	print $db->database;
		return $db->dlookup("cnt","promo_send","uid='$uid'",0);
	}
	function vars_set($vk_user_id,$vk_group_id,$var_name,$val) {
		if($uid==1)
			return true;
		$params = [ 
			'name' => $var_name, 
			'vk_user_id' => $vk_user_id,
			'vk_group_id' => $vk_group_id,
			'value' => $val,
		];  
		$params['hash'] =$this::GetHash($params, $this->callback_key);

		$myCurl = curl_init(); 
		curl_setopt_array($myCurl, [ 
			CURLOPT_URL => 'https://senler.ru/api/vars/set', 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_POST => true, 
			CURLOPT_POSTFIELDS => http_build_query($params) 
		]); 
		$response = json_decode(curl_exec($myCurl),true); 
		curl_close($myCurl);
		print_r($response);
		//return ($response);
		return ($response['success']);
	}
}
?>
