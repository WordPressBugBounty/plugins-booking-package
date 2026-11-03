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
                        
                        const errorMessage = (data && data.message) ? data.message : 'Failed to retrieve upgrade information.';
                        console.error('Failed to retrieve upgrade information:', errorMessage);
                        window.alert(errorMessage);
                        
                        if (submitBtn) {
                            
                            submitBtn.disabled = false;
                            
                        }
                        
                    }
                    
                });
                
            });
            
        }
        
    }
    
    function Add_on(nonce, action, url) {
        
        var object = this;
        object.download();
        
    }
    
    Add_on.prototype.download = function () {
        
        var btn = document.getElementById('booking-package-addon-install-btn');
        var form = document.getElementById('booking-package-addon-addon-form');
        var statusEl = document.getElementById('booking-package-addon-install-status');
        var fallbackEl = document.getElementById('booking-package-addon-manual-fallback');
        
        if (!btn || !form || typeof booking_package_install_addon_data === 'undefined') {
            return;
        }

        var config = booking_package_install_addon_data;

        btn.addEventListener('click', function() {
            btn.disabled = true;
            statusEl.style.display = 'block';
            statusEl.textContent = config.text_downloading || 'Downloading add-on...';
            
            fetch(config.download_url, { method: 'GET', mode: 'cors' })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Download failed with status: ' + response.status);
                    }
                    return response.blob();
                })
                .then(function(blob) {
                    statusEl.textContent = config.text_installing || 'Installing and activating add-on...';
                    var formData = new FormData(form);
                    
                    formData.set('pluginzip', blob, 'booking-package-addon.zip');
                    formData.append('is_ajax', '1');

                    var postUrl = form.getAttribute('action');

                    return fetch(postUrl, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data && data.success && data.data && data.data.redirect_url) {
                        statusEl.textContent = config.text_completed || 'Completed! Redirecting...';
                        window.location.href = data.data.redirect_url;
                    } else {
                        var errorMsg = (data && data.data && data.data.message) ? data.data.message : (config.text_failed || 'Installation failed.');
                        throw new Error(errorMsg);
                    }
                })
                .catch(function(error) {
                    console.error('Booking Package Addon Install Error:', error);
                    btn.style.display = 'none';
                    statusEl.style.display = 'none';
                    if (fallbackEl) {
                        fallbackEl.style.display = 'block';
                    }
                });
        });
        
    };
    
    
    