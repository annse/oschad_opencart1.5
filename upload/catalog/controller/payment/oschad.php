<?php
class ControllerPaymentOschad extends Controller{
	
	protected function index(){
		
	$this->data['button_confirm'] = $this->language->get('button_confirm');
	
	$this->data['button_back'] = $this->language->get('button_back');
	
	
	// form action
	if ($this->config->get('oschad_server') == 'test'){		
		$this->data['action'] = 'https://3ds.oschadnybank.com/cgi-bin/cgi_test';		
	} else {		
		$this->data['action'] = 'https://3ds.oschadnybank.com/cgi-bin/cgi_link';		
	}
	
	$this->load->model('checkout/order');
	
	$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);	
	
	$this->data['merchant_id'] = $this->config->get('oschad_merch_id');
	
	$this->data['terminal_id'] = $this->config->get('oschad_terminal_id');	
	
	
	$this->load->language('payment/oschad');
	
	
	
	// vars
	$uah_code = "UAH";
	
	$uah_order_total = $this->currency->convert($order_info['total'], $order_info['currency_code'], $uah_code);
	
	$this->data['amount'] = $this->currency->format($uah_order_total, $uah_code, $order_info['currency_value'], false);
	
	$merchant = $this->data['merchant_id'];

	$terminal = $this->data['terminal_id'];
	
	$oschad_key = pack('H*', $this->config->get('oschad_key'));
		
	$merchant_url = $_SERVER['SERVER_NAME'];

	$amount = $this->data['amount'];
	
	$order_id = $this->session->data['order_id'];	
	

	if(strlen($order_id) < 6){
	$n = 6-strlen($order_id);
	for($i = 0; $i < $n; $i++){
		$order_id = "0".$order_id;
	}
	}
	
	
	$desc = trim(html_entity_decode($this->language->get('text_order_desc')).$this->session->data['order_id']);
	$desc_win = mb_convert_encoding ($desc, "Windows-1251", "UTF-8");
	
	$merch_name = html_entity_decode($this->config->get('config_name'));
	$merch_name_win = mb_convert_encoding ($merch_name, "Windows-1251", "UTF-8");

	$email = html_entity_decode($this->config->get('config_email'));	
	
	$time = gmdate("YmdHis", time());	
	
	$var = unpack("H*r", strtoupper(substr(md5(uniqid(30)), 0, 8)));
	$nonce = $var["r"];
	
	$trtype = '0';
	
	$backref = HTTPS_SERVER;

	// PSign
	$sign = strtoupper($this->bx_hmac("sha1", 
		(strlen($amount) > 0 ? strlen($amount).$amount : "-").
		(strlen($uah_code) > 0 ? strlen($uah_code).$uah_code : "-").
		(strlen($order_id) > 0 ? strlen($order_id).$order_id : "-").
		(strlen($desc_win) > 0 ? strlen($desc_win).$desc_win : "-").
		(strlen($merch_name_win) > 0 ? strlen($merch_name_win).$merch_name_win : "-").
		(strlen($merchant_url) > 0 ? strlen($merchant_url).$merchant_url : "-").
		(strlen($merchant) > 0 ? strlen($merchant).$merchant : "-").
		(strlen($terminal) > 0 ? strlen($terminal).$terminal : "-").
		(strlen($email) > 0 ? strlen($email).$email : "-").
		(strlen($trtype) > 0 ? strlen($trtype).$trtype : "-").
		"--".
		(strlen($time) > 0 ? strlen($time).$time : "-").
		(strlen($nonce) > 0 ? strlen($nonce).$nonce : "-").
		(strlen($backref) > 0 ? strlen($backref).$backref : "-"), 
		$oschad_key
	));
	
	 
	// tpl
	$this->data['order_id'] = $order_id;
	$this->data['trtype'] = $trtype;
	$this->data['uah_code'] = $uah_code;
	$this->data['desc'] =  $desc;
	$this->data['merch_name'] = $merch_name;
	$this->data['merch_url'] = $merchant_url;
	$this->data['email'] = $email;
	$this->data['nonce'] = $nonce;
	$this->data['time'] = $time;
	$this->data['backref'] = $backref;
	$this->data['sign'] = $sign;
	
	// -->		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oschad.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/oschad.tpl';
		} else {
			$this->template = 'default/template/payment/oschad.tpl';
		}
		
		$this->render();
	}
	

	private function bx_hmac($algo, $data, $key, $raw_output = false) 
	{ 
		$algo = strtolower($algo); 
		$pack = "H".strlen($algo("test")); 
		$size = 64; 
		$opad = str_repeat(chr(0x5C), $size); 
		$ipad = str_repeat(chr(0x36), $size); 

		if (strlen($key) > $size) { 
			$key = str_pad(pack($pack, $algo($key)), $size, chr(0x00)); 
		} else { 
			$key = str_pad($key, $size, chr(0x00)); 
		} 

		$lenKey = strlen($key) - 1;
		for ($i = 0; $i < $lenKey; $i++) { 
			$opad[$i] = $opad[$i] ^ $key[$i]; 
			$ipad[$i] = $ipad[$i] ^ $key[$i]; 
		} 

		$output = $algo($opad.pack($pack, $algo($ipad.$data))); 
		return ($raw_output) ? pack($pack, $output) : $output; 

	}
	
	
	public function callback(){
	
		$p_terminal = $this->request->post['TERMINAL'];
		$p_trtype = $this->request->post['TRTYPE'];
		$p_order = $this->request->post['ORDER'];
		$p_amount = $this->request->post['AMOUNT'];
		$p_currency = $this->request->post['CURRENCY'];
		$p_action = $this->request->post['ACTION'];
		$p_rc = $this->request->post['RC'];
		$p_approval = $this->request->post['APPROVAL'];
		$p_rrn = $this->request->post['RRN'];
		$p_int_ref = $this->request->post['INT_REF'];
		$p_tm = $this->request->post['TIMESTAMP'];
		$p_cardbin = $this->request->post['CARDBIN'];
		$p_nonce = $this->request->post['NONCE'];
		$p_sign = $this->request->post['P_SIGN'];
		$p_extcode = $this->request->post['EXTCODE'];
		
		$order = $this->session->data['order_id'];
		
		if(strlen($order) < 6){
		$n = 6-strlen($order);
		for($i = 0; $i < $n; $i++){
		$order = "0".$order;
		}
		}
				
	if($order == intval($p_order)){
		
				
		$oschad_key = pack('H*', $this->config->get('oschad_key'));
		

		// PSign		
		$sign = strtoupper($this->bx_hmac("sha1", 
		(strlen($p_rrn) > 0 ? strlen($p_rrn).$p_rrn : "-").
		(strlen($p_int_ref) > 0 ? strlen($p_int_ref).$p_int_ref : "-").
		(strlen($p_terminal) > 0 ? strlen($p_terminal).$p_terminal : "-").
		(strlen($p_trtype) > 0 ? strlen($p_trtype).$p_trtype : "-").
		(strlen($p_order) > 0 ? strlen($p_order).$p_order : "-").
		(strlen($p_amount) > 0 ? strlen($p_amount).$p_amount : "-").
		(strlen($p_currency) > 0 ? strlen($p_currency).$p_currency : "-").
		(strlen($p_action) > 0 ? strlen($p_action).$p_action : "-").
		(strlen($p_rc) > 0 ? strlen($p_rc).$p_rc : "-").
		(strlen($p_approval) > 0 ? strlen($p_approval).$p_approval : "-").
		(strlen($p_tm) > 0 ? strlen($p_tm).$p_tm : "-").
		(strlen($p_nonce) > 0 ? strlen($p_nonce).$p_nonce : "-")
		, 
		$oschad_key
		));
		
		if($sign == $p_sign){
			
			if($p_action == "0" && $p_rc = "00"){
				
				$this->load->model('checkout/order');
				
				$order_info = $this->model_checkout_order->getOrder($order);

				$uah_code = "UAH";
	
				$uah_order_total = $this->currency->convert($order_info['total'], $order_info['currency_code'], $uah_code);
	
				$amount = $this->currency->format($uah_order_total, $uah_code, $order_info['currency_value'], false);				
				
				$terminal = $this->config->get('oschad_terminal_id');	
				
				$email = html_entity_decode($this->config->get('config_email'));				
			
				$time = gmdate("YmdHis", time());
				
				$var = unpack("H*r", strtoupper(substr(md5(uniqid(30)), 0, 8)));
				$nonce = $var["r"];
				
				$trtype = 21;				
				
				$signew = $this->bx_hmac("sha1", 
						strlen($order).$order.
						strlen($amount).$amount.
						strlen($uah_code).$uah_code.
						strlen($p_rrn).$p_rrn.
						strlen($p_int_ref).$p_int_ref.
						strlen($trtype).$trtype.
						strlen($terminal).$terminal.
						strlen($time).$time.
						strlen($nonce).$nonce
						, 
						$oschad_key
					);
				
				$res = "";
				$res .= "TRTYPE=".$trtype;
				$res .= "&ORDER=".$order;
				$res .= "&AMOUNT=".$amount;
				$res .= "&CURRENCY=".$uah_code;
				$res .= "&RRN=".$p_rrn;
				$res .= "&INT_REF=".$p_int_ref;
				$res .= "&TERMINAL=".$terminal;
				$res .= "&TIMESTAMP=".$time;
				$res .= "&NONCE=".$nonce;
				$res .= "&EMAIL=".$email;
				$res .= "&LANG=";
				$res .= "&P_SIGN=".$signew;
				
				
				// server
				if ($this->config->get('oschad_server') == 'test'){		
				$server_url = 'https://3ds.oschadnybank.com/cgi-bin/cgi_test';		
				} else {		
				$server_url = 'https://3ds.oschadnybank.com/cgi-bin/cgi_link';		
				}

				$header = "POST ".$server_url." HTTP/1.0\r\n";
				$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$header .= "Content-Length: " . strlen($res) . "\r\n\r\n";
				
				$fp = fsockopen("ssl://3ds.oschadnybank.com", 443, $errno, $errstr, 60);
				if($fp)
					fputs ($fp, $header.$res);
				fclose ($fp);
				
				
				$this->model_checkout_order->confirm($order, $this->config->get('oschad_order_status_id'), 'OschadBank');
				
				$this->redirect(HTTPS_SERVER . 'index.php?route=payment/oschad/success');	
					
				}
				
				 else{
			
					$this->redirect(HTTPS_SERVER . 'index.php?route=payment/oschad/fail');	
			
		}
		
		} else{
			
			$this->redirect(HTTPS_SERVER . 'index.php?route=payment/oschad/fail');	
			
		}
		
	}
	}
	
	 public function fail() {
	
		$this->redirect(HTTPS_SERVER . 'index.php?route=checkout/checkout');
		
		return true;
	}
	
	
	public function success() {
		
		$this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
		
		return true;
	}

	}
?>