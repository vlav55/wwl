<?
//$db->notify_me("HERE_");
$no_insales_lk_cashout_button=true;
$prodamus_usd="AC|SBP|ACkz|ACkztjp|ACf|ACUSDGTL|ACEURGTL|ACBYNGTL|ACUSDKB|ACEURKB";
$prodamus_inst="AC|SBP|fresh_installment_0_0_6|fresh_installment_0_0_10|fresh_installment_0_0_12|fresh_installment_0_0_18|fresh_installment_0_0_24|fresh_installment_0_0_36|dolyame:installment|proonline_installment_rb_0_0_6|proonline_installment_rb_0_0_12|proonline_installment_rb_0_0_18|proonline_installment_rb_0_0_24|proonline_installment_kz_0_0_6|proonline_installment_kz_0_0_12|proonline_installment_kz_0_0_18|proonline_installment_kz_0_0_24|proonline_installment_kg_0_0_6|proonline_installment_kg_0_0_12|proonline_installment_kg_0_0_18|proonline_installment_kg_0_0_24|broker_installment_0_0_6|broker_installment_0_0_10|broker_installment_0_0_12|broker_installment_0_0_24|installment_4_14:v3.0|installment_5_21:v3.0|installment_6_28:v3.0|installment_10_28:v3.0|installment_12_28:v3.0|yandex_installment_0_0_2|yandex_installment_0_0_4|yandex_installment_0_0_6|yandex_installment_0_0_12|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_3|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_4|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_6|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_10|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_12|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_18|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_24|TINKOFF_API_SUBSIDIZED_HIGH_INSTALLMENT_0_0_36|credit|vsegdada_installment_0_0_3|vsegdada_installment_0_0_4|vsegdada_installment_0_0_6|vsegdada_installment_0_0_10|vsegdada_installment_0_0_12|vsegdada_installment_0_0_18|vsegdada_installment_0_0_24|vsegdada_installment_0_0_36|sbrf_installment_0_0_6|sbrf_installment_0_0_10|sbrf_installment_0_0_12|sbrf_installment_0_0_18|otp_installment_0_0_3|otp_installment_0_0_6|otp_installment_0_0_10|otp_installment_0_0_12|otp_installment_0_0_18|otp_installment_0_0_24|otp_installment_0_0_36|freedomfinance_installment_0_0_3|freedomfinance_installment_0_0_6|freedomfinance_installment_0_0_12|freedomfinance_installment_0_0_24|monetaworld|sbrf_bnpl";
$pay_prodamus_available_payment_methods=$_POST['custom']==1 ? $prodamus_inst : $prodamus_usd;

$lk_display_referal_contacts=true;

$products_winwinland=[]; //wwl account will create for these products
$res=$vkt->query("SELECT * FROM product WHERE term>0 AND del=0 AND (id>=20 AND id <=40)");
while($r=$vkt->fetch_assoc($res)) {
	$products_winwinland[]=$r['id'];
}
$products_yclients=[130, 131, 132, 135];  //check also tm_end_licence($ctrl_id)
foreach ($products_yclients as $pid_yclients) {
    $products_winwinland[] = $pid_yclients;
}
?>
