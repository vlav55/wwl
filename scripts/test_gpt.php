<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
$db=new db('vkt');
$ctrl_id=1;
$vsegpt_secret=$db->dlookup("vsegpt_secret","0ctrl","id=$ctrl_id");
$vsegpt_model=$db->dlookup("vsegpt_model","0ctrl","id=$ctrl_id");
$messages="";
$arr[]=['role' => 'system', 'content' => "You are a large language model.
Carefully heed the user's instructions.
Respond without Markdown."];
		//~ while($r=$this->fetch_assoc($res)) {
			//~ $role=($r['outg']==0)?"user":"assistant";
			//~ $arr[]=['role' => $role, 'content' => $r['msg']];
		//~ }
chdir("../d/1000");
$p=file_get_contents("calls/prompt.txt");
$p.="\n".file_get_contents("calls/161960870_78002223211_13-05-2025_13-51.txt");
//print $p; exit;

$arr[]=['role' => 'user', 'content' => $p];

$res=$db->vsegpt($vsegpt_secret,$arr,$model='openai/gpt-4o-mini');
print $res;
?>
