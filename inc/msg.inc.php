<?
$t=new top($database,0,false, $favicon);

$m=new msg;
$m->gid=$t->gid;
$m->allow_change_acc=($t->userdata['access_level']<3)?true:false; 
$m->userdata=$t->userdata;
$m->connect($database);
$m->msg_add_to_friends=$msg_add_to_friends;
$m->save_images=$save_images;
$m->send_talk_to_email=$send_talk_to_email;
$m->send_talk_to_email_from=$send_talk_to_email_from;
$m->send_talk_to_vk=$send_talk_to_vk;
if(!isset($request_to_friends_as_default))
	$request_to_friends_as_default=true;
$m->request_to_friends_as_default=$request_to_friends_as_default;
$m->run();
$t->bottom();

?>
