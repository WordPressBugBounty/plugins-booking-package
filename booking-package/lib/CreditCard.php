<?php
    if(!defined('ABSPATH')){
    	exit;
	}
	
    class booking_package_CreditCard {
        
        public $response = array();
        
        public $pluginName = null;
        
        public $lang = null;
        
        public $prefix = null;
        
        public $request_timeout = 30;
        
        public function __construct($pluginName, $prefix){
            
            $this->pluginName = $pluginName;
            $this->prefix = $prefix;
            
        }
        
        public function pay($payId, $stripe_konbini, $stripe_paypay, $public_key, $secret, $token, $payment_live, $amont, $currency, $key, $name, $email, $bookingDate){
            
            if (has_filter('booking_package_creditcard_pay')) {
                return apply_filters('booking_package_creditcard_pay', $payId, $stripe_konbini, $stripe_paypay, $public_key, $secret, $token, $payment_live, $amont, $currency, $key, $name, $email, $bookingDate);
            }

            $response = array('error' => "8001");
            
            if (intval($amont) <= 0) {
                
                return array('error' => __('Online payment is required to complete this booking.', 'booking-package'));
                
            }
            
		    if ($payId == 'stripe') {
                
                $capture_method = get_option($this->prefix . 'stripe_capture_method', 'automatic');
                if ($capture_method == 'automatic' && $stripe_konbini === 0 && $stripe_paypay === 0) {
                    
                    $this->captureStripe($payId, $secret, $token);
                    
                }
                
			    $response = $this->commitStripe($payId, $stripe_konbini, $stripe_paypay, $secret, $token, $amont, $currency, $key, $name, $email, $bookingDate);
                
		    } else if ($payId == 'paypal') {
		        
		        $access_token = $this->getAccessTokenPayPal($public_key, $secret, $payment_live);
		        $response = $this->getPaymentDetailsPayPal($token, $access_token, $payment_live, $key, $name, $email, $bookingDate, $amont, $currency);
		        #var_dump($response);
		        
		    }
            
            $this->response = $response;
	        return $response;
            
        }
        
        public function cancel($payId, $public_key, $secret, $payment_live, $chargeId){
            
            if (has_filter('booking_package_creditcard_cancel')) {
                return apply_filters('booking_package_creditcard_cancel', $payId, $public_key, $secret, $payment_live, $chargeId);
            }

            #var_dump($payId);
            $response = array('error' => "8002");
            if($payId == 'stripe' || $payId == 'stripe_paypay'){
                
                $response = $this->cancelStripe($payId, $secret, $chargeId);
                
            }else if($payId == 'paypal'){
                
                $access_token = $this->getAccessTokenPayPal($public_key, $secret, $payment_live);
                $response = $this->refoundPayPal($chargeId, $access_token, $payment_live);
                
            }
            
            $this->response = $response;
	        return $response;
            
        }
        
        public function intentForStripe($secret, $amont, $currency) {
            
            if (has_filter('booking_package_creditcard_intent_stripe')) {
                return apply_filters('booking_package_creditcard_intent_stripe', $secret, $amont, $currency);
            }

            $params = array(
                'amount' => $amont, 
                'currency' => $currency, 
                'capture_method' => 'manual', 
                /** 'payment_method_types' => array('card', 'konbini'), **/
                'metadata' => array(
                    'integration_check' => 'accept_a_payment'
                )
            );
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request("https://api.stripe.com/v1/payment_intents", $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $response;
            
        }
        
        public function intentForStripeExpressCheckout($secret, $amont, $currency) {
            
            if (has_filter('booking_package_creditcard_intent_stripe_express_checkout')) {
                return apply_filters('booking_package_creditcard_intent_stripe_express_checkout', $secret, $amont, $currency);
            }

            $params = array(
                'amount' => $amont, 
                'currency' => $currency, 
                'capture_method' => 'manual', 
                'automatic_payment_methods' => ['enabled' => 'true'],
            );
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => http_build_query($params),
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request("https://api.stripe.com/v1/payment_intents", $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $response;
            
        }
        
        public function intentForStripePayPay($secret, $amont, $currency) {
            
            if (has_filter('booking_package_creditcard_intent_stripe_paypay')) {
                return apply_filters('booking_package_creditcard_intent_stripe_paypay', $secret, $amont, $currency);
            }

            $params = array(
                'amount' => $amont, 
                'currency' => $currency, 
                'payment_method_types' => array('paypay'),
            );
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request("https://api.stripe.com/v1/payment_intents", $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $response;
            
        }
        
        public function intentForStripeKonbini($secret, $amont, $currency, $expiresDate) {
            
            if (has_filter('booking_package_creditcard_intent_stripe_konbini')) {
                return apply_filters('booking_package_creditcard_intent_stripe_konbini', $secret, $amont, $currency, $expiresDate);
            }

            $params = array(
                'amount' => $amont, 
                'currency' => $currency, 
                'capture_method' => 'automatic', 
                'payment_method_types' => array('konbini'),
                'payment_method_options' => array(
                    'konbini' => array(
                        'expires_at' => $expiresDate,
                    ),
                ),
                'metadata' => array(
                    'integration_check' => 'accept_a_payment'
                )
            );
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request("https://api.stripe.com/v1/payment_intents", $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $response;
            
        }
        
        public function updateIntentForStripe($secret, $amont, $id) {
            
            if (has_filter('booking_package_creditcard_update_intent_stripe')) {
                return apply_filters('booking_package_creditcard_update_intent_stripe', $secret, $amont, $id);
            }

            $params = array(
                'amount' => $amont,
            );
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            
            $response = wp_remote_request("https://api.stripe.com/v1/payment_intents/" . $id, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $response;
            
        }
        
        public function captureStripe($payId, $secret, $token) {
            
            if (has_filter('booking_package_creditcard_capture_stripe')) {
                return apply_filters('booking_package_creditcard_capture_stripe', $payId, $secret, $token);
            }

            $params = array();
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                /**'body' => $params, **/
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request('https://api.stripe.com/v1/payment_intents/' . $token . '/capture', $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/" . $token . '/capture');
			curl_setopt($ch, CURLOPT_USERPWD, $secret.":");
			#curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt($ch, CURLOPT_POST, 1);
			
			ob_start();
			$response = curl_exec($ch);
			$response = ob_get_contents();
			ob_end_clean();
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
            $response = json_decode($response, true);
            **/
            
        }
        
        public function commitStripe($payId, $stripe_konbini, $stripe_paypay, $secret, $token, $amont, $currency, $key, $name, $email, $bookingDate) {
            
            if (has_filter('booking_package_creditcard_commit_stripe')) {
                return apply_filters('booking_package_creditcard_commit_stripe', $payId, $stripe_konbini, $stripe_paypay, $secret, $token, $amont, $currency, $key, $name, $email, $bookingDate);
            }

            $message = array();
            #$params = array('source' => $token, 'amount' => $amont, 'currency' => $currency, 'description' => "Key: " . $key . " Name: " . $name);
            $response = null;
            $params = array('description' => "Key: " . $key . " Booking: " . $bookingDate . " Name: " . $name . " Emails: " . $email);
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($secret . ':')
                )
            );
            $response = wp_remote_request('https://api.stripe.com/v1/payment_intents/' . $token, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/" . $token);
			curl_setopt($ch, CURLOPT_USERPWD, $secret.":");
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt($ch, CURLOPT_POST, 1);
			
			ob_start();
			$response = curl_exec($ch);
			$response = ob_get_contents();
			ob_end_clean();
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
            $response = json_decode($response, true);
            **/
            
            #$id = $response['charges']['data'][0]['id'];
            $id = isset($response['id']) ? $response['id'] : null;
            #var_dump($response);
            
            if ($httpCode < 400) {
                
                $status = isset($response['status']) ? $response['status'] : '';
                $stripe_amount = isset($response['amount']) ? intval($response['amount']) : 0;
                $payment_method_types = (isset($response['payment_method_types']) && is_array($response['payment_method_types'])) ? $response['payment_method_types'] : array();
                $is_konbini_intent = (intval($stripe_konbini) === 1 && in_array('konbini', $payment_method_types, true));
                $is_paypay_intent = (intval($stripe_paypay) === 1 && in_array('paypay', $payment_method_types, true) && ($status === 'succeeded' || $status === 'requires_capture'));
                if (!empty($id) && $stripe_amount > 0 && $stripe_amount === intval($amont) && ($status === 'succeeded' || $status === 'requires_capture' || $is_konbini_intent || $is_paypay_intent)) {
                    
                    $message['cardToken'] = $id;
                    
                } else {
                    
                    if (isset($response['last_payment_error']['message']) && !empty($response['last_payment_error']['message'])) {
                        
                        $message['error'] = $response['last_payment_error']['message'] . " (" . $httpCode . ")";
                        
                    } else if (isset($response['error']['message']) && !empty($response['error']['message'])) {
                        
                        $message['error'] = $response['error']['message'] . " (" . $httpCode . ")";
                        
                    } else {
                        
                        $message['error'] = __('Payment processing failed. Please try again.', 'booking-package') . " (" . $httpCode . ")";
                        
                    }
                
                }
            
            } else {
                
                $message = $this->httpCodeError($httpCode, $payId);
                if (isset($response['error']) && isset($response['error']['message'])) {
                    
                    $message = array('code' => $httpCode, 'error' => $response['error']['message'], 'object' => $response);
                    
                }
                
            }
            
            return $message;
            
        }
        
        public function commitPayPal($payId, $secret, $token, $amont, $currency, $key, $name){
            
            if (has_filter('booking_package_creditcard_commit_paypal')) {
                return apply_filters('booking_package_creditcard_commit_paypal', $payId, $secret, $token, $amont, $currency, $key, $name);
            }

            $message = array();
            $bool = true;
            if($bool == true){
                
                $request = array(
                    'amount' => $amont, 
                    'merchantAccountId' => $currency, 
                    'paymentMethodNonce' => $token, 
                    'orderId' => $key, 
                    'options' => array(
                        'submitForSettlement' => true
                    )
                );
                $gateway = new Braintree_Gateway(array('accessToken' => $secret));
                $result = $gateway->transaction()->sale($request);
                if($result->success === true){
                    
                    $message['cardToken'] = $result->transaction->id;
                    $message['paymentId'] = $result->transaction->paypalDetails->paymentId;
                    $message['payerId'] = $result->transaction->paypalDetails->payerId;
                    
                }else{
                    
                    $message['error'] = $result->message;
                    
                }
                
                return $message;
                
            }else{
                
                return array('error' => "8020");
                
            }
            
        }
        
        public function update($payId, $public_key, $secret, $chargeId, $bookingId, $payment_live, $name = '', $email = '', $bookingDate = ''){

            if (has_filter('booking_package_creditcard_update')) {
                return apply_filters('booking_package_creditcard_update', $payId, $public_key, $secret, $chargeId, $bookingId, $payment_live, $name, $email, $bookingDate);
            }

            $response = array('error' => "8002");
            if($payId == 'paypal'){

                $access_token = $this->getAccessTokenPayPal($public_key, $secret, $payment_live);
                $response = $this->updatePaymentPayPal($chargeId, $bookingId, $access_token, $payment_live);

            } else if($payId == 'stripe' || $payId == 'stripe_paypay' || $payId == 'stripe_konbini'){
                
                $params = array(
                    'description' => "Key: " . $bookingId . " Booking: " . $bookingDate . " Name: " . $name . " Emails: " . $email,
                    'metadata' => array(
                        'booking_id' => strval($bookingId)
                    )
                );
                $args = array(
                    'method'  => 'POST',
                    'timeout' => $this->request_timeout,
                    'body'    => $params,
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode($secret . ':')
                    )
                );
                $res = wp_remote_request('https://api.stripe.com/v1/payment_intents/' . $chargeId, $args);
                $response = json_decode(wp_remote_retrieve_body($res), true);

            }

            $this->response = $response;
            return $response;

        }
        
        public function getAccessTokenPayPal($public_key, $secret, $payment_live){
            
            if (has_filter('booking_package_creditcard_get_access_token_paypal')) {
                return apply_filters('booking_package_creditcard_get_access_token_paypal', $public_key, $secret, $payment_live);
            }

            $sandbox = '';
            if(intval($payment_live) == 0){
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api.".$sandbox."paypal.com/v1/oauth2/token";
            $message = array();
            $headers = array('Accept: application/json', 'Accept-Language: en_US');
            $params = array('grant_type' => 'client_credentials');
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'body' => $params,
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($public_key . ':' . $secret)
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            return isset($response['access_token']) ? $response['access_token'] : null;
            
        }
        
        public function capturePayPal($id, $token, $payment_live) {
            
            if (has_filter('booking_package_creditcard_capture_paypal')) {
                return apply_filters('booking_package_creditcard_capture_paypal', $id, $token, $payment_live);
            }

            $sandbox = '';
            if(intval($payment_live) == 0){
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api." . $sandbox . "paypal.com/v2/checkout/orders/" . $id . '/capture';
            $message = array();
            $headers = array('content-type: application/json', 'Authorization: Bearer '.$token);
            
            $args = array(
                'method' => 'POST',
                'timeout' => $this->request_timeout, 
                'headers' => array(
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $response = null;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array()));
			curl_setopt($ch, CURLOPT_POST, 1);
            
            ob_start();
			$response = curl_exec($ch);
			$response = ob_get_contents();
			ob_end_clean();
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
            $response = json_decode($response, true);
            **/
            
            return $response;
            
        }
        
        public function addBookingIdOnPayPal($id, $bookingId, $token, $payment_live) {
            
            if (has_filter('booking_package_creditcard_add_booking_id_on_paypal')) {
                return apply_filters('booking_package_creditcard_add_booking_id_on_paypal', $id, $bookingId, $token, $payment_live);
            }

            $sandbox = '';
            if(intval($payment_live) == 0){
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api." . $sandbox . "paypal.com/v2/checkout/orders/" . $id;
            $message = array();
            $headers = array('content-type: application/json', 'Authorization: Bearer '.$token);
            $params = array(
                array(
                    'op' => 'add',
                    'path' => "/purchase_units/@reference_id=='default'/custom_id",
                    'value' => $bookingId,
                ),
            );
            
            $args = array(
                'method' => 'PATCH',
                'timeout' => $this->request_timeout, 
                'body' => json_encode($params),
                'headers' => array(
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $response = null;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            
            ob_start();
            $response = curl_exec($ch);
            $response = ob_get_contents();
            ob_end_clean();
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);
            **/
            
        }
        
        public function getPaymentDetailsPayPal($id, $token, $payment_live, $bookingId, $name, $email, $bookingDate, $expectedAmount = null, $expectedCurrency = null) {
            
            if (has_filter('booking_package_creditcard_get_payment_details_paypal')) {
                return apply_filters('booking_package_creditcard_get_payment_details_paypal', $id, $token, $payment_live, $bookingId, $name, $email, $bookingDate, $expectedAmount, $expectedCurrency);
            }

            $message = array();
            if (empty($token)) {
                
                $message['error'] = 'Payment through PayPal failed.';
                return $message;
                
            }
            
            $this->addBookingIdOnPayPal($id, 'Booking ID: ' . $bookingId . ', Name: ' . $name, $token, $payment_live);
            $this->capturePayPal($id, $token, $payment_live);
            $sandbox = '';
            if (intval($payment_live) == 0) {
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api.". $sandbox ."paypal.com/v1/payments/payment/".$id;
            $url = "https://api." . $sandbox . "paypal.com/v2/checkout/orders/" . $id;
            $headers = array('content-type: application/json', 'Authorization: Bearer '.$token);
            
            $args = array(
                'method' => 'GET',
                'timeout' => $this->request_timeout, 
                'headers' => array(
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            if ($httpCode < 400 && isset($response['status']) && $response['status'] == 'COMPLETED') {
                
                if ($expectedAmount !== null && $expectedCurrency !== null) {
                    
                    $paidAmount = isset($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'])
                        ? floatval($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'])
                        : (isset($response['purchase_units'][0]['amount']['value']) ? floatval($response['purchase_units'][0]['amount']['value']) : -1);
                    $paidCurrency = isset($response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'])
                        ? strtoupper($response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'])
                        : (isset($response['purchase_units'][0]['amount']['currency_code']) ? strtoupper($response['purchase_units'][0]['amount']['currency_code']) : '');
                    
                    $zeroDecimalCurrencies = array('JPY', 'KRW', 'HUF', 'TWD', 'TRY', 'THB', 'COP', 'ARS', 'IDR');
                    if (class_exists('booking_package_setting')) {
                        
                        $setting = new booking_package_setting($this->prefix, $this->pluginName);
                        $currencies = $setting->getCurrencies();
                        $currencyKey = strtolower($expectedCurrency);
                        $digits = isset($currencies[$currencyKey]['ISOdigits']) ? intval($currencies[$currencyKey]['ISOdigits']) : (in_array(strtoupper($expectedCurrency), $zeroDecimalCurrencies, true) ? 0 : 2);
                        
                    } else {
                        
                        $digits = in_array(strtoupper($expectedCurrency), $zeroDecimalCurrencies, true) ? 0 : 2;
                        
                    }
                    
                    if ($digits > 0 && $paidAmount >= 0) {
                        
                        $paidAmount = $paidAmount * pow(10, $digits);
                        
                    }
                    
                    if (intval(round($paidAmount)) !== intval($expectedAmount) || $paidCurrency !== strtoupper($expectedCurrency)) {
                        
                        $message['error'] = 'Payment amount or currency mismatch.';
                        return $message;
                        
                    }
                    
                }
                
                $message['cardToken'] = isset($response['id']) ? $response['id'] : $id;
                if (isset($response['purchase_units'][0]['payments']['captures'][0]['id'])) {
                    
                    $message['cardToken'] = $response['purchase_units'][0]['payments']['captures'][0]['id'];
                    
                }
                
            } else {
                
                $message['error'] = 'Payment through PayPal failed.';
                $message['response'] = $response;
                
            }
            
            return $message;
            
        }
        
        public function updatePaymentPayPal($id, $bookingId, $token, $payment_live){
            
            if (has_filter('booking_package_creditcard_update_payment_paypal')) {
                return apply_filters('booking_package_creditcard_update_payment_paypal', $id, $bookingId, $token, $payment_live);
            }

            $sandbox = '';
            if(intval($payment_live) == 0){
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api.".$sandbox."paypal.com/v1/payments/payment/".$id;
            $message = array();
            $headers = array('Content-Type: application/json', 'Authorization: Bearer '.$token);
            $params = array(array('op' => 'replace', 'path' => '/transactions/0/custom', 'value' => 'Booking ID ' . $bookingId));
            
            $args = array(
                'method' => 'PATCH',
                'timeout' => $this->request_timeout, 
                'body' => json_encode($params),
                'headers' => array(
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $response = null;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
			
			ob_start();
			$response = curl_exec($ch);
			$response = ob_get_contents();
			ob_end_clean();
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			**/
			
			$message['httpCode'] = $httpCode;
			$message['id'] = $id;
			$message['params'] = $params;
			
			return $message;
            
        }
        
        public function refoundPayPal($id, $token, $payment_live){
            
            if (has_filter('booking_package_creditcard_refound_paypal')) {
                return apply_filters('booking_package_creditcard_refound_paypal', $id, $token, $payment_live);
            }

            $sandbox = '';
            if(intval($payment_live) == 0){
                
                $sandbox = 'sandbox.';
                
            }
            
            $url = "https://api.".$sandbox."paypal.com/v1/payments/sale/".$id;
            $message = array();
            $headers = array('content-type: application/json', 'Accept: application/json', 'Authorization: Bearer '.$token);
            
            $args = array(
                'method' => 'GET',
                'timeout' => $this->request_timeout, 
                'headers' => array(
                    'content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                )
            );
            $response = wp_remote_request($url, $args);
            $httpCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            
            /**
            $response = null;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, 0);
			
			ob_start();
			$response = curl_exec($ch);
			$response = ob_get_contents();
			ob_end_clean();
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$response = json_decode($response, true);
			**/
			
			if ($httpCode == 200) {
                
                $url = "https://api." . $sandbox . "paypal.com/v1/payments/sale/" . $id . "/refund";
                $headers = array('content-type: application/json', 'Accept: application/json', 'Authorization: Bearer '.$token);
                $params = array('amount' => array('currency' => $response['amount']['currency'], 'total' => $response['amount']['total']));
                
                $args = array(
                    'method' => 'POST',
                    'timeout' => $this->request_timeout, 
                    /**'body' => json_encode($params),**/
                    'headers' => array(
                        'content-type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    )
                );
                $response = wp_remote_request($url, $args);
                $httpCode = wp_remote_retrieve_response_code($response);
                $response = json_decode(wp_remote_retrieve_body($response), true);
                
                /**
                $response = null;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                #curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                #curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                
                ob_start();
                $response = curl_exec($ch);
                $response = ob_get_contents();
                ob_end_clean();
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $response = json_decode($response, true);
                **/
                
                $message['response'] = $response;
                if($httpCode != 201){
                    
                    $message['status'] = 'error';
                    $message['message'] = 'Refund failed';
                    
                }
                
			}
			
            return $message;
            
        }
        
        public function cancelStripe($payId, $secret, $chargeId) {
            
            if (has_filter('booking_package_creditcard_cancel_stripe')) {
                return apply_filters('booking_package_creditcard_cancel_stripe', $payId, $secret, $chargeId);
            }

            $status = 'requires_capture';
            $originalId = $chargeId;
            if (preg_match('/^pi_/', $originalId)) {
                $args = array(
                    'method' => 'GET',
                    'timeout' => $this->request_timeout, 
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode($secret . ':')
                    )
                );
                $response = wp_remote_request('https://api.stripe.com/v1/payment_intents/' . $originalId, $args);
                
                if (!is_wp_error($response)) {
                    
                    $responseBody = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($responseBody['status']) && $responseBody['status'] == 'succeeded') {
                        
                        $status = 'succeeded';
                        
                    }
                    
                } else {
                    
                    return $this->httpCodeError(500, $payId);
                    
                }
            } else {
                
                $status = 'succeeded';
                
            }
            
            $response = null;
            $httpCode = null;
            $message = array();
            if ($status == 'requires_capture') {
                
                $args = array(
                    'method' => 'POST',
                    'timeout' => $this->request_timeout, 
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode($secret . ':')
                    )
                );
                $response = wp_remote_request('https://api.stripe.com/v1/payment_intents/' . $originalId . '/cancel', $args);
                
            } else if ($status == 'succeeded') {
                
                if (preg_match('/^pi_/', $originalId)) {
                    
                    $params = array('payment_intent' => $originalId);
                    
                } else {
                    
                    $params = array('charge' => $originalId);
                    
                }
                
                $args = array(
                    'method' => 'POST',
                    'timeout' => $this->request_timeout, 
                    'body' => $params,
                    'headers' => array(
                        'Authorization' => 'Basic ' . base64_encode($secret . ':')
                    )
                );
                $response = wp_remote_request('https://api.stripe.com/v1/refunds', $args);
                
            }
            
            if (!is_wp_error($response) && $response !== null) {
                
                $httpCode = wp_remote_retrieve_response_code($response);
                $responseBody = json_decode(wp_remote_retrieve_body($response), true);
                
                if ($httpCode < 400) {
                    
                    $success_statuses = array('succeeded', 'canceled', 'pending');
                    if (isset($responseBody['id']) && isset($responseBody['status']) && in_array($responseBody['status'], $success_statuses)) {
                        
                        $message['cardToken'] = $responseBody['id'];
                        
                    } else {
                        
                        $message = $this->httpCodeError($httpCode, $payId);
                        
                    }
                    
                } else {
                    
                    $message = $this->httpCodeError($httpCode, $payId);
                    
                }
                
            } else {
                $message = $this->httpCodeError(500, $payId);
            }
            
            return $message;
        }
        
        public function cancelPayPal($payId, $secret, $chargeId){
            
            if (has_filter('booking_package_creditcard_cancel_paypal')) {
                return apply_filters('booking_package_creditcard_cancel_paypal', $payId, $secret, $chargeId);
            }

        }
        
        public function httpCodeError($httpCode, $payId){
            
            if (has_filter('booking_package_creditcard_http_code_error')) {
                return apply_filters('booking_package_creditcard_http_code_error', $httpCode, $payId);
            }

            $message = array('code' => $httpCode);
            if($httpCode == 400){
                $message['error'] = "The request was unacceptable, often due to missing a required parameter.";
            }else if($httpCode == 401){
                $message['error'] = "No valid API key provided.";
            }else if($httpCode == 402){
                $message['error'] = "The parameters were valid but the request failed.";
            }else if($httpCode == 404){
                $message['error'] = "The requested resource doesn't exist.";
            }else if($httpCode == 409){
                $message['error'] = "The request conflicts with another request (perhaps due to using the same idempotent key).";
            }else if($httpCode == 429){
                $message['error'] = "Too many requests hit the API too quickly. We recommend an exponential backoff of your requests.";
            }else{
                $message['error'] = "Something went wrong on Stripe's end.";
            }
            
            return $message;
            
        }
        
    }
?>