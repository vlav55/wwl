<?
class justclick_api { //111  
	var $user_rs=array('user_id' => 'yogacenter','user_rps_key' => '8948f07def594f8d34cdc34f0188e99d');
	var $users=[
		'style-inside'=>['user_id' => 'style-inside','user_rps_key' => '093a77cd5321b86c6334ce410bcf9124','gid_ok'=>'1608202637.2284137403', 'gid_ban'=>'1608202720.8613846517'],
		'papa'=>['user_id' => 'formula12','user_rps_key' => '544d70cb4cf5241b21b4fe367dbe8234','gid_ok'=>'1622752987.3355633443', 'gid_ban'=>'1622753017.955720807']
			];
	var $error_code=0;
	var $error_text=null;
	var $gid_ok="1580329366.8204686068";
	var $gid_ban="1595591409.2195132425";
	var $gids=array("week0"=>"1578515091.9668659223",
					"webinar-registered"=>"1583874262.7523841141",
					"webinar-registered-2"=>"1583353483.1728736766",
					"webinar-registered-5days"=>"1586281127.3515980491",
					"webinar-registered-today"=>"1586981571.7365208128",
					"webinar-visited"=>"1569268863.913977502",
					"webinar-repeat"=>"1569347886.3808730932",
					"OK"=>"1580329366.8204686068",
					"clients_1"=>"clients_1",
					"WEEK0-3DAYS"=>"1583359637.2243118155",
					"100"=>"1583359637.2243118155",
					"101"=>"1586122781.211104304",
					"catalog_asan"=>"1586515723.2159277630",
					"free_course"=>"1592143771.7189332705",
					"103"=>"1593555486.4483862836",
					"anosov"=>"1594809422.1563752354",
					);
	var $lead_phone=false;
	function login($user=false) {
		if(!$user)
			return;
		if(isset($this->users[$user]) ) {
			$this->user_rs['user_id']=$this->users[$user]['user_id'];
			$this->user_rs['user_rps_key']=$this->users[$user]['user_rps_key'];
			$this->gid_ok=$this->users[$user]['gid_ok'];
			$this->gid_ban=$this->users[$user]['gid_ban'];
		}
	//print "HERE_".$this->user_rs['user_id']." ".$this->users[$user]['user_id'];
	}
	function ban($email) {
		return $this->add_to_group($this->gid_ban,$email);
	}
	function add_to_group($group_id,$email,$name=false,$doneurl2=false) {
	//	print "HERE_".$this->user_rs['user_id'];
		if(!$group_id)
			return true;
		if(empty($email))
			return false;
		if($email==1) //for testing
			return true;
		$this->error_code=0;
		$send_data = array(
				'rid[0]' => $group_id, // группа, в которую попадёт подписчик
				'rid[1]' => $this->gid_ok, //OK
				'lead_email' => $email
			);
		if($name)
			$send_data['lead_name']=$name;
		if($doneurl2)
			$send_data['doneurl2']=$doneurl2;
		if($this->lead_phone)
			$send_data['lead_phone']=$this->lead_phone;
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/AddLeadToGroup', $send_data));
		if(!$this->CheckHash($resp, $this->user_rs)){
	//		echo "Ошибка! Подпись к ответу не верна!";
			return false;
		}
		if($resp->error_code == 0) {
		//	echo "Пользователь добавлен в группу {$send_data['rid[0]']}. Ответ сервиса: {$resp->error_code}";
			return true;
		} else {
			$this->error_code=$resp->error_code;
			$this->error_text=$resp->error_text;
		//	echo "<div class='alert alert-danger' >Ошибка JC код:{$resp->error_code} - описание: {$resp->error_text}</div>\n";
			return false;
		}
	}
	function delete_from_group($group_id,$email) {
		$this->error_code=0;
		$send_data = array(
				'rass_name' => $group_id,
				'lead_email' => $email
			);
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		//print_r($send_data);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/DeleteSubscribe', $send_data));
		// Проверяем ответ сервиса
		if(!$this->CheckHash($resp, $this->user_rs)) {
			echo "Ошибка! Подпись к ответу не верна!";
			return false;
		}
		if($resp->error_code == 0) {
		//	echo "Пользователь $email отписан из группы {$send_data['rass_name']}. Ответ сервиса: $resp->error_code <br>\n";
			return true;
		} else {
			$this->error_code=$resp->error_code;
			echo "Ошибка код:{$resp->error_code} - описание: {$resp->error_text} <br> \n";
			return false;
		}
	}
	function is_subscribed($gid,$email) {
		$this->error_code=0;
		$send_data = array(
				'email' => $email
			);
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/GetLeadGroupStatuses', $send_data));
		// Проверяем ответ сервиса
		if(!$this->CheckHash($resp, $this->user_rs)) {
			echo "Ошибка! Подпись к ответу не верна!";
			return false;
		}
		$this->error_code=$resp->error_code;
		if($resp->error_code == 0 || $resp->error_code == 501){
			foreach($resp->result AS $r) {
				if($r->rass_name==trim($gid) && $r->rass_status=='STATUS_SUBSCRIBE')
					return true;
			}
			return false;
		} else {
			echo "Ошибка email=$email код:{$resp->error_code} - описание: {$resp->error_text} <br>\n";
			return false;
		}
	}
	function get_status($email) {
		$this->error_code=0;
		$send_data = array(
				'email' => $email
			);
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/GetLeadGroupStatuses', $send_data));
		// Проверяем ответ сервиса
		if(!$this->CheckHash($resp, $this->user_rs)) {
			echo "Ошибка! Подпись к ответу не верна!";
			return false;
		}
		$this->error_code=$resp->error_code;
		if($resp->error_code == 0 || $resp->error_code == 501){
			return ($resp->result[0]->rass_status);
		} else {
			return false;
		}
	}
	function get_all_subscriptions($email) {
		$this->error_code=0;
		$send_data = array(
				'email' => $email
			);
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/GetLeadGroupStatuses', $send_data));
		// Проверяем ответ сервиса
		if(!$this->CheckHash($resp, $this->user_rs)) {
			echo "Ошибка! Подпись к ответу не верна!";
			return false;
		}
		$this->error_code=$resp->error_code;
		if($resp->error_code == 0 || $resp->error_code == 501){
			$res=array();
			//print_r($resp);
			foreach($resp->result AS $r) {
				if($r->rass_status=='STATUS_SUBSCRIBE')
					$res[]=trim($r->rass_title);
				if($r->rass_status=='STATUS_WAIT')
					$res[]="ЕМЭЙЛ НЕ ПОДТВЕРЖДЕН";
				if($r->rass_status=='STATUS_INVALID_EMAIL')
					$res[]="ЕМЭЙЛ НЕ СУЩЕСТВУЕТ";
			}
			return $res;
		} else {
			echo "Ошибка email=$email код:{$resp->error_code} - описание: {$resp->error_text} <br>\n";
			return false;
		}
	}
	function add_contact($group_id,$contact=array(),$utm=array(),$activation=false, $subscr_2="thanks.php") {
		$contact['email']=(isset($contact['email']))?$contact['email']:null;
		$contact['name']=(isset($contact['name']))?$contact['name']:null;
		$contact['phone']=(isset($contact['phone']))?$contact['phone']:null;
		$contact['city']=(isset($contact['city']))?$contact['city']:null;
		$utm['medium']=(isset($utm['medium']))?$utm['medium']:null;
		$utm['source']=(isset($utm['source']))?$utm['source']:null;
		$utm['campaign']=(isset($utm['campaign']))?$utm['campaign']:null;
		$utm['content']=(isset($utm['content']))?$utm['content']:null;
		$utm['term']=(isset($utm['term']))?$utm['term']:null;
		$send_data = array(
				 'rid[0]' =>$group_id, // группа, в которую попадёт подписчик. Значение можно взять с вкладки API ID при редактировании группы (в Личном Кабинете)
				 'lead_email' =>$contact['email'],
				 'lead_name' =>$contact['name'],
				 'lead_phone' =>$contact['phone'],
				 'lead_city' =>$contact['city'],
				 'doneurl2' =>$subscr_2, // адрес после подтверждения подписки
				 'activation' =>$activation, // требуем подтверждение подписки
				 'utm[utm_medium]' =>$utm['medium'],
				 'utm[utm_source]' =>$utm['source'],
				 'utm[utm_campaign]' =>$utm['campaign'],
				 'utm[utm_content]' =>$utm['content'],
				 'utm[utm_term]' =>$utm['term'],
			 );
		 // Формируем подпись к передаваемым данным
		 $send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		 // Вызываем функцию AddLeadToGroup в API и декодируем полученные данные
		 $resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/AddLeadToGroup', $send_data));
		 // Проверяем ответ сервиса
		 if(!$this->CheckHash($resp, $this->user_rs)) {
			 echo "Ошибка! Подпись к ответу не верна!";
			 return false;
		 }
		 if($resp->error_code == 0) {
			return true; //echo "Пользователь добавлен в группу {$send_data['rid[0]']}. Ответ сервиса: {$resp->error_code}";
		 } else {
			 $this->error_code=$resp->error_code;
			 $this->error_text=$resp->error_text;
			return false; //echo "Ошибка код:{$resp->error_code} - описание: {$resp->error_text}";
		 }
	}

	function add_tag($email,$tag) {
		$send_data = array(
			'lead_email' => $email, // имейл существующего подписчика
			'lead_tags' => $tag, //'tag1,tag2,tag3', // теги подписчика
			);
		$send_data['hash'] = $this->GetHash($send_data, $this->user_rs);
		$resp = json_decode($this->Send('https://'.$this->user_rs['user_id'].'.justclick.ru/api/UpdateSubscriberData', $send_data));
		if(!$this->CheckHash($resp, $this->user_rs)) {
			 echo "Ошибка! Подпись к ответу не верна!";
			 return false;
		}
		if($resp->error_code == 0) {
			return true; //
		} else {
			$this->error_code=$resp->error_code;
			$this->error_text=$resp->error_text;
			return false; //echo "Ошибка код:{$resp->error_code} - описание: {$resp->error_text}";
		}
    }

	function Send($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // выводим ответ в переменную
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
	// Формируем подпись к передаваемым в API данным
	function GetHash($params, $user_rs) {
		$params = http_build_query($params);
		$user_id = $user_rs['user_id'];
		$secret = $user_rs['user_rps_key'];
		$params = "$params::$user_id::$secret";
		return md5($params);
	}
	// Проверяем полученную подпись к ответу
	function CheckHash($resp, $user_rs) {
		$secret = $user_rs['user_rps_key'];
		$code = $resp->error_code;
		$text = $resp->error_text;
		$hash = md5("$code::$text::$secret");
		if($hash == $resp->hash)
			return true; // подпись верна
		else
			return false; // подпись не верна
	}

}
?>
