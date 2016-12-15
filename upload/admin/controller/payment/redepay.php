<?php
class ControllerPaymentRedePay extends Controller {
    private $error = array();

    public function index() {
        if(version_compare(VERSION, '2.2.0.0', '>=')) {
            $ssl = true;
        }
		else {
            $ssl = 'SSL';
        }

        $this->load->language('payment/redepay');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('redepay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], $ssl));
        }
		
		$data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $ssl),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], $ssl),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/redepay', 'token=' . $this->session->data['token'], $ssl),
        );
		
		$config = $this->getConfig();
		
		$base_url = "";
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$base_url = $this->config->get('config_ssl');
		}
		else {
			$base_url = $this->config->get('config_url');
		}
		$data['notification_url'] = $base_url . $config->notification_url;
		$data['redirect_url'] = $base_url . $config->redirect_url;
		$data['cancel_url'] = $base_url . $config->cancel_url;
		
		
		$data['action'] = $this->url->link('payment/redepay', 'token=' . $this->session->data['token'], $ssl);
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], $ssl);
		
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['entry_max_installments'] = $this->language->get('entry_max_installments');
		$data['entry_api_key'] = $this->language->get('entry_api_key');
		$data['entry_token_nip'] = $this->language->get('entry_token_nip');
		$data['entry_public_token'] = $this->language->get('entry_public_token');
		$data['entry_notification_url'] = $this->language->get('entry_notification_url');
		$data['entry_redirect_url'] = $this->language->get('entry_redirect_url');
		$data['entry_cancel_url'] = $this->language->get('entry_cancel_url');
		$data['entry_document'] = $this->language->get('entry_document');
		$data['entry_address'] = $this->language->get('entry_address');
		$data['entry_number'] = $this->language->get('entry_number');
		$data['entry_complement'] = $this->language->get('entry_complement');
		$data['entry_neighborhood'] = $this->language->get('entry_neighborhood');
		$data['entry_phone'] = $this->language->get('entry_phone');
		$data['entry_cellphone'] = $this->language->get('entry_cellphone');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
        $data['entry_order_waiting_payment'] = $this->language->get('entry_order_waiting_payment');
        $data['entry_order_payment_analisys'] = $this->language->get('entry_order_payment_analisys');
        $data['entry_order_approved_payment'] = $this->language->get('entry_order_approved_payment');
        $data['entry_order_payment_dispute'] = $this->language->get('entry_order_payment_dispute');
        $data['entry_order_canceled_payment'] = $this->language->get('entry_order_canceled_payment');
        $data['entry_order_reversed_payment'] = $this->language->get('entry_order_reversed_payment');
        $data['entry_order_chargeback_payment'] = $this->language->get('entry_order_chargeback_payment');
		
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_edit'] = $this->language->get('text_edit');
		
		$data['help_max_installments'] = $this->language->get('help_max_installments');
		$data['help_api_key'] = $this->language->get('help_api_key');
		$data['help_token_nip'] = $this->language->get('help_token_nip');
		$data['help_public_token'] = $this->language->get('help_public_token');
		$data['help_notification_url'] = $this->language->get('help_notification_url');
		$data['help_redirect_url'] = $this->language->get('help_redirect_url');
		$data['help_cancel_url'] = $this->language->get('help_cancel_url');
		$data['help_document'] = $this->language->get('help_document');
		$data['help_address'] = $this->language->get('help_address');
		$data['help_number'] = $this->language->get('help_number');
		$data['help_complement'] = $this->language->get('help_complement');
		$data['help_neighborhood'] = $this->language->get('help_neighborhood');
		$data['help_phone'] = $this->language->get('help_phone');
		$data['help_cellphone'] = $this->language->get('help_cellphone');
		$data['help_order_waiting_payment'] = $this->language->get('help_order_waiting_payment');
		$data['help_order_payment_analisys'] = $this->language->get('help_order_payment_analisys');
		$data['help_order_approved_payment'] = $this->language->get('help_order_approved_payment');
		$data['help_order_payment_dispute'] = $this->language->get('help_order_payment_dispute');
		$data['help_order_canceled_payment'] = $this->language->get('help_order_canceled_payment');
		$data['help_order_reversed_payment'] = $this->language->get('help_order_reversed_payment');
		$data['help_order_chargeback_payment'] = $this->language->get('help_order_chargeback_payment');
		
		$data['error_api_key'] = $this->language->get('error_api_key');
		$data['error_token_nip'] = $this->language->get('error_token_nip');
		$data['error_public_token'] = $this->language->get('error_public_token');
		$data['error_notification_url'] = $this->language->get('error_notification_url');
		$data['error_redirect_url'] = $this->language->get('error_redirect_url');
		$data['error_cancel_url'] = $this->language->get('error_cancel_url');
		$data['error_document'] = $this->language->get('error_document');
		$data['error_address'] = $this->language->get('error_address');
		$data['error_number'] = $this->language->get('error_number');
		$data['error_complement'] = $this->language->get('error_complement');
		$data['error_neighborhood'] = $this->language->get('error_neighborhood');
		$data['error_phone'] = $this->language->get('error_phone');
		$data['error_cellphone'] = $this->language->get('error_cellphone');
		
		$data['util_installments_range'] = $this->getInstallmentsRange();
		$data['util_fields'] = $this->getFields();
		$data['util_status'] = $this->getStatus();
		
		
		/* get */
		if(isset($this->request->post['redepay_max_installments'])) {
            $data['redepay_max_installments'] = $this->request->post['redepay_max_installments'];
        }
		else {
            $data['redepay_max_installments'] = $this->config->get('redepay_max_installments');
        }
		
		if(isset($this->request->post['redepay_api_key'])) {
            $data['redepay_api_key'] = $this->request->post['redepay_api_key'];
        }
		else {
            $data['redepay_api_key'] = $this->config->get('redepay_api_key');
        }
		
		if(isset($this->request->post['redepay_token_nip'])) {
            $data['redepay_token_nip'] = $this->request->post['redepay_token_nip'];
        }
		else {
            $data['redepay_token_nip'] = $this->config->get('redepay_token_nip');
        }
		
		if(isset($this->request->post['redepay_public_token'])) {
            $data['redepay_public_token'] = $this->request->post['redepay_public_token'];
        }
		else {
            $data['redepay_public_token'] = $this->config->get('redepay_public_token');
        }
		
		if(isset($this->request->post['redepay_notification_url'])) {
            $data['redepay_notification_url'] = $this->request->post['redepay_notification_url'];
        }
		else {
            $data['redepay_notification_url'] = $this->config->get('redepay_notification_url');
        }
		
		if(isset($this->request->post['redepay_redirect_url'])) {
            $data['redepay_redirect_url'] = $this->request->post['redepay_redirect_url'];
        }
		else {
            $data['redepay_redirect_url'] = $this->config->get('redepay_redirect_url');
        }
		
		if(isset($this->request->post['redepay_cancel_url'])) {
            $data['redepay_cancel_url'] = $this->request->post['redepay_cancel_url'];
        }
		else {
            $data['redepay_cancel_url'] = $this->config->get('redepay_cancel_url');
        }
		
		if(isset($this->request->post['redepay_document'])) {
            $data['redepay_document'] = $this->request->post['redepay_document'];
        }
		else {
            $data['redepay_document'] = $this->config->get('redepay_document');
        }
		
		if(isset($this->request->post['redepay_address'])) {
            $data['redepay_address'] = $this->request->post['redepay_address'];
        }
		else {
            $data['redepay_address'] = $this->config->get('redepay_address');
        }
		
		if(isset($this->request->post['redepay_number'])) {
            $data['redepay_number'] = $this->request->post['redepay_number'];
        }
		else {
            $data['redepay_number'] = $this->config->get('redepay_number');
        }
		
		if(isset($this->request->post['redepay_complement'])) {
            $data['redepay_complement'] = $this->request->post['redepay_complement'];
        }
		else {
            $data['redepay_complement'] = $this->config->get('redepay_complement');
        }
		
		if(isset($this->request->post['redepay_neighborhood'])) {
            $data['redepay_neighborhood'] = $this->request->post['redepay_neighborhood'];
        }
		else {
            $data['redepay_neighborhood'] = $this->config->get('redepay_neighborhood');
        }
		
		if(isset($this->request->post['redepay_phone'])) {
            $data['redepay_phone'] = $this->request->post['redepay_phone'];
        }
		else {
            $data['redepay_phone'] = $this->config->get('redepay_phone');
        }
		
		if(isset($this->request->post['redepay_cellphone'])) {
            $data['redepay_cellphone'] = $this->request->post['redepay_cellphone'];
        }
		else {
            $data['redepay_cellphone'] = $this->config->get('redepay_cellphone');
        }
		
		if(isset($this->request->post['redepay_status'])) {
            $data['redepay_status'] = $this->request->post['redepay_status'];
        }
		else {
            $data['redepay_status'] = $this->config->get('redepay_status');
        }

        if(isset($this->request->post['redepay_sort_order'])) {
            $data['redepay_sort_order'] = $this->request->post['redepay_sort_order'];
        }
		else {
            $data['redepay_sort_order'] = $this->config->get('redepay_sort_order');
        }
		
		if(isset($this->request->post['redepay_order_waiting_payment'])) {
            $data['redepay_order_waiting_payment'] = $this->request->post['redepay_order_waiting_payment'];
        }
		else {
            $data['redepay_order_waiting_payment'] = $this->config->get('redepay_order_waiting_payment');
        }
		
		if(isset($this->request->post['redepay_order_payment_analisys'])) {
            $data['redepay_order_payment_analisys'] = $this->request->post['redepay_order_payment_analisys'];
        }
		else {
            $data['redepay_order_payment_analisys'] = $this->config->get('redepay_order_payment_analisys');
        }
		
		if(isset($this->request->post['redepay_order_approved_payment'])) {
            $data['redepay_order_approved_payment'] = $this->request->post['redepay_order_approved_payment'];
        }
		else {
            $data['redepay_order_approved_payment'] = $this->config->get('redepay_order_approved_payment');
        }
		
		if(isset($this->request->post['redepay_order_payment_dispute'])) {
            $data['redepay_order_payment_dispute'] = $this->request->post['redepay_order_payment_dispute'];
        }
		else {
            $data['redepay_order_payment_dispute'] = $this->config->get('redepay_order_payment_dispute');
        }
		
		if(isset($this->request->post['redepay_order_reversed_payment'])) {
            $data['redepay_order_reversed_payment'] = $this->request->post['redepay_order_reversed_payment'];
        }
		else {
            $data['redepay_order_reversed_payment'] = $this->config->get('redepay_order_reversed_payment');
        }
		
		if(isset($this->request->post['redepay_order_chargeback_payment'])) {
            $data['redepay_order_chargeback_payment'] = $this->request->post['redepay_order_chargeback_payment'];
        }
		else {
            $data['redepay_order_chargeback_payment'] = $this->config->get('redepay_order_chargeback_payment');
        }
		
		if(isset($this->request->post['redepay_order_canceled_payment'])) {
            $data['redepay_order_canceled_payment'] = $this->request->post['redepay_order_canceled_payment'];
        }
		else {
            $data['redepay_order_canceled_payment'] = $this->config->get('redepay_order_canceled_payment');
        }
		
		/* errors */
		if(isset($this->error['public_token'])) {
            $data['error_public_token'] = $this->error['public_token'];
        }
		else {
            $data['error_public_token'] = '';
        }
		
		if(isset($this->error['max_installments'])) {
            $data['error_max_installments'] = $this->error['max_installments'];
        }
		else {
            $data['error_max_installments'] = '';
        }
		
		if(isset($this->error['api_key'])) {
            $data['error_api_key'] = $this->error['api_key'];
        }
		else {
            $data['error_api_key'] = '';
        }
		
		if(isset($this->error['token_nip'])) {
            $data['error_token_nip'] = $this->error['token_nip'];
        }
		else {
            $data['error_token_nip'] = '';
        }
		
		if(isset($this->error['notification_url'])) {
            $data['error_notification_url'] = $this->error['notification_url'];
        }
		else {
            $data['error_notification_url'] = '';
        }
		
		if(isset($this->error['redirect_url'])) {
            $data['error_redirect_url'] = $this->error['redirect_url'];
        }
		else {
            $data['error_redirect_url'] = '';
        }
		
		if(isset($this->error['cancel_url'])) {
            $data['error_cancel_url'] = $this->error['cancel_url'];
        }
		else {
            $data['error_cancel_url'] = '';
        }
		
		if(isset($this->error['document'])) {
            $data['error_document'] = $this->error['document'];
        }
		else {
            $data['error_document'] = '';
        }
		
		if(isset($this->error['address'])) {
            $data['error_address'] = $this->error['address'];
        }
		else {
            $data['error_address'] = '';
        }
		
		if(isset($this->error['number'])) {
            $data['error_number'] = $this->error['number'];
        }
		else {
            $data['error_number'] = '';
        }
		
		if(isset($this->error['neighborhood'])) {
            $data['error_neighborhood'] = $this->error['neighborhood'];
        }
		else {
            $data['error_neighborhood'] = '';
        }
		
		if(isset($this->error['cellphone'])) {
            $data['error_cellphone'] = $this->error['cellphone'];
        }
		else {
            $data['error_cellphone'] = '';
        }
		
		if(isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }
		else {
            $data['error_warning'] = '';
        }
		
        if(version_compare(VERSION, '2.2.0.0', '>=')) {
            $this->response->setOutput($this->load->view('payment/redepay', $data));
        }
		else {
            $this->response->setOutput($this->load->view('payment/redepay.tpl', $data));
        }
    }
	
	private function getInstallmentsRange() {
		return array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12'
		);
	}
	
	private function getFields() {
		$fields = array();
		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);

		$this->load->model('sale/custom_field');
		$this->load->language('sale/customer');

        $custom_fields = $this->model_sale_custom_field->getCustomFields();

		$fields['address_1'] = $this->language->get('entry_address_1');
		$fields['address_2'] = $this->language->get('entry_address_2');
		$fields['telephone'] = $this->language->get('entry_telephone');
		$fields['fax'] = $this->language->get('entry_fax');
		$fields['company'] = $this->language->get('entry_company');
		
		foreach($custom_fields as $custom_field) {
			$fields[$custom_field['custom_field_id']] = $custom_field['name'];
		}
		return $fields;
	}
	
	private function getStatus() {
		$this->load->model('localisation/order_status');
		$statuses = $this->model_localisation_order_status->getOrderStatuses();
		$status = array();
		
		foreach ($statuses as $stats) {
			$status[$stats['order_status_id']] = $stats['name'];
		}
        return $status;
	}

    protected function validate() {
        if(!$this->user->hasPermission('modify', 'payment/redepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if(!$this->request->post['redepay_api_key']) {
            $this->error['api_key'] = $this->language->get('error_api_key');
        }

        if(!$this->request->post['redepay_token_nip']) {
            $this->error['token_nip'] = $this->language->get('error_token_nip');
        }

        if(!$this->request->post['redepay_public_token']) {
            $this->error['public_token'] = $this->language->get('error_public_token');
        }

        if(!$this->request->post['redepay_notification_url']) {
            $this->error['notification_url'] = $this->language->get('error_notification_url');
        }

        if(!$this->request->post['redepay_redirect_url']) {
            $this->error['redirect_url'] = $this->language->get('error_redirect_url');
        }

        if(!$this->request->post['redepay_cancel_url']) {
            $this->error['cancel_url'] = $this->language->get('error_cancel_url');
        }

        if(!$this->request->post['redepay_document']) {
            $this->error['document'] = $this->language->get('error_document');
        }

        if(!$this->request->post['redepay_address']) {
            $this->error['address'] = $this->language->get('error_address');
        }

        if(!$this->request->post['redepay_number']) {
            $this->error['number'] = $this->language->get('error_number');
        }

        if(!$this->request->post['redepay_neighborhood']) {
            $this->error['neighborhood'] = $this->language->get('error_neighborhood');
        }

        if(!$this->request->post['redepay_cellphone']) {
            $this->error['redepay_cellphone'] = $this->language->get('error_redepay_cellphone');
        }
        return !$this->error;
    }
	
	private function getConfig() {
		return (object) parse_ini_file(DIR_SYSTEM . 'library/redepay/redepay-config.ini');
	} 
}
