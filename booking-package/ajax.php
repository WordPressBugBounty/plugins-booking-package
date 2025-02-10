<?php
    
    /** Load WordPress Bootstrap */
	require_once dirname( __DIR__, 3 ) . '/wp-load.php';
	
    define('WP_USE_THEMES', false);
	
    class BOOKING_PACKAGE_AJAX {
        
        public $plugin_name = 'booking-package';
		
		public $prefix = 'booking_package_';
		
		public $action_public = 'package_app_public_action';
		
		public $userRoleName = 'booking_package_' . 'member';
		
		public $timezone = 'UTC';
		
		public $currencies = array();
        
        public $schedule = null;
		
		private $setting = null;
		
		public $nonce = null;
		
		public $ajaxUrl = 'ajax';
		
		public $ajaxNonceFunction = 'check_ajax_referer';
        
        public function __construct() {
            
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Setting.php');
			require_once(plugin_dir_path( __FILE__ ) . 'lib/Schedule.php');
			require_once(plugin_dir_path( __FILE__ ) . 'lib/CreditCard.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Html.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Database.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Schedule.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Ical.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Webhook.php');
            require_once(plugin_dir_path( __FILE__ ) . 'lib/Nonce.php');
            
            global $wpdb;
            $this->ajaxUrl = get_option($this->prefix . 'ajax_url', 'ajax');
			$this->ajaxNonceFunction = get_option($this->prefix . 'ajax_nonce_function', 'custom_nonce_validation');
            #$this->schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->userRoleName); 
            $this->setting = new booking_package_setting($this->prefix, $this->plugin_name, $this->userRoleName);
            $this->currencies = $this->setting->getCurrencies();
            $this->nonce = new booking_package_nonce($this->prefix, $this->plugin_name, $this->ajaxNonceFunction);
            
            /** Allow for cross-domain requests (from the front end). */
			send_origin_headers();
			
			header('Content-Type: text/html; charset=' . get_option('blog_charset'));
			header('X-Robots-Tag: noindex');
			
			// Require a valid action parameter.
			if (empty($_REQUEST['action']) || !is_scalar($_REQUEST['action'])) {
				wp_die('0', 400);
			}
			
			/** Load WordPress Administration APIs */
			#require_once ABSPATH . 'wp-admin/includes/admin.php';
			
			/** Load Ajax Handlers for WordPress Core */
			#require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
			
			send_nosniff_header();
			nocache_headers();
            
			$textdomain = load_plugin_textdomain($this->plugin_name, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
            
        }
        
        public function wp_ajax_booking_package_for_public() {
        	
        	$_POST['mode'] = sanitize_text_field( esc_html($_POST['mode']) );
			$_POST['booking_package_nonce'] = sanitize_text_field( esc_html($_POST['booking_package_nonce']) );
			$verify_nonce = false;
			if ($this->ajaxNonceFunction === 'check_ajax_referer' && isset($_POST['booking_package_nonce']) === true) {
				
				$verify_nonce = check_ajax_referer($this->action_public . "_ajax", 'booking_package_nonce', false);
				
			} else if ($this->ajaxNonceFunction === 'wp_verify_nonce' && isset($_POST['booking_package_nonce']) === true) {
				
				$verify_nonce = wp_verify_nonce($_POST['booking_package_nonce'], $this->action_public . "_ajax");
				
			}  else if ($this->ajaxNonceFunction === 'custom_nonce_validation' && isset($_POST['booking_package_nonce']) === true) {
				
				if ($this->nonce->verify($_POST['booking_package_nonce'], $this->action_public . "_ajax") === true) {
					
					$verify_nonce = 1;
					
				}
				
			}
			
			if (intval($verify_nonce) === 1 || intval($verify_nonce) === 2) {
				
				$setting = $this->setting;
				$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
				
				date_default_timezone_set($this->getTimeZone());
				if (isset($_POST['accountKey'])) {
					
					$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
					if (isset($calendarAccount['timezone']) && $calendarAccount['timezone'] != 'none') {
						
						if (date_default_timezone_set($calendarAccount['timezone'])) {
							
							$this->timezone = $calendarAccount['timezone'];
							
						}
						
					}
					
				}
				
				$response = $schedule->requestAjaxFrontEnd($this->prefix);
				print json_encode($response);
				
			} else {
				
				print json_encode(array('status' => 'error', 'mode' => $_POST['mode'], "message" => __("The nonce has been invalidated. Please reload the page.", 'booking-package'), "received_nonce" => $_POST['booking_package_nonce'], "verify_nonce" => $verify_nonce));
				
			}
			
			die();
			
		}
		
		public function getTimeZone() {
			
			$timezone = get_option($this->prefix . "timezone", null);
			if (is_null($timezone)) {
				
				$timezone = get_option('timezone_string', 'UTC');
				if (is_null($timezone) || strlen($timezone) == 0) {
					
					$timezone = 'UTC';
					
				}
				
				add_option($this->prefix . "timezone", sanitize_text_field($timezone));
				
			}
			$this->timezone = $timezone;
			return $timezone;
			
		}
        
    }
    
    $booking_package_ajax = new BOOKING_PACKAGE_AJAX();
    $booking_package_ajax->wp_ajax_booking_package_for_public();
    
?>