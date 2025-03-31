<?php
/*
Plugin Name: Booking Package SAASPROJECT
Plugin URI:  https://saasproject.net/plans/
Description: Booking Package is a high-performance booking calendar system that anyone can easily use.
Version:     1.6.76
Author:      SAASPROJECT Booking Package
Author URI:  https://saasproject.net/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: booking-package
Domain Path: /languages
*/
	
	if (!defined('ABSPATH')) {
		
		exit;
		
	}
	
	define("BOOKING_PACKAGE_EXTENSION_URL", "https://saasproject.net/api/1.7/");
	
	define('BOOKING_PACKAGE_UPGRRADE_URL', "https://saasproject.net/upgrade/");
	
	define('BOOKING_PACKAGE_MY_SUBSCRIPTION_URL', "https://saasproject.net/update-subscription/");
	
	define("BOOKING_PACKAGE_MAX_DEADLINE_TIME", 1440);
	
	class BOOKING_PACKAGE {
		
		public $db_version = "1.1.9";
		
		public $plugin_version = 0;
		
		public $table_name = null;
		
		public $plugin_name = 'booking-package';
		
		public $prefix = 'booking_package_';
		
		public $action_control = 'package_app_action';
		
		public $action_public = 'package_app_public_action';
		
		public $userRoleName = 'booking_package_' . 'member';
		
		public $locale = 'en';
		
		public $pluginLocale = null;
		
		public $timezone = 'UTC';
		
		public $loaded_plugin = false;
		
		public $customizeClass = '';
		
		public $calendarScript = null;
		
		public $is_owner_site = 1;
		
		public $is_mobile = 0;
		
		public $shortcodes = 0;
		
		public $widget = false;
		
		public $schedule = null;
		
		private $setting = null;
		
		private $isExtensionsValid = null;
		
		private $front_end_js = false;
		
		public $currencies = array();
		
		public $visitorSubscriptionForStripe = 0;
		
		public $groupOfInputField = 0;
		
		public $siteNetwork = 0;
		
		public $nonce = null;
		
		public $ajaxUrl = 'ajax';
		
		public $ajaxNonceFunction = 'custom_nonce_validation';
		
		public $dubug_javascript = 0;
		
		public $multipleRooms = 0;
		
		public $expirationDateForTax = 0;
		
		public $maxAndMinNumberOfGuests = 0;
		
		public $stopService = 0;
		
		public $guestForDayOfTheWeekRates = 0;
		
		public $servicesExcludedGuestsInEmail = 0;
		
		public $newTaxesAndExtraCharges = 0;
		
		public $errorNumberOfCustomers = 0;
		
		public $messagingApp = 0;
		
		public $enabledStaff = 0;
		
		public $themes = 0;
		
		public $customizeLayouts = 0;
		
		public $maxBookingSlotsPerDay = 0;
		
		public $managementUsersV2 = 0;
		
		public function __construct($shortcodes = 0, $widget = false) {
			
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
            $this->setting = new booking_package_setting($this->prefix, $this->plugin_name, $this->userRoleName);
            $this->setting->setMessagingApp($this->messagingApp);
            $this->setting->setMaxBookingSlotsPerDay($this->maxBookingSlotsPerDay);
            $this->currencies = $this->setting->getCurrencies();
            $this->nonce = new booking_package_nonce($this->prefix, $this->plugin_name, $this->ajaxNonceFunction);
            $this->widget = $widget;
            #$this->schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName); 
			if ($shortcodes > 0) {
				
				$this->shortcodes = $shortcodes;
				
			}
			
			$monitorNumberOfShortcode = get_option($this->prefix . "monitorNumberOfShortcode", 1);
			if (intval($monitorNumberOfShortcode) == 1) {
				
				$this->shortcodes++;
				
			}
			
			$this->timezone = get_option('timezone_string', '');
			if (is_null($this->timezone) || strlen($this->timezone) == 0) {
				
				$this->timezone = 'UTC';
				
			}
			
			$plugin_headers = get_file_data(__FILE__, array('version' => 'Version', 'Page Name' => 'Page Name'));
			$this->plugin_version = $plugin_headers['version'];
			
			if (function_exists('register_activation_hook')) {
				
				register_activation_hook(__FILE__, array($this,'register_activation_hook'));
				
			}
			
			if (wp_get_schedule('booking_package_notification') === false) {
				
				$unixTime = date('U') + 60 * 60;
				$timeStamp = mktime(date('H', $unixTime), 0, 0, date('m', $unixTime), date('d', $unixTime), date('Y', $unixTime));
				wp_schedule_event($timeStamp, 'hourly', 'booking_package_notification');
				
			} else {
				
				$unixTime = wp_next_scheduled('booking_package_notification');
				if (intval(date('i', $unixTime)) > 0) {
					
					wp_clear_scheduled_hook('booking_package_notification');
					$timeStamp = mktime(date('H', $unixTime), 0, 0, date('m', $unixTime), date('d', $unixTime), date('Y', $unixTime));
					wp_schedule_event($timeStamp, 'hourly', 'booking_package_notification');
					
				}
				
			}
			
			if (function_exists('register_deactivation_hook')) {
				
				register_deactivation_hook(__FILE__, array($this, 'deactivation_event'));
				
			}
			
			if (isset($_GET['key']) && isset($_GET['calendar']) && isset($_GET['month']) && isset($_GET['day']) && isset($_GET['year'])) {
				
				$expire = date('U') + (30 * 24 * 3600);
				setcookie($this->prefix.'accountKey', intval($_GET['calendar']), $expire);
				
			}
			
			add_filter( 'locale', array($this, 'plugin_localized'));
			add_filter( 'load_textdomain_mofile', array($this, 'plugin_textdomain'), 10, 2);
			
			$textdomain = load_plugin_textdomain($this->plugin_name, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
			
			add_action('booking_package_notification', array($this, 'do_booking_notification'));
			add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
			add_action('admin_menu', array($this, 'add_pages'));
			add_action('profile_update', array($this, 'update_user_profile'));
			add_action('personal_options_update', array($this, 'update_user_profile'));
			add_action('user_register', array($this, 'regist_user'));
			add_action('delete_user', array($this, 'delete_user'));
			add_action('wp_before_admin_bar_render', array($this, 'admin_toolbar'));
			
			add_action('admin_bar_menu', array($this, 'admin_bar_menu'), 100);
			add_action('wp_ajax_'.$this->action_control, array($this, 'wp_ajax_booking_package'));
			add_action('wp_ajax_nopriv_'.$this->action_control, array($this, 'wp_ajax_booking_package'));
			
			if ($this->ajaxUrl === 'admin-ajax') {
				
				add_action('wp_ajax_'.$this->action_public, array($this, 'wp_ajax_booking_package_for_public'));
				add_action('wp_ajax_nopriv_'.$this->action_public, array($this, 'wp_ajax_booking_package_for_public'));
				
			}
			
			add_action('widgets_init', array($this, 'register_widget'));
			//add_action('load-plugins.php', array($this, 'load_plugins'));
			add_action('login_enqueue_scripts', array($this, 'login_enqueue_scripts'));
			add_action('init', array($this, 'session_start'));
			add_filter('login_headerurl', array($this, 'login_headerurl'));
			add_filter('login_headertext', array($this, 'login_headertext'));
			add_action('wp_print_footer_scripts', array($this, 'add_footer_scripts'), 5);
			
			if (is_admin() === false) {
				
				add_shortcode('booking_package', array($this, 'booking_package_front_end'));
				
			}
			
			add_filter('widget_text', 'do_shortcode');
			#add_filter('login_errors', array($this, 'login_errors'));
			
			if (function_exists('wp_insert_site')) {
				
				add_action('wp_insert_site', array($this, 'wp_insert_site'));
				
			} else {
				
				add_action('wpmu_new_blog', array($this, 'wpmu_new_blog'), 10, 6);
				
			}
			
			if (function_exists('wp_delete_site')) {
				
				add_action('wp_delete_site', array($this, 'wp_delete_site'));
				
			} else {
				
				add_action('delete_blog', array($this, 'delete_blog'), 10, 6);
				
			}
			
			if (isset($_GET['ical'])) {
				
				add_action('init', array($this, 'ical_feeds'));
				
			}
			
			if ($this->ajaxUrl === 'top') {
				
				if (isset($_POST['plugin_name']) && $_POST['plugin_name'] === $this->plugin_name && isset($_POST['action']) && $_POST['action'] === $this->action_public) {
					
					send_origin_headers();
					header('Content-Type: text/html; charset=' . get_option('blog_charset'));
					header('X-Robots-Tag: noindex');
					
					if (empty( $_REQUEST['action']) || !is_scalar($_REQUEST['action'])) {
						wp_die('0', 400);
					}
					
					send_nosniff_header();
					nocache_headers();
					
					add_action('init', array($this, 'wp_ajax_booking_package_for_public'));
					
				}
				
			}
			
			if (isset($_POST['booking_package_heartbeat'])) {
				
				add_action('init', array($this, 'heartbeat'));
				
			}
			
			if (isset($_POST['mode']) && $_POST['mode'] == 'booking-package-activate-subscription') {
				
				add_action('init', array($this, 'activatePaidSubscription'));
				
			}
			
			if (isset($_GET['mode']) && isset($_GET['unique']) && $_GET['mode'] == 'booking-package-update-paid-subscription') {
				
				add_action('init', array($this, 'updatePaidSubscription'));
				
			}
			
			if (isset($_POST['mode']) && $_POST['mode'] == 'booking_package_getDownloadCSV') {
				
				add_action('admin_init', array($this, 'getDownloadCSV'));
				
			}
			
		}
		
		public function session_start() {
			
			/**
			if (session_status() == PHP_SESSION_NONE) {
                
                session_start();
                session_write_close();
                
            }
            **/
			
		}
		
		public function plugin_localized($locale) {
			
			if (isset($_POST['locale'])) {
				
				$this->locale = sanitize_text_field( esc_html($_POST['locale']) );
				return $this->locale;
				
			}
			
			if (isset($_GET['locale'])) {
				
				$this->locale = sanitize_text_field( esc_html($_GET['locale']) );
				return $this->locale;
				
			}
			
			$this->locale = sanitize_text_field( esc_html($locale) );
			return $this->locale;
			
		}
		
		public function plugin_textdomain($mofile, $domain) {
			
			$locale = apply_filters('plugin_locale', determine_locale(), $domain);
			if ($this->plugin_name == $domain) {
				
				$this->pluginLocale = $locale;
				
			}
			
			return $mofile;
			
		}
		
		public function register_activation_hook() {
			
			$this->create_database();
			
		}
		/**
		public function booking_package_notification() {
			
			#wp_schedule_event(time(), 'hourly', 'booking_package_notification');
			
		}
		**/
		public function do_booking_notification() {
			
			$setting = $this->setting;
			$database = new booking_package_database($this->prefix, $this->db_version);
			$queries = $database->create();
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $isExtensionsValid = $this->getExtensionsValid(false, true);
            if ($isExtensionsValid === true) {
				
				#$this->update_database();
				$calendarAccounts = $schedule->booking_notification();
				$schedule->deleteCustomers();
				
            }
			
		}
		
		public function create_database($activation = true){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			add_option($this->prefix."javascriptSyntaxErrorNotification", 1);
			$database = new booking_package_database($this->prefix, $this->db_version);
			$queries = $database->create();
			if ($activation === true) {
				
				$setting = $this->setting;
				$setting->activation(BOOKING_PACKAGE_EXTENSION_URL, "activation", $this->plugin_version);
				$setting->setPaidSubscription('activation');
				
			}
			
			$this->createFirstCalendar();
			
		}
		
		public function upgrader_process(){
			
			$key = $this->prefix . "version";
			$now_version = get_option($key, 0);
			if ($now_version == 0) {
				
				add_option($key, $this->plugin_version);
				
			} else {
				
				if ($this->plugin_version != $now_version) {
					
					update_option($key, $this->plugin_version);
					$setting = $this->setting;
					$setting->activation(BOOKING_PACKAGE_EXTENSION_URL, "upgrader", $this->plugin_version);
					
				}
				
			}
			
		}
		
		public function update_database(){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$this->setHomePath();
			$this->update_memberAccount();
			$setting = $this->setting;
			$setting->setOpensslAlgo('AES-128-ECB');
			$setting->setSubscribeSite(false);
			$setting->setSubscribeSite(true);
			$installed_ver = get_option($this->prefix . "db_version");
			if (version_compare($this->db_version, $installed_ver, '>')) {
			#if ($installed_ver != $this->db_version) {
				
				$database = new booking_package_database($this->prefix, $this->db_version);
				$queries = $database->create();
				if (count($queries['tables']) === 0 && count($queries['columns']) === 0) {
					
					update_option($this->prefix."db_version", $this->db_version);
					if ($installed_ver < $this->db_version && $this->db_version == "0.1.7") {
						
						$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
						$schedule->changeMaxAccountScheduleDay();
						
					}
					
				}
				
				
			}
			
		}
		
		public function load_plugins() {
			
			$bool = true;
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				include_once(ABSPATH . 'wp-includes/ms-functions.php');
				if (is_plugin_active_for_network('booking-package/index.php') === true) {
					
					$bool = false;
					
				}
			
			}
			
			if ($bool === true) {
				
				$setting = $this->setting;
	            $isExtensionsValid = $this->getExtensionsValid(false, false);
				if ($isExtensionsValid === true) {
					
					$dictionary = array(
						'You currently have a valid subscription. Do you want to deactivate the Booking Package?' => __('You currently have a valid subscription. Do you want to deactivate the Booking Package?', 'booking-package'),
					);
					$localize_script = array(
						'url' => admin_url('admin-ajax.php'), 
						'action' => $this->action_control, 
						'nonce' => wp_create_nonce($this->action_control."_ajax"), 
						'prefix' => $this->prefix,
						'year' => date('Y'), 
						'month' => date('m'), 
						'day' => date('d'), 
						'isExtensionsValid' => $isExtensionsValid,
						'debug' => $this->dubug_javascript,
						'pluginName' => $this->plugin_name,
						'general_setting_url' => admin_url('admin.php?page=booking-package_setting_page&tab=subscriptionLink'), 
					);
					
					$p_v = "?p_v=".$this->plugin_version;
					wp_enqueue_style( 'Control.css', plugin_dir_url( __FILE__ ) . 'css/Control.css', array(), $this->plugin_version);
					wp_enqueue_style( 'Control_for_madia_css', plugin_dir_url( __FILE__ ) . 'css/Control_for_madia.css', array(), $this->plugin_version);
					$fontFaceStyle = $this->getFontFaceStyle();
		            wp_add_inline_style("Control.css", $fontFaceStyle);
					
					wp_enqueue_script( 'i18n_js', plugin_dir_url( __FILE__ ).'js/i18n.js'.$p_v);
					wp_enqueue_script('Delete_Plugin_js', plugin_dir_url( __FILE__ ) . 'js/Delete_plugin.js', array(), $this->plugin_version);
					wp_localize_script('Delete_Plugin_js', $this->prefix.'dictionary', $dictionary);
					wp_localize_script('Delete_Plugin_js', 'delete_plugin_data', $localize_script);
					
				}
				
			}
			
		}
		
		public function heartbeat() {
			
			if (intval($_POST['booking_package_heartbeat']) == 1) {
				
				$plugin_headers = get_file_data(__FILE__, array('version' => 'Version', 'Page Name' => 'Page Name'));
				$this->plugin_version = $plugin_headers['version'];
				$setting = $this->setting;
				$expiration = 0;
				$isExtensionsValid = $this->getExtensionsValid(false, false);
				if ($isExtensionsValid === true) {
					
					$subscription = $setting->upgradePlan('get');
					if ($subscription['expiration_date_for_subscriptions'] != 0) {
						
						$expiration = date('c', $subscription['expiration_date_for_subscriptions']);
						
					}
					
				}
				http_response_code(200) ;
				header('Content-type: text/json; charset=utf-8');
				$array = array('status' => true, 'isExtensionsValid' => $isExtensionsValid, 'expiration' => $expiration, 'pluginVersion' => $this->plugin_version, 'timestamp' => date('U'));
				echo json_encode($array);
				die();
				
			}
			
		}
		
		public function update_user_profile($user_id){
			
			#re_once(plugin_dir_path( __FILE__ ).'lib/Schedule.php');
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $schedule->update_email($user_id);

		}
		
		public function regist_user($user_id){
			
			$setting = $this->setting;
			$isExtensionsValid = $this->getExtensionsValid(false, false);
			$memberSetting = $setting->getMemberSetting($isExtensionsValid);
			$find = false;
			if (user_can($user_id, 'subscriber') === true && intval($memberSetting['accept_subscribers_as_users']['value']) == 1) {
				
				$find = true;

			} else if (user_can($user_id, 'contributor') === true && intval($memberSetting['accept_contributors_as_users']['value']) == 1) {
				
				$find = true;

			}/** else if (user_can($user_id, 'author') === true && intval($memberSetting['accept_authors_as_users']['value']) == 1) {
				
				$find = true;

			}**/
			
			if ($find === true) {
				
				$user = get_user_by('id', $user_id);
				$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
				$schedule->find_users($user_id, 1, true);
				
			}
			
		}
		
		public function delete_user($user_id){
			
			#echo plugin_dir_path( __FILE__ );
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $schedule->deleteForPluginUser($user_id);

		}
		
		public function login_enqueue_scripts() {
			
			if (isset($_GET['plugin']) && $_GET['plugin'] == 'booking-package') {
				
				$setting = $this->setting;
				$setting->getCss("front_end.css", plugin_dir_path( __FILE__ ));
				$front_end_url = $setting->getCssUrl("front_end.css");
	            wp_enqueue_style( 'front_end_url', $front_end_url['dirname'], array(), $front_end_url['v']);
	            
	            $isExtensionsValid = $this->getExtensionsValid(false, false);
				if ($isExtensionsValid === true) {
					
					$setting->getJavaScript("front_end.js", plugin_dir_path( __FILE__ ));
					$front_end_javascript_url = $setting->getJavaScriptUrl("front_end.js");
					wp_enqueue_script('front_end_javascript_url', $front_end_javascript_url['dirname'], array(), $front_end_javascript_url['v']);
					
				}
				
			}
			
		}
		
		public function login_headerurl() {
			
			if (isset($_GET['plugin']) && $_GET['plugin'] == 'booking-package') {
				
				return home_url();
				
			}
			
		}
		
		public function login_headertext() {
			
			if (isset($_GET['plugin']) && $_GET['plugin'] == 'booking-package') {
				
				return get_bloginfo('name');
				
			}
			
		}
		
		public function login_errors($error) {
			
			if (isset($_POST['redirect_to']) && isset($_POST['pluginName']) && $_POST['pluginName'] == 'booking-package') {
				
				global $errors;
				$err_codes = $errors->get_error_codes();
				if (in_array('invalid_email', $err_codes)) {
					
					$error = __('E-mail or password you entered is incorrect.', 'booking-package');
					
				} else if (in_array('invalid_username', $err_codes)) {
					
					$error = __('Username or password you entered is incorrect.', 'booking-package');
					
				} else if (in_array('incorrect_password', $err_codes)) {
					
					$error = __('Username or password you entered is incorrect.', 'booking-package');
					
				} else {
					
					$error = __('Unknown error.', 'booking-package');
					
				}
				
				$query = "?".$this->prefix."login_error=".$error;
	        	$redirect_to = $_POST['redirect_to'];
	        	$parse = parse_url($redirect_to);
	        	if (isset($parse['query'])) {
	        		
	        		$query = $parse['query']."&".$this->prefix."login_error=".$error;
	        		$redirect_to = $parse['scheme']."://".$parse['host'].$parse['path']."?".$query;
	        		
	        	} else {
	        		
	        		$redirect_to .= $query;
	        		
	        	}
	        	
	        	if (function_exists('urldecode')) {
	        		
	        		$redirect_to = urldecode($redirect_to);
	        		
	        	}
	        	
				header('Location: '.$redirect_to);
				die();
				
			}
			
			return $error;
		}
		
		public function admin_toolbar(){
			
			global $wp_admin_bar;
			#var_dump($wp_admin_bar);
			#print "test";
			
		}
		
		public function admin_bar_menu($wp_admin_bar){
			
			$displayMenu = false;
			$roles = array('manage_categories', $this->prefix . 'manager', $this->prefix . 'editor');
			for ($i = 0; $i < count($roles); $i++) {
				
				if (current_user_can($roles[$i]) === true) {
					
					$displayMenu = true;
					break;
					
				}
				
			}
			
			if ($displayMenu === true) {
				
				#wp_enqueue_style( 'Control.css', plugin_dir_url( __FILE__ ).'css/Control.css', array(), $this->plugin_version);
				$title = '<span class="top_toolbar_icon"></span><span>Booking Package</span>';
				$plugin_top_bar = $this->plugin_name.'_top_bar';
				$args = array(
					"id" => $plugin_top_bar,
					"meta" => array(), 
					'title' => $title,
					'href' => admin_url("admin.php?page=".$this->plugin_name."/index.php")
				);
				$wp_admin_bar->add_node($args);
				
				$args = array(
					"id" => $plugin_top_bar."_report",
					"parent" => $plugin_top_bar, 
					"meta" => array(), 
					/** 'title' => __('Report & Booking', $this->plugin_name), **/
					'title' => __('Booked Customers', 'booking-package'),
					'href' => admin_url("admin.php?page=".$this->plugin_name."/index.php")
				);
				$wp_admin_bar->add_node($args);
				
				$args = array(
					"id" => $plugin_top_bar."_members",
					"parent" => $plugin_top_bar, 
					"meta" => array(), 
					'title' => __('Users', 'booking-package'),
					'href' => admin_url("admin.php?page=".$this->plugin_name."_members_page")
				);
				$wp_admin_bar->add_node($args);
				
				if (current_user_can('manage_network') === true || current_user_can($this->prefix . 'editor') === false) {
					
					$args = array(
						"id" => $plugin_top_bar."_schedule",
						"parent" => $plugin_top_bar, 
						"meta" => array(), 
						'title' => __('Calendar Accounts', 'booking-package'),
						'href' => admin_url("admin.php?page=".$this->plugin_name."_schedule_page")
					);
					$wp_admin_bar->add_node($args);
					
					$args = array(
						"id" => $plugin_top_bar."_setting",
						"parent" => $plugin_top_bar, 
						"meta" => array(), 
						'title' => __('General Settings', 'booking-package'),
						'href' => admin_url("admin.php?page=".$this->plugin_name."_setting_page")
					);
					$wp_admin_bar->add_node($args);
					
				}
				
			}
			
			
		}
		
		public function register_widget(){
			
			register_widget('booking_package_widget');
			
		}
		
		public function add_pages() {
			
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$setting = $this->setting;
			$response = $schedule->get_user();
			if ($response['status'] == 1 && intval($response['user']['user_toolbar']) != 1) {
				
				$url = get_site_url();
				header('Location:' . $url);
				return null;
				
			}
			
			$schedule_page = null;
			$manager = $this->prefix . 'manager';
			$editor = $this->prefix . 'editor';
			$plugin_name = $this->plugin_name.'_admin';
			$manager_cap = 'manage_categories';
			$editor_cap = 'manage_categories';
			if (current_user_can('manage_categories') === true) {
				
				$manager_cap = 'manage_categories';
				$editor_cap = 'manage_categories';
				
				
			} else if (current_user_can($manager) === true && !is_null(get_role($manager))) {
				
				$manager_cap = $manager;
				$editor_cap = $manager;
				
			} else if (current_user_can($editor) === true && !is_null(get_role($editor))) {
				
				$manager_cap = 'manage_categories';
				$editor_cap = $editor;
				
			}
			
			add_menu_page($plugin_name, 'Booking Package', $editor_cap, __FILE__, array($this,'booking_package_booked_customers'), 'dashicons-calendar-alt', 26);
			add_submenu_page(__FILE__, $plugin_name, __('Booked Customers', 'booking-package'),  $editor_cap, __FILE__, array($this,'booking_package_booked_customers'));
			add_submenu_page(__FILE__, $plugin_name, __('Users', 'booking-package'),  $editor_cap, $this->plugin_name.'_members_page', array($this,'members_page'));
			$schedule_page = add_submenu_page(__FILE__, $plugin_name, __('Calendar Accounts', 'booking-package'),  $manager_cap, $this->plugin_name.'_schedule_page', array($this,'schedule_page'));
			add_submenu_page(__FILE__, $plugin_name, __('General Settings', 'booking-package'),  $manager_cap, $this->plugin_name.'_setting_page', array($this,'setting_page'));
			
			if (!is_null($schedule_page)) {
				
				add_action('load-'.$schedule_page, array($this, 'help_calendar_box'));
				
			}
			
		}
		
		public function add_dashboard_widget(){
			
			#var_dump(wp_get_current_user());
			#$userId = get_currentuserinfo();
			if(current_user_can("administrator") === true || current_user_can("editor") === true){
				
				wp_add_dashboard_widget($this->plugin_name, 'Booking Package', array($this, 'dashboard_widget_function'));
			
				global $wp_meta_boxes;
				$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
				$example_widget_backup = array($this->plugin_name => $normal_dashboard[$this->plugin_name]);
				unset($normal_dashboard[$this->plugin_name]);
				$sorted_dashboard = array_merge($example_widget_backup, $normal_dashboard);
				$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
				
			}
			
		}
		
		public function dashboard_widget_function(){
			
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$calendarAccountList = $schedule->getCalendarAccountListData("`key`, `name`, `type`, `status`, `timezone`");
			$newCalendarAccountList = array();
			for ($i = 0; $i < count($calendarAccountList); $i++) {
				
				$newCalendarAccountList[$calendarAccountList[$i]['key']] = $calendarAccountList[$i];
				
			}
			
			$calendarAccountList = $newCalendarAccountList;
			$setting = $this->setting;
			$list = $setting->getList();
			$emailMessageList = $setting->getEmailMessage(array('enable'));
			$dateFormat = get_option($this->prefix."dateFormat", "0");
			$positionOfWeek = get_option($this->prefix."positionOfWeek", "before");
			
			$list['General'][$this->prefix . 'clock']['value'] = $this->changeTimeFormat($list['General'][$this->prefix . 'clock']['value']);
			
			$dictionary = $this->getDictionary("booking_package_booked_customers", $this->plugin_name);
			$localize_script = array(
				'url' => admin_url('admin-ajax.php'), 
				'action' => $this->action_control, 
				'nonce' => wp_create_nonce($this->action_control."_ajax"), 
				'prefix' => $this->prefix,
				'courseBool' => 0, 
				'courseName' => "", 
				'year' => date('Y'), 
				'month' => date('m'), 
				'day' => date('d'), 
				'locale' => get_locale(),
				'courseList' => array(), 
				'currency' => 'usd',
				'dateFormat' => $dateFormat,
				'positionOfWeek' => $positionOfWeek,
				'emailEnable' => $emailMessageList,
				'bookingBool' => 0,
				'calendarAccountList' => $calendarAccountList,
				'error_url' => BOOKING_PACKAGE_EXTENSION_URL,
				'debug' => $this->dubug_javascript,
				'clock' => $list['General']['booking_package_clock']['value'],
			);
			
			$p_v = "?p_v=" . $this->plugin_version;
			wp_enqueue_style( 'Control.css', plugin_dir_url( __FILE__ ) . 'css/Control.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style( 'Control_for_madia_css', plugin_dir_url( __FILE__ ) . 'css/Control_for_madia.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'Error_js', plugin_dir_url( __FILE__ ) . 'js/Error.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'i18n_js', plugin_dir_url( __FILE__ ) . 'js/i18n.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'Confirm_js', plugin_dir_url( __FILE__ ) . 'js/Confirm.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'XMLHttp_js', plugin_dir_url( __FILE__ ) . 'js/XMLHttp.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'Calendar_js', plugin_dir_url( __FILE__ ) . 'js/Calendar.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script( 'Reservation_manage_js', plugin_dir_url( __FILE__ ) . 'js/Reservation_manage.js' . $p_v, array(), $this->plugin_version);
			wp_localize_script('Reservation_manage_js', 'schedule_data', $localize_script);
			wp_localize_script('Reservation_manage_js', $this->prefix . 'dictionary', $dictionary);
			
			print "<div id='booking_pacage_dashboard_widget'>";
			$dateFormat = intval(get_option($this->prefix . "dateFormat", 0));
			$positionOfWeek = get_option($this->prefix . "positionOfWeek", "before");
			$unixTime = date('U');
			$today = $schedule->dateFormat($dateFormat, $positionOfWeek, $unixTime, null, false, false, 'text');
			$this->customersList($today, $unixTime, $calendarAccountList, $schedule);
			
			$unixTime += 1440 * 60;
			$nextDay = $schedule->dateFormat($dateFormat, $positionOfWeek, $unixTime, null, false, false, 'text');
			$this->customersList($nextDay, $unixTime, $calendarAccountList, $schedule);
			
			print "</div>";
			
			?>
			<div id="blockPanel" class="edit_modal_backdrop hidden_panel"></div>
			<div id="dialogPanel" class="hidden_panel">
				<div class="blockPanel"></div>
				<div class="confirmPanel">
					<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
					<div class="body"><?php _e("Message", 'booking-package'); ?></div>
					<div class="buttonPanel">
						<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
						<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
					</div>
				</div>
			</div>
			<!--
			<div id="loadingPanel" class="loading_modal_backdrop hidden_panel"><img src="<?php print plugin_dir_url( __FILE__ ); ?>images/loading_0.gif"></div>
			-->
			<div id="loadingPanel" class="hidden_panel">
				<div class="loader">
					<svg viewBox="0 0 64 64" width="64" height="64">
						<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>
					</svg>
				</div>
			</div>
			<?php
		}
		
		public function customersList($date, $unixTime, $calendarAccountList, $schedule){
			
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$dateFormat = get_option($this->prefix . "dateFormat", "0");
			$clock = get_option($this->prefix . "clock", '24hours');
			$clock = $this->changeTimeFormat($clock);
			$url = admin_url("admin.php?page=" . $this->plugin_name . "/index.php");
			print "<div class='title'>" . $date . "</div>";
			foreach ((array) $calendarAccountList as $key => $calendarAccount) {
				
				if ($calendarAccount['status'] === 'open') {
					
					$list = $schedule->getReservationUsersData($calendarAccount, date('n', $unixTime), date('j', $unixTime), date('Y', $unixTime));
					if (empty($list) === false) {
						
						print "<div class='calendarName'>" . $calendarAccount['name'] . "</div>";
						print "<ul class=''>";
						for ($i = 0; $i < count($list); $i++) {
							
							$this->showCustomersList($list[$i], $url, $calendarAccount['type'], $schedule, $dateFormat, $clock);
							
						}
						print "</ul>";
						
					}
					
				}
				
			}
			
			date_default_timezone_set($this->getTimeZone());
			
		}
		
		public function showCustomersList($visitor, $url, $calendarType, $schedule, $dateFormat, $clock = '24hours'){
			
			$date = null;
			$visitor['date'] = array('month' => date('n', $visitor['scheduleUnixTime']), 'year' => date('Y', $visitor['scheduleUnixTime']));
			if ($calendarType == 'day') {
				
				$date = date('H:i', $visitor['scheduleUnixTime']) . ' ' . $visitor['courseName'];
				if ($clock == '12a.m.p.m') {
					
					$hour = intval(date('G', $visitor['scheduleUnixTime']));
					$print_am_pm = 'a.m.';
					if ($hour >= 12) {
						
						$print_am_pm = 'p.m.';
					
					}
					
					$date = sprintf(__('%s:%s ' . $print_am_pm, 'booking-package'), date('h', $visitor['scheduleUnixTime']), date('i', $visitor['scheduleUnixTime'])) . ' ' . $visitor['courseName'];

				} else if ($clock == '12ampm') {
					
					$hour = intval(date('G', $visitor['scheduleUnixTime']));
					$print_am_pm = 'am';
					if ($hour >= 12) {
						
						$print_am_pm = 'pm';
					
					}
					
					$date = sprintf(__('%s:%s ' . $print_am_pm, 'booking-package'), date('h', $visitor['scheduleUnixTime']), date('i', $visitor['scheduleUnixTime'])) . ' ' . $visitor['courseName'];

				} else if ($clock == '12AMPM') {
					
					$hour = intval(date('G', $visitor['scheduleUnixTime']));
					$print_am_pm = 'AM';
					if ($hour >= 12) {
						
						$print_am_pm = 'PM';
					
					}
					
					$date = sprintf(__('%s:%s ' . $print_am_pm, 'booking-package'), date('h', $visitor['scheduleUnixTime']), date('i', $visitor['scheduleUnixTime'])) . ' ' . $visitor['courseName'];

				}
				
			} else if ($calendarType == 'hotel') {
				
				$checkIn = $visitor['accommodationDetails']['checkIn'];
				$checkOut = $visitor['accommodationDetails']['checkOut'];
				$date = sprintf(__('Until %s', 'booking-package'), $schedule->dateFormat($dateFormat, date('w', $checkOut), $checkOut, null, false, true, 'text'));
			}
			
			$url .= "&key=" . intval($visitor['key']) . "&calendar=" . intval($visitor['accountKey']) . "&month=" . intval( date('n', $visitor['scheduleUnixTime']) ) . "&day=" . intval( date('j', $visitor['scheduleUnixTime']) ) . "&year=" . intval( date('Y', $visitor['scheduleUnixTime']) );
			$praivateData = $visitor['praivateData'];
			$name = array();
			for ($i = 0; $i < count($praivateData); $i++) {
				
				if (isset($praivateData[$i]['isName']) && $praivateData[$i]['isName'] == 'true') {
					
					array_push($name, $praivateData[$i]['value']);
					
				}
				
			}
			
			$name = strtoupper(implode(' ', $name));
			?>
			
			<li class=''>
				<div class='date'><?php print $date; ?></div>
				<div onClick='changeStatusForDashboard(this, <?php print $visitor['key']; ?>, "<?php print $visitor['cancellationToken']; ?>", <?php print $visitor['accountKey']; ?>, "<?php print $visitor['status']; ?>", <?php print $visitor['date']['month']; ?>, 1, <?php print $visitor['date']['year']; ?>)' class='status <?php print $visitor['status']; ?>'><?php print __(strtoupper($visitor['status']), 'booking-package'); ?></div>
				<div class='name'><a href='<?php print $url; ?>'><?php print $name; ?></a></div>
			</li>
			
			<?php
		}
		
		public function booking_package_front_end($atts) {
			
			if ($this->isSearchEngineBot() === true) {
				
				return '';
				
			}
			
			$this->loaded_plugin = true;
			$load_start_time = microtime(true);
			$atts = extract(shortcode_atts(array('id' => 1, 'locale' => null, 'services' => null, 'initial_month' => null, 'initial_year' => null), $atts, "booking_package"));
			$accountKey = $id;
			$this->upgrader_process();
			
			if (isset($services) && !empty($services)) {
				
				$_REQUEST['services'] = $services;
				
			}
			
			$initial_month = intval($initial_month);
			$initial_year = intval($initial_year);
			if ($initial_month === 0 || $initial_year === 0) {
				
				$initial_month = null;
				$initial_year = null;
				
			}
			
            $p_v = "?p_v=".$this->plugin_version;
            /**
            wp_enqueue_style( 'booking_app_js_css', plugin_dir_url( __FILE__ ).'css/Booking_app.css' . '?plugin_v=' . $this->plugin_version, array(), $this->plugin_version);
            wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
            **/
            
			$fontFaceStyle = $this->getFontFaceStyle();
            wp_add_inline_style("booking_app_js_css", $fontFaceStyle);
            
            $this->update_database();
            
            $setting = $this->setting;
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $isExtensionsValid = $this->getExtensionsValid(false, false);
            $calendarAccount = $schedule->getCalendarAccount($accountKey, $isExtensionsValid);
            if ($calendarAccount === false) {
            	
            	echo '<div class="calendarNotFound">';
            	echo 'The booking calendar was not found.';
            	echo '</div>';
            	return null;
            	
            }
            
			$deleteKeys = array("googleCalendarID", "idForGoogleWebhook", "expirationForGoogleWebhook", "ical", "icalToken", "email_from", "email_from_title", "email_to", "email_to_title");
			for ($i = 0; $i < count($deleteKeys); $i++) {
				
				if (!empty($deleteKeys[$i]) && isset($calendarAccount[$deleteKeys[$i]])) {
					
					unset($calendarAccount[$deleteKeys[$i]]);
					
				}
				
			}
			
			date_default_timezone_set($calendarAccount['timezone']);
			if (empty($calendarAccount['courseTitle'])) {
				
				$calendarAccount['courseTitle'] = __('Service', 'booking-package');
				
			}
			
            $htmlElement = new booking_package_HTMLElement($this->prefix, $this->plugin_name, $this->currencies);
            $htmlElement->setManagementUsersV2($this->managementUsersV2);
            
			$list = $setting->getList();
			
			$setting->getCss("front_end.css", plugin_dir_path( __FILE__ ));
			$front_end_url = $setting->getCssUrl("front_end.css");
            wp_enqueue_style( 'front_end_url', $front_end_url['dirname'], array(), $front_end_url['v']);
			
			if ($isExtensionsValid === true) {
				
				$this->front_end_js = true;
				/**
				$setting->getJavaScript("front_end.js", plugin_dir_path( __FILE__ ));
				$front_end_javascript_url = $setting->getJavaScriptUrl("front_end.js");
				wp_enqueue_script('front_end_javascript_url', $front_end_javascript_url['dirname'], array(), $front_end_javascript_url['v']);
				**/
				
			} else {
				
				$calendarAccount['hasMultipleServices'] = 0;
				$calendarAccount['maximumNights'] = 0;
				$calendarAccount['minimumNights'] = 0;
				$calendarAccount['bookingVerificationCode'] = 'false';
				$calendarAccount['bookingVerificationCodeToUser'] = 'false';
				$calendarAccount['insertConfirmedPage'] = '0';
				$calendarAccount['displayRemainingCapacity'] = '0';
				
			}
			
			$member_login_error = 0;
			$member_form = '';
			$wq_login_form = '';
			$wp_register = '';
			$userId = 0;
			if (isset($_GET['k']) && isset($_GET['u']) && isset($_GET['mode']) && $_GET['mode'] == 'activation') {
				
				$key = sanitize_text_field($_GET['k']);
				$user_login = sanitize_text_field($_GET['u']);
				$memberSetting['activation'] = $schedule->setActivationUser($key, $user_login, 1);
				
			}
			
			$user = $schedule->get_user();
			if (intval($user['status']) == 1) {
				
				$user = apply_filters('booking_package_login_user_on_front_end_page_as_filter', $user);
				$memberSetting = $user['user'];
				
			} else {
				
				$memberSetting = $user['user'];
				if (isset($user['user']['message'])) {
					
					$member_login_error = $user['user']['message'];
					
				}
				
			}
			do_action('booking_package_login_user_on_front_end_page_as_action', $user);
			
			if ($isExtensionsValid !== true) {
				
				$memberSetting['function_for_member'] = 0;
				
			}
			
			$cancellationOfBooking = 0;
			if (isset($_GET['bookingID']) && isset($_GET['bookingToken'])) {
				
				$bookingDetailsResponse = $schedule->getBookingDetailsOnVisitor($_GET['bookingID'], $_GET['bookingToken']);
				if ($bookingDetailsResponse['status'] == 'success') {
					
					$myBookingDetails = $bookingDetailsResponse['details'];
					$myBookingDetails['courseTitle'] = $calendarAccount['courseTitle'];
					unset($myBookingDetails['iCalUIDforGoogleCalendar']);
					unset($myBookingDetails['iCalIDforGoogleCalendar']);
					unset($myBookingDetails['resultOfGoogleCalendar']);
					unset($myBookingDetails['resultModeOfGoogleCalendar']);
					unset($myBookingDetails['payToken']);
					
					
					$verifyCancellation = $schedule->verifyCancellation($myBookingDetails, $isExtensionsValid, $user['status']);
					if ($verifyCancellation['cancel'] == true) {
						
						$cancellationOfBooking = 1;
						
					}
					
					$myBookingDetails['praivateData'] = array();
					
				}
				
			}
			
			
			#$memberSetting['subscription_list'] = array();
			$query = null;
			if (isset($_GET['bookingID']) === true && isset($_GET['bookingToken']) === true) {
				
				unset($_GET['bookingID']);
				unset($_GET['bookingToken']);
				$query = http_build_query($_GET);
				
			}
			
			$permalink = get_permalink();
			$urlQuery = parse_url($_SERVER['REQUEST_URI']);
			if (isset($urlQuery['query'])) {
				
				$parse_permalink = parse_url($permalink);
				if (isset($parse_permalink['query'])) {
					
					if (empty($query) === true) {
						
						#$permalink .= '&' . $urlQuery['query'];
						
					} else {
						
						$permalink .= '&' . $query;
						
					}
					
				} else {
					
					if (empty($query) === true) {
						
						#$permalink .= '?' . $urlQuery['query'];
						
					} else {
						
						$permalink .= '?' . $query;
						
					}
					
				}
				
			}
			#var_dump($permalink);
			
			$login_url = wp_login_url($permalink);
			$dictionary = $this->getDictionary("booking_package_front_end", $this->plugin_name);
			$dictionary = $this->updateDictionary($calendarAccount, $dictionary);
			$member_form = $this->member_form($memberSetting, $member_login_error, $dictionary);
			
			ob_start();
			wp_login_form(array('form_id' => 'booking-package-loginform', 'redirect' => $permalink));
			$wq_login_form = ob_get_contents();
			ob_get_clean();
			
			$formData = $setting->getForm($accountKey, true);
			$guestsList = $setting->getGuestsList($accountKey, true);
			$courseList = $setting->getCourseList($accountKey);
			$target_users = 'users';
			if (isset($user['status']) && intval($user['status']) == 1) {
				
				$target_users = 'visitors';
				
			}
			
			if (count($guestsList) == 0) {
				
				$calendarAccount['guestsBool'] = 0;
				
			}
			
			for ($i = 0; $i < count($formData); $i++) {
				
				if (isset($formData[$i]['targetCustomers'])) {
					
					if ($target_users == 'users' && $formData[$i]['targetCustomers'] == 'users') {
						
						$formData[$i]['active'] = '';
						
					} else if ($target_users == 'visitors' && $formData[$i]['targetCustomers'] == 'visitors') {
						
						$formData[$i]['active'] = '';
						
					}
					
				}
				
			}
			
			$hasServices = null;
			if (isset($_REQUEST['services']) && intval($calendarAccount['courseBool']) == 1) {
				
				$hasServices = explode(',', sanitize_text_field($_REQUEST['services']));
				if ($isExtensionsValid === false || intval($calendarAccount['hasMultipleServices']) == 0) {
					
					$hasServices = array($hasServices[0]);
					
				}
				
				for ($i = 0; $i < count($hasServices); $i++) {
					
					$hasServices[$i] = intval($hasServices[$i]);
					
				}
				
				if (wp_get_referer()) {
					
					$referer_url = parse_url(wp_get_referer());
					$home_url = parse_url(get_home_url());
					if ($referer_url['host'] == $home_url['host']) {
						
						$calendarAccount['refererURL'] = wp_get_referer();
						
					}
					
				}
				
			}
			
			$countHasServices = 0;
			$countServices = 0;
			foreach ((array) $courseList as $key => $service) {
				
				$courseList[$key]['directlySelected'] = 0;
				$courseList[$key]['directlyOptions'] = array();
				if ($service['target'] == $target_users) {
					
					unset($courseList[$key]);
					
				}
				
				if ($service['active'] == 'true') {
					
					$countServices++;
					
				}
				
				if (is_array($hasServices) === true) {
					
					$calendarAccount['flowOfBooking'] = 'services';
					$isService = array_search(intval($service['key']), $hasServices);
					if ($isService !== false && $courseList[$key]['active'] == 'true') {
						
						$countHasServices++;
						$courseList[$key]['directlySelected'] = 1;
						
					} else {
						
						$courseList[$key]['active'] = '';
						
					}
					
				}
				
				if ($service['stopServiceUnderFollowingConditions'] != 'doNotStop') {
					
					if ($isExtensionsValid === true) {
						
						$calendarAccount['hasMultipleServices'] = 0;
						
					} else {
						
						$service['stopServiceUnderFollowingConditions'] = 'doNotStop';
						
					}
					
				}
				
			}
			
			if (is_array($hasServices) === true && count($hasServices) != $countHasServices) {
				
				wp_die('<pre>' . sprintf(__('Not found services in the %s.', 'booking-package'), $calendarAccount['name']) . '</pre>');
				
			}
			
			if ($countServices == 0) {
				
				$calendarAccount['flowOfBooking'] = "calendar";
				
			}
			
			$schedule->deleteOldDaysInSchedules();
			$schedule->insertAccountSchedule(date('m'), date('d'), date('Y'), $accountKey);
			date_default_timezone_set($calendarAccount['timezone']);
			
			if ($calendarAccount['status'] == 'close' || $calendarAccount['status'] == 'closed') {
				
				return '<div id="calendarStatus">'.__('We do not accept reservations for this calendar.', 'booking-package').'</div>';
				
			}
			
			$dateFormat = get_option($this->prefix."dateFormat", "0");
			$positionOfWeek = get_option($this->prefix."positionOfWeek", "before");
			$courseBool = "false";
			if (intval($calendarAccount["courseBool"]) == 1) {
				
				$courseBool = "true";
				if ($countServices == 0 || $calendarAccount['flowOfBooking'] == 'services') {
					
					$courseBool = "false";
					
				}
				
			} else {
				
				$calendarAccount['flowOfBooking'] = "calendar";
				$calendarAccount['hasMultipleServices'] = 0;
				
			}
			
			if ($this->multipleRooms === 0) {
				
				$calendarAccount['multipleRooms'] = 0;
				
			}
			
			$locale = get_locale();
			$localize_script = $this->localizeScript("booking_package_front_end");
			$localize_script['uniqueID'] = $this->plugin_name . '-id-' . $accountKey;
			$localize_script['courseBool'] = $courseBool;
			$localize_script['courseName'] = $calendarAccount["courseTitle"];
			$localize_script['hasMultipleServices'] = $calendarAccount['hasMultipleServices'];
			$localize_script['accountKey'] = $accountKey;
			$localize_script['calendarAccount'] = $calendarAccount;
			$localize_script['courseList'] = $courseList;
			$localize_script['guestsList'] = $guestsList;
			$localize_script['formData'] = $formData;
			$localize_script['enableFixCalendar'] = intval($calendarAccount['enableFixCalendar']);
			$localize_script['memberSetting'] = $memberSetting;
			$localize_script['cancellationOfBooking'] = $cancellationOfBooking;
			$localize_script['permalink'] = $permalink;
			#$localize_script['isExtensionsValid'] = $isExtensionsValid;
			if ($isExtensionsValid === true) {
				
				$localize_script['isExtensionsValid'] = 1;
				
			} else {
				
				$localize_script['isExtensionsValid'] = 0;
				
			}
			
			
			if (intval($calendarAccount['enableFixCalendar']) == 1) {
				
				$localize_script['month'] = intval($calendarAccount['monthForFixCalendar']) + 1;
				$localize_script['year'] = intval($calendarAccount['yearForFixCalendar']);
				
			}
			
			if (isset($myBookingDetails)) {
				
				$localize_script['myBookingDetails'] = $myBookingDetails;
				
			}
			
			if ($initial_month !== null && $initial_year !== null) {
				
				$localize_script['month'] = $initial_month;
				$localize_script['year'] = $initial_year;
				
			}
			
			$localize_script['googleReCAPTCHA'] = array('status' => false, 'locked' => false);
			$googleReCAPTCHA_active = get_option($this->prefix."googleReCAPTCHA_active", "0");
			if (intval($googleReCAPTCHA_active) == 1) {
				
				$localize_script['googleReCAPTCHA'] = array(
					'v' => get_option($this->prefix."googleReCAPTCHA_version", "v2"), 
					'key' => get_option($this->prefix."googleReCAPTCHA_site_key", "")
				);
				
				if (empty($localize_script['googleReCAPTCHA']['key'])) {
					
					$localize_script['googleReCAPTCHA']['status'] = false;
					$localize_script['googleReCAPTCHA']['locked'] = false;
					
				} else {
					
					$localize_script['googleReCAPTCHA']['status'] = true;
					$localize_script['googleReCAPTCHA']['locked'] = false;
					
				}
				
			}
			
			$localize_script['hCaptcha'] = array('status' => false, 'locked' => false);
			$hCaptcha_active = get_option($this->prefix."hCaptcha_active", "0");
			if (intval($hCaptcha_active) == 1) {
				
				$localize_script['hCaptcha'] = array(
					'key' => get_option($this->prefix."hCaptcha_site_key", ""),
					'theme' => get_option($this->prefix."hCaptcha_Theme", "light"),
					'size' => get_option($this->prefix."hCaptcha_Size", "normal"),
				);
				
				if (empty($localize_script['hCaptcha']['key'])) {
					
					$localize_script['hCaptcha']['status'] = false;
					$localize_script['hCaptcha']['locked'] = false;
					
				} else {
					
					$localize_script['hCaptcha']['status'] = true;
					$localize_script['hCaptcha']['locked'] = true;
					
				}
				
			}
			
			if ($isExtensionsValid === true) {
				
				$localize_script['taxes'] = $setting->getTaxes($accountKey, 'yes');
				$userSubscriptions = $setting->upgradePlan('get');
				if (is_string($userSubscriptions['customer_id_for_subscriptions'])) {
					
					$localize_script['site_subscriptions'] = substr($userSubscriptions['customer_id_for_subscriptions'], -5);
					
				}
				
			} else {
				
				$localize_script['taxes'] = array();
				
			}
			
			if ($calendarAccount['type'] === 'hotel') {
				
				$localize_script['hotelOptions'] = $setting->getOptionsForHotel($calendarAccount['key'], true, true);
				
			}
			
			$postPages = array(
				'servicesPostPage' => array('key' => 'servicesPage', 'page' => null), 
				'calendarPostPage' => array('key' => 'calenarPage', 'page' => null), 
				'schedulesPostPage' => array('key' => 'schedulesPage', 'page' => null), 
				'visitorDetailsPostPage' => array('key' => 'visitorDetailsPage', 'page' => null), 
				'confirmPostPage' => array('key' => 'confirmDetailsPage', 'page' => null),
				'thanksPostPage' => array('key' => 'thanksPage', 'page' => null),
			);
			
			if (!isset($calendarAccount['confirmDetailsPage'])) {
				
				$calendarAccount['confirmDetailsPage'] = null;
				
			}
			
			foreach ((array) $postPages as $key => $value) {
				
				if (!is_null($calendarAccount[$value['key']])) {
					
					$page = get_pages(array('include' => intval($calendarAccount[$value['key']])));
					if (!empty($page)) {
						
						$postPages[$key]['page'] = $page[0]->post_content;
						
					}
					
				}
				
			}
			
			$localize_script['redirectPage'] = null;
			if ($calendarAccount['redirectMode'] == 'page' && !empty($calendarAccount['redirectPage'])) {
				
				$page = get_pages(array('include' => intval($calendarAccount['redirectPage'])));
				if (!empty($page)) {
					
					$localize_script['redirectPage'] = get_page_link($calendarAccount['redirectPage']);
					
				}
				
			} else if ($calendarAccount['redirectMode'] == 'url' && !empty($calendarAccount['redirectURL'])) {
				
				$localize_script['redirectPage'] = $calendarAccount['redirectURL'];
				
			}
			
			$accountList = $schedule->getCalendarAccountListData("`key`, `name`, `expressionsCheck`, `type`, `courseTitle`, `includeChildrenInRoom`, `numberOfPeopleInRoom`");
			$localize_script['calendarAccountList'] = $accountList;
			$paymentMethod = explode(",", $calendarAccount['paymentMethod']);
			if ((count($paymentMethod) == 1 && strlen($paymentMethod[0]) == 0) || $isExtensionsValid === false) {
				
				$paymentMethod = array('locally');
				
			}
			
			$new_paymentMethod = array();
			for ($i = 0; $i < count($paymentMethod); $i++) {
				
				if ($paymentMethod[$i] == 'stripe' || $paymentMethod[$i] == 'stripe_konbini') {
					
					$stripe_public_key = get_option($this->prefix."stripe_public_key", null);
					if (!empty($stripe_public_key)) {
						
						array_push($new_paymentMethod, $paymentMethod[$i]);
						$localize_script['stripe_active'] = 1;
						$localize_script['stripe_public_key'] = $stripe_public_key;
						wp_enqueue_script('stripe_checkout_v3_js', 'https://js.stripe.com/v3/');
						
					} else {
						
						$localize_script['stripe_active'] = 0;
						
					}
					
				} else if ($paymentMethod[$i] == 'paypal') {
					
					$paypal_public_key = get_option($this->prefix."paypal_client_id", null);
					if (!empty($paypal_public_key)) {
						
						$localePayPal = 'locale=en_US';
						if ($this->locale == 'ja') {
							
							$localePayPal = 'locale=ja_JP';
							
						} else {
							
							if (strlen($this->locale) == 5) {
								
								$localePayPal = 'locale=' . $this->locale;
								
							}
							
						}
						
						array_push($new_paymentMethod, $paymentMethod[$i]);
						$localize_script['paypal_active'] = 1;
						$localize_script['paypal_mode'] = intval(get_option($this->prefix."paypal_live", 0));
						$localize_script['paypal_client_id'] = $paypal_public_key;
						#wp_enqueue_script('paypal_checkout_v3_js', 'https://www.paypalobjects.com/api/checkout.js');
						wp_enqueue_script('paypal_checkout_v4_js', 'https://www.paypal.com/sdk/js?client-id=' . $paypal_public_key . '&currency=' .  strtoupper($localize_script['currency']) . '&intent=capture&' . $localePayPal, array(), null);
						
					} else {
						
						$localize_script['paypal_active'] = 0;
						
					}
					
				} else if ($paymentMethod[$i] == 'locally') {
					
					array_push($new_paymentMethod, $paymentMethod[$i]);
					
				}
				
			}
			
			if (count($new_paymentMethod) == 0) {
				
				$new_paymentMethod = array('locally');
				
			}
			
			$localize_script['paymentMethod'] = $new_paymentMethod;
			
			if (!empty($localize_script['googleAnalytics'])) {
				
				wp_enqueue_script($this->prefix . 'googleAnalytics', 'https://www.googletagmanager.com/gtag/js?id=' . $localize_script['googleAnalytics'], array(), $this->plugin_version);
				
			} else {
				
				unset($localize_script['googleAnalytics']);
				
			}
			
			$howdy = "";
			if (isset($memberSetting['user_login'])) {
				
				#$howdy = sprintf(__('Hello, %s', 'booking-package'), $memberSetting['user_login']);
				$howdy = sprintf($dictionary['Hello, %s'], $memberSetting['user_login']);
				
			}
			
			$widgetClass = '';
			if ($this->widget === true) {
				
				$widgetClass = ' booking_package_widget';
				
			}
			
			$nonce_error_message = __('The AJAX failed or the nonce verification failed. ', 'booking-package') . ' ';
			$nonce_error_message .= __('If you encounter this error repeatedly, it is possible that access is being blocked by a security-related plugin.', 'booking-package') . ' ';
			$nonce_error_message .= __('Please try changing the values of "%s" and "%s" in Booking Package > General Settings.', 'booking-package');
			$general_settings_link = __('General Settings', 'booking-package');
			if (current_user_can('administrator') || current_user_can('editor')) {
				
				$general_settings_link = '<a href="' . admin_url('admin.php') . '?page=booking-package_setting_page' . '">' . __('General Settings', 'booking-package') . '</a>';
				
			}
			
			$html = '<div id="booking-package-locale-' . $this->locale . '" class="start_booking_package' . $widgetClass . '" data-ID="' . $accountKey . '">';
			$html .= '<div id="booking_package_json_format_error_panel" class="hidden_panel"><p></p><p></p></div>';
			$html .= '<div id="booking_package_nonce_error_panel" class="booking_package_nonce_error hidden_panel"><p>' . sprintf($nonce_error_message, '<b>' . __('Select the URL for AJAX on the public page', 'booking-package') . '</b>', '<b>' . __('Select a function to validate the value of a nonce with AJAX on the public page', 'booking-package') . '</b>') . '</p></div>';
			$html .= '<div id="booking-package-id-' . $accountKey . '" class="">';
			#$html .= $wp_register;
			$html .= '<div id="booking-package-memberActionPanel" class="hidden_panel">';
			if (isset($_GET[$this->prefix.'login_error'])) {
				
				$html .= "<div id='" . $this->prefix . "login_error' class='login_error'>" . esc_html($_GET[$this->prefix.'login_error']) . "</div>";
				
			}
			$html .= '<div class="userTopButtonPanel">';
			$html .= '<label class="displayName">' . $howdy . '</label>';
			$html .= '<div id="booking-package-register" class="register">' . $dictionary['Create account'] . '</div>';
			$html .= '<div id="booking-package-login" class="login">' . $dictionary['Sign in'] . '</div>';
			$html .= '<div id="booking-package-logout" class="logout hidden_panel">' . $dictionary['Sign out'] . '</div>';
			$html .= '<div id="booking-package-edit" class="edit">' . $dictionary['Edit My Profile'] . '</div>';
			$html .= '<div id="booking-package-bookedHistory" class="edit">' . $dictionary['Booking history'] . '</div>';
			$html .= '<div id="booking-package-subscribed" class="edit">Subscribed items</div>';
			$html .= '</div>';
			$html .= $wq_login_form.$member_form . '</div>';
			$html .= $this->subscription_form($localize_script['calendarAccount'], $memberSetting);
			$html .= $htmlElement->myBookingHistory_panel($dictionary);
			$html .= $htmlElement->myBookingDetails_panel($dictionary);
			$html .= $htmlElement->cancelBookingDetailsForVisitor_panel($dictionary);
			
			$html .= '<div id="booking-package" class="booking-package">';
			
			$html .= '	<div id="' . $this->prefix . 'navigationPage" class="navigationPage">';
			$html .= '		<div id="' . $this->prefix . 'schedulesPostPage" class="hidden_panel">' . $postPages['schedulesPostPage']['page'] . '</div>';
			$html .= '		<div id="' . $this->prefix . 'calendarPostPage" class="hidden_panel">' . $postPages['calendarPostPage']['page'] . '</div>';
			$html .= '		<div id="' . $this->prefix . 'servicesPostPage" class="hidden_panel">' . $postPages['servicesPostPage']['page'] . '</div>';
			$html .= '		<div id="' . $this->prefix . 'visitorDetailsPostPage" class="hidden_panel">' . $postPages['visitorDetailsPostPage']['page'] . '</div>';
			$html .= '		<div id="' . $this->prefix . 'confirmPostPage" class="hidden_panel">' . $postPages['confirmPostPage']['page'] . '</div>';
			$html .= '		<div id="' . $this->prefix . 'thanksPostPage" class="hidden_panel">' . $postPages['thanksPostPage']['page'] . '</div>';
			$html .= '	</div>';
			$html .= '<div id="booking-package_servicePage" class="hidden_panel"><div id="booking-package_serviceTitle" class="title borderColor">' . $dictionary['Please select a service'] . '</div><div class="list borderColor"></div></div>';
			$html .= '<div id="booking-package_serviceDetails" class="hidden_panel"><div class="title borderColor">' . $dictionary['Service details'] . '</div><div class="list borderColor"></div></div>';
			#$html .= '<div id="bookingBlockPanel" class="hidden_panel"><img src="'.plugin_dir_url( __FILE__ ).'images/loading_0.gif"></img></div>';
			$html .= '<div id=""></div>';
			
			$html .= '	<div id="booking-package_calendarPage" class=""></div>';
			$html .= '	<div id="booking-package_durationStay" class="hidden_panel"></div>';
			$html .= '	<div id="booking-package_schedulePage" class="hidden_panel">';
			$html .= '		<div id="topPanel"></div>';
			$html .= '		<div id="daysListPanel"></div>';
			$html .= '		<div id="courseMainPanel"></div>';
			$html .= '		<div id="optionsMainPanel"></div>';
			$html .= '		<div id="scheduleMainPanel"></div>';
			$html .= '		<div id="blockPanel"></div>';
			$html .= '		<div id="bottomPanel">';
			$html .= '			<button id="returnToCalendarButton" class="return_button">' . $dictionary['Return'] . '</button>';
			$html .= '			<button id="returnToDayListButton" class="return_button hidden_panel">' . $dictionary['Return'] . '</button>';
			$html .= '			<button id="previous_available_day_button" class="hidden_panel"></button>';
			$html .= '			<button id="next_available_day_button" class="hidden_panel"></button>';
			$html .= '			<button id="nextButton" class="next_button hidden_panel">' . $dictionary['Next'] . '</button>';
			$html .= '		</div>';
			$html .= '	</div>';
			#$html .= '	<div id="booking-package_thanksPanel" class="hidden_panel"></div>';
			$html .= '	<div id="booking-package_inputFormPanel" class="hidden_panel"></div>';
			
			$html .= '</div>';
			
			$html .= '	<div id="booking_package_verificationCodePanel" class="hidden_panel">';
			$html .= '		<div id="booking_package_verificationCodeContent">';
			$html .= '			<span class="notifications">' . __('We sent a verification code to the following address.', 'booking-package') . '</span> ';
			$html .= '			<span class="address"></span>';
			$html .= '			<span>' . __('Please enter a verification code.', 'booking-package') . '</span>';
			$html .= '			<input type="text" maxlength="6" placeholder="123456" class="form_text">';
			$html .= '			<button class="booking_verification_button">' .$dictionary['Verify'] . '</button>';
			$html .= '		</div>';
			$html .= '	</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '	<div id="bookingBlockPanel" class="hidden_panel">';
			$html .= '		<div class="">';
			$html .= '			<div class="loader" class="hidden_panel">';
			$html .= '				<svg viewBox="0 0 64 64" width="64" height="64">';
			$html .= '					<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>';
			$html .= '				</svg>';
			$html .= '			</div>';
			$html .= '		</div>';
			$html .= '	</div>';
			if ($localize_script['googleReCAPTCHA']['status'] === true) {
				
				if ($localize_script['googleReCAPTCHA']['v'] == 'v2') {
					
					#$html .= '<script src="https://www.google.com/recaptcha/api.js?render=explicit" asyn defer></script>';
					wp_enqueue_script('recaptcha_v2_js', 'https://www.google.com/recaptcha/api.js?render=explicit', array(), null);
					
				} else if ($localize_script['googleReCAPTCHA']['v'] == 'v3') {
					
					#$html .= '<script src="https://www.google.com/recaptcha/api.js?render=' . $localize_script['googleReCAPTCHA']['key'] . '" asyn defer></script>';
					wp_enqueue_script('recaptcha_v3_js', 'https://www.google.com/recaptcha/api.js?render=' . $localize_script['googleReCAPTCHA']['key'], array(), null);
					
				}
				
			}
			
			if ($localize_script['hCaptcha']['status'] === true) {
				
				#$html .= '<script src="https://hcaptcha.com/1/api.js?render=explicit" asyn defer></script>';
				wp_enqueue_script('hcaptcha_js', 'https://hcaptcha.com/1/api.js?render=explicit', array(), null);
				
			}
			
			$pluginDate = date('U');
			$load_end_time = microtime(true) - $load_start_time;
			$html .= '<!-- Load time: ' . $load_end_time . ' -->';
			$html .= '<div data-key="Loading_time" data-value="' . $load_end_time . '" data-plugin-version="' . $this->plugin_version . '" style="display: none;"></div>';
			
			if (empty($this->calendarScript)) {
				
				$this->calendarScript = array('dictionary' => $dictionary, 'localize_script' => $localize_script, 'style' => $this->getStyle($calendarAccount, $list));
				
			}
			
			$this->set_class_selector_in_footer($calendarAccount);
			$html = apply_filters('booking_package_front_end_page', $html);
			
			return $html;
			
		}
		
		public function set_class_selector_in_footer($calendarAccount) {
			
			if (intval($calendarAccount['customizeButtonsBool']) === 1) {
				
				$customizeButtons = $calendarAccount['customizeButtons'];
				$id = $this->plugin_name . '-id-' .  $calendarAccount['key'];
				$customizeClass = '';
				foreach ($customizeButtons as $key => $classObject) {
					
					$customizeClass .= (function($id, $key, $classObject) {
						
						$class = '#' . $id . ' .' . $key . " {\n";
						foreach ($classObject as $name => $value) {
							
							$class .= "\t" . $name . ': ' . $value . ";\n";
							
						}
						$class .= "}\n";
						return $class;
						
					})($id, $key, $classObject);
					
				}
				
				$this->customizeClass .= $customizeClass;
				#var_dump($customizeClass);
			}
			
			if (intval($calendarAccount['customizeLayoutsBool']) === 1) {
				
				$styles = array(
					'booking-package-locale-' . $this->locale => array(),
					'booking-package_calendarPage' => array(
						'calendarHeader' => array('background-color', 'color'),
						'week_slot' => array('background-color', 'color', 'border-color'),
						'day_slot' => array('background-color', 'color', 'border-color'),
					),
					'booking-package_durationStay' => array(
						'bookingDetailsTitle' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					'booking-package_servicePage' => array(
						'title' => array('background-color', 'color', 'border-color'),
						'selectable_service_slot' => array('background-color', 'color', 'border-color'),
					),
					'booking-package_serviceDetails' => array(
						'title' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					'booking-package_schedulePage' => array(
						'topPanel' => array('background-color', 'color', 'border-color'),
						'selectable_day_slot' => array('background-color', 'color', 'border-color'),
						'selectable_service_slot' => array('background-color', 'color', 'border-color'),
						'selectable_time_slot' => array('background-color', 'color', 'border-color'),
						'selectPanelError' => array('background-color', 'color', 'border-color'),
						'bottomPanel' => array('background-color', 'color', 'border-color'),
						'bottomPanelForPositionInherit' => array('background-color', 'color', 'border-color'),
					),
					'booking-package_inputFormPanel' => array(
						'title_in_form' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					
					'booking-package-loginform' => array(
						'title_in_form' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					'booking-package-user-form' => array(
						'title_in_form' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					
					'booking-package-user-edit-form' => array(
						'title_in_form' => array('background-color', 'color', 'border-color'),
						'row' => array('background-color', 'color', 'border-color'),
					),
					
				);
				
				$customizeLayouts = $calendarAccount['customizeLayouts'];
				$customizeClass = '#booking-package-locale-' . $this->locale . " * {font-size: " . $customizeLayouts['general']['font-size'] . ";}\n";
				$customizeClass = '';
				
				foreach ($customizeLayouts as $id => $classNames) {
					
					if ($id === 'general') {
						
						foreach ($styles as $key => $classes) {
							
							
							$customizeClass .= (function($id, $classes, $classNames) {
								
								$className = '';
								$className = '#' . $id . " {\n";
								$styleNames = array('background-color', 'color', 'border-color', 'font-size');
								for ($i = 0; $i < count($styleNames); $i++) {
									
									$styleName = $styleNames[$i];
									$className .= "\t" . $styleName . ': ' . $classNames[$styleName] . ";\n";
									
								}
								$className .= "}\n";
								
								foreach ($classes as $key => $class) {
									
									$className .= '#' . $id . ' .' . $key . " {\n";
									for ($i = 0; $i < count($class); $i++) {
										
										$styleName = $class[$i];
										$className .= "\t" . $styleName . ': ' . $classNames[$styleName] . ";\n";
										
									}
									$className .= "}\n";
									
								}
								
								return $className;
								
							})($key, $classes, $classNames);
							
						}
						
					} else if ($id === 'calendar' || $id === 'bookingDetails' || $id === 'service' || $id === 'timeSlot' || $id == 'form') {
						
						$idName = array('booking-package_calendarPage');
						if ($id === 'calendar') {
							
							$idName = array('booking-package_calendarPage');
							
						} else if ($id === 'bookingDetails') {
							
							$idName = array('booking-package_durationStay', 'summaryListPanel');
							
						} else if ($id === 'service' /**|| $id === 'timeSlot'**/ ) {
							
							$idName = array('booking-package_schedulePage', 'booking-package_servicePage', 'booking-package_serviceDetails');
							
						} else if ($id === 'timeSlot') {
							
							$idName = array('scheduleMainPanel');
							
						} else if ($id == 'form') {
							
							$idName = array('booking-package_inputFormPanel', 'booking-package-user-form', 'booking-package-loginform', 'booking-package-user-edit-form');
							$customizeClass .= (function ($styles) {
								
								$styleLines = '';
								$ids = ['booking_package_verificationCodeContent', 'booking-package-loginform', 'booking-package-user-form', 'booking-package-user-edit-form', 'booking-package-loginform'];
								for ($i = 0; $i < count($ids); $i++) {
									
									$styleLines .= "#" . $ids[$i] . " .form_text {\n";
									foreach ($styles as $name => $value) {
										
										$styleLines .= "\t" . $name . ": " . $value . ";\n";
										
									}
									$styleLines .= "}\n";
									
								}
								
								return $styleLines;
								
							})($classNames['form_text']);
							
						}
						
						for ($i = 0; $i < count($idName); $i++) {
							
							foreach ($classNames as $name => $value) {
								
								$customizeClass .= '#' . $idName[$i] . ' .' . $name . " {\n";
								$customizeClass .= (function($styles) {
									
									$styleLines = '';
									foreach ($styles as $name => $value) {
										
										$styleLines .= "\t" . $name . ": " . $value . ";\n";
										
									}
									return $styleLines;
									
								})($value);
								$customizeClass .= "}\n";
								
							}
							
						}
						
					}
					
				}
				
				$this->customizeClass .= $customizeClass;
				#var_dump($customizeClass);
				
			}
			
		}
		
		public function add_footer_scripts() {
			
			if ($this->loaded_plugin === true && !is_admin()) {
				
				$setting = $this->setting;
				$pluginDate = date('U');
				$files = array(
					'booking-package-script-Error-js' => plugin_dir_url( __FILE__ ) . 'js/Error.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-i18n-js' =>  plugin_dir_url( __FILE__ ) . 'js/i18n.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-XMLHttp-js' =>  plugin_dir_url( __FILE__ ) . 'js/XMLHttp.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Input-js' =>  plugin_dir_url( __FILE__ ) . 'js/Input.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Calendar-js' =>  plugin_dir_url( __FILE__ ) . 'js/Calendar.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Hotel-js' =>  plugin_dir_url( __FILE__ ) . 'js/Hotel.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Member-js' =>  plugin_dir_url( __FILE__ ) . 'js/Member.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Booking_app-js' =>  plugin_dir_url( __FILE__ ) . 'js/Booking_app.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
					'booking-package-script-Reservation_manage-js' =>  plugin_dir_url( __FILE__ ) . 'js/Reservation_manage.js' . '?ver=' . $this->plugin_version . '&date=' . $pluginDate,
				);
				
				if ($this->front_end_js === true) {
					
					$ssl = 'https:';
					if (is_ssl() === false) {
						
						$ssl = 'http:';
						
					}
					$setting->getJavaScript("front_end.js", plugin_dir_path( __FILE__ ));
					$front_end_javascript_url = $setting->getJavaScriptUrl("front_end.js");
					$files['booking-package-script-front_end-js'] =  $ssl . $front_end_javascript_url['dirname'] . '?ver=' . $front_end_javascript_url['v'];
					
				}
				
				$load_script = file_get_contents(plugin_dir_path( __FILE__ ) . 'js/Load_plugin.js');
				$html = '';
				foreach ($files as $id => $file_path) {
					
					$html .= '<script id="' . $id . '" src="' . $file_path . '" asyn defer></script>';
					
				}
				
				$html .= '<link rel="stylesheet" id="booking_app_js_css" href="' . plugin_dir_url( __FILE__ ) . 'css/Booking_app.css' . '?plugin_v=' . $this->plugin_version . '" type="text/css" media="all">';
				$html .= '<link rel="stylesheet" id="Material_Icons" href="https://fonts.googleapis.com/css?family=Material+Icons" type="text/css" media="all">';
				#wp_enqueue_style( 'booking_app_js_css', plugin_dir_url( __FILE__ ).'css/Booking_app.css' . '?plugin_v=' . $this->plugin_version, array(), $this->plugin_version);
            	#wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
				#$fontFaceStyle = $this->getFontFaceStyle();
				$html .= '<style type="text/css">' . $this->customizeClass . "</style>\n";
				$html .= '<style type="text/css">' . $this->getFontFaceStyle() . "</style>\n";
				print $html;
				print $this->calendarScript['style'];
				print "<script id='booking_package_load_plugin'>\n";
				print "const textOfErrorContent = '" . __('You cannot insert multiple shortcodes of the Booking Package on a single page.', 'booking-package') . "';\n";
				print "const textOfErrorID = '" . __('The display of the calendar with ID %s has been canceled.', 'booking-package') . "';\n";
				print 'var ' . $this->prefix . 'dictionary = ' . json_encode($this->calendarScript['dictionary']) . ";\n";
				print 'var reservation_info = ' . json_encode($this->calendarScript['localize_script']) . ";\n";
				print 'const booking_package_js_files = ' . json_encode($files) . ";\n";
				print $load_script . "\n";
				print "</script>\n";
				
			}
			
		}
		
		public function subscription_form($calendarAccount, $subscription_form){
			
			$htmlElement = new booking_package_HTMLElement($this->prefix, $this->plugin_name, $this->currencies);
			$htmlElement->setManagementUsersV2($this->managementUsersV2);
			$html = $htmlElement->subscription_form($calendarAccount, $subscription_form);
			return $html;
			
		}
		
		public function member_form($user = null, $member_login_error = 0, $dictionary = null) {
			
			$htmlElement = new booking_package_HTMLElement($this->prefix, $this->plugin_name, $this->currencies);
			$htmlElement->setManagementUsersV2($this->managementUsersV2);
			$htmlElement->setVisitorSubscriptionForStripe($this->visitorSubscriptionForStripe);
			$member_form = $htmlElement->member_form($user, $member_login_error, $dictionary);
			return $member_form;
			
		}
		
		public function booking_package_booked_customers() {
			
			$load_start_time = microtime(true);
			global $wpdb;
            
			$this->update_database();
			$this->upgrader_process();
			$this->databaseUpdateErrors('page=booking-package%2Findex.php', false);
            
			$setting = $this->setting;
			#$timeMin = date('U') - (7 * 24 * 60 * 60);
			
            $webhook = new booking_package_webhook($this->prefix, $this->plugin_name);
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $schedule->deleteOldDaysInSchedules();
            $isExtensionsValid = $this->getExtensionsValid(true, false);
            $dictionary = $this->getDictionary("booking_package_booked_customers", $this->plugin_name);
            $localize_script = $this->localizeScript('booking_package_booked_customers');
            $localize_script['isExtensionsValid'] = 0;
            if ($isExtensionsValid === true) {
            	
            	$localize_script['isExtensionsValid'] = 1;
            	
            }
            $p_v = "?p_v=" . $this->plugin_version;
			wp_enqueue_style( 'Control.css', plugin_dir_url( __FILE__ ) . 'css/Control.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style( 'Control_for_madia_css', plugin_dir_url( __FILE__ ) . 'css/Control_for_madia.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
			$fontFaceStyle = $this->getFontFaceStyle();
            wp_add_inline_style("Control.css", $fontFaceStyle);
            wp_enqueue_script('Error_js', plugin_dir_url( __FILE__ ) . 'js/Error.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('i18n_js', plugin_dir_url( __FILE__ ) . 'js/i18n.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('XMLHttp_js', plugin_dir_url( __FILE__ ) . 'js/XMLHttp.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Confirm_js', plugin_dir_url( __FILE__ ) . 'js/Confirm.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Input_js', plugin_dir_url( __FILE__ ) . 'js/Input.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Calendar_js', plugin_dir_url( __FILE__ ) . 'js/Calendar.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Hotel_js', plugin_dir_url( __FILE__ ) . 'js/Hotel.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Reservation_manage_js', plugin_dir_url( __FILE__ ) . 'js/Reservation_manage.js' . $p_v, array(), $this->plugin_version);
			
			wp_localize_script('Reservation_manage_js', $this->prefix.'dictionary', $dictionary);
			wp_localize_script('Reservation_manage_js', 'schedule_data', $localize_script);
			wp_enqueue_script('Reservation_manage_js');
			
			
			$updated_style = "display: none;";
			$control_panel_button_style = "display: none;";
			$user = wp_get_current_user();
			if (!empty($_POST) && $user->allcaps['manage_options'] === true) {
				
				if (check_admin_referer('booking_package_action', 'booking_package_nonce_field')) {
					
					
					
				}
				
			}
			
			$active = get_option("booking_package_active", 0);
			$booking_package_id = get_option("booking_package_id", "");
			$booking_package_path = get_option("booking_package_path", "");
			$booking_package_script_path = get_option("booking_package_script_path", "");
			$booking_package_serial = get_option("booking_package_serial", "");
			
			if (!empty($booking_package_script_path)) {
				
				$control_panel_button_style = "";
				
			}
			
			$update_class = "";
			?>
			
			<style id="booking_pacage_booked_customers_style"></style>
			<div id='booking_pacage_booked_customers'>
				
				<div class="">
					<div class="top_bar">
						<?php
						#$this->upgradeButton($isExtensionsValid, true);
						?>
					</div>
					<div class="<?php print $update_class; ?>settings-error notice is-dismissible" id="res" style="<?php print $updated_style; ?>"></div>
					
					<div id="select_package" class="">
						<div id="calendarPage"></div>
					</div>
					
					<div id="editPanel" class="edit_modal hidden_panel">
						<button type="button" id="media_modal_close" class="media_modal_close">
							<span class="">
								<span class="material-icons">close</span>
							</span>
						</button>
						<div class="edit_modal_content">
							<div id="menu_panel" class="media_frame_menu">
								<div id="media_menu" class="media_menu"></div>
							</div>
							<div id="media_title" class="media_frame_title"><h1 id="edit_title"></h1></div>
							<div id="media_router" class="media_frame_router">
								<div class="reservation_table_row">
									<div id="reservation_users" class="media_menu_item active"><?php _e("Customers", 'booking-package'); ?></div>
									<div id="add_reservation" class="media_menu_item"><?php _e("Booking", 'booking-package'); ?></div>
								</div>
							</div>
							<div id="media_frame_reservation_content"></div>
							<div id="frame_toolbar" class="media_frame_toolbar">
								<div class="media_toolbar">
									<div id="buttonPanel" class="media_toolbar_primary" style="float: initial;">
										
										<div id="leftButtonPanel"></div>
										<div id="rightButtonPanel"></div>
										
									</div>
								</div>
							</div>
							
						</div>
						
					</div>
					
					<div id="blockPanel" class="edit_modal_backdrop hidden_panel"></div>
					
					<div id="dialogPanel" class="hidden_panel">
						<div class="blockPanel"></div>
						<div class="confirmPanel">
							<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
							<div class="body"><?php _e("Message", 'booking-package'); ?></div>
							<div class="buttonPanel">
								<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
								<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
							</div>
						</div>
					</div>
					
					<div id="selectOptionsPanel" class="hidden_panel">
						<div class="blockPanel"></div>
						<div class="confirmPanel">
							<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
							<div class="body"><?php _e("Message", 'booking-package'); ?></div>
							<div class="buttonPanel">
								<button type="button" class="decisionButton button media-button button-primary button-large media-button-insert"><?php _e("Decision", 'booking-package'); ?></button>
								<!--
								<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
								<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
								-->
							</div>
						</div>
					</div>
					
				<!-- /.wrap -->
				</div>	
				
				<div id="loadingPanel">
					<div class="loader">
						<svg viewBox="0 0 64 64" width="64" height="64">
							<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>
						</svg>
					</div>
				</div>
				
				<div id="lookForUserPanel" class="hidden_panel">
					
					<div>
						<div class="titlePanel">
							<div class="title">
								<!-- Choose a schedule -->
								<?php _e('Looking for users', 'booking-package'); ?>
							</div>
							<div id="lookForUserPanel_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel">
							
						</div>
						<div class="buttonPanel">
							<input id='search_users_text' class='serch_users_text' type='text'>
							<button id='search_user_button' class='w3tc-button-save button-primary serch_user_button'><?php _e('Search', 'booking-package'); ?></button>
							<!--
							<button id="selectionScheduleResetButton" class="media-button button-primary button-large media-button-insert deleteButton" style="margin-right: 1em;">Reset</button>
							<button id="selectionScheduleButton" class="media-button button-primary button-large media-button-insert">Apply</button>
							-->
						</div>
					</div>
					
				</div>
				<div id="load_blockPanel" style="z-index: 16000;" class="edit_modal_backdrop hidden_panel"></div>
				
			</div>
			<?php
			$list = $setting->getList();
			print '<style type="text/css">' . $this->getCustomizeStatus(true, $list['General']) . '</style>';
			$load_end_time = microtime(true) - $load_start_time;
			echo '<!-- Load time: ' . $load_end_time . ' -->';
			
		}
		
		public function members_page(){
			
			$load_start_time = microtime(true);
            date_default_timezone_set($this->getTimeZone());
			$this->update_database();
			$this->upgrader_process();
            $this->databaseUpdateErrors('page=booking-package_members_page', false);
            
			$p_v = "?p_v=".$this->plugin_version;
			$setting = $this->setting;
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $limit = get_option($this->prefix."read_member_limit", 10);
            #$limit = 1;
            $users = $schedule->get_users('users', 0, $limit, null);
			$localize_script = $this->localizeScript("member");
			$localize_script['action'] = $this->action_control;
			$localize_script['nonce'] = wp_create_nonce($this->action_control."_ajax");
			$localize_script['limit'] = $limit;
			
			$isExtensionsValid = $this->getExtensionsValid(true, false);
			if ($isExtensionsValid == true) {
				
				$localize_script['isExtensionsValid'] = 1;
				
			} else {
				
				$users = array();
				$localize_script['isExtensionsValid'] = 0;
				
			}
			
			$localize_script['userFunctions'] = $setting->getMemberSetting($isExtensionsValid);
			
			$accountList = $schedule->getCalendarAccountListData("`key`, `name`, `expressionsCheck`, `type`, `courseTitle`, `includeChildrenInRoom`, `numberOfPeopleInRoom`");
			$localize_script['calendarAccountList'] = $accountList;
			$emailEnableList = array();
			foreach ((array) $accountList as $key => $value) {
				
				$emailEnableList[intval($value['key'])] = $setting->getEmailMessageList($key);
				
			}
			$localize_script['emailEnableList'] = $emailEnableList;
			#$emailEnableList = $setting->getEmailMessageList($_POST['accountKey']);
			
			$memberSetting = $setting->getMemberSetting($isExtensionsValid);
			$swich_authority_by_hidden = "";
			if (intval($memberSetting['accept_subscribers_as_users']['value']) == 0 && intval($memberSetting['accept_contributors_as_users']['value']) == 0) {
				
				$swich_authority_by_hidden = " hidden_panel";
				
			}
			
			$dictionary = $this->getDictionary("user_management", $this->plugin_name);
			wp_enqueue_script('i18n_js', plugin_dir_url( __FILE__ ).'js/i18n.js'.$p_v);
			wp_enqueue_script('XMLHttp_js', plugin_dir_url( __FILE__ ).'js/XMLHttp.js'.$p_v);
			wp_enqueue_script('Error_js', plugin_dir_url( __FILE__ ).'js/Error.js'.$p_v);
			wp_enqueue_script('Calendar_js', plugin_dir_url( __FILE__ ).'js/Calendar.js'.$p_v);
			wp_enqueue_script('Input_js', plugin_dir_url( __FILE__ ).'js/Input.js'.$p_v);
			wp_enqueue_script('Hotel_js', plugin_dir_url( __FILE__ ).'js/Hotel.js'.$p_v);
			wp_enqueue_script('Confirm_js', plugin_dir_url( __FILE__ ).'js/Confirm.js'.$p_v);
			wp_enqueue_script('Reservation_manage', plugin_dir_url( __FILE__ ).'js/Reservation_manage.js'.$p_v);
			wp_enqueue_script('Member_js', plugin_dir_url( __FILE__ ).'js/Member_manage.js'.$p_v);
			wp_localize_script('Member_js', 'setting_data', $localize_script);
			wp_localize_script('Member_js', $this->prefix.'dictionary', $dictionary);
			
			wp_enqueue_style('Control.css', plugin_dir_url( __FILE__ ).'css/Control.css', array(), $this->plugin_version);
			wp_enqueue_style('Control_for_madia_css', plugin_dir_url( __FILE__ ).'css/Control_for_madia.css', array(), $this->plugin_version);
			wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
            wp_add_inline_style("Control.css", $this->getFontFaceStyle());
			
			?>
			<div id="booking_pacage_users" class="wrap">
				
				
				<div id="tabFrame">
					<div style="overflow-x: auto;">
						<div id="menuList" class="menuList" style="grid-template-columns: auto;">
							<div id="usersLink" class="menuItem active hidden_panel" style="grid-column-start: 1;"><?php _e("Users", 'booking-package'); ?></div>
							<div id="formFieldsLink" class="menuItem hidden_panel" style="grid-column-start: 2;"><?php _e("Form fields", 'booking-package'); ?></div>
							<div id="settingLink" class="menuItem hidden_panel" style="grid-column-start: 3;"><?php _e("Settings", 'booking-package'); ?></div>
						</div>
					</div>
					<div id="contentPanel" class="content">
						<div id="member_list" class="">
							
							<div class="actionButtonPanel">
								
								<div class="actionButtonPanelLeft">
									<input type="text" id="search_users_text" class="serch_users_text" placeholder="Keywords" />
									<button id="search_user_button" type="button" class="w3tc-button-save button-primary serch_user_button"><?php _e("Search", 'booking-package'); ?></button>
									<button id="clear_user_button" type="button" class="w3tc-button-save button-primary clear_user_button"><?php _e("Clear", 'booking-package'); ?></button>
								</div>
								<div class="actionButtonPanelRight">
									<button id="add_member" type="button" class="w3tc-button-save button-primary" style="margin-right: 10px;"><?php _e("Add user", 'booking-package'); ?></button>
									<?php
									$this->upgradeButton($isExtensionsValid, true);
									?>
								</div>
							</div>
							
							<table id="member_list_table" class="wp-list-table widefat fixed striped">
								<tbody id="member_list_tbody">
								<?php
									print "<tr><td>ID</td><td>" . __("Username", 'booking-package') . "</td><td>" . __("Email", 'booking-package') . "</td><td>" . __("Registered", 'booking-package') . "</td></tr>\n";
									$users_data = array();
									foreach ((array) $users as $key => $user) {
										
										$priority_high = "";
										if (empty($user->status) || intval($user->status) == 0) {
											
											$priority_high = '<span class="material-icons priority_high">priority_high</span>';
											
										}
										$users_data['user_id_' . $user->ID] = $user;
										print "<tr id='user_id_" . $user->ID . "' class='tr_user'><td><span class='userId'>" . $user->ID . "</span>".$priority_high."</td><td>" . $user->user_login . "</td><td>" . $user->user_email . "</td><td>" . $user->user_registered . "</td></tr>\n";
										
									}
									
								?>
								</tbody>
							</table>
							
							<div class="page_action_panel">
								<select id="swich_authority" class="select_limit<?php echo $swich_authority_by_hidden; ?>">
									<option value="user">Booking Package</option>
									<?php
										if (intval($memberSetting['accept_subscribers_as_users']['value']) == 1) {
										
											print '<option value="subscriber">' . __("Subscriber", 'booking-package') . '</option>';
										
										}
										
										if (intval($memberSetting['accept_contributors_as_users']['value']) == 1) {
										
											print '<option value="contributor">' . __("Contributor", 'booking-package') . '</option>';
										
										}
										
									?>
								</select>
								
								<select id="member_limit" class="select_limit">
									<option value="10">10</option>
									<option value="20">20</option>
									<option value="30">30</option>
									<option value="40">40</option>
									<option value="50">50</option>
								</select>
								<button id="before_page" class="material-icons page_button w3tc-button-save button-primary">navigate_before</button>
								<button id="next_page" class="material-icons page_button w3tc-button-save button-primary">navigate_next</button>
								
							</div>
							
						</div>
						<div id="formFieldsPanel" class="hidden_panel">
							
						</div>
						<div id="userSettingPanel" class="hidden_panel">
							
						</div>
					</div>
				</div>
				
				<!--
				<div id="member_list">
					
					<div class="actionButtonPanel">
						
						<div class="actionButtonPanelLeft">
							<input type="text" id="search_users_text" class="serch_users_text" placeholder="Keywords" />
							<button id="search_user_button" type="button" class="w3tc-button-save button-primary serch_user_button"><?php _e("Search", 'booking-package'); ?></button>
							<button id="clear_user_button" type="button" class="w3tc-button-save button-primary clear_user_button"><?php _e("Clear", 'booking-package'); ?></button>
						</div>
						<div class="actionButtonPanelRight">
							<button id="add_member" type="button" class="w3tc-button-save button-primary" style="margin-right: 10px;"><?php _e("Add user", 'booking-package'); ?></button>
							<?php
							$this->upgradeButton($isExtensionsValid, true);
							?>
						</div>
					</div>
					
					<table id="member_list_table" class="wp-list-table widefat fixed striped">
						<tbody id="member_list_tbody">
						<?php
							print "<tr><td>ID</td><td>" . __("Username", 'booking-package') . "</td><td>" . __("Email", 'booking-package') . "</td><td>" . __("Registered", 'booking-package') . "</td></tr>\n";
							$users_data = array();
							foreach ((array) $users as $key => $user) {
								
								$priority_high = "";
								if (empty($user->status) || intval($user->status) == 0) {
									
									$priority_high = '<span class="material-icons priority_high">priority_high</span>';
									
								}
								$users_data['user_id_' . $user->ID] = $user;
								print "<tr id='user_id_" . $user->ID . "' class='tr_user'><td><span class='userId'>" . $user->ID . "</span>".$priority_high."</td><td>" . $user->user_login . "</td><td>" . $user->user_email . "</td><td>" . $user->user_registered . "</td></tr>\n";
								
							}
							
						?>
						</tbody>
					</table>
					<div class="page_action_panel">
						<select id="swich_authority" class="select_limit<?php echo $swich_authority_by_hidden; ?>">
							<option value="user">Booking Package</option>
							<?php
								if (intval($memberSetting['accept_subscribers_as_users']['value']) == 1) {
								
									print '<option value="subscriber">' . __("Subscriber", 'booking-package') . '</option>';
								
								}
								
								if (intval($memberSetting['accept_contributors_as_users']['value']) == 1) {
								
									print '<option value="contributor">' . __("Contributor", 'booking-package') . '</option>';
								
								}
								
							?>
						</select>
						
						<select id="member_limit" class="select_limit">
							<option value="10">10</option>
							<option value="20">20</option>
							<option value="30">30</option>
							<option value="40">40</option>
							<option value="50">50</option>
						</select>
						<button id="before_page" class="material-icons page_button w3tc-button-save button-primary">navigate_before</button>
						<button id="next_page" class="material-icons page_button w3tc-button-save button-primary">navigate_next</button>
						
					</div>
					
				</div>
				-->
				
				<div id="editPanel" class="edit_modal hidden_panel">
					<button type="button" id="media_modal_close" class="media_modal_close">
						<span class="">
							<span class="material-icons">close</span>
						</span>
					</button>
					<div class="edit_modal_content">
						<div id="menu_panel" class="media_frame_menu hidden_panel">
							<div id="media_menu" class="media_menu"></div>
						</div>
						<div id="media_title" class="media_left_zero"><h1 id="edit_title"></h1></div>
						<div id="media_router" class="media_left_zero">
							<div class="reservation_table_row">
								<div id="booked_list" class="media_menu_item active"><?php _e("Booking history", 'booking-package'); ?></div>
								<div id="edit_user" class="media_menu_item"><?php _e("User", 'booking-package'); ?></div>
							</div>
						</div>
						<div id="media_frame_reservation_content" class="media_left_zero">
							<div id="reservation_usersPanel" class="hidden_panel"></div>
							<div id="user_detail_panel" class="hidden_panel">
								<table class="wp-list-table widefat fixed">
									<tbody>
										<tr>
											<th><?php _e("Username", 'booking-package'); ?></th>
											<td><div id="user_edit_login"></div></td>
										</tr>
										<tr id='profile_tr_input_field_user_email'>
											<th><?php _e("Email", 'booking-package'); ?></th>
											<td><input type="text" name="user_edit_email" id="user_edit_email" class="input"></td>
										</tr>
										<tr>
											<th><?php _e("Status", 'booking-package'); ?></th>
											<td>
												<label>
													<input type="checkbox" name="user_edit_status" id="user_edit_status" class="" value="1">
													<?php _e("Approved", 'booking-package'); ?>
												</label>
											</td>
										</tr>
										<tr id='profile_tr_input_field_user_pass'>
											<th><?php _e("Password", 'booking-package'); ?></th>
											<td>
												<div>
													<button id="user_edit_change_password_button" class="w3tc-button-save button-primary"><?php _e("Change password", 'booking-package'); ?></button>
                    								<input type="text" name="user_edit_pass" id="user_edit_pass" class="input hidden_panel"　autocomplete="new-password">
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div id='editCustomFormFieldPanel'>
									
								</div>
							</div>
							
						</div>
						<div id="frame_toolbar" class="media_frame_toolbar media_left_zero">
							<div class="media_toolbar">
								<div id="buttonPanel" class="media_toolbar_primary" style="float: initial;">
									
									<div id="leftButtonPanel">
										<button id="beforButton" class="material-icons button media-button button-primary button-large media-button-insert">navigate_before</button>
										<button id="nextButton" class="material-icons button media-button button-primary button-large media-button-insert">navigate_next</button>
										<div id"positionOfBookedList"></div>
									</div>
									<div id="rightButtonPanel" style="float: none !important;">
										<button id="edit_user_button" class="w3tc-button-save button-primary"><?php _e("Update Profile", 'booking-package'); ?></button>
                						<button id="edit_user_delete_button"  class="w3tc-button-save button-primary deleteButton"><?php _e("Delete", 'booking-package'); ?></button>
									</div>
									
								</div>
							</div>
						</div>
						
					</div>
					
				</div>
				
				
				<div id="dialogPanel" class="hidden_panel">
					<div class="blockPanel"></div>
					<div class="confirmPanel">
						<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
						<div class="body"><?php _e("Message", 'booking-package'); ?></div>
						<div class="buttonPanel">
							<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
							<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
						</div>
					</div>
				</div>
				
				<div id="blockPanel" class="edit_modal_backdrop hidden_panel">
					<?php
						print $this->member_form();
					?>
				</div>
				
				<div id="loadingPanel">
					<div class="loader">
						<svg viewBox="0 0 64 64" width="64" height="64">
							<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>
						</svg>
					</div>
				</div>
				
				
				
			</div>
			
			
		
			<?php
			wp_localize_script('Member_js', 'users_data', $users_data);
			$load_end_time = microtime(true) - $load_start_time;
			echo '<!-- Load time: ' . $load_end_time . ' -->';
			
		}
		
		public function schedule_page(){
			
			$load_start_time = microtime(true);
			date_default_timezone_set($this->getTimeZone());
			
			$this->update_database();
			$this->upgrader_process();
			$this->databaseUpdateErrors('page=booking-package_schedule_page', false);
			
			$setting = $this->setting;
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$schedule->deleteOldDaysInSchedules();
			$dictionary = $this->getDictionary("schedule_page", $this->plugin_name);
			$localize_script = $this->localizeScript('schedule_page');
			$isExtensionsValid = $this->getExtensionsValid(true, false);
			if ($isExtensionsValid == true) {
				
				$localize_script['isExtensionsValid'] = 1;
				
			} else {
				
				$localize_script['isExtensionsValid'] = 0;
				
			}
			
			$p_v = "?p_v=" . $this->plugin_version;
			#wp_print_scripts(array('jquery-ui-sortable'.$p_v));
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style('booking_app_js_css.css', plugin_dir_url( __FILE__ ).'css/Booking_app.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style('Control.css', plugin_dir_url( __FILE__ ).'css/Control.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style('Control_for_madia_css', plugin_dir_url( __FILE__ ).'css/Control_for_madia.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
			$fontFaceStyle = $this->getFontFaceStyle();
            wp_add_inline_style("Control.css", $fontFaceStyle);
            wp_enqueue_script('Error_js', plugin_dir_url( __FILE__ ).'js/Error.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('i18n_js', plugin_dir_url( __FILE__ ).'js/i18n.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('XMLHttp_js', plugin_dir_url( __FILE__ ).'js/XMLHttp.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Confirm_js', plugin_dir_url( __FILE__ ).'js/Confirm.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Calendar_js', plugin_dir_url( __FILE__ ).'js/Calendar.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('input_js', plugin_dir_url( __FILE__ ).'js/Input.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Customize_js', plugin_dir_url( __FILE__ ).'js/Customize.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('schedule_pange', plugin_dir_url( __FILE__ ).'js/Schedule.js' . $p_v, array(), $this->plugin_version);
			wp_localize_script('schedule_pange', $this->prefix.'dictionary', $dictionary);
			wp_localize_script('schedule_pange', 'schedule_data', $localize_script);
			/**
			wp_enqueue_style('codemirror_css', 'https://codemirror.net/5/lib/codemirror.css', array(), $this->plugin_version);
			wp_enqueue_script('codemirror_js', 'https://codemirror.net/5/lib/codemirror.js', array(), $this->plugin_version);
			wp_enqueue_script('codemirror_css_js', 'https://codemirror.net/5/mode/css/css.js', array(), $this->plugin_version);
			wp_enqueue_script('codemirror_javascript_js', 'https://codemirror.net/5/mode/javascript/javascript.js', array(), $this->plugin_version);
			**/
			wp_enqueue_script('jquery');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('monaco_editor', 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs/loader.js', array(), $this->plugin_version);
			
			?>
			
			<div id="booking_package_calendar_accounts" style='display: none;'>
				
				<div class="">
					
					<div class="top_bar">
						
					</div>
					<!-- <div id="select_package" class="welcome-panel"><div>  -->
					<!-- <div id="select_package"></div> -->
					
					<div id="calendarAccountList">
						<div class="actionButtonPanel">
							<button id="add_new_calendar" type="button" class="w3tc-button-save button-primary" style="margin-right: 10px;"><?php _e("Add booking calendar", 'booking-package'); ?></button>
							<button id="create_clone" type="button" class="w3tc-button-save button-primary" style="margin-right: 10px;"><?php _e("Copy booking calendar", 'booking-package'); ?></button>
							<?php
								$this->upgradeButton($isExtensionsValid, true);
							?>
						</div>
						<table id="calendar_list_table" class="wp-list-table widefat fixed striped"></table>
						
					</div>
					<div id="tabFrame" class="hidden_panel">
						<!--
						<div class="actionButtonPanel">
							<button id="return_to_calendar_list" type="button" class="w3tc-button-save button-primary" style="margin-right: 10px;"><?php _e("Return", 'booking-package'); ?></button>
						</div>
						-->
						<div style="overflow-x: auto;">
							<div class="menuList">
								<div id='return_to_calendar_list' class='menuItem material-icons' style='padding-left: 0; color: #000; grid-column-start: 1; grid-column-end: 2;' title='Return'>menu_open</div>
								<div id="calendarLink" class="menuItem active"><?php _e("Schedules", 'booking-package'); ?></div>
								<div id="closedDaysLink" class="menuItem hidden_panel"><?php _e("Closing days", 'booking-package'); ?></div>
								<div id="formLink" class="menuItem hidden_panel"><?php _e("Form fields", 'booking-package'); ?></div>
								<div id="courseLink" class="menuItem hidden_panel"><?php _e("Services", 'booking-package'); ?></div>
								<div id="guestsLink" class="menuItem hidden_panel"><?php _e("Guests", 'booking-package'); ?></div>
								<div id="staffLink" class="menuItem hidden_panel"><?php _e("Staff", 'booking-package'); ?></div>
								<div id="optionsForHotelLink" class="menuItem hidden_panel"><?php _e('Options', 'booking-package'); ?></div>
								<div id="couponsLink" class="menuItem hidden_panel"><?php _e('Coupons', 'booking-package'); ?></div>
								<div id="taxLink" class="menuItem hidden_panel"><?php print __('Extra charges', 'booking-package') . ' | ' . __('Taxes', 'booking-package'); ?></div>
								
								<div id="extraChargesLink" class="menuItem hidden_panel"><?php _e('Extra Charges', 'booking-package'); ?></div>
								<div id="taxesLink" class="menuItem hidden_panel"><?php _e('Taxes', 'booking-package'); ?></div>
								
								<div id="emailLink" class="menuItem hidden_panel"><?php _e("Notifications", 'booking-package'); ?></div>
								<div id="syncLink" class="menuItem hidden_panel"><?php _e("Sync", 'booking-package'); ?></div>
								<div id="customizeLink" class="menuItem hidden_panel"><?php _e("Customize", 'booking-package'); ?></div>
								<div id="settingLink" class="menuItem hidden_panel"><?php _e("Settings", 'booking-package'); ?></div>
							</div>
						</div>
						<div id="contentPanel" class="content">
							<div id="schedulePage"></div>
							<div id="closedDaysPanel" class="hidden_panel">
								<div id="holidaysCalendarPanel"></div>
							</div>
							<div id="formPanel" class="hidden_panel"></div>
							<div id="coursePanel" class="hidden_panel"></div>
							<div id="staffPanel" class="hidden_panel"></div>
							<div id="guestsPanel" class="hidden_panel"></div>
							<div id="optionsForHotelPanel" class="hidden_panel"></div>
							<div id="taxPanel" class="hidden_panel"></div>
							<div id="taxesPanel" class="hidden_panel"></div>
							<div id="extraChargesPanel" class="hidden_panel"></div>
							<div id="couponsPanel" class="hidden_panel"></div>
							<div id="emailPanel" class="hidden_panel">
								<div id="mailSettingPanel">
									<div id="mailSettingButtonPanel"></div>
									<div id="content_area"></div>
								</div>
							</div>
							<div id="syncPanel" class="hidden_panel"></div>
							<div id="customizePanel" class="hidden_panel"></div>
							<div id="settingPanel" class="hidden_panel"></div>
						</div>
						
					</div>
					
					<div id="calendarName" class="hidden_panel"></div>
					<div id="calendarTimeZone" class="hidden_panel"></div>
				</div>
				
				<div id="editPanelForSchedule" class="edit_modal hidden_panel">
					<button type="button" id="media_modal_close_for_schedule" class="media_modal_close">
						<span class="media_modal_icon">
							<span class="screen_reader_text">Close</span>
						</span>
					</button>
					<div class="edit_modal_content">
						<div id="menu_panel_for_schedule" class="media_frame_menu">
							<div id="media_menu_for_schedule" class="media_menu"></div>
						</div>
						<div id="media_title_for_schedule" class="media_frame_title">
							<h1 id="edit_title_for_schedule"></h1>
						</div>
						<div id="media_router_for_schedule" class="media_frame_router">
							<!--
							<table class="tableNameList">
								
								<tr>
									<th>No</th>
									<td class="timeTd">Time</td>
									<td id="deadlineTime" class="td_width_100_px">Booking end time</td>
									<td>Title</td>
									<td class="td_width_50_px">Capacities</td>
									<td id="remainder" class="td_width_100_px hidden_panel">Remaining</td>
									<td id="stop" class="td_width_50_px"><div class="deletePanel">Stop</div></td>
									<td id="allScheduleDelete" class="td_width_50_px"><div class="deletePanel">Delete</div></td>
								</tr>
								
							</table>
							-->
						</div>
						<div id='incompletelyDeletedScheduleAlertPanel' class='hidden_panel'>
							<?php 
								#print __("This schedule has not been perfectly deleted.", 'booking-package') . ' ' . __('If you delete this schedule completely, the schedules will be re-registered based on the "Weekly schedule templates".', 'booking-package'); 
								print __("This time slot has not been deleted completely.", 'booking-package') . ' '; 
								printf( __('If you fully delete the time slots, they will be re-registered based on the "%s".', 'booking-package'), __('Weekly schedule templates', 'booking-package') );
							?> 
						</div>
						<div id="media_frame_content_for_schedule"></div>
						
						<div id="edit_schedule_for_hotel" class="media_left_zero hidden_panel">
							
							<table id="scheduleEditTable" class="table_option wp-list-table widefat fixed striped" style="border: 0px;">
								<tr class="">
									<td class="">Date</td>
									<td class="">State</td>
									<td class="">Charges</td>
									<td class="">Rooms</td>
								</tr>
								
							</table>
							
						</div>
						
						<div id="email_edit_panel" class="media_left_zero hidden_panel">
							
							<div id="edit_email_message" class="mail_message_area_left">
								<div class="enablePanel">
									<div class="enableLabel"><?php _e("Notifications", 'booking-package'); ?></div>
									<div class="enableValuePanel">
										<label style="margin-right: 10px;"><input type="checkbox" id="mailEnable"/><?php _e("Email", 'booking-package'); ?></label>
										<label><input type="checkbox" id="smsEnable"/>
										<?php 
											$messaging = __("Messaging Services", 'booking-package'); 
											if ($this->messagingApp === 0) {
												
												$messaging = __("SMS", 'booking-package'); 
												
											}
											print $messaging;
											
										?>
										</label>
										
									</div>
								</div>
								<div class="emailFormatPanel">
									<div class="emailFormatLabel"><?php _e("Format", 'booking-package'); ?></div>
									<div class="emailFormatValuePanel">
										<label style="margin-right: 10px;"><input type="radio" id="emailFormatHtml" name="emailFormat" /> HTML</label>
										<label><input type="radio" id="emailFormatText" name="emailFormat" /> TEXT</label>
									</div>
								</div>
								
								<div class="enablePanel" id="field_notifyAdministrator">
									<div class="enableLabel"><?php _e("Notify the administrator", 'booking-package'); ?></div>
									<div class="enableValuePanel">
										<label style="margin-right: 10px;"><input type="checkbox" id="notifyAdministrator"/><?php _e("Enabled", 'booking-package'); ?></label>
									</div>
								</div>
								
								<div class="enablePanel">
									<div class="enableLabel"><?php _e("Attach an iCalendar file in the email", 'booking-package'); ?></div>
									<div class="enableValuePanel">
										<label style="margin-right: 10px;"><input type="checkbox" id="attachICalendar"/><?php _e("Enabled", 'booking-package'); ?></label>
									</div>
								</div>
								
								<div>
									<div class="menuTags">
										<div id="menuList" class="menuList">
											<div id="for_visitor" class="menuItem active"><?php _e("For customer", 'booking-package'); ?></div>
											<div id="for_administrator" class="menuItem"><?php _e("For administrator", 'booking-package'); ?></div>
											<div id="for_icalendar" class="menuItem"><?php _e("For iCalendar", 'booking-package'); ?></div>
										</div>
										
									</div>
									<div class="content">
										
										<div id="edit_visitor_message">
											<input type="text" id="subject" class="mail_subject" placeholder="Subject">
											<textarea name="emailContent" id="emailContent" class="message_body" placeholder="Message body"></textarea>
										</div>
										<div id="edit_administrator_message" class="hidden_panel">
											<input type="text" id="subjectForAdmin" class="mail_subject" placeholder="Subject">
											<textarea name="emailContent" id="emailContentForAdmin" class="message_body" placeholder="Message body"></textarea>
										</div>
										<div id="edit_icalendar_message" class="hidden_panel">
											<input type="text" id="subjectForIcalendar" class="icalendar_subject" placeholder="Summary">
											<input type="text" id="locationForIcalendar" class="icalendar_location" placeholder="Location">
											<textarea name="emailContent" id="contentForIcalendar" class="icalendar_body" placeholder="Message body"></textarea>
											
										</div>
									</div>
								</div>
								
								
							</div>
							<div id="mail_message_area_right" class="mail_message_area_right"></div>
							
						</div>
						
						<div id="frame_toolbar_for_schedule" class="media_frame_toolbar">
							<div class="media_toolbar">
								<div id="buttonPanel_for_schedule" class="media_toolbar_primary">
									
								</div>
							</div>
						</div>
						
					</div>
					
				</div>
				
				<div id="blockPanel" class="edit_modal_backdrop hidden_panel"></div>
				
				<div id="deletePublishedSchedulesPanel" class="hidden_panel">
					
					<div>
						<div class="titlePanel">
							<div class="title"><?php echo __("Select a date", 'booking-package'); ?></div>
							<div id="deletePublishedSchedulesPanel_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							<table>
								<tr>
									<th><label><?php echo __("Period", 'booking-package'); ?></label></th>
									<td>
										<label>
											<input id="period_all" name="period" type="radio" value="period_all"><?php echo __("All", 'booking-package'); ?>
										</label>
										<label>
											<input id="period_after" name="period" type="radio" value="period_after" checked="checked"><?php echo __("After the specified date", 'booking-package'); ?>
										</label>
										<label>
											<input id="period_within" name="period" type="radio" value="period_within"><?php echo __("Within the specified date", 'booking-package'); ?>
										</label>
									</td>
								</tr>
								<tr>
									<th><label><?php echo __("Date", 'booking-package'); ?></label></th>
									<td>
										<label id="period_after_date">
											<?php
												
												if ($this->locale != 'ja') {
													
													print '<span class="from">' . __("From:", 'booking-package') . '</span>';
													
												}
												
											?>
											<!-- <span class="from"><?php echo __("From:", 'booking-package'); ?></span> -->
											<select id="deletePublishedSchedules_from_month">
												<option value="1"><?php echo __("January", 'booking-package'); ?></option>
												<option value="2"><?php echo __("February", 'booking-package'); ?></option>
												<option value="3"><?php echo __("March", 'booking-package'); ?></option>
												<option value="4"><?php echo __("April", 'booking-package'); ?></option>
												<option value="5"><?php echo __("May", 'booking-package'); ?></option>
												<option value="6"><?php echo __("June", 'booking-package'); ?></option>
												<option value="7"><?php echo __("July", 'booking-package'); ?></option>
												<option value="8"><?php echo __("August", 'booking-package'); ?></option>
												<option value="9"><?php echo __("September", 'booking-package'); ?></option>
												<option value="10"><?php echo __("October", 'booking-package'); ?></option>
												<option value="11"><?php echo __("November", 'booking-package'); ?></option>
												<option value="12"><?php echo __("December", 'booking-package'); ?></option>
											</select>
											<select id="deletePublishedSchedules_from_day">
											<?php
												
												for ($i = 1; $i < 32; $i++) {
													
													echo '<option value="'.$i.'">'.$i.'</option>';
													
												}
												
											?>
											</select>
											<select id="deletePublishedSchedules_from_year">
											<?php
												
												$year = date('Y');
												for ($i = 0; $i < 2; $i++) {
													
													echo '<option value="'.$year.'">'.$year.'</option>';
													$year++;
													
												}
												
											?>
											</select>
											<?php
												
												if ($this->locale == 'ja') {
													
													print '<span class="from">' . __("From:", 'booking-package') . '</span>';
													
												}
												
											?>
											</label>
											<label id="period_within_date" class="hidden_panel">
											<?php
												
												if ($this->locale != 'ja') {
													
													print '<span class="to">' . __("To:", 'booking-package') . '</span>';
													
												}
												
											?>
											
											<select id="deletePublishedSchedules_to_month">
												<option value="1"><?php echo __("January", 'booking-package'); ?></option>
												<option value="2"><?php echo __("February", 'booking-package'); ?></option>
												<option value="3"><?php echo __("March", 'booking-package'); ?></option>
												<option value="4"><?php echo __("April", 'booking-package'); ?></option>
												<option value="5"><?php echo __("May", 'booking-package'); ?></option>
												<option value="6"><?php echo __("June", 'booking-package'); ?></option>
												<option value="7"><?php echo __("July", 'booking-package'); ?></option>
												<option value="8"><?php echo __("August", 'booking-package'); ?></option>
												<option value="9"><?php echo __("September", 'booking-package'); ?></option>
												<option value="10"><?php echo __("October", 'booking-package'); ?></option>
												<option value="11"><?php echo __("November", 'booking-package'); ?></option>
												<option value="12"><?php echo __("December", 'booking-package'); ?></option>
											</select>
											<select id="deletePublishedSchedules_to_day">
											<?php
												
												for ($i = 1; $i < 32; $i++) {
													
													echo '<option value="'.$i.'">'.$i.'</option>';
													
												}
												
											?>
											</select>
											<select id="deletePublishedSchedules_to_year">
											<?php
												
												$year = date('Y');
												for ($i = 0; $i < 2; $i++) {
													
													echo '<option value="'.$year.'">'.$year.'</option>';
													$year++;
													
												}
												
											?>
											</select>
											<?php
												
												if ($this->locale == 'ja') {
													
													print '<span class="to">' . __("To:", 'booking-package') . '</span>';
													
												}
												
											?>
										</label>
										<p id="deletePublishedSchedules_freePlan" class="hidden_panel freePlan">
											<?php echo __("With the free plan, date selection is not available.", 'booking-package'); ?>
										</p>
									</td>
								</tr>
			                	<tr>
			                		<th><label><?php echo __("Action", 'booking-package'); ?></label></th>
			                		<td>
			                			<label><input id="action_delete" type="radio" name="type" value="delete" checked="checked"><?php echo __("Delete", 'booking-package'); ?></label>
			                			<label><input id="action_stop" type="radio" name="type" value="stop"><?php echo __("Paused", 'booking-package'); ?></label>
			                		</td>
			                	</tr>
			                	<tr>
			                		<th><label><?php echo __("Deletion type", 'booking-package'); ?></label></th>
			                		<td>
			                			<label><input id="delete_incomplete" type="radio" name="deletionType" value="incomplete" checked="checked"><?php echo __("Imperfect", 'booking-package'); ?></label>
			                			<label><input id="delete_perfect" type="radio" name="deletionType" value="perfect"><?php echo __("Perfect", 'booking-package'); ?></label>
			                		</td>
			                	</tr>
			                </table>
			            </div>
			            <div class="inputPanel">
			            	
			            </div>
			            <div class="buttonPanel">
			                <button id="deletePublishedSchedulesButton" class="media-button button-primary button-large media-button-insert deleteButton"><?php echo __("Delete", 'booking-package'); ?></button>
			            </div>
			        </div>
					
				</div>
				
				<div id="cssPanelInLayouts" class="hidden_panel">
					
					<div>
						<div class="titlePanel">
							<div class="title"><?php echo __("CSS", 'booking-package'); ?></div>
							<div id="closeCssPanelButton" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;"></div>
						<div class="buttonPanel">
							<button id='saveCssPanelButton' class='media-button button-primary button-large media-button-insert'><?php echo __("Apply", 'booking-package'); ?></button>
						</div>
					</div>
					
				</div>
				
				<div id="calendarPanelForBookingDate" class="hidden_panel">
					
					<div>
						
						<div class="titlePanel">
							<div class="title"><?php echo __("Select a date", 'booking-package'); ?></div>
							<div id="calendarPanelForBookingDate_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							
						</div>
						<div class="inputPanel" style="border-width: 0;">
							<span><?php echo __("Time", 'booking-package'); ?>: </span>
							<span>
								<select id="saveForBookingTime">
									<?php
										
										for ($i = 0; $i < 24; $i++) {
											
											echo '<option value="' . sprintf('%02d', $i) . '">' . sprintf('%02d', $i) . ':00' . '</option>';
											
										}
									
									?>
								</select>
							</span>
						</div>
						<div class="inputPanel" style="border-width: 0;">
							<span><?php echo __("Publication date", 'booking-package'); ?>: </span>
							<span id="setPublicationDate"></span>
						</div>
						<div class="inputPanel" style="border-width: 0; padding-bottom: 1em;">
							<span><?php echo __("The publication date applies to the frontend calendar.", 'booking-package'); ?></span>
						</div>
						<div class="buttonPanel">
							<?php
								
								if ($isExtensionsValid !== true) {
									
									print "<span class='extensionsValid' style='margin-right: 1em;'>" . __("Paid plan subscription required.", 'booking-package') . "</span>";
									
								}
								
							?>
							<button id='deleteForBookingDate' class='media-button button-primary button-large media-button-insert'><?php echo __("Clear", 'booking-package'); ?></button>
							<button id='saveForBookingDate' class='media-button button-primary button-large media-button-insert'><?php echo __("Save", 'booking-package'); ?></button>
						</div>
					</div>
					
				</div>
				
				<div id="loadSchedulesPanel" class="hidden_panel">
					
					<div>
						
						<div class="titlePanel">
							<div class="title"><?php echo __("Set up time slots", 'booking-package'); ?></div>
							<div id="loadSchedulesPanel_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							
							<div class="labelTitle" style="border-top: 0;"><?php echo __("Time", 'booking-package'); ?></div>
							<div>
								<span class="fromPanel">
								<?php
									if ($this->locale != 'ja') {
										
										print '<span class="from">' . __("From:", 'booking-package') . '</span>';
										
									}
								?>
								<input type="time" id="read_from_on_time" value="00:00" min="00:00" max="23:59" pattern="[0-9]{2}:[0-9]{2}" required>
								<?php
								if ($this->locale == 'ja') {
									
									print '<span class="from">' . __("From:", 'booking-package') . '</span>';
									
								}
								?>
								
								<?php
									if ($this->locale != 'ja') {
										
										print '<span class="to">' . __("To:", 'booking-package') . '</span>';
										
									}
									?>
									<input type="time" id="read_to_on_time" value="00:00" min="00:00" max="23:59" pattern="[0-9]{2}:[0-9]{2}" required>
									<?php
									if ($this->locale == 'ja') {
										
										print '<span class="to">' . __("To:", 'booking-package') . '</span>';
										
									}
								?>
								</span>
							</div>
							<div class="labelTitle"><?php echo __("Interval", 'booking-package'); ?></div>
							<div>
								<select id="interval_min_on_time" data-interval="5">
								<?php
									
									for ($i = 1; $i <= 120; $i += 1) { echo '<option value="'.$i.'">' . sprintf(__('%s min', 'booking-package'), $i) . '</option>'; }
									
								?>
								</select>
							</div>
							<div class="labelTitle"><?php echo __("Booking end time", 'booking-package'); ?></div>
							<div>
								<select id="load_deadline_time_on_time">
								<?php
									
									for ($i = 0; $i <= BOOKING_PACKAGE_MAX_DEADLINE_TIME; $i += 30) { echo '<option value="'.$i.'">' . sprintf(__('%s min before', 'booking-package'), $i) . '</option>'; }
									
								?>
								</select>
							</div>
							<div class="labelTitle"><?php echo __("Available slots", 'booking-package'); ?></div>
							<div>
								<select id="load_capacity">
								<?php
									
									for ($i = 1; $i <= 300; $i++) { echo '<option value="'.$i.'">' . $i . '</option>'; }
									
								?>
								</select>
							</div>
							
						</div>
						
						<div class="buttonPanel">
							<button id="readSchedulesButton" class="media-button button-primary button-large media-button-insert"><?php echo __("Apply", 'booking-package'); ?></button>
						</div>
						
					</div>
					
				</div>
				
				<div id="createClonePanel" class="hidden_panel">
					<div>
						<div class="titlePanel">
							<div class="title"><?php echo __("Select a calendar", 'booking-package'); ?></div>
							<div id="createClonePanel_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							<table>
								<tr>
									<th><label><?php echo __("Calendar", 'booking-package') . ':'; ?></label></th>
									<td>
										<select id="selectedClone">
										<?php
										
										
										?>
										</select>
									</td>
								</tr>
								<tr>
									<th><label><?php echo __("Target", 'booking-package') . ':'; ?></label></th>
									<td>
										<label><input type="checkbox" name="target" class="target" value="schedules" checked="checked"> <?php echo __("Schedules", 'booking-package'); ?></label>
										<label><input type="checkbox" name="target" class="target" value="form" checked="checked"> <?php echo __("Form fields", 'booking-package'); ?></label>
										<label><input type="checkbox" name="target" class="target" value="services" checked="checked"> <?php echo __("Services", 'booking-package'); ?></label>
										<label><input type="checkbox" name="target" class="target" value="guests" checked="checked"> <?php echo __("Guests", 'booking-package'); ?></label>
										<label><input type="checkbox" name="target" class="target" value="taxes" checked="checked"> <?php echo __("Surcharge and Tax", 'booking-package'); ?></label>
										<label><input type="checkbox" name="target" class="target" value="emails" checked="checked"> <?php echo __("Notifications", 'booking-package'); ?></label>
									</td>
								</tr>
							</table>
						</div>
						<div class="buttonPanel">
							<button id="createCloneButton" class="media-button button-primary button-large media-button-insert"><?php echo __("Create", 'booking-package'); ?></button>
						</div>
					</div>
				</div>
				
				<div id="selectionSchedule" class="hidden_panel">
					
					<div>
						<div class="titlePanel">
							<div class="title"><?php echo __("Add a time slot", 'booking-package'); ?></div>
							<div id="selectionSchedule_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							<div id="selectionSchedule_hours" class="selectBlock">
								<div data-key="time" class="items" style="cursor: auto;">
									<?php echo __("Time", 'booking-package'); ?>: 
									<span><input type="time" id="change_booking_time" value="00:00" min="00:00" max="23:59" pattern="[0-9]{2}:[0-9]{2}" required style="cursor: pointer;"></span>
								</div>
								<!--
								<div data-key="time" class="selectPanel closed">
									
									<input type="time" id="change_booking_time" value="00:00" min="00:00" max="23:59" pattern="[0-9]{2}:[0-9]{2}" required>
									
								</div>
								-->
							</div>
							<div id="selectionSchedule_deadline" class="selectBlock">
								<div data-key="deadline" class="items"><?php echo __("Booking end time", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="deadline" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= BOOKING_PACKAGE_MAX_DEADLINE_TIME; $i += 30) { echo '<span class="selectItem" data-key="deadline" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_capacitys" class="selectBlock">
								<div data-key="capacitys" class="items"><?php echo __("Available slots", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="capacitys" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= 300; $i++) { echo '<span class="selectItem" data-key="capacitys" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_remainders" class="selectBlock">
								<div data-key="remainders" class="items"><?php echo __("Remaining slots", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="remainders" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= 300; $i++) { echo '<span class="selectItem" data-key="remainders" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
						</div>
						<div class="buttonPanel">
							<button id="selectionScheduleResetButton" class="media-button button-primary button-large media-button-insert deleteButton" style="margin-right: 1em;"><?php echo __("Reset", 'booking-package'); ?></button>
							<button id="selectionScheduleButton" class="media-button button-primary button-large media-button-insert"><?php echo __("Apply", 'booking-package'); ?></button>
						</div>
					</div>
					
				</div>
				
				<!--
				<div id="selectionSchedule" class="hidden_panel">
					
					<div>
						<div class="titlePanel">
							<div class="title"><?php echo __("Add a time slot", 'booking-package'); ?></div>
							<div id="selectionSchedule_return_button" class="material-icons closeButton" style="font-family: 'Material Icons' !important">close</div>
						</div>
						<div class="inputPanel" style="border-width: 0; margin-bottom: 0;">
							<div id="selectionSchedule_hours" class="selectBlock">
								<div data-key="hours" class="items"><?php echo __("Hours", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="hours" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i < 24; $i++) { echo '<span class="selectItem" data-key="hours" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_minutes" class="selectBlock">
								<div data-key="minutes" class="items"><?php echo __("Minutes", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="minutes" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i < 60; $i++) { echo '<span class="selectItem" data-key="minutes" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_deadline" class="selectBlock">
								<div data-key="deadline" class="items"><?php echo __("Booking end time", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="deadline" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= BOOKING_PACKAGE_MAX_DEADLINE_TIME; $i += 30) { echo '<span class="selectItem" data-key="deadline" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_capacitys" class="selectBlock">
								<div data-key="capacitys" class="items"><?php echo __("Available slots", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="capacitys" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= 300; $i++) { echo '<span class="selectItem" data-key="capacitys" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
							<div id="selectionSchedule_remainders" class="selectBlock">
								<div data-key="remainders" class="items"><?php echo __("Remaining slots", 'booking-package'); ?>: <span>7</span></div>
								<div data-key="remainders" class="selectPanel closed">
									
									<?php
									
									for ($i = 0; $i <= 300; $i++) { echo '<span class="selectItem" data-key="remainders" data-value="' . $i . '">' . sprintf('%02d', $i) . '</span>'; }
									
									?>
									
								</div>
							</div>
						</div>
						<div class="buttonPanel">
							<button id="selectionScheduleResetButton" class="media-button button-primary button-large media-button-insert deleteButton" style="margin-right: 1em;"><?php echo __("Reset", 'booking-package'); ?></button>
							<button id="selectionScheduleButton" class="media-button button-primary button-large media-button-insert"><?php echo __("Apply", 'booking-package'); ?></button>
						</div>
					</div>
					
				</div>
				-->
				
				<div id="load_blockPanel" style="z-index: 16000;"></div>
				
				<div id="timeSelectPanel" class="hidden_panel">
					<div class="blockPanel"></div>
					
					<div id="selectPanelForConfirm" class="selectPanel">
						<div id="arror"></div>
						<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
						<div id="confirm_body" class="body"></div>
						<div class="buttonPanel scheduleButtonPanel">
							<button id="dialogButtonReset" type="button" class="yesButton button button-primary" style="width: 70px; margin: 0;"><?php _e("Reset", 'booking-package'); ?></button>
							<button id="dialogButtonDone" type="button" class="noButton button button-primary" style="width: 70px; margin: 0;"><?php _e("Close", 'booking-package'); ?></button>
						</div>
					</div>
					
				</div>
				
				<div id="dialogPanel" class="hidden_panel">
					<div class="blockPanel"></div>
					<div class="confirmPanel">
						<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
						<div class="body"><?php _e("Message", 'booking-package'); ?></div>
						<div class="buttonPanel">
							<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
							<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
						</div>
					</div>
				</div>
				
				<div id="copyAndPasteOnCalendarSetting" class="hidden_panel">
					<a href="https://booking-package.saasproject.net/how-does-the-booking-calendar-show-on-the-page/" target="_blank">
						<?php _e('Copy the shortcode and paste it onto your page content.', 'booking-package'); ?>
					</a>
				</div>
				
				
				<div id="loadingPanel" class="">
					<div class="loader">
						<svg viewBox="0 0 64 64" width="64" height="64">
							<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>
						</svg>
					</div>
				</div>
				
				<select id="timezone_choice" class="hidden_panel">
					<?php
						echo wp_timezone_choice($this->getTimeZone());
					?>
				</select>
				
			</div>	
			
			
			<?php
			
			$load_end_time = microtime(true) - $load_start_time;
			echo '<!-- Load time: ' . $load_end_time . ' -->';
			
		}
		
		public function setting_page(){
			
			$timeZone = $this->getTimeZone();
			$load_start_time = microtime(true);
			$this->update_database();
			$this->upgrader_process();
			$this->databaseUpdateErrors('page=booking-package_setting_page', true);
			$setting = $this->setting;
			$booking_sync = $setting->getBookingSyncList();
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			date_default_timezone_set($timeZone);
			$isExtensionsValid = $this->getExtensionsValid(true, false);
			$dictionary = $this->getDictionary("setting_page", $this->plugin_name);
			$localize_script = $this->localizeScript("setting_page");
			$p_v = "?p_v=" . $this->plugin_version;
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style( 'setting_page', plugin_dir_url( __FILE__ ).'css/Control.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style( 'control_for_madia_css', plugin_dir_url( __FILE__ ).'css/Control_for_madia.css' . $p_v, array(), $this->plugin_version);
			wp_enqueue_style('Material_Icons', 'https://fonts.googleapis.com/css?family=Material+Icons');
			$fontFaceStyle = $this->getFontFaceStyle();
            wp_add_inline_style("Control.css", $fontFaceStyle);
            
            $dateFormat = $localize_script['list']['General'][$this->prefix . 'dateFormat']['value'];
            $positionOfWeek = $localize_script['list']['General'][$this->prefix . 'positionOfWeek']['value'];
            
			$subscriptions = $this->getSubscriptions();
			foreach ((array) $subscriptions as $key => $value) {
				
				if ($key == 'expiration_date_for_subscriptions' && strlen($value) > 0) {
					
					#$localize_script['expiration_date'] = date('F d, Y H:i', $value);
					$localize_script['expiration_date'] = $schedule->dateFormat($dateFormat, $positionOfWeek, $value, '', true, false, 'text') . ' ' . $timeZone;
					
				}
				
				if ($value == '0') {
					
					$value = null;
					
				}
				
				$localize_script[$key] = $value;
				
			}
			
			if ($isExtensionsValid == true) {
				
				$localize_script['isExtensionsValid'] = 1;
				
			} else {
				
				$localize_script['isExtensionsValid'] = 0;
				
			}
			
			if (isset($_GET['tab']) === true) {
				
				$localize_script['tab'] = sanitize_text_field($_GET['tab']);
				
			}
			
			$memberSetting = $setting->getMemberSetting($isExtensionsValid);
			$localize_script['memberSetting'] = $memberSetting;
			$country = get_option($this->prefix . 'country' , 'US');
			if (strtolower($country) != 'jp') {
				
				unset($localize_script['list']['General'][$this->prefix . 'characterCodeOfDownloadFile']);
				unset($localize_script['list']['Stripe'][$this->prefix . 'stripe_konbini_expiration_date']);
				
			}
			
			$front_end_css = $setting->getCss("front_end.css", plugin_dir_path( __FILE__ ));
			$front_end_javascript = "";
			$front_end_javascript = $setting->getJavaScript("front_end.js", plugin_dir_path( __FILE__ ));
			$localize_script['javascriptForUser'] = 1;
			
			#wp_enqueue_script( array( 'jquery-ui-sortable' ));
			wp_enqueue_script('Error_js', plugin_dir_url( __FILE__ ).'js/Error.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('i18n_js', plugin_dir_url( __FILE__ ).'js/i18n.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('XMLHttp_js', plugin_dir_url( __FILE__ ).'js/XMLHttp.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('input_js', plugin_dir_url( __FILE__ ).'js/Input.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Confirm_js', plugin_dir_url( __FILE__ ).'js/Confirm.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('Calendar_js', plugin_dir_url( __FILE__ ).'js/Calendar.js' . $p_v, array(), $this->plugin_version);
			wp_enqueue_script('setting_page', plugin_dir_url( __FILE__ ).'js/setting.js' . $p_v, array(), $this->plugin_version);
			wp_localize_script('setting_page', $this->prefix.'dictionary', $dictionary);
			wp_localize_script('setting_page', 'setting_data', $localize_script);
			
			#wp_enqueue_style('codemirror_css', 'https://codemirror.net/5/lib/codemirror.css', array(), $this->plugin_version);
			#wp_enqueue_script('codemirror_js', 'https://codemirror.net/5/lib/codemirror.js', array(), $this->plugin_version);
			#wp_enqueue_script('codemirror_css_js', 'https://codemirror.net/5/mode/css/css.js', array(), $this->plugin_version);
			#wp_enqueue_script('codemirror_javascript_js', 'https://codemirror.net/5/mode/javascript/javascript.js', array(), $this->plugin_version);
			wp_enqueue_script('jquery');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('monaco_editor', 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs/loader.js', array(), $this->plugin_version);
			?>
				
				<div id="booking_package_general_settings" class="wrap">
					
					<div id="tabFrame">
						<div style="overflow-x: auto;">
							<div id="menuList" class="menuList" style="grid-template-columns: auto;">
								<div id="settingLink" class="menuItem active hidden_panel" style="grid-column-start: 1;"><?php _e("Settings", 'booking-package'); ?></div>
								<div id="holidayLink" class="menuItem hidden_panel" style="grid-column-start: 2;"><?php _e("Closing days", 'booking-package'); ?></div>
								<div id="nationalHolidayLink" class="menuItem hidden_panel" style="grid-column-start: 3;"><?php _e("Public holidays", 'booking-package'); ?></div>
								<div id="blockEmailListsLink" class="menuItem hidden_panel"  style="grid-column-start: 4;"><?php _e("Blocks list", 'booking-package'); ?></div>
								<div id="memberLink" class="menuItem hidden_panel"  style="grid-column-start: 5;"><?php _e("Users", 'booking-package'); ?></div>
								<div id="syncLink" class="menuItem hidden_panel"  style="grid-column-start: 6;"><?php _e("Sync", 'booking-package'); ?></div>
								<div id="cssLink" class="menuItem hidden_panel"  style="grid-column-start: 7;">CSS</div>
								<div id="javascriptLink" class="menuItem hidden_panel"  style="grid-column-start: 8;">JavaScript</div>
								<div id="subscriptionLink" class="menuItem hidden_panel"  style="grid-column-start: 9;"><?php _e("Paid subscription", 'booking-package'); ?></div>
							</div>
						</div>
						<div id="contentPanel" class="content">
							<div id="settingPanel" class="hidden_panel">
								<div id="setting_table"></div>
								<div class="bottomButtonPanel"><button id="save_setting" type="button" class="w3tc-button-save button-primary"><?php _e("Save Changes", 'booking-package'); ?></button></div>
							</div>
							<div id="holidayPanel" class="hidden_panel">
								<div class="title"><?php _e("Closing days", 'booking-package'); ?></div>
								<div id="holidaysCalendarPanel"></div>
							</div>
							<div id="nationalHolidayPanel" class="hidden_panel">
								<div class="title"><?php _e("Public holidays", 'booking-package'); ?></div>
								<div id="nationalHolidaysCalendarPanel"></div>
							</div>
							<div id="blockEmailListsPanel" class="hidden_panel">
								<div class="title"><?php _e("Blocks list", 'booking-package'); ?></div>
								<?php
								if ($isExtensionsValid === false) {
									
									print '<div class="extensionsValid">' . __('Paid plan subscription required.', 'booking-package') . '</div>';
									
								}
								?>
								<div class="addValuePanel">
									<input id="<?php print $this->prefix; ?>newEmail" type="text" class="regular-text" placeholder="<?php _e("Block an email address", 'booking-package'); ?>">
									<button id="<?php print $this->prefix; ?>addBlockEmail" class="w3tc-button-save button-primary"><?php _e("Add", 'booking-package'); ?></button>
								</div>
								<table class="wp-list-table widefat fixed striped">
									<tbody id="blockEmailListsTable"></tbody>
								</table>
							</div>
							<div id="memberPanel" class="hidden_panel">
								<div id="member_table">
									<div class="title"><?php _e("Users", 'booking-package'); ?></div>
									<table class="form-table">
										<tr valign="top">
											<th scope="row"><?php _e("User account", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="function_for_member" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row"><?php _e("Reject bookings from non-user accounts", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="reject_non_membder" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row"><?php _e("User registration from customers", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="visitors_registration_for_member" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row"><?php _e("Send a verification code to the user by email during registration and editing", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="check_email_for_member" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row"><?php _e("Accept subscribers as users", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="accept_subscribers_as_users" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										<tr valign="top">
											<th scope="row"><?php _e("Accept contributors as users", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="accept_contributors_as_users" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										<!--
										<tr valign="top">
											<th scope="row">Accept authors as users</th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid">Paid plan subscription required.</div>
													<label>
														<input data-value="1" id="accept_authors_as_users" type="checkbox" value="1">
														<span class="radio_title">Enabled</span>
													</label>
												</div>
											</td>
										</tr>
										-->
										<tr valign="top">
											<th scope="row"><?php _e("Toolbar", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="user_toolbar" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
										
										<tr valign="top">
											<th scope="row"><?php _e("Lost password", 'booking-package'); ?></th>
											<td>
												<div class="valuePanel">
													<div class="extensionsValid"><?php _e("Paid plan subscription required.", 'booking-package'); ?></div>
													<label>
														<input data-value="1" id="lost_password" type="checkbox" value="1">
														<span class="radio_title"><?php _e("Enabled", 'booking-package'); ?></span>
													</label>
												</div>
											</td>
										</tr>
										
									</table>
									
									<div class="bottomButtonPanel">
										<button id="save_member_setting_button" type="button" class="w3tc-button-save button-primary"><?php _e("Save Changes", 'booking-package'); ?></button>
									</div>
									
								</div>
								
								
							</div>
							
							<div id="syncPanel" class="hidden_panel">
								<div id="bookingSync_table"></div>
								<div><button id="save_bookingSync" type="button" class="w3tc-button-save button-primary"><?php _e("Save Changes", 'booking-package'); ?></button></div>
							</div>
							<div id="cssPanel" class="hidden_panel">
								
								<div class="title">CSS</div>
								<div style="padding-bottom: 1em;"><?php _e("Change the front-end page design by defining CSS.", 'booking-package'); ?></div>
								<div id="css_editor" style="height: 100vh;"></div>
								<textarea id="css" rows="50" style="display: none;"><?php print $front_end_css; ?></textarea>
								<div class="bottomButtonPanel"><button id="save_css" type="button" class="w3tc-button-save button-primary"><?php _e("Save Changes", 'booking-package'); ?></button></div>
								
							</div>
							
							<div id="javascriptPanel" class="hidden_panel">
								
								<div class="title">JavaScript</div>
								<?php
								if ($isExtensionsValid === false) {
									
									print '<div class="extensionsValid">' . __('Paid plan subscription required.', 'booking-package') . '</div>';
									
								}
								?>
								<div id="javascript_editor" style="height: 100vh;"></div>
								<textarea id="javascript_booking_package" rows="50" style="display: none;"><?php print $front_end_javascript; ?></textarea>
								<div class="bottomButtonPanel"><button id="save_javascript" type="button" class="w3tc-button-save button-primary"><?php _e("Save Changes", 'booking-package'); ?></button></div>
								
							</div>
							<div id="subscriptionPanel" class="hidden_panel"></div>
							
						</div>
					</div>
					
					
					<div id="editPanel" class="edit_modal hidden_panel">
						<button type="button" id="media_modal_close" class="media_modal_close">
							<span class="">
								<span class="material-icons">close</span>
							</span>
						</button>
						<div class="edit_modal_content">
							<div id="media_title" class="media_left_zero"><h1 id="edit_title"></h1></div>
							<div id="media_router" class="media_left_zero">
								<div class="table_row">
									
								</div>
							</div>
							<div id="media_frame_content" class="media_left_zero content_top_48">
								
							</div>
							<div id="frame_toolbar" class="media_frame_toolbar media_left_zero">
								<div class="media_toolbar">
									<div class="media_toolbar_primary">
										<button id="mail_message_save_button" type="button" class="button media-button button-primary button-large media-button-insert"><?php _e("Save", 'booking-package'); ?></button>
									</div>
								</div>
							</div>
							
						</div>
						
					</div>
					
					<div id="blockPanel" class="edit_modal_backdrop hidden_panel"></div>
					
					<div id="dialogPanel" class="hidden_panel">
						<div class="blockPanel"></div>
						<div class="confirmPanel">
							<div class="subject"><?php _e("Title", 'booking-package'); ?></div>
							<div class="body"><?php _e("Message", 'booking-package'); ?></div>
							<div class="buttonPanel">
								<button id="dialogButtonYes" type="button" class="yesButton button button-primary"><?php _e("Yes", 'booking-package'); ?></button>
								<button id="dialogButtonNo" type="button" class="noButton button button-primary"><?php _e("No", 'booking-package'); ?></button>
							</div>
						</div>
					</div>
					
					<div id="google_calendar_api" class="hidden_panel">
						
						<div>
							
						</div>
						
					</div>
					<!--
					<div id="loadingPanel" class="loading_modal_backdrop hidden_panel"><img src="<?php print plugin_dir_url( __FILE__ ); ?>images/loading_0.gif"></div>
					-->
					<div id="loadingPanel" class="">
						<div class="loader">
							<svg viewBox="0 0 64 64" width="64" height="64">
								<circle id="spinner" cx="32" cy="32" r="28" fill="none"></circle>
							</svg>
						</div>
					</div>
					<select id="timezone_choice" class="hidden_panel">
						<?php
							echo wp_timezone_choice($this->getTimeZone());
						?>
					</select>
					
					<?php
						$this->upgradeButton($isExtensionsValid, false);
					?>
					
				</div>
				
				
				
			<?php
			
			$load_end_time = microtime(true) - $load_start_time;
			echo '<!-- Load time: ' . $load_end_time . ' -->';
			
		}
		
		public function databaseUpdateErrors($path = '', $retry = true) {
			
			if (array_key_exists('updateDatabaseTable', $_POST) === true && intval($_POST['updateDatabaseTable']) === 1) {
				
				if (check_admin_referer($this->prefix . "_databaseUpdateErrors", 'booking_package_nonce') !== false) {
					
					$database = new booking_package_database($this->prefix, $this->db_version);
					$queries = $database->create();
					
				}
				
			}
			
			$queries = get_option('_' . $this->prefix . 'databaseUpdateErrors', null);
			if (is_null($queries)) {
				
				$queries = array('tables' => array(), 'columns' => array());
				
			} else {
				
				$queries = json_decode($queries, true);
				
			}
			
			if (count($queries['tables']) > 0 || count($queries['columns']) > 0) {
				
				print '<div id="booking_package_databaseUpdateErrors">';
				print '<div class="title">Table updates in the database have failed</div>';
				print '<p>Please execute the following queries using a database client tool such as phpMyAdmin:</p>';
				print '<div class="queries">';
				foreach ((array) $queries['tables'] as $key => $value) {
					
					print '<code id="tables_' . $key . '" class="query">' . $value . '</code><span id="copy_tables_' . $key . '" title="Copy" class="material-icons copy_icon">content_copy</span>';
					
				}
				
				foreach ((array) $queries['columns'] as $key => $value) {
					
					print '<code id="columns_' . $key . '" class="query">' . $value . '</code><span id="copy_columns_' . $key . '" title="Copy" class="material-icons copy_icon">content_copy</span>';
					
				}
				
				print '</div>';
				if ($retry === true) {
					
					$admin_url = admin_url() . 'admin.php?' . $path;
					print '<form method="POST" action="' . $admin_url . '">';
					print '<input type="hidden" name="updateDatabaseTable" value="1" >';
					wp_nonce_field($this->prefix . "_databaseUpdateErrors", 'booking_package_nonce');
					print '<input type="submit" value="' . __('Retry', 'booking-package') . '" class="media-button button-primary button-large media-button-insert deleteButton">';
					print '</form>';
					
				}
				
				print '<p> Last Update: ' . $queries['lastUpdate'] . '</p>';
				print '</div>';
				
			}
			
		}
		
		public function update_data($key, $value) {
			
			if (get_option($key) === false) {
							
				add_option($key, $value);
					
			} else {
				
				update_option($key, $value);
				
			}
			
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
		
		public function wp_ajax_booking_package(){
			
			$_POST['mode'] = sanitize_text_field( esc_html($_POST['mode']) );
			if (isset($_POST['nonce']) && check_ajax_referer($this->action_control . "_ajax", 'nonce')) {
				
				$response = $this->selectedMode();
				if ($this->getPhpVersion() <= 5.4) {
					
					print json_encode($response);
					
				} else {
					
					print json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
					
				}
				
				
			}
			
			die();
			
		}
		
		public function selectedMode(){
			
			$response = array('status' => 'error', 'mode' => $_POST['mode']);
			
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
        	
        	if ($_POST['mode'] == 'getUsersBookedList') {
        		
        		$response = $schedule->getUsersBookedList($_POST['user_id'], $_POST['offset'], false);
        		
        	}
			
			if ($_POST['mode'] == 'createUser') {
				
				$response = $schedule->createUser(1, null);
				
			}
			
			if ($_POST['mode'] == 'updateUser') {
				
				$response = $schedule->updateUser(1, null);
				
			}
			
			if ($_POST['mode'] == 'getMembers') {
				
				$response = $schedule->get_users($_POST['authority'] ,$_POST['offset'], $_POST['number']);
				
			}
			
			if ($_POST['mode'] == 'deleteUser') {
				
				$response = $schedule->deleteUser(1);
				
			}
			
			if ($_POST['mode'] == 'getRegularHolidays') {
				
				$startOfWeek = get_option('start_of_week', 0);
				$response = $schedule->getRegularHolidays($_POST['month'], $_POST['year'], $_POST['accountKey'], $startOfWeek, false);
				
			}
			
			if ($_POST['mode'] == 'updateRegularHolidays') {
				
				$response = $schedule->updateRegularHolidays();
				
			}
			
			if ($_POST['mode'] == 'setting') {
				
				$response = $setting->update($_POST);
				$response['status'] = 'success';
				
			}
			
			if ($_POST['mode'] == 'updateMemberSetting') {
				
				$response = $setting->updateMemberSetting();
				
			}
			
			if ($_POST['mode'] == 'refreshToken') {
				
				if (isset($_POST['accountKey'])) {
					
					$response = $schedule->refreshIcalToken($_POST['accountKey']);
					
				} else {
					
					$response = $setting->refreshToken($_POST['key']);
					
				}
				
			}
			
			if ($_POST['mode'] == 'getIcalToken') {
				
				$response = $schedule->getIcalToken($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateIcalToken') {
				
				$response = $schedule->updateIcalToken($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'getCourseList') {
				
				$response = $setting->getCourseList($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == $this->prefix.'addCourse') {
				
				$response = $setting->addCourse($_POST);
				
			}
			
			if ($_POST['mode'] == $this->prefix.'updateCourse') {
				
				$response = $setting->updateCourse($_POST);
				
			}
			
			if ($_POST['mode'] == 'getCouponsList') {
				
				$response = $setting->getCouponsList($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addCoupons') {
				
				$response = $setting->addCoupons($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteCouponsItem') {
				
				$response = $setting->deleteCouponsItem($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateCoupons') {
				
				$response = $setting->updateCoupons($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'getStaffList') {
				
				$response = $setting->getStaffList($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addStaff') {
				
				$response = $setting->addStaff($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateStaff') {
				
				$response = $setting->updateStaff($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteStaffItem') {
				
				$response = $setting->deleteStaffItem($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'changeStaffRank') {
				
				$response = $setting->changeStaffRank($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'getGuestsList') {
				
				$response = $setting->getGuestsList($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateGuests') {
				
				$setting->setGuestForDayOfTheWeekRates($this->guestForDayOfTheWeekRates);
				$response = $setting->updateGuests($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addGuests') {
				
				$response = $setting->addGuests($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteGuestsItem') {
				
				$response = $setting->deleteGuestsItem($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'changeGuestsRank') {
				
				$response = $setting->changeGuestsRank($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'getOptionsForHotel') {
				
				$response = $setting->getOptionsForHotel($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addOptionsForHotel') {
				
				$response = $setting->addOptionsForHotel($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateOptionsForHotel') {
				
				$response = $setting->updateOptionsForHotel($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteOptionsForHotel') {
				
				$response = $setting->deleteOptionsForHotel($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'changeOptionsForHotelRank') {
				
				$response = $setting->changeOptionsForHotelRank($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'copyCourse') {
				
				$response = $setting->copyCourse($_POST);
				
			}
			
			if ($_POST['mode'] == 'deleteCourse') {
				
				$response = $setting->deleteCourse($_POST);
				
			}
			
			if ($_POST['mode'] == 'changeCourseRank') {
				
				$response = $setting->changeCourseRank($_POST);
				
			}
			
			if ($_POST['mode'] == 'addUserInput') {
				
				$response = $setting->updateUserInputFields($_POST['mode']);
				
			}
			
			if ($_POST['mode'] == 'editUserInput') {
				
				$response = $setting->updateUserInputFields($_POST['mode']);
				
			}
			
			if ($_POST['mode'] == 'deleteUserInput') {
				
				$response = $setting->updateUserInputFields($_POST['mode']);
				
			}
			
			if ($_POST['mode'] == 'changedOrderUserInput') {
				
				$response = $setting->updateUserInputFields($_POST['mode']);
				
			}
			
			if ($_POST['mode'] == 'getUserInputFields') {
				
				$response = $setting->getUserInputFields();
				
			}
			
			if ($_POST['mode'] == 'getForm') {
				
				$response = $setting->getForm($_POST['accountKey'], false);
				
			}
			
			if ($_POST['mode'] == 'addForm') {
				
				$response = $setting->addForm($_POST);
				
			}
			
			if ($_POST['mode'] == 'updateForm') {
				
				$response = $setting->updateForm($_POST);
				
			}
			
			if ($_POST['mode'] == 'deleteFormItem') {
				
				$response = $setting->deleteFormItem($_POST);
				
			}
			
			if ($_POST['mode'] == 'changeFormRank') {
				
				$response = $setting->changeFormRank($_POST);
				
			}
			
			if ($_POST['mode'] == 'updataEmailMessageForCalendarAccount') {
				
				$response = $setting->updataEmailMessageForCalendarAccount();
				
			}
			
			if ($_POST['mode'] == 'upgradePlan') {
				
				$response = $setting->upgradePlan($_POST['type']);
				
			}
			
			if ($_POST['mode'] == 'resetSubscription') {
				
				$response = $setting->resetSubscription();
				
			}
			
			if ($_POST['mode'] == 'getEmailMessageList' && isset($_POST['accountKey'])) {
				
				$response = $setting->getEmailMessageList($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'scriptError') {
				
				$response = $schedule->scriptError($_POST);
				
			}
			
			if ($_POST['mode'] == 'getTemplateSchedule') {
				
				$response = $schedule->getTemplateSchedule($_POST['weekKey']);
				
			}
			
			if ($_POST['mode'] == 'getRangeOfSchedule') {
				
				$response = $schedule->getRangeOfSchedule($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateRangeOfSchedule') {
				
				$response = $schedule->updateRangeOfSchedule($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'getPublishedTimeSlots') {
				
				$response = $schedule->getPublishedTimeSlots();
				
			}
			
			if ($_POST['mode'] == 'deletePublishedSchedules') {
				
				$response = $schedule->deletePublishedSchedules(intval($_POST['accountKey']), $_POST['type']);
				
			}
			
			if ($_POST['mode'] == 'resetCustomizeLabels') {
				
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->resetCustomizeLabels($calendarAccount);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'updateCustomizeLabels') {
				
				$customizeLabels = json_decode(stripslashes($_POST['customizeLabels']), true);
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->updateCustomize($calendarAccount, 'customizeLabels', $customizeLabels);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'resetCustomizeButtons') {
				
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->resetCustomizeButtons($calendarAccount);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'updateCustomizeButtons') {
				
				$customizeButtons = json_decode(stripslashes($_POST['customizeButtons']), true);
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->updateCustomize($calendarAccount, 'customizeButtons', $customizeButtons);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'resetCustomizeLayouts') {
				
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->resetCustomizeLayouts($calendarAccount);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'updateCustomizeLayouts') {
				
				$customizeLayouts = json_decode(stripslashes($_POST['customizeLayouts']), true);
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$schedule->updateCustomize($calendarAccount, 'customizeLayouts', $customizeLayouts);
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'changeCustomizeTheme') {
				
				$calendarAccount = $schedule->getCalendarAccount($_POST['accountKey']);
				$theme = $schedule->changeCustomizeTheme($calendarAccount, $_POST['selectedTheme']);
				$calendarAccounts = $schedule->getCalendarAccountListData();
				for ($i = 0; $i < count($calendarAccounts); $i++) {
					
					if ($calendarAccounts[$i]['key'] == $_POST['accountKey']) {
						
						$calendarAccounts[$i]['customizeLayouts'] = $theme;
						break;
						
					}
					
				}
				return $calendarAccounts;
				
			}
			
			if ($_POST['mode'] == 'getCalendarAccountListData') {
				
				$response = $schedule->getCalendarAccountListData();
				
			}
			
			if ($_POST['mode'] == 'addCalendarAccount') {
				
				$addedCalendarAccount = $schedule->addCalendarAccount();
				$calendarAccount = $schedule->getCalendarAccount($addedCalendarAccount['accountKey']);
				$setting->getEmailMessageList($addedCalendarAccount['accountKey'], $calendarAccount['name']);
				$response = $addedCalendarAccount['getCalendarAccountListData'];
				
			}
			
			if ($_POST['mode'] == 'updateCalendarAccount') {
				
				$response = $schedule->updateCalendarAccount();
				
			}
			
			if ($_POST['mode'] == 'updateAccountFunction') {
				
				$response = $schedule->updateAccountFunction($_POST['accountKey'], $_POST['name'], $_POST['value']);
				
			}
			
			if ($_POST['mode'] == 'deleteCalendarAccount') {
				
				$response = $schedule->deleteCalendarAccount();
				
			}
			
			if ($_POST['mode'] == 'createCloneCalendar') {
				
				$response = $schedule->createCloneCalendar();
				
			}
			
			if ($_POST['mode'] == 'getAccountScheduleData') {
				
				if (isset($_POST['createSchedules']) && intval($_POST['createSchedules']) == 1) {
					
					$schedule->insertAccountSchedule(date('n'), date('j'), date('Y'), $_POST['accountKey']);
					
				}
				$response = $schedule->getAccountScheduleData(true);
				
			}
			
			if ($_POST['mode'] == 'updateAccountTemplateSchedule') {
				
				$list = $schedule->updateAccountTemplateSchedule();
				#$response['templateSchedules'] = $list;
				$response['status'] = 'success';
				$response['list'] = $list;
				
			}
			
			if ($_POST['mode'] == 'deletePerfectPublicSchedule') {
				
				$schedule->deletePerfectPublicSchedule();
				$response['status'] = 'success';
				
			}
			
			if ($_POST['mode'] == 'addAccountSchedule') {
				
				$schedule->addAccountSchedule();
				$response['status'] = 'success';
				
			}
			
			if ($_POST['mode'] == 'updateAccountSchedule') {
				
				$list = $schedule->updateAccountSchedule();
				$response['status'] = 'success';
				
			}
			
			if ($_POST['mode'] == $this->prefix.'getReservationData') {
				
				if (isset($_POST['accountKey'])) {
					
					if (isset($_POST['createSchedules']) && intval($_POST['createSchedules']) == 1) {
						
						$schedule->insertAccountSchedule(date('n'), date('j'), date('Y'), $_POST['accountKey']);
						
					}
					$expire = date('U') + (30 * 24 * 3600);
					setcookie($this->prefix.'accountKey', $_POST['accountKey'], $expire);
					$response = $schedule->getReservationData($_POST['month'], $_POST['day'], $_POST['year']);
					$response['formData'] = $setting->getForm($_POST['accountKey'], true);
					$response['courseList'] = $setting->getCourseList($_POST['accountKey']);
					$response['account'] = $schedule->getCalendarAccount($_POST['accountKey']);
					if (($response['account']['type'] == 'day' && intval($response['account']['guestsBool']) == 1) || $response['account']['type'] == 'hotel') {
						
						$response['guestsList'] = $setting->getGuestsList($_POST['accountKey'], true);
						if (count($response['guestsList']) == 0) {
							
							$response['account']['guestsBool'] = 0;
							
						}
						
					} else {
						
						$response['guestsList'] = array();
						
					}
					
					if ($response['account']['type'] == 'hotel') {
						
						$response['hotelOptions'] = $setting->getOptionsForHotel($_POST['accountKey'], true, true);
						
					}
					
					$response['taxes'] = $setting->getTaxes($_POST['accountKey'], 'yes');
					$emailEnableList = $setting->getEmailMessageList($_POST['accountKey']);
					$response['emailEnableList'] = $emailEnableList['emailMessageList'];
					
				} else {
					
					$response = $schedule->getReservationData($_POST['month'], $_POST['day'], $_POST['year']);
					
				}
				
			}
			
			if ($_POST['mode'] == 'sendBooking') {
				
				$response = $schedule->sendBooking(true);
				$response['guestsList'] = $setting->getGuestsList($_POST['accountKey'], true);
				$response['account'] = $schedule->getCalendarAccount($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteBookingData') {
				
				$bookingDetailsOnVisitor = $schedule->getBookingDetailsOnVisitor($_POST['key'], $_POST['token']);
				if ($bookingDetailsOnVisitor['status'] == 'error') {
					
					$response = $bookingDetailsOnVisitor;
					
				} else {
					
					$myBookingDetails = $bookingDetailsOnVisitor['details'];
					$response = $schedule->deleteBookingData($_POST['key'], $myBookingDetails['accountKey'], false, true, $_POST['sendEmail']);
					$response['guestsList'] = $setting->getGuestsList($_POST['accountKey'], true);
					$response['account'] = $schedule->getCalendarAccount($_POST['accountKey']);
					
				}
	    		
			}
			
			if ($_POST['mode'] == 'updateBooking') {
				
				$response = $schedule->updateBooking(true);
				$response['guestsList'] = $setting->getGuestsList($_POST['accountKey'], true);
				$response['account'] = $schedule->getCalendarAccount($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateStatus') {
				
				$response = $schedule->updateStatus($_POST['key'], $_POST['token'], $_POST['newStatus']);
				if (intval($_POST['reload']) == 1) {
					$response['guestsList'] = $setting->getGuestsList($_POST['accountKey'], true);
					$response['account'] = $schedule->getCalendarAccount($_POST['accountKey']);
				}
					
			}
			
			if ($_POST['mode'] == 'lookingForSubscription') {
				
				$response = $setting->lookingForSubscription( sanitize_text_field($_POST['customer_id_for_subscriptions']), sanitize_text_field($_POST['subscriptions_id_for_subscriptions']) );
				
			}
			
			if ($_POST['mode'] == 'deleteSubscription') {
				
				$response = $schedule->deleteSubscription($_POST['product'], $_POST['userId']);
				
			}
			
			if ($_POST['mode'] == 'getSubscriptions') {
				
				$response = $setting->getSubscriptions();
				
			}
			
			if ($_POST['mode'] == 'addSubscriptions') {
				
				$response = $setting->addSubscriptions();
				
			}
			
			if ($_POST['mode'] == 'updateSubscriptions') {
				
				$response = $setting->updateSubscriptions();
				
			}
			
			if ($_POST['mode'] == 'changeSubscriptionsRank') {
				
				$response = $setting->changeSubscriptionsRank();
				
			}
			
			if ($_POST['mode'] == 'getTaxes') {
				
				$response = $setting->getTaxes($_POST['accountKey'], 'no');
				
			}
			
			if ($_POST['mode'] == 'addTaxes') {
				
				$response = $setting->addTaxes($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addTax') {
				
				$response = $setting->addTax($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'addExtraCharge') {
				
				$response = $setting->addExtraCharge($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteTax') {
				
				$response = $setting->deleteTax($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteExtraCharge') {
				
				$response = $setting->deleteExtraCharge($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateTax') {
				
				$response = $setting->updateTax($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateExtraCharge') {
				
				$response = $setting->updateExtraCharge($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateTaxes') {
				
				$response = $setting->updateTaxes($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'deleteTaxes') {
				
				$response = $setting->deleteTaxes($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'changeTaxesRank') {
				
				$response = $setting->changeTaxesRank($_POST['accountKey']);
				
			}
			
			if ($_POST['mode'] == 'updateCss') {
				
				$response = $setting->updateCss('front_end.css');
				
			}
			
			if ($_POST['mode'] == 'updateJavaScript') {
				
				$response = $setting->updateJavaScript('front_end.js');
				
			}
			
			if ($_POST['mode'] == 'getBlockEmailLists') {
				
				$response = $setting->getBlockEmailLists($schedule);
				
			}
			
			if ($_POST['mode'] == 'addBlockEmail') {
				
				$response = $setting->addBlockEmail($_POST['email'], $schedule);
				
			}
			
			if ($_POST['mode'] == 'deleteBlockEmail') {
				
				$response = $setting->deleteBlockEmail($_POST['key'], $schedule);
				
			}
			
			#print json_encode($response);
			return $response;
			
		}
		
		public function getDownloadCSV() {
			
			$download = false;
			if (current_user_can('manage_options') && current_user_can('edit_pages')) {
				
				$download = true;
				
			}
			
			$roles = array($this->prefix . 'manager', $this->prefix . 'editor');
			for ($i = 0; $i < count($roles); $i++) {
				
				if (current_user_can($roles[$i]) === true) {
					
					$download = true;
					break;
					
				}
				
			}
			
			/** if (!current_user_can('manage_options') && !current_user_can('edit_pages') && (!defined('DOING_AJAX') || !DOING_AJAX)) { **/
			if ($download === false && (!defined('DOING_AJAX') || !DOING_AJAX)) {
				
				wp_die('You are not allowed to access this part of the site.');
				
			} else {
				
				$nonce = $_POST['nonce'];
				if (!wp_verify_nonce( $nonce, $this->action_control . "_download")) {
					
					die('Security check'); 
					
				} else {
					
					global $wpdb;
					$characterCodeOfDownloadFile = get_option($this->prefix . "characterCodeOfDownloadFile", "UTF-8");
					header("Content-Type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"List.csv\"");
					$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
					$data = $schedule->getDownloadCSV();
					$str = $data['csv'];
					if ($characterCodeOfDownloadFile != 'UTF-8' && function_exists('mb_convert_encoding')) {
						
						$str = mb_convert_encoding($data['csv'], $characterCodeOfDownloadFile, 'UTF-8');
						
					}
					
					echo $str;
					
				}
				die();
				
			}
			
		}
		
		public function activatePaidSubscription() {
			
			$setting = $this->setting;
			$setting->lookingForSubscription( sanitize_text_field($_POST['customer_id_for_subscriptions']), sanitize_text_field($_POST['subscriptions_id_for_subscriptions']) );
			header('Location: ' . admin_url("admin.php?page=" . $this->plugin_name . "_setting_page" . "&tab=subscriptionLink"));
			die();
			
		}
		
		public function updatePaidSubscription() {
			
			$setting = $this->setting;
			$setting->updatePaidSubscription(sanitize_text_field(esc_html($_GET['unique'])));
			header('Location: ' . admin_url("admin.php?page=" . $this->plugin_name . "_setting_page" . "&tab=subscriptionLink"));
			die();
			
		}
		
		private function getExtensionsValid($validate = false, $countExpiration = false) {
			
			$setting = $this->setting;
			$setting->validateWpSite($validate);
			if (is_null($this->isExtensionsValid)) {
				
				$this->isExtensionsValid = $setting->getSiteStatus();
				if (get_option($this->prefix . 'blocksEmail') === false) {
					
					add_option($this->prefix . 'blocksEmail', 0);
					
				}
				
				if ($this->isExtensionsValid === true) {
					
					$setting->updateRolesOfPlugin();
					update_option($this->prefix . 'blocksEmail', 1);
					
					
				} else {
					
					$setting->deleteRolesOfPlugin();
					update_option($this->prefix . 'blocksEmail', 0);
					
				}
				
			} else {
				
				$this->isExtensionsValid = 0;
				
			}
			
			return $this->isExtensionsValid;
			
		}
		
		public function getSubscriptions() {
			
			$setting = $this->setting;
			$subscriptions = $setting->upgradePlan('get');
			unset($subscriptions["status"]);
			/**
			var_dump($subscriptions);
			foreach((array) $subscriptions as $key => $value){
				
				$subscriptions[$key] = 0;
				
			}
			**/
			return $subscriptions;
			
		}
		
		public function upgradeButton($isExtensionsValid = false, $daiplay = true){
			
			if ($isExtensionsValid === true) {
				
				return $isExtensionsValid;
				
			}
			
			if ($this->is_owner_site == 0) {
				
				return false;
				
			}
			
			$uri = plugin_dir_url( __FILE__ );
			$parse_url = parse_url($uri);
			$locale = get_locale();
			$dictionary = $this->getDictionary("Upgrade_js", $this->plugin_name);
			
			$timezone = $this->timezone;
			$upgradeDetail = array(
				"timeZone" => $timezone, 
				"local" => get_locale(), 
				"site" => get_site_url(), 
				"locale" => $locale, 
				"plugin_v" => $this->plugin_version
			);
			
			$subscriptions = $this->getSubscriptions();
			foreach ((array) $subscriptions as $key => $value) {
				
				$upgradeDetail[$key] = $value;
				
			}
			
			$upgradeDetail['secure'] = 0;
			if ($parse_url['scheme'] == 'https') {
				
				$upgradeDetail['secure'] = 1;
				
			}
			
			#$pluginUrl = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			#$upgradeDetail['pluginUrl'] = $pluginUrl;
			$upgradeDetail['pluginUrl'] = get_site_url();
			$hidden_class = '';
			if ($daiplay === false) {
				
				$hidden_class = ' class="hidden_panel"';
				
			}
			echo '<form id="booking_packaeg_paid_subscription" action="' . BOOKING_PACKAGE_UPGRRADE_URL . '" method="post" style="" ' . $hidden_class . '>';
			$posts = array("extension_url", "local", "timeZone", "site", "pluginUrl", "plugin_v");
			for ($i = 0; $i < count($posts); $i++) {
				
				$key = $posts[$i];
				if (isset($upgradeDetail[$key])) {
					
					echo '<input type="hidden" name="'.$key.'" value="'.$upgradeDetail[$key].'">';
					
				}
				
			}
			
			echo '<input type="hidden" name="getUpgradeUrl" value="' . 'https://saasproject.net/api/1.7/' . '">';
			echo '<input id="upgradeSubmit" type="submit" class="media-button button-primary button-large media-button-insert" value="' . __('Get a paid subscription', 'booking-package') . '">';
			echo '</form>';
			
		}
		
		public function ical_feeds(){
			
			$id = 'all';
			if(isset($_GET['id'])) {
				
				$id = intval($_GET['id']);
				
			}
			
			if (!isset($_GET['site'])) {
				
				$_GET['site'] = false;
				
			}
			
			$ical = new booking_package_iCal($this->prefix, $this->plugin_name, $this->currencies);
			$valid = $ical->isValid($_GET['ical'], $_GET['site'], $id);
			if ($valid !== false) {
				
				die();
				
			}
			
		}
		
		public function webhook(){
			
			$target = sanitize_text_field($_GET["weebhook"]);
			$HTTP_X_GOOG_CHANNEL_ID = sanitize_text_field($_SERVER['HTTP_X_GOOG_CHANNEL_ID']);
			$HTTP_X_GOOG_CHANNEL_TOKEN = sanitize_text_field($_SERVER['HTTP_X_GOOG_CHANNEL_TOKEN']);
			
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$googleCalendarDeteils = $schedule->lookingForGoogleCalendarId($HTTP_X_GOOG_CHANNEL_ID);
			
			#lookingForGoogleCalendarId
			#if(get_option($this->prefix."id_for_google_webhook") == $HTTP_X_GOOG_CHANNEL_ID && get_option($this->prefix."token_for_google_webhook") == $HTTP_X_GOOG_CHANNEL_ID){
			
			if($googleCalendarDeteils['idForGoogleWebhook'] == $HTTP_X_GOOG_CHANNEL_ID){
				
				$webhook = new booking_package_webhook($this->prefix, $this->plugin_name);
				$webhook->catchWebhook($target, $HTTP_X_GOOG_CHANNEL_ID, $_POST);
				exit;
				
			}
			
		}
		
		public function localizeScript($mode) {
            
            if (isset($_GET['debug']) && intval($_GET['debug']) == 1) {
            	
            	$this->dubug_javascript = 1;
            	
            }
            
            $siteToken = get_option('_' . $this->prefix . 'siteToken', false);
            if ($siteToken === false) {
				
				$siteToken = hash('ripemd160', date('U'));
				add_option('_' . $this->prefix . 'siteToken', $siteToken);
				
			}
            
            $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
            $setting = $this->setting;
            $locale = get_locale();
            $javascriptSyntaxErrorNotification = get_option($this->prefix."javascriptSyntaxErrorNotification", 1);
            $clockFormat = get_option($this->prefix . "clock", "24");
            $dateFormat = get_option($this->prefix . "dateFormat", "0");
			$positionOfWeek = get_option($this->prefix . "positionOfWeek", "before");
			$positionTimeDate = get_option($this->prefix . "positionTimeDate", "dateTime");
            
            $javascriptFileslist = array();
            $dir = plugin_dir_path( __FILE__ ).'js/';
			if ($handle = opendir($dir)) {
				
				while (($file = readdir($handle)) !== false) {
					
					if (filetype($path = $dir.$file) == "file") {
						
						array_push($javascriptFileslist, $file);
						
					}
					
				}
				
			}
            
            $startOfWeek = get_option('start_of_week', 0);
            $currencies = $setting->getCurrencies();
            $numberFormatter = 0;
            if (class_exists('NumberFormatter') === true) {
            	
            	$numberFormatter = 1;
            	
            }
            
            $localize_script = array();
			if ($mode == 'booking_package_booked_customers') {
				
				$dashboardRequest = array('status' => 0);
				if (isset($_GET['key']) && isset($_GET['calendar']) && isset($_GET['month']) && isset($_GET['day']) && isset($_GET['year'])) {
					
					$dashboardRequest = array(
						'status' => 1, 
						'key' => intval($_GET['key']), 
						'calendar' => intval($_GET['calendar']), 
						'month' => intval($_GET['month']), 
						'day' => intval($_GET['day']), 
						'year' => intval($_GET['year'])
					);
					
				}
				
				
				$list = $setting->getList();
				$courseList = $setting->getCourseList();
				$emailMessageList = $setting->getEmailMessage(array('enable'));
				//$formData = $setting->getForm();
				
				$courseBool = get_option($this->prefix . "courseBool", "false");
				$courseName = get_option($this->prefix . "courseName", "false");
				
				$mobile = 0;
				if (wp_is_mobile() === true) {
					
					$mobile = 1;
					
				}
				
				$list['General'][$this->prefix . 'clock']['value'] = $this->changeTimeFormat($list['General'][$this->prefix . 'clock']['value']);
				
				$localize_script = array(
					'url' => admin_url('admin-ajax.php'), 
					'action' => $this->action_control, 
					'nonce' => wp_create_nonce($this->action_control."_ajax"), 
					'nonce_download' => wp_create_nonce($this->action_control."_download"), 
					'prefix' => $this->prefix,
					'courseBool' => $courseBool, 
					'courseName' => $courseName, 
					'year' => date('Y'), 
					'month' => date('m'), 
					'day' => date('d'), 
					'locale' => $locale,
					'courseList' => $courseList, 
					'country' => $list['General']['booking_package_country']['value'],
					'currency' => $list['General']['booking_package_currency']['value'],
					'currencies' => $currencies,
					'clock' => $list['General']['booking_package_clock']['value'],
					'dateFormat' => $dateFormat,
					'positionOfWeek' => $positionOfWeek,
					'positionTimeDate' => $positionTimeDate,
					'formData' => array(),
					'calendarAccountList' => $schedule->getCalendarAccountListData(),
					'is_mobile' => $this->is_mobile,
					'dashboardRequest' => $dashboardRequest,
					'bookingBool' => 1,
					'emailEnable' => $emailMessageList,
					'javascriptSyntaxErrorNotification' => $javascriptSyntaxErrorNotification,
					'javascriptFileslist' => $javascriptFileslist,
					'visitorSubscriptionForStripe' => $this->visitorSubscriptionForStripe,
					'startOfWeek' => $startOfWeek,
					'debug' => $this->dubug_javascript,
					'today' => date('Ymd'),
					'guestForDayOfTheWeekRates' => $this->guestForDayOfTheWeekRates,
					'numberFormatter' => $numberFormatter,
					'mobile' => $mobile,
				);
				
			} else if ($mode == 'schedule_page') {
				
				$this->setting->setGuestForDayOfTheWeekRates($this->guestForDayOfTheWeekRates);
				$courseData = $setting->getCourseData();
				$subscriptionsData = $setting->getSubscriptionsData();
				$formInputType = $setting->getFormInputType();
				$guestsInputType = $setting->guestsInputType();
				$couponsInputType = $setting->couponsInputType();
				$emailMessageList = $setting->getEmailMessage();
				$taxes = $setting->getTaxesData();
				
				if ($this->stopService == 0) {
					
					unset($courseData['stopService']);
					
				}
				
				if ($this->groupOfInputField == 0) {
					
					unset($formInputType['groupId']);
					
				}
				
				if ($this->expirationDateForTax == 0) {
					
					unset($taxes['expirationDate']);
					
				}
				
				$taxColumns = array();
				$extraChargeColumns = array();
				foreach ($taxes as $key => $tax) {
					
					if ($tax['gen'] === 'both' || intval($tax['gen']) === 2) {
						
						if ($tax['type'] === 'both' || $tax['type'] === 'tax') {
							
							$taxColumns[$key] = $tax;
							
						}
						
						if ($tax['type'] === 'both' || $tax['type'] === 'surcharge') {
							
							if ($key === 'scope') {
								
								if (array_key_exists('bookingEachGuests', $tax['valueList'])) {
									
									$tax['name'] = __('Range of extra charge', 'booking-package');
									unset($tax['valueList']['bookingEachGuests']);
									
								}
								
							}
							$extraChargeColumns[$key] = $tax;
							
						}
						
					}
					
				}
				$taxColumns['value']['name'] = __('Fixed tax amount', 'booking-package');
				$extraChargeColumns['value']['name'] = __('Extra charge', 'booking-package');
				
				$schedule->deleteOldDaysInSchedules();
				$timestamp = $schedule->getTimestamp();
				$courseBool = get_option($this->prefix . "courseBool", "false");
				$elementForCalendarAccount = $setting->getElementForCalendarAccount();
				$country = get_option($this->prefix . 'country' , 'US');
				if (strtolower($country) != 'jp') {
					
					unset($elementForCalendarAccount['paymentMethod']['valueList']['stripe_konbini']);
					
				}
				
				if ($this->messagingApp === 0) {
					
					unset($elementForCalendarAccount['messagingService']);
					
				}
				
				if ($this->visitorSubscriptionForStripe == 0) {
					
					unset($elementForCalendarAccount['subscriptionIdForStripe']);
					unset($elementForCalendarAccount['subscriptionIdForPayPal']);
					unset($elementForCalendarAccount['termsOfServiceForSubscription']);
					#unset($elementForCalendarAccount['enableTermsOfServiceForSubscription']);
					unset($elementForCalendarAccount['privacyPolicyForSubscription']);
					#unset($elementForCalendarAccount['enablePrivacyPolicyForSubscription']);
					
					
				}
				
				if ($this->multipleRooms == 0) {
					
					unset($elementForCalendarAccount['multipleRooms']);
					
				}
				
				if ($this->maxAndMinNumberOfGuests == 0) {
					
					unset($elementForCalendarAccount['minimum_guests']);
					unset($elementForCalendarAccount['maximum_guests']);
					
				}
				
				
				$pages = get_pages(array('meta_key' => 'booking-package', 'meta_value' => 'front-end'));
				
				$insertCustomPages = array(0 => array('key' => 0, 'name' => __('Disabled', 'booking-package')));
				foreach ((array) $pages as $key => $value) {
					
					$insertCustomPages[$value->ID] = array('key' => $value->ID, 'name' => $value->post_title);
					
				}
				for ($i = 0; $i < count($elementForCalendarAccount['insertCustomPage']['valueList']); $i++) {
					
					$elementForCalendarAccount['insertCustomPage']['valueList'][$i]['valueList'] = $insertCustomPages;
					
				}
				$elementForCalendarAccount['redirect_Page']['valueList'][1]['valueList'] = $insertCustomPages;
				
				$defaultEmail = array(
					'email_to' => get_option($this->prefix . "email_to", ''),
					'email_from' => get_option($this->prefix . "email_from", ''),
					'email_from_title' => get_option($this->prefix . "email_title_from", ''),
				);
				
				$localize_script = array(
					'url' => admin_url('admin-ajax.php'), 
					'action' => $this->action_control, 
					'nonce' => wp_create_nonce($this->action_control."_ajax"), 
					'prefix' => $this->prefix,
					'courseBool' => $courseBool, 
					'year' => date('Y'), 
					'month' => date('m'), 
					'locale' => $locale, 
					'clockFormat' => $clockFormat,
					'dateFormat' => $dateFormat, 
					'positionOfWeek' => $positionOfWeek,
					'positionTimeDate' => $positionTimeDate,
					'list' => array(), 
					'formInputType' => $formInputType, 
					'courseData' => $courseData,
					'subscriptionsData' => $subscriptionsData,
					'taxesData' => $taxes,
					'optionsForHotelData' => $setting->getOptionsForHotelData(),
					'elementForCalendarAccount' => $elementForCalendarAccount, 
					'guestsInputType' => $guestsInputType,
					'couponsInputType' => $couponsInputType,
					'is_mobile' => $this->is_mobile,
					'javascriptSyntaxErrorNotification' => $javascriptSyntaxErrorNotification,
					'javascriptFileslist' => $javascriptFileslist,
					'visitorSubscriptionForStripe' => $this->visitorSubscriptionForStripe,
					'timestamp' => $timestamp,
					'startOfWeek' => $startOfWeek,
					'currency' => get_option($this->prefix."currency", "usd"),
					'currencies' => $currencies,
					'timezone' => get_option($this->prefix . "timezone", "UTC"),
					'debug' => $this->dubug_javascript,
					'defaultEmail' => $defaultEmail,
					'siteToken' => $siteToken,
					'servicesExcludedGuestsInEmail' => $this->servicesExcludedGuestsInEmail,
					'newTaxesAndExtraCharges' => $this->newTaxesAndExtraCharges,
					'taxColumns' => $taxColumns,
					'extraChargeColumns' => $extraChargeColumns,
					'enabledStaff' => $this->enabledStaff,
					'themes' => $this->themes,
					'customizeLayouts' => $this->customizeLayouts,
					'numberFormatter' => $numberFormatter,
				);
				
			} else if ($mode == 'setting_page') {
				
				$list = $setting->getList();
				if ($this->messagingApp === 0) {
					
					unset($list['Messaging Services']);
					
				}
				
				
				$booking_sync = $setting->getBookingSyncList();
				$member_setting = $setting->getMemberSetting(true);
				$emailMessageList = $setting->getEmailMessage();
				#$courseList = $setting->getCourseList();
				#$courseData = $setting->getCourseData();
				#$formInputType = $setting->getFormInputType();
				$countries = json_decode(file_get_contents(plugin_dir_path( __FILE__ ).'lib/Countries_with_Regional_Codes.json'), true);
				$list['General'][$this->prefix.'country']['valueList'] = $countries;
				ksort($list["General"][$this->prefix . "currency"]["valueList"]);
				if (isset($booking_sync['Google_Calendar'])) {
					
					$booking_sync['Google_Calendar']['parse_url'] = parse_url(get_home_url());
					
				}
				
				$timezone = get_option($this->prefix . "timezone", null);
				if (is_null($timezone)) {
					
					$timezone = get_option('timezone_string', 'UTC');
					$list['General'][$this->prefix . 'timezone']['value'] = $timezone;
					
				} else {
					
					$list['General'][$this->prefix . 'timezone']['value'] = $timezone;
					
				}
				
				$list['General'][$this->prefix . 'clock']['value'] = $this->changeTimeFormat($list['General'][$this->prefix . 'clock']['value']);
				$booking_sync['iCal']['booking_package_ical_token']['home'] = get_home_url();
				if (is_null($booking_sync['iCal']['booking_package_ical_token']['value']) === true || strlen($booking_sync['iCal']['booking_package_ical_token']['value']) == 0) {
					
					$tokenResponse = $setting->refreshToken("booking_package_ical_token");
					$booking_sync['iCal']['booking_package_ical_token']['value'] = $tokenResponse['token'];
					
				}
				
				$return_url = admin_url() . '?' . http_build_query(
					array(
						'mode' => 'booking-package-update-paid-subscription',
						'unique' => get_option('_' . $this->prefix . 'unique', false),
					)
				);
				
				$localize_script = array(
					'url' => admin_url('admin-ajax.php'), 
					'action' => $this->action_control, 
					'nonce' => wp_create_nonce($this->action_control."_ajax"), 
					'prefix' => $this->prefix,
					'locale' => $locale,
					'list' => $list, 
					'bookingSyncList' => $booking_sync, 
					'memberSetting' => $member_setting,
					"extension_url" => BOOKING_PACKAGE_EXTENSION_URL, 
					'dateFormat' => $dateFormat, 
					'positionOfWeek' => $positionOfWeek,
					'positionTimeDate' => $positionTimeDate,
					/**
					'courseData' => $courseData, 
					'courseList' => $courseList, 
					'formInputType' => $formInputType, 
					'formData' => $formData, 
					'emailMessageList' => $emailMessageList,
					**/
					'is_mobile' => $this->is_mobile,
					'javascriptSyntaxErrorNotification' => $javascriptSyntaxErrorNotification,
					'javascriptFileslist' => $javascriptFileslist,
					'visitorSubscriptionForStripe' => $this->visitorSubscriptionForStripe,
					'regularHolidays' => $schedule->getRegularHolidays(date('m'), date('Y'), 'share', $startOfWeek, false),
					'nationalHolidays' => $schedule->getRegularHolidays(date('m'), date('Y'), 'national', $startOfWeek, false),
					'startOfWeek' => $startOfWeek,
					'is_owner_site' => $this->is_owner_site,
					'siteToken' => $siteToken,
					'return_url' => $return_url,
					'debug' => $this->dubug_javascript,
					'subscription_status' => $setting->getSubscriptionStatus(),
					'subscriptionLink' => admin_url("admin.php?page=" . $this->plugin_name . "_setting_page" . "&tab=subscriptionLink"),
					'numberFormatter' => $numberFormatter,
					'my_subscription_url' => BOOKING_PACKAGE_MY_SUBSCRIPTION_URL,
				);
				
			} else if ($mode == 'booking_package_front_end') {
				
				$list = $setting->getList();
				$courseList = $setting->getCourseList();
				$emailMessageList = $setting->getEmailMessage(array('enable'));
				$installed_ver = get_option($this->prefix . "db_version");
				//$formData = $setting->getForm();
				
				$courseBool = get_option($this->prefix . "courseBool", "false");
				$courseName = get_option($this->prefix . "courseName", "false");
				$autoWindowScroll = get_option($this->prefix . "autoWindowScroll", 1);
				$list['General'][$this->prefix . 'clock']['value'] = $this->changeTimeFormat($list['General'][$this->prefix . 'clock']['value']);
				
				$url = get_home_url();
				if (substr($url, -1) !== '/') {
					
					$url .= '/';
					
				}
				
				if ($this->ajaxUrl === 'ajax') {
					
					$url = plugins_url() . '/booking-package/ajax.php';
					
				} else if ($this->ajaxUrl === 'admin-ajax') {
					
					$url = admin_url('admin-ajax.php');
					
				}
				
				$nonce = wp_create_nonce($this->action_public . "_ajax");
				if ($this->ajaxNonceFunction === 'custom_nonce_validation') {
					
					$nonce = $this->nonce->create($this->action_public . "_ajax");
					
				}
				
				$localize_script = array(
					'url' => $url, 
					'action' => $this->action_public, 
					'nonce' => $nonce, 
					'prefix' => $this->prefix,
					'plugin_name' => $this->plugin_name,
					'courseBool' => $courseBool, 
					'year' => date('Y'), 
					'month' => date('m'), 
					'day' => date('d'), 
					'courseList' => $courseList, 
					'country' => $list['General']['booking_package_country']['value'],
					'currency' => $list['General']['booking_package_currency']['value'],
					'currencies' => $currencies,
					'clock' => $list['General']['booking_package_clock']['value'],
					'headingPosition' => $list['Design']['booking_package_headingPosition']['value'],
					'googleAnalytics' => $list['General']['booking_package_googleAnalytics']['value'],
					'dateFormat' => $dateFormat,
					'positionOfWeek' => $positionOfWeek,
					'positionTimeDate' => $positionTimeDate,
					'formData' => array(),
					'locale' => $locale,
					'is_mobile' => $this->is_mobile,
					'javascriptSyntaxErrorNotification' => $javascriptSyntaxErrorNotification,
					'javascriptFileslist' => $javascriptFileslist,
					'visitorSubscriptionForStripe' => $this->visitorSubscriptionForStripe,
					'startOfWeek' => $startOfWeek,
					'bookedList' => 'userBookingDetails',
					'permalink' => get_permalink(),
					'debug' => $this->dubug_javascript,
					'plugin_v' => $this->plugin_version,
					'today' => date('Ymd'),
					'autoWindowScroll' => intval($autoWindowScroll),
					'pluginLocale' => $this->pluginLocale,
					'guestForDayOfTheWeekRates' => $this->guestForDayOfTheWeekRates,
					'errorNumberOfCustomers' => $this->errorNumberOfCustomers,
					'd_v' => $installed_ver,
					'locale_id' => 'booking-package-locale-' . $this->locale,
					'numberFormatter' => $numberFormatter,
					'managementUsersV2' => intval($this->managementUsersV2),
					'userFormFields' => $setting->getUserInputFields(),
				);
				
			} else if ($mode == 'member') {
				
				$list = $setting->getList();
				$localize_script = array(
					'url' => admin_url('admin-ajax.php'), 
					#'url' => plugin_dir_url( __FILE__ ).'ajax.php',
					'action' => $this->action_control, 
					'nonce' => wp_create_nonce($this->action_control."_ajax"), 
					'prefix' => $this->prefix,
					'javascriptSyntaxErrorNotification' => $javascriptSyntaxErrorNotification,
					'javascriptFileslist' => $javascriptFileslist,
					'visitorSubscriptionForStripe' => $this->visitorSubscriptionForStripe,
					'currency' => $list['General']['booking_package_currency']['value'],
					'currencies' => $currencies,
					'clock' => $list['General']['booking_package_clock']['value'],
					'startOfWeek' => $startOfWeek,
					'debug' => $this->dubug_javascript,
					'dateFormat' => $dateFormat,
					'positionOfWeek' => $positionOfWeek,
					'positionTimeDate' => $positionTimeDate,
					'bookedList' => 1,
					'locale' => $locale,
					'numberFormatter' => $numberFormatter,
					'managementUsersV2' => intval($this->managementUsersV2),
					'inputType' => $setting->getFormInputTypeForUser(),
					'userFormFields' => $setting->getUserInputFields(),
				);
				
				
				
			}
			
			if ($this->managementUsersV2 === 0 && isset($localize_script['userFormFields'])) {
				
				$formFields = $localize_script['userFormFields'];
				$updateFormFields = array();
				for ($i = 0; $i < count($formFields); $i++) {
					
					if ($formFields[$i]['id'] === 'user_login' || $formFields[$i]['id'] === 'user_email' || $formFields[$i]['id'] === 'user_pass') {
						
						array_push($updateFormFields, $formFields[$i]);
						
					}
					
				}
				$localize_script['userFormFields'] = $updateFormFields;
				
			}
			
			$localize_script['referer_field'] = esc_attr(wp_unslash($_SERVER['REQUEST_URI']));
			
			return $localize_script;
			
		}
		
		public function deactivation_event(){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			wp_clear_scheduled_hook('retry_to_send_to_server');
			wp_clear_scheduled_hook('booking_package_notification');
			
			$setting = $this->setting;
			$setting->activation(BOOKING_PACKAGE_EXTENSION_URL, "deactivation");
			$setting->setPaidSubscription('deactivation');
			
			$database = new booking_package_database($this->prefix, $this->db_version);
			
		}
		
		public function help_calendar_box() {
			
			$screen = get_current_screen();
			if ($this->locale == 'ja') {
				
				$content = '<ul>';
				$content .= '<li><a href="https://manual-ja.saasproject.net/%e3%83%95%e3%83%ad%e3%83%b3%e3%83%88%e3%83%9a%e3%83%bc%e3%82%b8%e3%81%ab%e4%ba%88%e7%b4%84%e3%82%ab%e3%83%ac%e3%83%b3%e3%83%80%e3%83%bc%e3%82%92%e8%a1%a8%e7%a4%ba/" target="_blank">' . __('How do I show the booking calendar on the page?', 'booking-package') . '</a></li>';
				$content .= '<li><a href="https://manual-ja.saasproject.net/%e3%83%97%e3%83%a9%e3%82%b0%e3%82%a4%e3%83%b3%e3%81%8b%e3%82%89%e9%80%81%e4%bf%a1%e3%81%95%e3%82%8c%e3%82%8b%e3%83%a1%e3%83%bc%e3%83%ab%e3%81%ab%e3%81%a4%e3%81%84%e3%81%a6/" target="_blank">' . __('How do I send a booking email?', 'booking-package') . '</a></li>';
				$content .= '<li><a href="https://manual-ja.saasproject.net/%e4%ba%88%e7%b4%84%e3%82%b9%e3%82%b1%e3%82%b8%e3%83%a5%e3%83%bc%e3%83%ab%e3%81%ae%e4%bd%9c%e6%88%90/" target="_blank">' . __('How do I create booking schedules?', 'booking-package') . '</a></li>';
				$content .= '</ul>';
				$screen->add_help_tab(array(
					'id'    => $this->plugin_name . 'documents',
					'title'   => __('Documents', 'booking-package'), 
					'content' => $content,
				));
				
			}
			
			$content = '<ul>';
			$content .= '<li><a href="https://booking-package.saasproject.net/how-does-the-booking-calendar-show-on-the-page/" target="_blank">' . __('How do I show the booking calendar on the page?', 'booking-package') . '</a></li>';
			$content .= '<li><a href="https://booking-package.saasproject.net/how-do-i-send-a-booking-email-with-a-plugin/" target="_blank">' . __('How do I send a booking email?', 'booking-package') . '</a></li>';
			$content .= '<li><a href="https://booking-package.saasproject.net/how-do-i-create-booking-schedules/" target="_blank">' . __('How do I create booking schedules?', 'booking-package') . '</a></li>';
			$content .= '</ul>';
			$screen->add_help_tab(array(
				'id'    => $this->plugin_name . 'videos',
				'title'   => __('Videos', 'booking-package'), 
				'content' => $content,
			));
			
		}
		
		private function update_memberAccount() {
			
			if (is_null(get_role($this->userRoleName))) {
				
				$roleArray = array('read' => true, 'level_0' => true, 'booking_package' => true);
				$object = add_role($this->userRoleName, 'Booking Package User', $roleArray);
				
			} else {
				
				$object = get_role($this->userRoleName);
				
			}
			
		}
		
		public function createFirstCalendar(){
			
			$timeZone = 'UTC';
			$timeZoneList = timezone_identifiers_list();
			$currentTimeZone = $this->getTimeZone();
			$key = array_search($currentTimeZone, $timeZoneList);
			if (is_int($key)) {
				
				$timeZone = $timeZoneList[$key];
				
			}
			
			$schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $this->userRoleName);
			$schedule->createFirstCalendar($timeZone);
			
			$setting = $this->setting;
			$setting->getEmailMessageList(1, 'First Calendar');
			$setting->getEmailMessageList(2, 'First Calendar for hotel');
			
			$siteName = get_bloginfo('name');
			$email = get_bloginfo('admin_email');
			$options = array('email_from' => $email, 'email_to' => $email, 'email_title_from' => $siteName);
			foreach ($options as $key => $value) {
				
				if (get_option($this->prefix . $key, false) === false) {
					
					add_option($this->prefix . $key, $value);
					
				}
				
			}
			
		}
		
		public function setHomePath(){
			
			if (function_exists('get_home_path')) {
				
				$key = $this->prefix."home_path";
				if (get_option($key) === false) {
								
					add_option($key, get_home_path());
						
				} else {
					
					update_option($key, get_home_path());
					
				}
				
			}
			
		}
		
		public function getFontFaceStyle(){
			
			$url = plugin_dir_url( __FILE__ );
			
			#$style = "<style>\n";
			$style = "	@font-face {\n";
			$style .= "		font-family: 'Material Icons';\n";
			$style .= "		font-style: normal;\n";
			$style .= "		font-weight: 400;\n";
			$style .= "		src: url(".$url."iconfont/MaterialIcons-Regular.eot);\n";
			$style .= "		src: local('Material Icons'),\n";
			$style .= "			local('MaterialIcons-Regular'),\n";
			$style .= "			url(".$url."iconfont/MaterialIcons-Regular.woff2) format('woff2'),\n";
			$style .= "			url(".$url."iconfont/MaterialIcons-Regular.woff) format('woff'),\n";
			$style .= "			url(".$url."iconfont/MaterialIcons-Regular.ttf) format('truetype');\n";
			$style .= "	}\n";
			#$style .= "</style>\n";
			return $style;
			
		}
		
		public function getCustomizeStatus($admin, $list) {
			
			$customizeStatus = array();
			for ($i = 0; $i < count($list['booking_package_customizeStatus']['valueList']); $i++) {
				
				$status = $list['booking_package_customizeStatus']['valueList'][$i];
				$customizeStatus[$status['key']] = $status['value'];
				
			}
			
			$styles = '';
			if ($admin === true) {
				
				$styles .= '#reservation_usersPanel .bookedCustomerPanel {color: ' . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#reservation_usersPanel .visitorApprovedPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important;}\n";
	            $styles .= '#reservation_usersPanel .visitorPendingPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important;}\n";
	            $styles .= '#reservation_usersPanel .visitorCanceledPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important;}\n";
	            $styles .= '#inputFormPanel .visitorApprovedPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#inputFormPanel .visitorPendingPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#inputFormPanel .visitorCanceledPanel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#dialogPanel .approvedLabel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#dialogPanel .pendingLabel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#dialogPanel .canceledLabel {background-color: ' . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= '#calendarPage .approvedCount {color: ' . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important;}\n";
	            $styles .= '#calendarPage .pendingCount {color: ' . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important;}\n";
	            $styles .= '#calendarPage .canceledCount {color: ' . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important;}\n";
	            $styles .= "#reservation_usersPanel .approvedLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important; border-color: " . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . " !important; color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= "#reservation_usersPanel .pendingLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important;border-color: " . $customizeStatus['booking_package_statusBackgroundColorForPending'] . " !important;  color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
	            $styles .= "#reservation_usersPanel .canceledLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important;border-color: " . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . " !important;  color: " . $customizeStatus['booking_package_statusFontColor'] . " !important;}\n";
				
			} else {
				
				$styles .= "#booking-package_myBookingHistoryTable .approvedLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . "; border-color: " . $customizeStatus['booking_package_statusBackgroundColorForApproved'] . "; color: " . $customizeStatus['booking_package_statusFontColor'] . ";}\n";
	            $styles .= "#booking-package_myBookingHistoryTable .pendingLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForPending'] . "; border-color: " . $customizeStatus['booking_package_statusBackgroundColorForPending'] . "; color: " . $customizeStatus['booking_package_statusFontColor'] . ";}\n";
	            $styles .= "#booking-package_myBookingHistoryTable .canceledLabel {background-color: " . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . "; border-color: " . $customizeStatus['booking_package_statusBackgroundColorForCanceled'] . "; color: " . $customizeStatus['booking_package_statusFontColor'] . ";}\n";
				
			}
			
            
            return $styles;
			
		}
		
		public function getStyle($calendarAccount, $list) {
			
			$statusStyle = $this->getCustomizeStatus(false, $list['General']);
			if (intval($calendarAccount['customizeLayoutsBool']) === 1) {
				
				return '<style type="text/css">' . $statusStyle . '</style>';
				
			}
			
			$style = '<style type="text/css">';
			$style .= $statusStyle;
			$style .= "#booking-package-memberActionPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package_myBookingHistory, #booking-package_myBookingDetailsFroVisitor { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package_myBookingHistoryTable th, #booking-package_myBookingHistoryTable td { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package_myBookingDetails { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package button { font-size: ".$list['Design']['booking_package_fontSize']['value']."}\n";
			$style .= "#booking-package_durationStay { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_durationStay .bookingDetailsTitle { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_calendarPage { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_scheduleMainPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_courseMainPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .topPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .topPanelNoAnimation { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .daysListPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .daysListPanelNoAnimation { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .bottomPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#bottomPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selectedDate { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .courseListPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			
			
			$style .= "#booking-package-loginform { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package-loginform .title_in_form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package-user-form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package-user-form .title_in_form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			
			$style .= "#booking-package-user-edit-form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package-user-edit-form .title_in_form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			
			$style .= "#booking-package_inputFormPanel { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_inputFormPanel .title_in_form { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_myBookingDetails .selectedDate { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_calendarPage .closingDay { background-color: ".$list['Design']['booking_package_calendarBackgroundColorWithNoSchedule']['value']."; }\n";
			$style .= "#booking-package_calendarPage .pastDay { background-color: ".$list['Design']['booking_package_calendarBackgroundColorWithNoSchedule']['value']."; }\n";
			
			$style .= "#booking-package .selectable_service_slot { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package .selectable_service_slot:hover { background-color: ".$list['Design']['booking_package_mouseHover']['value']."; }\n";
			$style .= "#booking-package .selectable_time_slot { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package .selectable_time_slot:hover { background-color: ".$list['Design']['booking_package_mouseHover']['value']."; }\n";
			
			$style .= "#booking-package_schedulePage .selectable_day_slot { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selectPanelError { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selectPanelActive { background-color: ".$list['Design']['booking_package_backgroundColorOfSelectedLabel']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selected_day_slot { background-color: ".$list['Design']['booking_package_backgroundColorOfSelectedLabel']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selected_service_slot { background-color: ".$list['Design']['booking_package_backgroundColorOfSelectedLabel']['value']."; }\n";
			$style .= "#booking-package_schedulePage .selectedTimeSlotPanel { background-color: ".$list['Design']['booking_package_backgroundColorOfSelectedLabel']['value']."; }\n";
			
			$style .= "#booking-package_schedulePage .selectable_day_slot:hover { background-color: ".$list['Design']['booking_package_mouseHover']['value']."; }\n";
			$style .= "#booking-package_servicePage { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			$style .= "#booking-package_servicePage .title { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			
			$style .= "#booking-package_servicePage .selectable_day_slot { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package_servicePage .selectPanelError { background-color: ".$list['Design']['booking_package_scheduleAndServiceBackgroundColor']['value']."; }\n";
			$style .= "#booking-package_servicePage .selectPanelActive { background-color: ".$list['Design']['booking_package_backgroundColorOfSelectedLabel']['value']."; }\n";
			$style .= "#booking-package_servicePage .selectable_day_slot:hover { background-color: ".$list['Design']['booking_package_mouseHover']['value']."; }\n";
			
			$style .= "#booking-package_serviceDetails { background-color: ".$list['Design']['booking_package_backgroundColor']['value']."; }\n";
			
			$style .= "#booking-package_calendarPage .pointer:hover { background-color: ".$list['Design']['booking_package_mouseHover']['value']."; }\n";
			$style .= "#booking-package_calendarPage .holidayPanel { background-color: ".$list['Design']['booking_package_backgroundColorOfRegularHolidays']['value']." !important; }\n";
			#$style .= "#booking-package_calendarPage .nationalHoliday { background-color: ".$list['Design']['booking_package_backgroundColorOfNationalHolidays']['value']."; }\n";
			
			$styleList = array(
				"#booking-package .selectable_service_slot",
				"#booking-package .selectable_time_slot",
				"#booking-package_calendarPage .week_slot", 
				"#booking-package_calendarPage .day_slot", 
				"#booking-package_schedulePage .courseListPanel",
				"#booking-package_schedulePage .selectable_day_slot", 
				"#booking-package_schedulePage .selectPanelError", 
				"#booking-package_schedulePage .daysListPanel", 
				"#booking-package_schedulePage .topPanel", 
				"#booking-package_schedulePage .topPanelNoAnimation", 
				"#booking-package_schedulePage .bottomPanel",
				"#booking-package_schedulePage .bottomPanelForPositionInherit",
				"#booking-package_servicePage .selectable_day_slot", 
				"#booking-package_servicePage .selectPanelError", 
				"#booking-package_servicePage .daysListPanel", 
				"#booking-package_servicePage .topPanel", 
				"#booking-package_servicePage .topPanelNoAnimation", 
				"#booking-package_servicePage .bottomPanel",
				"#booking-package-loginform .title_in_form",
				"#booking-package-user-form .title_in_form",
				"#booking-package-user-edit-form .title_in_form",
				"#booking-package_inputFormPanel .title_in_form",
				"#booking-package_myBookingDetails .selectedDate",
				"#booking-package-loginform .row",
				"#booking-package-user-form .row",
				"#booking-package-user-edit-form .row",
				"#booking-package_inputFormPanel .row",
				"#booking-package_myBookingDetails .row",
				"#booking-package_durationStay .row",
				"#booking-package_myBookingDetailsFroVisitor .row",
				"#booking-package_durationStay .bookingDetailsTitle",
				"#booking-package_serviceDetails .row",
				"#booking-package_serviceDetails .borderColor",
				"#booking-package_servicePage .borderColor",
			);
			for ($i = 0; $i < count($styleList); $i++) {
				
				$style .= $styleList[$i]." { border-color: ".$list['Design']['booking_package_borderColor']['value']."; }\n";
				
			}
			
			$style .= "</style>";
			
			return $style;
			
		}
		
		public function getPhpVersion(){
			
			$v = explode('.', phpversion());
			$phpV = $v[0].".".$v[1];
			return floatval($phpV);
			
		}
		
		public function create_dir() {
			
			$upload_dir = wp_upload_dir();
            $dirname = $upload_dir['basedir'] . '/' . $this->plugin_name;
            if (!file_exists($dirname)) {
            	
            	wp_mkdir_p($dirname);
            	#file_put_contents($dirname . '/test.css', "test");
            	
            }
			
		}
		
		public function wp_insert_site($data){
			
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				include_once(ABSPATH . 'wp-includes/ms-functions.php');
				if (is_plugin_active_for_network('booking-package/index.php') === true) {
					
					switch_to_blog($data->id);
					$this->create_database(false);
					$site = "http://" . $data->domain . $data->path;
					if (is_ssl()) {
						
						$site = "https://" . $data->domain . $data->path;
						
					}
					
					$timezone = $this->getTimeZone();
					$setting = $this->setting;
					$setting->activation(BOOKING_PACKAGE_EXTENSION_URL, "activation", $this->plugin_version, $timezone, $site);
					restore_current_blog();
					
				}
				
			}
			
		}
		
		public function wpmu_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta){
			
			#var_dump($param);
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				include_once(ABSPATH . 'wp-includes/ms-functions.php');
				if (is_plugin_active_for_network('booking-package/index.php') === true) {
					
					switch_to_blog($blog_id);
					$this->create_database();
					restore_current_blog();
					
				}
				
			}
			
		}
		
		public function wp_delete_site($old_site){
			
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				include_once(ABSPATH . 'wp-includes/ms-functions.php');
				if (is_plugin_active_for_network('booking-package/index.php') === true) {
					
					switch_to_blog($old_site->id);
					$database = new booking_package_database($this->prefix, null);
					$database->uninstall(true);
					restore_current_blog();
					
				}
				
			}
			
		}
		
		public function delete_blog($blog_id, $drop) {
			
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				include_once(ABSPATH . 'wp-includes/ms-functions.php');
				if (is_plugin_active_for_network('booking-package/index.php') === true) {
					
					switch_to_blog($blog_id);
					$database = new booking_package_database($this->prefix, null);
					$database->uninstall(true);
					restore_current_blog();
					
				}
				
			}
			
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
		
		public function ms_site_check(){
			
			if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
				
				$bool = ms_site_check();
				if ($bool === true) {
					
					include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					include_once( ABSPATH . 'wp-includes/ms-functions.php' );
					if (is_plugin_active_for_network('booking-package/index.php') === true) {
						
						$response = array();
						$id = get_current_blog_id();
						$response['id'] = $id;
						
						if (intval($id) != intval(SITE_ID_CURRENT_SITE)) {
							
							$isExtensionsValid = false;
							$subscription = array();
							$sites = get_sites();
							foreach ((array) $sites as $site) {
								
								if (intval($site->id) == intval(SITE_ID_CURRENT_SITE)) {
									
									#var_dump($site->blog_id);
									switch_to_blog($site->blog_id);
									$setting = $this->setting;
									$isExtensionsValid = $this->getExtensionsValid(false, false);
									if ($isExtensionsValid === true) {
										
										$subscription = $setting->upgradePlan('get');
										
									}
									break;
									
								}
								
							}
							
							#switch_to_blog($id);
							$response['isExtensionsValid'] = $isExtensionsValid;
							$response['subscription'] = $subscription;
							restore_current_blog();
							
							return $response;
							
						} else {
							
							return false;
							
						}
						
					} else {
						
						return false;
						
					}
					
				} else {
					
					return false;
					
				}
				
			} else {
				
				return false;
				
			}
			
		}
		
		public function changeTimeFormat($timeFormat) {
			
			if (!is_numeric($timeFormat)) {
				
				return $timeFormat;
				
			}
			
			if (intval($timeFormat) == 12) {
				
				$timeFormat = '12a.m.p.m';
				
			} else if (intval($timeFormat) == 24) {
				
				$timeFormat = '24hours';
				
			}
			
			return $timeFormat;
			
		}
		
		public function isSearchEngineBot() {
			
			$bots = array('Googlebot', 'Bingbot', 'DuckDuckBot', 'YandexBot', 'Baiduspider', );
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			foreach ($bots as $bot) {
				
				if (stripos($user_agent, $bot) !== false) {
					
					return true;
					
				}
				
			}
			
			return false;
			
		}
		
		public function updateDictionary($calendarAccount, $dictionary) {
			
			if (intval($calendarAccount['customizeLabelsBool']) === 1) {
				
				$customizeLabels = $calendarAccount['customizeLabels'];
				#var_dump($customizeLabels);
				foreach ($customizeLabels as $key => $label) {
					
					if (array_key_exists($key, $dictionary) === true) {
						
						$dictionary[$key] = $label;
						
					}
					
				}
				
			}
			
			return $dictionary;
			
		}
		
		public function getDictionary($mode, $pluginName){
			
			$dictionary = array(
				'Book now' => __('Book now', 'booking-package'),
				'Verify' => __('Verify', 'booking-package'),
				'Error' => __('Error', 'booking-package'),
				'Next' => __('Next', 'booking-package'),
				'Service' => __('Service', 'booking-package'),
				'Add' => __('Add', 'booking-package'),
				'Delete' => __('Delete', 'booking-package'),
				'Edit' => __('Edit', 'booking-package'),
				'Change' => __('Change', 'booking-package'),
				'Copy' => __('Copy', 'booking-package'),
				'Calendar' => __('Calendar', 'booking-package'),
				'Return' => __('Return', 'booking-package'),
				'Save' => __('Save', 'booking-package'),
				'Cancel' => __('Cancel', 'booking-package'),
				'Close' => __('Close', 'booking-package'),
				'Update' => __('Update', 'booking-package'),
				'Help' => __('Help', 'booking-package'),
				'Price' => __('Price', 'booking-package'),
				'Attention' => __('Attention', 'booking-package'),
				'Warning' => __('Warning', 'booking-package'),
				'Booking' => __('Booking', 'booking-package'),
				'January' => __('January', 'booking-package'),
				'February' => __('February', 'booking-package'),
				'March' => __('March', 'booking-package'),
				'April' => __('April', 'booking-package'),
				'May' => __('May', 'booking-package'),
				'June' => __('June', 'booking-package'),
				'July' => __('July', 'booking-package'),
				'August' => __('August', 'booking-package'),
				'September' => __('September', 'booking-package'),
				'October' => __('October', 'booking-package'),
				'November' => __('November', 'booking-package'),
				'December' => __('December', 'booking-package'),
				'Jan' => __('Jan', 'booking-package'),
				'Feb' => __('Feb', 'booking-package'),
				'Mar' => __('Mar', 'booking-package'),
				'Apr' => __('Apr', 'booking-package'),
				'May' => __('May', 'booking-package'),
				'Jun' => __('Jun', 'booking-package'),
				'Jul' => __('Jul', 'booking-package'),
				'Aug' => __('Aug', 'booking-package'),
				'Sep' => __('Sep', 'booking-package'),
				'Oct' => __('Oct', 'booking-package'),
				'Nov' => __('Nov', 'booking-package'),
				'Dec' => __('Dec', 'booking-package'),
				'Sunday' => __('Sunday', 'booking-package'),
				'Monday' => __('Monday', 'booking-package'),
				'Tuesday' => __('Tuesday', 'booking-package'),
				'Wednesday' => __('Wednesday', 'booking-package'),
				'Thursday' => __('Thursday', 'booking-package'),
				'Friday' => __('Friday', 'booking-package'),
				'Saturday' => __('Saturday', 'booking-package'),
				'Sun' => __('Sun', 'booking-package'),
				'Mon' => __('Mon', 'booking-package'),
				'Tue' => __('Tue', 'booking-package'),
				'Wed' => __('Wed', 'booking-package'),
				'Thu' => __('Thu', 'booking-package'),
				'Fri' => __('Fri', 'booking-package'),
				'Sat' => __('Sat', 'booking-package'),
				'Booking Date' => __('Booking Date', 'booking-package'),
				'Booking date' => __('Booking Date', 'booking-package'),
				'Arrival (Check-in)' => __('Arrival (Check-in)', 'booking-package'),
				'Departure (Check-out)' => __('Departure (Check-out)', 'booking-package'),
				'Arrival' => __('Arrival', 'booking-package'),
				'Departure' => __('Departure', 'booking-package'),
				'Check-in' => __('Check-in', 'booking-package'),
				'Check-out' => __('Check-out', 'booking-package'),
				'%s guest' => __('%s guest', 'booking-package'),
				'%s guests' => __('%s guests', 'booking-package'),
				'Guests' => __('Guests', 'booking-package'),
				'Total number of guests' => __('Total number of guests', 'booking-package'),
				'Total length of stay' => __('Total length of stay', 'booking-package'),
				'Total number of nights' => __('Total number of nights', 'booking-package'),
				'Additional fees' => __('Additional fees', 'booking-package'),
				'Room charges' => __('Room charges', 'booking-package'),
				'Option charges' => __('Option charges', 'booking-package'),
				'Accommodation fees' => __('Accommodation fees', 'booking-package'),
				'Subtotal' => __('Subtotal', 'booking-package'),
				'Total number of options' => __('Total number of options', 'booking-package'),
				'Total amount' => __('Total amount', 'booking-package'),
				'Summary' => __('Summary', 'booking-package'),
				'%s night %s days' => __('%s night %s days', 'booking-package'),
				'%s nights %s days' => __('%s nights %s days', 'booking-package'),
				'night' => __('night', 'booking-package'),
				'nights' => __('nights', 'booking-package'),
				'Night' => __('Night', 'booking-package'),
				'Nights' => __('Nights', 'booking-package'),
				'room' => __('room', 'booking-package'),
				'rooms' => __('rooms', 'booking-package'),
				'Options' => __('Options', 'booking-package'),
				'Title' => __('Title', 'booking-package'),
				'Closed' => __('Closed', 'booking-package'),
				'Booking details' => __('Booking details', 'booking-package'),
				'Submission date' => __('Submission date', 'booking-package'),
				'Clear' => __('Clear', 'booking-package'),
				'person' => __('person', 'booking-package'),
				'people' => __('people', 'booking-package'),
				'Select a date' => __('Select a date', 'booking-package'),
				'Get a paid subscription' => __('Get a paid subscription', 'booking-package'),
				'Paid plan subscription required.' => __('Paid plan subscription required.', 'booking-package'),
				'%s slots left' => __('%s slots left', 'booking-package'),
				'An unknown cause of error occurred' => __('An unknown cause of error occurred', 'booking-package'),
				'Status' => __('Status', 'booking-package'),
				'approved' => __("approved", 'booking-package'),
				'pending' => __('pending', 'booking-package'),
				'canceled' => __('canceled', 'booking-package'),
				'Taxes' => __('Taxes', 'booking-package'),
				'Can we really cancel your booking?' => __('Can we really cancel your booking?', 'booking-package'),
				'We have canceled your booking.' => __('We have canceled your booking.', 'booking-package'),
				'Surcharge and Tax' => __('Surcharge and Tax', 'booking-package'),
				'Surcharge' => __('Surcharge', 'booking-package'),
				'Extra charges' => __('Extra charges', 'booking-package'),
				'Tax' => __('Tax', 'booking-package'),
				'Select' => __('Select', 'booking-package'),
				'Unselected' => __('Unselected', 'booking-package'),
				'%s:%s a.m.' => __('%s:%s a.m.', 'booking-package'),
				'%s:%s p.m.' => __('%s:%s p.m.', 'booking-package'),
				'%s:%s am' => __('%s:%s am', 'booking-package'),
				'%s:%s pm' => __('%s:%s pm', 'booking-package'),
				'%s:%s AM' => __('%s:%s AM', 'booking-package'),
				'%s:%s PM' => __('%s:%s PM', 'booking-package'),
				'Update status' => __('Update status', 'booking-package'),
				'Add a new room' => __('Add a new room', 'booking-package'),
				'Enabled' => __('Enabled', 'booking-package'),
				'Disabled' => __('Disabled', 'booking-package'),
				'Remaining' => __('Remaining', 'booking-package'),
				'The required total number of people must be %s or less.' => __('The required total number of people must be %s or less.', 'booking-package'),
				'The required total number of people must be %s or more.' => __('The required total number of people must be %s or more.', 'booking-package'),
				'The total number of people must be %s or less.' => __('The total number of people must be %s or less.', 'booking-package'),
				'The total number of people must be %s or more.' => __('The total number of people must be %s or more.', 'booking-package'),
				'%s to %s' => __('%s to %s', 'booking-package'),
				'From %s to %s' => __('From %s to %s', 'booking-package'),
				'Coupons' => __('Coupons', 'booking-package'),
				'Coupon code' => __('Coupon code', 'booking-package'),
				'Coupon' => __('Coupon', 'booking-package'),
				'Discount' => __('Discount', 'booking-package'),
				'Apply' => __('Apply', 'booking-package'),
				' to ' => __(' to ', 'booking-package'),
				'I will pay locally' => __('I will pay locally', 'booking-package'),
				'Pay locally' => __('Pay locally', 'booking-package'),
				'Pay with Credit Card' => __('Pay with Credit Card', 'booking-package'),
				'Pay with Stripe' => __('Pay with Credit Card', 'booking-package'),
				'Pay with PayPal' => __('Pay with PayPal', 'booking-package'),
				'Pay at a convenience store' => __('Pay at a convenience store', 'booking-package'),
				'Pay at a convenience store with Stripe' => __('Pay at a convenience store', 'booking-package'),
				'Usernames can only contain lowercase letters (a-z) and numbers.' => __('Usernames can only contain lowercase letters (a-z) and numbers.', 'booking-package'),
				'Please enter a valid email address.' => __('Please enter a valid email address.', 'booking-package'),
				'Please enter a valid password.' => __('Please enter a valid password.', 'booking-package'),
				'Deprecated' => __('Deprecated', 'booking-package'),
				'The total number of guests must be %s or less.' => __('The total number of guests must be %s or less.', 'booking-package'),
				'Range of booking options' => __('Range of booking options', 'booking-package'),
				'Per each booking date' => __('Per each booking date', 'booking-package'),
				'Per booking process' => __('Per booking process', 'booking-package'),
				'"%s"' => __('"%s"', 'booking-package'),
				'"%s" and "%s"' => __('"%s" and "%s"', 'booking-package'),
				'Please fill in your details' => __('Please fill in your details', 'booking-package'),
				'Please confirm your details' => __('Please confirm your details', 'booking-package'),
				'Booking Completed' => __('Booking Completed', 'booking-package'),
				'Select payment method' => __('Select payment method', 'booking-package'),
				'Payment method' => __('Payment method', 'booking-package'),
				'Credit card' => __('Credit card', 'booking-package'),
				'Sign in' => __('Sign in', 'booking-package'),
				'Sign out' => __('Sign out', 'booking-package'),
				'Create account' => __('Create account', 'booking-package'),
				'Edit My Profile' => __('Edit My Profile', 'booking-package'),
				'Booking history' => __('Booking history', 'booking-package'),
				'Please select a service' => __('Please select a service', 'booking-package'),
				'Service details' => __('Service details', 'booking-package'),
				'Hello, %s' => __('Hello, %s', 'booking-package'),
				'Sign up' => __('Sign up', 'booking-package'),
				'Register' => __('Register', 'booking-package'),
				'Cancel booking' => __('Cancel booking', 'booking-package'),
				'Next page' => __('Next page', 'booking-package'),
				'Change password' => __('Change password', 'booking-package'),
				'Update Profile' => __('Update Profile', 'booking-package'),
				'Change password' => __('Change password', 'booking-package'),
				'Username' => __('Username', 'booking-package'),
				'Email' => __('Email', 'booking-package'),
				'Password' => __('Password', 'booking-package'),
				'Custom form fields' => __('Custom form fields', 'booking-package'),
			);
			
			
			if ($mode == "booking_package_booked_customers") {
				
				$dictionary['Download CSV'] = __("Download CSV", 'booking-package');
				$dictionary['Timezone'] = __("Timezone", 'booking-package');
				$dictionary['Booking'] = __("Booking", 'booking-package');
				$dictionary['No schedules'] = __('No schedules', 'booking-package');
				$dictionary['No visitors'] = __('No visitors', 'booking-package');
				$dictionary['This booking was paid through %s. Will the payment be refunded to the customer?'] = __('This booking was paid through %s. Will the payment be refunded to the customer?', 'booking-package');
				$dictionary['Will emails be sent to both customers and administrators?'] = __('Will emails be sent to both customers and administrators?', 'booking-package');
				$dictionary['This booking has been paid by credit card. Do you refund the price to the customer?'] = __('This booking has been paid by credit card. Do you refund the price to the customer?', 'booking-package');
				$dictionary['Do you send e-mail notifications to customers or administrators?'] = __('Do you send e-mail notifications to customers or administrators?', 'booking-package');
				$dictionary['Are you sure you want to delete this booking?'] = __('Are you sure you want to delete this booking?', 'booking-package');
				$dictionary['Please create a service.'] = __('Please create a service.', 'booking-package');
				$dictionary['The user was not found.'] = __('The user was not found.', 'booking-package');
				$dictionary['Payment method'] = __('Payment method', 'booking-package');
				$dictionary['Payment ID'] = __('Payment ID', 'booking-package');
				
			} else if ($mode == "schedule_page") {
				
				$dictionary['General'] = __('General', 'booking-package');
				$dictionary['Form'] = __('Form', 'booking-package');
				$dictionary['User'] = __('User', 'booking-package');
				$dictionary['Themes'] = __('Themes', 'booking-package');
				$dictionary['Charges'] = __('Charges', 'booking-package');
				$dictionary['Published'] = __("Published", 'booking-package');
				$dictionary['Publication date'] = __("Publication date", 'booking-package');
				$dictionary['Paused'] = __("Paused", 'booking-package');
				$dictionary['Book the publication date'] = __("Book the publication date", 'booking-package');
				$dictionary['Publication'] = __("Publication", 'booking-package');
				$dictionary['Period'] = __("Period", 'booking-package');
				$dictionary['Every %s'] = __('Every %s', 'booking-package');
				$dictionary['Time'] = __('Time', 'booking-package');
				$dictionary['hour'] = __('hour', 'booking-package');
				$dictionary['hours'] = __('hours', 'booking-package');
				$dictionary['minutes'] = __('minutes', 'booking-package');
				$dictionary['Email address'] = __('Email address', 'booking-package');
				$dictionary['Booking end time'] = __('Booking end time', 'booking-package');
				$dictionary['Remaining'] = __('Remaining', 'booking-package');
				$dictionary['Available slots'] = __('Available slots', 'booking-package');
				$dictionary['Available room slots'] = __('Available room slots', 'booking-package');
				$dictionary['Remaining slots'] = __('Remaining slots', 'booking-package');
				$dictionary['Booking end time'] = __('Booking end time', 'booking-package');
				$dictionary['%s min before'] = __('%s min before', 'booking-package');
				$dictionary['%s min'] = __('%s min', 'booking-package');
				$dictionary['Choose %s'] = __('Choose %s', 'booking-package');
				$dictionary['Select booking calendar type'] = __('Select booking calendar type', 'booking-package');
				$dictionary['Do you delete the "%s"?'] = __('Do you delete the "%s"?', 'booking-package');
				$dictionary['Status'] = __('Status', 'booking-package');
				$dictionary['Shortcode'] = __('Shortcode', 'booking-package');
				$dictionary['Name'] = __('Name', 'booking-package');
				$dictionary['Description'] = __('Description', 'booking-package');
				$dictionary['Active'] = __('Active', 'booking-package');
				$dictionary['Price'] = __('Price', 'booking-package');
				$dictionary['Duration time'] = __('Duration time', 'booking-package');
				$dictionary['Value'] = __('Value', 'booking-package');
				$dictionary['Required'] = __('Required', 'booking-package');
				$dictionary['Type'] = __('Type', 'booking-package');
				$dictionary['Options'] = __('Options', 'booking-package');
				$dictionary['Save the changed order'] = __('Save the changed order', 'booking-package');
				$dictionary['Do you copy the "%s"?'] = __('Do you copy the "%s"?', 'booking-package');
				$dictionary['Do you delete the "%s"?'] = __('Do you delete the "%s"?', 'booking-package');
				$dictionary['Disable'] = __('Disable', 'booking-package');
				$dictionary['Enable'] = __('Enable', 'booking-package');
				$dictionary['New'] = __('New', 'booking-package');
				$dictionary['Approved'] = __('Approved', 'booking-package');
				$dictionary['Pending'] = __('Pending', 'booking-package');
				$dictionary['Updated'] = __('Updated', 'booking-package');
				$dictionary['Reminder'] = __('Reminder', 'booking-package');
				$dictionary['Canceled'] = __('Canceled', 'booking-package');
				$dictionary['Deleted'] = __('Deleted', 'booking-package');
				$dictionary['Subject'] = __('Subject', 'booking-package');
				$dictionary['Content'] = __('Content', 'booking-package');
				$dictionary['Date'] = __('Date', 'booking-package');
				$dictionary['Maximum number of people staying in one room'] = __('Maximum number of people staying in one room', 'booking-package');
				$dictionary['Include children in the maximum number of people in the room'] = __('Include children in the maximum number of people in the room', 'booking-package');
				$dictionary['Exclude'] = __('Exclude', 'booking-package');
				$dictionary['Include'] = __('Include', 'booking-package');
				$dictionary['Warning'] = __('Warning', 'booking-package');
				$dictionary['Time Slot Bookings'] = __('Time Slot Bookings', 'booking-package');
				$dictionary['Multi-night Bookings'] = __('Multi-night Bookings', 'booking-package');
				$dictionary['Time Slot Bookings (e.g., hair salon, hospital)'] = __('Time Slot Bookings (e.g., hair salon, hospital)', 'booking-package');
				$dictionary['Multi-night Bookings (e.g., accommodations like hotels)'] = __('Multi-night Bookings (e.g., accommodations like hotels)', 'booking-package');
				$dictionary['[%s] is inserting "%s"'] = __('[%s] is inserting "%s"', 'booking-package');
				$dictionary['Public days from today'] = __('Public days from today', 'booking-package');
				$dictionary['Unavailable days from today'] = __('Unavailable days from today', 'booking-package');
				$dictionary['Refresh token'] = __('Refresh token', 'booking-package');
				$dictionary['Cancellation URL'] = __('Cancellation URL', 'booking-package');
				$dictionary['Received URL'] = __('Received URL', 'booking-package');
				$dictionary['Customer details'] = __('Customer details', 'booking-package');
				$dictionary['URL of customer details for administrator'] = __('URL of customer details for administrator', 'booking-package');
				$dictionary['Public holiday'] = __('Public holiday', 'booking-package');
				$dictionary['Open'] = __('Open', 'booking-package');
				$dictionary['Surcharges'] = __('Surcharges', 'booking-package');
				$dictionary['Payment method'] = __('Payment method', 'booking-package');
				$dictionary['Stop'] = __('Stop', 'booking-package');
				$dictionary['You can use following shortcodes in content editer.'] = __('You can use following shortcodes in content editer.', 'booking-package');
				$doctionary['This calendar shares the schedules of the "%s".'] = __('This calendar shares the schedules of the "%s".', 'booking-package');
				$dictionary['Weekly schedule templates'] = __('Weekly schedule templates', 'booking-package');
				$dictionary['Select multiple days'] = __('Select multiple days', 'booking-package');
				$dictionary['Multiple days'] = __('Multiple days', 'booking-package');
				$dictionary['Add time slots'] = __('Add time slots', 'booking-package');
				$dictionary['Coupon name'] = __('Coupon name', 'booking-package');
				$dictionary['Add new item'] = __('Add new item', 'booking-package');
				$dictionary['Select all time slots'] = __('Select all time slots', 'booking-package');
				$dictionary['Specify the time slots for each day of the week'] = __ ('Specify the time slots for each day of the week', 'booking-package');
				$dictionary['This schedule has not been perfectly deleted.'] = __('This schedule has not been perfectly deleted.', 'booking-package');
				$dictionary['Delete the schedules perfectly'] = __('Delete the schedules perfectly', 'booking-package');
				$dictionary['Discount value'] = __('Discount value', 'booking-package');
				$dictionary['Booking date and time'] = __('Booking date and time', 'booking-package');
				$dictionary['Booking date'] = __('Booking date', 'booking-package');
				$dictionary['Booking time'] = __('Booking time', 'booking-package');
				$dictionary['Booking title'] = __('Booking title', 'booking-package');
				$dictionary['Timezone'] = __("Timezone", 'booking-package');
				$dictionary['Schedules'] = __("Schedules", 'booking-package');
				$dictionary['Number of rooms available'] = __("Number of rooms available", 'booking-package');
				$dictionary['Last %s days'] = __("Last %s days", 'booking-package');
				$dictionary['%s or %s'] = __("%s or %s", 'booking-package');
				$dictionary['You changed to a value less than the current "%s".'] = __('You changed to a value less than the current "%s".', 'booking-package');
				$dictionary['Services'] = __('Services', 'booking-package');
				$dictionary['Closing Days'] = __('Closing Days', 'booking-package');
				$dictionary['Form Fields'] = __('Form Fields', 'booking-package');
				$dictionary['Services excluded guests'] = __('Services excluded guests', 'booking-package');
				$dictionary['Services excluded guests and costs'] = __('Services excluded guests and costs', 'booking-package');
				$dictionary['Per all booking days'] = __('Per all booking days', 'booking-package');
				$dictionary['Per day'] = __('Per day', 'booking-package');
				$dictionary['Per one booking'] = __('Per one booking', 'booking-package');
				$dictionary['Per one booking for all guests'] = __('Per one booking for all guests', 'booking-package');
				$dictionary['Tax rate'] = __('Tax rate', 'booking-package');
				$dictionary['Fixed tax amount'] = __('Fixed tax amount', 'booking-package');
				$dictionary['Taxation method'] = __("Taxation method", 'booking-package');
				$dictionary['Excluding tax'] = __("Excluding tax", 'booking-package');
				$dictionary['Including tax'] = __("Including tax", 'booking-package');
				$dictionary['Display format: %s'] = __("Display format: %s", 'booking-package');
				$dictionary['Prices'] = __("Prices", 'booking-package');
				$dictionary['Number of guests'] = __("Number of guests", 'booking-package');
				$dictionary['Price adjustment for "%s" based on selected number of guests'] = __('Price adjustment for "%s" based on selected number of guests', 'booking-package');
				$dictionary['Insert a booking confirmation form between the booking form and the booking completion form'] = __('Insert a booking confirmation form between the booking form and the booking completion form', 'booking-package');
				$dictionary['Per day'] = __("Per day", 'booking-package');
				$dictionary['Per one booking'] = __("Per one booking", 'booking-package');
				$dictionary['Per one booking for all guests'] = __("Per one booking for all guests", 'booking-package');
				$dictionary['Select multiple days to add booking time slots.'] = __("Select multiple days to add booking time slots.", 'booking-package');
				$dictionary['Are you sure you want to delete the selected days?'] = __("Are you sure you want to delete the selected days?", 'booking-package');
				$dictionary['Based on the value of %s, schedules will be re-registered on %s.'] = __('Based on the value of %s, schedules will be re-registered on %s.', 'booking-package');
				$dictionary['Based on the value of %s, schedules will be re-registered from %s to %s.'] = __('Based on the value of %s, schedules will be re-registered from %s to %s.', 'booking-package');
				$dictionary['Enable the function'] = __('Enable the function', 'booking-package');
				$dictionary['Title of "%s"'] = __('Title of "%s"', 'booking-package');
				$dictionary['Pay with Stripe'] = __('Pay with Stripe', 'booking-package');
				$dictionary['Pay at a convenience store with Stripe'] = __('Pay at a convenience store with Stripe', 'booking-package');
				$dictionary['Reset'] = __("Reset", 'booking-package');
				$dictionary['%s labels'] = __("%s labels", 'booking-package');
				$dictionary['CSS class selector:'] = __("CSS class selector:", 'booking-package');
				$dictionary['Customize'] = __("Customize", 'booking-package');
				$dictionary['Labels'] = __("Labels", 'booking-package');
				$dictionary['Buttons'] = __("Buttons", 'booking-package');
				$dictionary['Layouts'] = __("Layouts", 'booking-package');
				$dictionary['Calendar'] = __("Calendar", 'booking-package');
				$dictionary['Time slots'] = __("Time slots", 'booking-package');
				$dictionary['Preview'] = __("Preview", 'booking-package');
				$dictionary['Previous available day'] = __("Previous available day", 'booking-package');
				$dictionary['Next available day'] = __("Next available day", 'booking-package');
				$dictionary['Return from the form'] = __("Return from the form", 'booking-package');
				$dictionary['Pseudo-class'] = __("Pseudo-class", 'booking-package');
				$dictionary['Left arrow'] = __('Left arrow', 'booking-package');
				$dictionary['Right arrow'] = __('Right arrow', 'booking-package');
				$dictionary['Cancel user booking'] = __('Cancel user booking', 'booking-package');
				$dictionary['Font size'] = __('Font size', 'booking-package');
				$dictionary['Font color'] = __('Font color', 'booking-package');
				$dictionary['Background color'] = __('Background color', 'booking-package');
				$dictionary['Border color'] = __('Border color', 'booking-package');
				$dictionary['Cancel a booking by your customer'] = __("Cancel a booking by your customer", 'booking-package');
				$dictionary['Booking reminder'] = __("Booking reminder", 'booking-package');
				$dictionary['Changing the values of the following items will not be reflected in already published schedules.'] = __('Changing the values of the following items will not be reflected in already published schedules.', 'booking-package');
				$dictionary['The changed values will be reflected in new schedules published in the future.'] = __('The changed values will be reflected in new schedules published in the future.', 'booking-package');
				$dictionary['Items to be changed: %s'] = __('Items to be changed: %s', 'booking-package');
				$dictionary['To modify already published schedules, adjustments to the values saved for the respective day need to be made in the "%s" tab.'] = __('To modify already published schedules, adjustments to the values saved for the respective day need to be made in the "%s" tab.', 'booking-package');
				$dictionary['The cancellation URL shortcode has been inserted into the text area.'] = __("The cancellation URL shortcode has been inserted into the text area.", 'booking-package');
				$dictionary['Please enable the "%s" item in the "Settings" tab.'] = __('Please enable the "%s" item in the "Settings" tab.', 'booking-package');
				$dictionary['Despite having valid items, the functionality of "%s" is disabled.'] = __('Despite having valid items, the functionality of "%s" is disabled.', 'booking-package');
				
			} else if ($mode == "setting_page") {
				
				$dictionary['My billing'] = __('My billing', 'booking-package');
				$dictionary['Cancel my subscription'] = __('Cancel my subscription', 'booking-package');
				$dictionary['Update my subscription'] = __("Update my subscription", 'booking-package');
				$dictionary['Cancel subscription'] = __('Cancel subscription', 'booking-package');
				$dictionary['Update subscription'] = __("Update subscription", 'booking-package');
				$dictionary['Value'] = __('Value', 'booking-package');
				$dictionary['Type'] = __('Type', 'booking-package');
				$dictionary['Subscription status'] = __('Subscription status', 'booking-package');
				$dictionary['Subscription ID'] = __('Subscription ID', 'booking-package');
				$dictionary['Expiration date'] = __('Expiration date', 'booking-package');
				$dictionary['Your email'] = __('Your email', 'booking-package');
				$dictionary['Do you delete the "%s"?'] = __('Do you delete the "%s"?', 'booking-package');
				$dictionary['Do you really cancel the subscription?'] = __('Do you really cancel the subscription?', 'booking-package');
				$dictionary['General'] = __('General', 'booking-package');
				$dictionary['Country'] = __('Country', 'booking-package');
				$dictionary['Selected country'] = __('Selected country', 'booking-package');
				$dictionary['Frequently used countries'] = __('Frequently used countries', 'booking-package');
				$dictionary['Other countries'] = __('Other countries', 'booking-package');
				$dictionary['There are blank fields.'] = __('There are blank fields.', 'booking-package');
				$dictionary['Subject'] = __('Subject', 'booking-package');
				$dictionary['Content'] = __('Content', 'booking-package');
				$dictionary['General'] = __('General', 'booking-package');
				$dictionary['Design'] = __('Design', 'booking-package');
				$dictionary['Date'] = __('Date', 'booking-package');
				$dictionary['Email'] = __('Email', 'booking-package');
				$dictionary['Refresh token'] = __('Refresh token', 'booking-package');
				$dictionary['Active'] = __('Active', 'booking-package');
				$dictionary['Inactive'] = __('Inactive', 'booking-package');
				$dictionary['Canceled'] = __('Canceled', 'booking-package');
				$dictionary['Not subscribed'] = __('Not subscribed', 'booking-package');
				$dictionary['The subscription has been canceled, so the paid subscription will end after the current expiration date unless this plugin is uninstalled.'] = __('The subscription has been canceled, so the paid subscription will end after the current expiration date unless this plugin is uninstalled.', 'booking-package');
				$dictionary['Reset the subscription immediately.'] = __('Reset the subscription immediately.', 'booking-package');
				$dictionary['Resetting the subscription will deactivate all premium features. Are you sure you want to reset the subscription?'] = __('Resetting the subscription will deactivate all premium features. Are you sure you want to reset the subscription?', 'booking-package');
				
			} else if ($mode == "Upgrade_js") {
				
				
				
			} else if ($mode == "booking_package_front_end") {
				
				$dictionary['Please fill in your details'] = __('Please fill in your details', 'booking-package');
				$dictionary['Please confirm your details'] = __('Please confirm your details', 'booking-package');
				$dictionary['Booking Completed'] = __('Booking Completed', 'booking-package');
				$dictionary['Credit card'] = __('Credit card', 'booking-package');
				$dictionary['Service is not registered. '] = __('Service is not registered. ', 'booking-package');
				$dictionary['Submit Payment'] = __('Submit Payment', 'booking-package');
				$dictionary['Sign in'] = __('Sign in', 'booking-package');
				$dictionary['Cancel booking'] = __('Cancel booking', 'booking-package');
				$dictionary['Next'] = __('Next', 'booking-package');
				$dictionary['Next page'] = __('Next page', 'booking-package');
				$dictionary['You have not selected anything'] = __('You have not selected anything', 'booking-package');
				$dictionary['Select option'] = __("Select option", 'booking-package');
				$dictionary['Select payment method'] = __('Select payment method', 'booking-package');
				$dictionary['I will pay locally'] = __('I will pay locally', 'booking-package');
				$dictionary['Pay with Credit Card'] = __('Pay with Credit Card', 'booking-package');
				$dictionary['Pay with PayPal'] = __('Pay with PayPal', 'booking-package');
				$dictionary['Pay at a convenience store'] = __('Pay at a convenience store', 'booking-package');
				$dictionary['Do you really want to delete the license as a member?'] = __('Do you really want to delete the license as a member?', 'booking-package');
				$dictionary['We sent a verification code to the following address.'] = __('We sent a verification code to the following address.', 'booking-package');
				$dictionary['Lost your password?'] = __('Lost your password?', 'booking-package');
				
			} else if ($mode == 'user_management') {
				
				$dictionary['User account'] = __('User account', 'booking-package');
				$dictionary['Reject bookings from non-user accounts'] = __('Reject bookings from non-user accounts', 'booking-package');
				$dictionary['User registration from customers'] = __('User registration from customers', 'booking-package');
				$dictionary['Send a verification code to the user by email during registration and editing'] = __('Send a verification code to the user by email during registration and editing', 'booking-package');
				$dictionary['Accept subscribers as users'] = __('Accept subscribers as users', 'booking-package');
				$dictionary['Accept contributors as users'] = __('Accept contributors as users', 'booking-package');
				$dictionary['Toolbar'] = __('Toolbar', 'booking-package');
				$dictionary['Lost password'] = __('Lost password', 'booking-package');
				$dictionary['Add new item'] = __('Add new item', 'booking-package');
				$dictionary['Save the changed order'] = __('Save the changed order', 'booking-package');
				$dictionary['Username'] = __('Username', 'booking-package');
				$dictionary['Email'] = __('Email', 'booking-package');
				$dictionary['Password'] = __('Password', 'booking-package');
				
			}
			
			return $dictionary;
			
		}
		
		
	}
	
	class booking_package_widget extends WP_Widget{
		
		public $plugin_name = 'booking-package';
		
		public $prefix = 'booking_package_';
		
		public function __construct() {
			
			#var_dump($locale);
			$textdomain = load_plugin_textdomain($this->plugin_name, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
			$widget_options = array(
		        'classname'                     => 'booking_package_widget',
		        'description'                   => 'Booking system works within the widget.',
		        'customize_selective_refresh'   => true,
		    );
		    
		    parent::__construct( 'booking_package_widget', 'Booking Package', $widget_options);
			
		}
		
		public function widget($args, $instance){
	        
	        if (is_active_widget(false, false, $this->id_base, true)) {
	        	
	        	$defaults = array("calendarKey" => null);
		        $instance = wp_parse_args((array) $instance, $defaults);
		        #var_dump($instance);
		        $shortcodes = 0;
		        if (isset($_REQUEST['shortcodes_for_booking_package'])) {
		        	
		        	$shortcodes = intval($_REQUEST['shortcodes_for_booking_package']);
		        	
		        }
		        $booking_package = new BOOKING_PACKAGE($shortcodes, true);
		        $account = array('id' => 0);
		        if (!is_null($instance['calendarKey'])) {
		        	
		        	$account['id'] = intval($instance['calendarKey']);
		        	
		        } else {
		        	
			        $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $booking_package->userRoleName);
			        $accountList = $schedule->getCalendarAccountListData();
			        foreach ((array) $accountList as $key => $value) {
			        	
			        	$account['id'] = intval($value['key']);
			        	break;
			        	
			        }
		        	
		        }
		        
		        $html = $booking_package->booking_package_front_end($account);
		        echo $html;
	        	
	        }
	        
	    }
	    
	    public function form($instance){
	        
	        
	        $defaults = array("calendarKey" => null);
	        $instance = wp_parse_args((array) $instance, $defaults);
	        $calendarKey = 0;
	        if (!is_null($instance['calendarKey'])) {
	        	
	        	$calendarKey = intval($instance['calendarKey']);
	        	
	        }
	        
	        $schedule = new booking_package_schedule($this->prefix, $this->plugin_name, $this->currencies, $booking_package->userRoleName);
	        $accountList = $schedule->getCalendarAccountListData();
	        echo '<p style="">';
	        echo _e('Booking Calendar :', $this->plugin_name);
	        echo '<select id="'.$this->get_field_id('calendarKey').'" name="'.$this->get_field_name('calendarKey').'" style="margin-left: 1em;">';
	        foreach ((array) $accountList as $key => $value) {
	        	
	        	if ($calendarKey == intval($value['key'])) {
	        		
	        		echo '<option value="'.intval($value['key']).'" selected>'.$value['name'].'</option>';
	        		
	        	} else {
	        		
	        		echo '<option value="'.intval($value['key']).'">'.$value['name'].'</option>';
	        		
	        	}
	        	
	        	
	        }
	        echo '</select></p>';
	        
	    }
		
	    public function update($new_instance, $old_instance){
	        
	        echo "update";
	        $instance = $old_instance;
	        $instance['calendarKey'] = sanitize_text_field($new_instance['calendarKey']);
			return $instance;
	        
	    }
		
	}
	
	$booking_package = new BOOKING_PACKAGE(0, false);
	
?>