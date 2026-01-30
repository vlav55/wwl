<?
include "../../api.class.php";
if($_SERVER['REQUEST_METHOD']==='GET') {
	$res=$db->query("SELECT * FROM product WHERE id>0 AND del=0 AND (fee_1>0 OR fee_2>0)");
	$arr=[];
	while($r=$db->fetch_assoc($res)) {
		if(!$fee1=$db->dlookup("fee_1","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
			$fee1=$r['fee_1'];
		if(!$fee2=$db->dlookup("fee_2","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
			$fee2=$r['fee_2'];
		if(!$fee_cnt=$db->dlookup("fee_cnt","partnerka_spec","uid='$uid' AND pid='{$r['id']}'"))
			$fee_cnt=$r['fee_cnt'];
		$fee1=($fee1<=100) ? $fee1."%" : $fee1."р.";
		$fee2=($fee2<=100) ? $fee2."%" : $fee2."р.";
		$fee_cnt=(!$fee_cnt) ? "без огр" : $fee_cnt;

		$arr[]=[
				'product_id'=>$r['id'],
				'title'=>$r['descr'],
				'price0'=>$r['price0'],
				'price1'=>$r['price1'],
				'price2'=>$r['price2'],
				'fee_1'=>$fee1,
				'fee_2'=>$fee2,
				'fee_cnt'=>$r['fee_cnt'],
			];
	}

	http_response_code(200);
	print json_encode($arr);
	exit;
}
?>
