<?
class uds_game {
	var $api_key;
	function __construct($api_key="123") {
		$this->api_key=$api_key;
	}
	function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}
	function get_company_info() {
		$date = new DateTime();
		$url = 'https://udsgame.com/v1/partner/company';
		$uuid_v4 = $this->gen_uuid(); //'UUID'; //generate universally unique identifier version 4 (RFC 4122)
		//print $uuid_v4;
		$apiKey = $this->api_key; //set your api-key

		// Create a stream
		$opts = array(
			'http' => array(
				'method' => 'GET',
				'header' => "Accept: application/json\r\n" .
							"Accept-Charset: utf-8\r\n" .
							"X-Api-Key: ".$apiKey."\r\n" .
							"X-Origin-Request-Id: ".$uuid_v4."\r\n" .
							"X-Timestamp: ".$date->format(DateTime::ATOM)
			)
		);

		$context = stream_context_create($opts);

		$result = file_get_contents($url, false, $context);
		return $result;
	} 
}

?>
