<?
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
chdir("../d/1000/");
include "init.inc.php";
exit;
class test extends vkt_send {
	function vkt_send_task_0ctrl_($vkt_send_id,$ctrl_id,$uid_if_mode3=0) {
		$this->vkt_send_tg_bot=$this->dlookup("tg_bot_msg","0ctrl","id='$ctrl_id'");
		//$uid_if_mode3=$this->dlookup("uid","0ctrl_vkt_send_tasks","vkt_send_id='$vkt_send_id' AND ctrl_id='$ctrl_id'");
		$database=$this->get_ctrl_database($ctrl_id);
		$this->connect($database);
		$this->db200=$this->get_db200($this->get_ctrl_dir($ctrl_id));

		$r=$this->fetch_assoc($this->query("SELECT * FROM vkt_send_1 WHERE id='$vkt_send_id'"));
		$vkt_send_tm=$r['vkt_send_tm']; //$this->dlookup("vkt_send_tm","vkt_send_1","id='$vkt_send_id'",0);
		$sid=$r['sid']; //$this->dlookup("sid","vkt_send_1","id='$vkt_send_id'");
		$dt=date('d.m.Y H:i',$vkt_send_tm);
		print "vkt_send_now started. vkt_send_tm=$dt \n";

		if(!$sid) {
			if(!$vkt_send_tm || $vkt_send_tm>time()) {
				$this->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3);
				return false;
			}
		}

		$email_template=$r['email_template'];
		$email_from=$r['email_from'];
		$email_from_name=$r['email_from_name'];
		$uni=false;
		if(!empty($email_template)) {
			$this->connect('vkt');
			$api_key=$this->dlookup("unisender_secret","0ctrl","id='$ctrl_id'");
			$this->connect($database);
			if(!empty($api_key)) {
				include_once "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
				$uni=new unisender($api_key,$email_from,$email_from_name);
				include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
			}
		}

		$this->vk_token=$this->get_vk_token();

		$this->vkt_send_vk_photo=(!empty($r['vk_attach']))?$r['vk_attach']:false;
		$this->vkt_send_tg_photo=(!empty($r['tg_image']))?$r['tg_image']:false;
		$this->vkt_send_tg_video_note=(!empty($r['tg_video_note']))?$r['tg_video_note']:false;
		$this->vkt_send_tg_video=(!empty($r['tg_video']))?$r['tg_video']:false;
		$this->vkt_send_tg_audio=(!empty($r['tg_audio']))?$r['tg_audio']:false;
		$msg=$r['msg'];

		if(!$sid)
			$res_arr=$this->vkt_send_filter($vkt_send_id);
		else
			$res_arr=[$uid_if_mode3];


		$this->print_r($res_arr);
		exit;

		$tm1=time()-(1*60*60); //time prevent repeated actions!!!!
	//$tm1=time()-(1*60);
		foreach($res_arr AS $uid) {
			if($this->dlookup("id","vkt_send_log1","vkt_send_id='$vkt_send_id' AND uid='$uid' AND tm>'$tm1'")) {
				print "uid=$uid already in log, passed \n";
				$this->query("INSERT INTO vkt_send_log1 SET
					vkt_send_id='$vkt_send_id',
					uid='$uid',
					tm='".time()."',
					res_vk='10',
					res_tg='10',
					res_wa='10',
					res_email='10'
					");
				continue;
			}
	//$uid=-1001;
			//$this->vkt_send_msg($uid,$this->vkt_send_prepare_msg($uid,$msg));
	//$this->print_r($this->vkt_send_res);
			print "uid=$uid proceed \n";
	
			$this->query("INSERT INTO vkt_send_log1 SET
				vkt_send_id='$vkt_send_id',
				uid='$uid',
				tm='".time()."',
				res_vk='{$this->vkt_send_res['vk']}',
				res_tg='{$this->vkt_send_res['tg']}',
				res_wa='{$this->vkt_send_res['wa']}',
				res_email='$res_email'
				");
	//break;
		}
		if(!$sid)
			$this->query("UPDATE vkt_send_1 SET del=1 WHERE id='$vkt_send_id'");

		$this->vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3);
		print "vkt_send_task_del($vkt_send_id,$ctrl_id,$uid_if_mode3) \n";
		print "ok\n";
		return true;
	}
}

$vkt_send=new test($database);
$vkt_send->vkt_send_task_0ctrl($vkt_send_id=71,1,0);

?>
