<?
if(preg_match("/[0-9\+\-\ \(\)\.]+/",$msg,$m)) {
	if($mob=$db->check_mob($m[0])) {
		//~ if(empty($db->dlookup("mob_search","cards","uid='$uid'"))) {
			//~ $db->query("UPDATE cards SET mob='$mob',mob_search='$mob' WHERE uid='$uid'");
			//~ $db->notify($uid,'MOBILE DETECTED: '.$mob);
			//~ if($uid2=$db->dlookup("uid","cards","del=0 AND uid!='$uid' AND mob_search='$mob'")) {
				//~ $db->merge_cards($uid,$uid2);
			//~ }
		//~ }
	}
}
?>
