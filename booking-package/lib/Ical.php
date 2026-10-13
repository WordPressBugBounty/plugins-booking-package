<?php
    if (!defined('ABSPATH')) {
        exit;
    }
    
    class booking_package_iCal {
        
        public $prefix = null;
        
        public $pluginName = null;
        
        public $setting = null;
        
        public $currencies = array();
        
        public function __construct($prefix, $pluginName, $currencies) {
            
            $this->prefix = $prefix;
            $this->pluginName = $pluginName;
            $this->setting = new booking_package_setting($prefix, $pluginName);
            $this->currencies = $currencies;
            
        }
        
        private function escapeIcalText($text) {
            
            $text = str_replace('\\', '\\\\', (string)$text);
            $text = str_replace(';', '\;', $text);
            $text = str_replace(',', '\,', $text);
            $text = str_replace(array("\r\n", "\r", "\n"), '\n', $text);
            return $text;
            
        }
        
        public function isValid($token, $siteToken, $id = 'all'){
            
            $booking_sync = $this->setting->getBookingSyncList();
            $booking_sync = isset($booking_sync['iCal']) ? $booking_sync['iCal'] : array();
            
            $storedSiteToken = get_option('_' . $this->prefix . 'siteToken', '');
            if (empty($storedSiteToken) || empty($siteToken) || !hash_equals((string)$storedSiteToken, (string)$siteToken)) {
                
                return false;
                
            }
            
            if ($id === 'all') {
                
                $active = isset($booking_sync[$this->prefix.'ical_active']['value']) ? intval($booking_sync[$this->prefix.'ical_active']['value']) : 0;
                $savedToken = isset($booking_sync[$this->prefix.'ical_token']['value']) ? (string)$booking_sync[$this->prefix.'ical_token']['value'] : '';
                
                if ($active === 1 && !empty($savedToken) && !empty($token) && hash_equals($savedToken, (string)$token)) {
                    
                    $ical_data = $this->getBookingSchedules($id, null);
                    print $ical_data;
                    exit;
                    
                } else {
                    
                    return false;
                    
                }
                
            } else {
                
                $schedule = new booking_package_schedule($this->prefix, $this->pluginName, $this->currencies);
                $calendarAccount = $schedule->getCalendarAccount(intval($id));
                
                if (!empty($calendarAccount) && is_array($calendarAccount) && isset($calendarAccount['ical']) && intval($calendarAccount['ical']) === 1) {
                    
                    $savedToken = isset($calendarAccount['icalToken']) ? (string)$calendarAccount['icalToken'] : '';
                    if (!empty($savedToken) && !empty($token) && hash_equals($savedToken, (string)$token)) {
                        
                        $ical_data = $this->getBookingSchedules($id, $calendarAccount);
                        print $ical_data;
                        exit;
                        
                    }
                    
                }
                
                return false;
                
            }
            
        }
        
        public function getBookingSchedules($id = 'all', $calendarAccount = null) {
            
            $timezone = get_option($this->prefix . "timezone", null);
            $syncPastCustomersForIcal = intval(get_option($this->prefix . 'syncPastCustomersForIcal', 7));
            if (is_null($timezone)) {
                
                $timezone = get_option('timezone_string', 'UTC');
                
            }
            
            if ($id !== 'all' && !empty($calendarAccount) && is_array($calendarAccount)) {
                
                if (!empty($calendarAccount['timezone']) && $calendarAccount['timezone'] !== 'none') {
                    
                    $timezone = $calendarAccount['timezone'];
                    
                }
                
                if (isset($calendarAccount['syncPastCustomersForIcal'])) {
                    
                    $syncPastCustomersForIcal = intval($calendarAccount['syncPastCustomersForIcal']);
                    
                }
                
            }
            
            $safe_id = ($id === 'all') ? 'all' : intval($id);
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: inline; filename="WP_BOOKING_PACKAGE-' . $safe_id . '.ics"');
            header('X-Content-Type-Options: nosniff');
            
            date_default_timezone_set('UTC');
            $unixTime = time() - ($syncPastCustomersForIcal * 1440 * 60);
            $url_parce = parse_url(get_home_url());
            $host = isset($url_parce["host"]) ? $url_parce["host"] : 'localhost';
            
            $ical_data  = "BEGIN:VCALENDAR\r\n";
            $ical_data .= "PRODID:-//WP_BOOKING_APP/" . $safe_id . '/' . $host . "\r\n";
            $ical_data .= "VERSION:2.0\r\n";
            $ical_data .= "CALSCALE:GREGORIAN\r\n";
            $ical_data .= "METHOD:PUBLISH\r\n";
            $ical_data .= "X-WR-CALNAME:WP_BOOKING_PACKAGE-" . $safe_id . "\r\n";
            $ical_data .= "X-WR-TIMEZONE:" . $timezone . "\r\n";
            $ical_data .= "X-WR-CALDESC:\r\n";
            
            $schedule = new booking_package_schedule($this->prefix, $this->pluginName, $this->currencies);
            
            $month = date('m', $unixTime);
            $day   = date('d', $unixTime);
            $year  = date('Y', $unixTime);
            
            if ($id === 'all') {
                
                $calendarList = $schedule->getCalendarAccountListData("`key`, `name`, `type`, `timezone`, `guestsBool`");
                foreach ((array) $calendarList as $key => $value) {
                    
                    $calendarTimezone = (!empty($value['timezone']) && $value['timezone'] !== 'none') ? $value['timezone'] : $timezone;
                    $_POST['accountKey'] = $value['key'];
                    $response = $schedule->getReservationData($month, $day, $year, true);
                    $ical_data .= $this->createCard($value, $value['key'], $value['name'], $value['type'], $schedule, $response, $calendarTimezone, $host);
                    
                }
                
            } else {
                
                $calendar = !empty($calendarAccount) ? $calendarAccount : $schedule->getCalendarAccount(intval($id));
                if (!empty($calendar) && is_array($calendar)) {
                    
                    $calendarTimezone = (!empty($calendar['timezone']) && $calendar['timezone'] !== 'none') ? $calendar['timezone'] : $timezone;
                    $_POST['accountKey'] = $calendar['key'];
                    $response = $schedule->getReservationData($month, $day, $year, true);
                    $ical_data .= $this->createCard($calendar, $calendar['key'], $calendar['name'], $calendar['type'], $schedule,$response, $calendarTimezone,$host);
                    
                }
                
            }
            
            $ical_data .= "END:VCALENDAR\r\n";
            return $ical_data;
            
        }
        
        public function createCard($calendarAccount,$calendarKey, $calendarName,$calendarType, $schedule,$response, $timezone,$host) {
            
            date_default_timezone_set($timezone);
            $ical_data = '';
            
            if (!is_array($response)) {
                
                return $ical_data;
                
            }
            
            $countResponse = count($response);
            for ($i = 0; $i < $countResponse; $i++) {
                
                $bookingDetail = $response[$i];
                if (isset($bookingDetail['status']) && $bookingDetail['status'] === 'canceled') {
                    
                    continue;
                    
                }
                $summaryList = array();
                $descriptionList = array();$guestsList = array();
                
                if (isset($calendarAccount['type']) && $calendarAccount['type'] === 'day') {
                    
                    $guestsRaw = isset($bookingDetail['guests']) ? $bookingDetail['guests'] : '';
                    $guests = (!empty($guestsRaw) && is_string($guestsRaw)) ? json_decode($guestsRaw, true) : array();
                    
                    if (is_array($guests) && !empty($guests['guests']) && is_array($guests['guests'])) {
                        
                        $guestsList = $guests['guests'];
                        foreach ($guestsList as $guest) {
                            
                            if (isset($guest['index'])) {
                                
                                $index = intval($guest['index']);
                                if ($index > 0 && isset($guest['json'][$index]['name'])) {
                                    
                                    array_push($descriptionList,$guest['name'] . ": " . $guest['json'][$index]['name']);
                                    
                                }
                                
                            }
                            
                        }
                        
                        array_push($descriptionList, '');
                        
                    }
                    
                } else {
                    
                    $accommodationRaw = isset($bookingDetail['accommodationDetails']) ? $bookingDetail['accommodationDetails'] : '';
                    $accommodationDetails = (!empty($accommodationRaw) && is_string($accommodationRaw)) ? json_decode($accommodationRaw, true) : array();
                    $descriptionList = $this->addGuestsInRooms($accommodationDetails,$descriptionList);
                    
                }
                
                $praivateDataList = (isset($bookingDetail['praivateData']) && is_array($bookingDetail['praivateData'])) ? $bookingDetail['praivateData'] : array();
                foreach ($praivateDataList as $praivateData) {
                    
                    if (isset($praivateData['isName']) && $praivateData['isName'] === 'true' && !empty($praivateData['value'])) {
                        
                        $nameVal = is_array($praivateData['value']) ? implode(' ', $praivateData['value']) : (string)$praivateData['value'];
                        array_push($summaryList,$nameVal);
                        
                    }
                    
                    if (isset($praivateData['active']) &&$praivateData['active'] === 'true') {
                        
                        if (is_string($praivateData['value'])) {
                            
                            array_push($descriptionList, $praivateData['name'] . ": " . $praivateData['value']);
                            
                        } else if (is_array($praivateData['value'])) {
                            
                            array_push($descriptionList, $praivateData['name'] . ": " . implode(' ', $praivateData['value']));
                            
                        }
                        
                    }
                    
                }
                
                $summary = !empty($summaryList) ? implode(" ", $summaryList) : "No title";
                $description = implode('\n', array_map(array($this, 'escapeIcalText'), $descriptionList));
                $optionsData = (!empty($bookingDetail['options']) && is_string($bookingDetail['options'])) ? json_decode($bookingDetail['options'], true) : array();
                $selectedOptionsObject = $schedule->getSelectedServices($calendarAccount, $optionsData,$guestsList, "options", array(), 1, false);
                
                $courseTime = isset($bookingDetail['courseTime']) ? intval($bookingDetail['courseTime']) : 0;
                $services = array();
                if (is_array($selectedOptionsObject)) {
                    
                    $courseTime += isset($selectedOptionsObject['time']) ? intval($selectedOptionsObject['time']) : 0;
                    $services = isset($selectedOptionsObject['object']) && is_array($selectedOptionsObject['object']) ? $selectedOptionsObject['object'] : array();
                    
                }
                
                $serviceDetails = '';$detailsList = array();
                if (!empty($services)) {
                    
                    foreach ($services as $service) {
                        
                        array_push($detailsList, $service['name']);$no = 0;
                        if (isset($service['options']) && is_array($service['options'])) {
                            
                            foreach ($service['options'] as $option) {
                                
                                if (isset($option['selected']) && intval($option['selected']) === 1) {
                                    
                                    $no++;
                                    array_push($detailsList, "#" . $no . " " . $option['name']);
                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                    $serviceDetails = implode('\n', array_map(array($this, 'escapeIcalText'),$detailsList)) . '\n\n';
                    
                }
                
                $unixTime = isset($bookingDetail['date']['unixTime']) ? intval($bookingDetail['date']['unixTime']) : (isset($bookingDetail['scheduleUnixTime']) ? intval($bookingDetail['scheduleUnixTime']) : time());
                $reserveTime = isset($bookingDetail['reserveTime']) ? intval($bookingDetail['reserveTime']) : time();
                $ical_data .= "BEGIN:VEVENT\r\n";
                $ical_data .= "UID:" . $bookingDetail['key'] . "@" . $host . "\r\n";
                
                if ($calendarType === 'day') {
                    
                    $ical_data .= "DTSTART;TZID=" . $timezone . ":" . date("Ymd\THis", $unixTime) . "\r\n";
                    $endTime = ($courseTime !== 0) ? ($unixTime + ($courseTime * 60)) :$unixTime;
                    $ical_data .= "DTEND;TZID=" . $timezone . ":" . date("Ymd\THis", $endTime) . "\r\n";
                    
                } else {
                    
                    $accommodationRaw = isset($bookingDetail['accommodationDetails']) ? $bookingDetail['accommodationDetails'] : '';
                    $accommodationDetails = (!empty($accommodationRaw) && is_string($accommodationRaw)) ? json_decode($accommodationRaw, true) : array();
                    $checkIn = !empty($accommodationDetails['checkIn']) ? intval($accommodationDetails['checkIn']) : $unixTime;
                    $checkOut = !empty($accommodationDetails['checkOut']) ? intval($accommodationDetails['checkOut']) : $unixTime;
                    $ical_data .= "DTSTART;VALUE=DATE:" . date("Ymd", $checkIn) . "\r\n";
                    $ical_data .= "DTEND;VALUE=DATE:" . date("Ymd", $checkOut) . "\r\n";
                    
                }
                
                $ical_data .= "DTSTAMP:" . gmdate("Ymd\THis\Z") . "\r\n";
                $ical_data .= "CREATED:" . gmdate("Ymd\THis\Z", $reserveTime) . "\r\n";
                $ical_data .= "STATUS:CONFIRMED\r\n";
                $ical_data .= "SEQUENCE:0\r\n";
                $ical_data .= "SUMMARY:" . $this->escapeIcalText($summary) . "\r\n";
                $ical_data .= "LOCATION:" . $this->escapeIcalText($calendarName) . "\r\n";
                $ical_data .= "DESCRIPTION:" . $serviceDetails .$description . "\r\n";
                $ical_data .= "END:VEVENT\r\n";
                
            }
            
            return $ical_data;
            
        }
        
        public function addGuestsInRooms($accommodationDetails,$descriptionList) {
            
            $rooms = (isset($accommodationDetails['rooms']) && is_array($accommodationDetails['rooms'])) ? $accommodationDetails['rooms'] : array();
            
            foreach ($rooms as $i => $room) {
                
                $person = isset($room['person']) ? $room['person'] : '';
                array_push($descriptionList, __("Room", 'booking-package') . ' ' . ($i + 1) . ': ' . $person);$nameOfGuests = array();
                if (isset($room['guestsList']) && is_array($room['guestsList'])) {
                    
                    foreach ($room['guestsList'] as $key => $guestData) {
                        
                        $nameOfGuests[$key] = isset($guestData['name']) ? $guestData['name'] : '';
                        
                    }
                    
                }
                
                if (isset($room['guests']) && is_array($room['guests'])) {
                    
                    foreach ($room['guests'] as $key => $guest) {
                        
                        if (isset($guest['number']) && intval($guest['number']) > 0) {
                            
                            $guestLabel = isset($nameOfGuests[$key]) ? $nameOfGuests[$key] : '';$guestVal = isset($guest['name']) ? $guest['name'] : '';
                            array_push($descriptionList, $guestLabel . ": " . $guestVal);
                            
                        }
                        
                    }
                    
                }
                
            }
            
            return $descriptionList;
            
        }
        
        public function attachICalendar($calendarAccount,$event_data, $email_id,$id, $token = null,$type = 'attach') {
            
            $locationForIcalendar = isset($event_data['ical_location']) ? $event_data['ical_location'] : '';
            $emailData = array(
                'subject' => isset($event_data['ical_subject']) ? $event_data['ical_subject'] : '',
                'body' => isset($event_data['ical_description']) ? $event_data['ical_description'] : ''
            );
            
            if (empty($emailData['subject'])) {
                
                return array('temp_file' => null, 'temp_file_name' => null, 'status' => false);
                
            }
            
            $schedule = new booking_package_schedule($this->prefix, $this->pluginName,$this->currencies);
            $customer =$schedule->getCustomer($id,$token);
            
            if (empty($customer) || !is_array($customer)) {
                
                return array('temp_file' => null, 'temp_file_name' => null, 'status' => false);
                
            }
            
            $unixTime = isset($customer['scheduleUnixTime']) ? intval($customer['scheduleUnixTime']) : time();
            $notificationContents = $schedule->getNotificationContents($calendarAccount, $customer,$email_id, 'visitor', $emailData, 'text');
            $emailSubject = isset($notificationContents['emailSubject']) ? $notificationContents['emailSubject'] : '';
            $emailBody = isset($notificationContents['emailBody']) ? $notificationContents['emailBody'] : '';
            $servicesDetails = isset($notificationContents['servicesDetails']) ? $notificationContents['servicesDetails'] : array();
            
            $timezone = (!empty($calendarAccount['timezone']) && $calendarAccount['timezone'] !== 'none') ? $calendarAccount['timezone'] : 'UTC';
            $url_parce = parse_url(get_home_url());$host = isset($url_parce["host"]) ? $url_parce["host"] : 'localhost';
            $accountKey = isset($calendarAccount['key']) ? $calendarAccount['key'] : 1;
            
            $ical_data  = "BEGIN:VCALENDAR\r\n";
            $ical_data .= "PRODID:-//WP_BOOKING_PACKAGE_CUSTOMER//" . $accountKey . '//' .$host . "\r\n";
            $ical_data .= "VERSION:2.0\r\n";
            $ical_data .= "CALSCALE:GREGORIAN\r\n";
            $ical_data .= "METHOD:PUBLISH\r\n";
            $ical_data .= "X-WR-CALNAME:WP_BOOKING_PACKAGE-" . $accountKey . "\r\n";
            $ical_data .= "X-WR-TIMEZONE:" . $timezone . "\r\n";
            
            $ical_data .= "BEGIN:VEVENT\r\n";
            $ical_data .= "UID:" . $id . "@" . $host . "\r\n";
            
            if (isset($calendarAccount['type']) && $calendarAccount['type'] === 'day') {
                
                $ical_data .= "DTSTART;TZID=" . $timezone . ":" . date("Ymd\THis", $unixTime) . "\r\n";
                $serviceTime = (!empty($servicesDetails) && isset($servicesDetails['time'])) ? intval($servicesDetails['time']) : 0;
                $endTime = ($serviceTime !== 0) ? ($unixTime + ($serviceTime * 60)) : $unixTime;
                $ical_data .= "DTEND;TZID=" . $timezone . ":" . date("Ymd\THis", $endTime) . "\r\n";
                
            } else {
                
                $accommodationRaw = isset($customer['accommodationDetails']) ? $customer['accommodationDetails'] : '';
                $accommodationDetails = (!empty($accommodationRaw) && is_string($accommodationRaw)) ? json_decode($accommodationRaw, true) : array();
                $checkIn = !empty($accommodationDetails['checkIn']) ? intval($accommodationDetails['checkIn']) : $unixTime;
                $checkOut = !empty($accommodationDetails['checkOut']) ? intval($accommodationDetails['checkOut']) : $unixTime;
                $ical_data .= "DTSTART;VALUE=DATE:" . date("Ymd", $checkIn) . "\r\n";
                $ical_data .= "DTEND;VALUE=DATE:" . date("Ymd", $checkOut) . "\r\n";
                
            }
            
            $reserveTime = isset($customer['reserveTime']) ? intval($customer['reserveTime']) : time();
            $ical_data .= "DTSTAMP:" . gmdate("Ymd\THis\Z") . "\r\n";
            $ical_data .= "CREATED:" . gmdate("Ymd\THis\Z", $reserveTime) . "\r\n";
            $ical_data .= "STATUS:CONFIRMED\r\n";
            $ical_data .= "SEQUENCE:0\r\n";
            $ical_data .= "SUMMARY:" . $this->escapeIcalText(trim($emailSubject)) . "\r\n";
            $ical_data .= "LOCATION:" . $this->escapeIcalText(trim($locationForIcalendar)) . "\r\n";
            $ical_data .= "DESCRIPTION:" . $this->escapeIcalText(trim($emailBody)) . "\r\n";
            $ical_data .= "END:VEVENT\r\n";
            $ical_data .= "END:VCALENDAR\r\n";
            
            $temp_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ical_' . wp_generate_password(16, false, false) . '.ics';
            $temp_file_name = basename($temp_file);
            file_put_contents($temp_file,$ical_data);
            
            if ($type === 'attach') {
                
                return array('temp_file' => $temp_file, 'temp_file_name' =>$temp_file_name, 'status' => true);
                
            } else if ($type === 'download') {
                
                $file_mime_type = 'text/calendar';
                header('Content-Type: ' . $file_mime_type . '; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $temp_file_name . '"');
                header('Content-Length: ' . filesize($temp_file));
                readfile($temp_file);
                
                if (file_exists($temp_file)) {
                    
                    unlink($temp_file);
                    
                }
                
                exit;
                
            }
            
        }
        
    }
    
?>