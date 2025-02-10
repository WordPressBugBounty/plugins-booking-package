<?php

    if (!defined('ABSPATH')) {
    	exit;
	}
    
    class booking_package_nonce {
        
        private $session_id = null;
        
        public function __construct($prefix, $pluginName, $ajaxNonceFunction) {
            
            if ($ajaxNonceFunction !== 'custom_nonce_validation') {
                
                return false;
                
            }
            
            if (session_status() == PHP_SESSION_NONE) {
                
                session_start();
                session_write_close();
                
            }
            
            $this->session_id = session_id();
            if (empty($this->session_id) === true) {
                
                $this->session_id = get_current_user_id();
                
            }
            
        }
        
        public function create($action) {
            
            $uniqid = uniqid();
            $unique = get_site_url();
            $timestamp = time();
            #$nonce = hash_hmac('sha256', $action . get_current_user_id() . $timestamp, $secret_key) . '|' . $timestamp;
            $nonce = wp_hash($action . $this->session_id . $timestamp, $unique) . '|' . $timestamp;
            return $nonce;
            
        }
        
        public function verify($nonce, $action) {
            
            $unique = get_site_url();
            list($hash, $timestamp) = explode('|', $nonce);
            $expiration = 1440 * 60;
            if ((time() - $timestamp) > $expiration) {
                
                return false;
                
            }
            
            #$expected_hash = hash_hmac('sha256', $action . get_current_user_id() . $timestamp, 'your_secret_key');
            $expected_hash = wp_hash($action . $this->session_id . $timestamp, $unique);
            return hash_equals($expected_hash, $hash);
            
        }
        
    }
    
    
?>