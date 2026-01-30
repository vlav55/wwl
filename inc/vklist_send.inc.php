<?
$s=new vklist_send;
$s->connect($database);
$s->uid_julia=$DO_NOT_TOUCH_FRIENDS;
$s->hour_of_start_sending=$hour_of_start_sending;
$s->hour_of_end_sending=$hour_of_end_sending;
$s->interval_min=$interval_min; //interval_in_hours_of_next_trying_after_errors
$s->sex_allowed=$sex_allowed; //false - any sex allowed; 1-female only; 2-male only; 0- not specified
$s->min_age_limit=$min_age_limit;
$s->max_age_limit=$max_age_limit;
$s->allow_if_in_cards=$allow_if_in_cards;
/*$uid=198746774;
print $s->check_if_friend($uid)."\n";
print $s->check_in_cards_and_not_D($uid)."\n";
print $s->check_if_in_stopwords_list($uid)."\n";
print $s->send($uid,"С‚РµСЃС‚", $acc_id=3,$gid=0,$group_name="", $n=0)."\n";
*/

$s->run();


?>
