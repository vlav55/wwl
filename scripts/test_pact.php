<?
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "/var/www/vlav/data/www/wwl/inc/pact.class.php";
$db=new db('vkt1_230');
$wa_api_key_pact="36626d54607b9d9e59d09c62ff95943d3eac0038a2d7b539f3b6b0a66bb9205ebb194223511695add7a48916f28b7c773cf9958e065a6e0f312a4574418c7baa";
			$p=new pact($wa_api_key_pact);
			//~ $cid=$p->get_cid_by_phone('79119841012');
			//~ print "HERE_$cid";
			//$p->attach=[$p->upload_attachment("logo.png",$cid)];
			//print_r( $p->send_msg($cid,"test"));
			$p->attach_file_name="logo.png";
			if($p->send($db,$uid=-1500,$msg="test",0,0,0,false,true))
				print "TRUE"; else print "FALSE";
print "OK";
?>
