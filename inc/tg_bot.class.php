<?
require "/var/www/vlav/data/www/wwl/inc/tg_api/vendor/autoload.php";
class tg_bot {
	var $tokens=['f12'=>'1820548789:AAGejAyt2oBcru_EsvVwU6JGlUNj_SyYvo8',
				'yogahelpyou_bot'=>'954808749:AAFmStxrxNG1hee_dspmp88_aWoxrNPL2wg',
				'julietavshtolis_bot'=>'5353319913:AAEJWzEplz54bcJc_jEpfzjl6nqFuKwd8LY',
				'vkt_manager_bot'=>'5761252981:AAFEYbOttMbSf42zxx3D83Rh3gOszRvgavY', //'5134301126:AAGeYtSwhNAuipFFMaDOjIpNtYCW4_2-4v4',
				'vktrade_bot'=>'484406014:AAGo7T30Is2pC4QKFsCidOcg2p-Zz36g5L8',
				];
	var $token=false;
	var $bot;
	function __construct($token_id) {
		if(isset($this->tokens[$token_id]))
			$this->token=$this->tokens[$token_id];
		else
			$this->token=$token_id;
		$this->bot = new \TelegramBot\Api\Client($this->token);
	}
	function set_webhook($url_to) {
		$url="https://api.telegram.org/bot".$this->token."/setWebhook";
		$ch = curl_init($url);
		$params=['url'=>$url_to];
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($params) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Private-Api-Token: '.$this->token,'Content-Type: application/json') );
		$res = json_decode(curl_exec($ch),true);
		curl_close($ch);
		if($res['result']!=1) {
			print_r($res);
			return false;
		}
		return true;
	}
	function send_msg($chat_id,$msg) {
		try {
			$this->bot->sendMessage($chat_id, $msg);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			// $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_file($chat_id,$path) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			$this->bot->sendDocument($chat_id, $file);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_photo($chat_id,$path,$caption=null) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			$this->bot->sendPhoto($chat_id,$file,$caption);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_video($chat_id,$path,$caption=null) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			$this->bot->sendVideo($chat_id, $file, null,$caption);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_audio($chat_id,$path) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			$this->bot->sendAudio($chat_id, $file);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_voice($chat_id,$path) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			$this->bot->sendVoice($chat_id, $file);
			return true;
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function send_video_note($chat_id,$path) {
		if(file_exists($path))
			$file = new \CURLFile($path); else $file=$path;
		try {
			return $this->bot->sendVideoNote($chat_id, $file);
		} catch (\TelegramBot\Api\Exception $e) {
			//print $e->getMessage()."<br>\n";
			return false;
		}
	}
	function get_updates() {
		return $this->bot->getUpdates($offset = 0, $limit = 100, $timeout = 0);
	}
}

?>
