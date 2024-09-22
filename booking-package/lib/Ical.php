<?php
    if(!defined('ABSPATH')){
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
        
        public function isValid($token, $siteToken, $id = 'all'){
            
            $booking_sync = $this->setting->getBookingSyncList();
            $booking_sync = $booking_sync['iCal'];
            if (get_option('_' . $this->prefix . 'siteToken', false) != $siteToken) {
                
                return false;
                
            }
            
            if ($id == 'all') {
                
                if (intval($booking_sync[$this->prefix.'ical_active']['value']) == 1 && $booking_sync[$this->prefix.'ical_token']['value'] == $token) {
                    
    			    $ical_data = $this->getBookingSchedules($id, null);
    			    print $ical_data;
                    exit;
                    
                } else {
                    
                    return false;
                    
                }
                
            } else {
                
                $schedule = new booking_package_schedule($this->prefix, $this->pluginName, $this->currencies);
                $calendarAccount = $schedule->getCalendarAccount($id);
                if (intval($calendarAccount['ical']) == 1 && $calendarAccount['icalToken'] == $token) {
                    
                    $ical_data = $this->getBookingSchedules($id, $calendarAccount);
                    print $ical_data;
                    exit;
                    
                } else {
                    
                    return false;
                    
                }
                
            }
            
        }
        
        public function getBookingSchedules($id = 'all', $calendarAccount = null){
            
            #$timezone = get_option('timezone_string');
            $timezone = get_option($this->prefix . "timezone", null);
            $syncPastCustomersForIcal = intval(get_option($this->prefix . 'syncPastCustomersForIcal', 7));
			if (is_null($timezone)) {
				
				$timezone = get_option('timezone_string', 'UTC');
				
			}
			
			if ($id != 'all' && !empty($calendarAccount)) {
			    
			    $timezone = $calendarAccount['timezone'];
			    $syncPastCustomersForIcal = intval($calendarAccount['syncPastCustomersForIcal']);
			    
			}
			
			header('Content-type: text/calendar; charset=utf-8');
			header('Content-Disposition: inline; filename=WP_BOOKING_PACKAGE-' . $id . '.ics');
            
		    date_default_timezone_set('UTC');
		    $unixTime = date('U') - ($syncPastCustomersForIcal * 1440 * 60);
		    $gmtOffset = get_option('gmt_offset');
		    $gmtOffset = date('P');
		    $url_parce = parse_url(get_home_url());
		    $host = $url_parce["host"];
		    
		    $ical_data = "BEGIN:VCALENDAR\n";
            $ical_data .= "PRODID:-//WP_BOOKING_APP/" . $id . '/' . $host . "\n";
            $ical_data .= "VERSION:2.0\n";
            $ical_data .= "CALSCALE:GREGORIAN\n";
            $ical_data .= "METHOD:PUBLISH\n";
            $ical_data .= "X-WR-CALNAME:WP_BOOKING_PACKAGE-" . $id . "\n";
            $ical_data .= "X-WR-TIMEZONE:".$timezone."\n";
            $ical_data .= "X-WR-CALDESC:\n";
		    
            $schedule = new booking_package_schedule($this->prefix, $this->pluginName, $this->currencies);
            
            $month = date('m', $unixTime);
            $year = date('Y', $unixTime);
            if ($id == 'all') {
                
                $calendarList = $schedule->getCalendarAccountListData("`key`, `name`, `type`, `timezone`, `guestsBool`");
                foreach ((array) $calendarList as $key => $value) {
                    
                    $calendarTimezone = $timezone;
                    if ($value['timezone'] != 'none') {
                        
                        $calendarTimezone = $value['timezone'];
                        
                    }
                    
                    $month = date('m', $unixTime);
                    $year = date('Y', $unixTime);
                    $_POST['accountKey'] = $value['key'];
                    $response = $schedule->getReservationData($month, date('d', $unixTime), $year, true);
                    $ical_data .= $this->createCard($value, $value['key'], $value['name'], $value['type'], $schedule, $response, $calendarTimezone, $host);
                    $month++;
                    if ($month > 12) {
                        
                        $month = 1;
                        $year++;
                        
                    }
                    
                }
                
            } else {
                
                $calendar = $schedule->getCalendarAccount(intval($id));
                $calendarTimezone = $timezone;
                if ($calendar['timezone'] != 'none') {
                    
                    $calendarTimezone = $calendar['timezone'];
                    
                }
                $_POST['accountKey'] = $calendar['key'];
                $response = $schedule->getReservationData($month, date('d', $unixTime), $year, true);
                $ical_data .= $this->createCard($calendar, $calendar['key'], $calendar['name'], $calendar['type'], $schedule, $response, $calendarTimezone, $host);
                $month++;
                if ($month > 12) {
                    
                    $month = 1;
                    $year++;
                    
                }
                
            }
            
            $ical_data .= "END:VCALENDAR\n";
            return $ical_data;
            
        }
        
        public function createCard($calendarAccount, $calendarKey, $calendarName, $calendarType, $schedule, $response, $timezone, $host){
            
            #date_default_timezone_set('UTC');
            date_default_timezone_set($timezone);
            $ical_data = '';
            for ($i = 0; $i < count($response); $i++) {
                
                $bookingDetail = $response[$i];
                if (isset($bookingDetail['status']) && $bookingDetail['status'] == 'canceled') {
                    
                    continue;
                    
                }
                $summaryList = array();
                //DESCRIPTION
                
                $descriptionList = array();
                $guestsList = array();
                if ($calendarAccount['type'] == 'day') {
                    
                    $guests = $bookingDetail['guests'];
                    if (empty($guests) || is_null($guests)) {
                        
                        $guests = array();
                        
                    } else {
                        
                        $guests = json_decode($guests, true);
                        
                    }
                    
                    
                    if (count($guests) > 0 && isset($guests['guests'])) {
                        
                        $guestsList = $guests['guests'];
                        for ($guestKey = 0; $guestKey < count($guestsList); $guestKey++) {
                            
                            $guest = $guestsList[$guestKey];
                            if (isset($guest['index'])) {
                                
                                $index = intval($guest['index']);
                                if ($index > 0) {
                                    
                                    array_push($descriptionList, $guest['name'] . ": " . $guest['json'][$index]['name']);
                                    
                                }
                                   
                            }
                            
                        }
                        
                        array_push($descriptionList, '');
                        
                    }
                    
                } else {
                    
                    $accommodationDetails = json_decode($bookingDetail['accommodationDetails'], true);
                    $descriptionList = $this->addGuestsInRooms($accommodationDetails, $descriptionList);
                    
                }
                
                
                
                for ($a = 0; $a < count($bookingDetail['praivateData']); $a++) {
                    
                    $praivateData = $bookingDetail['praivateData'][$a];
                    if (isset($praivateData['type']) && $praivateData['type'] == 'TEXTAREA') {
                        
                        $praivateData['value'] = str_replace(array("\r\n", "\r", "\n"), "\\n", $praivateData['value']);
                        
                    }
                    
                    if (isset($praivateData['isName']) && $praivateData['isName'] == 'true') {
                        
                        array_push($summaryList, $praivateData['value']);
                        
                    }
                    
                    if (isset($praivateData['active']) && $praivateData['active'] == 'true') {
                        
                        if (is_string($praivateData['value'])) {
                            
                            array_push($descriptionList, $praivateData['name'] . ": " . $praivateData['value']);
                            
                        } else if (is_array($praivateData['value'])) {
                            
                            array_push($descriptionList, $praivateData['name'] . ": " . implode(' ', $praivateData['value']));
                            
                        }
                        
                        
                    }
                    
                }
                
                $summary = "No title";
                if (count($summaryList) != 0) {
                    
                    $summary = implode(" ", $summaryList);
                    
                }
                
                $description = implode('\n', $descriptionList);
                $selectedOptionsObject = $schedule->getSelectedServices($calendarAccount, json_decode($bookingDetail['options'], true), $guestsList, "options", array());
                $courseTime = intval($bookingDetail['courseTime']);
                $courseTime += $selectedOptionsObject['time'];
                $services = $selectedOptionsObject['object'];
                
                $serviceDetails = '';
                $detailsList = array();
                if (is_array($services)) {
					
					foreach ((array) $services as $key => $service) {
						
						$responseCostInService = $schedule->getCostsInService($calendarAccount, $service, $guestsList);
						$costs = $responseCostInService['costs'];
						$details = $service['name'];
						
						array_push($detailsList, $details);
						
						$no = 0;
						foreach ((array) $service['options'] as $option) {
							
							if (intval($option['selected']) == 1) {
								
								$no++;
								$details = "#".$no." ".$option['name']." ";
								
								array_push($detailsList, $details);
								
							}
							
						}
						
					}
					
					$serviceDetails = implode('\n', $detailsList) . '\n\n';
					
				}
                
                
                
                $unixTime = $bookingDetail['date']['unixTime'];
                $ical_data .= "BEGIN:VEVENT\n";
                $ical_data .= "UID:".$bookingDetail['key']."@".$host."\n";
                
                if ($calendarType == 'day') {
                    
                    $ical_data .= "DTSTART;TZID=".$timezone.":".date("Ymd\THi00", $unixTime)."\n";
                    if ($courseTime != 0) {
                        
                        $ical_data .= "DTEND;TZID=".$timezone.":".date("Ymd\THi00", ($unixTime + ($courseTime * 60)))."\n";
                        
                    } else {
                        
                        $ical_data .= "DTEND;TZID=".$timezone.":".date("Ymd\THi00", $unixTime)."\n";
                        
                    }
                    
                } else {
                    
                    $accommodationDetails = json_decode($bookingDetail['accommodationDetails'], true);
                    $ical_data .= "DTSTART;TZID=".$timezone.":".date("Ymd\T12i00", $accommodationDetails['checkIn'])."\n";
                    $ical_data .= "DTEND;TZID=".$timezone.":".date("Ymd\T12i00", $accommodationDetails['checkOut'])."\n";
                    
                }
                
                $ical_data .= "DTSTAMP:".date("Ymd\THi00\Z")."\n";
                $ical_data .= "CREATED;TZID=".$timezone.":".date("Ymd\THi00", $bookingDetail['reserveTime'])."\n";
                $ical_data .= "STATUS:CONFIRMED\n";
                #$ical_data .= "SEQUENCE:".($i + 1)."\n";
                $ical_data .= "SEQUENCE:0\n";
                $ical_data .= "SUMMARY:".$summary."\n";
                $ical_data .= "LOCATION:".$calendarName."\n";
                $ical_data .= "DESCRIPTION:" . $serviceDetails . $description . "\n";
                $ical_data .= "END:VEVENT\n";
                #var_dump($bookingDetail['praivateData']);
                
            }
            
            return $ical_data;
            
        }
        
        public function addGuestsInRooms($accommodationDetails, $descriptionList) {
            
            $rooms = array();
            if (isset($accommodationDetails['rooms'])) {
                
                $rooms = $accommodationDetails['rooms'];
                
            }
            
            for ($i = 0; $i < count($rooms); $i++) {
                
                $room = $rooms[$i];
                $guests = $room['guests'];
                if (array_key_exists('guestsList', $room) === false) {
                    
                    $room['guestsList'] = array();
                    
                }
                array_push($descriptionList, __("Room", 'booking-package') . ' ' . ($i + 1) . ': ' . $room['person']);
                
                $nameOfGuests = array();
                foreach ($room['guestsList'] as $key => $guestData) {
                    
                    $nameOfGuests[$key] = $guestData['name'];
                    
                }
                
                foreach ($guests as $key => $guest) {
                    
                    if (intval($guest['number']) > 0) {
                        
                        array_push($descriptionList, $nameOfGuests[$key] . ": " . $guest['name']);
                        
                    }
                    
                }
                
            }
            
            return $descriptionList;
            
        }
        
        public function attachICalendar($calendarAccount, $email_id, $id, $token = null, $type = 'attach') {
            
            global $wpdb;
            $locationForIcalendar = '';
            $emailData = array('subject' => null, 'body' => null);
            $table_name = $wpdb->prefix . "booking_package_email_settings";
            
            for ($i = 0; $i < count($email_id); $i++) {
				
				$sql = $wpdb->prepare(
					"SELECT `subjectForIcalendar`, `locationForIcalendar`, `contentForIcalendar` FROM " . $table_name . " WHERE `accountKey` = %d AND `mail_id` = %s;", 
					array(intval($calendarAccount['key']), $email_id[$i])
				);
				$row = $wpdb->get_row($sql, ARRAY_A);
				$locationForIcalendar = $row['locationForIcalendar'];
				$emailData['subject'] = $row['subjectForIcalendar'];
				$emailData['body'] = $row['contentForIcalendar'];
				break;
				
			}
			
			if (empty($emailData['subject']) === true) {
			    
			    return array('temp_file' => null, 'temp_file_name' => null, 'status' => false);
			    
			}
            
            $schedule = new booking_package_schedule($this->prefix, $this->pluginName, $this->currencies);
            $customer = $schedule->getCustomer($id, $token);
            $unixTime = $customer['scheduleUnixTime'];
            $notificationContents = $schedule->getNotificationContents($calendarAccount, $customer, $email_id, 'visitor', $emailData, 'text');
			$emailSubject = $notificationContents['emailSubject'];
			$emailBody = str_replace("\n", '\n', $notificationContents['emailBody']);
			$servicesDetails = $notificationContents['servicesDetails'];
            
            $timezone = $calendarAccount['timezone'];
            $url_parce = parse_url(get_home_url());
		    $host = $url_parce["host"];
		    
		    $ical_data = "BEGIN:VCALENDAR\n";
            $ical_data .= "PRODID:-//WP_BOOKING_PACKAGE_CUSTOMER//" . $calendarAccount['key'] . '//' . $host . "\n";
            $ical_data .= "VERSION:2.0\n";
            $ical_data .= "CALSCALE:GREGORIAN\n";
            $ical_data .= "METHOD:PUBLISH\n";
            $ical_data .= "X-WR-CALNAME:WP_BOOKING_PACKAGE-" . $calendarAccount['key'] . "\n";
            $ical_data .= "X-WR-TIMEZONE:".$timezone."\n";
            
            $ical_data .= "BEGIN:VEVENT\n";
            $ical_data .= "UID:" . $id . "@" . $host . "\n";
            
            if ($calendarAccount['type'] == 'day') {
                
                $ical_data .= "DTSTART;TZID=" . $timezone . ":" . date("Ymd\THi00", $unixTime) . "\n";
                #$ical_data .= "DTSTART:" . date("Ymd\THi00\Z", $unixTime) . "\n";
                if (intval($servicesDetails['time']) != 0) {
                    
                    $ical_data .= "DTEND;TZID=" . $timezone . ":" . date("Ymd\THi00", ($unixTime + (intval($servicesDetails['time']) * 60))) . "\n";
                    #$ical_data .= "DTEND:" . date("Ymd\THi00\Z", ($unixTime + ($serviceTime * 60))) . "\n";
                    
                } else {
                    
                    $ical_data .= "DTEND;TZID=" . $timezone . ":" . date("Ymd\THi00", $unixTime) . "\n";
                    #$ical_data .= "DTEND:" . date("Ymd\THi00\Z", $unixTime) . "\n";
                   
                }
                
            } else {
                
                $accommodationDetails = json_decode($customer['accommodationDetails'], true);
                #$ical_data .= "DTSTART;TZID=" . $timezone . ":" . date("Ymd\T12i00", $accommodationDetails['checkIn']) . "\n";
                #$ical_data .= "DTEND;TZID=" . $timezone . ":" . date("Ymd\T12i00", $accommodationDetails['checkOut']) . "\n";
                $ical_data .= "DTSTART;VALUE=DATE:" . date("Ymd", $accommodationDetails['checkIn']) . "\n";
                $ical_data .= "DTEND;VALUE=DATE:" . date("Ymd", $accommodationDetails['checkOut']) . "\n";
                
            }
            
            $ical_data .= "DTSTAMP:" . date("Ymd\THi00\Z") . "\n";
            #$ical_data .= "CREATED;TZID=" . $timezone . ":" . date("Ymd\THi00", $bookingDetail['reserveTime']) . "\n";
            $ical_data .= "CREATED:" . date("Ymd\THi00\Z", $customer['reserveTime']) . "\n";
            $ical_data .= "STATUS:CONFIRMED\n";
            $ical_data .= "SEQUENCE:0\n";
            $ical_data .= "SUMMARY:" . trim($emailSubject) . "\n";
            $ical_data .= "LOCATION:" . trim($locationForIcalendar) . "\n";
            $ical_data .= "DESCRIPTION:" . trim($emailBody) . "\n";
            $ical_data .= "END:VEVENT\n";
            $ical_data .= "END:VCALENDAR\n";
            
            $temp_file = tempnam(sys_get_temp_dir(), 'ical_')  . '.ics';
            $temp_file_name = basename($temp_file);
            
            file_put_contents($temp_file, $ical_data);
            
            if ($type === 'attach') {
                
                return array('temp_file' => $temp_file, 'temp_file_name' => $temp_file_name, 'status' => true);
                
            } else if ($type === 'download') {
                
                $file_mime_type = 'text/calendar';
                header('Content-Type: ' . $file_mime_type);
                header('Content-Disposition: attachment; filename="' . $temp_file_name . '"');
                header('Content-Length: ' . filesize($temp_file));
                
                readfile($temp_file);
                unlink($temp_file);
                
            }
            
            
            
        }
        
        
    }



?>