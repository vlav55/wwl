<?
class vklist_api {
	public static $proxy;
	public static $proxy_passw;
	var $token, $last_response, $error_code;
	var $version="5.95";
	var $min_age_limit=16;
	var $max_age_limit=59;
	var $uids=array("vlav"=>198746774,"viktorov"=>29362230, "julia"=>70412844,"m1"=>8411576,"bobrova"=>98618853);
	var $tokens=array("vlav"=>"38013336779beafcd4569dac543fe8f0cff797d1216c4100a4857751c2973b7bd2dd5c642c25fbeaae588",
						"vktrade"=>"9cf6f3f625d19154c5d101626b2620dbb0f8958425320d018e2cb90d096846ae36a90eec1d3d1036790dd",
						"yogahelpyou"=>"54ab01de9f02b3a1d8d08bb5cd0350edfc02fecaf66f66067d9d66ffe21fb3e2949a5877d098d872696f5",
						);
	var $ad_account_id=1604129923;
	var $telegram_bots=array("vktrade"=>"484406014:AAGo7T30Is2pC4QKFsCidOcg2p-Zz36g5L8",
					"yogahelpyou"=>"484406014:AAGo7T30Is2pC4QKFsCidOcg2p-Zz36g5L8",
					"dancehall"=>"410546936:AAEjE0G6e3Yz0hs-Cs7_A-0iUYvYNk3ri8M",
					"avtotrade"=>"414523530:AAHRP6XB1zZmrxtapHnYWO6KeiIrbeM7-tg",
					"style-inside"=>"1451314745:AAHLX4MAf3M008jcAtWiQPCgJDi1-IZr28k",
					"papavdekrete"=>"1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8",
					"f12"=>"1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8",
					"vkt"=>"5318845938:AAFwkeX5dcBtTHChYmG8i2BS_IJEx_81sv4",
					);
	var $telegram_chat_ids=array("vlav"=>315058329, "julia"=>282133533, "hohlova"=>231895421,"didenkova"=>125641037,"efimova"=>331616748,"bobrova"=>419661812);
	
	function __construct($token=false) {
		if(!$token)
			$this->token=$this->tokens['yogahelpyou'];
		else
			$this->token=$token;
	}
	function check_token($required=true) {
		return true;
		if(!$required)
			return true;
//		$this->token=$this->tokens['vlav'];
		if(empty($this->token)) {
			include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
			$db=new db;
			$r=$db->get_first_working_acc();
			$this->token=$r['token'];
		}
	}
	function get_uid_from_url($url) {
		if(is_numeric(trim($url))) {
			//print "HERE_".intval(trim($url)); exit;
			return intval(trim($url));
		}	
		if(preg_match("#^id([0-9]*)$#",$url,$res))
			$uid=$res[1]; 
		elseif(preg_match("#http(s)?://vk.com/id(.*)#",$url,$res))
			$uid=$res[2];
		elseif(preg_match("#http(s)?://vk.com/(.*)#",$url,$res)) 
			$uid=$this->vk_get_uid_by_domain($res[2]);
		else
			$uid=$this->vk_get_uid_by_domain($url);
		//print "HERE_".$uid; exit;
		return intval(trim($uid));
	}
	function vk_get_city_country($id1,$id2) {
		if(isset($id2['id'])) {
			if($id2['id']>1)
				$country=",".$id2['title']; else $country="";
		} else {
			if($id2>1)
				$country=",".$this->vk_get_country_name($id2); else $country="";
		}
		if(isset($id1['id'])) 
			$res=$id1['title'].$country;
		else
			$res=$this->vk_get_city_name($id1).$country;
		return $res;
	}
	function vk_get_city_name($id) {
		if(isset($id['title']))
			return $id['title'];
		$arr=$this->vk_get_city(array($id));
		return $arr[$id];
	}
	function vk_get_country_name($id) {
		if(isset($id['title']))
			return $id['title'];
		$arr=$this->vk_get_country(array($id));
		return $arr[$id];
	}
	function vk_get_city($ids_arr) {
		$this->check_token($required=true);
		//print_r($ids_arr);
		$city_ids="";
		foreach($ids_arr AS $id)
			$city_ids.="$id,";
		$city_ids.=0;
		//print $city_ids;
		$url = 'https://api.vk.com/method/database.getCitiesById';
		$params=array('v'=>$this->version,'city_ids'=>$city_ids,"version"=>$this->version);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($this->last_response,true);
		//print_r($ids_arr); print_r($res); exit;
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else {
			$arr=array(0=>"n/a");
			foreach($res['response'] AS $val) {
				$arr[$val['id']]=$val['title'];
			}
			return $arr;
		}
	}
	function vk_get_country($ids_arr) {
		$this->check_token($required=true);
		$country_ids="";
		foreach($ids_arr AS $id)
			$country_ids.="$id,";
		$url = 'https://api.vk.com/method/database.getCountriesById';
		$params=array('v'=>$this->version, 'access_token'=>$this->token,'country_ids'=>$country_ids,"version"=>$this->version);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($this->last_response,true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else {
			$arr=array(0=>"n/a");
			foreach($res['response'] AS $val) {
				$arr[$val['id']]=$val['title'];
			}
			return $arr;
		}
	}
	function vk_wall_get_text($wall_id,$is_group=true) { //56136843_77
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/wall.getById';
		$wall_id=($is_group)?"-".$wall_id:$wall_id;
		$params=array('v'=>$this->version, 'access_token'=>$this->token,'posts'=>$wall_id);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else return $res['response'][0]['text'];
	}
	function wall_get_comments($group_id,$post_id,$start_comment_id=false,$offset=0,$count=10) { //group_id is negative
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/wall.getComments';
		$params=array('v'=>$this->version, 'access_token'=>$this->token,
		'owner_id'=>$group_id,
		'post_id'=>$post_id,
		'count'=>$count,
			);
		if($start_comment_id)
			$params['start_comment_id']=$start_comment_id;
		else
			$params['offset']=$offset;
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($res['error'])) {
			print_r($res);
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else return $res['response'];
	}
	function vk_is_group_member($grp,$uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.isMember';
		$params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$grp,'user_id'=>$uid);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else return $res['response'];
	}
	function vk_is_messages_from_group_allowed($grp,$uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.isMessagesFromGroupAllowed';
		$params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$grp,'user_id'=>$uid);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else return $res['response']['is_allowed'];
	}
	function vk_group_getinfo($grp) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.getById';
		print $this->token;
		$params=array('v'=>$this->version,'access_token'=>$this->token, 'group_id'=>$grp,'fields'=>"city,country,place,description,wiki_page,members_count,counters,start_date,finish_date,can_post,can_see_all_posts,activity,status,contacts,links,fixed_post,verified,site,ban_info,cover");
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			print_r($res);
			return false; 
		} else {
			//print_r( $res['response']);
			if(isset($res['response'][0]['city']))
				//$res['response'][0]['city']=$this->vk_get_city(array($res['response'][0]['city']))[$res['response'][0]['city']];
				$res['response'][0]['city']=$res['response'][0]['city']['title'];
			else
				$res['response'][0]['city']="n/a";
			if(isset($res['response'][0]['country']))
				//$res['response'][0]['country']=$this->vk_get_city(array($res['response'][0]['country']))[$res['response'][0]['country']];
				$res['response'][0]['country']=$res['response'][0]['country']['title'];
			else
				$res['response'][0]['country']="n/a";
			return $res['response'][0];
		}
	}
	function vk_group_getmembers_part($groupid,$cnt=1000,$offset) { //groupid or domain
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.getMembers';
		
		$params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$groupid,'offset'=>$offset,'count'=>$cnt,"fields"=>"sex, bdate, city, country");
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else {
			return $res['response']['users'];
		}
	}
	function vk_group_getmembers_cnt($groupid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.getMembers';
		$params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$groupid,'offset'=>0,'count'=>5);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else {
			return $res['response']['count'];
		}
	}
	function vk_group_getmembers($groupid,$cnt=1000,$limit=5000) { //groupid or domain
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.getMembers';
		$this->error_code=false;
		
		$params=array('v'=>$this->version,'access_token'=>$this->token, 'group_id'=>$groupid,'offset'=>0,'count'=>5);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
	print_r($res);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false;
		}
		$items=$res['response']['count'];
		if($items>$limit) {
			print "Items=$items Limit=$limit - getted items truncated to limit ! \n";
			$items=$limit;
		}
		//print $items."<br>";
		$members=array();
		for($n=0; $n<$items+$cnt; $n+=$cnt) {
			print "Cycle from item $n cnt=$cnt \n";
			//~ if($res['error']['error_code']==15) {
				//~ usleep(1000000);
				//~ $params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$groupid,'offset'=>$n,'count'=>$cnt,"fields"=>"sex, bdate, city, country");
				//~ $this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
				//~ $res=json_decode($this->last_response,true);
			//~ }
			//print_r($res);


			$res['error']['error_code']=6;
			$cnt_err=0;
			while(isset($res['error'])) {
				usleep(1000000);
				//$params=array('v'=>$this->version, 'access_token'=>$this->token, 'sort'=>'time_desc', 'group_id'=>$groupid,'offset'=>$n,'count'=>$cnt,"fields"=>"sex, bdate, city, country");
				$params=array('v'=>$this->version, 'access_token'=>$this->token, 'group_id'=>$groupid,'offset'=>$n,'count'=>$cnt,"fields"=>"sex, bdate, city, country");
				$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
				$res=json_decode($this->last_response,true);

				if(isset($res['error']))
					$this->error_code=$res['error']['error_code'];
				else
					break;

				if($cnt_err++>5) {
					include_once("/var/www/vlav/data/www/wwl/inc/db.class.php");
					$db=new db;
					$db->email($emails=array("vlav@mail.ru"), "vk_group_getmembers ERROR", print_r($res,true), $from="noreply@winwinland.ru",$fromname="VKTRADE", $add_globals=true);
					return false;
				}
			}
				//~ include_once("/var/www/vlav/data/www/wwl/inc/db.class.php");
				//~ $db=new db;
				//~ $db->email($emails=array("vlav@mail.ru"), "vk_group_getmembers OK", "", $from="vktrade@1-info.ru",$fromname="VKTRADE", $add_globals=true);

			$members=array_merge($members,$res['response']['items']);
			//usleep(300000);
			//print_r($res['response']['users']);
			//print "$n<br>";
		}
		//print_r($members);
		return $members;	
	}
	function vk_friends_getlist_for_uid($uid) { //friends for uid
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/friends.get';
		$params=array('v'=>$this->version,'user_id'=>$uid,'access_token'=>$this->token);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($this->last_response,true);
		//print_r($res);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			print "vk_friends_getlist_for_uid : error : ".$vk->error_code." List set to empty array!\n";
			return array(); 
		} else return $res['response']['items'];
	}
	function vk_friends_getcnt($uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/friends.get';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'user_id'=>$uid);
		$last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$r=json_decode($last_response,true);
		//print_r($r['response']);
		if(@$r['error']) {
			return false;
		} else 
			return $r['response']['count'];
	}
	function vk_friends_add($uid , $message) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/friends.add';
		if(!is_int($uid))
			$uid=$this->vk_get_uid_by_domain($uid);
		$message=substr($message,0,500);
		$params=array('v'=>$this->version,
		'access_token'=>$this->token,
		'user_id'=>$uid,
		'follow'=>0
		);
		if(trim($message)!="")
			$params['text']=$message;
	
		$last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		if(strpos($last_response,"error")!==false) {
			$e=json_decode($last_response,true);
			return $e['error']['error_code'];
		} else
			return 0;
	}
	function vk_get_friend_status($uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/friends.areFriends';
		if(!is_int($uid))
			$uid=$this->vk_get_uid_by_domain($uid);
		$params=array('v'=>$this->version,'access_token'=>$this->token,'user_ids'=>$uid,'need_sign'=>0	);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$e=json_decode($this->last_response,true);
		if(isset($e['error'])) {
			return -1;
		} else
			return $e['response'][0]['friend_status'];
			/*
			*/
	}
	function vk_get_name_by_uid($uid) {
		if($uid<=0)
			return false;
		$this->check_token($required=true);
		if(!$user_info=$this->vk_get_userinfo($uid))
			return false;
		$first_name=$user_info['first_name'];
		$last_name=$user_info['last_name'];
		return htmlspecialchars("$first_name $last_name");
	}
	function vk_get_userinfo($uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/users.get';
		$params=array('lang'=>0,'v'=>$this->version, 'access_token'=>$this->token,'user_ids'=>$uid,'fields'=>'bdate,sex,status,city,country,photo_200,photo_100,photo_50,about,activities,career,has_photo,interests,occupation,site,deactivated,blacklisted,can_send_friend_request,can_write_private_message,is_friend');
		//print_r($params);
	//	$proxy="tcp://18.218.163.13:3128";
	//	$proxy_passw='vlav:fokova^142586';
	//print "HERE_".self::$proxy_passw;
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded') )))),true);
		$this->last_response=$res;
	//print_r($res);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return false; 
		} else {
			if(!isset($res['response'][0]['city']))
				$res['response'][0]['city']=0;
			if(!isset($res['response'][0]['country']))
				$res['response'][0]['country']=0;
			return $res['response'][0];
		}
	}
	function vk_can_write($uid) {
		$url = 'https://api.vk.com/method/users.get';
		$params=array('v'=>$this->version, 'access_token'=>$this->token,'user_ids'=>$uid,'fields'=>'status,occupation,sex,bdate,deactivated,blacklisted,can_send_friend_request,can_write_private_message,is_friend');
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($this->last_response,true);
		if(isset($res['response'][0]['can_write_private_message'])) {
			return ($res['response'][0]['can_write_private_message']) ? true : false;
		}
		return -1;
	}
	function vk_is_user_blocked($uid,$use_stop_words=false, $sex_allowed=false) { 
		//0-OK, 
		//1-blocked but it is possible to add to friends, 
		//2-blocked completely, 
		//3 -banned, 
		//4 -too young, 
		//5 -sex=man
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/users.get';
		$params=array('v'=>$this->version, 'access_token'=>$this->token,'user_ids'=>$uid,'fields'=>'status,occupation,sex,bdate,deactivated,blacklisted,can_send_friend_request,can_write_private_message,is_friend');
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($this->last_response,true);
		if(isset($res['error'])) {
			$this->error_code=$res['error']['error_code'];
			return -1; 
		}
		if(isset($res['response'][0]['sex'])) {
			if($sex_allowed!==false) { 
				if($res['response'][0]['sex']!=$sex_allowed)
					return 4; //sex=man
			}
		}
		if(isset($res['response'][0]['bdate'])) {
			$a=explode(".",$res['response'][0]['bdate']);
			if(sizeof($a)==3) {
				if( (date("Y")-$a[2])<$this->min_age_limit  || (date("Y")-$a[2])>$this->max_age_limit) {
					return 5; //too young
				}
			}
		}
		if(isset($res['response'][0]['deactivated']))
			return 3;
		if($res['response'][0]['can_write_private_message']==0) {
			if($res['response'][0]['can_send_friend_request']==0)
				return 2; else return 1;
		}
		return 0;
		if($use_stop_words) {
			$stop_words=file("vklist_send.stop_words.txt");
			if($stop_words) {
				foreach($stop_words AS $word) {
					if(trim($word)=="")
						continue;
					$p1=(isset($res['response'][0]['status']))?$res['response'][0]['status']:"";
					$p2=(isset($res['response'][0]['occupation']['name']))?$res['response'][0]['occupation']['name']:"";
					$str= mb_strtolower($p1." ".$p2,"utf8");
					$word=mb_strtolower(trim($word),"utf8");
					//print $str."<br>";
					//print $word."<br>";
					if(strpos($str,$word)!==false)
						return 6; //STOP WORD FOUND
				}
			} else 
				print "vk_is_user_blocked : ERROR : not found vklist_send.stop_words.txt\n";
		}
		return 0;
	}
	function vk_get_uid_by_domain($domain) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/users.get';
		$params=array('v'=>$this->version,'user_ids'=>$domain,'access_token'=>$this->token);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(isset($res['response']))
			return $res['response']['0']['id']; else return false;
	}
	function vk_get_group_id_by_domain($domain) {
		$this->check_token($required=true);
		$domain=str_replace('https://vk.com/','',$domain);
		$url = 'https://api.vk.com/method/groups.getById';
		$params=array('v'=>$this->version,'group_id'=>$domain,'access_token'=>$this->token);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(isset($res['response']))
			return $res['response']['0']['id']; else return false;
	}
	function vk_messages_get_by_user___($acc_id,$uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.search';
		if($uid<1000) //chat
			$uid+=2000000000;
		$params=array(
		'v'=>$this->version,
		'version'=>$this->version,
		'access_token'=>$this->token,
		'peer_id'=>$uid,
		'date'=>date("dmY"),
		'count'=>50
		);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
			if($_SESSION['username']=='vlav') {
				print($this->last_response);
			}
		return json_decode($this->last_response,true);
	}
	function vk_messages_get_by_user($acc_id,$uid,$count=50) {
		//print "HERE_$uid"; exit;
		//~ if($uid<0) {
			//~ include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
			//~ $db=new db;
			//~ $vk_id=$db->dlookup("vk_id","cards","uid='$uid'");
			//~ if($vk_id>0)
				//~ $uid=$vk_id;
			//~ else
				//~ return false;
		//~ }
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.getHistory';
		//print "HERE_".$this->token; exit;
		if($uid<1000) //chat
			$uid+=2000000000;
		$params=array(
		'v'=>$this->version,
		'version'=>$this->version,
		'access_token'=>$this->token,
		'offset'=>0,
		'count'=>$count,
		'user_id'=>$uid,
		);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
			if($_SESSION['username']=='vlav') {
				 //print($this->last_response);
			}
		return json_decode($this->last_response,true);
	}

	function vk_messages_get_last_mid() {
		$mid=0;
		$res=$this->vk_messages_get_all($mid,$cnt=200);
		return $res;
	}
	function vk_messages_get_all_part($last_mid,$cnt=200) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.get';
		$params=array(
		'v'=>$this->version,
		'access_token'=>$this->token,
		'count'=>$cnt,
		'last_message_id'=>$last_mid
		);
		$last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$res=json_decode($last_response,true);
		//print_r($res);
		if(isset($res['error']))
			print $last_response;
		return $res;
	}
	function vk_messages_get_all($last_mid,$cnt=200) {
		$res=$this->vk_messages_get_all_part($last_mid,$cnt=200);
		if(isset($res['response']['items'])) {
			if(sizeof($res['response']['items'])==0 && $last_mid>0) {
				usleep(300000);
				$res1=$this->vk_messages_get_all_part($last_mid-1,$cnt=1);
				if(sizeof($res1['response']['items'])==0)	{
					print "error : vk_messages_get_all : last_mid=$last_mid <br>\n";
					include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
					$db=new db;
					$db->email($emails=array("vlav@mail.ru"), "error : vk_messages_get_all : last_mid=$last_mid", "", $from="noreply@winwinland.ru",$fromname="VKTRADE", $add_globals=true);
				}
			}
		}
		//exit;
		return $res;
	}
	var $last_mid=0;
	function vk_get_new_conversations_test($last_mid,$cnt=500) {
		$url = 'https://api.vk.com/method/messages.getConversations';
		$params=array('v'=>'5.80',
			'access_token'=>$this->token,
			'offset'=>0,
			'filter'=>'all',
			'count'=>$cnt,
			);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$response=json_decode($this->last_response,true);
	//	print_r($tresponse);
		if(!isset($response['response']['items'])) {
			if(isset($response['error']['error_code']))
				$this->error_code=$response['error']['error_code'];
			return false;
		}
		$msg=array();
		foreach($response['response']['items'] AS $item) {
			//print_r($item);
			if($item['last_message']['out']!=0)
				continue;
			$peer_id=$item['conversation']['peer']['id'];
			$mid=$item['conversation']['last_message_id'];
			if($mid<$last_mid)
				break;
			if($mid>$this->last_mid)
				$this->last_mid=$mid;
			$msg[]=array('uid'=>$peer_id,'mid'=>$mid,'date'=>$item['last_message']['date'],'body'=>$item['last_message']['text']);
		}
		return $msg;
	}
	function vk_get_new_conversations($last_mid,$cnt=100) {
		$url = 'https://api.vk.com/method/messages.getConversations';
		$params=array('v'=>$this->version,
			'access_token'=>$this->token,
			'offset'=>0,
			'filter'=>'all',
			'count'=>$cnt,
			);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		$response=json_decode($this->last_response,true);
		//print_r($response);
		if(!isset($response['response']['items'])) {
			if(isset($response['error']['error_code']))
				$this->error_code=$response['error']['error_code'];
			return false;
		}
		$msg=array();
		foreach($response['response']['items'] AS $item) {
			//print_r($item);
			if($item['last_message']['out']!=0)
				continue;
			$peer_id=$item['conversation']['peer']['id'];
			$mid=$item['conversation']['last_message_id'];
			if($mid<$last_mid)
				break;
			if($mid>$this->last_mid)
				$this->last_mid=$mid;
			$msg[]=array('uid'=>$peer_id,'mid'=>$mid,'date'=>$item['last_message']['date'],'body'=>$item['last_message']['text']);
		}
		return $msg;
	}
	function vk_messages_is_read($mid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.getById';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'message_ids'=>$mid);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(@$res['error'])
			return false;
		else
			return $res['response'][1]['read_state'];
	}
	function vk_ourchat_send($message, $uids=array("vladimir_avshtolis"), $chat_id=false) {
		$this->check_token($required=true);
		$tmp=$this->token;
		$this->token=$this->tokens['vlav'];
		if(!$chat_id) {
			foreach($uids AS $uid) {
				$this->vk_msg_send($uid, $message)."\n";
				sleep(1);
			}
		} else
			$this->vk_msg_send($uid=false, $message,$fake=false, $chat_id, $attachment=false);
		$this->token=$tmp;
	}
	function vk_msg_send_prepare($uid,$message) {
		if(strpos($message,"%user_name%")!==false) {
			$info=$this->vk_get_userinfo($uid);
			$message=str_replace("%user_name%",$info['first_name'],$message);
		}
		return $message;
	}
	function vk_msg_send($uid, $message, $fake=false, $chat_id=false, $attachment=false, $peer_id=false) {
		$this->check_token($required=true);
		if($fake) {
			print "vk_msg_send : fake mode : not really sent<br>\n";
			return 0;
		}
		//print "vk_msg_send<br>";
		$url = 'https://api.vk.com/method/messages.send';
		$params=array();
		if($uid!==false) {
			if(is_numeric($uid)) {
				$params['user_id']=$uid; 
			} else 	
				$params['domain']=$uid;
		}
		if($chat_id && is_numeric($chat_id) && !isset($params['user_id']))
			$params['chat_id']=$chat_id;
		if($peer_id!==false) {
			$params['peer_id']=$peer_id;
			unset($params['user_id']);
			unset($params['domain']);
		}
				//~ if($_SESSION['userid_sess']==1) {
					//~ print "HERE_".$message; exit;
				//~ }
			
		if($attachment)
			$params['attachment']=$attachment;
		$params['message']=$this->vk_msg_send_prepare($uid,$message);
		$params['access_token']=$this->token;
		$params['version']=$this->version;
		$params['v']=$this->version;
		$params['dont_parse_links']=1;
		$params['random_id']=time()+rand(0,9999999);
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
		if(strpos($this->last_response,"error")!==false) {
			$e=json_decode($this->last_response,true);
			return $e['error']['error_code'];
		} else
			return 0;
	}
	function vk_msg_mark_read($uid) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/messages.markAsRead';
		$params=array();
		$params['access_token']=$this->token;
		$params['v']=$this->version;
		$params['peer_id']=$uid;
		$params['start_message_id']=0;
		$this->last_response=file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded')))));
	//print_r($this->last_response);
		if(strpos($this->last_response,"error")!==false) {
			$e=json_decode($this->last_response,true);
			return $e['error']['error_code'];
		} else
			return 0;
	}

	function polls_getinfo($owner_id,$post_id) {
		/*
		poll-116176094_104
		where 	post_id=-116176094_104, $owner_id=-116176094
		$vk=new vklist_api("23cd9478c0311cd5e6221483751b99edb0a40d4b46814e8580acdb1611919ee520ff2914c3acccf448923");
		$vk->pools_getinfo(-116176094,"-116176094_104");
		$vk->pools_getvoters(-116176094,262956609, 881372285);
		*/
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/wall.getById';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'posts'=>$post_id );
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(!isset($res['response'][0]['attachments'])) {
			$this->last_response= "pools_getinfo : wrong post_id : $post_id";
			return false;
		}
		foreach($res['response'][0]['attachments'] AS $a) {
			if($a['type']=='poll') {
				$a['poll']['poll_id']=$a['poll']['id'];
				$poll_id=$a['poll']['poll_id'];
			}
		}
		if(!isset($poll_id)) {
			$this->last_response= "pools_getinfo : can not find poll_id";
			return false;
		} else
				print "poll_id=$poll_id owner_id=$owner_id\n";
		$answ=array("poll_id"=>$poll_id);
		$url = 'https://api.vk.com/method/polls.getById';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'owner_id'=>$owner_id,'poll_id'=>$poll_id );
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print $this->token;
		//print_r($res);
		if(!isset($res['response']['answers'])) {
			$this->last_response= "pools_getinfo : error getting answers";
			return false;
		}
		foreach($res['response']['answers'] AS $a) {
			$answ['answers'][]=$a;
		}
		return $answ;
		/*[id] => 882100273
		[text] => Да, мне это нужно
		[votes] => 8
		[rate] => 40*/
	}
	function polls_getvoters_part($owner_id,$poll_id, $answer_id,$offset=0,$count=100) {
		$this->check_token($required=true);
		//print "HERE $owner_id $poll_id $answer_id\n";
		$url = 'https://api.vk.com/method/polls.getVoters';
		$params=array('v'=>$this->version, 'access_token'=>$this->token,'count'=>$count,'offset'=>$offset,'owner_id'=>$owner_id,'poll_id'=>$poll_id,'answer_ids'=>$answer_id );
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print "<br><br>===========================<br><br>";
		//print_r( $res);
		if(isset($res['error']))
			return false;
		else {
			//unset($res['response'][0]['users'][0]);
			return $res['response'][0]['users']['items'];
		}
	}
	function polls_getvoters($owner_id,$poll_id, $answer_id,$count=500) {
		$offset=0; $uids=array();
		while(1) {
			$res=$this->polls_getvoters_part($owner_id,$poll_id, $answer_id,$offset,$count);
			usleep(300000);
			if (!$res)
				break;
			$uids=array_merge($uids,$res);
			if(sizeof($res)<$count)
				break;
			$offset+=$count;
		}
		return $uids;
	}
	function wall_repost($wall_id) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/wall.repost';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'object'=>$wall_id);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(@$res['error'])
			return false;
		else {
			return $res['response']['success'];
		}
	}
	function wall_post($owner_id, $message, $attachments="") {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/wall.post';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'owner_id'=>$owner_id,'message'=>$message, 'attachments'=>$attachments, 'from_group'=>1);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		$this->last_response=$res;
		if(isset($res['error'])) {
			return false;
		} else {
			return $res['response']['post_id'];
		}
	}
	function send_telegram_alert($msg,$chat_id, $bot_id) {
		//~ $url="https://api.telegram.org/bot".$bot_id."/sendMessage";
		//~ $data=array('chat_id'=>$chat_id,'text'=>$msg);
		//~ $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);
        //~ $context=stream_context_create($options);
        //~ $res=json_decode(file_get_contents($url,false,$context),true);
		//~ return (isset($res['ok']))?true:false;
		
		//$vk->send_telegram_alert("test ТЕСТ",$vk->telegram_chat_ids['vlav'], $vk->telegram_bots['avtotrade']);
		//print "HERE_send_telegram_alert";
		$res=json_decode(file_get_contents("https://api.telegram.org/bot".urlencode($bot_id)."/sendMessage?chat_id=$chat_id&text=".urlencode($msg).""),true);
		//print time()." $msg\n";
		return (isset($res['ok']))?true:false;
	}
	function telegram_get_updates() {
		$url="https://api.telegram.org/bot410546936:AAEjE0G6e3Yz0hs-Cs7_A-0iUYvYNk3ri8M/getUpdates";
		$res=json_decode(file_get_contents($url),true);
		print_r($res);
		foreach($res['result'] AS $r) {
			print_r($r);
		}
	}
	function ad_get_target_groups($cab_id) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/ads.getTargetGroups';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'account_id'=>$cab_id);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		$out="";
		return($res);
		if($res['error']) {
			print_r($res);
			return false;
		} else {
			foreach($res['response'] AS $arr) {
				$name=$arr['name'];
				$id=$arr['id'];
				$out.= "id=$id | $name | {$arr['audience_count']}\n";
			}
		}
		return $out;
	}
	function ad_add_target_contacts($cab_id,$target_group_id,$contacts) { 
		$this->check_token($required=true);
		if(!$target_group_id)
			return 0;
		//contacts - список телефонов, email адресов или идентификаторов пользователей, указанных через запятую
		$url = 'https://api.vk.com/method/ads.importTargetContacts';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'account_id'=>$cab_id,'target_group_id'=>$target_group_id,'contacts'=>$contacts);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		return $res;
	}
	function ad_add_target_contacts_($cab_id,$target_group_id,$contacts_arr) { 
		$this->check_token($required=true);
		if(!$target_group_id)
			return 0;
		if(sizeof($contacts_arr)==0)
			return 0;
		//contacts - список телефонов, email адресов или идентификаторов пользователей, указанных через запятую
		$db=new db();
		$contacts="";
		foreach($contacts_arr AS $uid) {
			if(!$db->dlookup("id","ad_add_target_contacts","uid='$uid'")) {
				$contacts.="$uid,";
			}
		}
		if($contacts=="")
			return 0;
		$url = 'https://api.vk.com/method/ads.importTargetContacts';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'account_id'=>$this->ad_account_id,'target_group_id'=>$target_group_id,'contacts'=>$contacts);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		//print_r($res);
		if(!isset($res['error'])) {
			$uids=explode(",",$contacts);
			foreach($uids AS $uid) {
				if($uid>0)
					$db->query("INSERT INTO ad_add_target_contacts SET uid='".trim($uid)."',tm='".time()."',target_group_id='$target_group_id',script_name='".$db->escape($_SERVER["SCRIPT_NAME"])."'");
			}
			return $res['response'];
		} else
			return "Error";
	}
	function ad_get_cabinets() {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/ads.getAccounts';
		$params=array('v'=>$this->version,'access_token'=>$this->token);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		return $res;
	}
	function ad_create_target_group($name,$ad_cab_id) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/ads.createTargetGroup';
		$params=array('v'=>$this->version,'access_token'=>$this->token,'account_id'=>$ad_cab_id,'name'=>$name);
		$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		return $res;
	}
	function is_group_member($uid,$gid_arr) {
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/groups.isMember';
		foreach($gid_arr AS $gid) {
			if(!intval($gid))
				continue;
			$params=array('v'=>$this->version,'access_token'=>$this->token, 'user_id'=>$uid, 'group_id'=>$gid);
			$res=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
			if(isset($res['response']))
				if($res['response']==1)
					return true;
			if(isset($res['error']))
				print "error : is_group_member : $gid <br>\n";
			usleep(300000);
		}
		return false;
	}
	function likes_getlist($gid,$item_id) {
		$gid=-$gid; $item_id=$item_id;
		$this->check_token($required=true);
		$url = 'https://api.vk.com/method/likes.getList';
		$params=array('v'=>$this->version,
				'access_token'=>$this->token,
				'type'=>'post',
				'owner_id'=>$gid,
				'item_id'=>$item_id,
				'count'=>1000,
				'offset'=>0,
				'filter'=>'likes'
				);
		$r=json_decode(file_get_contents($url, false, stream_context_create(array('http' => array('method'=>'POST','header'=>'Content-type: application/x-www-form-urlencoded','content'=>http_build_query($params),'request_fulluri' => true,'proxy' => self::$proxy,'header' => array('Proxy-Authorization: Basic '.base64_encode(self::$proxy_passw),'Content-type: application/x-www-form-urlencoded'))))),true);
		if(isset($r['response']))
			return $r['response']['items'];
		else {
			$this->error=$r['error']['error_code'];
			return false;
		}	
	}
}
?>
