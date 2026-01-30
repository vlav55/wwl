<?

include "/var/www/vlav/data/www/wwl/inc/unisender.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt.class.php";
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "/var/www/vlav/data/www/wwl/inc/tg_bot.class.php";
//chdir("../d/3994858278/");
chdir("../d/1000/");
include "init.inc.php";

$db=new db('vkt');

$file = '/var/www/vlav/data/www/wwl/scripts/code/winwinland_chat/users.csv';

// Read the file
$handle = fopen($file, 'r');
if (!$handle) {
    die('Unable to open file');
}

// Parse the CSV data
$users = [];
while (($data = fgetcsv($handle)) !== false) {
    $user = [
        'User_id' => $data[0],
        'Username' => $data[1],
        'Имя' => $data[2],
        'Пол' => $data[3],
        'Телефон' => $data[4],
        'Последняя активность (UTC)' => $data[5]
    ];
    $users[] = $user;
}

// Close the file
fclose($handle);

// Display the parsed data
$n=0;
foreach ($users as $user) {
    //~ echo "User ID: " . $user['User_id'] . "<br>";
    //~ echo "Username: " . $user['Username'] . "<br>";
    //~ echo "Имя: " . $user['Имя'] . "<br>";
    //~ echo "Пол: " . $user['Пол'] . "<br>";
    //~ echo "Телефон: " . $user['Телефон'] . "<br>";
    //~ echo "Последняя активность (UTC): " . $user['Последняя активность (UTC)'] . "<br>";
    //~ echo "<br>";

    if(!$db->dlookup("id","tg_public_yoga","tg_id='{$user['User_id']}'")) {
		$db->query("INSERT INTO tg_public_yoga SET
				tm='".time()."',
				tg_id='{$user['User_id']}',
				tg_nic='".$db->escape($user['Username'])."',
				f_name='".$db->escape($user['Имя'])."',
				res=1
				");
		$n++;
	}
}
print "Saved in tg_public_yoga OK cnt=$n<br>";

$res=$db->query("SELECT * FROM cards WHERE del=0 AND telegram_id>0");
$n=0;
while($r=$db->fetch_assoc($res)) {
	if(!$db->dlookup("id","tg_public_yoga","tg_id='{$r['telegram_id']}'")) {
		//print "{$r['uid']} {$r['telegram_id']}<br>";
		$n++;
		$db->query("UPDATE cards SET fl=1 WHERE uid='{$r['uid']}'");
	}
}

print "Checked in cards all that not in chat Ok cnt=$n <br>";
exit;



exit;
