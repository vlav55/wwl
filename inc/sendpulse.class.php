<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
$senpulse_path= "/var/www/vlav/data/www/wwl/inc/sendpulse";
require("$senpulse_path/src/ApiInterface.php");
require("$senpulse_path/src/ApiClient.php");
require("$senpulse_path/src/Storage/TokenStorageInterface.php");
require("$senpulse_path/src/Storage/FileStorage.php");
require("$senpulse_path/src/Storage/SessionStorage.php");
require("$senpulse_path/src/Storage/MemcachedStorage.php");
require("$senpulse_path/src/Storage/MemcacheStorage.php");

use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

// API credentials from https://login.sendpulse.com/settings/#api
define('API_USER_ID', 'd826fe25d75aaa7c4ed4c926a736b29f');
define('API_SECRET', 'f164eab0b20c4c41441ff77793cea10f');
define('PATH_TO_ATTACH_FILE', __FILE__);

class sendpulse extends db {
	var $projects=[
		'yogahelpyou'=>['API_USER_ID'=>'d826fe25d75aaa7c4ed4c926a736b29f','API_SECRET'=>'f164eab0b20c4c41441ff77793cea10f'],
		'f12'=>['API_USER_ID'=>'2b8542ad092a10b43cb85d2e4ec6e925','API_SECRET'=>'7921fc0eda9d255ca7b8aeb09939efd4'],
		];
	var $SPApiClient;
	var $err=false;
	var $project=false;
	function __construct($p=false) {
		if($p && !isset($this->projects[$p])) {
			$this->SPApiClient=$p;
			$this->project=false;
			return;
		}
		if(!$p) {
			$this->SPApiClient = new ApiClient(API_USER_ID, API_SECRET, new FileStorage());
			$this->project='yogahelpyou';
		} else {
			$this->SPApiClient = new ApiClient($this->projects[$p]['API_USER_ID'], $this->projects[$p]['API_SECRET'], new FileStorage());
			$this->project=$p;
		}
	}
	function list_address_books() {
		if(!$this->project)
			return true;
		return $this->SPApiClient->listAddressBooks();
	}
	function del($bookID,$emails) { //emails=[$email]
		if(!$this->project)
			return true;
		$res=$this->SPApiClient->removeEmails($bookID, $emails);
		if(@$res->is_error) {
			$this->err=$res;
			$this->yoga_email("sendpulse error DEL: $email",nl2br(print_r($res,true)));
			return false;
		}
		return true;
	}
	function add($book_id,$email,$phone="",$name="",$uid=0,$uid_md5='') {
		if(!$this->project)
			return true;
		if(!$book_id)
			return true;
		if(!$this->validate_email($email))
			return true;
		$emails = array(
			array(
				'email' => $email,
				'variables' => array(
					'phone' => $phone,
					'name' => $name,
					'uid'=>$uid,
					'uid_md5'=>$uid_md5,
				)
			)
		);
		$res=$this->SPApiClient->addEmails($book_id, $emails);
		if(@$res->is_error) {
			$this->err=$res;
			$this->yoga_email("sendpulse error add: $email",nl2br(print_r($res,true))."\n".nl2br(print_r($GLOBALS,true)));
		}
		$_SESSION['fl_sp']=true;
		return true;
	}
	function update_vars($book_id,$email,$vars) {
		if(!$this->project)
			return true;
		if(!$this->validate_email($email))
			return false;
		/**
		 * Change varible by user email
		 *
		 * @param int $bookID
		 * @param string $email User email
		 * @param array $vars User vars in [key=>value] format
		 * @return stdClass
		 */
		//public function updateEmailVariables(int $bookID, string $email, array $vars)
		$res=$this->SPApiClient->updateEmailVariables($book_id, $email, $vars);
		if(@$res->is_error) {
			$this->err=$res;
			$this->yoga_email("sendpulse error update_var: $email",nl2br(print_r($res,true))."\n".nl2br(print_r($GLOBALS,true)));
		}
	}
	function get_var($book_id,$email,$var_name) {
		if(!$this->project)
			return true;
		$res=$this->SPApiClient->getEmailInfo($book_id, $email);
		foreach($res->variables AS $v) {
			if($v->name==$var_name)
				return $v->value;
		}
		return false;
	}
	function email_send($r) {
		if(!$this->project)
			return true;
		$email = array(
			'html' => 'test',
			'text' => 'test',
			'subject' => 'Mail subject',
			'from' => array(
				'name' => 'John',
				'email' => 'info@formula12.ru',
			),
			'to' => array(
				array(
					'name' => 'Subscriber Name',
					'email' => 'vlav@mail.ru',
				),
			),
			'attachments' => array(
				//'file.txt' => file_get_contents(PATH_TO_ATTACH_FILE),
			),
		);
		return ($this->SPApiClient->smtpSendMail($r));

	}

	var $email_by_template_vars=false;
	function email_by_template($template_id,
								$to_email,
								$to_name,
								$subj,
								$from_email='',
								$from_name='',
								$uid=false,$phone=false) {
		if(!$this->validate_email($to_email))
			return false;

		if(!empty($this->SPApiClient) && !$this->project) {
			include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
			if($this->SPApiClient=='vkt') {
				$database='vkt';
				$temlate_path='/var/www/vlav/data/www/wwl/scripts/sendpulse/';

				include_once "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
				$vkt=new vkt('vkt');
				if($ctrl_id=$vkt->get_ctrl_id_by_uid($uid)) {
					$vkt_link=$vkt->get_ctrl_link($ctrl_id);
				} else
					$vkt_link='error';

			} elseif($this->SPApiClient=='yogahelpyou_smtp') {
			}
			$db=new db($database);
			if(!$msg=file_get_contents($temlate_path.$template_id)) {
				print "File ".$temlate_path.$template_id." is not found <br>";
				return false;
			}
			$uid_md5=$db->uid_md5($uid);
			$msg=preg_replace("/{{vkt_link}}/m", $vkt_link, $msg);
			$msg=preg_replace("/{{uid}}/m", $uid, $msg);
			$msg=preg_replace("/{{uid_md5}}/m", $uid_md5, $msg);
			$msg=preg_replace("/{{email}}/m", $to_email,$msg);
			$phone=$phone?$phone:"";
			$msg=preg_replace("/{{mob}}/m", $phone,$msg);
			$msg=preg_replace("/{{passw}}/m", $phone,$msg);
			$db->email([$to_email],$subj, $msg, $from_email, $from_name);
			return true;
		} elseif($this->project=='yogahelpyou' && !intval($template_id)) {

			$temlate_path='/var/www/html/pini/yogahelpyou/1/scripts/sendpulse/';
	//print "smtpbz <br>";

			if(!$msg=file_get_contents($temlate_path.$template_id)) {
				print "File ".$temlate_path.$template_id." is not found <br>";
				return false;
			}
			$msg=preg_replace("/{{uid}}/m", $uid, $msg);
			$msg=preg_replace("/{{email}}/m", $to_email,$msg);
			$phone=$phone?$phone:"";
			$msg=preg_replace("/{{mob}}/m", $phone,$msg);

			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.smtp.bz/v1/smtp/send",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_HTTPHEADER => array(
				"authorization: wY3uRnaJyLHYm29Q52NM4kXJCJvhVpmhefBV"
			  ),
			  CURLOPT_POSTFIELDS => http_build_query(array(
				'subject' => $subj, // Обязательно
				'name' => $from_name,
				'html' => $msg,
				'reply' => $from_email, //"info@yogahelpyou.com",
				'from' => $from_email,  //"info@yogahelpyou.com", // Обязательно
				'to' => $to_email, // Обязательно
				'headers' => "[{ 'msg-type': 'media' }]",
				'text' => strip_tags($msg)
			  ))
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				$this->err=$err;
	//echo "cURL Error #:" . $err;
				return false;
			} else {
	//echo $response;
				return true;
			}
			
		}

		$email = array(
			'subject' => $subj,
			'template' => [
						'id'=>$template_id,
						'variables'=>['uid'=>$uid,'uid_md5'=>$uid,'email'=>$to_email,'name'=>$to_name, 'phone'=>$phone]
							],
			'from' => array(
				'name' => $from_name,
				'email' => $from_email,
			),
			'to' => array(
				array(
					'name' => $to_name,
					'email' => $to_email,
				),
			),
			'attachments' => array(
				//'file.txt' => file_get_contents(PATH_TO_ATTACH_FILE),
			),
		);

		if($this->email_by_template_vars) {
			$email['template']['variables']=array_merge($email['template']['variables'],$this->email_by_template_vars);
		}
		$res=$this->SPApiClient->smtpSendMail($email);
		if($res->result)
			return true;
		else {
			$this->err=$res;
	//		$this->yoga_email("sendpulse error: email_by_template: $email",nl2br(print_r($email,true)."\n\nRES\n".print_r($res,true)."\n\nGLOBALS\n".print_r($GLOBALS,true)));
			return false;
		}
	}
	function email_by_template_test($template_id,
								$to_email,
								$to_name,
								$subj,
								$from_email='',
								$from_name='',
								$uid=false,$phone=false) {
		$email = array(
			'subject' => $subj,
			'template' => [
						'id'=>$template_id,
						'variables'=>['uid'=>$uid,'uid_md5'=>$uid,'email'=>$to_email,'name'=>$to_name, 'phone'=>$phone]
							],
			'from' => array(
				'name' => $from_name,
				'email' => $from_email,
			),
			'to' => array(
				array(
					'name' => $to_name,
					'email' => $to_email,
				),
			),
			'attachments' => array(
				//'file.txt' => file_get_contents(PATH_TO_ATTACH_FILE),
			),
		);
		return $this->SPApiClient->smtpSendMail($email);
		if($res)
			return true;
		else {
			$this->err=$res;
			$this->yoga_email("sendpulse error: email_by_template: $email",nl2br(print_r($email,true)."\n\nRES\n".$res."\n\nGLOBALS\n".print_r($GLOBALS,true)));
			return false;
		}
	}
}
?>
