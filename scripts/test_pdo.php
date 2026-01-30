<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";

$db = new db();
$db->debug=1;
$msg="ЗАКАЗ: Создан SKU=B144 N=2750 Волчок Zwei Longinus Drake Spiral\" Metsu B-144 от \" Takara Tomy sum=1781 ";
$db->query("INSERT INTO msgs SET uid='0', acc_id=0, mid=0, tm=1769348808, user_id='0', msg='".$db->escape($msg)."', outg=2, imp=12, vote='0', source_id='0', custom='0'");
print "<br>OK"; 
?>
