<?php
class ModelPaymentRedePay extends Model {
    public function getMethod($address, $total) {
        $this->load->language('payment/redepay');
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$total = ($total * $order_info['currency_value']);
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
}
