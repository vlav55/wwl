<?
include "/var/www/vlav/data/www/wwl/inc/insales_webhook.1.inc.php";
exit;

http_response_code(200);
include "/var/www/vlav/data/www/wwl/inc/db.class.php";
include "init.inc.php";
$db=new db($database);
$rawData = file_get_contents('php://input');
$r=json_decode($rawData,true);
$out="";

$paid=false;
foreach($r['order_changes'] AS $item) {
	$out=print_r($item['value_is'],true);
	if($item['value_is']=='paid') 
		$paid=true;
}
if(!$paid)
	exit;
	
$order_number=trim($r['number']);

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

$name=$r['client']['name'];
$mob=$db->check_mob($r['client']['phone']);
$mob= $mob ? $mob : "";
$email=$db->validate_email($r['client']['email']) ? $r['client']['email'] : "";

$klid=0; $user_id=0;
if($bc) {
	if($klid=$db->get_klid_by_bc($bc)) {
		$user_id=$db->get_user_id($klid);
	}
}

$card=[
	'first_name'=>$name,
	'phone'=>$mob,
	'email'=>$email,
	'user_id'=>$user_id,
	'klid'=>$klid,
];
$uid=$db->cards_add($card,$update_if_exist=false);
if(!$db->hold_chk($uid) && $user_id) {
	$db->query("UPDATE cards SET user_id='$user_id',tm_user_id='".time()."',utm_affiliate='$klid' WHERE uid='$uid'");
}

$comm="INSALES SALE:\n";
foreach($r['order_lines'] AS $item) {
	$order_number=$item['order_id'];
	$sum=intval($item['full_total_price']);
	$sku=trim($item['sku']);
	$insales_product_id=intval($item['product_id']);
	if(empty($sku)) {
		$db->notify($uid,"INSALES ошибка : SKU не задан : {$item['title']}");
		continue;
	}
	if(!$pid=$db->dlookup("id","product","sku='$sku'"))
		$pid=1;
	if(!$db->dlookup("id","avangard","res=1 AND vk_uid=$uid AND order_number='$order_number' AND order_id='$insales_product_id'")) {
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
			res=1
			
		");
		$comm.="sku=$sku n=$order_number {$item['title']} sum=$sum \n";
	}
}
$db->save_comm($uid,0,$comm);

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



file_put_contents("insales_webhook.log",print_r($r,true) );
$db->notify_me("$order_number {$r['total_price']} $name $mob $email $bc\n".print_r($m,true));
$db->notify($uid,"INSALES: Заказ №$order_number на сумму: {$r['total_price']} оплачен");
print "ok";
exit;
?>

Array
(
    [fields_values] => Array
        (
        )

    [order_lines] => Array
        (
            [0] => Array
                (
                    [id] => 699044263
                    [order_id] => 125499872
                    [sale_price] => 10
                    [full_sale_price] => 9.5
                    [total_price] => 10
                    [full_total_price] => 9.5
                    [discounts_amount] => 0.5
                    [quantity] => 1
                    [reserved_quantity] => 1
                    [weight] => 
                    [dimensions] => 
                    [variant_id] => 787110960
                    [product_id] => 472170650
                    [sku] => 
                    [barcode] => 
                    [title] => тест 10р
                    [unit] => pce
                    [comment] => 
                    [updated_at] => 2024-12-29T00:57:03.000+03:00
                    [created_at] => 2024-12-27T21:26:15.000+03:00
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

        )

    [order_changes] => Array
        (
            [0] => Array
                (
                    [id] => 1667812108
                    [created_at] => 2024-12-29T14:05:14.110+03:00
                    [action] => financial_status_changed
                    [value_was] => pending
                    [value_is] => paid
                    [full_description] => Статус оплаты изменен с 'Не оплачен' на 'Оплачен' пользователем Vladimir Avshtolis
                    [user_name] => Vladimir Avshtolis
                )

            [1] => Array
                (
                    [id] => 1667806315
                    [created_at] => 2024-12-29T14:01:57.620+03:00
                    [action] => financial_status_changed
                    [value_was] => paid
                    [value_is] => pending
                    [full_description] => Статус оплаты изменен с 'Оплачен' на 'Не оплачен' пользователем Vladimir Avshtolis
                    [user_name] => Vladimir Avshtolis
                )
        )

    [discount] => Array
        (
            [id] => 35816224
            [description] => 
            [type_id] => 1
            [amount] => 0.5
            [full_amount] => 0.5
            [percent] => 5.0
            [discount] => 5.0
            [reference_id] => 
            [reference_type] => 
            [discount_products_ids] => Array
                (
                )

            [discount_order_lines_ids] => Array
                (
                )

            [discount_code_id] => 
            [created_at] => 2024-12-29T00:57:02.000+03:00
            [updated_at] => 2024-12-29T00:57:02.000+03:00
        )

    [shipping_address] => Array
        (
            [id] => 134356456
            [fields_values] => Array
                (
                )

            [name] => Vladimir Avshtolis
            [surname] => 
            [middlename] => 
            [phone] => 8(911)984-10-12
            [formatted_phone] => 8(911)984-10-12
            [full_name] => Vladimir Avshtolis
            [full_locality_name] => г Санкт-Петербург
            [full_delivery_address] => г Санкт-Петербург
            [address_for_gis] => г Санкт-Петербург
            [location_valid] => 1
            [recipient_fields] => Array
                (
                    [0] => Array
                        (
                            [id] => 36692385
                            [destiny] => 6
                            [position] => 1
                            [office_title] => Имя
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => contact_name
                            [created_at] => 2024-12-27T15:03:53.471+03:00
                            [updated_at] => 2024-12-27T15:03:53.471+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => Ваше имя
                            [example] => 
                            [type] => Field::ObligatoryTextField
                        )

                    [1] => Array
                        (
                            [id] => 36692388
                            [destiny] => 6
                            [position] => 2
                            [office_title] => Телефон
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => phone
                            [created_at] => 2024-12-27T15:03:53.542+03:00
                            [updated_at] => 2024-12-27T15:03:53.542+03:00
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
                            [id] => 36692386
                            [destiny] => 1
                            [position] => 2
                            [office_title] => Населенный пункт
                            [for_buyer] => 1
                            [obligatory] => 1
                            [active] => 1
                            [system_name] => full_locality_name
                            [created_at] => 2024-12-27T15:03:53.492+03:00
                            [updated_at] => 2024-12-27T15:03:53.492+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::Locality
                        )

                    [1] => Array
                        (
                            [id] => 36692390
                            [destiny] => 1
                            [position] => 4
                            [office_title] => Почтовый индекс
                            [for_buyer] => 1
                            [obligatory] => 
                            [active] => 1
                            [system_name] => zip
                            [created_at] => 2024-12-27T15:03:53.595+03:00
                            [updated_at] => 2024-12-27T15:03:53.595+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::SystemTextField
                        )

                    [2] => Array
                        (
                            [id] => 36692399
                            [destiny] => 1
                            [position] => 7
                            [office_title] => Улица
                            [for_buyer] => 1
                            [obligatory] => 
                            [active] => 1
                            [system_name] => street
                            [created_at] => 2024-12-27T15:03:53.911+03:00
                            [updated_at] => 2024-12-27T15:03:53.911+03:00
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
                            [id] => 36692401
                            [destiny] => 1
                            [position] => 8
                            [office_title] => Дом
                            [for_buyer] => 1
                            [obligatory] => 
                            [active] => 1
                            [system_name] => house
                            [created_at] => 2024-12-27T15:03:53.968+03:00
                            [updated_at] => 2024-12-27T15:03:53.968+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::SystemTextField
                        )

                    [4] => Array
                        (
                            [id] => 36692403
                            [destiny] => 1
                            [position] => 9
                            [office_title] => Квартира
                            [for_buyer] => 1
                            [obligatory] => 
                            [active] => 1
                            [system_name] => flat
                            [created_at] => 2024-12-27T15:03:54.092+03:00
                            [updated_at] => 2024-12-27T15:03:54.092+03:00
                            [show_in_result] => 1
                            [show_in_checkout] => 1
                            [is_indexed] => 
                            [hide_in_backoffice] => 
                            [handle] => 
                            [title] => 
                            [example] => 
                            [type] => Field::SystemTextField
                        )

                )

            [no_delivery] => 
            [kladr_autodetected_address] => 190000, Россия, г Санкт-Петербург
            [country_options] => Array
                (
                    [0] => Array
                        (
                            [code] => RU
                            [title] => Россия
                            [selected] => 
                        )

                )

            [address] => 
            [country] => 
            [state] => г Санкт-Петербург
            [city] => Санкт-Петербург
            [zip] => 
            [street] => 
            [house] => 
            [flat] => 
            [entrance] => 
            [doorphone] => 
            [floor] => 
            [kladr_json] => Array
                (
                    [code] => 7800000000000
                    [country] => RU
                    [state] => Санкт-Петербург
                    [state_type] => г
                    [area] => 
                    [area_type] => 
                    [city] => Санкт-Петербург
                    [city_type] => г
                    [settlement] => 
                    [settlement_type] => 
                    [street] => 
                    [street_type] => 
                    [latitude] => 59.94067450827501
                    [longitude] => 30.09286149999999
                    [zip] => 190000
                    [result] => г Санкт-Петербург
                    [last_level] => Санкт-Петербург
                    [last_level_type] => г
                    [region_zip] => 190000
                    [is_kladr] => 1
                )

            [location] => Array
                (
                    [kladr_code] => 7800000000000
                    [zip] => 
                    [kladr_zip] => 190000
                    [region_zip] => 190000
                    [country] => RU
                    [state] => Санкт-Петербург
                    [state_type] => г
                    [area] => 
                    [area_type] => 
                    [city] => Санкт-Петербург
                    [city_type] => г
                    [settlement] => 
                    [settlement_type] => 
                    [address] => 
                    [street] => 
                    [street_type] => 
                    [house] => 
                    [flat] => 
                    [is_kladr] => 1
                    [latitude] => 59.94067450827501
                    [longitude] => 30.09286149999999
                    [autodetected] => 
                )

        )

    [client] => Array
        (
            [id] => 85518240
            [email] => vlav@mail.ru
            [name] => Vladimir Avshtolis
            [phone] => 89119841012
            [created_at] => 2024-12-27T21:26:15.000+03:00
            [updated_at] => 2024-12-29T14:05:13.000+03:00
            [comment] => 
            [registered] => 
            [subscribe] => 1
            [client_group_id] => 
            [surname] => 
            [middlename] => 
            [bonus_points] => 0
            [type] => Client::Individual
            [correspondent_account] => 
            [settlement_account] => 
            [consent_to_personal_data] => 1
            [o_auth_provider] => 
            [messenger_subscription] => 1
            [contact_name] => Vladimir Avshtolis
            [progressive_discount] => 
            [group_discount] => 
            [ip_addr] => 2.56.204.5
            [fields_values] => Array
                (
                )

        )

    [discounts] => Array
        (
            [0] => Array
                (
                    [id] => 35816224
                    [description] => 
                    [type_id] => 1
                    [amount] => 0.5
                    [full_amount] => 0.5
                    [percent] => 5.0
                    [discount] => 5.0
                    [reference_id] => 
                    [reference_type] => 
                    [discount_products_ids] => Array
                        (
                        )

                    [discount_order_lines_ids] => Array
                        (
                        )

                    [discount_code_id] => 
                    [created_at] => 2024-12-29T00:57:02.000+03:00
                    [updated_at] => 2024-12-29T00:57:02.000+03:00
                )

        )

    [total_price] => 9.5
    [items_price] => 9.5
    [id] => 125499872
    [key] => e8e9f0f2bf2b07bb58ed7bc1724517d1
    [number] => 1001
    [comment] => 
    [archived] => 
    [delivery_title] => Самовывоз
    [delivery_description] => Самовывоз (На пункте выдачи)
    [delivery_price] => 0
    [full_delivery_price] => 0
    [payment_description] => <p><span>Оплата наличными или банковской картой при получении заказа</span></p>
    [payment_title] => Наличными или картой при получении
    [first_referer] => 
    [first_current_location] => /
    [first_query] => 
    [first_source_domain] => 
    [first_source] => Прямой трафик
    [referer] => 
    [current_location] => /
    [query] => 
    [source_domain] => 
    [source] => Прямой трафик
    [fulfillment_status] => delivered
    [custom_status] => Array
        (
            [permalink] => delivered
            [title] => Доставлен
        )

    [delivered_at] => 2024-12-28T15:39:23.000+03:00
    [accepted_at] => 2024-12-27T21:29:12.000+03:00
    [created_at] => 2024-12-27T21:26:15.000+03:00
    [updated_at] => 2024-12-29T14:05:13.000+03:00
    [financial_status] => paid
    [delivery_date] => 
    [delivery_from_hour] => 
    [delivery_from_minutes] => 
    [delivery_to_hour] => 
    [delivery_to_minutes] => 
    [delivery_time] => 
    [paid_at] => 2024-12-29T14:05:13.000+03:00
    [delivery_variant_id] => 8364666
    [payment_gateway_id] => 6254719
    [margin] => 0.0
    [margin_amount] => 0.0
    [client_transaction_id] => 
    [currency_code] => RUR
    [cookies] => Array
        (
        )

    [account_id] => 5790531
    [manager_comment] => 
    [locale] => ru
    [delivery_info] => Array
        (
            [delivery_variant_id] => 8364666
            [tariff_id] => 
            [title] => 
            [description] => 
            [price] => 0
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
    [total_profit] => 9.5
)
bash-4.4$ 

