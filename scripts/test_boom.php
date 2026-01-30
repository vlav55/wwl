<?php
include "/var/www/vlav/data/www/wwl/inc/wa_boom.class.php";

// Использование класса
$wa = new wa_boom('6911b56d77fc3','79111936781');

try {
   print_r($wa->send_msg(79119841012,"test"));
  // print_r($wa->send_media($instance_id, 79119841012, "test", "https://winwinland.ru/refs/bblslegrushka.png"));
	//print_r($wa->set_webhook($instance_id, "https://webhook.site/00c599b7-b50c-442b-a851-1d3ddc3edd5c"));
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
