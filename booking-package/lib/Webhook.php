<?php
    if(!defined('ABSPATH')){
    	exit;
	}
	
	class booking_package_webhook {
        
        public $prefix = null;
        
        public $pluginName = null;
        
        public $setting = null;
        
        public $schedule = null;
        
        public function __construct($prefix, $pluginName) {
            
            $this->prefix = $prefix;
            $this->pluginName = $pluginName;
            $this->setting = new booking_package_setting($prefix, $pluginName);
            $this->schedule = new booking_package_schedule($prefix, $pluginName, null);
            
        }
        
        public function catchWebhook($target, $server, $post, $body = false){
            
        }
        
        public function prepareCourses($time, $accountKey){
            
        }
        
        public function prepareFrom($mode, $id, $name, $address, $description, $form){
            
        }
        
        public function prepareValues($values, $isKey, $count, $form){
        	
        }
        
        
    }

?>