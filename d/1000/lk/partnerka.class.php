<?
include_once "/var/www/vlav/data/www/wwl/inc/db.class.php";
class partnerka extends db {
	var $products_exclude;
	var $fee=0;
	var $fee2=0;
	var $levels=1;
	function partnerka($klid) {
		if(!$klid)
			return;
		include "products_exclude.inc.php";
		$this->products_exclude=$products_exclude;
		$this->fee=$this->dlookup("fee","users","klid='$klid'");
		$this->fee2=$this->dlookup("fee2","users","klid='$klid'");
		$this->levels=$this->dlookup("levels","users","klid='$klid'");
	}
	function first_date_of_prev_month($month,$year) {
		$last_month=($month==1)?12:$month-1;
		$year1=($last_month==12)?$year-1:$year;
		return "01.$last_month.$year1";
	}
	function cnt_reg($klid,$tm1,$tm2) {
		return $this->fetch_assoc($this->query("SELECT COUNT(uid) AS cnt FROM (SELECT cards.uid AS uid FROM msgs JOIN cards ON cards.uid=msgs.uid WHERE cards.id!=$klid AND  (msgs.source_id=13 OR msgs.source_id=39) AND utm_affiliate='$klid' AND msgs.tm>=$tm1 AND msgs.tm<=$tm2 GROUP BY cards.uid) AS q1"))['cnt'];
	}
	function sum_pay($klid,$tm1,$tm2,$debug=0) {
		$add="";
		foreach($this->products_exclude AS $pid)
			$add.="product_id!=$pid AND ";
		$add.="1";
		return $this->fetch_assoc($this->query("SELECT SUM(amount) AS s FROM avangard JOIN cards ON uid=vk_uid WHERE cards.id!=$klid AND avangard.tm>=$tm1 AND avangard.tm<=$tm2 AND res=1 AND utm_affiliate='$klid' AND $add",$debug))['s'];
	}
	function sum_fee($klid,$tm1,$tm2,$debug=0) {
		$fee=$this->fee;
		$fee2=$this->fee2;
		$levels=$this->levels;
		$add="";
		foreach($this->products_exclude AS $pid)
			$add.="product_id!=$pid AND ";
		$add.="1";
		$res= $this->query("SELECT vk_uid,amount FROM avangard JOIN cards ON uid=vk_uid WHERE cards.id!=$klid AND avangard.tm>=$tm1 AND avangard.tm<=$tm2 AND res=1 AND utm_affiliate='$klid' AND $add",$debug);
		$sum=0;
		while($r=$this->fetch_assoc($res)) {
			if($levels==0)
				$sum+=intval($r['amount']*$fee/100);
			if($levels==1)
				continue;
			$klid2=$this->dlookup("id","cards","uid='{$r['vk_uid']}'");
			$amount2=$this->sum_pay($klid2,$tm1,$tm2,$debug=0);
			$sum+=intval($amount2*$fee2/100);
		}
		return $sum;
	}
}


?>
