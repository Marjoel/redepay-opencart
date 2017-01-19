<?php
/**
*  @author   Marjoel Moreira [marjoel@marjoel.com]
*  @license  https://www.gnu.org/licenses/gpl-3.0.en.html
*/

class ControllerExtensionPaymentRedePay extends Controller {
    private $error = array();

    public function index() {
        $this->load->language("extension/payment/redepay");

        if(($this->request->server["REQUEST_METHOD"] == "POST") && $this->validate()) {
        	$this->load->model("setting/setting");
            $this->model_setting_setting->editSetting("redepay", $this->request->post);
            $this->session->data["success"] = $this->language->get("text_success");
            $this->response->redirect($this->url->link("extension/extension", "token=" . $this->session->data["token"] . "&type=payment", true));
        }

		/* get all texts */
		$texts = $this->getAllTexts();
		foreach ($texts as $text) {
			$data[$text] = $this->language->get($text);
		}

		$config = $this->getConfig();

		/* opencart default */
		$this->document->setTitle($data["heading_title"]);
		$data["breadcrumbs"] = $this->getBreadcrumbs($datal);
		$data["action"] = $this->url->link("extension/payment/redepay", "token=" . $this->session->data["token"], true);
        $data["cancel"] = $this->url->link("extension/payment", "token=" . $this->session->data["token"] . "&type=payment", true);
		$data["header"] = $this->load->controller("common/header");
        $data["column_left"] = $this->load->controller("common/column_left");
        $data["footer"] = $this->load->controller("common/footer");
		
		/* utils */
		$data["util_fields"] = $this->getFields();
		$data["util_status"] = $this->getStatus();
		$data["util_installments_range"] = $this->getInstallmentsRange();
		$data["notification_url"] = $this->getUrlBase() . $config->notification_url;
		$data["redirect_url"] = $this->getUrlBase() . $config->redirect_url;
		$data["cancel_url"] = $this->getUrlBase() . $config->cancel_url;

		/* get all fields */
		$fields = $this->getAllFields();
		foreach ($fields as $field) {
			$data[$field] = isset($this->request->post[$field]) ? $this->request->post[$field] : $this->config->get($field);
		}

		/* if there are errors, show them */
		$fields = $this->getAllRequiredFields();
		$fields[] = "warning";

		foreach ($fields as $field) {
			$data["error_" . $field] = isset($this->error[$field]) ? $this->error[$field] : "";
		}
		$this->response->setOutput($this->load->view("extension/payment/redepay", $data));
	}

	protected function validate() {
		if(!$this->user->hasPermission("modify", "extension/payment/redepay")) {
			$this->error["warning"] = $this->language->get("error_permission");
			return !$this->error;
		}

		if(!$this->request->post["redepay_min_installment_value"]) {
			$this->request->post["redepay_min_installment_value"] = 0;
		}

		if(!$this->request->post["redepay_min_value_enable"]) {
			$this->request->post["redepay_min_value_enable"] = 0;
		}

		/* validate required fields */
		$fields = $this->getAllRequiredFields();
		foreach ($fields as $field) {
			if(!$this->request->post["redepay_" .$field]) {
				$this->error[$field] = $this->language->get("error_" . $field);
			}
		}
		return !$this->error;
	}

	private function getFields() {
		$this->load->language("customer/customer");
		$this->load->model("customer/custom_field");

		$fields = array();

		/* get default fields */
		$default_fields = array(
			"address_1",
			"address_2",
			"telephone",
			"fax",
			"company"
		);

		foreach ($default_fields as $field) {
			$fields[$field] = $this->language->get("entry_" . $field);
		}

		/* get custom fields */
		$custom_fields = $this->model_customer_custom_field->getCustomFields();
		foreach($custom_fields as $field) {
			$fields[$field["custom_field_id"]] = $field["name"];
		}
		return $fields;
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

	private function getStatus() {
		$this->load->model("localisation/order_status");
		$statuses = $this->model_localisation_order_status->getOrderStatuses();
		$status = array();

		foreach ($statuses as $stats) {
			$status[$stats["order_status_id"]] = $stats["name"];
		}
		return $status;
	}

	private function getConfig() {
		return (object) parse_ini_file(DIR_SYSTEM . "library/redepay/redepay-config.ini");
	}

	private function getUrlBase() {
		if (isset($this->request->server["HTTPS"])) {
			return HTTPS_CATALOG;
		}
		return HTTP_CATALOG;
	}

	private function getBreadcrumbs($data) {
		$breadcrumbs = array();

		$breadcrumbs[] = array(
			"text" => $data["text_home"],
			"href" => $this->url->link("common/dashboard", "token=" . $this->session->data["token"], true)
		);

		$breadcrumbs[] = array(
			"text" => $data["text_payment"],
			"href" => $this->url->link("extension/extension", "token=" . $this->session->data["token"]."&type=payment", true)
		);

		$breadcrumbs[] = array(
			"text" => $data["heading_title"],
			"href" => $this->url->link("extension/payment/redepay", "token=" . $this->session->data["token"], true)
		);
		return $breadcrumbs;
	}

	private function getAllRequiredFields() {
		return array(
			"max_installments",
			"api_key",
			"token_nip",
			"public_token",
			"notification_url",
			"redirect_url",
			"cancel_url",
			"document",
			"address",
			"number",
			"neighborhood",
			"cellphone"
		);
	}

	private function getAllFields() {
		return array(
			"redepay_max_installments",
			"redepay_min_value_installment",
			"redepay_min_installment_value",
			"redepay_min_value_enable",
			"redepay_api_key",
			"redepay_token_nip",
			"redepay_public_token",
			"redepay_notification_url",
			"redepay_redirect_url",
			"redepay_cancel_url",
			"redepay_document",
			"redepay_address",
			"redepay_number",
			"redepay_complement",
			"redepay_neighborhood",
			"redepay_phone",
			"redepay_cellphone",
			"redepay_status",
			"redepay_sort_order",
			"redepay_order_waiting_payment",
			"redepay_order_payment_analisys",
			"redepay_order_approved_payment",
			"redepay_order_payment_dispute",
			"redepay_order_reversed_payment",
			"redepay_order_chargeback_payment",
			"redepay_order_canceled_payment"
		);
	}

	private function getAllTexts() {
		return array(
			"heading_title",
			"button_save",
			"button_cancel",
			"entry_min_value_enable",
			"entry_max_installments",
			"entry_min_value_installment",
			"entry_min_installment_value",
			"entry_api_key",
			"entry_token_nip",
			"entry_public_token",
			"entry_notification_url",
			"entry_redirect_url",
			"entry_cancel_url",
			"entry_document",
			"entry_address",
			"entry_number",
			"entry_complement",
			"entry_neighborhood",
			"entry_phone",
			"entry_cellphone",
			"entry_sort_order",
			"entry_status",
			"entry_order_waiting_payment",
			"entry_order_payment_analisys",
			"entry_order_approved_payment",
			"entry_order_payment_dispute",
			"entry_order_canceled_payment",
			"entry_order_reversed_payment",
			"entry_order_chargeback_payment",
			"text_home",
			"text_payment",
			"text_enabled",
			"text_disabled",
			"text_edit",
			"text_edit_tokens",
			"text_edit_installments",
			"text_edit_notifications",
			"text_edit_redirects",
			"text_edit_fields",
			"text_edit_order_status",
			"text_edit_settings",
			"text_register",
			"help_max_installments",
			"help_min_value_installment",
			"help_min_installment_value",
			"help_min_value_enable",
			"help_api_key",
			"help_token_nip",
			"help_public_token",
			"help_notification_url",
			"help_redirect_url",
			"help_cancel_url",
			"help_document",
			"help_address",
			"help_number",
			"help_complement",
			"help_neighborhood",
			"help_phone",
			"help_cellphone",
			"help_order_waiting_payment",
			"help_order_payment_analisys",
			"help_order_approved_payment",
			"help_order_payment_dispute",
			"help_order_canceled_payment",
			"help_order_reversed_payment",
			"help_order_chargeback_payment",
			"error_api_key",
			"error_token_nip",
			"error_public_token",
			"error_notification_url",
			"error_redirect_url",
			"error_cancel_url",
			"error_document",
			"error_address",
			"error_number",
			"error_neighborhood",
			"error_cellphone"
		);
	}
}
