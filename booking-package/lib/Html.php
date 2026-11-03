<?php
    if (!defined('ABSPATH')) {
        exit;
    }
    
    class booking_package_HTMLElement {
        
        public $prefix = null;
        
        public $plugin_name = null;
        
        public $accountKey = null;
        
        public $currencies = array();
        
        public $managementUsersV2 = 1;
        
        public function __construct($prefix, $pluginName, $currencies) {
            
            $this->prefix = $prefix;
            $this->plugin_name = $pluginName;
            $this->currencies = $currencies;
            
        }
        
        public function setManagementUsersV2($managementUsersV2) {
            
            $this->managementUsersV2 = $managementUsersV2;
            
        }
        
        public function member_form($user, $member_login_error, $dictionary) {
            
            $user_login = "";
            $user_email = "";
            if (isset($user['user_login']) && isset($user['user_email'])) {
                
                $user_login = $user['user_login'];
                $user_email = $user['user_email'];
                
            }
            
            $permalink = get_permalink();
            
            $text = array(
                'Sign Up'                                           => __("Sign Up", 'booking-package'),
                'Username'                                          => __("Username", 'booking-package'),
                'Email'                                             => __("Email", 'booking-package'),
                'Password'                                          => __("Password", 'booking-package'),
                'Registration confirmation will be emailed to you.' => __("Registration confirmation will be emailed to you.", 'booking-package'),
                'Register'                                          => __("Register", 'booking-package'),
                'Return'                                            => __("Return", 'booking-package'),
                'Profile'                                           => __("Profile", 'booking-package'),
                'Status'                                            => __("Status", 'booking-package'),
                'Approved'                                          => __("Approved", 'booking-package'),
                'Change Password'                                   => __("Change Password", 'booking-package'),
                'Update Profile'                                    => __("Update Profile", 'booking-package'),
                'Delete'                                            => __("Delete", 'booking-package'),
            );
            
            if (!empty($dictionary) && is_array($dictionary)) {
                
                if (isset($dictionary['Sign Up'])) {
                    $text['Sign Up'] = $dictionary['Sign Up'];
                }
                if (isset($dictionary['Register'])) {
                    $text['Register'] = $dictionary['Register'];
                }
                
            }
            
            if (isset($user['check_email_for_member']) && intval($user['check_email_for_member']) == 0) {
                
                $text['Registration confirmation will be emailed to you.'] = '';
                
            }
            
            $addUserPanel = '<div id="booking-package-user-form" class="hidden_panel">';
            $addUserPanel .= '  <div>';
            $addUserPanel .= '      <div class="titlePanel">';
            $addUserPanel .= '          <div class="title">' . esc_html($text["Sign Up"]) . '</div>';
            $addUserPanel .= '          <div id="booking-package-register_user_return_button" class="material-icons closeButton" style="font-family: \'Material Icons\' !important;">close</div>';
            $addUserPanel .= '      </div>';
            $addUserPanel .= '      <div id="addCustomFormFieldPanel" class=""></div>';
            $addUserPanel .= '      <div id="booking-package-user_regist_message">' . esc_html($text["Registration confirmation will be emailed to you."]) . '</div>';
            $addUserPanel .= '      <div id="booking-package-user_regist_error_message" class="login_error hidden_panel"></div>';
            $addUserPanel .= '      <button id="booking-package-register_user_button" class="register_button">' . esc_html($text["Register"]) . '</button>';
            $addUserPanel .= '  </div>';
            $addUserPanel .= '</div>';
            
            $editUserPanel = '<div id="booking-package-user-edit-form" class="hidden_panel">';
            $editUserPanel .= ' <div>';
            $editUserPanel .= '     <div class="titlePanel">';
            $editUserPanel .= '         <div class="title">' . esc_html($text["Profile"]) . '</div>';
            $editUserPanel .= '         <div id="booking-package-edit_user_return_button" class="material-icons closeButton" style="font-family: \'Material Icons\' !important">close</div>';
            $editUserPanel .= '     </div>';
                        
            $editUserPanel .= '      <div id="editCustomFormFieldPanel" class=""></div>';
            
            $editUserPanel .= '     <div id="booking-package-user-profile" class="inputPanel">';
            $editUserPanel .= '         <div> <label>' . esc_html($text['Username']) . '</label> <input type="text" name="booking-package-user_edit_login" id="booking-package-user_edit_login" class="input" value="' . esc_attr($user_login) . '" size="20" disabled> </div>';
            $editUserPanel .= '         <div> <label>' . esc_html($text['Email']) . '</label> <input type="text" name="booking-package-user_edit_email" id="booking-package-user_edit_email" class="input" value="' . esc_attr($user_email) . '" size="20"> </div>';
            $editUserPanel .= '         <div id="booking-package-user_status_field">';
            $editUserPanel .= '             <label>' . esc_html($text["Status"]) . '</label>';
            $editUserPanel .= '             <label> <input type="checkbox" name="booking-package-user_edit_status" id="booking-package-user_edit_status" class="" value="1"> ' . esc_html($text["Approved"]) . ' </label>';
            $editUserPanel .= '         </div>';
            $editUserPanel .= '         <div id="booking-package-edit_password_filed">';
            $editUserPanel .= '             <label>' . esc_html($text["Password"]) . '</label>';
            $editUserPanel .= '             <button id="booking-package-user_edit_change_password_button" class="change_user_password_button">' . esc_html($text["Change Password"]) . '</button>';
            $editUserPanel .= '             <input type="password" name="booking-package-user_edit_pass" id="booking-package-user_edit_pass" class="input hidden_panel" value="" size="20">';
            $editUserPanel .= '         </div>';
            $editUserPanel .= '     </div>';
            
            $editUserPanel .= '     <div>';
            $editUserPanel .= '         <button id="booking-package-edit_user_button" class="update_user_button">' . esc_html($text["Update Profile"]) . '</button>';
            $editUserPanel .= '         <button id="booking-package-edit_user_delete_button" class="delete_user_button">' . esc_html($text["Delete"]) . '</button>';
            $editUserPanel .= '     </div>';
            $editUserPanel .= ' </div>';
            $editUserPanel .= '</div>';
            $editUserPanel .= '<input type="hidden" id="booking-package-permalink" value="' . esc_url($permalink) . '">';
            
            $html = '';
            if (!empty($member_login_error) && is_string($member_login_error)) {
                
                $html .= '<div class="member_login_error">' . esc_html($member_login_error) . '</div>';
                
            }
            $html .= $addUserPanel;
            $html .= $editUserPanel;
            
            return $html;
            
        }
        
        public function cancelBookingDetailsForVisitor_panel($dictionary) {
            
            $text = array(
                'Booking Details'    => (isset($dictionary["Booking Details"])) ? $dictionary["Booking Details"] : __("Booking Details", 'booking-package'),
                'Return to Calendar' => __("Return to Calendar", 'booking-package'),
                'Cancel Booking'     => (isset($dictionary["Cancel Booking"])) ? $dictionary["Cancel Booking"] : __("Cancel Booking", 'booking-package'),
            );
            
            $title = esc_html($text["Booking Details"]);
            
            $html = '<div id="booking-package_myBookingDetailsFroVisitor" class="hidden_panel">' . "\n";
            $html .= '    <div class="titlePanel">' . "\n";
            $html .= '        <div class="title selectedDate">' . $title . '</div>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '    <div class="buttonPanel">' . "\n";
            $html .= '        <div id="myPersonalDetails" class="myPersonalDetails row" style="border-width: 0;"></div>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '</div>' . "\n";
            
            return $html;
            
        }
        
        public function myBookingHistory_panel($dictionary) {
            
            $text = array(
                'Booking History'    => (isset($dictionary['Booking History'])) ? $dictionary['Booking History'] : __("Booking History", 'booking-package'),
                'Return to Calendar' => __("Return to Calendar", 'booking-package'),
                'Cancel Booking'     => __("Cancel Booking", 'booking-package'),
                'ID'                 => __("ID", 'booking-package'),
                "Booking Date"       => (isset($dictionary['Booking Date'])) ? $dictionary['Booking Date'] : __("Booking Date", 'booking-package'),
                "Calendar"           => (isset($dictionary['Calendar'])) ? $dictionary['Calendar'] : __("Calendar", 'booking-package'),
                "Status"             => (isset($dictionary['Status'])) ? $dictionary['Status'] : __("Status", 'booking-package'),
            );
            
            $title = esc_html($text["Booking History"]);
            $th_id = esc_html($text["ID"]);
            $th_date = esc_html($text["Booking Date"]);
            $th_calendar = esc_html($text["Calendar"]);
            $th_status = esc_html($text["Status"]);
            
            $html = '<div id="booking-package_myBookingHistory" class="hidden_panel">' . "\n";
            $html .= '    <div class="titlePanel">' . "\n";
            $html .= '        <div class="title">' . $title . '</div>' . "\n";
            $html .= '        <div id="booking-package-bookingHistory_close_button" class="material-icons closeButton" style="font-family: \'Material Icons\' !important;">close</div>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '    <div>' . "\n";
            $html .= '        <table id="booking-package_myBookingHistoryTable">' . "\n";
            $html .= '            <tr data-head="th">' . "\n";
            $html .= '                <th>' . $th_id . '</th>' . "\n";
            $html .= '                <th>' . $th_date . '</th>' . "\n";
            $html .= '                <th>' . $th_calendar . '</th>' . "\n";
            $html .= '                <th>' . $th_status . '</th>' . "\n";
            $html .= '            </tr>' . "\n";
            $html .= '        </table>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '    <div class="buttonPanel">' . "\n";
            $html .= '        <button id="booking-package-bookingHistory_returnButton" class="material-icons left_arrow_button" style="font-family: \'Material Icons\' !important;">navigate_before</button>' . "\n";
            $html .= '        <button id="booking-package-bookingHistory_nextButton" class="material-icons right_arrow_button" style="font-family: \'Material Icons\' !important;">navigate_next</button>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '</div>' . "\n";
            
            return $html;
            
        }
        
        public function myBookingDetails_panel($dictionary) {
            
            $text = array(
                'Booking History'    => (isset($dictionary["Booking History"])) ? $dictionary["Booking History"] : __("Booking History", 'booking-package'),
                'Return to Calendar' => __("Return to Calendar", 'booking-package'),
                'Cancel Booking'     => (isset($dictionary["Cancel Booking"])) ? $dictionary["Cancel Booking"] : __("Cancel Booking", 'booking-package'),
                'Booking Details'    => (isset($dictionary["Booking Details"])) ? $dictionary["Booking Details"] : __("Booking Details", 'booking-package'),
                'ID'                 => __("ID", 'booking-package'),
                "Booking Date"       => __("Booking Date", 'booking-package'),
                "Status"             => __("Status", 'booking-package'),
                "Return"             => (isset($dictionary["Return"])) ? $dictionary["Return"] : __("Return", 'booking-package'),
            );
            
            $title = esc_html($text["Booking Details"]);
            $return_text = esc_html($text["Return"]);
            $cancel_text = esc_html($text["Cancel Booking"]);
            
            $html = '<div id="booking-package_myBookingDetails" class="hidden_panel">' . "\n";
            $html .= '    <div class="titlePanel">' . "\n";
            $html .= '        <div class="title">' . $title . '</div>' . "\n";
            $html .= '        <div id="booking-package-myBookingDetails_close_button" class="material-icons closeButton" style="font-family: \'Material Icons\' !important;">close</div>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '    <div id="booking-package_myBookingDetails_panel"></div>' . "\n";
            $html .= '    <div class="buttonPanel">' . "\n";
            $html .= '        <button id="booking-package-myBookingDetails_returnButton" class="returnButton">' . $return_text . '</button>' . "\n";
            $html .= '        <button id="booking-package-cancelThisBooking" class="cancel_user_booking_button">' . $cancel_text . '</button>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '</div>' . "\n";
            
            return $html;
            
        }
        
    }
?>