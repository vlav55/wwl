<?php
require_once '/var/www/vlav/data/www/wwl/inc/qr_code/vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
class qrcode_gen {
    public function generate($url, $fname) {
        // Remove existing file
        if (file_exists($fname)) {
            unlink($fname);
        }
        
        // Generate QR code
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($fname);
        // Return success status
        return $result;
    }
	function display($result) {
		header('Content-Type: '.$result->getMimeType());
		echo $result->getString();
	}
}
?>
