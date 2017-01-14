<?php
class RedePayAPI {
	public function __construct() {
	}

	public function getConfig() {
		return (object) parse_ini_file("redepay-config.ini");
	} 

	public function createOrderId($properties, $config) {
		$postData = $this->buildJson($properties, $config);
		$url = $config->order_url;
		return $this->executeCurl("POST", $url, $postData, $config);
	}

	private function executeCurl($method, $url, $postData = null, $config) {
		$apiKey = $config->api_key;

		$headers = array(
			'access-token: '.$apiKey,
			'api-version: v1',
			'version: v1',
			'Content-Length: '.strlen($postData),
			'Accept: application/json',
			'Content-Type: application/json'
		);

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		if($method == "POST") {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		}
		elseif($method == "DELETE") {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		}

		curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Rede-Pay/1.0 (+https://github.com/marjoel/redepay-opencart)");
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        // Make the request
        $response = curl_exec($curl);
        $http_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_body = substr($response, $http_header_size);

		$data = $http_body;
		
		if (json_last_error() > 0) { 
			$data = $http_body;
		}
		return $data;
	}

	public function buildJson($properties, $config) {
		$parameters = array(
			"reference" => $this->getReference($properties),
			"discount" => $this->getDiscount($properties),
			"settings" => array(
				"maxInstallments" => $this->getMaxInstallments($properties, $config)
			),
			"customer" => array(
				"name" => $this->getCustomerName($properties),
				"email" => $this->getCustomerEmail($properties),
				"documents" => array(
					0 => array(
						"kind" => "cpf",
						"number" => $this->getDocument($properties)
					)
				),
				"phones" => $this->getPhoneNumbers($properties)
			),
			"shipping" => $this->getShippingInformation($properties),
			"items" => $this->getProducts($properties),
			"urls" => array(
				array(
					"kind" => "notification",
					"url" => $this->getNotificationUrl($config)
				),
				array(
					"kind" =>"redirect",
					"url" => $this->getRedirectUrl($properties, $config)
				),				
				array(
					"kind" => "cancel",
					"url" => $this->getCancelUrl($config)
				)
			),
		);
		return json_encode($parameters, JSON_UNESCAPED_UNICODE);
	}

	private function getShippingCost($shipping) {
		return $shipping['cost'];
	}

	private function handleCurrency($value) {
		return str_replace(".", "",number_format($value, 2));
	}

	private function getExpirationDate($hours = null) {
        $format  = 'Y-m-d\TH:i:s\+01:00';
        $hours = max($hours, 72);
        $timestamp  = time();
        $timestamp += (60 * 60) * $hours;
        $timestamp  = date($format, $timestamp);
        return $timestamp;
    }

	private function getReference($properties) {
		return strval($properties['order_id']);
	}

	private function getDiscount($properties) {
		return strval($this->handleCurrency($properties['discount']));
	}

	private function getMaxInstallments($properties, $config) {
		$totalOrder = floatval($properties['total']);
		$minValueForInstallment = floatval($config->min_value_installment);
		$totalInstallmentsAllowed = intval($config->max_installments);
		$minInstallmentValue = floatval($config->min_installment_value);

		if($totalOrder >= $minValueForInstallment) {
			while(($totalOrder/$totalInstallmentsAllowed) < $minInstallmentValue) {
				$totalInstallmentsAllowed--;
			}
		}
		else {
			$totalInstallmentsAllowed = 1;
		}
		return strval($totalInstallmentsAllowed);
	}

	private function getCustomerName($properties) {
		return ($properties['firstname']." ".$properties['lastname']);
	}

	private function getCustomerEmail($properties) {
		return $properties['email'];
	}

	private function getDocument($properties) {
		return strval(preg_replace('/[^0-9]/', '', $properties['customer_document']));
	}

	private function getPhoneNumbers($properties) {
		$phones[0] = array(
			"kind" => "cellphone",
			"number" => strval(preg_replace("/[^0-9]/", "", $properties['customer_cellphone']))
		);

		if($properties['customer_phone']){
			$phones[1] = array(
				"kind" => "home",
				"number" => strval(preg_replace("/[^0-9]/", "", $properties['customer_phone']))
			);
		}
		return $phones;
	}

	private function getShippingInformation($properties){
		$shipping['cost'] = $this->handleCurrency($this->getShippingCost($properties['shipping']));

		$shipping['address'] = array(
			//"alias" => '',
			"street" => $properties['address_street'],
			"number" => strval($properties['address_number']),
			"complement" => $properties['address_complement'],
			"district" => $properties['address_district'],
			"postalCode" => $properties['shipping_postcode'],
			"city" => $properties['shipping_city'],
			"state" => $properties['shipping_zone_code']
		);
		return $shipping;
	}

	private function getProducts($properties) {
		$products = $properties['products'];
		$productDetails = array();
		$i = 0;

		foreach ($products as $key => $value) {
			$productDetails[$i] = array(
				"id" => $value['product_id'],
				"amount" => $this->handleCurrency($value['price']),
				"quantity" => strval($value['quantity']),
				"description" => substr($value['name'], 0, (strlen($value['name']) > 80 ? 80 : strlen($value['name'])))
			);
			$i++;
		}
		return $productDetails;
	}

	private function getNotificationUrl($config) {
		return $config->notification_url;
	}

	private function getRedirectUrl($properties, $config) {
		return $config->redirect_url.'&orderId='.strval($properties['order_id']);
	}

	private function getCancelUrl($config) {
		return $config->cancel_url;
	}
}
?>
