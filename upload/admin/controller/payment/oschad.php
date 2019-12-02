<?php
class ControllerPaymentOschad extends Controller{
	
	private $error = array();
	
	public function index(){
		
	$this->load->language('payment/oschad');
	
	$this->document->setTitle($this->language->get('heading_title'));
	
	$this->load->model('setting/setting');
	
	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		
			$this->model_setting_setting->editSetting('oschad', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		
		}
		
	$this->data['heading_title'] = $this->language->get('heading_title');
	$this->data['entry_merch_id'] = $this->language->get('entry_merch_id');
	$this->data['entry_terminal_id'] = $this->language->get('entry_terminal_id');
	$this->data['entry_key'] = $this->language->get('entry_key');
	
	$this->data['text_enabled'] = $this->language->get('text_enabled');
	$this->data['text_disabled'] = $this->language->get('text_disabled');
	$this->data['text_all_zones'] = $this->language->get('text_all_zones');	
	$this->data['text_test'] = $this->language->get('text_test');
	$this->data['text_live'] = $this->language->get('text_live');
	
	$this->data['entry_server'] = $this->language->get('entry_server');
	$this->data['entry_order_status'] = $this->language->get('entry_order_status');
	$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
	$this->data['entry_status'] = $this->language->get('entry_status');
	$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
	$this->data['entry_dev'] = $this->language->get('entry_dev');
		
	$this->data['button_save'] = $this->language->get('button_save');
	$this->data['button_cancel'] = $this->language->get('button_cancel');
	$this->data['tab_general'] = $this->language->get('tab_general');
	
	if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
	} else {
			$this->data['error_warning'] = '';
	}
	
	if (isset($this->error['merch_id'])) {
			$this->data['error_merch_id'] = $this->error['merch_id'];
	} else {
			$this->data['error_merch_id'] = '';
	}
	
	if (isset($this->error['terminal_id'])) {
			$this->data['error_terminal_id'] = $this->error['terminal_id'];
	} else {
			$this->data['error_terminal_id'] = '';
	}
	
	if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
	} else {
			$this->data['error_key'] = '';
	}
	
	
	$this->data['breadcrumbs'] = array();

   	$this->data['breadcrumbs'][] = array(
       	'text'      => $this->language->get('text_home'),
		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      	'separator' => false
   	);
	
	$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   	);

   	$this->data['breadcrumbs'][] = array(
        'text'      => $this->language->get('heading_title'),
		'href'      => $this->url->link('payment/oschad', 'token=' . $this->session->data['token'], 'SSL'),      		
      	'separator' => ' :: '
   	);
	
	
	
	
	
	$this->data['action'] = $this->url->link('payment/oschad', 'token=' . $this->session->data['token'], 'SSL');
		
	$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
	
	//Merchant ID
	if (isset($this->request->post['oschad_merch_id'])) {
		$this->data['oschad_merch_id'] = $this->request->post['oschad_merch_id'];
	} else {
		$this->data['oschad_merch_id'] = $this->config->get('oschad_merch_id');
	}
	
	//Terminal ID
	if (isset($this->request->post['oschad_terminal_id'])) {
		$this->data['oschad_terminal_id'] = $this->request->post['oschad_terminal_id'];
	} else {
		$this->data['oschad_terminal_id'] = $this->config->get('oschad_terminal_id');
	}

	if (isset($this->request->post['oschad_key'])) {
		$this->data['oschad_key'] = $this->request->post['oschad_key'];
	} else {
		$this->data['oschad_key'] = $this->config->get('oschad_key');
	}
	

	
	if (isset($this->request->post['oschad_server'])) {
		$this->data['oschad_server'] = $this->request->post['oschad_server'];
	} else {
		$this->data['oschad_server'] = $this->config->get('oschad_server');
	}
	
	
	if (isset($this->request->post['oschad_order_status_id'])) {
		$this->data['oschad_order_status_id'] = $this->request->post['oschad_order_status_id'];
	} else {
		$this->data['oschad_order_status_id'] = $this->config->get('oschad_order_status_id'); 
	}
		
	$this->load->model('localisation/order_status');
	
	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
	if (isset($this->request->post['oschad_geo_zone_id'])) {
		$this->data['oschad_geo_zone_id'] = $this->request->post['oschad_geo_zone_id'];
	} else {
		$this->data['oschad_geo_zone_id'] = $this->config->get('oschad_geo_zone_id'); 
	}
	
	$this->load->model('localisation/geo_zone');
		
	$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
	
	if (isset($this->request->post['oschad_status'])) {
		$this->data['oschad_status'] = $this->request->post['oschad_status'];
	} else {
		$this->data['oschad_status'] = $this->config->get('oschad_status');
	}
		
	if (isset($this->request->post['oschad_sort_order'])) {
		$this->data['oschad_sort_order'] = $this->request->post['oschad_sort_order'];
	} else {
		$this->data['oschad_sort_order'] = $this->config->get('oschad_sort_order');
	}

	$this->template = 'payment/oschad.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
	);
		
	$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/oschad')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['oschad_merch_id']) {
			$this->error['merch_id'] = $this->language->get('error_merch_id');
		}
		
		if (!$this->request->post['oschad_terminal_id']) {
			$this->error['terminal_id'] = $this->language->get('error_terminal_id');
		}
		
		if (!$this->request->post['oschad_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}
		
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>