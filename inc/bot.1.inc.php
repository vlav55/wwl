<?
	if(1) { //$vkts->database == 'vkt1_101'
		$gpt_off_period=24*60*60;
		$msg_gpt=false;
		if($tm_last_gpt=$vkts->dlookup("fl_gpt","cards","del=0 AND uid='$uid'")) {
			if($tm_last_gpt > (time()- $gpt_off_period) ) {
				global $vsegpt_secret,$vsegpt_model,$vsegpt_delay_sec;
				//$fl_notify=false;
				$api_key = $vsegpt_secret;
				$arr=$vkts->gpt_get_messages($uid,$limit=50);
				$arr[]=['role' => 'user', 'content' => $msg];
				//$vkts->notify_me("from bot.1.inc.php:\n".print_r($arr,true));
				if($msg_gpt= $vkts->vsegpt($api_key,$arr,$vsegpt_model)) {
					sleep($vsegpt_delay_sec);
					$vkts->vkt_send_msg_user_id=-1; //AI
					$vkts->vkt_send_msg($uid,$vkts->prepare_msg($uid,$msg_gpt),0,false,true);
				} else {
					$vkts->notify_me("VSEGPT ERROR: ".$vkts->vsegpt_err); 
					$fl_notify=true;
				}
			} else 
				$vkts->query("UPDATE cards SET fl_gpt=0 WHERE uid='$uid'");
		}
	}
?>
