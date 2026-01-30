<?
include_once('/var/www/vlav/data/www/wwl/inc/parser/simple_html_dom.php');
include_once "/var/www/vlav/data/www/wwl/inc/vklist_api.class.php";
class vklist_parser {
	function get_uids_from_file($fname) {
		$html = file_get_html($fname);
		$vk=new vklist_api;
		if(!$html) {
			return false;
		}
		$res=array();
		foreach($html->find("div[class='labeled name'] a") as $e) {
			if(preg_match("#http(s)?://vk.com/(.*)#",$e->href,$res))
				$uid=$res[2];
			if(preg_match("#^id([0-9]*)$#",$uid,$res))
				$uid=$res[1];
			if(!is_numeric($uid))
				if(!$uid=$vk->vk_get_uid_by_domain($uid))
					continue;
			$arr[]=$uid;
		}
		$html->clear();
		return $arr;
	}
}
?>