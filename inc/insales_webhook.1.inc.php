<?
http_response_code(200);
include "/var/www/vlav/data/www/wwl/inc/vkt_send.class.php";
include "init.inc.php";
$db=new vkt_send($database);

$rawData = file_get_contents('php://input');
if($r=json_decode($rawData,true)) {
	$out="";
	
//$db->vkt_email("HERE_",("<pre>".print_r($r,true)."</pre>"));
	file_put_contents("insales_webhook.log", "\n\n---".date("d.m.Y H:i:s")." $insales_status ---\n".print_r($r,true), FILE_APPEND);
	if (!$db->table_exists('webhook_log')) {
		$db->connect($database,true);
		$db->query("CREATE TABLE `webhook_log` (
			  `id` int NOT NULL AUTO_INCREMENT,
			  `tm` int NOT NULL,
			  `log_name` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
			  `hook` text COLLATE utf8mb4_general_ci NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `tm` (`tm`),
			  KEY `log_name` (`log_name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
		$db->connect($database,false);
	}
	$sql = "INSERT INTO webhook_log SET tm = :tm, log_name = :log_name, hook = :hook";
	$params = [
		':tm' => time(),
		':log_name' => 'insales',
		':hook' => $rawData  // Your PDO class will handle escaping
	];
	$db->query($sql, $params);

	$paid=false;
	foreach($r['order_changes'] AS $item) {
		$out=print_r($item['value_is'],true);
		if($item['value_is']==$insales_status) 
			$paid=true;
	}
	//~ if(!$paid)
		//~ exit;
		
	$number=trim($r['number']);

	parse_str(parse_url($r['first_current_location'],PHP_URL_QUERY),$m);
	$bc=false;
	foreach($m AS $key=>$val) {
		if(trim($key)=='bc')
			$bc=intval($val);
		if(trim($key)=='utm_source')
			$utm_source=intval($val);
		if(trim($key)=='utm_medium')
			$utm_medium=intval($val);
		if(trim($key)=='utm_campaign')
			$utm_campaign=intval($val);
		if(trim($key)=='utm_term')
			$utm_term=intval($val);
		if(trim($key)=='utm_content')
			$utm_content=intval($val);
		if(trim($key)=='utm_ab')
			$utm_ab=intval($val);
	}

	$client_id=$r['client']['id'];
	$name=$r['client']['name'];
	$mob=$db->check_mob($r['client']['phone']);
	$mob= $mob ? $mob : "";
	$email=$db->validate_email($r['client']['email']) ? $r['client']['email'] : "";

	$klid=0; $user_id=0; $uid=0;
	if(!empty($mob) && !$uid) {
		$uid=$db->dlookup("uid","cards","mob_search='$mob' AND del=0");
	}
	if(!empty($email) && !$uid) {
		$uid=$db->dlookup("uid","cards","email='$email' AND del=0");
	}
	if($uid) {
		$user_id=$db->dlookup("user_id","cards","uid='$uid'");
		$klid=$db->get_klid($user_id);
	}
	if($bc) {
		if($klid=$db->get_klid_by_bc($bc)) {
			$user_id=$db->get_user_id($klid);
		}
	}
	$fee_1=0; $fee_2=0;
	foreach($r['discounts'] AS $d) {
		if(preg_match('/\S+$/', $d['description'], $m))
			$promocode=$m[0]; else $promocode=false;
	}

	if(!$user_id) {
	//	$db->notify_me("INSALES passed \n url={$r['first_current_location']}\n user_id=$user_id klid=$klid): order_id={$r['id']} n=$number {$r['total_price']} $name $mob $email $bc\n".print_r($m,true));
		print "ok";
		exit;
	}

	$card=[
		'first_name'=>$name,
		'phone'=>$mob,
		'email'=>$email,
		'user_id'=>$user_id,
		'klid'=>$klid,
	];
	$uid=$db->cards_add($card,$update_if_exist=false);
	if(!$db->dlookup("id","cards2other","uid='$uid' AND tool='insales' AND tool_uid='$client_id'"))
		$db->query("INSERT INTO cards2other SET uid='$uid',tool='insales',tool_uid='$client_id'");

	$comm="ЗАКАЗ:\n";
	$sum_all=0;
	$accept=false;
	foreach($r['order_lines'] AS $item) {
		$order_number=$item['order_id'];
		$sum=intval($item['full_total_price']);
		$sku=trim($item['sku']);
		$insales_product_id=intval($item['product_id']);
		if(empty(trim($sku))) {
			$sku="-";
			$pid=1;
		} else {
			if(!$pid=$db->dlookup("id","product","sku='$sku'")) {
				$pid=1;
				$res_sku=$db->query("SELECT * FROM product WHERE sku LIKE '%$sku%'");
				while($r_sku=$db->fetch_assoc($res_sku)) {
					$arr=preg_split('/[\s,]+/', $r_sku['sku']);
					foreach($arr AS $pat) {
						if(trim($pat)==$sku) {
							$pid=$r_sku['id'];
							break;
						}
					}
					if($pid!=1)
						break;
				}
			}
		}
		if(!$avangard_id=$db->dlookup("id","avangard","vk_uid=$uid AND order_number='$order_number' AND order_id='$insales_product_id'")) {
			$avangard_res=$paid ? 1 : 0;
			$fee_1=0; $fee_2=0;
			if($promocode) {
				$tm=time();
				if($r_p=$db->fetch_assoc($db->query("SELECT * FROM promocodes WHERE
							promocode LIKE '$promocode'
							AND product_id='$pid'
							AND cnt!=0
							AND (tm1<$tm ANd tm2>$tm)"))) {
					$klid=$db->get_klid_by_uid($r_p['uid']);
					$user_id=$db->get_user_id($klid);
					$fee_1=$r_p['fee_1'];
					$fee_2=$r_p['fee_2'];
				}
			}
			$db->query("INSERT INTO avangard SET
				tm='".time()."',
				pay_system='insales',
				sku='".$db->escape($sku)."',
				product_id='$pid',
				order_id='$insales_product_id',
				order_number='".$db->escape($order_number)."',
				order_descr='".$db->escape($item['title'])."',
				amount='$sum',
				amount1='$sum',
				c_name='".$db->escape($name)."',
				phone='$mob',
				email='".$db->escape($email)."',
				vk_uid='$uid',
				fee_1='$fee_1',
				fee_2='$fee_2',
				res=$avangard_res
			");
			$status="Создан";
			$accept=true;
		} elseif($paid && !$db->dlookup("id","avangard","id=$avangard_id AND res=1")) {
			$db->query("UPDATE avangard SET res=1 WHERE id=$avangard_id");
			$status=$insales_status;
			$accept=true;
		}
		if($accept) {
			$comm.="$status SKU=$sku N=$number {$item['title']} sum=$sum \n";
			$sum_all += $sum;
		}
	}

	if(!$accept) {
		print "ok";
		exit;
	}
	
	if( $sum_all >=0 ) {
		$db->save_comm($uid,0,$comm);

		if($klid) {
			if($db->dlookup("id","vkt_send_1","del=0 AND id=1")) {
				$uid_p=$db->dlookup("uid","cards","del=0 AND id='$klid'");;
				$db->vkt_send_task_add($ctrl_id,
					$tm_event=intval(time()+($insales_delay_fee*24*60*60)),
					$vkt_send_id=1,
					$vkt_send_type=3,
					$uid_p);
			}
			//$db->notify_me("$ctrl_id $insales_delay_fee $uid");
			/*
			if($insales_bonuses) {
				include "/var/www/vlav/data/www/wwl/scripts/insales/insales_app_credentials.inc.php";
				include "/var/www/vlav/data/www/wwl/scripts/insales/insales_func.inc.php";
				$token=$insales_token;
				$shop=$insales_shop;
				$passw=md5($token.$secret_key);
				$credentials = base64_encode("$id_app:$passw");
				//print "=$token =$shop =$insales_id =$secret_key =$id_app<br>";
			}
			include_once "/var/www/vlav/data/www/wwl/inc/partnerka.class.php";
			$p=new partnerka($klid,$database);
			$last_op_id=$db->dlast("id","partnerka_op","uid='$uid'");
			if(!$last_op_id)
				$last_op_id=0;
			$db->notify_me("INSALES last_op_id=$last_op_id");
			$p->fill_op($klid,$db->dt1(time()),$db->dt2(time()), $ctrl_id);
			$db->notify_me("INSALES fill_op done");
			$res_op=$db->query("SELECT * FROM partnerka_op WHERE uid='$uid' AND id>$last_op_id");
			while($r_op=$db->fetch_assoc($res_op)) {
				$partner_uid=$db->dlookup("uid","cards","id=".$r['klid_up']);
				$insales_partner_id=$db->dlookup("tool_uid","cards2other","tool='insales' AND uid='$partner_uid'");
				$db->notify_me("INSALES insales_partner_id=$insales_partner_id");
				$res=insales_bonus_create($insales_partner_id, $r_op['fee_sum'], $descr='Бонус WinWinLand');
				if(isset($res['error']))
					$db->notify_me("INSALES insales_bonus_create ERROR\n".print_r($res,true));
			}
			*/
		}
		$card_hold_tm=time()+($hold*24*60*60);
		if(!$db->hold_chk($uid) && $user_id) {
			$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid',card_hold_tm='$card_hold_tm' WHERE uid='$uid'");
		}
		if(!$keep && $user_id) {
			$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid',card_hold_tm='$card_hold_tm' WHERE uid='$uid'");
		}

		if(!empty($utm_campaign) ||
		!empty($utm_content) ||
		!empty($utm_medium) ||
		!empty($utm_source) ||
		!empty($utm_term) ||
		!empty($utm_ab) ) {
		//~ if(isset($land_num)) //if utm set land_num=0 that cause wrong par pass to telegram bot, I don't know why
		//~ $land_num=0;
		$db->query("INSERT INTO utm SET
			uid='$uid',
			tm='".time()."',
			utm_campaign='".$db->escape($utm_campaign)."',
			utm_content='".$db->escape($utm_content)."',
			utm_medium='".$db->escape($utm_medium)."',
			utm_source='".$db->escape($utm_source)."',
			utm_term='".$db->escape($utm_term)."',
			utm_ab='".$db->escape($utm_ab)."',
			pwd_id='0',
			promo_code='0',
			mob='$mob' ");
		}


		//$sum_all=$r['items_price'];
		file_put_contents("insales_webhook.log",print_r($r,true) );
		//$db->notify_me("order_id={$r['id']} $number $sum_all $name $mob $email $bc\n".print_r($m,true));
		$order_status=$paid ? $insales_status : "Создан";
		$notify_key=$paid ? "pay" : "order";
		$db->notify($uid,"INSALES: Заказ №$number на сумму: $sum_all $order_status", $notify_key);
	}
}
print "ok";
exit;
?>
Array
(
    [fields_values] => Array
        (
            [0] => Array
                (
                    [id] => 230245588
                    [field_id] => 34866889
                    [value] => CONFIRMED
                    [created_at] => 2025-01-15T15:14:07.370+03:00
                    [updated_at] => 2025-01-15T15:14:07.370+03:00
                    [type] => Текст
                    [name] => Статус возврата Тинькофф
                    [handle] => TinkoffBankRefundId
                )

            [1] => Array
                (
                    [id] => 230245543
                    [field_id] => 34773060
                    [value] => 19.01.2025
                    [created_at] => 2025-01-15T15:13:19.580+03:00
                    [updated_at] => 2025-01-15T15:13:19.580+03:00
                    [type] => Дата
                    [name] => Дата
                    [handle] => date
                )

            [2] => Array
                (
                    [id] => 230245544
                    [field_id] => 34773128
                    [value] => г. Казань, ул. Сибирский тракт, 23 (KDL)
                    [created_at] => 2025-01-15T15:13:19.583+03:00
                    [updated_at] => 2025-01-15T15:13:19.583+03:00
                    [type] => Выпадающий список
                    [name] => Адрес медофиса
                    [handle] => medofis_name
                )

            [3] => Array
                (
                    [id] => 230245545
                    [field_id] => 34972278
                    [value] => -
                    [created_at] => 2025-01-15T15:13:19.586+03:00
                    [updated_at] => 2025-01-15T15:13:19.586+03:00
                    [type] => Дата
                    [name] => Дата рождения (другого члена семьи)
                    [handle] => family_member_dob
                )

            [4] => Array
                (
                    [id] => 230245555
                    [field_id] => 34866888
                    [value] => 5690512326
                    [created_at] => 2025-01-15T15:13:24.356+03:00
                    [updated_at] => 2025-01-15T15:13:24.356+03:00
                    [type] => Текст
                    [name] => Идентификатор транзакции Тинькофф
                    [handle] => TinkoffBankTransactionId
                )

        )

    [order_lines] => Array
        (
            [0] => Array
                (
                    [id] => 701618051
                    [order_id] => 127553532
                    [sale_price] => 769
                    [full_sale_price] => 0.75
                    [total_price] => 769
                    [full_total_price] => 0.75
                    [discounts_amount] => 768.25
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 761390814
                    [product_id] => 457989435
                    [sku] => 
                    [barcode] => 
                    [title] => 3 гормона щитовидной железы
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:14:41.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [1] => Array
                (
                    [id] => 701618055
                    [order_id] => 127553532
                    [sale_price] => 1249
                    [full_sale_price] => 1.25
                    [total_price] => 1249
                    [full_total_price] => 1.25
                    [discounts_amount] => 1247.75
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 772798546
                    [product_id] => 464515780
                    [sku] => 
                    [barcode] => 
                    [title] => 5 гормонов щитовидной железы
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [2] => Array
                (
                    [id] => 701618050
                    [order_id] => 127553532
                    [sale_price] => 1099
                    [full_sale_price] => 1.1
                    [total_price] => 1099
                    [full_total_price] => 1.1
                    [discounts_amount] => 1097.9
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762987082
                    [product_id] => 459076639
                    [sku] => 
                    [barcode] => 
                    [title] => Биохимия, базовый. 9 анализов
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [3] => Array
                (
                    [id] => 701618057
                    [order_id] => 127553532
                    [sale_price] => 979
                    [full_sale_price] => 0.98
                    [total_price] => 979
                    [full_total_price] => 0.98
                    [discounts_amount] => 978.02
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526420
                    [product_id] => 452006702
                    [sku] => 
                    [barcode] => 
                    [title] => Витамин D
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 4
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745759392
                )

            [4] => Array
                (
                    [id] => 701618047
                    [order_id] => 127553532
                    [sale_price] => 969
                    [full_sale_price] => 0.97
                    [total_price] => 969
                    [full_total_price] => 0.97
                    [discounts_amount] => 968.03
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762988315
                    [product_id] => 459077185
                    [sku] => 
                    [barcode] => 
                    [title] => Витамины группы B
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [5] => Array
                (
                    [id] => 701618046
                    [order_id] => 127553532
                    [sale_price] => 969
                    [full_sale_price] => 0.97
                    [total_price] => 969
                    [full_total_price] => 0.97
                    [discounts_amount] => 968.03
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762985370
                    [product_id] => 459075434
                    [sku] => 
                    [barcode] => 
                    [title] => Диагностика железодефицита
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [6] => Array
                (
                    [id] => 701618052
                    [order_id] => 127553532
                    [sale_price] => 499
                    [full_sale_price] => 0.5
                    [total_price] => 499
                    [full_total_price] => 0.5
                    [discounts_amount] => 498.5
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762990379
                    [product_id] => 459078463
                    [sku] => 
                    [barcode] => 
                    [title] => Здоровое сердце и сосуды
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [7] => Array
                (
                    [id] => 701618048
                    [order_id] => 127553532
                    [sale_price] => 779
                    [full_sale_price] => 0.78
                    [total_price] => 779
                    [full_total_price] => 0.78
                    [discounts_amount] => 778.22
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762989534
                    [product_id] => 459077836
                    [sku] => 
                    [barcode] => 
                    [title] => Микроэлементы и электролиты
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [8] => Array
                (
                    [id] => 701618054
                    [order_id] => 127553532
                    [sale_price] => 589
                    [full_sale_price] => 0.59
                    [total_price] => 589
                    [full_total_price] => 0.59
                    [discounts_amount] => 588.41
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 762975885
                    [product_id] => 459069839
                    [sku] => 
                    [barcode] => 
                    [title] => Обследование печени
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [9] => Array
                (
                    [id] => 701618049
                    [order_id] => 127553532
                    [sale_price] => 3999
                    [full_sale_price] => 4
                    [total_price] => 3999
                    [full_total_price] => 4
                    [discounts_amount] => 3995
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 757882480
                    [product_id] => 456598333
                    [sku] => 
                    [barcode] => 
                    [title] => Паразитарные заболевания
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [10] => Array
                (
                    [id] => 701618056
                    [order_id] => 127553532
                    [sale_price] => 299
                    [full_sale_price] => 0.3
                    [total_price] => 299
                    [full_total_price] => 0.3
                    [discounts_amount] => 298.7
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 753988201
                    [product_id] => 454588526
                    [sku] => 100001
                    [barcode] => 
                    [title] => Ферритин
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [11] => Array
                (
                    [id] => 701618043
                    [order_id] => 127553532
                    [sale_price] => 4599
                    [full_sale_price] => 4.6
                    [total_price] => 4599
                    [full_total_price] => 4.6
                    [discounts_amount] => 4594.4
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526412
                    [product_id] => 452006695
                    [sku] => 
                    [barcode] => 
                    [title] => Чекап базовый. Женщины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:14:42.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744561
                )

            [12] => Array
                (
                    [id] => 701618044
                    [order_id] => 127553532
                    [sale_price] => 5099
                    [full_sale_price] => 5.1
                    [total_price] => 5099
                    [full_total_price] => 5.1
                    [discounts_amount] => 5093.9
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526416
                    [product_id] => 452006698
                    [sku] => 
                    [barcode] => 
                    [title] => Чекап базовый. Мужчины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744564
                )

            [13] => Array
                (
                    [id] => 701618045
                    [order_id] => 127553532
                    [sale_price] => 4999
                    [full_sale_price] => 5
                    [total_price] => 4999
                    [full_total_price] => 5
                    [discounts_amount] => 4994
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 757911358
                    [product_id] => 456616420
                    [sku] => 
                    [barcode] => 
                    [title] => Чекап базовый. Подростки (10-18 лет)
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 
                )

            [14] => Array
                (
                    [id] => 701618058
                    [order_id] => 127553532
                    [sale_price] => 6999
                    [full_sale_price] => 7
                    [total_price] => 6999
                    [full_total_price] => 7
                    [discounts_amount] => 6992
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526413
                    [product_id] => 452006696
                    [sku] => 
                    [barcode] => 
                    [title] => Чек-ап расширенный. Женщины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744562
                )

            [15] => Array
                (
                    [id] => 701618061
                    [order_id] => 127553532
                    [sale_price] => 7499
                    [full_sale_price] => 7.5
                    [total_price] => 7499
                    [full_total_price] => 7.5
                    [discounts_amount] => 7491.5
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526417
                    [product_id] => 452006699
                    [sku] => 
                    [barcode] => 
                    [title] => Чек-ап расширенный. Мужчины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744565
                )

            [16] => Array
                (
                    [id] => 701618059
                    [order_id] => 127553532
                    [sale_price] => 8999
                    [full_sale_price] => 9
                    [total_price] => 8999
                    [full_total_price] => 9
                    [discounts_amount] => 8990
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526414
                    [product_id] => 452006697
                    [sku] => 
                    [barcode] => 
                    [title] => Чек-ап экспертный. Женщины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744563
                )

            [17] => Array
                (
                    [id] => 701618062
                    [order_id] => 127553532
                    [sale_price] => 10199
                    [full_sale_price] => 10.2
                    [total_price] => 10199
                    [full_total_price] => 10.2
                    [discounts_amount] => 10188.8
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 749526418
                    [product_id] => 452006700
                    [sku] => 
                    [barcode] => 
                    [title] => Чек-ап экспертный. Мужчины
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [bundle_id] => 
                    [vat] => -1
                    [fiscal_product_type] => 1
                    [requires_marking] => 
                    [marking_codes] => 
                    [accessory_lines] => Array
                        (
                        )

                    [external_variant_id] => 745744568
                )

        )

    [order_changes] => Array
        (
            [0] => Array
                (
                    [id] => 1682106697
                    [created_at] => 2025-01-15T15:14:42.134+03:00
                    [action] => fulfillment_status_changed
                    [value_was] => new
                    [value_is] => delivered
                    [full_description] => Статус заказа изменен с 'Новый' на 'Доставлен' пользователем Павел
                    [user_name] => Павел
                )

            [1] => Array
                (
                    [id] => 1682106696
                    [created_at] => 2025-01-15T15:14:42.124+03:00
                    [action] => custom_status_changed
                    [value_was] => Заказ оформлен и ожидает подтверждения
                    [value_is] => Заказ выполнен
                    [full_description] => Статус заказа изменен с 'Заказ оформлен и ожидает подтверждения' на 'Заказ выполнен' пользователем Павел
                    [user_name] => Павел
                )

            [2] => Array
                (
                    [id] => 1682106044
                    [created_at] => 2025-01-15T15:14:07.097+03:00
                    [action] => financial_status_changed
                    [value_was] => pending
                    [value_is] => paid
                    [full_description] => Статус оплаты изменен с 'Не оплачен' на 'Оплачен' пользователем Тинькофф Банк
                    [user_name] => Тинькофф Банк
                )

            [3] => Array
                (
                    [id] => 1682103866
                    [created_at] => 2025-01-15T15:13:19.981+03:00
                    [action] => order_created
                    [value_was] => 
                    [value_is] => 
                    [full_description] => Заказ создан
                    [user_name] => 
                )

        )

    [discount] => Array
        (
            [id] => 35876932
            [description] => Скидка по купону admin1083
            [type_id] => 1
            [amount] => 60531.41
            [full_amount] => 60531.41
            [percent] => 99.9
            [discount] => 99.9
            [reference_id] => 17390164
            [reference_type] => DiscountCode
            [discount_products_ids] => Array
                (
                )

            [discount_order_lines_ids] => Array
                (
                )

            [discount_code_id] => 17390164
            [created_at] => 2025-01-15T15:13:19.000+03:00
            [updated_at] => 2025-01-15T15:13:19.000+03:00
        )

    [shipping_address] => Array
        (
            [id] => 136359899
            [fields_values] => Array
                (
                )

            [name] => Наталия
            [surname] => Артемьева
            [middlename] => Александровна
            [phone] => 8(937)616-14-70
            [formatted_phone] => 8(937)616-14-70
            [full_name] => Артемьева Наталия Александровна
            [full_locality_name] => Респ Татарстан, Казань
            [full_delivery_address] => г Казань, Респ Татарстан
            [address_for_gis] => г Казань, Респ Татарстан
            [location_valid] => 1
            [recipient_fields] => Array
                (
                    [0] => Array
                        (
                            [id] => 34773152
                            [destiny] => 6
                            [position] => 1
                            [office_title] => Фамилия
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => surname
                            [created_at] => 2024-08-27T11:53:39.503+03:00
                            [updated_at] => 2024-08-27T11:53:50.070+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::SystemTextField
                        )

                    [1] => Array
                        (
                            [id] => 34764099
                            [destiny] => 6
                            [position] => 2
                            [office_title] => Имя
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => contact_name
                            [created_at] => 2024-08-26T18:10:26.303+03:00
                            [updated_at] => 2024-09-06T19:35:26.814+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => Имя
                            [example] => 
                            [type] => Field::ObligatoryTextField
                        )

                    [2] => Array
                        (
                            [id] => 34773153
                            [destiny] => 6
                            [position] => 3
                            [office_title] => Отчество
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => middlename
                            [created_at] => 2024-08-27T11:53:45.357+03:00
                            [updated_at] => 2024-08-27T11:53:52.274+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::SystemTextField
                        )

                    [3] => Array
                        (
                            [id] => 34764104
                            [destiny] => 6
                            [position] => 4
                            [office_title] => Телефон
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => phone
                            [created_at] => 2024-08-26T18:10:26.379+03:00
                            [updated_at] => 2024-10-07T12:21:50.060+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::Phone
                        )

                )

            [backoffice_fields] => Array
                (
                    [0] => Array
                        (
                            [id] => 34764098
                            [destiny] => 1
                            [position] => 2
                            [office_title] => Населенный пункт
                            [for_buyer] => 1
                            [obligatory] => 
                            [active] => 
                            [system_name] => full_locality_name
                            [created_at] => 2024-08-26T18:10:26.289+03:00
                            [updated_at] => 2024-08-27T14:37:43.420+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::Locality
                        )

                )

            [no_delivery] => 
            [kladr_autodetected_address] => 420000, Россия, Респ Татарстан, г Казань
            [country_options] => Array
                (
                    [0] => Array
                        (
                            [code] => RU
                            [title] => Россия
                            [selected] => 1
                        )

                )

            [address] => 
            [country] => 
            [state] => 
            [city] => 
            [zip] => 
            [street] => 
            [house] => 
            [flat] => 
            [entrance] => 
            [doorphone] => 
            [floor] => 
            [kladr_json] => Array
                (
                    [code] => 1600000100000
                    [country] => RU
                    [state] => Татарстан
                    [state_type] => Респ
                    [area] => 
                    [area_type] => 
                    [city] => Казань
                    [city_type] => г
                    [settlement] => 
                    [settlement_type] => 
                    [street] => 
                    [street_type] => 
                    [latitude] => 55.76730619529868
                    [longitude] => 49.099981999999976
                    [zip] => 420000
                    [result] => г Казань, Респ Татарстан
                    [last_level] => Казань
                    [last_level_type] => г
                    [region_zip] => 420000
                    [autodetected] => 1
                )

            [location] => Array
                (
                    [kladr_code] => 1600000100000
                    [zip] => 
                    [kladr_zip] => 420000
                    [region_zip] => 420000
                    [country] => RU
                    [state] => Татарстан
                    [state_type] => Респ
                    [area] => 
                    [area_type] => 
                    [city] => Казань
                    [city_type] => г
                    [settlement] => 
                    [settlement_type] => 
                    [address] => 
                    [street] => 
                    [street_type] => 
                    [house] => 
                    [flat] => 
                    [is_kladr] => 1
                    [latitude] => 55.76730619529868
                    [longitude] => 49.099981999999976
                    [autodetected] => 1
                )

        )

    [client] => Array
        (
            [id] => 85599207
            [email] => talichka93@gmail.com
            [name] => Наталия
            [phone] => 89376161470
            [created_at] => 2025-01-03T19:39:30.000+03:00
            [updated_at] => 2025-01-15T15:14:06.000+03:00
            [comment] => 
            [registered] => 1
            [subscribe] => 1
            [client_group_id] => 
            [surname] => Артемьева
            [middlename] => Александровна
            [bonus_points] => 5009
            [type] => Client::Individual
            [correspondent_account] => 
            [settlement_account] => 
            [consent_to_personal_data] => 1
            [o_auth_provider] => 
            [messenger_subscription] => 
            [contact_name] => Наталия
            [progressive_discount] => 
            [group_discount] => 
            [ip_addr] => 31.13.132.2
            [fields_values] => Array
                (
                    [0] => Array
                        (
                            [id] => 229774364
                            [field_id] => 34773177
                            [value] => 11.02.1995
                            [created_at] => 2025-01-03T19:39:30.757+03:00
                            [updated_at] => 2025-01-03T19:39:30.757+03:00
                            [type] => Дата
                            [name] => Дата рождения
                            [handle] => birthday
                        )

                    [1] => Array
                        (
                            [id] => 229774365
                            [field_id] => 34969831
                            [value] => 
                            [created_at] => 2025-01-03T19:39:30.763+03:00
                            [updated_at] => 2025-01-03T19:39:30.763+03:00
                            [type] => Текст
                            [name] => По рекомендации
                            [handle] => recommendation
                        )

                )

        )

    [discounts] => Array
        (
            [0] => Array
                (
                    [id] => 35876932
                    [description] => Скидка по купону admin1083
                    [type_id] => 1
                    [amount] => 60531.41
                    [full_amount] => 60531.41
                    [percent] => 99.9
                    [discount] => 99.9
                    [reference_id] => 17390164
                    [reference_type] => DiscountCode
                    [discount_products_ids] => Array
                        (
                        )

                    [discount_order_lines_ids] => Array
                        (
                        )

                    [discount_code_id] => 17390164
                    [created_at] => 2025-01-15T15:13:19.000+03:00
                    [updated_at] => 2025-01-15T15:13:19.000+03:00
                )

        )

    [total_price] => 210.59
    [items_price] => 60.59
    [id] => 127553532
    [key] => bf8e150c9441fe684f5664fae4a2cffa
    [number] => 102386
    [comment] => 
    [archived] => 
    [delivery_title] => Взятие биоматериала
    [delivery_description] => Взятие биоматериала (Оплачивается единоразово в каждом заказе)
    [delivery_price] => 150
    [full_delivery_price] => 150
    [payment_description] => 
    [payment_title] => Банковской картой, СБП, Долями, T-Pay
    [first_referer] => 
    [first_current_location] => /
    [first_query] => 
    [first_source_domain] => 
    [first_source] => Прямой трафик
    [referer] => https://insales-tinkoff.helixmedia.ru/
    [current_location] => /orders/4400d388ff9c9fc0c358f79e1bb1676c?success_payment=true
    [query] => 
    [source_domain] => insales-tinkoff.helixmedia.ru
    [source] => Сайты
    [fulfillment_status] => delivered
    [custom_status] => Array
        (
            [permalink] => zakaz-vypolnen
            [title] => Заказ выполнен
        )

    [delivered_at] => 2025-01-15T15:14:41.000+03:00
    [accepted_at] => 2025-01-15T15:14:41.000+03:00
    [created_at] => 2025-01-15T15:13:19.000+03:00
    [updated_at] => 2025-01-15T15:14:42.000+03:00
    [financial_status] => paid
    [delivery_date] => 
    [delivery_from_hour] => 
    [delivery_from_minutes] => 
    [delivery_to_hour] => 
    [delivery_to_minutes] => 
    [delivery_time] => 
    [paid_at] => 2025-01-15T15:14:06.000+03:00
    [delivery_variant_id] => 8225243
    [payment_gateway_id] => 6155301
    [margin] => 0.0
    [margin_amount] => 0.0
    [client_transaction_id] => 28469513
    [currency_code] => RUR
    [cookies] => Array
        (
        )

    [account_id] => 5708265
    [manager_comment] => 
    [locale] => ru
    [delivery_info] => Array
        (
            [delivery_variant_id] => 8225243
            [tariff_id] => 
            [title] => 
            [description] => 
            [price] => 150.0
            [shipping_company] => 
            [shipping_company_handle] => 
            [delivery_interval] => Array
                (
                    [min_days] => 
                    [max_days] => 
                    [description] => 
                )

            [errors] => Array
                (
                )

            [warnings] => Array
                (
                )

            [outlet] => Array
                (
                    [id] => 
                    [external_id] => 
                    [latitude] => 
                    [longitude] => 
                    [title] => 
                    [description] => 
                    [address] => 
                    [payment_method] => Array
                        (
                        )

                    [source_id] => 
                )

            [not_available] => 
        )

    [responsible_user_id] => 
    [total_profit] => -6464.41
)
