<?php
    if (!defined('ABSPATH')) {
    	exit;
	}
    
    class booking_package_setting {
        
        public $prefix = null;
        
        public $pluginName = null;
        
        public $userRoleName = null;
        
        private $isExtensionsValid = null;
        
        public $guestForDayOfTheWeekRates = 1;
        
        public $messagingApp = 0;
        
        public $maxBookingSlotsPerDay = 0;
        
        
        
        public $form = array(
        	array('id' => 'firstname', 'name' => 'First name', 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'true', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false'),
        	array('id' => 'lastname', 'name' => 'Last name', 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'true', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false'),
        	array('id' => 'email', 'name' => 'Email', 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'true', 'isTerms' => 'false'),
        	array('id' => 'phone', 'name' => 'Phone', 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false'),
        	array('id' => 'zip', 'name' => 'Zip', 'value' => '', 'type' => 'TEXT', 'options' => '', 'required' => 'false', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false'),
        	array('id' => 'address', 'name' => 'Address', 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'false', 'isName' => 'false', 'isAddress' => 'true', 'isEmail' => 'false', 'isTerms' => 'false'),
        	array('id' => 'terms', 'name' => 'Terms of Service', 'value' => '', 'type' => 'CHECK', 'active' => 'true', 'options' => 'I agree', 'required' => 'false', 'isName' => 'false', 'isAddress' => 'true', 'isEmail' => 'false', 'isTerms' => 'true'),
        );
		
        public $email_message = array(
            "mail_new_admin" => array("key" => "mail_new_admin", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'New', 'message' => ''), 
            /** "mail_new_visitor" => array("key" => "mail_new_visitor", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => ''), **/
            "mail_approved" => array("key" => "mail_approved", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Approved', 'message' => ''),
            "mail_pending" => array("key" => "mail_pending", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Pending', 'message' => ''),
            "mail_updated" => array("key" => "mail_updated", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Updated', 'message' => ''),
            "mail_reminder" => array("key" => "mail_reminder", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Reminder', 'message' => ''),
            /**"mail_cancel" => array("key" => "mail_cancel", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Cancellation of booking', 'message' => ''),**/
            "mail_canceled_by_visitor_user" => array("key" => "mail_canceled_by_visitor_user", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Canceled', 'message' => ''),
            "mail_deleted" => array("key" => "mail_deleted", "subject" => "", "content" => "", 'enable' => '0', 'format' => 'text', 'title' => 'Deleted', 'message' => ''),
        );
        
        public function __construct($prefix, $pluginName, $userRoleName = 'booking_package_user') {
            
            $this->prefix = $prefix;
            $this->pluginName = $pluginName;
            $this->userRoleName = $userRoleName;
            
        }
        
        public function setMessagingApp($messagingApp) {
            
            $this->messagingApp = $messagingApp;
            
        }
        
        public function setMaxBookingSlotsPerDay($maxBookingSlotsPerDay) {
            
            $this->maxBookingSlotsPerDay = $maxBookingSlotsPerDay;
            
        }
        
        public function setGuestForDayOfTheWeekRates($guestForDayOfTheWeekRates) {
            
            $this->guestForDayOfTheWeekRates = $guestForDayOfTheWeekRates;
            
        }
        
        public function userFunctions() {
            
            $userFunctions = array(
                'function_for_member' => array('name' => __('User account', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'reject_non_membder' => array('name' => __('Reject bookings from non-user accounts', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'visitors_registration_for_member' => array('name' => __('User registration from customers', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'check_email_for_member' => array('name' => __('Send a verification code to the user by email during registration and editing', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'accept_subscribers_as_users' => array('name' => __('Accept subscribers as users', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'accept_contributors_as_users' => array('name' => __('Accept contributors as users', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                'user_toolbar' => array('name' => __('Toolbar', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
                /**
                'subject_email_for_member' => array('Subject of email sent when confirming email address' => 'Active', 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'valueList' => array()), 
                'body_email_for_member' => array('Body of email sent when confirming email address' => 'Active', 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXTAREA', 'valueList' => array()), 
                **/
                'lost_password' => array('name' => __('Lost password', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array('0' => 'Enabled')), 
            );
            
            return $userFunctions;
            
        }
        
        public function booking_sync() {
        
            $booking_syn = array(
                "iCal" => array(
                    'ical_active' => array('name' => __('Status', 'booking-package'), 'value' => '0', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'syncPastCustomersForIcal' => array('name' => __('Period', 'booking-package'), 'value' => '0', 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => 
                        array(
                            '7' => sprintf(__('Last %s days', 'booking-package'), 7),
                            '14' => sprintf(__('Last %s days', 'booking-package'), 14),
                            '30' => sprintf(__('Last %s days', 'booking-package'), 30),
                            '60' => sprintf(__('Last %s days', 'booking-package'), 60),
                            '90' => sprintf(__('Last %s days', 'booking-package'), 90),
                            '180' => sprintf(__('Last %s days', 'booking-package'), 180),
                            '365' => sprintf(__('Last %s days', 'booking-package'), 365),
                        )
                    ), 
                    'ical_token' => array('name' => 'URL', 'value' => '', 'inputLimit' => 1, 'inputType' => 'CUSTOMIZE'),
                )
            );
            
            return $booking_syn;
            
        }
        
        public function defaultFrom() {
            
            $form = array(
            	array('id' => 'firstname', 'name' => __('First name', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'true', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'lastname', 'name' => __('Last name', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'true', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'email', 'name' => __('Email', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'true', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'phone', 'name' => __('Phone', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'true', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'zip', 'name' => __('Zip', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => '', 'options' => '', 'required' => 'false', 'isName' => 'false', 'isAddress' => 'false', 'isEmail' => 'false', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'address', 'name' => __('Address', 'booking-package'), 'value' => '', 'type' => 'TEXT', 'active' => 'true', 'options' => '', 'required' => 'false', 'isName' => 'false', 'isAddress' => 'true', 'isEmail' => 'false', 'isTerms' => 'false', 'targetCustomers' => 'customersAndUsers'),
            	array('id' => 'terms', 'name' => __('Terms of Service', 'booking-package'), 'value' => '', 'type' => 'CHECK', 'active' => 'true', 'options' => __('I agree', 'booking-package'), 'required' => 'false', 'isName' => 'false', 'isAddress' => 'true', 'isEmail' => 'false', 'isTerms' => 'true', 'targetCustomers' => 'customersAndUsers'),
            );
            return $form;
            
        }
        
        public function guestsInputType(){
            
            $guestsInputTypeList = array(
                'name' => array('key' => 'name', 'name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'target' => 'both'),
                'description' => array('name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => '', 'target' => 'both'),
                'active' => array('key' => 'active', 'name' => __('Status', 'booking-package'), 'value' => 'true', 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('true' => 'Enabled'), "class" => '', 'target' => 'both'),
                'required' => array('key' => 'required', 'name' => __('Required', 'booking-package'), 'value' => '0', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array(1 => __('Yes', 'booking-package'), 0 => __('No', 'booking-package')), 'target' => 'both'),
                'costInServices' => array('key' => 'costInServices', 'name' => __('Select the service price from Base Price to Price 6', 'booking-package'), 'value' => 'cost_1', 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => array('cost_1' => __(/**'Cost 1'**/ 'Base Price', 'booking-package'), 'cost_2' => sprintf(__('Price %s', 'booking-package'), '2'), 'cost_3' => sprintf(__('Price %s', 'booking-package'), '3'), 'cost_4' => sprintf(__('Price %s', 'booking-package'), '4'), 'cost_5' => sprintf(__('Price %s', 'booking-package'), '5'), 'cost_6' => sprintf(__('Price %s', 'booking-package'), '6')), 'target' => 'day'),
                'target' => array('key' => 'target', 'name' => __('Target', 'booking-package'), 'value' => 'adult', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('adult' => __('Adults', 'booking-package'), 'children' => __('Children', 'booking-package')), 'target' => 'hotel'),
                'guestsInCapacity' => array('key' => 'guestsInCapacity', 'name' => __('Include the number of guests in the available slots', 'booking-package'), 'value' => 'adult', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('excluded' => __('Excluded', 'booking-package'), 'included' => __('Included', 'booking-package')), 'target' => 'day'),
                'reflectService' => array('key' => 'reflectService', 'name' => sprintf(__('Price adjustment for "%s" based on selected number of guests', 'booking-package'), __('Services', 'booking-package')), 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')), 'target' => 'day'),
                'reflectAdditional' => array('key' => 'reflectAdditional', 'name' => sprintf(__('Price adjustment for "%s" based on selected number of guests', 'booking-package'), __('Extra charges', 'booking-package')), 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')), 'target' => 'day'),
                'json' => array(
                    'key' => 'json', 
                    'name' => __('Options', 'booking-package'), 
                    'value' => '', 
                    'inputLimit' => 1, 
                    'inputType' => 'EXTRA', 
                    "optionsType" => array(
                        "number" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "name" => array("type" => "TEXT", "value" => "", "target" => "both"),
                        "price" => array("type" => "TEXT", "value" => "", "target" => "hotel"), 
                        
                        "prices" => array(
                            "type" => "dayOfTheWeek", 
                            "target" => "hotel",
                            "options" => array(
                                array("key" => "priceOnMonday", "name" => __('Monday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnTuesday", "name" => __('Tuesday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnWednesday", "name" => __('Wednesday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnThursday", "name" => __('Thursday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnFriday", "name" => __('Friday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnSaturday", "name" => __('Saturday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnSunday", "name" => __('Sunday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 0), 
                                array("key" => "priceOnDayBeforeNationalHoliday", "name" => __('The day Before National holiday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 1), 
                                array("key" => "priceOnNationalHoliday", "name" => __('National holiday', 'booking-package'), "type" => "TEXT", "value" => "0", 'isExtensionsValid' => 1), 
                            ),
                        )
                        
                    ), 
                    'titleList' => array(
                        'number' => __('Number of guests', 'booking-package'), 
                        'name' => __('Option value', 'booking-package'),
                        'price' => __('Extra charge', 'booking-package'), 
                        'prices' => __('Costs', 'booking-package')
                    ), 
                    'target' => 'both'
                ),
            );
            
            if ($this->guestForDayOfTheWeekRates === 0) {
                
                unset($guestsInputTypeList['json']['optionsType']['prices']);
                unset($guestsInputTypeList['json']['titleList']['prices']);
                
            } else {
                
                unset($guestsInputTypeList['json']['optionsType']['price']);
                unset($guestsInputTypeList['json']['titleList']['price']);
                
            }
            
            return $guestsInputTypeList;
            
        }
        
        public function couponsInputType(){
            
            $month = array(
                '1' => array('key' => 1, 'name' => __('Jan', 'booking-package')), 
                '2' => array('key' => 2, 'name' => __('Feb', 'booking-package')), 
                '3' => array('key' => 3, 'name' => __('Mar', 'booking-package')), 
                '4' => array('key' => 4, 'name' => __('Apr', 'booking-package')), 
                '5' => array('key' => 5, 'name' => __('May', 'booking-package')), 
                '6' => array('key' => 6, 'name' => __('Jun', 'booking-package')), 
                '7' => array('key' => 7, 'name' => __('Jul', 'booking-package')), 
                '8' => array('key' => 8, 'name' => __('Aug', 'booking-package')), 
                '9' => array('key' => 9, 'name' => __('Sep', 'booking-package')), 
                '10' => array('key' => 10, 'name' => __('Oct', 'booking-package')), 
                '11' => array('key' => 11, 'name' => __('Nov', 'booking-package')), 
                '12' => array('key' => 12, 'name' => __('Dec', 'booking-package')), 
            );
            
            $day = array();
            for ($i = 1; $i <= 31; $i++) {
                
                $day[$i] = array('key' => $i, 'name' => $i);
                
            }
            
            $yearList = array();
            for ($i = 0; $i <= 10; $i++) {
                
                $year = date('Y') + $i;
                $yearList[$year] = array('key' => $year, 'name' => $year);
                
            }
            
            $couponsInputTypeList = array(
                'id' => array('key' => 'id', 'name' => __('Coupon code', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'target' => 'both'),
                'name' => array('key' => 'name', 'name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'target' => 'both'),
                'description' => array('key' => 'description', 'name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'active' => array('key' => 'active', 'name' => __('Status', 'booking-package'), 'value' => 'true', 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('1' => 'Enabled'), "class" => ""),
                'target' => array('key' => 'target', 'name' => __('Target', 'booking-package'), 'value' => 'customers', 'inputLimit' => 1, 'inputType' => 'RADIO', 'target' => 'both', 'valueList' => array('visitors' => __('Customers', 'booking-package'), 'users' => __('Users', 'booking-package')), "class" => ""),
                'limited' => array('key' => 'limited', 'name' => __('Coupon usage', 'booking-package'), 'value' => 'unlimited', 'inputLimit' => 1, 'inputType' => 'RADIO', 'target' => 'both', 'valueList' => array('unlimited' => __('Offer unlimited coupons', 'booking-package'), 'limited' => __('Offer a limited one-time coupon per users', 'booking-package')), "class" => ""),
                'expirationDate' => array(
                    'key' => 'expirationDate', 
                    'name' => __('Expiration date', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'expirationDateStatus',
                            'name' => '',
                            'value' => '0',
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array('1' => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'expirationDateFromMonth',
                            'name' => __('From', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        2 => array(
                            'key' => 'expirationDateFromDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        3 => array(
                            'key' => 'expirationDateFromYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                        4 => array(
                            'key' => 'expirationDateToMonth',
                            'name' => __('To', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        5 => array(
                            'key' => 'expirationDateToDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        6 => array(
                            'key' => 'expirationDateToYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                    ),
                ),
                'method' => array('key' => 'method', 'name' => __('Calculation method', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array('subtraction' => __('Subtraction', 'booking-package'), 'multiplication' => __('Multiplication', 'booking-package'))),
                'value' => array('key' => 'value', 'name' => __('Value', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
            );
            
            return $couponsInputTypeList;
        }
        
        public function getCurrencies() {
            
            $currencies = array ( 
                'aed' => array('name' => 'AED - United Arab Emirates Dirham', 'ISOdigits' => 2, 'decimals' => 2),
                'afn' => array('name' => 'AFN - Afghan Afghani', 'ISOdigits' => 2, 'decimals' => 2),
                'all' => array('name' => 'ALL - Albanian Lek', 'ISOdigits' => 2, 'decimals' => 2),
                'amd' => array('name' => 'AMD - Armenian Dram', 'ISOdigits' => 2, 'decimals' => 2),
                'ang' => array('name' => 'ANG - Netherlands Antillean Guilder', 'ISOdigits' => 2, 'decimals' => 2),
                'aoa' => array('name' => 'AOA - Angolan Kwanza', 'ISOdigits' => 2, 'decimals' => 2),
                'ars' => array('name' => 'ARS - Argentine Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'aud' => array('name' => 'AUD - Australian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'awg' => array('name' => 'AWG - Aruban Florin', 'ISOdigits' => 2, 'decimals' => 2),
                'azn' => array('name' => 'AZN - Azerbaijani Manat', 'ISOdigits' => 2, 'decimals' => 2),
                'bam' => array('name' => 'BAM - Bosnia and Herzegovina Convertible Mark', 'ISOdigits' => 2, 'decimals' => 2),
                'bbd' => array('name' => 'BBD - Barbadian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'bdt' => array('name' => 'BDT - Bangladeshi Taka', 'ISOdigits' => 2, 'decimals' => 2),
                'bgn' => array('name' => 'BGN - Bulgarian Lev', 'ISOdigits' => 2, 'decimals' => 2),
                'bhd' => array('name' => 'BHD - Bahraini Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'bif' => array('name' => 'BIF - Burundian Franc', 'ISOdigits' => 0, 'decimals' => 2),
                'bmd' => array('name' => 'BMD - Bermudian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'bnd' => array('name' => 'BND - Brunei Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'bob' => array('name' => 'BOB - Bolivian Boliviano', 'ISOdigits' => 2, 'decimals' => 2),
                'brl' => array('name' => 'BRL - Brazilian Real', 'ISOdigits' => 2, 'decimals' => 2),
                'bsd' => array('name' => 'BSD - Bahamian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'btn' => array('name' => 'BTN - Bhutanese Ngultrum', 'ISOdigits' => 2, 'decimals' => 2),
                'bwp' => array('name' => 'BWP - Botswana Pula', 'ISOdigits' => 2, 'decimals' => 2),
                'byn' => array('name' => 'BYN - Belarusian Ruble', 'ISOdigits' => 2, 'decimals' => 2),
                'bzd' => array('name' => 'BZD - Belize Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'cad' => array('name' => 'CAD - Canadian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'cdf' => array('name' => 'CDF - Congolese Franc', 'ISOdigits' => 2, 'decimals' => 2),
                'chf' => array('name' => 'CHF - Swiss Franc', 'ISOdigits' => 2, 'decimals' => 2),
                'ckd' => array('name' => 'CKD - Cook Islands Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'clp' => array('name' => 'CLP - Chilean Peso', 'ISOdigits' => 0, 'decimals' => 0),
                'cny' => array('name' => 'CNY - Chinese Yuan', 'ISOdigits' => 2, 'decimals' => 2),
                'cop' => array('name' => 'COP - Colombian Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'crc' => array('name' => 'CRC - Costa Rican Colon', 'ISOdigits' => 2, 'decimals' => 2),
                'cuc' => array('name' => 'CUC - Cuban convertible Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'cup' => array('name' => 'CUP - Cuban Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'cve' => array('name' => 'CVE - Cabo Verdean Escudo', 'ISOdigits' => 2, 'decimals' => 2),
                'czk' => array('name' => 'CZK - Czech Koruna', 'ISOdigits' => 2, 'decimals' => 2),
                'djf' => array('name' => 'DJF - Djiboutian Franc', 'ISOdigits' => 0, 'decimals' => 2),
                'dkk' => array('name' => 'DKK - Danish Krone', 'ISOdigits' => 2, 'decimals' => 2),
                'dop' => array('name' => 'DOP - Dominican Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'dzd' => array('name' => 'DZD - Algerian Dinar', 'ISOdigits' => 2, 'decimals' => 2),
                'egp' => array('name' => 'EGP - Egyptian Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'ehp' => array('name' => 'EHP - Sahrawi Peseta', 'ISOdigits' => 2, 'decimals' => 2),
                'ern' => array('name' => 'ERN - Eritrean Nakfa', 'ISOdigits' => 2, 'decimals' => 2),
                'etb' => array('name' => 'ETB - Ethiopian Birr', 'ISOdigits' => 2, 'decimals' => 2),
                'eur' => array('name' => 'EUR - Euro', 'ISOdigits' => 2, 'decimals' => 2),
                'fjd' => array('name' => 'FJD - Fijian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'fkp' => array('name' => 'FKP - Falkland Islands Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'fok' => array('name' => 'FOK - Faroese Króna', 'ISOdigits' => 2, 'decimals' => 2),
                'gbp' => array('name' => 'GBP - Pound Sterling', 'ISOdigits' => 2, 'decimals' => 2),
                'gel' => array('name' => 'GEL - Georgian Lari', 'ISOdigits' => 2, 'decimals' => 2),
                'ggp' => array('name' => 'GGP - Guernsey Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'ghs' => array('name' => 'GHS - Ghanaian Cedi', 'ISOdigits' => 2, 'decimals' => 2),
                'gip' => array('name' => 'GIP - Gibraltar Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'gmd' => array('name' => 'GMD - Gambian Dalasi', 'ISOdigits' => 2, 'decimals' => 2),
                'gnf' => array('name' => 'GNF - Guinean Franc', 'ISOdigits' => 0, 'decimals' => 2),
                'gtq' => array('name' => 'GTQ - Guatemalan Quetzal', 'ISOdigits' => 2, 'decimals' => 2),
                'gyd' => array('name' => 'GYD - Guyanese Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'hkd' => array('name' => 'HKD - Hong Kong Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'hnl' => array('name' => 'HNL - Honduran Lempira', 'ISOdigits' => 2, 'decimals' => 2),
                'hrk' => array('name' => 'HRK - Croatian Kuna', 'ISOdigits' => 2, 'decimals' => 2),
                'htg' => array('name' => 'HTG - Haitian Gourde', 'ISOdigits' => 2, 'decimals' => 2),
                'huf' => array('name' => 'HUF - Hungarian Forint', 'ISOdigits' => 2, 'decimals' => 2),
                'idr' => array('name' => 'IDR - Indonesian Rupiah', 'ISOdigits' => 2, 'decimals' => 2),
                'ils' => array('name' => 'ILS - Israeli new Shekel', 'ISOdigits' => 2, 'decimals' => 2),
                'imp' => array('name' => 'IMP - Manx Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'inr' => array('name' => 'INR - Indian Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'iqd' => array('name' => 'IQD - Iraqi Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'irr' => array('name' => 'IRR - Iranian Rial', 'ISOdigits' => 2, 'decimals' => 2),
                'isk' => array('name' => 'ISK - Icelandic Krona', 'ISOdigits' => 0, 'decimals' => 2),
                'jep' => array('name' => 'JEP - Jersey Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'jmd' => array('name' => 'JMD - Jamaican Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'jod' => array('name' => 'JOD - Jordanian Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'jpy' => array('name' => 'JPY - Japanese Yen', 'ISOdigits' => 0, 'decimals' => 2),
                'kes' => array('name' => 'KES - Kenyan Shilling', 'ISOdigits' => 2, 'decimals' => 2),
                'kgs' => array('name' => 'KGS - Kyrgyzstani Som', 'ISOdigits' => 2, 'decimals' => 2),
                'khr' => array('name' => 'KHR - Cambodian Riel', 'ISOdigits' => 2, 'decimals' => 2),
                'kid' => array('name' => 'KID - Kiribati Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'kmf' => array('name' => 'KMF - Comorian Franc', 'ISOdigits' => 0, 'decimals' => 2),
                'kpw' => array('name' => 'KPW - North Korean Won', 'ISOdigits' => 2, 'decimals' => 2),
                'krw' => array('name' => 'KRW - South Korean Won', 'ISOdigits' => 0, 'decimals' => 2),
                'kwd' => array('name' => 'KWD - Kuwaiti Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'kyd' => array('name' => 'KYD - Cayman Islands Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'kzt' => array('name' => 'KZT - Kazakhstani Tenge', 'ISOdigits' => 2, 'decimals' => 2),
                'lak' => array('name' => 'LAK - Lao Kip', 'ISOdigits' => 2, 'decimals' => 2),
                'lbp' => array('name' => 'LBP - Lebanese Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'lkr' => array('name' => 'LKR - Sri Lankan Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'lrd' => array('name' => 'LRD - Liberian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'lsl' => array('name' => 'LSL - Lesotho Loti', 'ISOdigits' => 2, 'decimals' => 2),
                'lyd' => array('name' => 'LYD - Libyan Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'mad' => array('name' => 'MAD - Moroccan Dirham', 'ISOdigits' => 2, 'decimals' => 2),
                'mdl' => array('name' => 'MDL - Moldovan Leu', 'ISOdigits' => 2, 'decimals' => 2),
                'mga' => array('name' => 'MGA - Malagasy Ariary', 'ISOdigits' => 2, 'decimals' => 0),
                'mkd' => array('name' => 'MKD - Macedonian Denar', 'ISOdigits' => 2, 'decimals' => 2),
                'mmk' => array('name' => 'MMK - Myanmar Kyat', 'ISOdigits' => 2, 'decimals' => 2),
                'mnt' => array('name' => 'MNT - Mongolian Tögrög', 'ISOdigits' => 2, 'decimals' => 2),
                'mop' => array('name' => 'MOP - Macanese Pataca', 'ISOdigits' => 2, 'decimals' => 2),
                'mru' => array('name' => 'MRU - Mauritanian Ouguiya', 'ISOdigits' => 2, 'decimals' => 0),
                'mur' => array('name' => 'MUR - Mauritian Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'mvr' => array('name' => 'MVR - Maldivian Rufiyaa', 'ISOdigits' => 2, 'decimals' => 2),
                'mwk' => array('name' => 'MWK - Malawian Kwacha', 'ISOdigits' => 2, 'decimals' => 2),
                'mxn' => array('name' => 'MXN - Mexican Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'myr' => array('name' => 'MYR - Malaysian Ringgit', 'ISOdigits' => 2, 'decimals' => 2),
                'mzn' => array('name' => 'MZN - Mozambican Metical', 'ISOdigits' => 2, 'decimals' => 2),
                'nad' => array('name' => 'NAD - Namibian Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'ngn' => array('name' => 'NGN - Nigerian Naira', 'ISOdigits' => 2, 'decimals' => 2),
                'nio' => array('name' => 'NIO - Nicaraguan Córdoba', 'ISOdigits' => 2, 'decimals' => 2),
                'nok' => array('name' => 'NOK - Norwegian Krone', 'ISOdigits' => 2, 'decimals' => 2),
                'npr' => array('name' => 'NPR - Nepalese Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'nzd' => array('name' => 'NZD - New Zealand Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'omr' => array('name' => 'OMR - Omani Rial', 'ISOdigits' => 3, 'decimals' => 3),
                'pab' => array('name' => 'PAB - Panamanian Balboa', 'ISOdigits' => 2, 'decimals' => 2),
                'pen' => array('name' => 'PEN - Peruvian Sol', 'ISOdigits' => 2, 'decimals' => 2),
                'pgk' => array('name' => 'PGK - Papua New Guinean Kina', 'ISOdigits' => 2, 'decimals' => 2),
                'php' => array('name' => 'PHP - Philippine Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'pkr' => array('name' => 'PKR - Pakistani Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'pln' => array('name' => 'PLN - Polish Zloty', 'ISOdigits' => 2, 'decimals' => 2),
                'pnd' => array('name' => 'PND - Pitcairn Islands Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'prb' => array('name' => 'PRB - Transnistrian Ruble', 'ISOdigits' => 2, 'decimals' => 2),
                'pyg' => array('name' => 'PYG - Paraguayan Guaraní', 'ISOdigits' => 0, 'decimals' => 2),
                'qar' => array('name' => 'QAR - Qatari Riyal', 'ISOdigits' => 2, 'decimals' => 2),
                'ron' => array('name' => 'RON - Romanian Leu', 'ISOdigits' => 2, 'decimals' => 2),
                'rsd' => array('name' => 'RSD - Serbian Dinar', 'ISOdigits' => 2, 'decimals' => 2),
                'rub' => array('name' => 'RUB - Russian Ruble', 'ISOdigits' => 2, 'decimals' => 2),
                'rwf' => array('name' => 'RWF - Rwandan Franc', 'ISOdigits' => 0, 'decimals' => 2),
                'sar' => array('name' => 'SAR - Saudi Riyal', 'ISOdigits' => 2, 'decimals' => 2),
                'sbd' => array('name' => 'SBD - Solomon Islands Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'scr' => array('name' => 'SCR - Seychellois Rupee', 'ISOdigits' => 2, 'decimals' => 2),
                'sdg' => array('name' => 'SDG - Sudanese Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'sek' => array('name' => 'SEK - Swedish Krona', 'ISOdigits' => 2, 'decimals' => 2),
                'sgd' => array('name' => 'SGD - Singapore Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'shp' => array('name' => 'SHP - Saint Helena Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'sll' => array('name' => 'SLL - Sierra Leonean Leone', 'ISOdigits' => 2, 'decimals' => 2),
                'sls' => array('name' => 'SLS - Somaliland Shilling', 'ISOdigits' => 2, 'decimals' => 2),
                'sos' => array('name' => 'SOS - Somali Shilling', 'ISOdigits' => 2, 'decimals' => 2),
                'srd' => array('name' => 'SRD - Surinamese Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'ssp' => array('name' => 'SSP - South Sudanese Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'stn' => array('name' => 'STN - Sao Tome and Príncipe Dobra', 'ISOdigits' => 2, 'decimals' => 2),
                'svc' => array('name' => 'SVC - Salvadoran Colón', 'ISOdigits' => 2, 'decimals' => 2),
                'syp' => array('name' => 'SYP - Syrian Pound', 'ISOdigits' => 2, 'decimals' => 2),
                'szl' => array('name' => 'SZL - Swazi Lilangeni', 'ISOdigits' => 2, 'decimals' => 2),
                'thb' => array('name' => 'THB - Thai Baht', 'ISOdigits' => 2, 'decimals' => 2),
                'tjs' => array('name' => 'TJS - Tajikistani Somoni', 'ISOdigits' => 2, 'decimals' => 2),
                'tmt' => array('name' => 'TMT - Turkmenistan Manat', 'ISOdigits' => 2, 'decimals' => 2),
                'tnd' => array('name' => 'TND - Tunisian Dinar', 'ISOdigits' => 3, 'decimals' => 3),
                'top' => array('name' => 'TOP - Tongan Paʻanga', 'ISOdigits' => 2, 'decimals' => 2),
                'try' => array('name' => 'TRY - Turkish Lira', 'ISOdigits' => 2, 'decimals' => 2),
                'ttd' => array('name' => 'TTD - Trinidad and Tobago Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'tvd' => array('name' => 'TVD - Tuvaluan Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'twd' => array('name' => 'TWD - New Taiwan Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'tzs' => array('name' => 'TZS - Tanzanian Shilling', 'ISOdigits' => 2, 'decimals' => 2),
                'uah' => array('name' => 'UAH - Ukrainian Hryvnia', 'ISOdigits' => 2, 'decimals' => 2),
                'ugx' => array('name' => 'UGX - Ugandan Shilling', 'ISOdigits' => 0, 'decimals' => 2),
                'usd' => array('name' => 'USD - United States Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'uyu' => array('name' => 'UYU - Uruguayan Peso', 'ISOdigits' => 2, 'decimals' => 2),
                'uzs' => array('name' => 'UZS - Uzbekistani Som', 'ISOdigits' => 2, 'decimals' => 2),
                'ved' => array('name' => 'VED - Venezuelan bolívar digital', 'ISOdigits' => 2, 'decimals' => 2),
                'ves' => array('name' => 'VES - Venezuelan Bolívar Soberano', 'ISOdigits' => 2, 'decimals' => 2),
                'vnd' => array('name' => 'VND - Vietnamese Dong', 'ISOdigits' => 0, 'decimals' => 2),
                'vuv' => array('name' => 'VUV - Vanuatu Vatu', 'ISOdigits' => 0, 'decimals' => 0),
                'wst' => array('name' => 'WST - Samoan Tala', 'ISOdigits' => 2, 'decimals' => 2),
                'xaf' => array('name' => 'XAF - Central African CFA Franc BEAC', 'ISOdigits' => 0, 'decimals' => 2),
                'xcd' => array('name' => 'XCD - East Caribbean Dollar', 'ISOdigits' => 2, 'decimals' => 2),
                'xof' => array('name' => 'XOF - West African CFA Franc BCEAO', 'ISOdigits' => 0, 'decimals' => 2),
                'xpf' => array('name' => 'XPF - CFP Franc (Franc Pacifique)', 'ISOdigits' => 0, 'decimals' => 0),
                'yer' => array('name' => 'YER - Yemeni Rial', 'ISOdigits' => 2, 'decimals' => 2),
                'zar' => array('name' => 'ZAR - South African Rand', 'ISOdigits' => 2, 'decimals' => 2),
                'zmw' => array('name' => 'ZMW - Zambian Kwacha', 'ISOdigits' => 2, 'decimals' => 2),
                'zwb' => array('name' => 'ZWB - RTGS Dollar', 'ISOdigits' => 0, 'decimals' => 0),
                'zwl' => array('name' => 'ZWL - Zimbabwean Dollar', 'ISOdigits' => 2, 'decimals' => 2),
            );
            
            return $currencies;
            
        }
        
        public function getList(){
            
            $list =  array(
                "General" => array(
                    'site_name' => array('name' => __('Site name', 'booking-package'), 'value' => 'Site name', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'account_email_address' => array(
                        'key' => 'email_address', 
                        'name' => __('Email address', 'booking-package'), 
                        'target' => 'both', 
                        'disabled' => 0, 
                        'value' => '1', 
                        'inputLimit' => 2, 
                        'inputType' => 'MULTIPLE_FIELDS', 
                        'isExtensionsValid' => 0, 
                        'option' => 0,
                        'valueList' => array(
                            0 => array(
                                'key' => 'email_to',
                                'name' => __('Receiving email address', 'booking-package') . ': ',
                                'value' => '',
                                'inputType' => 'TEXT',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            1 => array(
                                'key' => 'email_title_from',
                                'name' => __('Sender name', 'booking-package') . ': ',
                                'value' => '',
                                'inputType' => 'TEXT',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            2 => array(
                                'key' => 'email_from',
                                'name' => __('Sending email address', 'booking-package') . ': ',
                                'value' => '',
                                'inputType' => 'TEXT',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                        ),
                    ),
                    /**
                    'email_to' => array('name' => __('Receiving email address', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'email_from' => array('name' => __('Sending email address', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'email_title_from' => array('name' => __('Sender name', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    **/
                    'country' => array('name' => __('Country', 'booking-package'), 'value' => 'US', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'SELECT_GROUP', 'valueList' => array()),
                    'currency' => array('name' => __('Currency', 'booking-package'), 'value' => 'usd', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'SELECT', 'valueList' => 
                        array(
                            'usd' => 'USD - United States of America', 
                            'gbp' => 'GBP - United Kingdom', 
                            'eur' => 'EUR - EU', 
                            'jpy' => 'JPY - 日本円', 
                            'dkk' => 'DKK - Dansk krone', 
                            'cny' => 'CNY - 人民币',
                            'twd' => 'TWD - 台湾元', 
                            'thb' => 'THB - Thai Baht', 
                            'cop' => 'COP - Peso Colombiano', 
                            'cad' => 'CAD - Canadian Dollar', 
                            'aud' => 'AUD - Australian Dollar', 
                            'huf' => 'HUF - Magyar forint', 
                            'php' => 'PHP - Philippine Peso', 
                            'chf' => 'CHF - Swiss franc',
                            'czk' => 'CZK - Koruna česká',
                            'rub' => 'RUB - Российский рубль',
                            'nzd' => 'NZD - New Zealand Dollar',
                            'hrk' => 'HRK - Croatian kuna',
                            'uah' => 'UAH - Українська гривня',
                            'brl' => 'BRL - Real brasileiro',
                            'krw' => 'KRW - 한국 원',
                            'aed' => 'AED - United Arab Emirates',
                            'gtq' => 'GTQ - Guatemalan Quetzal',
                            'mxn' => 'MXN - Peso Mexicano',
                            'ars' => 'ARS - Peso Argentino',
                            'zar' => 'ZAR - South African Rand',
                            'try' => 'TRY - Türk Lirası',
                            'sek' => 'SEK - Svensk krona',
                            'ron' => 'RON - Leu românesc',
                            'inr' => 'INR - भारतीय रुपया',
                            'sgd' => 'SGD - Singapore Dollar',
                            'idr' => 'IDR - Rupiah Indonesia',
                        )
                    ),
                    'timezone' => array('name' => __('Default Timezone', 'booking-package'), 'value' => 'UTC', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'SELECT_TIMEZONE', 'valueList' => array()),
                    'dateFormat' => array('name' => __('Date format', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'SELECT', 'valueList' => array()),
                    'clock' => array('key' => 'clock', 'name' => __('Time Format', 'booking-package'), 'value' => '24hours', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => 
                        array(
                            '12a.m.p.m' => __('09:00 a.m.', 'booking-package'), 
                            '12ampm' => __('09:00 am', 'booking-package'), 
                            '12AMPM' => __('03:00 PM', 'booking-package'), 
                            '24hours' => '17:00'
                        )
                    ),
                    'positionTimeDate' => array('key' => 'positionTimeDate', 'name' => __('Position of date and time', 'booking-package'), 'value' => 'dateTime', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => 
                        array(
                            'timeDate' => __('Time', 'booking-package') . ' - ' . __('Date', 'booking-package'), 
                            'dateTime' => __('Date', 'booking-package') . ' - ' . __('Time', 'booking-package'), 
                        )
                    ),
                    'positionOfWeek' => array('name' => __('Position of the day of the week', 'booking-package'), 'value' => 'before', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'RADIO', 'valueList' => array('before' => __('Before the date', 'booking-package'), 'after' => __('After the date', 'booking-package'))),
                    'automaticApprove' => array('name' => __('Automatically approve of booking', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('1' => __('Enabled', 'booking-package'))), 
                    'dataRetentionPeriod' => array('name' => __('Data retention period of customer', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'SELECT', 'valueList' => 
                        array(
                            '0' => __('Forever', 'booking-package'), 
                            '30' => sprintf(__('%d days', 'booking-package'), 30), 
                            '90' => sprintf(__('%d days', 'booking-package'), 90), 
                            '180' => sprintf(__('%d days', 'booking-package'), 180), 
                            '365' => sprintf(__('%d year', 'booking-package'), 1), 
                            '730' => sprintf(__('%d years', 'booking-package'), 2), 
                            '1095' => sprintf(__('%d years', 'booking-package'), 3), 
                            '1460' => sprintf(__('%d years', 'booking-package'), 4), 
                            '1825' => sprintf(__('%d years', 'booking-package'), 5), 
                        )
                    ), 
                    'ajax_url' => array('name' => __('Select the URL for AJAX on the public page', 'booking-package'), 'value' => 'ajax', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => 
                    array(
                        'ajax' => plugins_url() . '/booking-package/ajax.php',
                        'top' => get_home_url(),
                        'admin-ajax' => admin_url('admin-ajax.php'),
                    )), 
                    'ajax_nonce_function' => array('name' => __('Select a function to validate the value of a nonce with AJAX on the public page', 'booking-package'), 'value' => 'custom_nonce_validation', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('check_ajax_referer' => 'check_ajax_referer()', 'wp_verify_nonce' => 'wp_verify_nonce()', 'custom_nonce_validation' => __('Use custom plugin nonce validation', 'booking-package'))), 
                    
                    'javascriptSyntaxErrorNotification' => array('name' => __('Javascript syntax error notification', 'booking-package'), 'deprecated' => false, 'value' => 1, 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('1' => __('Automatically notify developers', 'booking-package'))), 
                    
                    'characterCodeOfDownloadFile' => array('name' => __('Character code of download file', 'booking-package'), 'value' => 'UTF-8', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'RADIO', 'valueList' => array('UTF-8' => 'UTF-8', 'EUC-JP' => 'EUC-JP', 'SJIS' => 'SJIS')),
                    'lineBreakCodesInCsv' => array('name' => __('Line break codes in a CSV file', 'booking-package'), 'value' => 'LF', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'RADIO', 'valueList' => array('LF' => 'LF', 'CRLF' => 'CRLF')),
                    
                    'googleAnalytics' => array('name' => __('Tracking ID for the Google analytics', 'booking-package'), 'deprecated' => true, 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    
                    'customizeStatus' => array(
                        'key' => 'customizeStatus', 
                        'name' => __('Color settings for booked customer status', 'booking-package'), 
                        'disabled' => 0, 
                        'value' => '1', 
                        'inputLimit' => 2, 
                        'inputType' => 'MULTIPLE_FIELDS', 
                        'isExtensionsValid' => 0, 
                        'option' => 0,
                        'valueList' => array(
                            0 => array(
                                'key' => 'statusFontColor',
                                'name' => __('Font Color', 'booking-package') . ': ',
                                'value' => '#fff',
                                'inputType' => 'COLOR',
                                'className' => 'multiple_fields_margin_top',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            1 => array(
                                'key' => 'statusBackgroundColorForApproved',
                                'name' => __('Approved background color', 'booking-package') . ': ',
                                'value' => '#98c878',
                                'inputType' => 'COLOR',
                                'className' => 'multiple_fields_margin_top',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            2 => array(
                                'key' => 'statusBackgroundColorForPending',
                                'name' => __('Pending background color', 'booking-package') . ': ',
                                'value' => '#f06767',
                                'inputType' => 'COLOR',
                                'className' => 'multiple_fields_margin_top',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            3 => array(
                                'key' => 'statusBackgroundColorForCanceled',
                                'name' => __('Canceled background color', 'booking-package') . ': ',
                                'value' => '#f0c267',
                                'inputType' => 'COLOR',
                                'className' => 'multiple_fields_margin_top',
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(),
                            ),
                        ),
                    ), 
                    
                    
                ),
                "Design" => array(
                    'autoWindowScroll' => array('name' => __('Automatic scroll to the top on the booking field', 'booking-package'), 'value' => '1', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('1' => __('Enabled', 'booking-package'))),
                    'headingPosition' => array('name' => __('Define "position: sticky" for the css (style) in the calendar for visitors', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('1' => __('Enabled', 'booking-package'))),
                    'fontSize' => array('name' => __('Font size', 'booking-package'), 'value' => '16px', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    #'fontColor' => array('name' => __('Font color', 'booking-package'), 'value' => '#969696', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'backgroundColor' => array('name' => __('Background color', 'booking-package'), 'value' => '#FFF', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'calendarBackgroundColorWithSchedule' => array('name' => __('Calendar background color with schedule', 'booking-package'), 'value' => '#FFF', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'calendarBackgroundColorWithNoSchedule' => array('name' => __('Calendar background color with no schedule', 'booking-package'), 'value' => '#EEE', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'backgroundColorOfRegularHolidays' => array('name' => __('Background color of closed days', 'booking-package'), 'value' => '#FFD5D5', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    
                    'scheduleAndServiceBackgroundColor' => array('name' => __('Schedule and service background color', 'booking-package'), 'value' => '#FFF', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'backgroundColorOfSelectedLabel' => array('name' => __('Background color of selected label', 'booking-package'), 'value' => '#EAEDF3', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'mouseHover' => array('name' => __('Background color when the pointer overlaps a link', 'booking-package'), 'value' => '#EAEDF3', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    'borderColor' => array('name' => __('Border color', 'booking-package'), 'value' => '#ddd', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT', 'js' => 'colorPicker'), 
                    
                ),
                "twilio" => array(
                    'twilio_active' => array('name' => __('Active', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'twilio_sendingMethod' => array('name' => __('Sending method', 'booking-package'), 'value' => 'phoneNumber', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('phoneNumber' => __('Phone number', 'booking-package'), 'senderID' => __('Alphanumeric Sender ID', 'booking-package'))), 
                    'twilio_sid' => array('name' => __('Account SID', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'twilio_service_sid' => array('name' => __('Messaging Service SID', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'twilio_token' => array('name' => __('Auth token', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'twilio_countryCode' => array('name' => __('Country calling code', 'booking-package'), 'value' => '', 'placeholder' => '+1', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'twilio_number' => array('name' => __('Phone number', 'booking-package'), 'value' => '', 'placeholder' => '+11234567890', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                ),
                "Messaging Services" => array(
                    'whatsApp' => array(
                        'key' => 'whatsApp', 
                        'name' => 'WhatsApp', 
                        'target' => 'day', 
                        'disabled' => 0, 
                        'value' => '1', 
                        'inputLimit' => 2, 
                        'inputType' => 'MULTIPLE_FIELDS', 
                        'isExtensionsValid' => 0, 
                        'option' => 0,
                        'valueList' => array(
                            0 => array(
                                'key' => 'whatsApp_active',
                                'name' => '',
                                'value' => 0,
                                'inputType' => 'RADIO',
                                'inputLimit' => 0,
                                'isExtensionsValid' => 0, 
                                'actions' => null,
                                'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')),
                            ),
                            1 => array(
                                'key' => 'whatsApp_countryCode',
                                'name' => __('Country calling code', 'booking-package') . ': ',
                                'value' => null,
                                'inputType' => 'TEXT',
                                'inputLimit' => 0,
                                'placeholder' => '+1',
                                'isExtensionsValid' => 0, 
                                'class' => 'multiple_fields_margin_top',
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            2 => array(
                                'key' => 'whatsApp_phoneId',
                                'name' => __('Phone number ID', 'booking-package') . ': ',
                                'value' => null,
                                'inputType' => 'TEXT',
                                'inputLimit' => 0,
                                'isExtensionsValid' => 0, 
                                'class' => 'multiple_fields_margin_top',
                                'actions' => null,
                                'valueList' => array(),
                            ),
                            3 => array(
                                'key' => 'whatsApp_token',
                                'name' => __('Token', 'booking-package') . ': ',
                                'value' => null,
                                'inputType' => 'TEXT',
                                'inputLimit' => 0,
                                'isExtensionsValid' => 0, 
                                'class' => 'multiple_fields_margin_top',
                                'actions' => null,
                                'valueList' => array(),
                            ),
                        ),
                    ), 
                    
                ),
                
                
                
                "Mailgun" => array(
                    'mailgun_active' => array('name' => __('Active', 'booking-package'), 'deprecated' => true, 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'mailgun_aip_base_url' => array('name' => __('API Base URL', 'booking-package'), 'deprecated' => true, 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'mailgun_api_key' => array('name' => __('API Key', 'booking-package'), 'deprecated' => true, 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    /**
                    'mailgun_password' => array('name' => __('Password', 'booking-package'), 'value' => '', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'TEXT'),
                    **/
                ),
                "Stripe" => array(
                    'stripe_active' => array('name' => __('Active', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'stripe_public_key' => array('name' => __('Public Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'stripe_secret_key' => array('name' => __('Secret Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'TEXT'),
                    'stripe_capture_method' => array('name' => __('Capture method for payment intent', 'booking-package'), 'value' => 'automatic', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('automatic' => __('Automatic', 'booking-package'), 'manual' => __('Manual', 'booking-package'))), 
                    'stripe_konbini_expiration_date' => array('name' => __('Expiration date for convenience store payments', 'booking-package'), 'value' => '120', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => 
                        array(
                            '60' => sprintf(__('%d minutes', 'booking-package'), 60), 
                            '120' => sprintf(__('%d minutes', 'booking-package'), 120), 
                            '180' => sprintf(__('%d minutes', 'booking-package'), 180), 
                            '360' => sprintf(__('%d minutes', 'booking-package'), 360), 
                            '720' => sprintf(__('%d minutes', 'booking-package'), 720), 
                            '1440' => sprintf(__('%d days', 'booking-package'), 1), 
                            '2880' => sprintf(__('%d days', 'booking-package'), 2), 
                            '4320' => sprintf(__('%d days', 'booking-package'), 3), 
                            '5760' => sprintf(__('%d days', 'booking-package'), 4), 
                            '7200' => sprintf(__('%d days', 'booking-package'), 5), 
                            '8640' => sprintf(__('%d days', 'booking-package'), 6), 
                            '10080' => sprintf(__('%d days', 'booking-package'), 7), 
                        ),
                    ), 
                ),
                "PayPal" => array(
                    'paypal_active' => array('name' => __('Active', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'paypal_live' => array('name' => __('Mode', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Live', 'booking-package'), '0' => __('Test', 'booking-package'))), 
                    'paypal_client_id' => array('name' => __('Client ID', 'booking-package'), 'value' => '', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'paypal_secret_key' => array('name' => __('Secret Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 1, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                ),
                "reCAPTCHA" => array(
                    'googleReCAPTCHA_active' => array('name' => __('Active', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'googleReCAPTCHA_site_key' => array('name' => __('Site Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'googleReCAPTCHA_Secret_key' => array('name' => __('Secret Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'),
                    'googleReCAPTCHA_version' => array('name' => __('Version', 'booking-package'), 'value' => 'v2', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('v2' => 'v2', 'v3' => 'v3')), 
                ),
                "hCaptcha" => array(
                    'hCaptcha_active' => array('name' => __('Active', 'booking-package'), 'value' => '0', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('1' => __('Enabled', 'booking-package'), '0' => __('Disabled', 'booking-package'))), 
                    'hCaptcha_site_key' => array('name' => __('Site Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'), 
                    'hCaptcha_Secret_key' => array('name' => __('Secret Key', 'booking-package'), 'value' => '', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'TEXT'),
                    'hCaptcha_Theme' => array('name' => __('Theme', 'booking-package'), 'value' => 'light', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('light' => __('Light', 'booking-package'), 'dark' => __('Dark', 'booking-package'))),
                    'hCaptcha_Size' => array('name' => __('Size', 'booking-package'), 'value' => 'normal', 'isExtensionsValid' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('normal' => __('Normal', 'booking-package'), 'compact' => __('Compact', 'booking-package'))),
                    
                ),
            );
            
            $newDataFormatList = array();
            
            $dateFormatList = array(
                array("format" => "m/d/Y / m/Y", "type" => "number"),
                array("format" => "m-d-Y / m-Y", "type" => "number"),
                array("format" => "% d, Y / %, Y", "type" => "string"),
                array("format" => "d/m/Y / m/Y", "type" => "number"),
                array("format" => "d-m-Y / m-Y", "type" => "number"),
                array("format" => "d %, Y / %, Y", "type" => "string"),
                array("format" => "Y/m/d / Y/m", "type" => "number"),
                array("format" => "Y-m-d / Y-m", "type" => "number"),
                array("format" => "d.m.Y / m.Y", "type" => "number"),
                array("format" => "d.m.Y / %.Y", "type" => "string"),
                array("format" => "d.%.Y / %.Y", "type" => "string"),
                array("format" => "% d Y / % Y", "type" => "string"),
                array("format" => "d % Y / % Y", "type" => "string"),
                array("format" => "d.m.Y / % Y", "type" => "string"),
                array("format" => "d.%.Y / % Y", "type" => "string"),
                array("format" => "Y年m月d日 / Y年m月", "type" => "number"),
            );
            
            $month = __(date('F'), 'booking-package');
            for ($i = 0; $i < count($dateFormatList); $i++) {
                
                $format = $dateFormatList[$i];
                $date = null;
                if ($format['type'] == 'number') {
                    
                    $date = date($format['format']);
                    
                } else {
                    
                    $date = date($format['format']);
                    $date = str_replace('%', $month, $date);
                    
                }
                
                $dateFormatList[$i] = $date;
                
            }
            
            $list['General']['dateFormat']['valueList'] = $dateFormatList;
            
            if (class_exists('NumberFormatter') === true) {
                
                $currencies_name = array();
                $currencies = $this->getCurrencies();
                foreach ($currencies as $key => $value) {
                    
                    $currencies_name[$key] = $value['name'];
                    
                }
                
                $list['General']['currency']['valueList'] = $currencies_name;
                
            }
            
            
            foreach ((array) $list as $listKey => $listValue) {
                
                $category = array();
                foreach ((array) $listValue as $key => $value) {
                    
                    if ($value['inputType'] !== 'MULTIPLE_FIELDS') {
                        
                        $optionsValue = get_option($this->prefix . $key);
                        if ($optionsValue !== false) {
                            
                            $value['value'] = $optionsValue;
                            
                        }
                        
                        
                        
                    } else {
                        
                        for ($i = 0; $i < count($value['valueList']); $i++) {
                            
                            $value['valueList'][$i]['key'] = $this->prefix . $value['valueList'][$i]['key'];
                            $optionsValue = get_option($value['valueList'][$i]['key']);
                            if ($optionsValue !== false) {
                                
                                $value['valueList'][$i]['value'] = $optionsValue;
                                
                            }
                            
                        }
                        
                    }
                    
                    $category[$this->prefix . $key] = $value;
                    
                    
                    
                }
                
                $list[$listKey] = $category;
                
            }
            
            return $list;
            
        }
        
        public function getBookingSyncList($accountKey = false){
            
            $list = array();
            $booking_sync = $this->booking_sync();
            foreach ((array) $booking_sync as $listKey => $listValue) {
                
                $category = array();
                foreach ((array) $listValue as $key => $value) {
                    
                    $optionsValue = get_option($this->prefix.$key);
                    if($optionsValue !== false){
                        
                        $value['value'] = stripslashes($optionsValue);
                        
                    }
                    
                    $category[$this->prefix.$key] = $value;
                    
                }
                
                $list[$listKey] = $category;
                
            }
            
            return $list;
            
        }
        
        public function getMemberSetting($extension = false){
            
            $userFunctions = $this->userFunctions();
            foreach ((array) $userFunctions as $key => $input) {
                
                $defaultValue = $input['value'];
                $value = get_option($this->prefix . $key);
                if ($value !== false) {
                    
                    $userFunctions[$key]['value'] = $value;
                    
                } else {
                    
                    add_option($this->prefix . $key, sanitize_text_field($defaultValue));
                    
                }
                
                if ($extension !== true && $input['isExtensionsValid'] == 1) {
                    
                    $userFunctions[$key]['value'] = 0;
                    
                }
                
            }
            
            return $userFunctions;
            
        }
        
        public function getMemberSettingValues(){
            
            $userFunctions = $this->userFunctions();
            $values = array(
                'function_for_member' => $userFunctions['function_for_member']['value'],
                'visitors_registration_for_member' => $userFunctions['visitors_registration_for_member']['value'],
                'check_email_for_member' => $userFunctions['check_email_for_member']['value'],
                'reject_non_membder' => $userFunctions['reject_non_membder']['value'],
                'accept_subscribers_as_users' => $userFunctions['accept_subscribers_as_users']['value'],
                'accept_contributors_as_users' => $userFunctions['accept_contributors_as_users']['value'],
                /**
                'accept_authors_as_users' => $userFunctions['accept_authors_as_users']['value'],
                **/
                'user_toolbar' => $userFunctions['user_toolbar']['value'],
                'lost_password' => $userFunctions['lost_password']['value'],
            );
            
            foreach ((array) $values as $key => $value) {
                
                $value = get_option($this->prefix.$key, $value);
                $values[$key] = $value;
                
            }
            
            $values['lost_password_url'] = wp_lostpassword_url(get_permalink());
            
            return $values;
            
        }
        
        public function getEmailMessageList($accountKey = 1, $calendarName = null, $calendarAccount = null) {
            
            if (empty($calendarName)) {
                
                $calendarName = 'Your Calendar';
                
            }
            
            $enable = 1;
            $messages = array(
                'mail_new_admin' => array(
                    'enable' => 1, 
                    'subject' => "Booking notification for your visitors [Booking Package]", 
                    'content' => sprintf("Hello,\n\nID: [id] \nFirst name: [firstname] \nLast name: [lastname] \nEmail: [email] \nPhone: [phone] \nAddress: [address] \n\nYou can edit this message anytime in the \"Notifications\" tab on the %s.\n\nThank you for trying Booking Package.", $calendarName), 
                    'subjectForAdmin' => 'Booking notification for you [Booking Package]', 
                    'contentForAdmin' => '',
                ),
                'mail_approved' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
                'mail_canceled_by_visitor_user' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
                'mail_deleted' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
                'mail_pending' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
                'mail_updated' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
                'mail_reminder' => array('enable' => 0, 'subject' => "", 'content' => "", 'subjectForAdmin' => '', 'contentForAdmin' => '',),
            );
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_email_settings";
            $email_message = $this->email_message;
            foreach ((array) $email_message as $key => $value) {
                
                #var_dump($value);
                $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d AND `mail_id` = %s;", array(intval($accountKey), $value['key']));
                $row = $wpdb->get_row($sql, ARRAY_A);
                if (is_null($row)) {
                    
                    #var_dump($row);
                    $wpdb->insert(
                        $table_name, 
                        array(
                            'accountKey' => intval($accountKey), 
                            'mail_id' => sanitize_text_field($value['key']),
                            'enable' => intval($messages[$key]['enable']),
                            'data' => date('U'),
                            'subject' => sanitize_text_field($messages[$key]['subject']),
                            'content' => htmlspecialchars($messages[$key]['content'], ENT_QUOTES|ENT_HTML5),
                            'subjectForAdmin' => sanitize_text_field($messages[$key]['subjectForAdmin']),
                            'contentForAdmin' => htmlspecialchars($messages[$key]['content'], ENT_QUOTES|ENT_HTML5),
                            'format' => 'text',
                        ), 
                        array('%d', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s')
                    );
                    
                } else {
                    
                    #var_dump($row);
                    $email_message[$key]['enable'] = intval($row['enable']);
                    $email_message[$key]['enableSMS'] = intval($row['enableSMS']);
                    $email_message[$key]['format'] = $row['format'];
                    $email_message[$key]['subjectForAdmin'] = $row['subjectForAdmin'];
                    $email_message[$key]['contentForAdmin'] = '';
                    if (!is_null($row['contentForAdmin'])) {
                        
                        $email_message[$key]['contentForAdmin'] = htmlspecialchars_decode($row['contentForAdmin'], ENT_QUOTES|ENT_HTML5);
                        
                    }
                    
                    if (!is_null($row['subject'])) {
                        
                        $email_message[$key]['subject'] = $row['subject'];
                        
                    }
                    
                    if (!is_null($row['content'])) {
                        
                        $email_message[$key]['content'] = htmlspecialchars_decode($row['content'], ENT_QUOTES|ENT_HTML5);
                        
                    }
                    
                    $email_message[$key]['attachICalendar'] = intval($row['attachICalendar']);
                    $email_message[$key]['notifyAdministrator'] = intval($row['notifyAdministrator']);
                    $email_message[$key]['subjectForIcalendar'] = $row['subjectForIcalendar'];
                    $email_message[$key]['locationForIcalendar'] = $row['locationForIcalendar'];
                    $email_message[$key]['contentForIcalendar'] = '';
                    if (!is_null($row['contentForIcalendar'])) {
                        
                        $email_message[$key]['contentForIcalendar'] = htmlspecialchars_decode($row['contentForIcalendar'], ENT_QUOTES|ENT_HTML5);
                        
                    }
                    
                }
                
                #break;
                
            }
            
            #var_dump($email_message);
            $response = array('emailMessageList' => $email_message);
            $response['formData'] = $this->getForm($accountKey, false);
            return $response;
            
        }
        
        public function getEmailMessage($keys = null){
            
            $list = array();
            foreach ((array) $this->email_message as $key => $value) {
                
                $value['key'] = $this->prefix.$key;
                
                if($keys == null || in_array("subject", $keys) === true){
                    
                    $optionsValue = get_option($this->prefix.$key."_subject", "");
                    if($optionsValue !== false){
                        
                        $value['subject'] = $optionsValue;
                        
                    }
                    
                }
                
                if($keys == null || in_array("content", $keys) === true){
                    
                    $optionsValue = get_option($this->prefix.$key."_content", "<div>No message</div>");
                    if($optionsValue !== false){
                        
                        $value['content'] = $optionsValue;
                        
                    }
                    
                }
                
                if($keys == null || in_array("enable", $keys) === true){
                    
                    $optionsValue = get_option($this->prefix.$key."_enable", 1);
                    if($optionsValue !== false){
                        
                        $value['enable'] = $optionsValue;
                        
                    }
                    
                }
                
                if($keys == null || in_array("format", $keys) === true){
                    
                    $optionsValue = get_option($this->prefix.$key."_format", "html");
                    if($optionsValue !== false){
                        
                        $value['format'] = $optionsValue;
                        
                    }
                    
                }
                
                $list[$key] = $value;
            }
            
            return $list;
            
        }
        
        public function words() {
            
            $words = array(
                __('Arrival/Departure display format', 'booking-package'), 
                __('Total stay display format', 'booking-package'), 
                __('Include children in maximum guest count', 'booking-package'), 
                __('Maximum guests per room', 'booking-package'), 
                __('Number of available rooms', 'booking-package'), 
                __('Maximum stay (nights)', 'booking-package'), 
                __('Minimum stay (nights)', 'booking-package'), 
                __('Day before public holiday', 'booking-package'), 
                __('Rates', 'booking-package'), 
                __('Use <a href="https://material.io/tools/icons/?style=baseline" target="_blank">Material icons</a> web font.', 'booking-package'), 
                __('When no slots remain (0%)', 'booking-package'), 
                __('When slots are below threshold', 'booking-package'), 
                __('When slots exceed threshold', 'booking-package'), 
                __('Remaining slots threshold', 'booking-package'), 
                __('Show text/symbols', 'booking-package'), 
                __('Show numbers', 'booking-package'), 
                __('Display remaining slots on the calendar', 'booking-package'), 
                __('Allow selecting multiple services', 'booking-package'), 
                __('Booking steps on the front-end', 'booking-package'), 
                __('After the booking time slot', 'booking-package'), 
                __('Before the booking time slot', 'booking-package'), 
                __('Before and after the booking time slot', 'booking-package'), 
                __('Local payment', 'booking-package'), 
                __('Week starts on', 'booking-package'), 
                __('Share available time slots from other calendars', 'booking-package'), 
                __('Number of available days from today', 'booking-package'), 
                __('Number of unavailable days from today', 'booking-package'), 
                __('Notification email address', 'booking-package'), 
                __('Insert booking confirmation page before completion page', 'booking-package'), 
                __('Reminders are sent only for \'Approved\' bookings.', 'booking-package'), 
                __('Show canceled booking details on "Booked Customers"', 'booking-package'), 
                __('Allow customer cancellations', 'booking-package'), 
                __('Allow cancellation up to', 'booking-package'), 
                __('Applicable booking status', 'booking-package'), 
                __('Yes - Front-end and Dashboard', 'booking-package'), 
                __('Yes - Front-end only', 'booking-package'), 
                __('Use as Name field', 'booking-package'), 
                __('Use as Email field', 'booking-package'), 
                __('Use as SMS field', 'booking-package'), 
                __('Use as Location field', 'booking-package'), 
                __('Use as Agreement field (Terms/Privacy)', 'booking-package'), 
                
            );
            
            return $words;
            
        }
        
        public function getElementForCalendarAccount(){
            
            $preparationTime = array();
            for ($i = 0; $i <= 180; $i += 5) {
                
                array_push($preparationTime, array('key' => $i, 'name' => sprintf(__("%s minutes", 'booking-package'), $i)));
                
            }
            
            $calendarAccount = array(
                'name' => array('key' => 'name', 'name' => 'Name', 'target' => 'both', 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0),
                'type' => array(
                    'key' => 'type', 
                    'name' => 'Select a type', 
                    'target' => 'hidden',
                    'value' => 'day', 
                    'inputLimit' => 1, 
                    'inputType' => 'RADIO', 
                    'isExtensionsValid' => 0, 
                    'option' => 1, 
                    'optionsList' => array(
                        /**
                        'cost' => 0, 
                        **/
                        'hotelCharges' => 0,
                        'maximumNights' => 0,
                        'minimumNights' => 0,
                        'subscriptionIdForStripe' => 1,
                        'termsOfServiceForSubscription' => 1,
                        /**'enableSubscriptionForStripe' => 1,**/
                        'numberOfRoomsAvailable' => 0, 
                        'numberOfPeopleInRoom' => 0, 
                        'includeChildrenInRoom' => 0, 
                        'expressionsCheck' => 0, 
                        'preparationTime' => 1,
                        'flowOfBooking' => 1,
                        'courseBool' => 1,
                        'guestsBool' => 1,
                        'hasMultipleServices' => 1,
                        'courseTitle' => 1,
                        'displayRemainingCapacity' => 1,
                        'servicesPage' => 1,
                        'schedulesPage' => 1,
                        'minimum_guests' => 1,
                        'maximum_guests' => 1,
                    ), 'valueList' => array(
                        'day' => 'Booking is completed within 24 hours (hair salon, hospital etc.)', 
                        'hotel' => 'Accommodation (hotels, campgrounds, etc.)'
                    )
                ),
                'account_email_address' => array(
                    'key' => 'email_address', 
                    'name' => __('Email address', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'email_to',
                            'name' => __('Receiving email address', 'booking-package') . ': ',
                            'value' => '',
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                        1 => array(
                            'key' => 'email_from_title',
                            'name' => __('Sender name', 'booking-package') . ': ',
                            'value' => '',
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                        2 => array(
                            'key' => 'email_from',
                            'name' => __('Sending email address', 'booking-package') . ': ',
                            'value' => '',
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ),
                /**
                'email_to' => array('key' => 'email_to', 'name' => __('Receiving email address', 'booking-package'), 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0),
                'email_from' => array('key' => 'email_from', 'name' => __('Sending email address', 'booking-package'), 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0),
                'email_from_title' => array('key' => 'email_from_title', 'name' => __('Sender name', 'booking-package'), 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0),
                **/
                'status' => array('key' => 'status', 'name' => __('Calendar status', 'booking-package'), 'target' => 'both', 'value' => 'open', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array('open' => __('Enabled', 'booking-package'), 'closed' => __('Disabled', 'booking-package'))),
                'maxAccountScheduleDay' => array('key' => 'maxAccountScheduleDay', 'name' => 'Public days from today', 'target' => 'both', 'disabled' => 0, 'value' => '0', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0),
                'unavailableDaysFromToday' => array('key' => 'unavailableDaysFromToday', 'name' => 'Unavailable days from today', 'target' => 'both', 'disabled' => 0, 'value' => '0', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30')),
                'maxBookingSlotsPerDay' => array(
                    'key' => 'maxBookingSlotsPerDay', 
                    'name' => __('Max booking slots per weekday', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0,
                    'value' => 'false', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0, 
                    'valueList' => array(
                        0 => array(
                            'key' => 'maxBookingSlotsPerDayStatus',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'extensionsValidMessage' => 1,
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'maxBookingSlotsOnMonday',
                            'name' => __('Monday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        2 => array(
                            'key' => 'maxBookingSlotsOnTuesday',
                            'name' => __('Tuesday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        3 => array(
                            'key' => 'maxBookingSlotsOnWednesday',
                            'name' => __('Wednesday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        4 => array(
                            'key' => 'maxBookingSlotsOnThursday',
                            'name' => __('Thursday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        5 => array(
                            'key' => 'maxBookingSlotsOnFriday',
                            'name' => __('Friday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        6 => array(
                            'key' => 'maxBookingSlotsOnSaturday',
                            'name' => __('Saturday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        7 => array(
                            'key' => 'maxBookingSlotsOnSunday',
                            'name' => __('Sunday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                        ), 
                        8 => array(
                            'key' => 'maxBookingSlotsOnNationalHoliday',
                            'name' => __('Public holiday', 'booking-package'),
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'class' => 'multiple_fields_margin_top',
                            'action' => null,
                            'isExtensionsValid' => 0, 
                            'message' => __('To disable the maximum booking slots for a specific weekday, please enter "0".', 'booking-package'),
                        ), 
                    ),
                    "message" => '',
                ),
                'calendar_sharing' => array(
                    'key' => 'calendar_sharing', 
                    'name' => __('Share the available slots of other calendar accounts', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'schedulesSharing',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'targetSchedules',
                            'name' => __('Select the calendar to share', 'booking-package') . ': ',
                            'value' => '0',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ), 
                
                'timezone' => array('key' => 'timezone', 'name' => __('Timezone', 'booking-package'), 'target' => 'both', 'value' => 'open', 'inputLimit' => 1, 'inputType' => 'SELECT_TIMEZONE', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array()),
                'startOfWeek' => array('key' => 'startOfWeek', 'name' => __('Week Starts On', 'booking-package'), 'target' => 'both', 'disabled' => 0, 'value' => '0', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array('0' => __('Sunday', 'booking-package'), '1' => __('Monday', 'booking-package'), '2' => __('Tuesday', 'booking-package'), '3' => __('Wednesday', 'booking-package'), '4' => __('Thursday', 'booking-package'), '5' => __('Friday', 'booking-package'), '6' => __('Saturday', 'booking-package'))),
                'sendBookingVerificationCode' => array(
                    'key' => 'sendBookingVerificationCode', 
                    'name' => __('Send a booking verification code', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'bookingVerificationCode',
                            'name' => __('For customers', 'booking-package') . ': ',
                            'value' => '30',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(
                                0 => array('key' => 'emailAndSms', 'name' => __('Enabled', 'booking-package') . ' - ' . __('Email and SMS', 'booking-package')), 
                                1 => array('key' => 'email', 'name' => __('Enabled', 'booking-package') . ' - ' . __('Email', 'booking-package')), 
                                2 => array('key' => 'sms', 'name' => __('Enabled', 'booking-package') . ' - ' . __('SMS', 'booking-package')), 
                                3 => array('key' => 'false', 'name' => __('Disabled', 'booking-package')), 
                            ),
                        ),
                        1 => array(
                            'key' => 'bookingVerificationCodeToUser',
                            'name' => __('For users', 'booking-package') . ': ',
                            'value' => '30',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(
                                0 => array('key' => 'emailAndSms', 'name' => __('Enabled', 'booking-package') . ' - ' . __('Email and SMS', 'booking-package')), 
                                1 => array('key' => 'email', 'name' => __('Enabled', 'booking-package') . ' - ' . __('Email', 'booking-package')), 
                                2 => array('key' => 'sms', 'name' => __('Enabled', 'booking-package') . ' - ' . __('SMS', 'booking-package')), 
                                3 => array('key' => 'false', 'name' => __('Disabled', 'booking-package')), 
                            ),
                        ),
                    ),
                ), 
                
                'messagingService' => array('key' => 'messagingService', 'name' => __('Messaging Services', 'booking-package'), 'target' => 'both', 'value' => 'open', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(0 => __('Disabled', 'booking-package'), /**'facebookMessenger' => 'Facebook Messenger',**/ 'whatsApp' => 'WhatsApp', /**'line' => 'LINE',**/ 'twilio' => 'twilio SMS')),
                
                'paymentMethod' => array('key' => 'paymentMethod', 'name' => __('Payment methods', 'booking-package'), 'target' => 'both', 'value' => 'open', 'inputLimit' => 1, 'inputType' => 'CHECK', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array('locally' => __('Pay locally', 'booking-package'), 'stripe' => __('Pay with Stripe', 'booking-package'), 'paypal' => __('Pay with PayPal', 'booking-package'), 'stripe_konbini' => __('Pay at a convenience store with Stripe', 'booking-package'))),
                'subscriptionIdForStripe' => array('key' => 'subscriptionIdForStripe', 'name' => 'Product ID of subscription for Stripe', 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'SUBSCRIPTION', 'optionKeys' => array('subscriptionIdForStripe' => array('title' => 'Product ID', 'inputType' => 'TEXT'), 'enableSubscriptionForStripe' => array('title' => __('Enabled', 'booking-package'), 'inputType' => 'CHECKBOX')), 'isExtensionsValid' => 1, 'option' => 0, 'optionValues' => array("enableSubscriptionForStripe" => "")),
                'termsOfServiceForSubscription' => array('key' => 'termsOfServiceForSubscription', 'name' => 'The terms of service for subscription', 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'SUBSCRIPTION', 'optionKeys' => array('termsOfServiceForSubscription' => array('title' => 'URI', 'inputType' => 'TEXT'), 'enableTermsOfServiceForSubscription' => array('title' => __('Enabled', 'booking-package'), 'inputType' => 'CHECKBOX')), 'isExtensionsValid' => 1, 'option' => 0, 'optionValues' => array("enableTermsOfServiceForSubscription" => "")),
                'privacyPolicyForSubscription' => array('key' => 'privacyPolicyForSubscription', 'name' => 'The privacy policy for subscription', 'target' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'SUBSCRIPTION', 'optionKeys' => array('privacyPolicyForSubscription' => array('title' => 'URI', 'inputType' => 'TEXT'), 'enablePrivacyPolicyForSubscription' => array('title' => __('Enabled', 'booking-package'), 'inputType' => 'CHECKBOX')), 'isExtensionsValid' => 1, 'option' => 0, 'optionValues' => array("enablePrivacyPolicyForSubscription" => "")),
                #'subscriptionIdForPayPal' => array('key' => 'subscriptionIdForPayPal', 'name' => 'Subscription ID for PayPal', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'option' => 0),
                
                'hotelCharges' => array(
                    'key' => 'hotelCharges', 
                    'name' => __('Charges', 'booking-package'), 
                    'target' => 'hotel', 
                    'disabled' => 0,
                    'value' => 'false', 
                    'inputLimit' => 1, 
                    'inputType' => 'HOTEL_CHARGES', 
                    'isExtensionsValid' => 0, 
                    'option' => 0, 
                    'valueList' => array(
                        0 => array(
                            'key' => 'hotelChargeOnMonday',
                            'name' => __('Monday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        1 => array(
                            'key' => 'hotelChargeOnTuesday',
                            'name' => __('Tuesday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        2 => array(
                            'key' => 'hotelChargeOnWednesday',
                            'name' => __('Wednesday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        3 => array(
                            'key' => 'hotelChargeOnThursday',
                            'name' => __('Thursday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        4 => array(
                            'key' => 'hotelChargeOnFriday',
                            'name' => __('Friday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        5 => array(
                            'key' => 'hotelChargeOnSaturday',
                            'name' => __('Saturday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        6 => array(
                            'key' => 'hotelChargeOnSunday',
                            'name' => __('Sunday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 0, 
                        ), 
                        7 => array(
                            'key' => 'hotelChargeOnDayBeforeNationalHoliday',
                            'name' => __('The day of the week before a public holiday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 1, 
                        ), 
                        8 => array(
                            'key' => 'hotelChargeOnNationalHoliday',
                            'name' => __('Public holiday', 'booking-package'),
                            'value' => 0,
                            'isExtensionsValid' => 1, 
                        ), 
                    ),
                    "message" => '',
                ),
                
                'minimumNights' => array('key' => 'minimumNights', 'name' => __('Minimum nights', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => '1', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'option' => 0), 
                'maximumNights' => array('key' => 'maximumNights', 'name' => __('Maximum nights', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => '1', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'option' => 0), 
                'numberOfRoomsAvailable' => array('key' => 'numberOfRoomsAvailable', 'name' => __('Available room slots', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => '1', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0), 
                'numberOfPeopleInRoom' => array('key' => 'numberOfPeopleInRoom', 'name' => __('Maximum number of guests per room', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => '2', 'inputLimit' => 2, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'option' => 0), 
                'includeChildrenInRoom' => array('key' => 'includeChildrenInRoom', 'name' => __('Include children in the maximum guests of the room', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(1 => 'Include', 0 => 'Exclude')),
                
                'formatNightDay' => array('key' => 'formatNightDay', 'name' => __('Display format of the "Total length of stay"', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(0 => 2 . ' ' . __('nights', 'booking-package'), 1 => sprintf(__('%s nights %s days', 'booking-package'), '2', '3'))),
                
                'expressionsCheck' => array('key' => 'expressionsCheck', 'name' => __('Display format of the "Arrival and Departure"', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(0 => __('Arrival (Check-in) & Departure (Check-out)', 'booking-package'), 1 => __('Arrival & Departure', 'booking-package'), 2 => __('Check-in & Check-out', 'booking-package'))),
                'multipleRooms' => array('key' => 'multipleRooms', 'name' => __('Allow booking of multiple rooms', 'booking-package'), 'target' => 'hotel', 'disabled' => 0, 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(1 => 'Enabled', 0 => 'Disabled')),
                
                'preparationTimeSetting' => array(
                    'key' => 'preparationTimeSetting', 
                    'name' => __('Preparation time', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'preparationTime',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => $preparationTime,
                        ),
                        1 => array(
                            'key' => 'positionPreparationTime',
                            'name' => __('Position', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 1, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array('before_after' => __('Before and after the booked time slot', 'booking-package'), 'before' => __('Before the booked time slot', 'booking-package'), 'after' => __('After the booked time slot', 'booking-package')),
                        ),
                    ),
                ), 
                
                'servicesFunction' => array(
                    'key' => 'servicesFunction', 
                    'name' => __('Service functions', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'courseBool',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'flowOfBooking',
                            'name' => __('The steps to book on the front-end page', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 0, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array('calendar' => __('Start with date selection', 'booking-package'), 'services' => __('Start with service selection', 'booking-package')),
                        ),
                        2 => array(
                            'key' => 'hasMultipleServices',
                            'name' => __('Selecting multiple services', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 1, 
                            'extensionsValidMessage' => 1,
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')),
                        ),
                    ),
                ), 
                
                'minimum_guests' => array(
                    'key' => 'minimum_guests', 
                    'name' => __('Minimum the number of guests per one booking', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'minimumGuests',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'minimumGuestsRequiredNo',
                            'name' => __('Includes "No" selected on "Required" in the Guests', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(1 => __('Included', 'booking-package')),
                        ),
                        2 => array(
                            'key' => 'minimumGuestsOfValue',
                            'name' => __('Number of guests', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ), 
                
                'maximum_guests' => array(
                    'key' => 'maximum_guests', 
                    'name' => __('Maximum the number of guests per one booking', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'maximumGuests',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'maximumGuestsRequiredNo',
                            'name' => __('Includes "No" selected on "Required" in the Guests', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(1 => __('Included', 'booking-package')),
                        ),
                        2 => array(
                            'key' => 'maximumGuestsOfValue',
                            'name' => __('Number of guests', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'class' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ), 
                
                
                'displayRemainingSlots' => array(
                    'key' => 'displayRemainingSlots', 
                    'name' => __('Display the remaining slots as numbers or symbols on each day of the calendar', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'message' => __('You can use the web font of <a href="https://material.io/tools/icons/?style=baseline" target="_blank">Material icons</a>.', 'booking-package'),
                    'valueList' => array(
                        0 => array(
                            'key' => 'displayRemainingSlotsInCalendar',
                            'name' => __('Display Method', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(0 => __('Disabled', 'booking-package'), 'int' => __('Numbers', 'booking-package'), 'text' => __('Text or Symbols', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'displayThresholdOfRemainingCapacity',
                            'name' => __('The threshold value of remaining slots', 'booking-package') . ': ',
                            'value' => '50',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                                70 => array('key' => 70, 'name' => '70%'), 
                                60 => array('key' => 60, 'name' => '60%'), 
                                50 => array('key' => 50, 'name' => '50%'), 
                                40 => array('key' => 40, 'name' => '40%'), 
                                30 => array('key' => 30, 'name' => '30%'), 
                                20 => array('key' => 20, 'name' => '20%'), 
                                10 => array('key' => 10, 'name' => '10%'), 
                            ),
                        ),
                        2 => array(
                            'key' => 'displayRemainingCapacityHasMoreThenThreshold',
                            'name' => __('When the threshold value is exceeded', 'booking-package') . ': ',
                            'value' => array(),
                            'inputType' => 'REMAINING_CAPACITY',
                            'className' => 'multiple_fields_margin_top',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                        3 => array(
                            'key' => 'displayRemainingCapacityHasLessThenThreshold',
                            'name' => __('When the threshold value is not exceeded', 'booking-package') . ': ',
                            'value' => array(),
                            'inputType' => 'REMAINING_CAPACITY',
                            'className' => 'multiple_fields_margin_top',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                        4 => array(
                            'key' => 'displayRemainingCapacityHas0',
                            'name' => __('When the remaining slots reach 0%', 'booking-package') . ': ',
                            'value' => array(),
                            'inputType' => 'REMAINING_CAPACITY',
                            'className' => 'multiple_fields_margin_top',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ), 
                
                'displayRemainingCapacity' => array('key' => 'displayRemainingCapacity', 'name' => __('Display the remaining slots as numbers for each booking time slot', 'booking-package'), 'target' => 'day', 'value' => 0, 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'option' => 0, 'valueList' => array(1 => 'Enabled', 0 => 'Disabled')),
                'fixCalendar' => array('key' => 'fixCalendar', 'name' => __('Fixed calendar', 'booking-package'), 'target' => 'both', 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'FIX_CALENDAR', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(0 => 'month', 1 => 'year')),
                'insertConfirmedPage' => array('key' => 'insertConfirmedPage', 'name' => __('Insert a booking confirmed page between the input page and the completed page', 'booking-package'), 'target' => 'both', 'value' => '0', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'option' => 0, 'valueList' => array(1 => 'Enabled', 0 => 'Disabled')),
                'blockSameTimeBookingByUser' => array('key' => 'blockSameTimeBookingByUser', 'name' => __('Block multiple booking in the same time slot', 'booking-package'), 'target' => 'day', 'value' => '0', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'option' => 0, 'valueList' => array(1 => 'Enabled', 0 => 'Disabled')),
                
                'bookingReminder' => array('key' => 'bookingReminder', 'name' => __('Reminder notification time', 'booking-package'), 'target' => 'both', 'disabled' => 0, 'value' => '0', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 1, 'option' => 0, 'message' => sprintf(__('Reminder notifications are only sent if the status of the booked customer is "%s".', 'booking-package'), __('Approved', 'booking-package')), 
                    'valueList' => array(
                        '60' => sprintf(__('About %d hour before', 'booking-package'), 1), 
                        '120' => sprintf(__('About %d hours before', 'booking-package'), 2), 
                        '180' => sprintf(__('About %d hours before', 'booking-package'), 3), 
                        '240' => sprintf(__('About %d hours before', 'booking-package'), 4), 
                        '300' => sprintf(__('About %d hours before', 'booking-package'), 5), 
                        '360' => sprintf(__('About %d hours before', 'booking-package'), 6), 
                        '420' => sprintf(__('About %d hours before', 'booking-package'), 7), 
                        '480' => sprintf(__('About %d hours before', 'booking-package'), 8), 
                        '540' => sprintf(__('About %d hours before', 'booking-package'), 9), 
                        '600' => sprintf(__('About %d hours before', 'booking-package'), 10), 
                        '660' => sprintf(__('About %d hours before', 'booking-package'), 11), 
                        '720' => sprintf(__('About %d hours before', 'booking-package'), 12), 
                        '1440' => sprintf(__('About %d hours before', 'booking-package'), 24), 
                        '2160' => sprintf(__('About %d hours before', 'booking-package'), 36), 
                        '2880' => sprintf(__('About %d hours before', 'booking-package'), 48), 
                        '3600' => sprintf(__('About %d hours before', 'booking-package'), 60), 
                        '4320' => sprintf(__('About %d hours before', 'booking-package'), 72), 
                    )
                ),
                'displayDetailsOfCanceled' => array('key' => 'displayDetailsOfCanceled', 'name' => __('Display details of canceled customers on the "Booked Customers"', 'booking-package'), 'target' => 'both', 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'option' => 0, 'valueList' => array(1 => 'Enabled', 0 => 'Disabled')),
                'cancellation_of_booking' => array(
                    'key' => 'cancellation_of_booking', 
                    'name' => __('Cancel a booking by your customer', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'cancellationOfBooking',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package'), 0 => __('Disabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'allowCancellationVisitor',
                            'name' => __('Booking start time', 'booking-package') . ': ',
                            'value' => '30',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            /** 'insertAfterElement' => __('%s the booking start time', 'booking-package'), **/
                            'actions' => null,
                            'valueList' => array(
                                30 => array('key' => 30, 'name' => sprintf(__('%s minutes before', 'booking-package'), "30")), 
                                60 => array('key' => 60, 'name' => sprintf(__('%s hour before', 'booking-package'), "1")), 
                                120 => array('key' => 120, 'name' => sprintf(__('%s hours before', 'booking-package'), "2")), 
                                240 => array('key' => 240, 'name' => sprintf(__('%s hours before', 'booking-package'), "4")), 
                                480 => array('key' => 480, 'name' => sprintf(__('%s hours before', 'booking-package'), "8")), 
                                720 => array('key' => 720, 'name' => sprintf(__('%s hours before', 'booking-package'), "12")), 
                                1440 => array('key' => 1440, 'name' => sprintf(__('%s day before', 'booking-package'), "1")), 
                                2880 => array('key' => 2880, 'name' => sprintf(__('%s days before', 'booking-package'), "2")), 
                                4320 => array('key' => 4320, 'name' => sprintf(__('%s days before', 'booking-package'), "3")), 
                                5760 => array('key' => 5760, 'name' => sprintf(__('%s days before', 'booking-package'), "4")), 
                                7200 => array('key' => 7200, 'name' => sprintf(__('%s days before', 'booking-package'), "5")), 
                                8640 => array('key' => 8640, 'name' => sprintf(__('%s days before', 'booking-package'), "6")), 
                                10080 => array('key' => 10080, 'name' => sprintf(__('%s days before', 'booking-package'), "7")), 
                                11520 => array('key' => 11520, 'name' => sprintf(__('%s days before', 'booking-package'), "8")), 
                                12960 => array('key' => 12960, 'name' => sprintf(__('%s days before', 'booking-package'), "9")), 
                                14400 => array('key' => 14400, 'name' => sprintf(__('%s days before', 'booking-package'), "10")), 
                                15840 => array('key' => 15840, 'name' => sprintf(__('%s days before', 'booking-package'), "11")), 
                                17280 => array('key' => 17280, 'name' => sprintf(__('%s days before', 'booking-package'), "12")), 
                                18720 => array('key' => 18720, 'name' => sprintf(__('%s days before', 'booking-package'), "13")), 
                                20160 => array('key' => 20160, 'name' => sprintf(__('%s days before', 'booking-package'), "14")), 
                            ),
                        ),
                        2 => array(
                            'key' => 'refuseCancellationOfBooking',
                            'name' => __('Status of booking', 'booking-package') . ': ',
                            'value' => '30',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(
                                0 => array('key' => 'not_refuse', 'name' => __('Pending and Approved', 'booking-package')), 
                                1 => array('key' => 'pending', 'name' => __('Pending', 'booking-package')), 
                                2 => array('key' => 'approved', 'name' => __('Approved', 'booking-package')), 
                            ),
                        ),
                    ),
                ), 
                /**
                'refuseCancellationOfBooking' => array('key' => 'refuseCancellationOfBooking', 'name' => __('Status to approve cancellation of booking', 'booking-package').":", 'target' => 'both', 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 1, 'option' => 0, 
                    'valueList' => array(
                        'not_refuse' => __('Pending and Approved', 'booking-package'), 
                        'pending' => __('Pending', 'booking-package'), 
                        'approved' => __('Approved', 'booking-package'), 
                    )
                ),
                **/
                
                'insertCustomPage' => array(
                    'key' => 'insertCustomPage', 
                    'name' => __('Insert a custom page at each step', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'message' => __('To add custom pages to the select box, you need to add a custom field named "booking-package" to the page, with a value of "front-end.".', 'booking-package'),
                    'valueList' => array(
                        0 => array(
                            'key' => 'calenarPage',
                            'name' => __('Calendar', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'target' => 'both',
                            'actions' => null,
                            'valueList' => array(),
                        ),
                        1 => array(
                            'key' => 'schedulesPage',
                            'name' => __('Schedules', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'target' => 'day',
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                            ),
                        ),
                        2 => array(
                            'key' => 'servicesPage',
                            'name' => __('Services', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'target' => 'day',
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                            ),
                        ),
                        3 => array(
                            'key' => 'visitorDetailsPage',
                            'name' => __('Booking form', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'target' => 'both',
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                            ),
                        ),
                        4 => array(
                            'key' => 'confirmDetailsPage',
                            'name' => __('Booking confirmation form', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'target' => 'both',
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                            ),
                        ),
                        5 => array(
                            'key' => 'thanksPage',
                            'name' => __('Booking completion form', 'booking-package') . ': ',
                            'value' => 0,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(
                                90 => array('key' => 90, 'name' => '90%'), 
                                80 => array('key' => 80, 'name' => '80%'), 
                            ),
                        ),
                        
                    ),
                ), 
                
                'redirect_Page' => array(
                    'key' => 'redirect_Page', 
                    'name' => __('Redirect to another page without displaying the booking completion page', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'redirectMode',
                            'name' => '',
                            'value' => 'page',
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array('page' => __('Pages', 'booking-package'), 'url' => __('URL', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'redirectPage',
                            'name' => __('Pages', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(1 => __('Enabled', 'booking-package')),
                            'message' => __('To add custom pages to the select box, you need to add a custom field named "booking-package" to the page, with a value of "front-end.".', 'booking-package'),
                        ),
                        2 => array(
                            'key' => 'redirectURL',
                            'name' => __('URL', 'booking-package') . ': ',
                            'value' => '',
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 0, 
                            'className' => 'multiple_fields_margin_top',
                            'actions' => null,
                            'valueList' => array(),
                        ),
                    ),
                ),
                
            );
            
            if ($this->maxBookingSlotsPerDay === 0) {
                
                unset($calendarAccount['maxBookingSlotsPerDay']);
                
            }
            
            return $calendarAccount;
            
        }
        
        public function getCourseData(){
            
            $month = array(
                '1' => array('key' => 1, 'name' => __('Jan', 'booking-package')), 
                '2' => array('key' => 2, 'name' => __('Feb', 'booking-package')), 
                '3' => array('key' => 3, 'name' => __('Mar', 'booking-package')), 
                '4' => array('key' => 4, 'name' => __('Apr', 'booking-package')), 
                '5' => array('key' => 5, 'name' => __('May', 'booking-package')), 
                '6' => array('key' => 6, 'name' => __('Jun', 'booking-package')), 
                '7' => array('key' => 7, 'name' => __('Jul', 'booking-package')), 
                '8' => array('key' => 8, 'name' => __('Aug', 'booking-package')), 
                '9' => array('key' => 9, 'name' => __('Sep', 'booking-package')), 
                '10' => array('key' => 10, 'name' => __('Oct', 'booking-package')), 
                '11' => array('key' => 11, 'name' => __('Nov', 'booking-package')), 
                '12' => array('key' => 12, 'name' => __('Dec', 'booking-package')), 
            );
            
            $day = array();
            for ($i = 1; $i <= 31; $i++) {
                
                $day[$i] = array('key' => $i, 'name' => $i);
                
            }
            
            $yearList = array();
            for ($i = 0; $i <= 10; $i++) {
                
                $year = date('Y') + $i;
                $yearList[$year] = array('key' => $year, 'name' => $year);
                
            }
            
            $addNewCourse =  array(
                'name' => array('name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'description' => array('name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'active' => array('name' => __('Status', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'CHECK', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array('true' => __('Enabled', 'booking-package'))),
                'target' => array('name' => __('Target', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 0, 'valueList' => array('visitors_users' => __('Customers and Users', 'booking-package'), 'visitors' => __('Customers', 'booking-package'), 'users' => __('Users', 'booking-package'))),
                'stopService' => array(
                    'key' => 'stopService',
                    'name' => __('Stop this service under the following conditions', 'booking-package'), 
                    'value' => '', 
                    'inputLimit' => 1, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1,
                    'message' => __('If you have selected a value other than the "Disabled", the "Selection of multiple services" will be disabled.', 'booking-package'),
                    'valueList' => array(
                        0 => array(
                            'key' => 'stopServiceUnderFollowingConditions',
                            'name' => /**'Under the following conditions'**/ '',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(
                                'doNotStop' => __('Disabled', 'booking-package'), 
                                #'isNotEqual' => __('The "Capacity" and "Remaining" values of the time slot are not equal.', 'booking-package'), 
                                'isNotEqual' => sprintf( __('The "%s" and "%s" values for the time slot do not match.', 'booking-package'), __('Available slots', 'booking-package'), __('Remaining slots', 'booking-package') ), 
                                #'isEqual' => __('The "Capacity" and "Remaining" values of the time slot are equal.', 'booking-package'),
                                'isEqual' => sprintf( __('The "%s" and "%s" values for the time slot are equal.', 'booking-package'), __('Available slots', 'booking-package'), __('Remaining slots', 'booking-package') ),
                                'specifiedNumberOfTimes' => __('When the specified number of times is reached.', 'booking-package'),
                            ),
                            'className' => 'stopServiceUnderFollowingConditions',
                        ),
                        1 => array(
                            'key' => 'doNotStopServiceAsException',
                            'name' => '',
                            'value' => null,
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array( 
                                'sameServiceIsNotStopped' => __('If the same service is already booked for this time slot, the booking will not be allowed.', 'booking-package'), 
                            ),
                            'className' => 'doNotStopServiceAsException',
                        ),
                        2 => array(
                            'key' => 'stopServiceForDayOfTimes',
                            'name' => __('Target', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'RADIO',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array( 
                                'day' => __('Per day slots', 'booking-package'), 
                                'timeSlot' => __('Per time slots', 'booking-package'), 
                            ),
                            'className' => 'stopServiceForDayOfTimes',
                        ),
                        3 => array(
                            'key' => 'stopServiceForSpecifiedNumberOfTimes',
                            'name' => __('Number of times', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'stopServiceForSpecifiedNumberOfTimes',
                        ),
                    ),
                ),
                'costs' => array(
                    'key' => 'costs', 
                    'name' => __('Prices', 'booking-package'), 
                    'target' => 'day', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'message' => '',
                    'valueList' => array(
                        0 => array(
                            'key' => 'cost_1',
                            'name' => __(/**'Cost 1'**/ 'Base Price', 'booking-package'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                        1 => array(
                            'key' => 'cost_2',
                            'name' => sprintf(__('Price %s', 'booking-package'), '2'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                        2 => array(
                            'key' => 'cost_3',
                            'name' => sprintf(__('Price %s', 'booking-package'), '3'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                        3 => array(
                            'key' => 'cost_4',
                            'name' => sprintf(__('Price %s', 'booking-package'), '4'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                        4 => array(
                            'key' => 'cost_5',
                            'name' => sprintf(__('Price %s', 'booking-package'), '5'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                        5 => array(
                            'key' => 'cost_6',
                            'name' => sprintf(__('Price %s', 'booking-package'), '6'),
                            'value' => null,
                            'inputType' => 'TEXT',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array(),
                            'className' => 'costs',
                        ),
                    ),
                ), 
                'expirationDate' => array(
                    'key' => 'expirationDate', 
                    'name' => __('Expiration date', 'booking-package'), 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 0, 
                    'option' => 0,
                    'valueList' => array(
                        0 => array(
                            'key' => 'expirationDateStatus',
                            'name' => '',
                            'value' => '0',
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 0, 
                            'actions' => null,
                            'valueList' => array('1' => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'expirationDateFromMonth',
                            'name' => __('From:', 'booking-package') . ' ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        2 => array(
                            'key' => 'expirationDateFromDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        3 => array(
                            'key' => 'expirationDateFromYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                        4 => array(
                            'key' => 'expirationDateToMonth',
                            'name' => __('To:', 'booking-package') . ' ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        5 => array(
                            'key' => 'expirationDateToDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        6 => array(
                            'key' => 'expirationDateToYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 0, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                    ),
                ),
                'time' => array('name' => __('Duration time', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array()),
                'timeToProvide' => array('name' => __('Specify the time slots for each day of the week', 'booking-package'), 'value' => '0', 'inputLimit' => 2, 'inputType' => 'TIME_TO_PROVIDE', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array()),
                'selectOptions' => array('name' => __('Selection of multiple options', 'booking-package'), 'value' => '0', 'inputLimit' => 2, 'inputType' => 'CHECK', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('1' => __('Enabled', 'booking-package'))),
                'options' => array(
                    'name' => __('Options', 'booking-package'), 
                    'value' => '', 
                    'inputLimit' => 2, 
                    'inputType' => 'EXTRA', 
                    'isExtensionsValid' => 1, 
                    'isExtensionsValidPanel' => 0, 
                    'format' => 'json', 
                    'valueList' => '', 
                    "optionsType" => array(
                        "name" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        /** "cost" => array("type" => "TEXT", "value" => "", "target" => "both"), **/
                        "cost_1" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "cost_2" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "cost_3" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "cost_4" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "cost_5" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "cost_6" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "time" => array("type" => "SELECT", "value" => 0, "target" => "both", "start" => 0, "end" => 725, "addition" => 5, 'unit' => __("%s min", 'booking-package'))
                    ), 
                    'titleList' => array('name' => __('Name', 'booking-package'), 
                    /** 'cost' => __('Price', 'booking-package'), **/
                    'cost_1' => __(/**'Cost 1'**/ 'Base Price', 'booking-package'), 
                    'cost_2' => sprintf(__('Price %s', 'booking-package'), '2'), 
                    'cost_3' => sprintf(__('Price %s', 'booking-package'), '3'), 
                    'cost_4' => sprintf(__('Price %s', 'booking-package'), '4'), 
                    'cost_5' => sprintf(__('Price %s', 'booking-package'), '5'), 
                    'cost_6' => sprintf(__('Price %s', 'booking-package'), '6'), 
                    
                    'time' => __('Extra time', 'booking-package'))
                ),
            );
            return $addNewCourse;
            
        }
        
        public function getSubscriptionsData(){
            
            $addSubscriptions = array(
                'subscription' => array('name' => 'Subscription', 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'name' => array('name' => 'Name', 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'active' => array('name' => 'Status', 'value' => '', 'inputLimit' => 2, 'inputType' => 'CHECK', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array('true' => __('Enabled', 'booking-package'))),
                'renewal' => array('name' => 'Automatic subscription renewal', 'value' => '', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'valueList' => array('0' => __('Invalid', 'booking-package'), '1' => __('Valid', 'booking-package')), "message" => ''),
                'limit' => array('name' => 'Booking limit', 'value' => '', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'valueList' => array('0' => __('Invalid', 'booking-package'), '1' => __('Valid', 'booking-package')), "message" => ''),
                'numberOfTimes' => array('name' => 'Number of times users can book by the following deadline', 'value' => '', 'inputLimit' => 1, 'inputType' => 'SELECT', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array(1 => '1', 2 => '2')),
            );
            
            return $addSubscriptions;
            
        }
        
        public function getTaxesData(){
            
            $month = array(
                '1' => array('key' => 1, 'name' => __('Jan', 'booking-package')), 
                '2' => array('key' => 2, 'name' => __('Feb', 'booking-package')), 
                '3' => array('key' => 3, 'name' => __('Mar', 'booking-package')), 
                '4' => array('key' => 4, 'name' => __('Apr', 'booking-package')), 
                '5' => array('key' => 5, 'name' => __('May', 'booking-package')), 
                '6' => array('key' => 6, 'name' => __('Jun', 'booking-package')), 
                '7' => array('key' => 7, 'name' => __('Jul', 'booking-package')), 
                '8' => array('key' => 8, 'name' => __('Aug', 'booking-package')), 
                '9' => array('key' => 9, 'name' => __('Sep', 'booking-package')), 
                '10' => array('key' => 10, 'name' => __('Oct', 'booking-package')), 
                '11' => array('key' => 11, 'name' => __('Nov', 'booking-package')), 
                '12' => array('key' => 12, 'name' => __('Dec', 'booking-package')), 
            );
            
            $day = array();
            for ($i = 1; $i <= 31; $i++) {
                
                $day[$i] = array('key' => $i, 'name' => $i);
                
            }
            
            $yearList = array();
            for ($i = 0; $i <= 10; $i++) {
                
                $year = date('Y') + $i;
                $yearList[$year] = array('key' => $year, 'name' => $year);
                
            }
            
            $addSubscriptions = array(
                'name' => array('name' => __('Name', 'booking-package'), 'type' => 'both', 'gen' => 'both', 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => ''),
                'active' => array('name' => __('Status', 'booking-package'), 'type' => 'both', 'gen' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'CHECK', 'isExtensionsValid' => 0, 'isExtensionsValidPanel' => 1, 'valueList' => array('true' => __('Enabled', 'booking-package')), 'actions' => null),
                'type' => array('name' => __('Type', 'booking-package'), 'type' => 'both', 'gen' => '1', 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('tax' => __('Tax', 'booking-package'), 'surcharge' => __('Extra charge', 'booking-package')), 'option' => 1, 'optionsList' => array('tax' => 1, 'method' => 1), 'actions' => null),
                'tax' => array('name' => __('Tax', 'booking-package'), 'type' => 'tax', 'gen' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('tax_exclusive' => __('Tax-exclusive pricing', 'booking-package'), 'tax_inclusive' => __('Tax-inclusive pricing', 'booking-package')), 'actions' => null, 'valueClasses' => array('tax_exclusive' => 'tax_exclusive', 'tax_inclusive' => 'tax_inclusive'), 'disabled' => 1),
                'method' => array('name' => __('Calculation method', 'booking-package'), 'type' => 'tax', 'gen' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('addition' => __('Addition', 'booking-package'), 'multiplication' => __('Multiplication', 'booking-package')), 'actions' => null, 'valueClasses' => array('addition' => 'calculationMethod', 'multiplication' => 'calculationMethod')),
                'target' => array('name' => __('Target', 'booking-package'), 'type' => 'both', 'gen' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('room' => __('Per room', 'booking-package'), 'guest' => __('Per guest', 'booking-package')), 'actions' => null, 'valueClasses' => array('room' => 'target_room', 'guest' => 'target_guest')),
                'scope' => array('name' => __('Range of tax or surcharge', 'booking-package'), 'type' => 'both', 'gen' => 'both', 'value' => '', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => array('day' => __('Per day', 'booking-package'), 'booking' => __('Per one booking', 'booking-package'), 'bookingEachGuests' => __('Per one booking for all guests', 'booking-package')), 'actions' => null),
                'value' => array('name' => __('Value', 'booking-package'), 'type' => 'both', 'gen' => 'both', 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 1, 'valueList' => '', 'actions' => null),
                'expirationDate' => array(
                    'key' => 'expirationDate', 
                    'name' => __('Expiration date', 'booking-package'), 
                    'type' => 'both', 
                    'gen' => 'both', 
                    'target' => 'both', 
                    'disabled' => 0, 
                    'value' => '1', 
                    'inputLimit' => 2, 
                    'inputType' => 'MULTIPLE_FIELDS', 
                    'isExtensionsValid' => 1, 
                    'option' => 0,
                    'actions' => null,
                    'valueList' => array(
                        0 => array(
                            'key' => 'expirationDateStatus',
                            'name' => '',
                            'value' => '0',
                            'inputType' => 'CHECK',
                            'isExtensionsValid' => 1, 
                            'actions' => null,
                            'valueList' => array('1' => __('Enabled', 'booking-package')),
                        ),
                        1 => array(
                            'key' => 'expirationDateFromMonth',
                            'name' => __('From', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        2 => array(
                            'key' => 'expirationDateFromDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        3 => array(
                            'key' => 'expirationDateFromYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateFrom',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                        4 => array(
                            'key' => 'expirationDateToMonth',
                            'name' => __('To', 'booking-package') . ': ',
                            'value' => null,
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $month,
                        ),
                        5 => array(
                            'key' => 'expirationDateToDay',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $day,
                        ),
                        6 => array(
                            'key' => 'expirationDateToYear',
                            'name' => '',
                            'value' => '',
                            'inputType' => 'SELECT',
                            'isExtensionsValid' => 1, 
                            'className' => 'expirationDateTo',
                            'actions' => null,
                            'valueList' => $yearList,
                        ),
                    ),
                ),
            );
            
            return $addSubscriptions;
            
        }
        
        public function getOptionsForHotelData(){
            
            $options = array(
                'name' => array('name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => '', 'disabled' => 0),
                'description' => array('name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => '', 'target' => 'both'),
                'active' => array('name' => __('Status', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'CHECK', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => array('true' => __('Enabled', 'booking-package'))),
                'required' => array('key' => 'required', 'name' => 'Required', 'value' => 'true', 'inputLimit' => 1, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'range' => array('name' => __('Range of booking option', 'booking-package'), 'value' => 'allDays', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => array('allDays' => __('Per all booking days', 'booking-package'), 'oneBooking' => __('Per one room booking', 'booking-package')), 'actions' => null),
                'target' => array('name' => __('Target', 'booking-package'), 'value' => 'guests', 'inputLimit' => 2, 'inputType' => 'RADIO', 'isExtensionsValid' => 1, 'isExtensionsValidPanel' => 0, 'valueList' => array('guests' => __('Per guest', 'booking-package'), 'room' => __('Per room', 'booking-package')), 'actions' => null),
                'json' => array(
                    'name' => __('Options', 'booking-package'), 
                    'value' => '', 
                    'inputLimit' => 2, 
                    'inputType' => 'EXTRA', 
                    'isExtensionsValid' => 1, 
                    'isExtensionsValidPanel' => 0, 
                    'format' => 'json', 
                    'valueList' => '', 
                    "optionsType" => array(
                        "name" => array("type" => "TEXT", "value" => "", "target" => "both"), 
                        "adult" => array("type" => "TEXT", "value" => "", "target" => "both", "class" => array("optionWithChargeAdult")), 
                        "child" => array("type" => "TEXT", "value" => "", "target" => "both", "class" => array("optionWithChargeChild")), 
                        "room" => array("type" => "TEXT", "value" => "", "target" => "both", "class" => array("optionWithChargeRoom"))
                    ), 
                    'titleList' => array(
                        'name' => __('Option value', 'booking-package'), 
                        'adult' => __('Extra charge per adult', 'booking-package'), 
                        'child' => __('Extra charge per child', 'booking-package'), 
                        'room' => __('Extra charge per room', 'booking-package')
                    )
                ),
            );
            
            return $options;
            
        }
        
        public function getFormInputTypeForUser() {
            
            $input = array(
                'id' => array('key' => 'id', 'name' => __('Unique ID', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', "class" => ""),
                'name' => array('key' => 'name', 'name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', "class" => ""),
                'uri' => array('key' => 'uri', 'name' => 'URL', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                'placeholder' => array('key' => 'placeholder', 'name' => __('Placeholder text', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                'description' => array('key' => 'description', 'name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', "class" => ""),
                'active' => array('key' => 'active', 'name' => __('Status', 'booking-package'), 'value' => array('Enabled'), 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array(__('Enabled', 'booking-package')), "class" => ""),
                'required' => array('key' => 'required', 'name' => __('Required', 'booking-package'), 'value' => array('Enabled'), 'inputLimit' => 1, 'inputType' => 'CHECK', 'valueList' => array(__('Enabled', 'booking-package')), "class" => ""),
                'type' => array('key' => 'type', 'name' => __('Type', 'booking-package'), 'value' => 'TEXT', 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => array('TEXT' => 'TEXT', 'SELECT' => 'SELECT', 'CHECK' => 'CHECK', 'RADIO' => 'RADIO', 'TEXTAREA' => 'TEXTAREA'), "class" => ""),
                'options' => array('key' => 'options', 'name' => __('Options', 'booking-package'), 'value' => array(''), 'inputLimit' => 2, 'inputType' => 'OPTION', 'format' => 'array', "class" => "", "options" => array("name" => "text"), 'format' => 'jsonString', 'optionsType' => array(array("type" => "TEXT", "value" => "", "target" => "both"))),
            );
            
            foreach ($input as $key => $value) {
                
                $input[$key]['disabled'] = 0;
                $input[$key]['id'] = $value['key'];
                $input[$key]['type'] = $value['inputType'];
                
                if (isset($value['valueList']) === true) {
                    
                    $input[$key]['options'] = $value['valueList'];
                    
                }
                
            }
            
            return $input;
            
        }
        
        public function getFormInputType(){
            
            $formInputType = array(
                'id' => array('key' => 'id', 'name' => __('Unique ID', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', "class" => ""),
                'name' => array('key' => 'name', 'name' => __('Name', 'booking-package'), 'value' => '', 'inputLimit' => 1, 'inputType' => 'TEXT', "class" => ""),
                'value' => array('key' => 'value', 'name' => __('Value', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => "hidden_panel"),
                'groupId' => array('key' => 'groupId', 'name' => 'Group ID', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                #'groupName' => array('key' => 'groupName', 'name' => 'Group name', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                'uri' => array('key' => 'uri', 'name' => 'URL', 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                'placeholder' => array('key' => 'placeholder', 'name' => __('Placeholder text', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXT', "class" => ""),
                'description' => array('key' => 'description', 'name' => __('Description', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'TEXTAREA', "class" => ""),
                'active' => array('key' => 'active', 'name' => __('Status', 'booking-package'), 'value' => 'true', 'inputLimit' => 2, 'inputType' => 'CHECK', 'valueList' => array('true' => __('Enabled', 'booking-package')), "class" => ""),
                'required' => array('key' => 'required', 'name' => __('Required', 'booking-package'), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package') . ' - ' . __('The front-end and the dashboard', 'booking-package'), 'true_frontEnd' => __('Yes', 'booking-package') . ' - ' . __('The front-end only', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'isName' => array('key' => 'isName', 'name' => sprintf(__('Is the field for %s', 'booking-package'), __('Name', 'booking-package')), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'isEmail' => array('key' => 'isEmail', 'name' => sprintf(__('Is the field for %s', 'booking-package'), __('Email address', 'booking-package')), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'isSMS' => array('key' => 'isSMS', 'name' => sprintf(__('Is the field for %s', 'booking-package'), __('SMS (Short Message Service)', 'booking-package')), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => "", 'message' => sprintf(__('If you select "%s," you will need to change the value of "Messaging Services" in the %s tab.', 'booking-package'), __('Yes', 'booking-package'), __('Settings', 'booking-package'))),
                'isAddress' => array('key' => 'isAddress', 'name' => sprintf(__('Is the field for %s', 'booking-package'), __('Location', 'booking-package')), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'isTerms' => array('key' => 'isTerms', 'name' => sprintf(__('Is the field for %s', 'booking-package'), __('Terms of Service or Privacy Policy', 'booking-package')), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'isAutocomplete' => array('key' => 'isAutocomplete', 'name' => __('Save the values entered by the user', 'booking-package'), 'value' => 'false', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('true' => __('Yes', 'booking-package'), 'false' => __('No', 'booking-package')), "class" => ""),
                'targetCustomers' => array('key' => 'targetCustomers', 'name' => __('Target', 'booking-package'), 'value' => 'customers', 'inputLimit' => 1, 'inputType' => 'RADIO', 'valueList' => array('customersAndUsers' => __('Customers and Users', 'booking-package'), 'visitors' => __('Customers', 'booking-package'), 'users' => __('Users', 'booking-package')), "class" => ""),
                'type' => array('key' => 'type', 'name' => __('Type', 'booking-package'), 'value' => 'TEXT', 'inputLimit' => 1, 'inputType' => 'SELECT', 'valueList' => array('TEXT' => 'TEXT', 'SELECT' => 'SELECT', 'CHECK' => 'CHECK', 'RADIO' => 'RADIO', 'TEXTAREA' => 'TEXTAREA'), "class" => ""),
                'options' => array('key' => 'options', 'name' => __('Options', 'booking-package'), 'value' => '', 'inputLimit' => 2, 'inputType' => 'OPTION', 'format' => 'array', "class" => "", "options" => array("name" => "text"), 'format' => 'jsonString', 'optionsType' => array(array("type" => "TEXT", "value" => "", "target" => "both"))),
            );
            
            if ($this->messagingApp === 0) {
                
                unset($formInputType['isSMS']['message']);
                
            }
            
            return $formInputType;
            
        }
        
        public function updateMemberSetting(){
            
            global $wpdb;
            $isExtensionsValid = $this->getExtensionsValid();
            $user_toolbar = intval(get_option($this->prefix . 'user_toolbar', 0));
            if (isset($_POST['user_toolbar']) && intval($_POST['user_toolbar']) != $user_toolbar) {
                
                $bool = 'false';
                if (intval($_POST['user_toolbar']) == 1) {
                    
                    $bool = 'true';
                    
                }
                $table_name = $wpdb->prefix."booking_package_users";
                $sql = $wpdb->prepare("SELECT `key` FROM ".$table_name.";", array());
                $rows = $wpdb->get_results($sql, ARRAY_A);
                for ($i = 0; $i < count($rows); $i++) {
                    
                    $key = intval($rows[$i]['key']);
                    update_user_meta($key, 'show_admin_bar_front', $bool);
                    
                }
                
            }
            
            $userFunctions = $this->userFunctions();
            foreach ((array) $userFunctions as $key => $input) {
                
                if (isset($_POST[$key])) {
                    
                    $value = sanitize_text_field($_POST[$key]);
                    if ($isExtensionsValid !== true && $input['inputType'] == "CHECK") {
                        
                        $value = 0;
                        
                    }
                    
                    if ($input['inputType'] == "TEXTAREA") {
                        
                        $value = sanitize_textarea_field($_POST[$key]);
                        
                    }
                    
                    update_option($this->prefix.$key, $value);
                    $userFunctions[$key]["value"] = $value;
                    
                }
                
            }
            
            return $userFunctions;
            
        }
        
        public function update($post){
            
            $extentionBool = $this->getExtensionsValid();
            $list = $this->getList();
            if (isset($_POST['type']) && $_POST['type'] == "bookingSync") {
                
                $list = $this->getBookingSyncList();
                
            }
            
            foreach ((array) $list as $listKey => $listValue) {
                /**
                if ($extentionBool === false && $listKey == 'Stripe') {
                    
                    continue;
                    
                }
                **/
                
                $category = array();
                foreach ((array) $listValue as $key => $value) {
                    
                    
                    if (isset($post[$key]) === true) {
                        
                        $value = "";
                        if (isset($listValue['inputType']) && $listValue['inputType'] == "TEXTAREA") {
                            
                            $value = sanitize_textarea_field($post[$key]);
                            if ($key == 'booking_package_googleCalendar_json') {
                                
                                $value = array();
                                $json = json_decode($post[$key], true);
                                foreach ((array) $json as $jsonKey => $jsonValue) {
                                    
                                    $value[sanitize_text_field($jsonKey)] = sanitize_text_field($jsonValue);
                                    
                                }
                                
                                $value = json_encode($value);
                                
                            }
                            
                        } else if (isset($listValue['inputType']) && $listValue['inputType'] == "MULTIPLE_FIELDS") {
                            
                            var_dump($listValue);
                            
                        } else {
                            
                            $value = sanitize_text_field(trim($post[$key]));
                            
                        }
                        #$value = sanitize_text_field($post[$key]);
                        
                        if (get_option($key) === false) {
					        
	                        add_option($key, $value);
					        
                        } else {
				            
				            update_option($key, $value);
				            
			            }
                        
                    }
                    
                    if (isset($listValue[$key]['inputType']) && $listValue[$key]['inputType'] == "MULTIPLE_FIELDS") {
                        
                        $options = $listValue[$key]['valueList'];
                        for ($i = 0; $i < count($options); $i++) {
                            
                            
                            $optionKey = $options[$i]['key'];
                            if (isset($post[$optionKey]) === true) {
                                
                                if (get_option($optionKey) === false) {
        					        
        	                        add_option($optionKey, sanitize_text_field($post[$optionKey]));
        					        
                                } else {
        				            
        				            update_option($optionKey, sanitize_text_field($post[$optionKey]));
        				            
        			            }
                                
                            }
                            
                        }
                        
                    }
                    
                    $category[$key] = $value;
                    
                }
                
                $list[$listKey] = $category;
                
            }
            
            return $list;
            
        }
        
        public function refreshToken($key, $home = false){
            
            $key = sanitize_text_field($key);
            $token = hash('ripemd160', sanitize_text_field($home));
            if($home === false){
                
                #$timezone = get_option('timezone_string');
                #date_default_timezone_set($timezone);
                $token = hash('ripemd160', date('U'));
                
            }
            
            update_option($key, $token);
            return array('status' => 'success', 'token' => $token, 'key' => $key);
            
        }
        
        public function initialUserInputFields () {
            
            $requiredKeys = array('user_login', 'user_email', 'user_pass');
            $initialUserInputFields = array(
                'user_login' => array('id' => 'user_login', 'active' => 'true', 'name' => 'Username', 'value' => '', 'isName' => 'true', 'options' => array(), 'description' => '', 'placeholder' => '', 'uri' => '', 'required' => 'true', 'type' => 'TEXT'),
                'user_email' => array('id' => 'user_email', 'active' => 'true', 'name' => 'Email', 'value' => '', 'isName' => 'true', 'options' => array(), 'description' => '', 'placeholder' => '', 'uri' => '', 'required' => 'true', 'type' => 'TEXT'),
                'user_pass' => array('id' => 'user_pass', 'active' => 'true', 'name' => 'Password', 'value' => '', 'isName' => 'true', 'options' => array(), 'description' => '', 'placeholder' => '', 'uri' => '', 'required' => 'true', 'type' => 'PASSWORD'),
                'first_name' => array('id' => 'first_name', 'active' => 'false', 'name' => 'First name', 'value' => '', 'isName' => 'true', 'options' => array(), 'description' => '', 'placeholder' => '', 'uri' => '', 'required' => 'true', 'type' => 'TEXT'),
                'last_name' => array('id' => 'last_name', 'active' => 'false', 'name' => 'Last name', 'value' => '', 'isName' => 'true', 'options' => array(), 'description' => '', 'placeholder' => '', 'uri' => '', 'required' => 'true', 'type' => 'TEXT'),
            );
            $userInputFields = get_option("_" . $this->prefix . "getUserInputFields", null);
            if (empty($userInputFields) === true) {
                
                $userInputFields = $initialUserInputFields;
                add_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                    
            } else {
                
                $userInputFields = json_decode($userInputFields, true);
                $required = true;
                for ($i = 0; $i < count($requiredKeys); $i++) {
                    
                    if (array_key_exists($requiredKeys[$i], $userInputFields) === false) {
                        
                        $required = false;
                        break;
                        
                    }
                    
                }
                
                if ($required === false) {
                    
                    $userInputFields = array_merge($initialUserInputFields, $userInputFields);
                    update_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                    
                }
                
            }
            
            return array_values($userInputFields);
            
        }
        
        public function updateUserInputFields($mode) {
            
            function checkEnabled($json) {
                
                if (count($json['active']) === 0) {
                    
                    $json['active'] = 'false';
                    
                } else {
                    
                    $json['active'] = 'true';
                    
                }
                
                if (count($json['required']) === 0) {
                    
                    $json['required'] = 'false';
                    
                } else {
                    
                    $json['required'] = 'true';
                    
                }
                
                if (array_key_exists('value', $json) === false) {
                    
                    $json['value'] = '';
                    
                }
                
                return $json;
                
            }
            
            function updateUserInputFields($items) {
                
                $defaultName = array('user_login' => 'Username', 'user_email' => 'Email', 'user_pass' => 'Password');
                $userInputFields = array();
                for ($i = 0; $i < count($items); $i++) {
                    
                    $item = $items[$i];
                    if (array_key_exists($item['id'], $defaultName) === true) {
                        
                        $item['name'] = $defaultName[ $item['id'] ];
                        
                    }
                    
                    $userInputFields[$item['id']] = $item;
                    
                }
                
                return $userInputFields;
                
            }
            
            $userInputFields = $this->initialUserInputFields();
            if ($mode === 'addUserInput') {
                
                $json = json_decode(sanitize_text_field( stripslashes($_POST['jsonStr']) ), true);
                $json = checkEnabled($json);
                array_push($userInputFields, $json);
                $userInputFields = updateUserInputFields($userInputFields);
                update_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                
            } else if ($mode === 'editUserInput') {
                
                $json = json_decode(sanitize_text_field( stripslashes($_POST['jsonStr']) ), true);
                $json = checkEnabled($json);
                if ($json['id'] === 'user_pass') {
                    
                    $json['type'] = 'PASSWORD';
                    
                }
                for ($i = 0; $i < count($userInputFields); $i++) {
                    
                    if ($userInputFields[$i]['id'] === $json['id']) {
                        
                        $userInputFields[$i] = $json;
                        break;
                        
                    }
                    
                }
                $userInputFields = updateUserInputFields($userInputFields);
                update_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                
            } else if ($mode === 'deleteUserInput') {
                
                array_splice($userInputFields, intval($_POST['index']), 1);
                $userInputFields = updateUserInputFields($userInputFields);
                update_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                
            } else if ($mode === 'changedOrderUserInput') {
                
                $sortedUserInputFields = array();
                $json = json_decode(sanitize_text_field( stripslashes($_POST['keys']) ), true);
                for ($i = 0; $i < count($json); $i++) {
                    
                    $item = (function($id, $userInputFields) {
                        
                        for ($i = 0; $i < count($userInputFields); $i++) {
                            
                            if ($id === $userInputFields[$i]['id']) {
                                
                                return $userInputFields[$i];
                                
                            }
                            
                        }
                        
                    })($json[$i], $userInputFields);
                    array_push($sortedUserInputFields, $item);
                    
                    
                }
                
                $userInputFields = updateUserInputFields($sortedUserInputFields);
                update_option("_" . $this->prefix . "getUserInputFields", json_encode($userInputFields));
                
            }
            
            
            
            
            return array_values($userInputFields);
            
        }
        
        public function getUserInputFields() {
            
            $userInputFields = $this->initialUserInputFields();
            return $userInputFields;
            
        }
        
        public function getForm($accountKey = 1, $originalActive = false){
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_form";
            #$wpdb->query("DROP TABLE IF EXISTS ".$table_name.";");
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d;", array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                
                $wpdb->insert(
                    $table_name, 
    				array(
    			        'accountKey' => intval($accountKey), 
    			        'data' => json_encode($this->defaultFrom())
    				), 
    				array('%d', '%s')
    	        );
                
                return $this->defaultFrom();
                
            } else {
                
                $form = array();
                $data = json_decode($row['data'], true);
                if (is_array($data)) {
                    
                    foreach ((array) $data as $key => $value) {
                        
                        if (isset($value['active']) === false) {
                            
                            $value['active'] = '';
                            
                        }
                        
                        #$options = json_decode($value['options'], true);
                        if (is_string($value['options']) === true) {
                            
                            $value['options'] = explode(',', $value['options']);
                            
                        }
                        
                        if ($originalActive === true) {
                            
                            $value['originalActive'] = $value['active'];
                            
                        }
                        
                        if (isset($value['isAutocomplete']) === false) {
                            
                            $value['isAutocomplete'] = 'true';
                            
                        }
                        
                        if (isset($value['isSMS']) === false) {
                            
                            $value['isSMS'] = 'false';
                            
                        }
                        
                        if (isset($value['placeholder']) === false) {
                            
                            $value['placeholder'] = '';
                            
                        }
                        
                        array_push($form, $value);
                        
                    }
                    
                }
                
                return $form;
                #return json_decode($row['data'], true);
                
            }
            
        }
        
        /**
        public function getFormList(){
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_form";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d;", array(intval($accountKey)));
            
        }
        **/
        
        public function getCourseList($accountKey = 1) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            $isExtensionsValid = $this->getExtensionsValid();
            for ($i = 0; $i < count($rows); $i++) {
                
                $rows[$i]['timeToProvide'] = json_decode($rows[$i]['timeToProvide'], true);
                if (is_null($rows[$i]['timeToProvide']) || is_string($rows[$i]['timeToProvide']) || $rows[$i]['timeToProvide'] === false) {
                    
                    $rows[$i]['timeToProvide'] = array();
                    
                }
                
                $options = json_decode($rows[$i]['options'], true);
                if (is_array($options)) {
                    
                    if (count($options) > 0 && isset($options[0]['cost_1']) === false) {
                        
                        for ($a = 0; $a < count($options); $a++) {
                            
                            $options[$a]['cost_1'] = $options[$a]['cost'];
                            $options[$a]['cost_2'] = $options[$a]['cost'];
                            $options[$a]['cost_3'] = $options[$a]['cost'];
                            $options[$a]['cost_4'] = $options[$a]['cost'];
                            $options[$a]['cost_5'] = $options[$a]['cost'];
                            $options[$a]['cost_6'] = $options[$a]['cost'];
                            
                        }
                        
                    }
                    
                    $rows[$i]['options'] = json_encode($options);
                    
                } else {
                    
                    $rows[$i]['options'] = "[]";
                    
                }
                
                if (is_null($rows[$i]['cost']) === true) {
                    
                    $rows[$i]['cost'] = 0;
                    
                }
                
                if (is_null($rows[$i]['cost_1']) === true) {
                    
                    /**
                    $options = json_decode($rows[$i]['options'], true);
                    for ($a = 0; $a < count($options); $a++) {
                        
                        $options[$a]['cost_1'] = $options[$a]['cost'];
                        $options[$a]['cost_2'] = $options[$a]['cost'];
                        $options[$a]['cost_3'] = $options[$a]['cost'];
                        $options[$a]['cost_4'] = $options[$a]['cost'];
                        $options[$a]['cost_5'] = $options[$a]['cost'];
                        $options[$a]['cost_6'] = $options[$a]['cost'];
                        
                    }
                    
                    $rows[$i]['options'] = $options;
                    **/
                    $options = json_encode($options);
                    $rows[$i]['cost_1'] = $rows[$i]['cost'];
                    $table_name = $wpdb->prefix . "booking_package_services";
					
					try {
					    
					    $wpdb->query("START TRANSACTION");
					    $wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
						$bool = $wpdb->update(
							$table_name,
							array(
								'cost_1' => intval($rows[$i]['cost']), 
								'cost_2' => intval($rows[$i]['cost']), 
								'cost_3' => intval($rows[$i]['cost']), 
								'cost_4' => intval($rows[$i]['cost']), 
								'cost_5' => intval($rows[$i]['cost']), 
								'cost_6' => intval($rows[$i]['cost']), 
								'options' => $options,
							),
							array('key' => intval($rows[$i]['key'])),
							array(
								'%d', '%d', '%d', '%d', '%d', '%d', '%s', 
							),
							array('%d')
						);
					
						$wpdb->query('COMMIT');
						$wpdb->query('UNLOCK TABLES');
						
					} catch (Exception $e) {
						
						$wpdb->query('ROLLBACK');
						$wpdb->query('UNLOCK TABLES');
						
					}/** finally {
						
						$wpdb->query('UNLOCK TABLES');
						
					}**/
                    
                }
                
                if ($isExtensionsValid === false) {
                    
                    $rows[$i]['options'] = "[]";
                    $rows[$i]['timeToProvide'] = array();
                    $rows[$i]['stopServiceUnderFollowingConditions'] = 'doNotStop';
                    $rows[$i]['cost_2'] = $rows[$i]['cost_1'];
                    $rows[$i]['cost_3'] = $rows[$i]['cost_1'];
                    $rows[$i]['cost_4'] = $rows[$i]['cost_1'];
                    $rows[$i]['cost_5'] = $rows[$i]['cost_1'];
                    $rows[$i]['cost_6'] = $rows[$i]['cost_1'];
                    
                }
                
            }
            /**
            if ($isExtensionsValid === false) {
				
				for($i = 0; $i < count($rows); $i++){
					
					$rows[$i]['options'] = "[]";
					
				}
				
			}
            **/
            return $rows;
            
        }
        
        /**
        public function getAllCourseList(){
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            
            
        }
        **/
        
        public function getCouponsList($accountKey = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_coupons";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `status` = 'active' AND `accountKey` = %d ORDER BY `key` ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            
        }
        
        public function addCoupons($accountKey = false) {
            
            global $wpdb;
            $id = strtoupper(uniqid('coupon_'));
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            $active = 0;
            if (intval($_POST['active']) == 1) {
                
                $active = 1;
                
            }
            
            $table_name = $wpdb->prefix."booking_package_coupons";
            $wpdb->insert(
                $table_name, 
                array(
                    'accountKey' => intval($accountKey), 
                    'id' => sanitize_text_field($id), 
                    'name' => sanitize_text_field(stripslashes($_POST['name'])), 
                    'target' => sanitize_text_field($_POST['target']),
                    'limited' => sanitize_text_field($_POST['limited']), 
                    'method' => sanitize_text_field($_POST['method']), 
                    'value' => intval($_POST['value']),
                    'expirationDateStatus' => intval($expirationDateStatus),
                    'expirationDateFrom' => intval($expirationDateFrom),
                    'expirationDateTo' => intval($expirationDateTo),
                    'description'       => sanitize_textarea_field(stripslashes($_POST['description'])),
                    'active'    => intval($active),
                ), 
				array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d')
			);
            
            
            return $this->getCouponsList($accountKey);
            
        }
        
        public function deleteCouponsItem($accountKey = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_coupons";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'status' => 'deleted', 
                    ),
                    array('key' => intval($_POST['key'])),
                    array(),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getCouponsList($accountKey);
            
        }
        
        public function updateCoupons($accountKey = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_coupons";
            $sql = $wpdb->prepare(
                "SELECT * FROM ".$table_name." WHERE `status` = 'active' AND `key` = %d ORDER BY `key` ASC;", 
                array(intval($_POST['key']))
            );
            $coupon = $wpdb->get_row($sql, ARRAY_A);
            if (!empty($coupon)) {
                
                if (!empty($_POST['id']) && $_POST['id'] != $coupon['id']) {
                    
                    $sql = $wpdb->prepare(
                        "SELECT * FROM ".$table_name." WHERE `status` = 'active' AND `accountKey` = %d AND `id` = %s;", 
                        array(
                            intval($_POST['key']), 
                            sanitize_text_field($_POST['id'])
                        )
                    );
                    $row = $wpdb->get_row($sql, ARRAY_A);
                    if (!empty($row)) {
                        
                        $_POST['id'] = $coupon['id'];
                        
                    }
                    
                }
                
                $expirationDate = $this->validExpirationDate();
                $expirationDateStatus = $expirationDate['expirationDateStatus'];
                $expirationDateFrom = $expirationDate['expirationDateFrom'];
                $expirationDateTo = $expirationDate['expirationDateTo'];
                $active = 0;
                if (intval($_POST['active']) == 1) {
                    
                    $active = 1;
                    
                }
                
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$wpdb->update(
                        $table_name,
                        array(
                            'id' => sanitize_text_field($_POST['id']), 
                            'name' => sanitize_text_field(stripslashes($_POST['name'])), 
                            'target' => sanitize_text_field($_POST['target']),
                            'limited' => sanitize_text_field($_POST['limited']), 
                            'method' => sanitize_text_field($_POST['method']), 
                            'value' => intval($_POST['value']),
                            'expirationDateStatus' => intval($expirationDateStatus),
                            'expirationDateFrom' => intval($expirationDateFrom),
                            'expirationDateTo' => intval($expirationDateTo),
                            'description'       => sanitize_textarea_field(stripslashes($_POST['description'])),
                            'active' => intval($active),
                        ),
                        array('key' => intval($_POST['key'])),
                        array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d'),
                        array('%d')
                    );
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
            }
            
            
            
            return $this->getCouponsList($accountKey);
            
        }
        
        public function getListOfDaysOfWeek() {
            
            $numberKeys = array();
            $guestsList = $this->guestsInputType();
            if (isset($guestsList['json']['optionsType']['prices'])) {
                
                $dayOfWeeks = $guestsList['json']['optionsType']['prices']['options'];
                for ($i = 0; $i < count($dayOfWeeks); $i++) {
                    
                    array_push($numberKeys, $dayOfWeeks[$i]['key']);
                    
                }
                
            }
            
            return $numberKeys;
            
        }
        
        public function getObjectOfDaysOfWeek() {
            
            $numberObject = array();
            $numberKeys = $this->getListOfDaysOfWeek();
            for ($i = 0; $i < count($numberKeys); $i++) {
                
                $numberObject[$numberKeys[$i]] = 0;
                
            }
            
            return $numberObject;
            
        }
        
        public function getStaffList($accountKey = false, $booking = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_staff";
            return array();
            
        }
        
        public function addStaff($accountKey = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_staff";
            return array();
            
        }
        
        public function updateStaff($accountKey = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_staff";
            return array();
            
        }
        
        public function deleteStaffItem($accountKey = false) {
            
            global $wpdb;
            if($accountKey != false){
                
                $table_name = $wpdb->prefix . "booking_package_staff";
                #$wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
                
                return $this->getStaffList($accountKey);
                
            }
            
            die();
            
        }
        
        public function changeStaffRank($accountKey = false) {
            
            global $wpdb;
            if($accountKey != false){
                
                $table_name = $wpdb->prefix . "booking_package_staff";
                /**
                $keyList = explode(",", $_POST['keyList']);
                for($i = 0; $i < count($keyList); $i++){
                    
                    $ranking = $i + 1;
                    $wpdb->update(
                        $table_name,
                        array(
                            'ranking' => intval($ranking)
                        ),
                        array('key' => intval($keyList[$i]), 'accountKey' => intval($accountKey)),
                        array('%d'),
                        array('%d', '%d')
                    );
                    
                }
                **/
                return $this->getStaffList($accountKey);
                
            }
            
            die();
            
        }
        
        public function getGuestsList($accountKey = false, $booking = false) {
            
            global $wpdb;
            $numberKeys = $this->getListOfDaysOfWeek();
            $isExtensionsValid = $this->getExtensionsValid();
            $table_name = $wpdb->prefix . "booking_package_guests";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            if ($booking === true) {
                
                $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d AND `active` = 'true' ORDER BY ranking ASC;", array(intval($accountKey)));
                
            }
            $rows = $wpdb->get_results($sql, ARRAY_A);
            /**
            if ($booking == true) {
                
                foreach ((array) $rows as $key => $value) {
                    
                    $list = json_decode($value['json'], true);
                    array_unshift($list, array("number" => 0, "price" => 0, "name" => __("Select", 'booking-package')));
                    $value['json'] = json_encode($list);
                    #$value['json'] = $list;
                    if ($isExtensionsValid !== true) {
                        
                        $value['costInServices'] = 'cost_1';
                        $value['reflectService'] = '0';
                        $value['reflectAdditional'] = '0';
                        
                    }
                    $rows[$key] = $value;
                    
                }
                
            }
            **/
            
            foreach ((array) $rows as $key => $value) {
                
                $updatePrices = false;
                $list = json_decode($value['json'], true);
                
                /**
                if (isset($list[0]) && isset($numberKeys[0]) && isset($list[0][$numberKeys[0]]) === false) {
                    
                    $updatePrices = true;
                    
                }
                **/
                if ($booking === true) {
                    
                    array_unshift($list, array("number" => 0, "price" => 0, "name" => __("Select", 'booking-package')));
                    if ($isExtensionsValid !== true) {
                        
                        $value['costInServices'] = 'cost_1';
                        $value['reflectService'] = '0';
                        $value['reflectAdditional'] = '0';
                        
                    }
                    
                }
                
                for ($i = 0; $i < count($list); $i++) {
                    
                    if (isset($list[$i]['price']) === false) {
                        
                        $list[$i]['price'] = 0;
                        
                    }
                    
                    for ($a = 0; $a < count($numberKeys); $a++) {
                        
                        if (isset($list[$i][$numberKeys[$a]]) === false) {
                            
                            $list[$i][$numberKeys[$a]] = $list[$i]['price'];
                            
                        }
                        
                        if (empty($list[$i][$numberKeys[$a]])) {
                            
                            $list[$i][$numberKeys[$a]] = 0;
                            
                        }
                        
                        if ($isExtensionsValid === false && ($numberKeys[$a] == 'priceOnDayBeforeNationalHoliday' || $numberKeys[$a] == 'priceOnNationalHoliday')) {
                            
                            $list[$i][$numberKeys[$a]] = 0;
                            
                        }
                        
                    }
                    
                }
                
                $value['json'] = json_encode($list);
                $rows[$key] = $value;
                
            }
            
            return $rows;
            
        }
        
        public function updateGuests($accountKey = false){
            
            global $wpdb;
            if ($accountKey != false) {
                /**
                $numberKeys = array(
                    'number',
                    'price',
                    'priceOnMonday',
                    'priceOnTuesday',
                    'priceOnWednesday',
                    'priceOnThursday',
                    'priceOnFriday',
                    'priceOnSaturday',
                    'priceOnSunday',
                    'priceOnDayBeforeNationalHoliday',
                    'priceOnNationalHoliday',
                );
                **/
                
                $numberKeys = $this->getListOfDaysOfWeek();
                array_unshift($numberKeys, 'number', 'price');
                $json = array();
                if (isset($_POST['json'])) {
                    
                    $jsonList = json_decode(stripslashes($_POST['json']), true);
                    for ($i = 0; $i < count($jsonList); $i++) {
                        
                        $object = array();
                        foreach ((array) $jsonList[$i] as $key => $value) {
                            
                            $object[sanitize_text_field($key)] = sanitize_text_field($value);
                            $isNumber = array_search($key, $numberKeys);
                            if ($isNumber !== false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                $object[sanitize_text_field($key)] = intval($value);
                                
                            }
                            
                            if ($this->guestForDayOfTheWeekRates === 0) {
                                
                                if ($key !== 'number' && $key !== 'price' && $key !== 'name') {
                                    
                                    unset($object[sanitize_text_field($key)]);
                                    
                                }
                                
                                
                            }
                            
                            /**
                            if ($key == 'number' || $key == 'price') {
                                
                                $value = $this->getOnlyNumbers($value);
                                $object[sanitize_text_field($key)] = intval($value);
                                
                            }
                            **/
                        }
                        
                        array_push($json, $object);
                        
                    }
                    
                    $guestsInCapacity = 'included';
                    if (isset($_POST['guestsInCapacity'])) {
                        
                        $guestsInCapacity = $_POST['guestsInCapacity'];
                        if (empty($_POST['guestsInCapacity']) === true) {
                            
                            $guestsInCapacity = 'included';
                            
                        }
                        
                    }
                    
                    $reflectService = 0;
                    if (isset($_POST['reflectService'])) {
                        
                        $reflectService = intval($_POST['reflectService']);
                        
                    }
                    
                    $reflectAdditional = 0;
                    if (isset($_POST['reflectAdditional'])) {
                        
                        $reflectAdditional = intval($_POST['reflectAdditional']);
                        
                    }
                    
                    $costInServices = 'cost_1';
                    if (isset($_POST['costInServices']) && $this->getExtensionsValid() === true) {
                        
                        $costInServices = $_POST['costInServices'];
                        
                    }
                    
                    $table_name = $wpdb->prefix."booking_package_guests";
                    try {
						
						$wpdb->query("START TRANSACTION");
						$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
						$wpdb->update(
                            $table_name,
                            array(
                                'name' => sanitize_text_field(stripslashes($_POST['name'])), 
                                'costInServices' => sanitize_text_field($costInServices),
                                'target' => sanitize_text_field($_POST['target']), 
                                'guestsInCapacity' => sanitize_text_field($guestsInCapacity),
                                'json' => json_encode($json),
                                'required' => intval($_POST['required']),
                                'reflectService' => intval($reflectService),
                                'reflectAdditional' => intval($reflectAdditional),
                                'description'       => sanitize_textarea_field(stripslashes($_POST['description'])),
                                'active' => sanitize_text_field($_POST['active']),
                            ),
                            array('key' => intval($_POST['key'])),
                            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s'),
                            array('%d')
                        );
						
						$wpdb->query('COMMIT');
						$wpdb->query('UNLOCK TABLES');
						
					} catch (Exception $e) {
						
						$wpdb->query('ROLLBACK');
						$wpdb->query('UNLOCK TABLES');
						
					}/** finally {
						
						$wpdb->query('UNLOCK TABLES');
						
					}**/
                    
                }
                
                #return $json;
                return $this->getGuestsList($accountKey);
                
            }
            
            die();
            
        }
        
        public function addGuests($accountKey = false){
            
            global $wpdb;
            if ($accountKey != false) {
                
                /**
                $numberKeys = array(
                    'number',
                    'price',
                    'priceOnMonday',
                    'priceOnTuesday',
                    'priceOnWednesday',
                    'priceOnThursday',
                    'priceOnFriday',
                    'priceOnSaturday',
                    'priceOnSunday',
                    'priceOnDayBeforeNationalHoliday',
                    'priceOnNationalHoliday',
                );
                **/
                $numberKeys = $this->getListOfDaysOfWeek();
                array_unshift($numberKeys, 'number', 'price');
                $json = array();
                if (isset($_POST['json'])) {
                    
                    $jsonList = json_decode(stripslashes($_POST['json']), true);
                    for ($i = 0; $i < count($jsonList); $i++) {
                        
                        $object = array();
                        foreach ((array) $jsonList[$i] as $key => $value) {
                            
                            $object[sanitize_text_field($key)] = sanitize_text_field($value);
                            $isNumber = array_search($key, $numberKeys);
                            if ($isNumber !== false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                $object[sanitize_text_field($key)] = intval($value);
                                
                            }
                            /**
                            if ($key == 'number' || $key == 'price') {
                                
                                $value = $this->getOnlyNumbers($value);
                                $object[sanitize_text_field($key)] = intval($value);
                                
                            }
                            **/
                            
                        }
                        
                        array_push($json, $object);
                        
                    }
                    
                    $guestsInCapacity = 'included';
                    if (isset($_POST['guestsInCapacity'])) {
                        
                        $guestsInCapacity = $_POST['guestsInCapacity'];
                        if (empty($_POST['guestsInCapacity']) === true) {
                            
                            $guestsInCapacity = 'included';
                            
                        }
                        
                    }
                    
                    $reflectService = 0;
                    if (isset($_POST['reflectService'])) {
                        
                        $reflectService = intval($_POST['reflectService']);
                        
                    }
                    
                    $reflectAdditional = 0;
                    if (isset($_POST['reflectAdditional'])) {
                        
                        $reflectAdditional = intval($_POST['reflectAdditional']);
                        
                    }
                    
                    $costInServices = 'cost_1';
                    if (isset($_POST['costInServices']) && $this->getExtensionsValid() === true) {
                        
                        $costInServices = $_POST['costInServices'];
                        
                    }
                    
                    $table_name = $wpdb->prefix."booking_package_guests";
                    $sql = $wpdb->prepare("SELECT COUNT(*) FROM ".$table_name." WHERE `accountKey` = %d;", array(intval($accountKey)));
                    $row = $wpdb->get_row($sql, ARRAY_A);
                    $count = $row['COUNT(*)'] + 1;
                    #var_dump($count);
                    
                    $wpdb->insert(
                        $table_name, 
    					array(
                            'accountKey' => intval($accountKey), 
    			            'name' => sanitize_text_field(stripslashes($_POST['name'])), 
    			            'costInServices' => sanitize_text_field($costInServices),
    			            'target' => sanitize_text_field($_POST['target']), 
    			            'guestsInCapacity' => sanitize_text_field($guestsInCapacity),
    				        'json' => json_encode($json), 
    				        'ranking' => intval($count),
    				        'required' => intval($_POST['required']),
    				        'reflectService' => intval($reflectService),
    				        'reflectAdditional' => intval($reflectAdditional),
    				        'description'       => sanitize_textarea_field(stripslashes($_POST['description'])),
    				        'active' => sanitize_text_field($_POST['active']),
    					), 
    					array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
    				);
                    
                }
                
                return $this->getGuestsList($accountKey);
                
            }
            
            die();
            
        }
        
        public function deleteGuestsItem($accountKey = false){
            
            global $wpdb;
            if($accountKey != false){
                
                $table_name = $wpdb->prefix."booking_package_guests";
                $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
                
                return $this->getGuestsList($accountKey);
                
            }
            
            die();
            
        }
        
        public function changeGuestsRank($accountKey = false){
            
            global $wpdb;
            if($accountKey != false){
                
                $table_name = $wpdb->prefix . "booking_package_guests";
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$keyList = explode(",", $_POST['keyList']);
                    for($i = 0; $i < count($keyList); $i++){
                        
                        $ranking = $i + 1;
                        $wpdb->update(
                            $table_name,
                            array(
                                'ranking' => intval($ranking)
                            ),
                            array('key' => intval($keyList[$i]), 'accountKey' => intval($accountKey)),
                            array('%d'),
                            array('%d', '%d')
                        );
                        
                    }
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
                return $this->getGuestsList($accountKey);
                
            }
            
            die();
            
        }
        
        public function validExpirationDate() {
            
            $response = array('expirationDateStatus' => 0, 'expirationDateFrom' => 0, 'expirationDateTo' => 0);
            if (isset($_POST['expirationDateStatus'])) {
                
                if (empty($_POST['expirationDateStatus']) === false) {
                    
                    $response['expirationDateStatus'] = $_POST['expirationDateStatus'];
                    
                }
                $response['expirationDateFrom'] = $_POST['expirationDateFromYear'] . sprintf('%02d', $_POST['expirationDateFromMonth']) . sprintf('%02d', $_POST['expirationDateFromDay']);
                $response['expirationDateTo'] = $_POST['expirationDateToYear'] . sprintf('%02d', $_POST['expirationDateToMonth']) . sprintf('%02d', $_POST['expirationDateToDay']);
                
            }
            
            return $response;
            
        }
        
        public function getBlockEmailLists($schedule) {
            
            global $wpdb;
            $response = array();
            $dateFormat = intval(get_option($this->prefix."dateFormat", 0));
			$positionOfWeek = get_option($this->prefix."positionOfWeek", "before");
			
			$table_name = $wpdb->prefix . "booking_package_block_list";
			#$sql = $wpdb->prepare("SELECT * FROM " . $table_name . " ORDER BY date DESC;", array());
			$rows = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY date DESC;", ARRAY_A);
			foreach ((array) $rows as $key => $value) {
			    
			    $value['date'] = $schedule->dateFormat($dateFormat, $positionOfWeek, $value['date'], '', true, true, 'text');
			    array_push($response, $value);
			    
			}
			
            return $response;
            
        }
        
        public function addBlockEmail($email, $schedule) {
            
            global $wpdb;
            $lastKey = null;
            $email = sanitize_text_field(trim($email));
            $dateFormat = intval(get_option($this->prefix."dateFormat", 0));
			$positionOfWeek = get_option($this->prefix."positionOfWeek", "before");
			$date = date("U");
			$table_name = $wpdb->prefix . "booking_package_block_list";
            $sql = $wpdb->prepare(
                "SELECT `key` FROM `" . $table_name . "` WHERE `value` = %s;", 
                array($email)
            );
			$row = $wpdb->get_row($sql, ARRAY_A);
			
			if (is_null($row)) {
                
                $wpdb->insert(
                    $table_name, 
                    array(
                        'type' => 'email', 
                        'value' => $email, 
                        'date' => intval($date),
                    ), 
                    array('%s', '%s', '%d')
                );
                $lastKey = $wpdb->insert_id;
                $date = $schedule->dateFormat($dateFormat, $positionOfWeek, $date, '', true, true, 'text');
                $blocskList = $this->getBlockEmailLists($schedule);
                return array('status' => 'success', 'key' => $lastKey, 'email' => $email, 'date' => $date, 'blocskList' => $blocskList);
                
			}
			
			return array('status' => 'error', 'message' => sprintf(__('You have already added the "%s".', 'booking-package'), $email));
			
        }
        
        public function deleteBlockEmail($key, $schedule) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_block_list";
            $wpdb->delete(
    			$table_name, 
    			array(
    				'key' => intval($key)
    			), 
    			array('%d')
    		);
            
            return $this->getBlockEmailLists($schedule);
            
        }
        
        public function addCourse(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            $options = array();
            if (isset($_POST['options'])) {
                
                $jsonList = json_decode(stripslashes($_POST['options']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    foreach ((array) $jsonList[$i] as $key => $value) {
                        
                        if ($key == 'cost_1' || $key == 'cost_2' || $key == 'cost_3' || $key == 'cost_4' || $key == 'cost_5' || $key == 'cost_6') {
                            
                            if (is_int($value) === false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                
                            }
                            
                        }
                        
                        $object[sanitize_text_field($key)] = sanitize_text_field($value);
                        
                    }
                    array_push($options, $object);
                    
                }
                
                if ($this->getExtensionsValid() === false) {
                    
                    $options = array();
                    
                }
                
            }
            
            $timeToProvide = array();
            if (isset($_POST['timeToProvide'])) {
                
                $jsonList = json_decode(stripslashes($_POST['timeToProvide']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    foreach ((array) $jsonList[$i] as $key => $value) {
                        
                        $object[sanitize_text_field($key)] = sanitize_text_field($value);
                        
                    }
                    array_push($timeToProvide, $object);
                    
                }
                
                
                if ($this->getExtensionsValid() === false) {
                    
                    $timeToProvide = array();
                    
                }
                
            }
            
            if (!isset($_POST['target'])) {
                
                $_POST['target'] = 'visitors_users';
                
            }
            
            if (!isset($_POST['stopServiceUnderFollowingConditions'])) {
                
                $_POST['stopServiceUnderFollowingConditions'] = 'doNotStop';
                $_POST['stopServiceForDayOfTimes'] = 'timeSlot';
                $_POST['stopServiceForSpecifiedNumberOfTimes'] = 0;
                
            }
            
            if (!isset($_POST['doNotStopServiceAsException']) || empty($_POST['doNotStopServiceAsException'])) {
                
                $_POST['doNotStopServiceAsException'] = 'hasNotException';
                
            }
            
            for ($i = 1; $i <= 6; $i++) {
                
                if (!isset($_POST['cost_' . $i])) {
                    
                    $_POST['cost_' . $i] = 0;
                    
                }
                
                if (is_int($_POST['cost_' . $i]) === false) {
                    
                    $_POST['cost_' . $i] = $this->getOnlyNumbers($_POST['cost_' . $i]);
                    
                }
                
                if ($i > 1 && $this->getExtensionsValid() === false) {
                    
                    $_POST['cost_' . $i] = 0;
                    
                }
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            $wpdb->insert(	
                $table_name, 
                array(
                    'accountKey'            => intval($accountKey), 
                    'name'                  => sanitize_text_field($_POST['name']), 
                    'description'           => sanitize_textarea_field($_POST['description']),
                    'cost_1'                => intval($_POST['cost_1']), 
                    'cost_2'                => intval($_POST['cost_2']), 
                    'cost_3'                => intval($_POST['cost_3']), 
                    'cost_4'                => intval($_POST['cost_4']), 
                    'cost_5'                => intval($_POST['cost_5']), 
                    'cost_6'                => intval($_POST['cost_6']), 
                    'time'                  => intval($_POST['time']), 
                    'ranking'               => intval($_POST['rank']),
                    'active'                => sanitize_text_field($_POST['active']),
                    'target'                => sanitize_text_field($_POST['target']),
                    'selectOptions'         => intval($_POST['selectOptions']),
                    'options'               => json_encode($options),
                    'timeToProvide'         => json_encode($timeToProvide),
                    'expirationDateStatus'  => intval($expirationDateStatus),
                    'expirationDateFrom'    => intval($expirationDateFrom),
                    'expirationDateTo'      => intval($expirationDateTo),
                    'stopServiceUnderFollowingConditions' => sanitize_text_field($_POST['stopServiceUnderFollowingConditions']),
                    'doNotStopServiceAsException' => sanitize_text_field($_POST['doNotStopServiceAsException']),
                    'stopServiceForDayOfTimes' => sanitize_text_field($_POST['stopServiceForDayOfTimes']),
                    'stopServiceForSpecifiedNumberOfTimes'  => intval($_POST['stopServiceForSpecifiedNumberOfTimes']),
                ), 
                array(
                    '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', 
                    '%d', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', 
                    '%s', '%s', '%d', 
                )
            );
    		/**
			$sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            **/
            return $this->getCourseList($accountKey);
            
        }
        
        public function updateCourse(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            $options = array();
            if (isset($_POST['options'])) {
                
                $jsonList = json_decode(stripslashes($_POST['options']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    foreach ((array) $jsonList[$i] as $key => $value) {
                        
                        if ($key == 'cost_1' || $key == 'cost_2' || $key == 'cost_3' || $key == 'cost_4' || $key == 'cost_5' || $key == 'cost_6') {
                            
                            if (is_int($value) === false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                
                            }
                            
                        }
                        
                        $object[sanitize_text_field($key)] = sanitize_text_field($value);
                        
                    }
                    
                    array_push($options, $object);
                    
                }
                
                if ($this->getExtensionsValid() === false) {
                    
                    $options = array();
                    
                }
                
            }
            
            $timeToProvide = array();
            if (isset($_POST['timeToProvide'])) {
                
                $jsonList = json_decode(stripslashes($_POST['timeToProvide']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    if (is_array($jsonList[$i])) {
                        
                        foreach ((array) $jsonList[$i] as $key => $value) {
                            
                            $object[sanitize_text_field($key)] = sanitize_text_field($value);
                            
                        }
                        
                    }
                    
                    array_push($timeToProvide, $object);
                    
                }
                
                
                if ($this->getExtensionsValid() === false) {
                    
                    $timeToProvide = array();
                    
                }
                
            }
            
            if (!isset($_POST['target'])) {
                
                $_POST['target'] = 'visitors_users';
                
            }
            
            if (!isset($_POST['stopServiceUnderFollowingConditions'])) {
                
                $_POST['stopServiceUnderFollowingConditions'] = 'doNotStop';
                $_POST['stopServiceForDayOfTimes'] = 'timeSlot';
                $_POST['stopServiceForSpecifiedNumberOfTimes'] = 0;
                
            }
            
            if (!isset($_POST['doNotStopServiceAsException']) || empty($_POST['doNotStopServiceAsException'])) {
                
                $_POST['doNotStopServiceAsException'] = 'hasNotException';
                
            }
            
            for ($i = 1; $i <= 6; $i++) {
                
                if (!isset($_POST['cost_' . $i])) {
                    
                    $_POST['cost_' . $i] = 0;
                    
                }
                
                if (is_int($_POST['cost_' . $i]) === false) {
                    
                    $_POST['cost_' . $i] = $this->getOnlyNumbers($_POST['cost_' . $i]);
                    
                }
                
                if ($i > 1 && $this->getExtensionsValid() === false) {
                    
                    $_POST['cost_' . $i] = 0;
                    
                }
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'name'                                  => sanitize_text_field(stripslashes($_POST['name'])), 
                        'description'                           => sanitize_textarea_field(stripslashes($_POST['description'])),
                        'time'                                  => intval($_POST['time']), 
                        'cost_1'                                => intval($_POST['cost_1']), 
                        'cost_2'                                => intval($_POST['cost_2']), 
                        'cost_3'                                => intval($_POST['cost_3']), 
                        'cost_4'                                => intval($_POST['cost_4']), 
                        'cost_5'                                => intval($_POST['cost_5']), 
                        'cost_6'                                => intval($_POST['cost_6']), 
                        'active'                                => sanitize_text_field($_POST['active']),
                        'target'                                => sanitize_text_field($_POST['target']),
                        'selectOptions'                         => intval($_POST['selectOptions']),
                        'options'                               => json_encode($options),
                        'timeToProvide'                         => json_encode($timeToProvide),
                        'expirationDateStatus'                  => intval($expirationDateStatus),
                        'expirationDateFrom'                    => intval($expirationDateFrom),
                        'expirationDateTo'                      => intval($expirationDateTo),
                        'stopServiceUnderFollowingConditions'   => sanitize_text_field($_POST['stopServiceUnderFollowingConditions']),
                        'doNotStopServiceAsException'           => sanitize_text_field($_POST['doNotStopServiceAsException']),
                        'stopServiceForDayOfTimes'              => sanitize_text_field($_POST['stopServiceForDayOfTimes']),
                        'stopServiceForSpecifiedNumberOfTimes'  => intval($_POST['stopServiceForSpecifiedNumberOfTimes']),
                    ),
                    array('key' => intval($_POST['key'])),
                    array(
                        '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', 
                        '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', 
                        '%d', 
                    ),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            
            
            return $this->getCourseList($accountKey);
            
        }
        
        public function copyCourse() {
            
            $accountKey = 1;
            if(isset($_POST['accountKey'])){
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            $tmp_table_name = $table_name."_tmp";
            
            $sql = $wpdb->prepare('SELECT COUNT(`key`) as `count` FROM ' . $table_name . ' WHERE `accountKey` = %d', array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            $ranking = $row['count'] + 1;
            
            $sql = $wpdb->prepare("CREATE TEMPORARY TABLE " . $tmp_table_name . " SELECT * FROM " . $table_name . " WHERE `key` = %d;", array(intval($_POST['key'])));
    		$wpdb->query($sql);
    		$wpdb->query("ALTER TABLE " . $tmp_table_name . " drop `key`;");
    		$sql = $wpdb->prepare("UPDATE " . $tmp_table_name . " SET `name` = CONCAT(name, ' Copy'), `ranking` = %d, `active` = '';", array(intval($ranking)));
    		$wpdb->query($sql);
    		#$wpdb->query("UPDATE " . $tmp_table_name . " SET `name` = CONCAT(name, ' Copy'), `active` = '';");
    		$wpdb->query("INSERT INTO " . $table_name . " SELECT 0," . $tmp_table_name . ".* FROM " . $tmp_table_name . ";");
    		$wpdb->query("DROP TABLE " . $tmp_table_name . ";");
            
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            
        }
        
        public function deleteCourse(){
            
            $accountKey = 1;
            if(isset($_POST['accountKey'])){
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            
        }
        
        public function getSubscriptions(){
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_subscriptions";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($_POST['accountKey'])));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            $isExtensionsValid = $this->getExtensionsValid();
            if ($isExtensionsValid === false) {
				
				return array();
				
			} else {
			    
			    return $rows;
			    
			}
            
        }
        
        public function addSubscriptions(){
            
            $accountKey = $_POST['accountKey'];
            if ($this->getExtensionsValid() === false) {
                
                return array();
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_subscriptions";
            $wpdb->insert(	$table_name, 
    						array(
    					   		'accountKey'    => intval($accountKey), 
    							'name'          => sanitize_text_field($_POST['name']), 
    							'subscription'  => sanitize_text_field($_POST['subscription']), 
    							'active'        => sanitize_text_field($_POST['active']), 
    							'ranking'       => intval($_POST['rank']),
    							'renewal'       => intval($_POST['renewal']),
    							'limit'         => intval($_POST['limit']),
    							'numberOfTimes' => intval($_POST['numberOfTimes']),
    						), 
    						array('%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d')
    					);
    		
			$sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            
        }
        
        public function updateSubscriptions(){
            
            $accountKey = $_POST['accountKey'];
            if ($this->getExtensionsValid() === false) {
                
                return array();
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_subscriptions";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'name' => sanitize_text_field($_POST['name']), 
                        'active' => sanitize_text_field($_POST['active']),
                        'renewal' => intval($_POST['renewal']), 
                        'limit' => intval($_POST['limit']), 
                        'numberOfTimes' => intval($_POST['numberOfTimes']),
                    ),
                    array('key' => intval($_POST['key'])),
                    array('%s', '%s', '%d', '%d', '%d'),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getSubscriptions();
            
        }
        
        public function deleteSubscriptions(){
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_subscriptions";
            $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
            return $this->getSubscriptions();
            
        }
        
        public function changeSubscriptionsRank(){
            
            $keyList = explode(",", $_POST['keyList']);
            $indexList = explode(",", $_POST['indexList']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_subscriptions";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				for($i = 0; $i < count($keyList); $i++){
                    
                    $wpdb->update(
                        $table_name,
                        array('ranking' => intval($indexList[$i])),
                        array('key' => intval($keyList[$i])),
                        array('%d'),
                        array('%d')
                    );
                    
                }
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getSubscriptions();
            
        }
        
        public function getTaxes($accountKey, $sort = 'no') {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            if ($sort === 'yes') {
                
                $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d ORDER BY (type = 'surcharge') DESC, (type = 'tax') DESC, ranking ASC;", array(intval($accountKey)));
                
            }
            if (isset($_POST['taxType']) === true) {
                
                $sql = $wpdb->prepare(
                    "SELECT * FROM " . $table_name . " WHERE `accountKey` = %d AND `type` = %s ORDER BY ranking ASC;", 
                    array(
                        intval($accountKey),
                        sanitize_text_field($_POST['taxType'])
                    )
                );
                
            }
            
            $rows = $wpdb->get_results($sql, ARRAY_A);
            $isExtensionsValid = $this->getExtensionsValid();
            if ($isExtensionsValid === false) {
				
				return array();
				
			} else {
			    
			    return $rows;
			    
			}
            
        }
        
        public function addTax($accountKey) {
            
            $_POST['taxType'] = 'tax';
            $_POST['type'] = 'tax';
            $_POST['scope'] = 'day';
            return $this->addTaxAndExtraCharge($accountKey);
            
        }
        
        public function addExtraCharge($accountKey) {
            
            $_POST['taxType'] = 'surcharge';
            $_POST['type'] = 'surcharge';
            $_POST['method'] = 'addition';
            if ( !isset($_POST['scope']) ) {
                
                $_POST['scope'] = 'day';
                
            }
            
            return $this->addTaxAndExtraCharge($accountKey);
            
        }
        
        public function addTaxAndExtraCharge($accountKey) {
            
            if ($_POST['type'] == 'surcharge') {
                
                $_POST['tax'] = 'tax_inclusive';
                
            }
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            $wpdb->insert(
                $table_name, 
                array(
                    'accountKey'    => intval($accountKey), 
                    'name'          => sanitize_text_field($_POST['name']),  
                    'ranking'       => intval($_POST['rank']),
                    'active'        => sanitize_text_field($_POST['active']),
                    'type'          => sanitize_text_field($_POST['type']),
                    'tax'          => sanitize_text_field($_POST['tax']),
                    'method'        => sanitize_text_field($_POST['method']),
                    'target'        => sanitize_text_field($_POST['target']),
                    'scope'         => sanitize_text_field($_POST['scope']),
                    'value'         => floatval($_POST['value']),
                    'expirationDateStatus'  => intval($expirationDateStatus),
                    'expirationDateFrom'    => intval($expirationDateFrom),
                    'expirationDateTo'      => intval($expirationDateTo),
                    'generation' => intval(2),
                ), 
                array('%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%d', '%d', '%d')
            );
            
            return $this->getTaxes($accountKey);
            
        }
        
        public function updateTax($accountKey) {
            
             $_POST['taxType'] = 'tax';
             $_POST['type'] = 'tax';
            return $this->updateTaxAndExtraCharge($accountKey, 'tax');
            
        }
        
        public function updateExtraCharge($accountKey) {
            
             $_POST['taxType'] = 'surcharge';
             $_POST['type'] = 'surcharge';
             $_POST['method'] = 'addition';
            return $this->updateTaxAndExtraCharge($accountKey, 'extraChaarge');
            
        }
        
        public function updateTaxAndExtraCharge($accountKey, $mode) {
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            if (!isset($_POST['active']) || strlen($_POST['active']) == 0) {
                
                $_POST['active'] = "false";
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            $sql = $wpdb->prepare(
				"SELECT `key`, `generation` FROM ".$table_name." WHERE `accountKey` = %d AND `key` = %d;", 
				array(intval($accountKey), intval($_POST['key']))
			);
			$row = $wpdb->get_row($sql, ARRAY_A);
			$generation = intval($row['generation']);
            if ($generation == 2) {
                
                if ($mode == 'tax') {
                    
                    
                    $_POST['scope'] = 'day';
                    
                } else if ($mode == 'extraChaarge') {
                    
                    $_POST['tax'] = 'tax_inclusive';
                    
                }
                
            } else {
                
                if ($_POST['type'] == 'surcharge') {
                    
                    $_POST['tax'] = 'tax_inclusive';
                    
                }
                
            }
            
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'name'          => sanitize_text_field($_POST['name']),  
                        'active'        => sanitize_text_field($_POST['active']),
                        'type'          => sanitize_text_field($_POST['type']),
                        'tax'          => sanitize_text_field($_POST['tax']),
                        'method'        => sanitize_text_field($_POST['method']),
                        'target'        => sanitize_text_field($_POST['target']),
                        'scope'         => sanitize_text_field($_POST['scope']),
                        'value'         => floatval($_POST['value']),
                        'expirationDateStatus'  => intval($expirationDateStatus),
                        'expirationDateFrom'    => intval($expirationDateFrom),
                        'expirationDateTo'      => intval($expirationDateTo),
                    ),
                    array('key' => intval($_POST['key'])),
                    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%d', '%d'),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getTaxes($accountKey);
            
        }
        
        public function deleteTax($accountKey) {
            
             $_POST['taxType'] = 'tax';
            return $this->deleteTaxAndExtraCharge($accountKey);
            
        }
        
        public function deleteExtraCharge($accountKey) {
            
             $_POST['taxType'] = 'surcharge';
            return $this->deleteTaxAndExtraCharge($accountKey);
            
        }
        
        public function deleteTaxAndExtraCharge($accountKey) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
            return $this->getTaxes($accountKey);
            
        }
        
        public function addTaxes($accountKey) {
            
            if ($_POST['type'] == 'surcharge') {
                
                $_POST['tax'] = 'tax_inclusive';
                
            }
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_taxes";
            $wpdb->insert(
                $table_name, 
    			array(
                    'accountKey'    => intval($accountKey), 
    				'name'          => sanitize_text_field($_POST['name']),  
    				'ranking'       => intval($_POST['rank']),
    				'active'        => sanitize_text_field($_POST['active']),
    				'type'          => sanitize_text_field($_POST['type']),
    				'tax'          => sanitize_text_field($_POST['tax']),
    				'method'        => sanitize_text_field($_POST['method']),
    				'target'        => sanitize_text_field($_POST['target']),
    				'scope'         => sanitize_text_field($_POST['scope']),
    				'value'         => floatval($_POST['value']),
    				'expirationDateStatus'  => intval($expirationDateStatus),
                    'expirationDateFrom'    => intval($expirationDateFrom),
                    'expirationDateTo'      => intval($expirationDateTo),
    			), 
    			array('%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%d', '%d')
    		);
    		
            return $this->getTaxes($accountKey);
            
        }
        
        public function updateTaxes($accountKey) {
            
            $expirationDate = $this->validExpirationDate();
            $expirationDateStatus = $expirationDate['expirationDateStatus'];
            $expirationDateFrom = $expirationDate['expirationDateFrom'];
            $expirationDateTo = $expirationDate['expirationDateTo'];
            
            if (!isset($_POST['active']) || strlen($_POST['active']) == 0) {
                
                $_POST['active'] = "false";
                
            }
            
            if ($_POST['type'] == 'surcharge') {
                
                $_POST['tax'] = 'tax_inclusive';
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'name'          => sanitize_text_field($_POST['name']),  
                        'active'        => sanitize_text_field($_POST['active']),
                        'type'          => sanitize_text_field($_POST['type']),
                        'tax'          => sanitize_text_field($_POST['tax']),
                        'method'        => sanitize_text_field($_POST['method']),
                        'target'        => sanitize_text_field($_POST['target']),
                        'scope'         => sanitize_text_field($_POST['scope']),
                        'value'         => floatval($_POST['value']),
                        'expirationDateStatus'  => intval($expirationDateStatus),
                        'expirationDateFrom'    => intval($expirationDateFrom),
                        'expirationDateTo'      => intval($expirationDateTo),
                    ),
                    array('key' => intval($_POST['key'])),
                    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%d', '%d'),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            
            return $this->getTaxes($accountKey);
            
        }
        
        public function deleteTaxes($accountKey){
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_taxes";
            $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
            return $this->getTaxes($accountKey);
            
        }
        
        public function changeTaxesRank($accountKey){
            
            $keyList = explode(",", $_POST['keyList']);
            $indexList = explode(",", $_POST['indexList']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_taxes";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				for ($i = 0; $i < count($keyList); $i++) {
                    
                    $wpdb->update(
                        $table_name,
                        array('ranking' => intval($indexList[$i])),
                        array('key' => intval($keyList[$i])),
                        array('%d'),
                        array('%d')
                    );
                    
                }
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            
            return $this->getTaxes($accountKey);
            
        }
        
        public function getOptionsForHotel($accountKey, $active = false, $booking = false) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_hotel_options";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            if ($active === true) {
                
                $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d AND `active` = 'true' ORDER BY ranking ASC;", array(intval($accountKey)));
                
            }
            $rows = $wpdb->get_results($sql, ARRAY_A);
            $isExtensionsValid = $this->getExtensionsValid();
            if ($isExtensionsValid === false) {
				
				return array();
				
			} else {
                
                if ($booking === true) {
                    
                    foreach ((array) $rows as $key => $value) {
                        
                        $list = json_decode($value['json'], true);
                        array_unshift($list, array("adult" => 0, "child" => 0, "room" => 0, "name" => __("Select", 'booking-package')));
                        for ($i = 0; $i < count($list); $i++) {
                            
                            $list[$i]['index'] = $i;
                            
                        }
                        $value['json'] = json_encode($list);
                        $rows[$key] = $value;
                        
                    }
                    
                }
                
                return $rows;

			}
            
        }
        
        public function addOptionsForHotel($accountKey) {
            
            if (!isset($_POST['active']) || strlen($_POST['active']) == 0) {
                
                $_POST['active'] = "false";
                
            }
            
            $required = 0;
            if ($_POST['required'] == 'true') {
                
                $required = 1;
                
            }
            
            $json = array();
            if (isset($_POST['json'])) {
                
                $jsonList = json_decode(stripslashes($_POST['json']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    foreach ((array) $jsonList[$i] as $key => $value) {
                        
                        if ($key == 'adult' || $key == 'child' || $key == 'room') {
                            
                            if (is_int($value) === false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                
                            }
                            
                        }
                        
                        $object[sanitize_text_field($key)] = sanitize_text_field($value);
                        
                    }
                    array_push($json, $object);
                    
                }
                
            }
            
            if ($this->getExtensionsValid() === false) {
                
                $_POST['active'] = "false";
                $json = array();
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_hotel_options";
            $wpdb->insert(
                $table_name, 
                array(
                    'accountKey'            => intval($accountKey), 
                    'name'                  => sanitize_text_field($_POST['name']),  
                    'ranking'               => intval($_POST['rank']),
                    'active'                => sanitize_text_field($_POST['active']),
                    'required'              => intval($required),
                    'target'                => sanitize_text_field($_POST['target']),
                    'range'                => sanitize_text_field($_POST['range']),
                    'chargeForAdults'       => floatval(0),
                    'chargeForChildren'     => floatval(0),
                    'chargeForRoom'         => floatval(0),
                    "description"           => sanitize_textarea_field($_POST["description"]),
                    'json'               => json_encode($json),
                ), 
                array('%d', '%s', '%d', '%s', '%d', '%s', '%s', '%f', '%f', '%f', '%s', '%s')
            );
            
            return $this->getOptionsForHotel($accountKey);
            
        }
        
        public function updateOptionsForHotel($accountKey) {
            
            if (!isset($_POST['active']) || strlen($_POST['active']) == 0) {
                
                $_POST['active'] = "false";
                
            }
            
            $required = 0;
            if ($_POST['required'] == 'true') {
                
                $required = 1;
                
            }
            
            $json = array();
            if (isset($_POST['json'])) {
                
                $jsonList = json_decode(stripslashes($_POST['json']), true);
                for ($i = 0; $i < count($jsonList); $i++) {
                    
                    $object = array();
                    foreach ((array) $jsonList[$i] as $key => $value) {
                        
                        if ($key == 'adult' || $key == 'child' || $key == 'room') {
                            
                            if (is_int($value) === false) {
                                
                                $value = $this->getOnlyNumbers($value);
                                
                            }
                            
                        }
                        
                        $object[sanitize_text_field($key)] = sanitize_text_field($value);
                        
                    }
                    array_push($json, $object);
                    
                }
                
                
                
            }
            
            if ($this->getExtensionsValid() === false) {
                
                $_POST['active'] = "false";
                $json = array();
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_hotel_options";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'name'                  => sanitize_text_field($_POST['name']),  
                        'active'                => sanitize_text_field($_POST['active']),
                        'required'              => intval($required),
                        'range'                 => sanitize_text_field($_POST['range']),
                        'chargeForAdults'       => floatval(0),
                        'chargeForChildren'     => floatval(0),
                        'chargeForRoom'         => floatval(0),
                        "description"           => sanitize_textarea_field($_POST["description"]),
                        'json'                 => json_encode($json),
                    ),
                    array('key' => intval($_POST['key'])),
                    array('%s', '%s', '%d', '%s', '%f', '%f', '%f', '%s', '%s'),
                    array('%d')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            
            return $this->getOptionsForHotel($accountKey);
            
        }
        
        public function deleteOptionsForHotel($accountKey){
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_hotel_options";
            $wpdb->delete($table_name, array('key' => intval($_POST['key'])), array('%d'));
            return $this->getOptionsForHotel($accountKey);
            
        }
        
        public function changeOptionsForHotelRank($accountKey){
            
            $keyList = explode(",", $_POST['keyList']);
            $indexList = explode(",", $_POST['indexList']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_hotel_options";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				for ($i = 0; $i < count($keyList); $i++) {
                    
                    $wpdb->update(
                        $table_name,
                        array('ranking' => intval($indexList[$i])),
                        array('key' => intval($keyList[$i])),
                        array('%d'),
                        array('%d')
                    );
                    
                }
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getOptionsForHotel($accountKey);
            
        }
        
        public function changeCourseRank(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            $keyList = explode(",", $_POST['keyList']);
            $indexList = explode(",", $_POST['indexList']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_services";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				for ($i = 0; $i < count($keyList); $i++) {
                    
                    $wpdb->update(
                        $table_name,
                        array('ranking' => intval($indexList[$i])),
                        array('key' => intval($keyList[$i])),
                        array('%d'),
                        array('%d')
                    );
                    
                }
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d ORDER BY ranking ASC;", array(intval($accountKey)));
            $rows = $wpdb->get_results($sql, ARRAY_A);
            return $rows;
            
        }
        
        public function addForm(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            if (isset($_POST['isSMS']) === false) {
                
                $_POST['isSMS'] = 'false';
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_form";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d;", array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                
                return array('status' => 'error');
                
            } else {
                
                $id = strtolower($_POST['id']);
                $id = preg_replace('/[^0-9a-zA-Z]/', '', $id);
                $id  = preg_replace("/^( )|(　)$/", "", $id );
                #$options = str_replace("\\\"", "\"", sanitize_text_field($_POST['options']));
                #$options = str_replace("\'", "'", $options);
                $options = stripslashes($_POST['options']);
                $options = sanitize_text_field($options);
                $options = json_decode($options, true);
                if (is_null($options) || is_bool($options) === true) {
                    
                    $options = array();
                    
                }
                
                foreach ($options as $key => $value) {
                    
                    if (is_null($value) || empty($value) || $value == 'null') {
                        
                        unset($options[$key]);
                        
                    }
                    
                }
                
                $options = array_values($options);
                
                $data = json_decode($row['data'], true);
                foreach ((array) $data as $key => $value) {
                    
                    if ($value['id'] == $id) {
                        
                        return array("status" => "error", "message" => "An ID with the same name already exists in the form.");
                        
                    }
                    
                }
                
                $item = array(
                    "id"                => $id, 
                    "name"              => sanitize_text_field($_POST["name"]), 
                    "description"       => sanitize_textarea_field($_POST["description"]),
                    "value"             => "", 
                    "uri"               => sanitize_textarea_field($_POST["uri"]),
                    "type"              => sanitize_text_field($_POST["type"]), 
                    "active"            => sanitize_text_field($_POST["active"]), 
                    "options"           => $options, 
                    "required"          => sanitize_text_field($_POST["required"]), 
                    "isName"            => sanitize_text_field($_POST["isName"]),
                    "isEmail"           => sanitize_text_field($_POST["isEmail"]),
                    "isSMS"           => sanitize_text_field($_POST["isSMS"]),
                    "isAddress"         => sanitize_text_field($_POST["isAddress"]),
                    "isTerms"           => sanitize_text_field($_POST["isTerms"]),
                    "isAutocomplete"    => sanitize_text_field($_POST["isAutocomplete"]),
                    "placeholder"        => sanitize_text_field($_POST["placeholder"]),
                );
                
                if (isset($_POST['targetCustomers'])) {
                    
                    $item['targetCustomers'] = sanitize_text_field($_POST["targetCustomers"]);
                    
                }
                
                array_push($data, $item);
                $json = json_encode($data);
                if (defined('JSON_NUMERIC_CHECK')) {
                    
                    $json = json_encode($data, JSON_NUMERIC_CHECK);
                    
                }
                
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$wpdb->update(  
                        $table_name,
                        array('data' => $json),
                        array('key' => intval($row['key'])),
                        array('%s'),
                        array('%d')
                    );
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
                
                #return $data;
                return $this->getForm($accountKey, false);
                
            }
            
        }
        
        public function updateForm(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            if (isset($_POST['isSMS']) === false) {
                
                $_POST['isSMS'] = 'false';
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_form";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d;", array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                
                return array('status' => 'error');
                
            } else {
                
                $id = strtolower($_POST['id']);
                $id = preg_replace('/[^0-9a-zA-Z]/', '', $id);
                $id  = preg_replace("/^( )|(　)$/", "", $id );
                $input = array();
                #$options = str_replace("\\\"", "\"", sanitize_text_field($_POST['options']));
                #$options = str_replace("\'", "'", $options);
                $options = stripslashes($_POST['options']);
                $options = sanitize_text_field($options);
                $options = json_decode($options, true);
                if (is_null($options) || is_bool($options) === true) {
                    
                    $options = array();
                    
                }
                
                foreach ($options as $key => $value) {
                    
                    if (is_null($value) || empty($value) || $value == 'null') {
                        
                        unset($options[$key]);
                        
                    }
                    
                }
                
                $options = array_values($options);
                
                $data = json_decode($row['data']);
                #for($i = 0; $i < count($data); $i++){
                foreach ((array) $data as $i => $value) {
                    
                    if (intval($i) == intval($_POST['key']) && $value->id == $id) {
                        
                        $value->name                = sanitize_text_field($_POST['name']);
                        $value->description         = sanitize_textarea_field($_POST['description']);
                        $value->uri                 = sanitize_text_field($_POST['uri']);
                        $value->active              = sanitize_text_field($_POST['active']);
                        $value->type                = sanitize_text_field($_POST['type']);
                        $value->options             = $options;
                        $value->required            = sanitize_text_field($_POST['required']);
                        $value->isName              = sanitize_text_field($_POST['isName']);
                        $value->isAddress           = sanitize_text_field($_POST['isAddress']);
                        $value->isEmail             = sanitize_text_field($_POST['isEmail']);
                        $value->isSMS               = sanitize_text_field($_POST['isSMS']);
                        $value->isTerms             = sanitize_text_field($_POST['isTerms']);
                        $value->isAutocomplete      = sanitize_text_field($_POST['isAutocomplete']);
                        $value->placeholder         = sanitize_text_field($_POST["placeholder"]);
                        if (isset($_POST['targetCustomers'])) {
                            
                            $value->targetCustomers = sanitize_text_field($_POST['targetCustomers']);
                            
                        }
                        #break;
                        
                    }
                    
                    array_push($input, $value);
                    
                }
                
                $json = json_encode($input);
                if (defined('JSON_NUMERIC_CHECK')) {
                    
                    $json = json_encode($input, JSON_NUMERIC_CHECK);
                    
                }
                
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$wpdb->update(  
                        $table_name,
                        array('data' => $json),
                        array('key' => intval($row['key'])),
                        array('%s'),
                        array('%d')
                    );
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
                return $this->getForm($accountKey, false);
                
            }
            
            
        }
        
        public function deleteFormItem(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            global $wpdb;
            $table_name = $wpdb->prefix."booking_package_form";
            $sql = $wpdb->prepare("SELECT * FROM ".$table_name." WHERE `accountKey` = %d;", array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                
                return array('status' => 'error');
                
            } else {
                
                $data = json_decode($row['data']);
                array_splice($data, intval($_POST['key']), 1);
                $json = json_encode($data);
                if (defined('JSON_NUMERIC_CHECK')) {
                    
                    $json = json_encode($data, JSON_NUMERIC_CHECK);
                    
                }
                
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$wpdb->update(  
                        $table_name,
                        array('data' => $json),
                        array('key' => intval($row['key'])),
                        array('%s'),
                        array('%d')
                    );
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
                return $this->getForm($accountKey, false);
                
            }
            
        }
        
        public function changeFormRank(){
            
            $accountKey = 1;
            if (isset($_POST['accountKey'])) {
                
                $accountKey = $_POST['accountKey'];
                
            }
            
            $keyList = explode(",", $_POST['keyList']);
            $indexList = explode(",", $_POST['indexList']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_form";
            $sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE `accountKey` = %d;", array(intval($accountKey)));
            $row = $wpdb->get_row($sql, ARRAY_A);
            if (is_null($row)) {
                
                return array('status' => 'error');
                
            } else {
                
                $newData = array();
                $data = json_decode($row['data']);
                foreach ((array) $data as $key => $value) {
                    
                    $search = array_search($value->name, $keyList);
                    $index = intval($indexList[$search]);
                    $newData[$index] = $value;
                    
                }
                
                ksort($newData);
                $json = json_encode($newData);
                if (defined('JSON_NUMERIC_CHECK')) {
                    
                    $json = json_encode($newData, JSON_NUMERIC_CHECK);
                    
                }
                
                try {
					
					$wpdb->query("START TRANSACTION");
					$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
					$wpdb->update(
                        $table_name,
                        array('data' => $json),
                        array('key' => intval($row['key'])),
                        array('%s'),
                        array('%d')
                    );
					
					$wpdb->query('COMMIT');
					$wpdb->query('UNLOCK TABLES');
					
				} catch (Exception $e) {
					
					$wpdb->query('ROLLBACK');
					$wpdb->query('UNLOCK TABLES');
					
				}/** finally {
					
					$wpdb->query('UNLOCK TABLES');
					
				}**/
                
                
                return $newData;
                        
            }
            
            
        }
        
        public function getCss($fileName, $plugin_dir_path) {
            
            if (get_option('_' . $this->pluginName . '_css_v') === false) {
                
                add_option('_' . $this->pluginName . '_css_v', date('U'));
                
            }
            
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['basedir'] . '/' . $this->pluginName;
            /**
            if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
                
                $id = get_current_blog_id();
                $dirname .= '/' . $id;
                
            }
            **/
            $css = "";
            if (!file_exists($dirname)) {
            	
            	#wp_mkdir_p($dirname);
            	if (wp_mkdir_p($dirname) === true) {
            	    
            	    $css = file_get_contents($plugin_dir_path . 'css/front_end.css');
            	    file_put_contents($dirname . '/' . $fileName, $css);
            	    
            	} else {
            	    
            	    $css = "There is a problem with directory permissions on wp-content or wp-content/uploads.";
            	    
            	}
            	
            } else {
                
                if (file_exists($dirname . '/' . $fileName)) {
                    
                    $css = file_get_contents($dirname . '/' . $fileName);
                    
                } else {
                    
                    if (wp_mkdir_p($dirname) === true) {
                        
                        $css = file_get_contents($plugin_dir_path . 'css/front_end.css');
            	        file_put_contents($dirname . '/' . $fileName, $css);
                        
                    } else {
                        
                        $css = "There is a problem with directory permissions on wp-content or wp-content/uploads.";
            	        
                    }
                    
                }
                
                
            }
            
            return $css;
            
        }
        
        public function getCssUrl($fileName) {
            
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['baseurl'] . '/' . $this->pluginName;
            $parseUrl = parse_url($dirname);
            if ($parseUrl['scheme'] == 'https') {
                
                $dirname = str_replace('https://', '//', $dirname);
                
            } else if ($parseUrl['scheme'] == 'http') {
                
                $dirname = str_replace('http://', '//', $dirname);
                
            }
            
            /**
            if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
                
                $id = get_current_blog_id();
                $dirname .= '/' . $id . '/' . $fileName;
                
            } else {
                
                $dirname .= '/' . $fileName;
                
            }
            **/
            $dirname .= '/' . $fileName;
            return array('dirname' => $dirname, 'v' => get_option('_' . $this->pluginName . '_css_v'));
            
        }
        
        public function updateCss($fileName) {
            
            update_option('_' . $this->pluginName . '_css_v', date('U'));
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['basedir'] . '/' . $this->pluginName;
            
            /**
            if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
                
                $id = get_current_blog_id();
                $dirname .= '/' . $id;
                
            }
            **/
            #$value = str_replace("\\\"", "\"", $_POST['value']);
            #$value = str_replace("\'", "'", $value);
            $value = stripslashes($_POST['value']);
            file_put_contents($dirname . '/' . $fileName, $value);
            return array("status" => "success");
            
        }
        
        public function getJavaScript($fileName, $plugin_dir_path) {
            
            if (get_option('_' . $this->pluginName . '_javascript_v') === false) {
                
                add_option('_' . $this->pluginName . '_javascript_v', date('U'));
                
            }
            
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['basedir'] . '/' . $this->pluginName;
            $javascript = "";
            if (!file_exists($dirname)) {
            	
            	if (wp_mkdir_p($dirname) === true) {
            	    
            	    $javascript = file_get_contents($plugin_dir_path . 'js/front_end.js');
            	    file_put_contents($dirname . '/' . $fileName, $javascript);
            	    
            	} else {
            	    
            	    $javascript = "//There is a problem with directory permissions on wp-content or wp-content/uploads.";
            	    
            	}
            	
            } else {
                
                if (file_exists($dirname . '/' . $fileName)) {
                    
                    $javascript = file_get_contents($dirname . '/' . $fileName);
                    
                } else {
                    
                    if (wp_mkdir_p($dirname) === true) {
                        
                        $javascript = file_get_contents($plugin_dir_path . 'js/front_end.js');
                        file_put_contents($dirname . '/' . $fileName, $javascript);
                        
                    } else {
                        
                        $javascript = "//There is a problem with directory permissions on wp-content or wp-content/uploads.";
                        
                    }
                    
                }
                
            }
            
            return $javascript;
            
        }
        
        public function getJavaScriptUrl($fileName) {
            
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['baseurl'] . '/' . $this->pluginName;
            $parseUrl = parse_url($dirname);
            if ($parseUrl['scheme'] == 'https') {
                
                $dirname = str_replace('https://', '//', $dirname);
                
            } else if ($parseUrl['scheme'] == 'http') {
                
                $dirname = str_replace('http://', '//', $dirname);
                
            }
            $dirname .= '/' . $fileName;
            return array('dirname' => $dirname, 'v' => get_option('_' . $this->pluginName . '_javascript_v'));
            
        }
        
        public function updateJavaScript($fileName) {
            
            update_option('_' . $this->pluginName . '_javascript_v', date('U'));
            $upload_dir = wp_upload_dir();
            $dirname = $upload_dir['basedir'] . '/' . $this->pluginName;
            $value = $_POST['value'];
            #$value = str_replace("\\\\", "\\", $value);
            #$value = str_replace("\\\"", "\"", $value);
            #$value = str_replace("\'", "'", $value);
            $value = stripslashes($value);
            file_put_contents($dirname . '/' . $fileName, $value);
            return array("status" => "success", 'value' => $value);
            
        }
        
        public function updataEmailMessageForCalendarAccount(){
            
            $accountKey = intval($_POST['accountKey']);
            $mail_id = sanitize_text_field($_POST['mail_id']);
            
            $subject = sanitize_text_field($_POST['subject']);
            $content = sanitize_textarea_field(htmlspecialchars($_POST['content'], ENT_QUOTES|ENT_HTML5));
            
            $subjectForAdmin = sanitize_text_field($_POST['subjectForAdmin']);
            $contentForAdmin = sanitize_textarea_field(htmlspecialchars($_POST['contentForAdmin'], ENT_QUOTES|ENT_HTML5));
            
            $subjectForIcalendar = sanitize_text_field($_POST['subjectForIcalendar']);
            $locationForIcalendar = sanitize_text_field($_POST['locationForIcalendar']);
            $contentForIcalendar = sanitize_textarea_field(htmlspecialchars($_POST['contentForIcalendar'], ENT_QUOTES|ENT_HTML5));
            
            $enable = intval($_POST['enableEmail']);
            $enableSMS = intval($_POST['enableSms']);
            $format = sanitize_text_field($_POST['format']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . "booking_package_email_settings";
            try {
				
				$wpdb->query("START TRANSACTION");
				$wpdb->query("LOCK TABLES `" . $table_name . "` WRITE");
				$wpdb->update(
                    $table_name,
                    array(
                        'subject' => $subject, 
                        'content' => $content, 
                        'subjectForAdmin' => $subjectForAdmin, 
                        'contentForAdmin' => $contentForAdmin, 
                        'enable' => $enable, 
                        'enableSMS' => $enableSMS, 
                        'format' => $format,
                        'subjectForIcalendar' => $subjectForIcalendar,
                        'locationForIcalendar' => $locationForIcalendar,
                        'contentForIcalendar' => $contentForIcalendar,
                        'attachICalendar' => intval($_POST['attachICalendar']),
                        'notifyAdministrator' => intval($_POST['notifyAdministrator']),
                    ),
                    array('accountKey' => $accountKey, 'mail_id' => $mail_id),
                    array('%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d'),
                    array('%d', '%s')
                );
				
				$wpdb->query('COMMIT');
				$wpdb->query('UNLOCK TABLES');
				
			} catch (Exception $e) {
				
				$wpdb->query('ROLLBACK');
				$wpdb->query('UNLOCK TABLES');
				
			}/** finally {
				
				$wpdb->query('UNLOCK TABLES');
				
			}**/
            
            return $this->getEmailMessageList($accountKey);
            
        }
        
        public function lookingForSubscription($customer_id, $subscription_id) {
            
            global $wpdb;
            $url = BOOKING_PACKAGE_EXTENSION_URL;
            
            $license = array(
                'status' => 0,
                'customer_id' => trim(esc_html($customer_id)),
                'subscription_id' => trim(esc_html($subscription_id)),
                'url' => get_site_url(),
                'expiration_date' => 0,
            );
            
            $args = array(
                'method' => 'POST',
                'body' => array(
                    'mode' => 'error', 
                    'subscription_mode' => 'lookingForSubscription',
                    'customer_id_for_subscriptions' => $license['customer_id'],
                    'subscriptions_id_for_subscriptions' => $license['subscription_id'],
                    'url' => $license['url'],
                )
            );
            $response = wp_remote_request($url . "lookingForSubscription/", $args);
            $statusCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            $response['url'] = $url;
            if (intval($statusCode) == 200 && intval($response['status']) == 1) {
                
                unset($response['status']);
                $license['status'] = 1;
                $this->updateSubscribeSite();
                foreach ((array) $response as $key => $value) {
                    
                    $value = sanitize_text_field($value);
                    if ( $key === 'expiration_date_for_subscriptions' ) {
                        
                        $license['expiration_date'] = intval($value);
                        if ( is_numeric($value) === true ) {
                            
                            $value = $this->encryptValue($value);
                            
                        }
                        
                    }
                    
                    if (get_option('_' . $this->prefix . $key) !== false) {
                        
                        update_option('_' . $this->prefix . $key, $value);
                        
                    } else {
                        
                        $bool = add_option('_' . $this->prefix . $key, $value);
                        
                    }
                    
                }
                $this->updateSubscriptionStatus('Active');
                
                $numberOfVerificationAttemptsForExpiration = get_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', null);
                if ( is_null($numberOfVerificationAttemptsForExpiration) ) {
                    
                    add_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', 0, '', 'no');
                    
                } else {
                    
                    update_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', 0);
                    
                }
                
            } else {
                
                $license['errorCode'] = esc_html($response['errorCode']);
                $license['errorMessage'] = esc_html($response['errorMessage']);
                update_option('_' . $this->prefix . 'expiration_date_for_subscriptions', 0);
                
            }
            
            return $license;
            
        }
        
        public function upgradePlan($type) {
            
            $response = array('status' => 'error');
            if ($type === 'get') {
                
                $values = array(
                    'customer_id_for_subscriptions', 
                    'id_for_subscriptions', 
                    'customer_email_for_subscriptions', 
                    'invoice_id_for_subscriptions', 
                    'expiration_date_for_subscriptions'
                );
                 
                for ($i = 0; $i < count($values); $i++) {
                    
                    $key = $values[$i];
                    $value = get_option('_' . $this->prefix . $key, 0);
                    
                    if (get_option($this->prefix . $key) !== false) {
                        
                        $value = get_option($this->prefix . $key);
                        delete_option($this->prefix . $key);
                        add_option('_' . $this->prefix . $key, $value);
                        
                    }
                    
                    $response[$key] = $value;
                    
                }
                
                $expiration = $response['expiration_date_for_subscriptions'];
                if ( is_numeric($expiration) === false ) {
                    
                    $expiration = $this->decryptValue($expiration);
                    if ($expiration === false) {
                        
                        $expiration = 0;
                        
                    }
                    
                    if (intval($expiration) === 0) {
                        
                        $expiration = 0;
                        
                    }
                    
                } else {
                    
                    if (intval($expiration) > 0) {
                        
                        $subscriptionDetails = $this->lookingForSubscription($response['customer_id_for_subscriptions'], $response['id_for_subscriptions']);
                        $expiration = $subscriptionDetails['expiration_date'];
                        if (intval($subscriptionDetails['status']) === 1) {
                            
                            update_option('_' . $this->prefix . 'expiration_date_for_subscriptions', $this->encryptValue($expiration));
                            $this->updateSubscriptionStatus('Active');
                            
                        } else {
                            
                            update_option('_' . $this->prefix . 'expiration_date_for_subscriptions', 0);
                             $this->updateSubscriptionStatus('Inactive');
                            
                        }
                        
                    } else {
                        
                        #$this->updateSubscriptionStatus('Inactive');
                        
                    }
                    
                }
                
                $response['expiration_date_for_subscriptions'] = intval($expiration);
                $response['status'] = 'success';
                
            } else if ($type === 'delete') {
                
                $params = array(
                    'mode' => 'error', 
                    'subscription_mode' => 'cancelSubscription',
                    'customer_id' => $_POST['customer_id'],
                    'subscriptions_id' => trim($_POST['subscriptions_id']),
                );
                
                $args = array(
                    'method' => 'POST',
                    'body' => $params
                );
                $response = wp_remote_request(BOOKING_PACKAGE_EXTENSION_URL . "cancelSubscription/", $args);
                $statusCode = wp_remote_retrieve_response_code($response);
                $response = json_decode(wp_remote_retrieve_body($response), true);
                $response['url'] = BOOKING_PACKAGE_EXTENSION_URL;
                if ($statusCode == 200 && $response['status'] == 'success') {
                    
                    $this->updateSubscriptionStatus('Canceled');
                    $response['status'] = 'success';
                    
                }
                
            }
            
            return $response;
            
        }
        
        public function resetSubscription() {
            
            $this->deleteSubscriptionData();
            return array('status' => true);
            
        }
        
        private function deleteSubscriptionData() {
            
            $values = array('customer_id_for_subscriptions', 'id_for_subscriptions', 'customer_email_for_subscriptions', 'invoice_id_for_subscriptions', 'expiration_date_for_subscriptions');
            for ($i = 0; $i < count($values); $i++) {
                
                $key = $values[$i];
                if (get_option('_' . $this->prefix . $key) === false) {
                    
                    add_option('_' . $this->prefix . $key, 0);
                    
                } else {
                    
                    update_option('_' . $this->prefix . $key, 0);
                    
                }
                
            }
            
        }
        
        public function getSubscriptionStatus() {
            
            return get_option('_' . $this->prefix . 'subscription_status', 'Not subscribed');
            
        }
        
        private function updateSubscriptionStatus($status) {
            
            if (get_option('_' . $this->prefix . 'subscription_status') === false) {
                
                add_option('_' . $this->prefix . 'subscription_status', sanitize_text_field($status));
                
            } else {
                
                update_option('_' . $this->prefix . 'subscription_status', sanitize_text_field($status));
                
            }
            
        }
        
        private function getExtensionsValid() {
			
			if (is_null($this->isExtensionsValid)) {
				
				$this->isExtensionsValid = $this->getSiteStatus();
				
			}
			
			return $this->isExtensionsValid;
			
		}
		
		public function setSubscribeSite($scheme = false) {
            
            $unique = get_site_url();
            if ($scheme === true) {
                
                $unique = wp_hash( preg_replace('#^[^:]+://#', '', $unique) );
                if (get_option('_' . $this->prefix . 'unique_without_scheme') === false) {
                    
                    add_option('_' . $this->prefix . 'unique_without_scheme', sanitize_text_field($unique));
                    
                } else {
                    
                    #update_option('_' . $this->prefix . 'unique_without_scheme', sanitize_text_field($unique));
                    
                }
                
            } else {
                
                $unique = wp_hash($unique);
                if (get_option('_' . $this->prefix . 'unique') === false) {
                    
                    add_option('_' . $this->prefix . 'unique', sanitize_text_field($unique));
                    
                } else {
                    
                    #update_option('_' . $this->prefix . 'unique', sanitize_text_field($unique));
                    
                }
                
            }
            
            
            return $unique;
		    
		}
		
		public function setOpensslAlgo($algo) {
		    
		    if (get_option('_' . $this->prefix . 'openssl_algo') === false) {
                
                add_option('_' . $this->prefix . 'openssl_algo', sanitize_text_field($algo));
                
            }
		    
		}
		
		public function updateSubscribeSite() {
            
            $site = get_site_url();
            $unique_without_scheme = wp_hash( preg_replace('#^[^:]+://#', '', $site) );
            update_option('_' . $this->prefix . 'unique_without_scheme', sanitize_text_field($unique_without_scheme) );
            $unique = wp_hash($site);
            update_option('_' . $this->prefix . 'unique', sanitize_text_field($unique) );
            
		}
		
		public function setPaidSubscription($mode) {
            
            $subscriptions = $this->upgradePlan("get");
			$isExtensionsValid = $this->getSiteStatus();
			if ($isExtensionsValid == true) {
                
                if ($mode == 'activation') {
                    
                    $this->lookingForSubscription($subscriptions['customer_id_for_subscriptions'], $subscriptions['id_for_subscriptions']);
                    
                } else if ($mode == 'deactivation') {
                    
                    $statusCode = $this->cancelPaidSubscriptionAtPeriodEnd($subscriptions['customer_id_for_subscriptions'], $subscriptions['id_for_subscriptions']);
                    
                }
			    
			}
			
            return $isExtensionsValid;
            
        }
        
		public function updatePaidSubscription($unique) {
		    
		    $site = get_site_url();
			if ($unique !== false && wp_hash($site) === $unique) {
				
				$subscriptions = $this->upgradePlan('get');
			    $response = $this->lookingForSubscription($subscriptions['customer_id_for_subscriptions'], $subscriptions['id_for_subscriptions']);
                return $response;
                
			}
			
			return false;
		    
		}
		
        public function validateWpSite($validate = true) {
            
            if ($validate === false) {
                
                return true;
                
            }
            
            $site = get_site_url();
            $unique = get_option('_' . $this->prefix . 'unique', false);
            if (empty($unique)) {
                
                $unique = $this->setSubscribeSite(false);
                
            }
            
            $site_without_scheme = preg_replace('#^[^:]+://#', '', $site);
            $unique_without_scheme = get_option('_' . $this->prefix . 'unique_without_scheme', false);
            if (empty($unique_without_scheme)) {
                
                $unique_without_scheme = $this->setSubscribeSite(true);
                
            }
            
            if (wp_hash( $site_without_scheme ) !== $unique_without_scheme) {
                
                $this->updateSubscribeSite();
                $expiration = get_option('_' . $this->prefix . 'expiration_date_for_subscriptions', 0);
                if ( is_numeric($expiration) === true ) {
                    
                    $value = $this->encryptValue($expiration);
                    if (get_option('_' . $this->prefix . 'expiration_date_for_subscriptions') === false) {
                        
                        add_option('_' . $this->prefix . 'expiration_date_for_subscriptions', $value);
                        
                    } else {
                        
                        update_option('_' . $this->prefix . 'expiration_date_for_subscriptions', $value);
                        
                    }
                    
                } else {
                    
                    $expiration = $this->decryptValue($expiration);
                    
                }
                
                if (intval($expiration) > 0) {
                    
                    /**
                    $subscriptions = $this->upgradePlan("get");
                    $statusCode = $this->cancelPaidSubscriptionAtPeriodEnd($subscriptions['customer_id_for_subscriptions'], $subscriptions['id_for_subscriptions']);
                    
                    if (intval($statusCode) == 200) {
                        
                        update_option('_' . $this->prefix . "expiration_date_for_subscriptions", sanitize_text_field('0'));
                        
                    }
                    **/
                    
                    update_option('_' . $this->prefix . "expiration_date_for_subscriptions", sanitize_text_field('0'));
                    return false;
                    
                }
                
            }
            
            
            return true;
            
        }
        
        public function getSiteStatus($countExpiration = false) {
            
            $url = BOOKING_PACKAGE_EXTENSION_URL;
            $response = array('status' => 'error');
            $subscriptions = $this->upgradePlan('get');
            $expiration_date = intval($subscriptions['expiration_date_for_subscriptions']);
            if ( intval($subscriptions['expiration_date_for_subscriptions']) == 0 && $countExpiration === false ) {
                
                if ($this->getSubscriptionStatus() === 'Canceled') {
                    
                    $this->deleteSubscriptionData();
                    $this->updateSubscriptionStatus('Not subscribed');
                    
                }
                
                return false;
                
            }
            
            $numberOfVerificationAttemptsForExpiration = get_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', null);
            if ( is_null($numberOfVerificationAttemptsForExpiration) ) {
                
                $numberOfVerificationAttemptsForExpiration = 0;
                add_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', $numberOfVerificationAttemptsForExpiration, '', 'no');
                
            }
            
            if (
                $countExpiration === true && 
                intval($subscriptions['expiration_date_for_subscriptions']) == 0 && 
                intval($numberOfVerificationAttemptsForExpiration) < 3 && 
                empty($subscriptions['customer_id_for_subscriptions']) === false && 
                empty($subscriptions['id_for_subscriptions']) === false
            ) {
                
                $expiration_date = date('U') - 1440;
                
            } else if (
                $countExpiration === true && 
                (
                    intval($numberOfVerificationAttemptsForExpiration) >= 3 || 
                    empty($subscriptions['customer_id_for_subscriptions']) || 
                    empty($subscriptions['id_for_subscriptions']))
                ) 
            {
                
                $bool = update_option('_' . $this->prefix . "expiration_date_for_subscriptions", 0);
                return false;
                
            }
            
            if ($expiration_date < date('U')) {
                
                if ($this->getSubscriptionStatus() === 'Canceled') {
                    
                    $this->deleteSubscriptionData();
                    $this->updateSubscriptionStatus('Not subscribed');
                    return false;
                    
                }
                
                $params = array(
                    "subscription_mode" => "updateLicense",
                    "customer_id" => $subscriptions['customer_id_for_subscriptions'], 
                    "subscriptions_id" => $subscriptions['id_for_subscriptions'],
                    "site" => get_site_url(),
                );
                
                $args = array(
                    'method' => 'POST',
                    'body' => $params
                );
                $response = wp_remote_request($url . "updateLicense/", $args);
                $statusCode = wp_remote_retrieve_response_code($response);
                $response = json_decode(wp_remote_retrieve_body($response));
                
                $tmp_path = sys_get_temp_dir();
                if (intval($statusCode) == 200) {
                    
                    if (intval($response->status) == 1) {
                        
                        $expiration_date = $this->encryptValue( sanitize_text_field($response->expiration_date) );
                        update_option('_' . $this->prefix . "invoice_id_for_subscriptions", sanitize_text_field($response->invoice_id));
                        $bool = update_option('_' . $this->prefix . "expiration_date_for_subscriptions", $expiration_date);
                        $subscriptions = $this->upgradePlan('get');
                        if ($countExpiration === true) {
                            
                            update_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', 0);
                            
                        }
                        
                    } else if (intval($response->status) == 0) {
                        
                        $bool = update_option('_' . $this->prefix . "expiration_date_for_subscriptions", sanitize_text_field('0'));
                        $this->updateSubscriptionStatus('Inactive');
                        if ($countExpiration === true) {
                            
                            $numberOfVerificationAttemptsForExpiration++;
                            update_option('_' . $this->prefix . 'numberOfVerificationAttemptsForExpiration', $numberOfVerificationAttemptsForExpiration);
                            
                        }
                        
                        return false;
                        
                    }
                    
                }
                
            }
            
            if ($this->getSubscriptionStatus() !== 'Canceled') {
                
                $this->updateSubscriptionStatus('Active');
                
            }
            return true;
            
        }
        
        private function encryptValue($value) {
            
            $unique_without_scheme = get_option('_' . $this->prefix . 'unique_without_scheme', false);
            if ( $unique_without_scheme === false ) {
                
                $unique_without_scheme = $this->setSubscribeSite(true);
                
            }
            
            if (function_exists('openssl_encrypt') === false) {
                
                return $value;
                
            }
            $algo = get_option('_' . $this->prefix . 'openssl_algo', 'AES-128-ECB');
            $encryptedData = base64_encode(openssl_encrypt($value, $algo, $unique_without_scheme, OPENSSL_RAW_DATA));
            return $encryptedData;
            
        }
        
        private function decryptValue($value) {
            
            /**
            $unique = get_option('_' . $this->prefix . 'unique', false);
            if ( $unique === false ) {
                
                $unique = $this->setSubscribeSite(false);
                
            }
            
            $algo = get_option('_' . $this->prefix . 'openssl_algo', 'AES-128-ECB');
            $decryptedData = openssl_decrypt(base64_decode($value), $algo, $unique, OPENSSL_RAW_DATA);
            if ( $decryptedData === false ) {
                
                $unique_without_scheme = get_option('_' . $this->prefix . 'unique_without_scheme', false);
                if ( $unique_without_scheme === false || empty($unique_without_scheme)) {
                    
                    $unique_without_scheme = $this->setSubscribeSite(true);
                    
                }
                
                $decryptedData = openssl_decrypt(base64_decode($value), $algo, $unique_without_scheme, OPENSSL_RAW_DATA);
                if ($decryptedData === false) {
                    
                    return false;
                    
                }
                
            }
            **/
            
            $algo = get_option('_' . $this->prefix . 'openssl_algo', 'AES-128-ECB');
            $unique_without_scheme = get_option('_' . $this->prefix . 'unique_without_scheme', false);
            if ( $unique_without_scheme === false || empty($unique_without_scheme)) {
                
                $unique_without_scheme = $this->setSubscribeSite(true);
                
            }
            
            $decryptedData = openssl_decrypt(base64_decode($value), $algo, $unique_without_scheme, OPENSSL_RAW_DATA);
            if ($decryptedData === false) {
                
                return false;
                
            }
            
            return $decryptedData;
            
        }
        
        public function cancelPaidSubscriptionAtPeriodEnd($customer_id, $subscription_id) {
            
            $params = array(
                "subscription_mode" => "cancelAtPeriodEnd",
                "customer_id" => trim($customer_id), 
                "subscriptions_id" => trim($subscription_id),
            );
            $args = array(
                'method' => 'POST',
                'body' => $params
            );
            $response = wp_remote_request(BOOKING_PACKAGE_EXTENSION_URL . "cancelAtPeriodEnd/", $args);
            $statusCode = wp_remote_retrieve_response_code($response);
            $response = json_decode(wp_remote_retrieve_body($response), true);
            return $statusCode;
            
        }
        
        public function updateChannelGC($calendarAccountList){
            
            $url_parce = parse_url(get_home_url());
            if($url_parce['scheme'] != 'https'){
                
                return false;
                
            }
            
            $keyList = array();
            $calendarIdList = array();
            for($i = 0; $i < count($calendarAccountList); $i++){
                
                if($calendarAccountList[$i]['expirationForGoogleWebhook'] < date('U')){
                    
                    array_push($keyList, $calendarAccountList[$i]['key']);
                    array_push($calendarIdList, $calendarAccountList[$i]['googleCalendarID']);
                    
                }
                
            }
            
            if(count($calendarIdList) == 0){
                
                return null;
                
            }
            
            $calendarIdList = implode(",", $calendarIdList);
            
            $googleCalendar = array();
    		$bookingSync = $this->getBookingSyncList();
    		$bookingSync = $bookingSync['Google_Calendar'];
    		if(intval($bookingSync['booking_package_googleCalendar_active']['value']) == 1){
    		    
    		    if($this->getExtensionsValid(false) === true){
    		        
    		        $expiration_for_google_webhook = get_option($this->prefix."expiration_for_google_webhook", 0);
    		        $expiration_for_google_webhook -= (1440 * 60) * 2;
    		        #$timezone = get_option('timezone_string');
			        date_default_timezone_set("UTC");
			        if(date('U') < $expiration_for_google_webhook){
			            
			            return false;
			            
			        }
    		        
		            $host = $url_parce["host"];
		            $address = get_home_url()."/?webhook=google";
    		        $id = hash('ripemd160', date('U'));
    		        $timezone = get_option('timezone_string');
    		        $subscriptions = $this->upgradePlan('get');
    		        
    				$customer_id = $subscriptions['customer_id_for_subscriptions'];
    				$params = array(
    					'mode' => 'updateChannel',
    					'customer_id' => $customer_id, 
    					'calendarIdList' => $calendarIdList,
    					/**'calendarId' => $bookingSync['booking_package_calendar_id']['value'], **/
    					'service_account' => $bookingSync['booking_package_googleCalendar_json']['value'],
    					'id' => $id,
    					'token' => 'target='.hash('ripemd160', microtime()),
    					'address' => $address,
    					'timeZone' => get_option('timezone_string')
    				);
    				#var_dump($params);
    				if(isset($bookingSync['booking_package_googleCalendar_json'])){
    				    
    				    $params['calendarId'] = $bookingSync['booking_package_calendar_id']['value'];
    				    
    				}
    				
    				$tmp_path = sys_get_temp_dir();
    				
    				$url = BOOKING_PACKAGE_EXTENSION_URL;
    				$ch = curl_init();
                	curl_setopt($ch, CURLOPT_URL, $url."googleCalendar/");
                	curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_COOKIEFILE, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                	curl_setopt($ch, CURLOPT_POST, 1);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                	
                	ob_start();
                	$response = curl_exec($ch);
                	$response = ob_get_contents();
                	ob_end_clean();
                	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                	curl_close ($ch);
                	$response = json_decode($response, true);
                	#var_dump($response);
                	if($response['status'] != 'error'){
                	    
                	    for($i = 0; $i < count($response); $i++){
                    	    
                    	    $response[$i]['key'] = $keyList[$i];
                    	    
                    	}
                    	
                    	if(isset($response['expiration'])){
                    	    
                    	    $response['expiration'] /= 1000;
                    	    $list = array('id' => 'id_for_google_webhook', 'token' => 'token_for_google_webhook', 'expiration' => 'expiration_for_google_webhook');
                    	    foreach ((array) $list as $key => $value) {
                    	        
                    	        $optionKey = sanitize_text_field($this->prefix.$value);
                    	        $optionValue = sanitize_text_field($response[$key]);
                    	        if(get_option($optionKey) === false){
    					            
    	                            add_option($optionKey, $optionValue);
    					        
                                }else{
    				                
    				                update_option($optionKey, $optionValue);
    				                
    			                }
                    	        
                    	    }
                    	    
                    	}
                	    
                	}else{
                	    
                	    $response = array();
                	    
                	}
                	
                	
                	
                	#var_dump($response);
                	
                	return $response;
    		        
    		    }
    		    
    		}
            
        }
        
        public function listsGC($accountKey, $googleCalendarID, $timeMin){
            
            if(strlen($googleCalendarID) == 0){
                
                return array();
                
            }
            
            
            $eventList = array();
    		$bookingSync = $this->getBookingSyncList($accountKey);
    		$bookingSync = $bookingSync['Google_Calendar'];
    		#var_dump($bookingSync);
    		if(intval($bookingSync['booking_package_googleCalendar_active']['value']) == 1){
    			
    			if($this->getExtensionsValid(false) === true){
    			    
    			    $subscriptions = $this->upgradePlan('get');
    				$customer_id = $subscriptions['customer_id_for_subscriptions'];
    				$params = array(
    								'mode' => 'lists',
    								'timeMin' => $timeMin,
    								'customer_id' => $customer_id, 
    								'calendarId' => $googleCalendarID, 
    								'service_account' => $bookingSync['booking_package_googleCalendar_json']['value'],
    								'timeZone' => get_option('timezone_string')
    							);
    			    
    			    #var_dump($params);
    			    $tmp_path = sys_get_temp_dir();
    			    
    			    $url = BOOKING_PACKAGE_EXTENSION_URL;
    				$ch = curl_init();
                	curl_setopt($ch, CURLOPT_URL, $url."googleCalendar/");
                	#curl_setopt($ch, CURLOPT_USERPWD, $subscriptions['customer_id_for_subscriptions'].":");
                	curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_COOKIEFILE, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                	curl_setopt($ch, CURLOPT_POST, 1);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			        
                	ob_start();
                	$response = curl_exec($ch);
                	$response = ob_get_contents();
                	ob_end_clean();
                	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                	curl_close ($ch);
                	$eventList = json_decode($response);
                	if($eventList->status != 'error'){
                		
                	}
                	
                	return $eventList;
    			    
    			}
    			
    		}
            
        }
        
        public function pushGC($mode, $accountKey, $type, $id, $googleCalendarID, $sql_start_unixTime, $sql_end_unixTime, $form, $iCalID = false){
    		
    		if(strlen($googleCalendarID) == 0){
                
                return array();
                
            }
    		
    		$id = intval($id);
    		$googleCalendar = array();
    		$bookingSync = $this->getBookingSyncList($accountKey);
    		$bookingSync = $bookingSync['Google_Calendar'];
    		if(intval($bookingSync['booking_package_googleCalendar_active']['value']) == 1){
    			
    			if($this->getExtensionsValid(false) === true){
    				
    				if(is_null($sql_end_unixTime)){
    					
    					$sql_end_unixTime = $sql_start_unixTime;
    					
    				}
    				$nameList = array();
    				$addressList = array();
    				for($i = 0; $i < count($form); $i++){
    					
    					if($form[$i]->isName == 'true'){
    						
    						array_push($nameList, $form[$i]->value);
    						
    					}
    					
    					if($form[$i]->isAddress == 'true'){
    						
    						array_push($addressList, $form[$i]->value);
    						
    					}
    					
    				}
    				
    				$subscriptions = $this->upgradePlan('get');
    				$customer_id = $subscriptions['customer_id_for_subscriptions'];
    				$params = array(
    								'mode' => $mode,
    								'customer_id' => $customer_id, 
    								'calendarId' => $googleCalendarID, 
    								'service_account' => $bookingSync['booking_package_googleCalendar_json']['value'],
    								'startTime' => intval($sql_start_unixTime),
    								'endTime' => intval($sql_end_unixTime),
    								'form' => json_encode($form),
    								'timeZone' => get_option('timezone_string'),
    								'type' => $type
    							);
    							
    				if($iCalID !== false){
    				    
    				    $params['iCalID'] = $iCalID;
    				    
    				}
    				
    				#var_dump($params);
    				
    				$tmp_path = sys_get_temp_dir();
    				
    				$url = BOOKING_PACKAGE_EXTENSION_URL;
    				$ch = curl_init();
                	curl_setopt($ch, CURLOPT_URL, $url."googleCalendar/");
                	curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_COOKIEFILE, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                	curl_setopt($ch, CURLOPT_POST, 1);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			        
                	ob_start();
                	$response = curl_exec($ch);
                	$response = ob_get_contents();
                	ob_end_clean();
                	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                	curl_close ($ch);
                	#var_dump($response);
                	$googleCalendar = json_decode($response);
                	if($googleCalendar->status != 'error'){
                		
                	}
                	
                	#var_dump($httpCode);
                	$googleCalendar->responseMode = $mode;
                	if($httpCode >= 400){
                	    
                	    $googleCalendar->responseCode = $httpCode;
                	    $googleCalendar->responseStatus = 0;
                	    
                	}else{
                	    
                	    $googleCalendar->responseStatus = 1;
                	    
                	}
                	
                	return $googleCalendar;
                		
    			}
    			
    		}
    		
    	}
    	
    	public function deleteGC($accountKey, $id, $googleCalendarID){
            
            if(strlen($googleCalendarID) == 0){
                
                return array();
                
            }
            
            if(is_null($id)){
                
                return array("id" => "No ID");
                
            }
            
            $googleCalendar = array();
    		$bookingSync = $this->getBookingSyncList($accountKey);
    		$bookingSync = $bookingSync['Google_Calendar'];
    		if(intval($bookingSync['booking_package_googleCalendar_active']['value']) == 1){
    		    
    		    if($this->getExtensionsValid(false) === true){
    		        
    		        $timezone = get_option('timezone_string');
    		        $subscriptions = $this->upgradePlan('get');
    				$customer_id = $subscriptions['customer_id_for_subscriptions'];
    				$params = array(
    								'mode' => 'delete',
    								'customer_id' => $customer_id, 
    								'calendarId' => $googleCalendarID, 
    								'service_account' => $bookingSync['booking_package_googleCalendar_json']['value'],
    								'id' => $id,
    								'timeZone' => get_option('timezone_string')
    							);
    				#var_dump($params);
    				
    				$tmp_path = sys_get_temp_dir();
    				
    				$url = BOOKING_PACKAGE_EXTENSION_URL;
    				$ch = curl_init();
                	curl_setopt($ch, CURLOPT_URL, $url."googleCalendar/");
                	#curl_setopt($ch, CURLOPT_USERPWD, $subscriptions['customer_id_for_subscriptions'].":");
                	curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_COOKIEFILE, $tmp_path."/".$this->prefix."session.cookie");
                	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                	curl_setopt($ch, CURLOPT_POST, 1);
                	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			        
                	ob_start();
                	$response = curl_exec($ch);
                	$response = ob_get_contents();
                	ob_end_clean();
                	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                	curl_close ($ch);
                	$response = json_decode($response, true);
                	
                	return $response;
    		        
    		    }
    		    
    		}
            
        }
        
        public function activation($url, $mode, $version = null, $timezone = null, $site = null){
            
			if (is_null($timezone)) {
                
                $timezone = get_option($this->prefix . 'timezone', null);
                if (is_null($timezone)) {
                    
                    $timezone = get_option('timezone_string', '');
                    if(is_null($timezone) || strlen($timezone) == 0){
                        
                        $timezone = 'UTC';
                        
                    }
                    
                    add_option($this->prefix."timezone", sanitize_text_field($timezone));
                    
                }
                
			}
			
            if (is_null($site)) {
                
                $site = get_site_url();
                
            }
			
			$id = get_option($this->prefix."activation_id", null);
			$params = array("mode" => $mode, "timeZone" => $timezone, "local" => get_locale(), "site" => $site);
			
			if (!is_null($id) || $id != 0) {
				
				$params['id'] = $id;
				
			}
			
			if (!is_null($version)) {
			    
			    $params['version'] = $version;
			    
			}
			
			$args = array(
                'method' => 'POST',
                'body' => $params
            );
            $response = wp_remote_request($url . "activation/", $args);
            $object = json_decode(wp_remote_retrieve_body($response));
            $statusCode = wp_remote_retrieve_response_code($response);
			
			if (intval($statusCode) == 200 && $mode == 'activation') {
				
				if (get_option($this->prefix."activation_id") === false) {
					
					add_option($this->prefix."activation_id", intval($object->key));
					
				} else {
					
					update_option($this->prefix."activation_id", intval($object->key));
					
				}
				
			}
			
		}
		
		public function updateRolesOfPlugin() {
			
			$manager = $this->prefix . 'manager';
			$editor = $this->prefix . 'editor';
			
			if (is_null(get_role($manager))) {
				
				$roleArray = array('read' => true, 'level_0' => true, 'booking_package_manager' => true);
				$object = add_role($manager, 'Booking Package Manager', $roleArray);
				
			}
			
			if (is_null(get_role($editor))) {
				
				$roleArray = array('read' => true, 'level_0' => true, 'booking_package_editor' => true);
				$object = add_role($editor, 'Booking Package Editor', $roleArray);
				
			}
			
		}
		
		public function deleteRolesOfPlugin() {
			
			$roles = array($this->prefix . 'manager', $this->prefix . 'editor');
			for ($i = 0; $i < count($roles); $i++) {
				
				$role = $roles[$i];
				if (!is_null(get_role($role))) {
					
					$users = get_users(array('role' => $role));
					for ($a = 0; $a < count($users); $a++) {
						
						$user = $users[$a];
						$user->remove_role($role);
						
					}
					remove_role($role);
					
				}
				
			}
			
		}
		
		public function updateRolesOfUser() {
			
			$oldRole = $this->prefix . 'member';
			$newRole = $this->prefix . 'user';
			if (!is_null(get_role($oldRole))) {
				
				$users = get_users(array('role' => $oldRole));
				for ($i = 0; $i < count($users); $i++) {
					
					$user = $users[$i];
					#$user->remove_role($oldRole);
					#$user->add_role($newRole);
					var_dump($user->get_role_caps());
					echo "<br>\n";
					#break;
					
				}
				
				#remove_role($userRole);
				#$roleArray = array('read' => true, 'level_0' => true, 'booking_package' => true);
				#$object = add_role($newRole, 'Booking Package User', $roleArray);
				
			}
			
		}
		
		public function getOnlyNumbers($value) {
		    
		    if (function_exists('mb_convert_kana')) {
                
                $value = mb_convert_kana($value, 'n');
                
            }
            $value = preg_replace('/[^\-0-9]/', '', $value);
            
            if (empty($value)) {
                
                $value = 0;
                
            }
            
            return $value;
		    
		}
        
    }
    
    
?>