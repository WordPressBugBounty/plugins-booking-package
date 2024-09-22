/* globals booking_package_js_files */
/* globals Booking_Package */
/* globals Booking_package_user_function */
/* globals scriptError */
/* globals reservation_info */
/* globals booking_package_dictionary */
/* globals textOfErrorContent */
/* globals textOfErrorID */

	var booking_Package = null;
	var bookingPackageUserFunction = null;
    var start_booking_package = document.getElementsByClassName('start_booking_package');
	if (start_booking_package.length > 1) {
		
		for (var i = 0; i < start_booking_package.length; i++) {
			
			if (i > 0) {
				
				start_booking_package[i].textContent = null;
				start_booking_package[i].id += '_falsed';
				start_booking_package[i].classList.remove('hidden_panel');
				var error_booking_Package_id = start_booking_package[i].getAttribute('data-ID');
				
				var errorContent = document.createElement('p');
				errorContent.textContent = textOfErrorContent;
				
				var errorID = document.createElement('p');
				errorID.textContent = String(textOfErrorID).replace(/%s/g, error_booking_Package_id);
				
				var shortcode_error = document.createElement('div');
				shortcode_error.classList.add('shortcode_error');
				shortcode_error.appendChild(errorContent);
				shortcode_error.appendChild(errorID);
				start_booking_package[i].appendChild(shortcode_error);
				
			}
			
		}
		
	}
	
	
	function loadScript(id, src) {
		return new Promise((resolve, reject) => {
			
			const script = document.createElement('script');
			script.id = id;
			script.src = src;
			script.onload = resolve;
			script.onerror = reject;
			document.head.appendChild(script);
			
		});
	};
	
	async function loadScripts(unloadedFiles) {
		
		for (var id in unloadedFiles) {
			
			await loadScript(id, unloadedFiles[id]);
			
		}
		
	};
	
	function Booking_Package_LOAD_PLUGIN(reservation_info, booking_package_dictionary) {
		
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		if (typeof reservation_info.googleAnalytics == 'string') {
			
			gtag('js', new Date());
			gtag('config', reservation_info.googleAnalytics);
			
		}
		
		booking_Package = new Booking_Package(reservation_info, {status: 0}, booking_package_dictionary);
		booking_Package.setGtag(gtag);
		var locale_calendar = document.getElementById(reservation_info.uniqueID);
        if (booking_Package._myBookingDetailsBool == true) {
            
            if (parseInt(booking_Package._memberSetting.function_for_member) == 1 && parseInt(booking_Package._memberSetting.reject_non_membder) == 1 && parseInt(booking_Package._memberSetting.login) == 0) {
                
                locale_calendar.classList.remove('hidden_panel');
                return null;
                
            }
            
            locale_calendar.classList.remove('hidden_panel');
            booking_Package.myBookingDetails();
            
        } else {
            
            if (parseInt(booking_Package._memberSetting.function_for_member) == 1 && parseInt(booking_Package._memberSetting.reject_non_membder) == 1 && parseInt(booking_Package._memberSetting.login) == 0) {
                
                locale_calendar.classList.remove('hidden_panel');
                return null;
                
            }
            
            booking_Package.getReservationData(parseInt(reservation_info['month']), parseInt(reservation_info['day']), parseInt(reservation_info['year']), parseInt(reservation_info['accountKey']), false, true, true, null);
            
        }
        
        bookingPackageUserFunction = new Booking_package_user_function(reservation_info.accountKey);
        booking_Package.setBookingPackageUserFunction(bookingPackageUserFunction);
        try {
            
            var bookingPackageUserFunctionEvent = new Event('bookingPackageUserFunction');
            window.dispatchEvent(bookingPackageUserFunctionEvent);
            
        } catch (e) {
            
        }
        
        window.addEventListener('resize', function(event) {
            
            if (typeof booking_Package == 'object') {
                
                var top = booking_Package.getHeaderHeight(false);
                booking_Package.changeElementTop(top);
                
            }
            
        });
        
        window.addEventListener('scroll', function(event) {
            
            if (typeof booking_Package == 'object') {
                
                var top = booking_Package.getHeaderHeight(false);
                booking_Package.changeElementTop(top);
                
            }
            
        });
        
        window.addEventListener('error', function(event) {
            
            var script_error = new scriptError(reservation_info, booking_package_dictionary, event.message, event.filename, event.lineno, event.colno, event.error, true);
            if (booking_Package.getFunction() != null) {
                
                script_error.setFunction(booking_Package.getFunction());
                script_error.setResponseText(booking_Package.getResponseText());
                script_error.send();
                
            }
            
        }, false);
        
        return true;
		
	};
	
	
	document.addEventListener('DOMContentLoaded', function() {
	    
	    window.addEventListener('load', function() {
			
			var unloadedFile = 0;
			var unloadedFiles = {};
			for (var id in booking_package_js_files) {
				
				if (document.getElementById(id) == null) {
					
					unloadedFile++;
					unloadedFiles[id] = booking_package_js_files[id];
					console.error('Not found JS file with ' + booking_package_js_files[id]);
					
				}
				
			}
			
			if (unloadedFile > 0) {
				
				console.log('Retry downloading JS files.');
				loadScripts(unloadedFiles).then( () => {
					
					console.log('Downloaded JS files');
					var loaded_plugin = Booking_Package_LOAD_PLUGIN(reservation_info, booking_package_dictionary);
					
				});
				
			} else {
				
				var loaded_plugin = Booking_Package_LOAD_PLUGIN(reservation_info, booking_package_dictionary);
				
			}
			
	    });
	    
	});
	
	var reCAPTCHA_by_google_for_booking_package = function(token) {
		
		booking_Package.lockBooking(false, token, 'ReCAPTCHA');
		
	};
	
	var expired_reCAPTCHA_by_google_for_booking_package = function() {
		
		booking_Package.lockBooking(true, null, 'ReCAPTCHA');
		
	};
	
	var error_reCAPTCHA_by_google_for_booking_package = function(response) {
		
		booking_Package.lockBooking(true, null, 'ReCAPTCHA');
		
	};
	
	var hCaptcha_for_booking_package = function(token) {
		
		booking_Package.lockBooking(false, token, 'hCaptcha');
		
	};
	
	var expired_hCaptcha_for_booking_package = function() {
		
		booking_Package.lockBooking(true, null, 'hCaptcha');
		
	};
	
	var error_hCaptcha_for_booking_package = function(response) {
		
		booking_Package.lockBooking(true, null, 'hCaptcha');
		
	};
	