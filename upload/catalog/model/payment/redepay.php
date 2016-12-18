<?php
class ModelPaymentRedePay extends Model {
    public function getMethod($address, $total) {
		$this->load->language('payment/redepay');

		if($this->show()) {
			$this->load->model('localisation/currency');

			$currencies = $this->model_localisation_currency->getCurrencies();
			$currency_value = $currencies['BRL']['value'];
			$total = ($total * $currency_value);

			if (($this->config->get('redepay_min_value_enable') > 0) && ($this->config->get('redepay_min_value_enable') > $total)) {
				$status = true;
			}
			else {
				$status = false;
			}

			$method_data = array();

			if ($status) {
				$method_data = array(
					'code'       => 'redepay',
					'title'      => $this->language->get('text_title'),
					'terms'      => '',
					'sort_order' => $this->config->get('redepay_sort_order'),
				);
			}
			return $method_data;
		}
		else {
			$this->log->write(sprintf($this->language->get('error_currency'), $this->session->data['currency']));
		}
    }

	private function show() {
		if($this->session->data['currency'] === "BRL") {
			return true;
		}
		return false;
	}
}
