<?
class pact {
	var $token="";
	var $company_id=0;
	var $tokens=[];
	var $attach=[];
	var $client=false;
	public $attach_file_name=null;
	public $res=[];

	function __construct($token=false,$company_id=false) {
		$this->login($token,$company_id);
	}
	function login($token,$company_id) {
		$this->token=$token;
		$this->company_id=!$company_id ? $this->get_company_id() : $company_id;
	}
	function test() {
		global $db,$uid;
		if($db->pact_token=="papa") {
			if(!$this->get_user_token($db,$uid) )
				return false;
		}
		print "HERE_$uid $this->token";
		require("/var/www/vlav/data/www/wwl/inc/pact/vendor/autoload.php");
		$client = new \Pact\PactClient($this->token);
		$res=$client->companies->getCompanies();
		print_r( $res);
	}
	function upload_attachment($fname,$conversation) {
		global $db,$uid;
		if($db->pact_token=="papa") {
			if(!$this->get_user_token($db,$uid) )
				return false;
		}
		require("/var/www/vlav/data/www/wwl/inc/pact/vendor/autoload.php");
		$client = new \Pact\PactClient($this->token);
		if(!$file_path = realpath($fname))
			return false;
		$res = $client->attachments->uploadFile($this->company_id, $conversation, $file_path);
		if($res->status=='ok')
			return $res->data->external_id;
		print_r($res);
		return false;
	}
	function create_company($name,$webhook_url,$phone="",$descr="") {
		$url="https://api.pact.im/p1/companies";
		$params=array('name'=>$name,
				'webhook_url'=>$webhook_url,
				'phone'=>$phone,
				'description'=>$descr);
		$r=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('method'=>'POST',
									'header'=>'X-Private-Api-Token: '.$this->token,
									'content'=>http_build_query($params)))))
							, true);
		//print_r($r);
		if($r['status']=='created')
			return $r['data']['external_id'];
		else
			return false;
	}
	function hide_company($company_id, $hide=true) {
		$url="https://api.pact.im/p1/companies/$company_id";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['hidden'=>$hide]) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token,'Content-Type: application/json') );
		$res = json_decode(curl_exec($ch),true); 
		curl_close($ch);

		print_r($http_response_header);
		print_r($res);
		if($res['status']=="updated")
			return $res['data']['external_id']; else return false;
	}
	function get_all_companies() {
		$url="https://api.pact.im/p1/companies";
		$res=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('header'=>'X-Private-Api-Token: '.$this->token))))
							, true);
		print nl2br(print_r($res,true));
		//~ $cid=intval($res['data']['companies'][0]['external_id']);
		//~ $this->company_id=$cid;
		//~ if($res['status']=="ok")
			//~ return $cid; else return false;
	}
	function get_company_id($company_name="") {
		$url = "https://api.pact.im/p1/companies";
		
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'X-Private-Api-Token: ' . $this->token
			],
		]);
		
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($response === false) {
			// Handle cURL error
			return false;
		}
		
		$res = json_decode($response, true);
		$this->company_id = 0;

		if($res['status'] == "ok") {
			foreach($res['data']['companies'] AS $r) {
				if($r['name'] == $company_name)
					$this->company_id = $r['external_id'];
			}
			if(!$this->company_id)
				$this->company_id = intval($res['data']['companies'][0]['external_id']);
			return $this->company_id;
		} else {
			//print_r($res);
		}
		return false;
	}
	function delete_channel($company_id,$channel_id) {
		$url="https://api.pact.im/p1/companies/$company_id/channels/$channel_id";
		print "delete channel company_id=$company_id channel_id=$channel_id \n";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token) );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token,'Content-Type: application/json') );
		$res = curl_exec($ch);
		curl_close($ch);

		print $res;

		return $res;

		
		$r=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('method'=>'DELETE',
									'header'=>'X-Private-Api-Token: '.$this->token))))
							, true);
		//print_r($http_response_header);
		print "<br><br>";
		print_r($r);
		if($r['status']=="deleted")
			return "deleted"; else return false;
	}
	function create_channel($company_id) {
		$url="https://api.pact.im/p1/companies/$company_id/channels/$channel_id";
		$params=array('provider'=>'whatsapp',
				'sync_messages_from'=>time()
				);
		$r=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('method'=>'POST',
									'header'=>'X-Private-Api-Token: '.$this->token,
									'content'=>http_build_query($params)))))
							, true);
		print_r($r);
		if($http_response_header[0]=='HTTP/1.1 402 Payment Required')
			return 402;
		if($r['status']=='created')
			return $r['data']['external_id'];
		else
			return false;
		//Array ( [status] => created [data] => Array ( [external_id] => 83055 [billing_start_date] => 1623926097 ) ) 83055
	}
	function get_channel_id() {
		$url="https://api.pact.im/p1/companies/$this->company_id/channels";
		$res=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('header'=>'X-Private-Api-Token: '.$this->token))))
							, true);
		if($res['status']=="ok")
			return intval($res['data']['channels'][0]['external_id']); else return false;
	}
	function get_conversations($from=0,$cnt=50) {
		$url="https://api.pact.im/p1/companies/$this->company_id/conversations";
		$res=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array('header'=>'X-Private-Api-Token: '.$this->token))))
							, true);
		if($res['status']=="ok")
			return $res['data']; else return false;
	}
	function new_conversation__($phone) {
		if(!$this->company_id) {
			print "pact:new_conversation:company_id error";
			return false;
		}
		$url="https://api.pact.im/p1/companies/$this->company_id/conversations";
		$params=array('provider'=>'whatsapp','phone'=>$phone);
		$res=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array(
								'header'=>['X-Private-Api-Token: '.$this->token,'Content-type: application/x-www-form-urlencoded'],
								'method'=>'POST','content'=>http_build_query($params)
								))))
							, true);

		return $res;
	}
	function new_conversation($phone) {
		global $db;
		if(!$this->company_id) {
			print "pact:new_conversation:company_id error";
			return false;
		}
		$url="https://api.pact.im/p1/companies/$this->company_id/conversations";
		$params=array('provider'=>'whatsapp','phone'=>$phone);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token,'Content-Type: application/json') );
		$res = json_decode(curl_exec($ch),true); 
		curl_close($ch);
		//$db->notify_me($url."\n$this->token \n".print_r($params,true));
		if($res['status'] == 'errored')
			$db->notify_me("pact new_conversation($phone) ctrl_id=$db->ctrl_id \n".print_r($res,true));
		//$db->notify_me("_new_conversation $phone $url\n".print_r($res,true));
		//~ print_r($http_response_header);
		return $res;
	}
	function get_user_token($db,$uid) {
		if(!$uid) {
			print "Error: pact:get_user_token: uid=0";
			return false;
		}
		$user_id=$db->dlookup("user_id","cards","uid='$uid'");
		if($user_id) {
			$this->token=$db->dlookup("pact_token","users","id='$user_id'");
			$this->company_id=$db->dlookup("pact_company_id","users","id='$user_id'");
			if(empty($this->token)) {
				print "Error: pact:get_user_token: TOKEN IS EMPTY";
				return false;
			}
			if(!$this->company_id) {
				print "Error: pact:get_user_token: company_id==0";
				return false;
			}
			return true;
		} else {
			print "Error: pact:get_user_token: user_id=0";
			return false;
		}
	}
	function get_cid($db,$uid) {
		$cid=$db->dlookup("pact_conversation_id","cards","uid='$uid'");
		if(!$cid) {
			$mob=$db->dlookup("mob_search","cards","uid='$uid'");
			if(empty($mob) || !$mob) {
				//print "<div class='alert alert-danger' >Ошибка new_conversation. mobile number is empty</div>";
				return false;
				//~ $mob=$db->check_mob($db->dlookup("mob_search","cards","uid='$uid'"));
				//~ $db->query("UPDATE cards SET mob_search='".$db->escape($mob)."' WHERE uid='$uid'");
			}
			$r=$this->new_conversation($mob);
			//$db->notify_me(print_r($r,true));
			if($r['status']!="ok") {
				//~ print "<div class='alert alert-danger' >Ошибка new_conversation</div>";
				//~ print_r($r);
				return false ;
			}
			$cid=intval($r['data']['conversation']['external_id']);
			$db->query("UPDATE cards SET pact_conversation_id='$cid' WHERE uid='$uid'");
		}
		return $cid;
	}
	function get_cid_by_phone($mob) {
		$r=$this->new_conversation($mob);
		if($r['status']!="ok") {
			$this->res=$r;
			//print "HERE_$mob";
			return false ;
		}
		return intval($r['data']['conversation']['external_id']);
	}
	var $send_msg_error=false;
	function send_msg($conversation_id,$msg) {
		$this->send_msg_res=false;
		$url="https://api.pact.im/p1/companies/$this->company_id/conversations/$conversation_id/messages";
		//print $url;exit;
		if(!is_array($this->attach))
			$this->attach=[];
		
		if(sizeof($this->attach)>0)
			$params=array("message"=>$msg,"attachments_ids"=>$this->attach); 
		else
			$params=array("message"=>$msg); 

		//~ $res=json_decode(file_get_contents($url, false, stream_context_create(
							//~ array('http' =>
								//~ array(
								//~ 'header'=>['X-Private-Api-Token: '.$this->token,'Content-type: application/x-www-form-urlencoded'],
								//~ 'method'=>'POST','content'=>http_build_query($params)
								//~ ))))
							//~ , true);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token,'Content-Type: application/json') );
		$res = json_decode(curl_exec($ch),true); 
		curl_close($ch);

		$this->send_msg_res=$res;

 		if($res['status']!="ok") {
			//~ print "WA send error: cid=$conversation_id company_id=$this->company_id <br>\n";
			//~ print "URL\n"; print "$url <br>\n";
			//~ print "RES\n"; print_r($res); print "<br>\n";
			//~ print "PARAMS\n"; print_r($params); print "<br>\n";
			return $res;
		}
		return $res;
	}
	function send($db,$uid,$msg,$user_id=0, $num=0,$source_id=0,$save_outg=false,$force_if_not_wa_allowed=false) {
		$wa_allowed=$db->dlookup("wa_allowed","cards","uid='$uid'");
		if(!$wa_allowed && !$force_if_not_wa_allowed)
			return false;
		if($db->pact_token=="papa") {
			if(!$this->get_user_token($db,$uid) )
				return false;
		}
		$cid=$this->get_cid($db,$uid);
		if($this->attach_file_name) {
			$this->attach=[$this->upload_attachment($this->attach_file_name,$cid)];
			$this->attach_file_name=null;
		}
		
		$r=$this->send_msg($cid,$msg);
		if($r['status']=="ok") {
			if($save_outg || $source_id) {
				$outg=($source_id)?2:1;
				$db->query("INSERT INTO msgs SET
							uid='$uid',
							acc_id=101,
							tm=".time().",
							user_id='$user_id',
							msg='".$db->escape($msg)."',
							outg=$outg,
							vote='$num',
							new='".intval($num)."',
							source_id='$source_id'					
							",0);
			}
			return true;
		} else {
			$db->query("UPDATE cards SET pact_conversation_id=0 WHERE uid='$uid'");
			$cid=$this->get_cid($db,$uid);
			$r=$this->send_msg($cid,$msg);
			if($r['status']=="ok") {
				if($save_outg || $source_id) {
					$outg=($source_id)?2:1;
					$db->query("INSERT INTO msgs SET
								uid='$uid',
								acc_id=101,
								tm=".time().",
								user_id='$user_id',
								msg='".$db->escape($msg)."',
								outg=$outg,
								vote='$num',
								new='".intval($num)."',
								source_id='$source_id'					
								",0);
				}
				return true;
			} else
				return false;
		}
		return false;
	}
	function set_web_hook($web_hook_url) {
		$url="https://api.pact.im/p1/companies/$this->company_id";
		$params=array("webhook_url"=>$web_hook_url);
		$res=json_decode(file_get_contents($url, false, stream_context_create(
							array('http' =>
								array(
								'header'=>'X-Private-Api-Token: '.$this->token,
								'method'=>'PUT','content'=>http_build_query($params)
								))))
							, true);
		if($res['status']=="ok")
			return $res['data']; else return false;
	}
}


?>
