<?

$customer_id=intval(file_get_contents("./customer.id"));
if(!$customer_id) {
	print "Error getting of customer_id. File ./customer.id is not found or wrong content!"; exit;
}
$db=new db("vktrade");
$r=$db->fetch_assoc($db->query("SELECT * FROM customers WHERE id='$customer_id'"));
$token=$r['ad_token'];
$cab_id=$r['ad_cabinet'];
$target_group_id=$r['ad_target_group'];
$razdel_arr=explode(",",$r['ad_razdel']);
$database=$r['db'];
$tm_from=$r['ad_dt_from'];



$db->connect($database);

$vk=new vklist_api($token);

print "database=$database\n";
print "cab_id=$cab_id target_group_id=$target_group_id\n";
print "dt_from=".date("d.m.Y",$tm_from)."\n";
print "razdel_arr=\n";
print_r($razdel_arr);


$uids="";
$n=0; $cnt=0; $added=0;
foreach($razdel_arr AS $razdel) {
	if(!is_numeric($razdel))
		continue;
	$res=$db->query("SELECT msgs.uid AS uid FROM cards
					JOIN msgs ON cards.uid=msgs.uid
					WHERE cards.del=0 AND razdel='$razdel' AND msgs.tm>'$tm_from'
					GROUP BY msgs.uid
					",0);
	print "razdel=$razdel num=".$db->num_rows($res)."\n";
	while($r=$db->fetch_assoc($res)) {
		if(!intval($r['uid']))
			continue;
		$uids.=intval($r['uid']).",";
		$n++;
		if($n==499) {
			$cnt+=$n;
			$n=0;
			$vk_res=$vk->ad_add_target_contacts($cab_id,$target_group_id,$uids);
			print_r($vk_res); print "\n";
			$added+=$vk_res['response'];
			print "STEP : ad_add_target_contacts: cnt_uids=$cnt RES added in this cycle={$vk_res['response']} all=($added)\n";
			$uids="";
			sleep(1);
		}
	}
}
$cnt+=$n;
$n=0;
$vk_res=$vk->ad_add_target_contacts($cab_id,$target_group_id,$uids);
print_r($vk_res);
$added+=$vk_res['response'];
print "FINAL : ad_add_target_contacts: cnt_uids=$cnt RES added in this cycle={$vk_res['response']} all=($added)\n";



?>
