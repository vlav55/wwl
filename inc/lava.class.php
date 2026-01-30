<?php
class lava {
    private $apiKey;
    public $err;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }
    public function get_products() {
        // API URL specific to this method
        $apiUrl = 'https://gate.lava.top/api/v2/products?contentCategories=PRODUCT&feedVisibility=ONLY_VISIBLE&showAllSubscriptionPeriods=false';

        // Initialize cURL session
        $curl = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Api-Key: ' . $this->apiKey,
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            throw new Exception('cURL Error: ' . curl_error($curl));
        }

        // Close cURL session
        curl_close($curl);

        // Decode JSON response and return products
        $products = json_decode($response, true);
        return $products['items'] ?? []; // Return an empty array if 'items' is not set
    }
    public function get_invoice($email, $offer_id, $currency='RUB') {
        // API URL for creating an invoice
        $apiUrl = 'https://gate.lava.top/api/v2/invoice';

        // Prepare the data for the invoice
        $data = [
            'email' => $email,
            'offerId' => $offer_id,
            'currency' => $currency,
        ];

        // Initialize cURL session
        $curl = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Api-Key: ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            throw new Exception('cURL Error: ' . curl_error($curl));
        }

        // Close cURL session
        curl_close($curl);

        // Decode JSON response
        $r = json_decode($response, true);
        if(isset($r['error'])) {
			$this->err=$r['error'];
			return false;
		}
        return $r; // Return null if paymentUrl is not set
    }
}
?>
