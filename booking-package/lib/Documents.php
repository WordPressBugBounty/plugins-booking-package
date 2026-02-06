<?php
    
    if (!defined('ABSPATH')) {
    	exit;
	}
    
    class booking_package_documents {
        
        public $prefix = null;
        
        public $pluginName = null;
        
        public function __construct($prefix, $pluginName) {
            
            $this->prefix = $prefix;
            $this->pluginName = $pluginName;
            
        }
        
        public function dynamicTextModification() {
            
$document = <<<EOF

<div>
    <h2>Dynamic Text Translation and Modification (Filter Hook)</h2>
    <div>The text displayed in the Booking Package plugin can be dynamically modified using the <b><code>booking_package_get_translate_text</code></b> filter hook. Since this hook receives the Calendar Account ID and the site's language locale, it allows for highly flexible customization, such as applying different translations for each calendar and per language.</div>
    <strong style="font-size: 1.2em;">Filter Hook Details</strong>
    <ul><li>Hook Name: <code>booking_package_get_translate_text</code></li></ul>
    <strong style="font-size: 1.2em;">Parameters</strong>
    <ol>
        <li><b>\$type</b> (string) An identifier indicating the type of the translation target. It contains one of the following strings:
            <ul class="booking_package_get_translate_text_type"><li>form_field</li><li>service</li><li>option</li><li>guest</li><li>coupon</li><li>extra_charge</li><li>tax</li><li>notification</li><!-- <li>user_profile</li> --></ul></li>
        <li><b>\$calendar_account_id</b> (int) The ID of the calendar account associated with this text.</li>
        <li><b>\$unique_id</b> (string|int) A unique ID used to identify the specific translation target. (e.g., the form field key '<b>first_name</b>')</li>
        <li><b>\$locale</b> (string) The locale code of the current site language retrieved via the WordPress <b>get_locale()</b> function. (e.g., 'en_US', 'ja')</li>
        <li><b>\$string_array</b> (array) (<b>Target for modification</b>) An associative array containing the text to be translated. Modify the values within this array and ensure you return it as an array.
        <ul><li><b>Important</b>: When translating items where the value is an array, such as the options key (e.g., dropdown choices), you must ensure that the number of elements matches the original array. If the number of elements does not match the original array, the changes will not be reflected.</li></ul></li>
    </ol>
    <strong style="font-size: 1.2em;">Usage Example</strong>
    <div>This is a sample code that changes the label of the <b>first_name</b> field to "Nombre (Solo para ID 5)" only when the site language is Spanish (<b>es_ES</b>) and the Calendar Account ID is <b>5</b>.</div>
    <br>
    <code>/**<br>
    * Customize Booking Package text based on calendar and language<br>
    * <br>
    * @param string     \$type                Type of text<br>
    * @param int        \$calendar_account_id Calendar Account ID<br>
    * @param string|int \$unique_id           Unique ID<br>
    * @param string     \$locale              Current site locale<br>
    * @param array      \$string_array        Array of text to translate<br>
    * @return array                          Modified text array<br>
    */<br>
    function my_advanced_translator( \$type, \$calendar_account_id, \$unique_id, \$locale, \$string_array ) {<br>
    	// Check if specific conditions are met<br>
    	if ( \$type === 'form_field' && \$unique_id === 'first_name' && \$calendar_account_id === 5 && \$locale === 'es_ES' ) {<br>
    		\$string_array['label'] = 'Nombre (Solo para ID 5)';<br>
    	}<br>
    	// Always return the array<br>
    	return \$string_array;<br>
    }<br>
    add_filter( 'booking_package_get_translate_text', 'my_advanced_translator', 10, 5 );
    </code>
    
</div>

EOF;
            
            return $document;
            
        }
        
        public function displayBookingCalendar() {
            
$document = <<<EOF

<div>
    <h2>How to Display the Booking Calendar on Your Website</h2>
    <div>To publish your created calendar to site visitors, you need to paste the "shortcode" onto your page by following these steps:</div>
    <ol>
        <li><b>Copy the Shortcode</b> Check the "Shortcode" column on the right side of the "Calendar Settings" list screen. Select and copy the code for the calendar you want to display (e.g.,<code>[booking_package id=4]</code>).
        <li><b>Edit the Page</b> From the WordPress dashboard, navigate to "Pages" or "Posts" and open the editor for the page where you want to display the calendar (or click "Add New").</li>
        <li>
            Paste the Shortcode Paste the copied code into the content area.
            <ul>
                <li><b>If using the Block Editor (Gutenberg)</b>: Click the "+" button, search for and add the "Shortcode" block, then paste the code inside it.</li>
                <li><b>If using the Classic Editor</b>: Simply paste the code directly into the text input field.</li>
            </ul>
        </li>
        <li><b>Publish and Verify</b> Click "Publish" (or "Update"), then view the actual page to confirm that the calendar loads correctly.</li>
    </ol>
</div>

EOF;
            
            return $document;
            
        }
        
        public function emailSendingSettings() {
            
$document = <<<EOF

<div>
    <h2>Procedures for Configuring Email Settings in Booking Package</h2>
    <div>This guide explains how to configure settings to automatically send emails to the administrator and the booker (customer) when a booking is completed using Booking Package.</div>
    <h3>Step-by-Step Instructions</h3>
    <ol>
        <li>
             <b>Configuring Sender and Administrator Email Settings</b>
             <div>First, configure the sender information and the administrator email address that will receive booking notifications.</div>
             <ul>
                <li><b>Setting Location</b>: Calendar Account > <b>Settings</b> tab</li>
                <li><b>Relevant Section</b>: <b>Email Address</b> section</li>
             </ul>
             <div>The role of each item is as follows:</div>
             <ul>
                <li><b>Recipient Email Address</b>: Enter the email address of the administrator (you) who will receive notifications when a booking is made.</li>
                <li><b>Sender Name</b>: The name displayed as the "Sender" in the emails received by the customer (e.g., XX Salon Reservations, XX Inc.).</li>
                <li><b>Sending Email Address</b>: The email address that appears as the "From" address in the emails received by the customer.</li>
             </ul>
        </li>
        <li>
            <b>Linking the Booker's (Customer's) Email Address</b><br>
            <div>You must configure the plugin so it recognizes "which address to send the customer email to." Please note that if this setting is neglected, emails will not be delivered to the customer.</div>
            <ul>
                <li><b>Setting Location</b>: Calendar Account > <b>Form Fields</b> tab</li>
                <li><b>Relevant Section</b>: <b>Use as Email Field</b> within the advanced settings of the item being edited.</li>
            </ul>
            <div>Open the settings for the field where the customer enters their email address (e.g., "Email") and configure the following:</div>
            <ul>
                <li><b>Use as Email Field</b>: Make sure to select "<b>Yes</b>". By doing so, the address entered in this input field will be recognized as the destination for the automatic reply email.</li>
            </ul>
        </li>
        <li>
            <b>Creating the Email Body and Enabling Notifications</b><br>
            <div>Set the content of the emails to be sent for each booking status.</div>
            <ul><li><b>Setting Location</b>: Calendar Account > <b>Notifications</b> tab</li></ul>
            <div>When you open the Notifications tab, a list such as "New," "Pending," and "Approved" will appear. Follow the steps below to configure them:</div>
            <ul>
                <li><b>Select Sending Type</b>: Click on the timing of the email you wish to configure to open the edit screen.</li>
                <li><b>Notifications</b>: Check the box for "<b>Email</b>" within the edit screen. If this is not checked, the email will not be sent.</li>
                <li><b>Subject</b>: Enter the email subject line (e.g., [Booking Completed] Thank you for your reservation).</li>
                <li>
                    <b>Message body</b>: Enter the email body text. You can use the shortcodes found in the "Help" section on the right (e.g., <code>[name]</code> or <code>[bookingDate]</code>) to automatically insert the booker's name or booking date/time.
                    <ul>
                        <li><b>For Customer</b>: Enter the content intended for the customer.</li>
                        <li><b>For Administrator</b>: Enter the content intended for the administrator.</li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <b>If Emails Are Not Sent</b><br>
            <div>If emails are still not being sent from the plugin after configuring the settings above, please install a separate plugin that records email sending logs (such as <b>WP Mail Logging</b>) to check the logs. Checking the sending logs makes it easier to identify the cause, such as whether the email generation process is being performed correctly by the plugin or if an error is occurring on the server side.</div>
        </li>
    </ol>
    
</div>
EOF;
            
            return $document;
            
        }
        
        public function weeklyScheduleTemplates() {
            
$document = <<<EOF

<div>
    <h2>Setting Up Weekly Schedule Templates</h2>
    <div>In the "<b>Schedules</b>" tab under "Calendar Settings," you can configure the basic schedule pattern (template) that repeats every week.</div>
    <h3>Step-by-Step Instructions</h3>
    <ol>
        <li>
             <b>Open the Template Editing Screen</b>
             <div>Click on "<b>Weekly Schedule Templates</b>" in the top menu within the Schedules tab. From the list on the left side of the screen, select the "<b>Day of the Week</b>" you wish to configure.</div>
             
        </li>
        <li>
            <b>How to Add Time Slots</b><br>
            <div>There are two ways to add time slots:</div>
            <div><b>A. Adding Time Slots Manually (Individually)</b> This is useful when you want to add specific times only.</div>
            <ol>
                <li>Click the "Clock Icon" in the <b>empty row</b> at the bottom of the schedule list.</li>
                <li>The "<b>Add a Time Slot</b>" screen will appear.</li>
                <li>Select the desired time (minutes can also be specified) and click "<b>Apply</b>". The new slot will be added to the list.</li>
            </ol>
            <div><b>B. Generating Time Slots Automatically in Bulk</b> If no time slots are registered for that day, the "<b>Set Up Time Slots</b>" panel will automatically appear. This is useful for creating a full day's schedule at regular intervals all at once.</div>
            <ol>
                <li>
                    Configure the following items in the displayed panel and click "Apply":
                    <ul>
                        <li><b>Time</b>: Specify the start and end times (e.g., 09:00 to 18:00).</li>
                        <li><b>Interval</b>: Specify the gap between time slots (e.g., every 30 mins, every 1 hour).</li>
                        <li><b>Booking Deadline</b>: Set the cut-off time for accepting bookings.</li>
                        <li><b>Available Slots</b>: Set the capacity (stock) for each time slot.</li>
                    </ul>
                </li>
            </ol>
        </li>
        <li>
            <b>Saving Settings</b><br>
            <div>After adding or editing time slots, be sure to click the "<b>Save</b>" button at the bottom right of the screen to apply the settings.</div>
        </li>
        
    </ol>
    <h2>Important Notes: Applying Changes</h2>
    <ul>
        <li><b>Scope of Template Changes</b> Revisions made to the template here will <b>only apply to schedules generated after the change</b>. Template changes will not be automatically reflected in past or future time slots that have already been published (generated) on the calendar.</li>
        <li><b>How to Change Published Schedules</b> If you wish to modify the schedule for a specific day already displayed on the calendar, do not use the template editing screen. Instead, go to the "Schedules" tab, click directly on that <b>specific date</b> within the calendar, and edit the time slots individually.</li>
    </ul>
</div>
EOF;
            
            return $document;
            
            
        }
        
        public function bulkScheduleRegistration() {
            
$document = <<<EOF

<div>
    <h2>How to Select Multiple Dates and Bulk Register Schedules</h2>
    <div>In the Schedule management section of your Calendar Account, you can select multiple specific dates on the calendar to create and save common time slots at once.</div>
    <div>This feature is particularly useful when you want to register <b>consecutive dates</b> (such as for an event period) or <b>irregular business days</b> that differ from the patterns defined in your Weekly Schedule Templates.</div>
    <h3>Step-by-Step Instructions</h3>
    <ol>
        <li>
            <b>Switch to Multiple Day Selection Mode</b><br>
            <div>Click the "Select Multiple Days" button located at the top of the calendar in the Schedule tab.</div>
        </li>
        <li>
            <b>Select Dates</b><br>
            <div>Click on the dates on the calendar where you want to register a schedule.</div>
            <ul>
                <li><b>Selected dates</b> will change to have a <b>blue background color</b>.</li>
                <li>If you select a date by mistake, simply click it again to deselect it (the background will return to white).</li>
            </ul>
        </li>
        <li>
            <b>Add Time Slots</b><br>
            <div>Once you have selected all the target dates, click the "<b>Add Time Slots</b>" button at the top left of the screen. A settings popup will appear. Configure the following items:</div>
            <ul>
                <li><b>Time</b>: Set the start time (From) and end time (To) for accepting bookings.</li>
                <li><b>Interval</b>: Set the duration for each booking slot (e.g., 30 min, 60 min).</li>
                <li><b>Booking Deadline</b>: Set the cutoff time for accepting bookings (e.g., 0 min before).</li>
                <li><b>Available Slots</b>: Set the maximum number of guests/bookings accepted for each time slot.</li>
            </ul>
            <div>When you are finished configuring these settings, click "Apply" within the popup.</div>
        </li>
        <li>
            <b>Review the List</b><br>
            <div>A list of time slots will be automatically generated for all the selected dates. You can review the generated list and individually modify details (such as time or capacity) on this screen if necessary.</div>
        </li>
    </ol>
    <h2>Saving and Publication Settings</h2>
    <div>There are two methods for saving your schedule: "<b>Immediate Publication</b>" and "<b>Scheduled Publication</b>". Please choose the method that best suits your needs.</div>
    <b>Pattern A: Start Bookings Immediately (Standard Save)</b><br>
    <div>If you want the created schedule to appear on your website's calendar immediately, click the "<b>Save</b>" button at the bottom right of the screen. Acceptance of bookings will begin as soon as you save.</div>
    <b>Pattern B: Start Bookings Later (Set Publication Date)</b><br>
    <div>If you want to publish the booking slots only after a specific date and time (e.g., for a promotional campaign or when opening bookings for the next month), follow these steps:</div>
    <ol>
        <li>Click the "<b>Set the Publication Date</b>" button at the bottom right of the screen.</li>
        <li>A date selection calendar will appear. Select the <b>date and time you want the schedule to be published</b>.</li>
        <li>Click "<b>Save</b>" within the settings window.</li>
    </ol>
    <div>
        <b>Note</b>: Schedules saved using this method will not appear on the front-end calendar (the booking screen for customers) until the specified date and time is reached. Once that time passes, the slots will automatically appear and become available for booking.
    </div>
    <h2>Supplementary Information</h2>
    <ul>
        <li><b>Delete or Pause Schedules</b>: Before saving, you can use the checkboxes on the right side of the generated list to set specific slots to "<b>Delete</b>" or "<b>Paused</b>".</li>
    </ul>
</div>
EOF;
            
            return $document;
            
            
        }
        
        public function videos() {
            
            $document = '<div><h3>' . __('Videos', 'booking-package') . '</h3>';
            $document .= '<ul>';
			$document .= '<li><a href="https://booking-package.saasproject.net/how-does-the-booking-calendar-show-on-the-page/" target="_blank">' . __('How do I show the booking calendar on the page?', 'booking-package') . '</a></li>';
			$document .= '<li><a href="https://booking-package.saasproject.net/how-do-i-send-a-booking-email-with-a-plugin/" target="_blank">' . __('How do I send a booking email?', 'booking-package') . '</a></li>';
			$document .= '<li><a href="https://booking-package.saasproject.net/how-do-i-create-booking-schedules/" target="_blank">' . __('How do I create booking schedules?', 'booking-package') . '</a></li>';
			$document .= '</ul></div>';
            return $document;
            
        }
        
    }
    
    
    
?>