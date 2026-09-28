    /* globals Booking_App_XMLHttp */
    /* globals Booking_Package_Elements */
    
    function Upgrade(nonce, action, url) {
        
        var object = this;
        object._nonce = nonce;
        object._action = action;
        object._url = url;
        object._webApp = false;
        
        object.getDetails();
        
    }
    
    Upgrade.prototype.getDetails = function() {
        
        const object = this;
        const post = {nonce: object._nonce, action: object._action, mode: 'getUpgradeSubscriptionDetails'};
        const element = new Booking_Package_Elements(0);
        const form = document.getElementById('booking_package_paid_subscription');
        if (form) {
            
            form.addEventListener('submit', function(e) {
                
                e.preventDefault();
                const submitBtn = form.querySelector('input[type="submit"]');
                if (submitBtn) {
                    
                    submitBtn.disabled = true;
                    
                }
                
                const xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(data) {
                    
                    console.log(data);
                    if (data.status === true) {
                        
                        for (const key in data) {
                            
                            if (data.hasOwnProperty(key)) {
                                
                                if (key === 'status') {
                                    
                                    continue;
                                    
                                }
                                
                                const input = element.createInputElement('input', 'hidden', key, data[key], null, false, null, null, null, null);
                                form.appendChild(input);
                                
                            }
                            
                        }
                        
                        console.log(form);
                        form.submit();
                        
                    } else {
                        
                        console.error('Failed to retrieve upgrade information.');
                        window.alert('Failed to retrieve upgrade information.');
                        
                        if (submitBtn) {
                            
                            submitBtn.disabled = false;
                            
                        }
                        
                    }
                    
                });
                
            });
            
        }
        
    }
    