<?
session_start();
//~ unset($_SESSION['vk_uid']);
//~ print_r($_COOKIE);
print "Session=".$_SESSION['vk_uid']."<br>";
print("C=".$_COOKIE['saved_uid']);
?>
