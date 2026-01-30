<?
http_response_code(200);
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rawInput = file_get_contents('php://input');
    file_put_contents("calls/post_raw.txt", $rawInput);
    $arr=json_decode($rawInput,true);
    file_put_contents("calls/post.txt", print_r($arr, true));
    if(isset($arr['call_session_recognized_text_array'])) {
		//$db->notify_me("call_session_recognized_text_array detected duration={$arr['talk_time_duration']}");
		$f="calls/{$arr['call_session_id']}_{$arr['extension_phone_number']}_{$arr['contact_phone_number']}_".date("d-m-Y_H-i");
		$man_number=intval(trim($arr['extension_phone_number']));
		$call_session_id=intval(trim($arr['call_session_id']));
		$fname=$f.".txt";
		$fname_gpt=$f."_gpt.txt";
		$fname_json=$f.".json";
		$out=""; $j=[];
		foreach($arr['call_session_recognized_text_array'] AS $r) {
			$who=$r['is_operator'] ? ": Менеджер ({$arr['extension_phone_number']}) :" : ": Клиент ({$arr['contact_phone_number']}):";
			$out.= "$who {$r['start_time']}\n";
			$out.= $r['phrase']."\n";
		}
		file_put_contents($fname,$out);
		file_put_contents($fname_json,json_encode($arr['call_session_recognized_text_array']));


		//$prompt=file_get_contents("calls/prompt.txt")."\n$out";
	//$db->notify_me("man_number=$man_number {$arr['extension_phone_number']}");
	if(1==2) {
		$prompt=$db->dlookup("prompt","users_pbx","man_number='$man_number'")."\n$out";
		$vsegpt_model=$db->dlookup("vsegpt_model","users_pbx","man_number='$man_number'");
		$p[]=['role' => 'system', 'content' => "You are a large language model.
		Carefully heed the user's instructions.
		Respond without Markdown."];
		$p[]=['role' => 'user', 'content' => $prompt];
		$res=$db->vsegpt($vsegpt_secret,$p,$vsegpt_model='openai/gpt-4o-mini');
		file_put_contents($fname_gpt,$res);
		preg_match('/Итог=(\d+)/u', $res, $m);
		$val=isset($m[1]) ? intval($m[1]) : 0;
		$db->query("UPDATE novofon_log SET val='$val' WHERE call_session_id='$call_session_id'");
	}

		$db->query("UPDATE novofon_log SET transcribe='".$db->escape($fname)."',gpt='".$db->escape($fname_gpt)."' WHERE call_session_id='$call_session_id'");
		$msg="Поступила транскрибация звонка
{$arr['extension_phone_number']} -> {$arr['contact_phone_number']} ({$arr['talk_time_duration']} сек)
Текст https://for16.ru/d/1000/calls/?fname=$fname
Оценка ИИ https://for16.ru/d/1000/calls/?fname=$fname_gpt
";
		if($uid=$db->dlookup("uid","cards","del=0 AND mob_search='".$db->check_mob($arr['contact_phone_number'])."'")) {
			$db->save_comm($uid,0,$msg,166,$call_session_id);
		}
		if($arr['extension_phone_number']!='5648') 
				$db->notify_chat(-4799845674,$msg); else $db->notify_me($msg);
	}
}
if(isset($_GET['recorded'])) {
	//$db->notify_me("recorded \n".print_r($_GET,true));
	sleep(1);
	$call_session_id=intval($_GET['call_session_id']);
	$db->query("UPDATE novofon_log SET record='".$db->escape($_GET['file_link'])."' WHERE call_session_id='$call_session_id'");
	if($_GET['file_duration']>60) {
		$u=[
			'tm'=>0, //for new uid - tm=time() if 0
			'man_id'=>254,
			'first_name'=>$_GET['contact_phone_number'],
			'phone'=>$_GET['contact_phone_number'],
		];
		$uid=$db->cards_add($u,$update_if_exist=false);
		$db->save_comm($uid,0,"Novofon\n".print_r($_GET,true),165,$call_session_id);
		$db->notify_chat(-4799845674,"Звонок добавлен в CRM $DB200/msg.php?uid=$uid");
	}
}
if(isset($_GET['call_ended'])) {
	//$db->notify_me(print_r($_GET,true));
	$call_session_id=intval($_GET['call_session_id']);
	$db->query("INSERT INTO novofon_log SET
				tm='".time()."',
				client_number=".intval($_GET['contact_phone_number']).",
				man_number=".intval($_GET['extension_phone_number']).",
				talk_time_duration=".intval($_GET['talk_time_duration']).",
				total_time_duration=".intval($_GET['total_time_duration']).",
				wait_time_duration=".intval($_GET['wait_time_duration']).",
				call_session_id=".intval($_GET['call_session_id'])."
				");
}
print "ok";
?>
 
