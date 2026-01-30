<?
class ya_audience {
	var $token='y0_AgAAAAABQCpqAAfweAAAAADOj3sVyUX1aUv6Q6aA5vnyziZjBBQ80Bs';
	function get_segments() {
		$url="https://api-audience.yandex.ru/v1/management/segments";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['hidden'=>$hide]) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$this->token,'Content-Type: application/json') );
		$res = json_decode(curl_exec($ch),true); 
		curl_close($ch);

		//print_r($http_response_header);
		return ($res);
		//~ if($res['status']=="updated")
			//~ return $res['data']['external_id']; else return false;
	}
	function add_data($segment_id,$fname) {
		$url="https://api-audience.yandex.ru/v1/management/segment/$segment_id/modify_data?modification_type=addition";
		//print "$url <br>";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$this->token]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$args['file'] = new CurlFile($fname, 'text/csv');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		$r = json_decode(curl_exec($ch),true);
		curl_close($ch);
		return $r;
		
		if(isset($r['segment']))
			return $r['segment'];
		else
			return false;
		//print_r($res);
    }
}
?>
