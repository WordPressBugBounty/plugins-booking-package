/* globals scriptError */
/* globals I18n */
/* globals Booking_App_XMLHttp */
/* globals FORMAT_COST */
/* globals Booking_App_Calendar */
/* globals Booking_Package_Console */
/* globals Booking_Package_Elements */
/* globals Booking_Package_Input */
/* globals jQuery*/
    
    function Booking_Package_Customize(properties, booking_package_dictionary) {
        
        var object = this;
        this._debug = new Booking_Package_Console(properties.debug);
        this._console = {};
        this._console.log = this._debug.getConsoleLog();
        this._console.error = this._debug.getConsoleError();
        this._properties = properties;
        this._isExtensionsValid = parseInt(properties.isExtensionsValid);
        this._webApp = false;
        this._url = properties['url'];
        this._nonce = properties['nonce'];
        this._action = properties['action'];
        this._dateFormat = properties.dateFormat;
        this._clockFormat = properties.clockFormat;
        this._positionOfWeek = properties.positionOfWeek;
        this._positionTimeDate = properties.positionTimeDate;
        this._isExtensionsValid = parseInt(properties.isExtensionsValid);
        this._startOfWeek = properties.startOfWeek;
        this._currency = properties.currency;
        this._i18n = new I18n(properties.locale);
        this._i18n.setDictionary(booking_package_dictionary);
        this._weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        this._calendar = new Booking_App_Calendar(this._weekName, object._dateFormat, object._positionOfWeek, object._positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        this._element = new Booking_Package_Elements(properties.debug);
        this._loadingPanel = document.getElementById("loadingPanel");
        this._themes = 0;
        this._locale = properties.locale;
        this._numberFormatter = false;
        if (parseInt(properties.numberFormatter) === 1) {
            
            this._numberFormatter = true;
            
        }
        this._currencies = properties.currencies;
        this._currency_info = {locale: this._locale, currency: this._currency, info: this._currencies[this._currency]};
        this._format = new FORMAT_COST(this._i18n, this._debug, this._numberFormatter, this._currency_info);
        
    };
    
    Booking_Package_Customize.prototype.setThemes = function(themes) {
        
        this._themes = themes;
        
    }
    
    Booking_Package_Customize.prototype.customizeLabels = function(account, customizePanel, saveCallback, resetCallback) {
        
        var object = this;
        var customizeLabels = account.customizeLabels;
        var inputData = {};
        var disabled = false;
        var table = object.create('table', null, null, null, null, 'form-table', null);
        customizePanel.appendChild(table);
        var labels_name = {
            general_labels: object._i18n.get('General'), 
            timeSlot_labels: object._i18n.get('Time Slot Bookings'), 
            multiNight_Labels: object._i18n.get('Multi-night Bookings'), 
            form_labels: object._i18n.get('Form'), 
            user_labels: object._i18n.get('User'), 
        };
        
        var trList = {};
        for (var key in customizeLabels) {
            
            var directory_name = object.create('div', labels_name[key], null, null, null, null, null);
            var subLabels = customizeLabels[key];
            object._console.log(subLabels);
            object._console.log(Object.keys(subLabels).length);
            (function(directory_name, subLabels, table, trList) {
                
                var count = Object.keys(subLabels).length;
                
                var directory = true;
                for (var key in subLabels) {
                    
                    var inputObject = {key: key, name: object._i18n.get('Title of "%s"', [object._i18n.get(key)]), value: subLabels[key], inputType: 'TEXT', inputLimit: 1, target: 'both', option: 0};
                    object._console.log(inputObject);
                    var td_name = object.create('td', inputObject.name, null, null, null, null, null);
                    td_name.setAttribute("scope", "row");
                    var inputPanel = object.createInput(key, inputObject, inputData, account, disabled, null, object._isExtensionsValid);
                    var td = object.create('td', null, [inputPanel], null, null, null, null);
                    if (directory === true) {
                        
                        var td_directory = object.create('th', null, [directory_name], null, null, null, null);
                        td_directory.setAttribute("rowspan", count);
                        var tr = object.create('tr', null, [td_directory, td_name, td], null, null, null, null);
                        tr.setAttribute("valign", "top");
                        trList[key] = tr;
                        table.appendChild(tr);
                        directory = false;
                        
                    } else {
                        
                        var tr = object.create('tr', null, [td_name, td], null, null, null, null);
                        tr.setAttribute("valign", "top");
                        trList[key] = tr;
                        table.appendChild(tr);
                        
                    }
                    
                }
                
            })(directory_name, subLabels, table, trList);
            
        }
        
        var saveButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get('Save'));
        var resetButton = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get('Reset'));
        var buttonPanel = object.createButtonPanel(null, null, 'bottomButtonPanel', [saveButton, resetButton]);
        customizePanel.appendChild(buttonPanel);
        
        saveButton.onclick = function(event) {
            
            var customizeLabels = {};
            for (var key in inputData) {
                
                var value = inputData[key].textBox.value;
                customizeLabels[key] = value;
                
            }
            var jsonStr = JSON.stringify(customizeLabels);
            var post = {nonce: object._nonce, action: object._action, mode: 'updateCustomizeLabels', accountKey: account.key, customizeLabels: jsonStr};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel");
                saveCallback(json);
                
            }, function(text){
                
            });
            
        };
        
        resetButton.onclick = function(event) {
            
            var post = {nonce: object._nonce, action: object._action, mode: 'resetCustomizeLabels', accountKey: account.key};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel");
                resetCallback(json);
                
            }, function(text){
                
            });
            
        };
        
    };
    
    Booking_Package_Customize.prototype.customizeButtons = function(account, customizePanel, saveCallback, resetCallback) {
        
        var object = this;
        var customizeButtons = account.customizeButtons;
        var inputData = {};
        var disabled = false;
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = 10;
        var week = new Date(year, (month - 1), day).getDay();
        var labels = {select_date_button: object._i18n.get('Select a date'), return_button: object._i18n.get('Return'), next_button: object._i18n.get('Next'), apply_button: object._i18n.get('Apply'), next_page_button: object._i18n.get('Next page'), booking_verification_button: object._i18n.get('Verify'), book_now_button: object._i18n.get('Book now'), previous_available_day_button: object._i18n.get('Previous available day'), next_available_day_button: object._i18n.get('Next available day'), return_form_button: object._i18n.get('Return from the form'), cancel_booking_button: object._i18n.get('Cancel booking'), login_button: object._i18n.get('Sign in'), register_button: object._i18n.get('Register'), left_arrow_button: object._i18n.get('Left arrow'), right_arrow_button: object._i18n.get('Right arrow'), cancel_user_booking_button: object._i18n.get('Cancel user booking'), change_user_password_button: object._i18n.get('Change password'), update_user_button: object._i18n.get('Update Profile'), delete_user_button: object._i18n.get('Delete')
        };
        
        var setCustomizeCss = function(customizeButtons) {
            
            var css = '';
            for (var key in customizeButtons) {
                
                css += '#customizeButtonPanel' +  ' .' + key + " {\n";
                css += (function(buttons) {
                    
                    var css = '';
                    for (var name in buttons) {
                        
                        var value = buttons[name];
                        css += "\t" + name + ": " + value + ";\n";
                        
                    }
                    
                    return css;
                    
                })(customizeButtons[key]);
                css += "}\n";
                
            }
            var css = document.getElementById('booking-package_customizeButtons').innerHTML = css;
            
        };
        setCustomizeCss(customizeButtons);
        
        var keysWithHover = {};
        var keysWithoutHover = {};
        Object.keys(customizeButtons).forEach(function(key) {
            
            if (key.includes(':hover')) {
                
                keysWithHover[key] = customizeButtons[key];
                
            } else {
                
                keysWithoutHover[key] = customizeButtons[key];
                
            }
            
        });
        object._console.log(keysWithHover);
        object._console.log(keysWithoutHover);
        
        var table = object.create('table', null, null, 'customizeButtonPanel', null, 'form-table', null);
        customizePanel.appendChild(table);
        
        for (var key in keysWithoutHover) {
            
            object._console.log(key);
            var th = object.create('th', labels[key], null, null, 'vertical-align: top; font-weight: 600;', null, null);
            th.setAttribute("scope", "row");
            var switchPanel = object.create('div', null, null, null, null, 'switchButtonPanel', null);
            var classNamePanel = object.create('div', object._i18n.get('CSS class selector:') + ' .' + key, null, null, null, null, null);
            
            var textarea = object.create('div', null, null, 'customize_' + key, 'height: 20vh;', null, {key: key});
            var textareaPanel = object.create('div', null, [textarea], null, null, null, null);
            
            var textareaHover = object.create('div', null, null, 'customize_' + key + '_hover', 'height: 20vh;', null, {key: key + ':hover'});
            var textareaHoverPanel = object.create('div', null, [textareaHover], null, null, null, null);
            
            var previewLabel = object.create('div', object._i18n.get('Preview') + ':', null, null, null, 'previewLabel', []);
            var previewButton = object.createButton(null, null, key, null, labels[key]);
            if (key == 'previous_available_day_button') {
                
                var weekNum = new Date(year, (month - 1), 9).getDay();
                previewButton.textContent = this._i18n.get(object._weekName[weekNum]) + ' 9';
                
            } else if (key == 'next_available_day_button') {
                
                var weekNum = new Date(year, (month - 1), 11).getDay();
                previewButton.textContent = this._i18n.get(object._weekName[weekNum]) + ' 11';
                
            } else if (key == 'return_form_button') {
                
                previewButton.textContent = this._i18n.get('Return');
                
            } else if (key == 'cancel_user_booking_button') {
                
                previewButton.textContent = this._i18n.get('Cancel booking');
                
            } else if (key === 'left_arrow_button') {
                
                previewButton.textContent = this._i18n.get('navigate_before');
                previewButton.classList.add('material-icons');
                
            } else if (key === 'right_arrow_button') {
                
                previewButton.textContent = this._i18n.get('navigate_next');
                previewButton.classList.add('material-icons');
                
            }
            
            var previewPanel = object.create('div', null, [previewLabel, previewButton], null, null, 'previewPanel', null);
            var applyButton = object.createButton(null, 'width: 100%;', 'w3tc-button-save button-primary', null, object._i18n.get('Apply'));
            var td = object.create('td', null, [switchPanel, classNamePanel, textareaPanel, textareaHoverPanel, applyButton, previewPanel], null, null, null, null);
            var tr = object.create('tr', null, [th, td], null, null, null, []);
            tr.setAttribute("valign", "top");
            table.appendChild(tr);
            
            (function (key, customizeButtons, switchPanel, classNamePanel, textarea, textareaHover, previewButton, applyButton, setCustomizeCss) {
                
                var getStyle = function(key, customizeButtons) {
                    
                    var style = '';
                    if (customizeButtons[key] != null) {
                        
                        var styles = customizeButtons[key];
                        for (var name in styles) {
                            
                            style += name.trim() + ': ' + styles[name].trim() + ";\n";
                            
                        }
                        
                    }
                    return style;
                    
                };
                
                var editor = null;
                var editor_hover = null;
                require.config({
                    paths: {
                        'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs'
                    }
                });
                
                require(['vs/editor/editor.main'], function () {
                    
                    editor = monaco.editor.create(document.getElementById('customize_' + key), {
                        value: getStyle(key, customizeButtons),
                        language: 'css',
                        theme: 'vs-light'
                    });
                    
                    editor_hover = monaco.editor.create(document.getElementById('customize_' + key + '_hover'), {
                        value: getStyle(key + ':hover', customizeButtons),
                        language: 'css',
                        theme: 'vs-light'
                    });
                    
                    var textareaPanel = textarea.parentNode;
                    var textareaHoverPanel = textareaHover.parentNode;
                    textareaHoverPanel.classList.add('hidden_panel');
                    
                });
    			
                window.addEventListener('resize', () => {
                    
                    let hasHiddenOnTextarea = false;
                    let hasHiddenOnTextareaHover = false;
                    if (textareaPanel.classList.contains('hidden_panel')) {
                        
                        hasHiddenOnTextarea = true;
                        textareaPanel.classList.remove('hidden_panel');
                        
                    }
                    
                    if (textareaHoverPanel.classList.contains('hidden_panel')){
                        
                        hasHiddenOnTextareaHover = true;
                        textareaHoverPanel.classList.remove('hidden_panel');
                        
                    }
                    
                    editor.layout();
                    editor_hover.layout();
                    
                    if (hasHiddenOnTextarea === true) {
                        
                        textareaPanel.classList.add('hidden_panel');
                        
                    }
                    
                    if (hasHiddenOnTextareaHover === true) {
                        
                        textareaHoverPanel.classList.add('hidden_panel');
                        
                    }
                    
                    /**
                    let hasHiddenOnTextarea = false;
                    let hasHiddenOnTextareaHover = false;
                    if (textareaPanel.classList.contains('hidden_panel')) {
                        
                        hasHiddenOnTextarea = true;
                        textareaPanel.classList.remove('hidden_panel');
                        
                    }
                    
                    if (textareaHoverPanel.classList.contains('hidden_panel')){
                        
                        hasHiddenOnTextareaHover = true;
                        textareaHoverPanel.classList.remove('hidden_panel');
                        
                    }
                    
                    var timer = setInterval(function(){
                        
                        console.log('resize 2');
                        
                        let hasHiddenOnTextarea = false;
                        let hasHiddenOnTextareaHover = false;
                        if (textareaPanel.classList.contains('hidden_panel')) {
                            
                            hasHiddenOnTextarea = true;
                            
                            
                        }
                        
                        if (textareaHoverPanel.classList.contains('hidden_panel')){
                            
                            hasHiddenOnTextareaHover = true;
                            
                            
                        }
                        
                        textareaPanel.classList.remove('hidden_panel');
                        textareaHoverPanel.classList.remove('hidden_panel');
                        
                        console.log('hasHiddenOnTextarea = ' + hasHiddenOnTextarea);
                        console.log('hasHiddenOnTextareaHover = ' + hasHiddenOnTextareaHover);
                        console.log(editor.getContentWidth());
                        editor.layout();
                        editor_hover.layout();
                        
                        if (hasHiddenOnTextarea === true) {
                            
                            textareaPanel.classList.add('hidden_panel');
                            
                        }
                        
                        if (hasHiddenOnTextareaHover === true) {
                            
                            textareaHoverPanel.classList.add('hidden_panel');
                            
                        }
                        
                        clearInterval(timer);
                        
                    }, 1000);
                    
                    
                    console.log('resize 1');
                    editor.layout();
                    editor_hover.layout();
                    
                    if (hasHiddenOnTextarea === true) {
                        
                        textareaPanel.classList.add('hidden_panel');
                        
                    }
                    
                    if (hasHiddenOnTextareaHover === true) {
                        
                        textareaHoverPanel.classList.add('hidden_panel');
                        
                    }
                    
                    **/
                    
                });
                
                applyButton.onclick = function(event) {
                    
                    textarea.value = editor.getValue();
                    textareaHover.value = editor_hover.getValue();
                    var textareas = [textarea, textareaHover];
                    for (var textIndex = 0; textIndex < textareas.length; textIndex++) {
                        
                        var key = textareas[textIndex].getAttribute('data-key');
                        var value = textareas[textIndex].value.replace(/[\n\t]/g, ' ');
                        value = value.replace(/\/\*\*(.|\n)*?\*\*\//g, '');
                        var styleObject = {};
                        if (value) {
                            
                            var stylePairs = value.split(';');
                            for (var i = 0; i < stylePairs.length; i++) {
                                
                                var pair = stylePairs[i].split(':');
                                if (pair.length === 2) {
                                    
                                    styleObject[pair[0].trim()] = pair[1].trim();
                                    
                                }
                                
                            }
                            
                        }
                        object._console.log(styleObject);
                        customizeButtons[key] = styleObject;
                        
                    }
                    setCustomizeCss(customizeButtons);
                    object._console.log(customizeButtons);
                    
                };
                
                var textareaPanel = textarea.parentNode;
                var textareaHoverPanel = textareaHover.parentNode;
                //textareaHoverPanel.classList.add('hidden_panel');
                
                var mainClass = object.createButton(null, null, 'selectedButton', {key: key}, object._i18n.get('Class'));
                var hoverClass = object.createButton(null, null, null, {key: key + ':hover'}, object._i18n.get('Pseudo-class') + ' :hover');
                switchPanel.appendChild(mainClass);
                switchPanel.appendChild(hoverClass);
                mainClass.onclick = function() {
                    
                    classNamePanel.textContent = object._i18n.get('CSS class selector:') + ' .' + this.getAttribute('data-key');
                    this.classList.add('selectedButton');
                    hoverClass.classList.remove('selectedButton');
                    textareaPanel.classList.remove('hidden_panel');
                    textareaHoverPanel.classList.add('hidden_panel');
                    
                };
                
                hoverClass.onclick = function() {
                    
                    classNamePanel.textContent = object._i18n.get('CSS class selector:') + ' .' + this.getAttribute('data-key');
                    this.classList.add('selectedButton');
                    mainClass.classList.remove('selectedButton');
                    textareaPanel.classList.add('hidden_panel');
                    textareaHoverPanel.classList.remove('hidden_panel');
                    
                };
                
            })(key, customizeButtons, switchPanel, classNamePanel, textarea, textareaHover, previewButton, applyButton, setCustomizeCss);
            
        }
        
        var saveButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get('Save'));
        var resetButton = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get('Reset'));
        var buttonPanel = object.createButtonPanel(null, null, 'bottomButtonPanel', [saveButton, resetButton]);
        customizePanel.appendChild(buttonPanel);
        
        saveButton.onclick = function(event) {
            
            object._console.log(customizeButtons);
            var jsonStr = JSON.stringify(customizeButtons);
            var post = {nonce: object._nonce, action: object._action, mode: 'updateCustomizeButtons', accountKey: account.key, customizeButtons: jsonStr};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel");
                saveCallback(json);
                
            }, function(text){
                
            });
            
        };
        
        resetButton.onclick = function(event) {
            
            var post = {nonce: object._nonce, action: object._action, mode: 'resetCustomizeButtons', accountKey: account.key};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
                resetCallback(json);
                
            }, function(text){
                
            });
            
        };
        
        
    };
    
    Booking_Package_Customize.prototype.customizeLayouts = function(account, customizePanel, saveCallback, resetCallback) {
        
        var object = this;
        var customizeLayouts = account.customizeLayouts;
        var inputData = {};
        var disabled = false;
        if (parseInt(account.customizeLabelsBool) === 0) {
            
            disabled = true;
            
        }
        object._console.log(customizeLayouts);
        
        var setCss = function(customizeLayouts) {
            
            object._console.log(customizeLayouts);
            var styleTag = document.getElementById('booking-package_customizeStyle');
            var styles = {
                'booking-package_calendarPage': {
                    calendarHeader: ['background-color', 'color'], 
                    week_slot: ['background-color', 'color', 'border-color'], 
                    day_slot: ['background-color', 'color', 'border-color']
                },
                'booking-package_durationStay': {
                    bookingDetailsTitle: ['background-color', 'border-color', 'color'],
                     row: ['background-color', 'color', 'border-color'],
                },
                'booking-package_servicePage': {
                    title: ['background-color', 'border-color', 'color'], 
                    selectable_service_slot: ['background-color', 'border-color', 'color'], 
                },
                'booking-package_serviceDetails': {
                    title: ['background-color', 'border-color', 'color'], 
                    row: ['background-color', 'color', 'border-color'],
                },
                'booking-package_schedulePage': {
                    topPanel: ['background-color', 'border-color', 'color'], 
                    selectable_day_slot: ['background-color', 'border-color', 'color'], 
                    selectable_service_slot: ['background-color', 'border-color', 'color'], 
                    selectable_time_slot: ['background-color', 'border-color', 'color'], 
                    selectPanelError: ['background-color', 'border-color']
                },
                'booking-package_inputFormPanel': {
                    title_in_form: ['background-color', 'border-color', 'color'], 
                    row: ['background-color', 'color', 'border-color']
                },
            };
            var cssClass = '';
            for (var id in customizeLayouts) {
                
                var customizeLayout = customizeLayouts[id];
                if (id === 'general') {
                    
                    for (var key in styles) {
                        
                        var classes = styles[key];
                        cssClass += '#' + key + " {background-color: " + customizeLayout['background-color'] + "; font-size: " + customizeLayout['font-size'] + ";}\n";
                        cssClass += (function(id, classes, customizeLayout) {
                            
                            var className = '';
                            for (var key in classes) {
                                
                                className += '#' + id + ' .' + key + " {\n";
                                for (var i = 0; i < classes[key].length; i++) {
                                    
                                    var styleName = classes[key][i];
                                    var style = "\t" + styleName + ': ' + customizeLayout[styleName] + ";\n";
                                    className += style;
                                    
                                }
                                
                                className += "}\n";
                                
                            }
                            
                            return className;
                            
                        })(key, classes, customizeLayout);
                        
                    }
                    
                    cssClass += "#customizeLayoutPanel .td_background_color { background-color: " + customizeLayout['background-color'] + "; }\n";
                    
                    
                } else if (id === 'calendar' || id === 'bookingDetails' || id === 'service' || id === 'timeSlot' || id == 'form') {
                    
                    var idName = ['booking-package_calendarPage'];
                    if (id === 'calendar') {
                        
                        idName = ['booking-package_calendarPage'];
                        
                    } else if (id === 'bookingDetails') {
                        
                        idName = ['booking-package_durationStay', 'summaryListPanel'];
                        
                    } else if (id === 'service' /**|| id === 'timeSlot'**/ ) {
                        
                        idName = ['booking-package_schedulePage', 'booking-package_servicePage', 'booking-package_serviceDetails'];
                        
                    } else if (id == 'timeSlot') {
                        
                        idName = ['scheduleMainPanel'];
                        
                    } else if (id == 'form') {
                        
                        idName = ['booking-package_inputFormPanel'];
                        
                    }
                    
                    for (var i = 0; i < idName.length; i++) {
                        
                        for (var name in customizeLayout) {
                            
                            cssClass += '#' + idName[i] + ' .' + name + " {\n";
                            cssClass += (function(styles) {
                                
                                var styleLines = '';
                                for (var name in styles) {
                                    
                                    styleLines += "\t" + name + ": " + styles[name] + ";\n";
                                    
                                }
                                return styleLines;
                                
                            })(customizeLayout[name]);
                            cssClass += "}\n";
                            
                        }
                        
                    }
                    
                }
                
            }
            
            //object._console.log(cssClass);
            styleTag.innerHTML = cssClass;
            
        };
        /**
        var createTimeSlot = function(key, hour, min) {
            
            var time = object.create('span', object._calendar.getPrintTime(("0" + hour).slice(-2), ("0" + min).slice(-2)) + " ", null, null, null, 'timeSlot', null);
            var title = object.create('span', null, null, null, null, 'subtitle hidden_panel', null);
            var slot = object.create('span', object._i18n.get('%s slots left', [10]), null, null, null, 'remainingSlots', null);
            var timeSlotPanel = object.create('div', null, [time, title, slot], null, null, 'selectable_time_slot', {id: 'timeSlot'} );
            return timeSlotPanel;
            
        };
        **/
        var createServicePanel = function (services, servicePanels, optionIndex, options, added) {
            
            var clickActions = [];
            for (var serviceKey in services) {
                
                var classNames = ['selectable_service_slot', 'selectable_service_slot:hover', 'selected_element', 'serviceCost', 'descriptionOfService'];
                var courseNamePanel = object.create('span', services[serviceKey], null, null, null, null, null);
                var checkBox = object.createInputElement('input', 'checkbox', null, "", null, null, null, null, 'hidden_panel', null);
                var label = object.create('span', null, [checkBox, courseNamePanel], null, null, null, null);
                var coursePanel = object.create('div', null, [label], null, null, 'service_details', {status: '1'} );
                if (parseInt(serviceKey) != 0) {
                    
                    var courseCostPanel = object.create('div', object._format.formatCost(parseInt(serviceKey), object._currency), null, null, null, 'serviceCost maximumAndMinimum', null);
                    coursePanel.appendChild(courseCostPanel);
                    
                }
                var table_row = object.create('div', null, [coursePanel], null, null, 'selectable_service_slot', {id: 'service', option: '0', name: services[serviceKey]} );
                servicePanels.appendChild(table_row);
                if (options != null && serviceKey == optionIndex) {
                    
                    classNames.push('selectable_option_element');
                    classNames.push('selected_option_element');
                    table_row.classList.add('selected_element');
                    table_row.setAttribute('data-option', '1');
                    var selectOptionList = object.create('div', null, null, null, null, 'selectOptionList', null);
                    for (var optionKey in options) {
                        
                        var optionName = object.create('span', options[optionKey], null, null, null, null, null);
                        var optionCost = object.create('span', object._format.formatCost(parseInt(optionKey), object._currency), null, null, null, 'serviceCost', null);
                        var optionPanel = object.create('div', null, [optionName, optionCost], null, null, 'selectable_option_element', null);
                        coursePanel.appendChild(optionPanel);
                        if (optionKey == 500) {
                            
                            optionPanel.classList.add('selected_option_element');
                            
                        }
                        
                    }
                    
                }
                
                clickActions.push( {id: 'service', element: table_row, classes: classNames} );
                added(table_row);
                
            }
            
            return clickActions;
            
        };
        
        var createDayListPanel = function(daysListPanel, month, day, year) {
            
            var clickActions = [];
            for (var i = 7; i < 14; i++) {
                
                var weekNum = new Date(year, (month - 1), i).getDay();
                var weekPanel = object.create('div', object._i18n.get(object._weekName[weekNum]), null, null, null, 'week_slot ' + weekNum + '_OfWeek', null);
                var dayPanel = object.create('div', i, null, null, null, null, null);
                var weekDaysPanel = object.create('div', null, [weekPanel, dayPanel], null, null, null, {status: '1', id: 'service'} );
                weekDaysPanel.classList.add("selectable_day_slot");
                var classNames = ['selectable_day_slot', 'selectable_day_slot:hover'];
                if (day < i ) {
                    
                    classNames.push('closed');
                    weekDaysPanel.classList.add("closed");
                    
                }
                
                if (i == 10) {
                    
                    classNames.push('selected_day_slot');
                    weekDaysPanel.classList.add("selected_day_slot");
                    
                }
                clickActions.push( {id: 'service', element: weekDaysPanel, classes: classNames} );
                daysListPanel.appendChild(weekDaysPanel);
                weekDaysPanel.onclick = function() {
                    
                    var id = this.getAttribute('data-id');
                    var classNames = ['selectable_day_slot', 'selectable_day_slot:hover'];
                    if (this.classList.contains('closed') === true) {
                        
                        classNames.push('closed');
                        
                    }
                    
                    if (this.classList.contains('selected_day_slot') === true) {
                        
                        classNames.push('selected_day_slot');
                        
                    }
                    object._console.log(classNames);
                    object.editCssPanel(id, customizeLayouts, classNames, function() {
                        
                        setCss(customizeLayouts);
                        
                    });
                    
                };
                
            }
            
            return clickActions;
            
        };
        
        var createTimeSlotPanel = function(scheduleMainPanel, className) {
            
            var clickActions = [];
            for (var i = 10; i < 18; i++) {
                
                var time = object.create('span', object._calendar.getPrintTime(("0" + i).slice(-2), ("00")) + " ", null, null, null, 'timeSlot', null);
                var title = object.create('span', null, null, null, null, 'subtitle hidden_panel', null);
                var slot = object.create('span', object._i18n.get('%s slots left', [10]), null, null, null, 'remainingSlots', null);
                var timeSlotPanel = object.create('div', null, [time, title, slot], null, null, 'selectable_time_slot', {id: 'timeSlot'} );
                clickActions.push( {id: 'timeSlot', element: timeSlotPanel, classes: ['selectable_time_slot', 'selectable_time_slot:hover', 'timeSlot', 'subtitle', 'remainingSlots'] } );
                scheduleMainPanel.appendChild(timeSlotPanel);
                timeSlotPanel.onclick = function() {
                    
                    var id = this.getAttribute('data-id');
                    object.editCssPanel(id, customizeLayouts, ['selectable_time_slot', 'selectable_time_slot:hover', 'closed', 'timeSlot', 'subtitle', 'remainingSlots'], function() {
                        
                        setCss(customizeLayouts);
                        
                    });
                    
                };
                
            }
            
            return clickActions;
            
        };
        
        var table = object.create('table', null, null, 'customizeLayoutPanel', null, 'form-table', null);
        customizePanel.appendChild(table);
        
        var labels_name = { general: object._i18n.get("General"), calendar: object._i18n.get("Calendar"), serviceAndTimeSlot: object._i18n.get('Service and Time slot'), form: object._i18n.get('Form'), service: object._i18n.get('Service'), timeSlot: object._i18n.get('Time slot'), };
        var general_names = { 'font-size': object._i18n.get('Font size'), color: object._i18n.get('Font color'), 'background-color': object._i18n.get('Background color'), 'border-color': object._i18n.get('Border color') };
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = 10;
        var week = new Date(year, (month - 1), day).getDay();
        object._console.log(day);
        for (var key in customizeLayouts) {
        
            var customizeLayout = customizeLayouts[key];
            var th = object.create('th', labels_name[key], null, null, 'vertical-align: top; font-weight: 600;', null, null);
            th.setAttribute("scope", "row");
            var td = document.createElement("td");
            var tr = document.createElement("tr");
            tr.setAttribute("valign", "top");
            if (key === 'general') {
                
                object._console.log(customizeLayout);
                
                th.setAttribute('rowspan', Object.keys(customizeLayout).length);
                var thBool = true;
                for (var nameKey in customizeLayout) {
                    
                    var nameTd = object.create('td', general_names[nameKey], null, null, null, null, null);
                    var valueTd = document.createElement("td");
                    var tr = document.createElement("tr");
                    tr.setAttribute("valign", "top");
                    if (thBool === true) {
                        
                        tr.appendChild(th);
                        thBool = false;
                        
                    }
                    tr.appendChild(nameTd);
                    tr.appendChild(valueTd);
                    table.appendChild(tr);
                    
                    var input = object.createInputElement('input', 'text', null, customizeLayout[nameKey], null, null, nameKey, null, null, {key: nameKey} );
                    valueTd.appendChild(input);
                    if (nameKey.includes("color")) {
                        
                        (function(id, nameKey, customizeLayouts, currentColor) {
                            
                            (function( $ ) {
                                
                                $(function() {
                                    
                                    $('#' + nameKey).wpColorPicker({
                                        defaultColor: false,
                                        change: function(event, ui){
                                            
                                            var key = this.getAttribute("data-key");
                                            var color = "#" + ui.color._color.toString(16);
                                            customizeLayouts[id][key] = color;
                                            setCss(customizeLayouts);
                                            
                                        },
                                        clear: function(){
                                            
                                        }
                                        
                                    });
                                    
                                });
                                
                            })( jQuery );
                            
                        })(key, nameKey, customizeLayouts, customizeLayout[nameKey]);
                        
                    } else {
                        
                        (function(id, input, customizeLayout) {
                            
                            input.onchange = function() {
                                
                                object._console.log(this);
                                var key = this.getAttribute('data-key');
                                customizeLayouts[id][key] = this.value;
                                setCss(customizeLayouts);
                                
                            };
                            
                        })(key, input, customizeLayout);
                        
                    }
                    
                }
                
            } else if (key === 'calendar') {
                
                var topPanel = object._calendar.createHeader(month, year, 0, true);
                var childElements = topPanel.children;
                object._console.log(childElements);
                (function(childElements) {
                    
                    for (var i = 0;i < childElements.length; i++) {
                        
                        var childElement = childElements[i];
                        childElement.setAttribute('style', 'cursor: pointer;');
                        childElement.setAttribute('data-key', key);
                        childElement.onclick = function() {
                            
                            var childElement = this;
                            var className = this.getAttribute('class');
                            var key = this.getAttribute('data-key');
                            object.editCssPanel(key, customizeLayouts, [className], function() {
                                
                                setCss(customizeLayouts);
                                
                            });
                            
                        };
                        
                    }
                    
                })(childElements);
                var calendarPanel = document.createElement('div');
                var inputPanel = object.create('div', null, [topPanel, calendarPanel], 'booking-package_calendarPage', null, null, null);
                td.appendChild(inputPanel);
                td.classList.add('td_background_color');
                td.setAttribute('colspan', 2);
                tr.appendChild(th);
                tr.appendChild(td);
                table.appendChild(tr);
                object._calendar.setShortMonthNameBool(false);
                object._calendar.setShortWeekNameBool(true);
                var calendarData = object._calendar.createCalendarData(month, year);
                var returnLabel = document.createElement("label");
                var nextLabel = document.createElement("label");
                if (topPanel.querySelector('#change_calendar_return') != null) {
                    
                    returnLabel = topPanel.querySelector('#change_calendar_return');
                    
                }
                
                if (topPanel.querySelector('#change_calendar_next') != null) {
                    
                    nextLabel = topPanel.querySelector('#change_calendar_next');
                    
                }
                
                var weekClass = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                var days = {};
                object._calendar.create(calendarPanel, calendarData, calendarData.date.month, 1, calendarData.date.year, '', function(callback){
                    
                    var classNames = ['day_slot', 'dateField'];
                    if (callback.day >= 1 && callback.day < 10 && month == callback.month) {
                        
                        object._console.log(callback);
                        classNames.push('pastDay');
                        classNames.push('pastDay > .dateField');
                        callback.eventPanel.classList.remove('available_day');
                        callback.eventPanel.classList.add('pastDay');
                        
                    } else {
                        
                        classNames.push('available_day:hover');
                        classNames.push('available_day:hover .dateField');
                        callback.eventPanel.classList.add('available_day');
                        
                    }
                    
                    if (account.type === 'hotel') {
                        
                        if (callback.day >= 16 && callback.day < 20 && month == callback.month) {
                            
                            classNames.push('selected_day_slot');
                            callback.eventPanel.classList.add('selected_day_slot');
                            if (callback.day === 16) {
                                
                                classNames.push('selected_start_day');
                                classNames.push('selected_start_day > .dateField');
                                callback.eventPanel.classList.add('selected_start_day');
                                
                            } else if (callback.day === 19) {
                                
                                classNames.push('selected_end_day');
                                classNames.push('selected_end_day > .dateField');
                                callback.eventPanel.classList.add('selected_end_day');
                                
                            } else {
                                
                                classNames.push('selected_day_range');
                                classNames.push('selected_day_range > .dateField');
                                callback.eventPanel.classList.add('selected_day_range');
                                
                            }
                            
                        }
                        
                        if (callback.day === 12) {
                            
                            classNames.push('startDateOfFullRoom');
                            classNames.push('closingDay');
                            callback.eventPanel.querySelector('.dateField').classList.add('startDateOfFullRoom');
                            callback.eventPanel.classList.add('closingDay');
                            
                        } else if (callback.day === 13) {
                            
                            classNames.push('dateOfFullRoom');
                            classNames.push('closingDay');
                            callback.eventPanel.querySelector('.dateField').classList.add('dateOfFullRoom');
                            callback.eventPanel.classList.add('closingDay');
                            
                        } else if (callback.day === 14) {
                            
                            classNames.push('endDateOfFullRoom');
                            callback.eventPanel.querySelector('.dateField').classList.add('endDateOfFullRoom');
                            
                        }
                        
                    }
                    
                    (function(panel, classNames) {
                        
                        panel.onclick = function() {
                            
                            object.editCssPanel('calendar', customizeLayouts, classNames, function() {
                                
                                setCss(customizeLayouts);
                                
                            });
                            
                        };
                        
                    })(callback.eventPanel, classNames);
                    
                    
                    
                });
                
                var weekPanels = calendarPanel.querySelectorAll('.week_slot');
                (function(weekPanels) {
                    
                    for (var i = 0; i < weekPanels.length; i++) {
                        
                        weekPanels[i].setAttribute('data-id', key);
                        weekPanels[i].setAttribute('style', 'cursor: pointer;');
                        weekPanels[i].onclick = function() {
                             
                             var id = this.getAttribute('data-id');
                             var className = this.getAttribute('class');
                             object._console.log(className);
                             object.editCssPanel(id, customizeLayouts, ['week_slot'], function() {
                                
                                setCss(customizeLayouts);
                                
                            });
                             
                         };
                        
                    }
                    
                })(weekPanels);
                
                if (account.type === 'hotel') {
                    
                    inputPanel.classList.add('calendarWidthForHotel_in_dashboard');
                    
                    var bookingDetailsTitle = object.create('div', object._i18n.get('Booking details'), null, null, null, 'bookingDetailsTitle', null);
                    
                    var checkInWeek = new Date(year, (month - 1), 16).getDay();
                    var checkInDateFormat = object._calendar.formatBookingDate(month, 16, year, null, null, null, checkInWeek, 'text');
                    var checkInName = object.create('div', object._i18n.get('Arrival'), null, null, null, 'name', null);
                    var checkInClear = object.create('label', object._i18n.get('Clear'), null, null, null, 'clearLabel', null);
                    var checkInDate = object.create('div', checkInDateFormat, null, null, null, 'value', null);
                    var checkInPanel = object.create('div', null, [checkInName, checkInClear, checkInDate], null, null, 'row', null);
                    
                    var checkOutWeek = new Date(year, (month - 1), 19).getDay();
                    var checkOutDateFormat = object._calendar.formatBookingDate(month, 19, year, null, null, null, checkOutWeek, 'text');
                    var checkOutName = object.create('div', object._i18n.get('Departure'), null, null, null, 'name', null);
                    var checkOutClear = object.create('label', object._i18n.get('Clear'), null, null, null, 'clearLabel', null);
                    var checkOutDate = object.create('div', checkOutDateFormat, null, null, null, 'value', null);
                    var checkOutPanel = object.create('div', null, [checkOutName, checkOutClear, checkOutDate], null, null, 'row', null);
                    
                    var totalLengthName = object.create('div', object._i18n.get('Total length of stay'), null, null, null, 'name', null);
                    var totalLengthValue = object.create('div', object._i18n.get('%s nights', ['2']), null, null, null, 'value', null);
                    var totalLengthPanel = object.create('div', null, [totalLengthName, totalLengthValue], null, null, 'row', null);
                    
                    var optionsTitle = object.create('div', object._i18n.get('Options'), null, null, null, 'optionsTitle', null);
                    var optionsName = object.create('div', object._i18n.get('Option'), null, null, null, 'name required', null);
                    var optionInSelect = object.create('option', object._i18n.get('Select'), null, null, null, null, null);
                    var selectOption = object.createInputElement('select', null, null, null, null, null, null, null, 'form_select', null);
                    selectOption.appendChild(optionInSelect);
                    var optionValuPanel = object.create('div', null, [selectOption], null, null, 'value', null);
                    var options_row = object.create('div', null, [optionsName, optionValuPanel], null, null, 'options_row', null);
                    var optionsPanel = object.create('div', null, [optionsTitle, options_row], null, null, 'options_in_panel row', null);
                    
                    var guestsTitle = object.create('div', object._i18n.get('Guests'), null, null, null, 'guestsTitle', null);
                    var guestsName = object.create('div', object._i18n.get('Adults'), null, null, null, 'name required', null);
                    var guestsInSelect = object.create('option', object._i18n.get('Select'), null, null, null, null, null);
                    var selectGuests = object.createInputElement('select', null, null, null, null, null, null, null, 'form_select', null);
                    selectGuests.appendChild(guestsInSelect);
                    var guestsValuPanel = object.create('div', null, [guestsName, selectGuests], null, null, 'value', null);
                    var guests_row = object.create('div', null, [guests_row, guestsValuPanel], null, null, 'guests_row', null);
                    var guestsPanel = object.create('div', null, [guestsTitle, guests_row], null, null, 'guests_in_panel row', null);
                    
                    var roomPanel = object.create('div', null, [optionsPanel, guestsPanel], 'roomNo_0', null, null, null);
                    var roomsPanel = object.create('div', null, [roomPanel], 'roomListPanel', null, null, null);
                    
                    var summaryName = object.create('div', object._i18n.get('Summary'), null, null, null, 'name', null);
                    var checkInNameInSummary = object.create('div', object._i18n.get('Arrival'), null, null, null, 'summaryTitle summaryCheckInTitle', null);
                    var checkInDateInSummary = object.create('div', checkInDateFormat, null, null, null, 'summaryValue summaryCheckInValue', null);
                    var checkOutNameInSummary = object.create('div', object._i18n.get('Departure'), null, null, null, 'summaryTitle summaryCheckOutTitle', null);
                    var checkOutDateInSummary = object.create('div', checkOutDateFormat, null, null, null, 'summaryValue summaryCheckOutValue', null);
                    var totalLengthNameInSummary = object.create('div', object._i18n.get('Total length of stay'), null, null, null, 'summaryTitle summaryTotalLengthOfStayTitle', null);
                    var totalLengthValueInSummary = object.create('div', object._i18n.get('%s nights', ['2']) + " " + object._format.formatCost(12000, object._currency), null, null, null, 'summaryValue summaryNightsValue totalLengthOfStayLabel', null);
                    
                    var summaryListPanel = object.create('div', null, [checkInNameInSummary, checkInDateInSummary, checkOutNameInSummary, checkOutDateInSummary, totalLengthNameInSummary, totalLengthValueInSummary], 'summaryListPanel', null, 'value', null);
                    var summaryPanel = object.create('div', null, [summaryName, summaryListPanel], null, null, 'row summary', null);
                    
                    var totalAmountName = object.create('div', object._i18n.get('Total amount'), null, null, null, 'name', null);
                    var totalAmountValue = object.create('div', object._format.formatCost(12000, object._currency), null, null, null, 'value', null);
                    var totalAmountPanel = object.create('div', null, [totalAmountName, totalAmountValue], null, 'border-width: 0;', 'row total_amount', null);
                    
                    var bookingDetailsPanel = object.create('div', null, [bookingDetailsTitle, checkInPanel, checkOutPanel, roomsPanel, totalLengthPanel, summaryPanel, totalAmountPanel], 'booking-package_durationStay', null, null, null);
                    td.appendChild(bookingDetailsPanel);
                    
                    var actions = [
                        {id: 'bookingDetails', element: bookingDetailsTitle, classes: ['bookingDetailsTitle']},
                        {id: 'bookingDetails', element: checkInPanel, classes: ['row', 'name', 'value', 'clearLabel']},
                        {id: 'bookingDetails', element: checkOutPanel, classes: ['row', 'name', 'value', 'clearLabel']},
                        {id: 'bookingDetails', element: optionsPanel, classes: ['row', 'optionsTitle', 'options_row', 'name', 'value']},
                        {id: 'bookingDetails', element: guestsPanel, classes: ['row', 'guestsTitle', 'guests_row', 'name', 'value']},
                        {id: 'bookingDetails', element: totalLengthPanel, classes: ['row', 'name', 'value']},
                        {id: 'bookingDetails', element: summaryPanel, classes: ['row', 'summary', 'name', 'summaryTitle', 'summaryValue', 'totalLengthOfStayLabel']},
                        {id: 'bookingDetails', element: totalAmountPanel, classes: ['row', 'total_amount', 'name', 'value']},
                    ];
                    for (var i = 0; i < actions.length; i++) {
                        
                        (function(action) {
                            
                            action.element.classList.add('editCSS');
                            action.element.onclick = function() {
                                
                                object.editCssPanel(action.id, customizeLayouts, action.classes, function() {
                                    
                                    setCss(customizeLayouts);
                                    
                                });
                                
                            };
                            
                        })(actions[i]);
                        
                    }
                    
                }
                
            } else if (key === 'service' || key === 'timeSlot') {
                
                object._console.log(calendarData);
                
                
                if (account.type === 'day' && account.flowOfBooking === 'calendar' && key === 'timeSlot') {
                    
                    var serviceButton = object.create('button', object._i18n.get('Services'), null, null, null, 'selectedButton', null);
                    var timeSlotButton = object.create('button', object._i18n.get('Time slots'), null, null, null, null, null);
                    var toggleButtonPanel = object.create('div', null, [serviceButton, timeSlotButton], 'toggleButtonPanelOnCustomize2', null, null, null);
                    var buttons = [serviceButton, timeSlotButton];
                    td.appendChild(toggleButtonPanel);
                    td.classList.add('td_background_color');
                    td.setAttribute('colspan', 2);
                    tr.appendChild(th);
                    tr.appendChild(td);
                    table.appendChild(tr);
                    
                    serviceButton.onclick = function() {
                        
                        serviceButton.classList.add('selectedButton');
                        timeSlotButton.classList.remove('selectedButton');
                        var schedulePage = document.getElementById('booking-package_schedulePage');
                        var daysListPanel = document.getElementById('daysListPanel');
                        daysListPanel.setAttribute('class', 'daysListPanel positionSticky');
                        var courseMainPanel = document.getElementById('courseMainPanel');
                        courseMainPanel.setAttribute('class', 'courseListPanel box_shadow postionReturnForCourseListPanel');
                        var scheduleMainPanel = document.getElementById('scheduleMainPanel');
                        scheduleMainPanel.setAttribute('class', 'courseListPanel postionDefaultForScheduleListPanel');
                        var timer = setInterval(function(){
                            
                            var height = courseMainPanel.getBoundingClientRect().height + topPanel.getBoundingClientRect().height;
                            schedulePage.setAttribute('style', 'height: ' + height + 'px;');
                            object._console.log(height);
                            clearInterval(timer);
                            
                        }, 800);
                        
                    };
                    
                    timeSlotButton.onclick = function() {
                        
                        serviceButton.classList.remove('selectedButton');
                        timeSlotButton.classList.add('selectedButton');
                        var schedulePage = document.getElementById('booking-package_schedulePage');
                        var daysListPanel = document.getElementById('daysListPanel');
                        daysListPanel.setAttribute('class', 'daysListPanel positionSticky hidden_panel');
                        var courseMainPanel = document.getElementById('courseMainPanel');
                        courseMainPanel.setAttribute('class', 'courseListPanel postionLeftForCourseListPanel positionSticky');
                        var scheduleMainPanel = document.getElementById('scheduleMainPanel');
                        scheduleMainPanel.setAttribute('class', 'courseListPanel box_shadow postionCenterForScheduleListPanel');
                        var timer = setInterval(function(){
                            
                            var height = scheduleMainPanel.getBoundingClientRect().height + topPanel.getBoundingClientRect().height;
                            schedulePage.setAttribute('style', 'height: ' + height + 'px;');
                            object._console.log(height);
                            clearInterval(timer);
                            
                        }, 800);
                        
                    };
                    
                    object._calendar.setShortMonthNameBool(false);
                    object._calendar.setShortWeekNameBool(false);
                    var schedulePage = object.create('div', null, null, 'booking-package_schedulePage', null, null, null);
                    td.appendChild(schedulePage);
                    
                    var selectedDate = object.create('div', object._calendar.formatBookingDate(month, day, year, null, null, null, week, 'text'), null, 'selectedDate', null, 'selectedDate', null);
                    var topPanel = object.create('div', null, [selectedDate], 'topPanel', null, 'topPanel editCSS', null);
                    var daysListPanel = object.create('div', null, null, 'daysListPanel', null, 'daysListPanel positionSticky', null);
                    createDayListPanel(daysListPanel, month, day, year);
                    topPanel.onclick = function() {
                        
                        var classNames = ['topPanel', 'selectedDate'];
                        object.editCssPanel('service', customizeLayouts, classNames, function() {
                            
                            setCss(customizeLayouts);
                            
                        });
                        
                    };
                    
                    var courseMainPanel = object.create('div', null, null, 'courseMainPanel', null, 'courseListPanel box_shadow', null);
                    var services = {0: 'Service A', 100: 'Service B', 500: 'Service C', 1000: 'Service D', 1500: 'Service E', 2000: 'Service F', 2500: 'Service G'};
                    createServicePanel(services, courseMainPanel, 0, null, function(servicePanel) {
                        
                        if (servicePanel.getAttribute('data-name') === 'Service B') {
                            
                            servicePanel.classList.add('selected_service_slot');
                            
                        }
                        servicePanel.onclick = function() {
                            
                            var id = this.getAttribute('data-id');
                            var classNames = ['selectable_service_slot', 'selectable_service_slot:hover', 'selected_service_slot', 'serviceCost', 'descriptionOfService'];
                            object.editCssPanel(id, customizeLayouts, classNames, function() {
                                
                                setCss(customizeLayouts);
                                
                            });
                            
                        };
                        
                    });
                    var scheduleMainPanel = object.create('div', null, null, 'scheduleMainPanel', null, 'courseListPanel hidden_panel', null);
                    createTimeSlotPanel(scheduleMainPanel);
                    schedulePage.appendChild(topPanel);
                    schedulePage.appendChild(daysListPanel);
                    schedulePage.appendChild(courseMainPanel);
                    schedulePage.appendChild(scheduleMainPanel);
                    var timer = setInterval(function(){
                        
                        var height = courseMainPanel.getBoundingClientRect().height + topPanel.getBoundingClientRect().height;
                        schedulePage.setAttribute('style', 'height: ' + height + 'px;');
                        object._console.log(height);
                        clearInterval(timer);
                        
                    }, 800);
                    
                } else if (account.type === 'day' && account.flowOfBooking === 'services') {
                    
                    td.classList.add('td_background_color');
                    td.setAttribute('colspan', 2);
                    if (key === 'service') {
                        
                        var titlePanel = object.create('div', object._i18n.get('Please select a service'), null, 'booking-package_serviceTitle', null, 'title borderColor editCSS', {id: 'service'});
                        var servicePanels = object.create('div', null, null, 'courseMainPanel', null, 'courseListPanel box_shadow', null);
                        var services = {0: 'Service A', 100: 'Service B', 500: 'Service C', 1000: 'Service D', 1500: 'Service E', 2000: 'Service F', 2500: 'Service G'};
                        var options = {0: 'Option 1', 500: 'Option 2', 1000: 'Option 3'};
                        createServicePanel(services, servicePanels, 1000, options, function(servicePanel) {
                            
                            servicePanel.onclick = function() {
                                
                                var id = this.getAttribute('data-id');
                                var option = this.getAttribute('data-option');
                                var classNames = ['selectable_service_slot', 'selectable_service_slot:hover', 'selected_element'];
                                object._console.log(option);
                                if (parseInt(option) === 1 ) {
                                    
                                    classNames.push('selectable_option_element');
                                    classNames.push('selected_option_element');
                                    
                                }
                                object.editCssPanel(id, customizeLayouts, classNames, function() {
                                    
                                    setCss(customizeLayouts);
                                    
                                });
                                
                            };
                            
                        });
                        
                        var serviceTitle = object.create('div', object._i18n.get('Service details'), null, null, null, 'title borderColor editCSS', {id: 'service'});
                        
                        var serviceName = object.create('span', 'Service C', null, null, null, 'serviceName', null);
                        var serviceCost = object.create('span', object._format.formatCost(1000, object._currency), null, null, null, 'serviceCost', null);
                        var addedService = object.create('div', null, [serviceName, serviceCost], null, null, 'addedService', null);
                        var addedAllServices = object.create('div', null, [addedService], null, null, 'addedAllServices', null);
                        var name = object.create('div', object._i18n.get('Service'), null, null, null, 'name', null);
                        var value = object.create('div', null, [addedAllServices], null, null, 'value', null);
                        var row = object.create('div', null, [name, value], null, null, 'row editCSS', {id: 'service'});
                        var serviceDetailsBody = object.create('div', null, [row], null, null, 'list borderColor', null);
                        var totalAmountTitle = object.create('div', object._i18n.get('Total amount'), null, null, null, 'name', null);
                        var totalAmount = object.create('div', object._format.formatCost(1000, object._currency), null, null, null, 'value', null);
                        var totalAmountPanel = object.create('div', null, [totalAmountTitle, totalAmount], null, null, 'row total_amount editCSS', {id: 'service'});
                        var serviceDetailsPanel = object.create('div', null, [serviceTitle, serviceDetailsBody, totalAmountPanel], 'booking-package_serviceDetails', null, null, null);
                        var schedulePage = object.create('div', null, [titlePanel, servicePanels], 'booking-package_servicePage', null, null, null);
                        td.appendChild(schedulePage);
                        td.appendChild(serviceDetailsPanel);
                        
                        var actions = [
                            {id: 'service', element: titlePanel, classes: ['title']},
                            {id: 'service', element: serviceTitle, classes: ['title']},
                            {id: 'service', element: row, classes: ['row', 'name', 'value', 'serviceName', 'serviceCost']},
                            {id: 'service', element: totalAmountPanel, classes: ['row', 'name', 'value']},
                        ];
                        for (var i = 0; i < actions.length; i++) {
                            
                            (function(action) {
                                
                                action.element.onclick = function() {
                                    
                                    object.editCssPanel(action.id, customizeLayouts, action.classes, function() {
                                        
                                        setCss(customizeLayouts);
                                        
                                    });
                                    
                                };
                                
                            })(actions[i]);
                            
                        }
                        
                    } else if (key === 'timeSlot') {
                        
                        var selectedDate = object.create('div', object._calendar.formatBookingDate(month, day, year, null, null, null, week, 'text'), null, 'selectedDate', null, 'selectedDate', null);
                        var topPanel = object.create('div', null, [selectedDate], 'topPanel', null, 'topPanel', null);
                        var daysListPanel = object.create('div', null, null, 'daysListPanel', null, 'daysListPanel positionSticky', null);
                        createDayListPanel(daysListPanel, month, day, year);
                        var scheduleMainPanel = object.create('div', null, null, 'scheduleMainPanel', null, 'courseListPanel box_shadow positionOfPanelNotHavingCourseForScheduleListPanel', null);
                        createTimeSlotPanel(scheduleMainPanel);
                        var blockPanel = document.createElement('div');
                        var timeSlotPage = object.create('div', null, [topPanel, daysListPanel, scheduleMainPanel, blockPanel], 'booking-package_schedulePage', null, null, null);
                        td.appendChild(timeSlotPage);
                        //td.appendChild(serviceDetailsPanel);
                        var timer2 = setInterval(function(){
                            
                            var height = scheduleMainPanel.getBoundingClientRect().height + topPanel.getBoundingClientRect().height;
                            timeSlotPage.setAttribute('style', 'height: ' + height + 'px;');
                            object._console.log(height);
                            clearInterval(timer2);
                            
                        }, 800);
                        
                    }
                    
                    tr.appendChild(th);
                    tr.appendChild(td);
                    table.appendChild(tr);
                    
                }
                
                
            } else if (key == 'form') {
                
                var formPanel = object.create('div', null, null, 'booking-package_inputFormPanel', null, null, null);
                formPanel.classList.remove("booking_completed_panel");
                td.appendChild(formPanel);
                td.classList.add('td_background_color');
                td.setAttribute('colspan', 2);
                tr.appendChild(th);
                tr.appendChild(td);
                table.appendChild(tr);
                
                var topBarPanel = object.create('div', object._i18n.get("Please fill in your details"), null, 'reservationHeader', null, 'title_in_form editCSS', null);
                formPanel.appendChild(topBarPanel);
                var date = object._calendar.formatBookingDate(month, day, year, 12, 0, '', week, 'elements');
                object._console.log(date);
                var namePanel = object.create('div', object._i18n.get("Booking Date"), null, null, null, 'name', null);
                var valuePanel = object.create('div', null, [date.dateAndTime], null, null, 'value', null);
                var bookingDatePanel = object.create('div', null, [namePanel, valuePanel], null, null, 'row editCSS', null);
                formPanel.appendChild(bookingDatePanel);
                
                topBarPanel.onclick = function(event) {
                    
                    object._console.log(this);
                    var classNames = ['title_in_form'];
                    object.editCssPanel('form', customizeLayouts, classNames, function() {
                        
                        setCss(customizeLayouts);
                        
                    });
                    
                };
                
                bookingDatePanel.onclick = function(event) {
                    
                    object._console.log(this);
                    var classNames = ['row', 'name', 'value', 'description'];
                    object.editCssPanel('form', customizeLayouts, classNames, function() {
                        
                        setCss(customizeLayouts);
                        
                    });
                    
                };
                
                var inputs = {form_text: 'Text', form_select: 'Select', form_radio: 'Radio', form_checkbox: 'Check Box', form_textarea: 'Textarea'};
                for (var type in inputs) {
                    
                    var inputPanel = (function(id, type, name) {
                        
                        object._console.log(type);
                        var namePanel = object.create('div', name, null, null, null, 'name required', null);
                        var input = null;
                        var options = [name + ' ' + 1, name + ' ' + 2];
                        if (type == 'form_text') {
                            
                            input = object.createInputElement('input', 'text', null, null, null, null, null, null, 'form_text', {type: type} );
                            
                        } else if (type == 'form_select') {
                            
                            namePanel.classList.remove('required');
                            input = object.createInputElement('select', null, null, null, null, null, null, null, 'form_select', {type: type} );
                            var option = object.create('option', object._i18n.get('Select'), null, null, null, null, null);
                            input.appendChild(option);
                            for (var i = 0; i < options.length; i++) {
                                
                                var option = object.create('option', options[i], null, null, null, null, null);
                                input.appendChild(option);
                                
                            }
                            
                        } else if (type == 'form_radio' || type == 'form_checkbox') {
                            
                            input = object.create('div', null, null, null, null, 'form_radio', {type: type} );
                            var inputType = 'radio';
                            if (type == 'form_checkbox') {
                                
                                inputType = 'checkbox';
                                input.setAttribute('class', 'form_checkbox');
                                
                            }
                            for (var i = 0; i < options.length; i++) {
                                
                                var option = object.createInputElement('input', inputType, type, i, null, null, null, null, null, {value: i} );
                                if (i === 0) {
                                    
                                    option.checked = true;
                                    
                                }
                                var optionName = object.create('span', options[i], null, null, null, 'radio_title', null);
                                var label = object.create('label', null, [option, optionName], null, 'display: flex, justify-content: left; margin-bottom: 5px;', null, null);
                                input.appendChild(label);
                                
                            }
                            
                        } else if (type == 'form_textarea') {
                            
                            namePanel.classList.remove('required');
                            input = object.create('textarea', null, null, null, null, 'form_textarea', {type: type} );
                            
                        }
                        
                        var valuePanel = object.create('div', null, [input], null, null, 'value', null);
                        var inputPanel = object.create('div', null, [namePanel, valuePanel], null, null, 'row editCSS', null);
                        if (type == 'form_text') {
                            
                            inputPanel.classList.add('error_empty_value');
                            var description = object.create('div', object._i18n.get('Description'), null, null, null, 'description', null);
                            valuePanel.appendChild(description);
                            
                        }
                        
                        object._console.log(input);
                        //input.classList.add('editCSS');
                        (function(inputPanel, type) {
                            
                            object._console.log(type);
                            inputPanel.onclick = function(event) {
                                
                                object._console.log(type);
                                var classNames = ['row', 'name', 'value', 'required:after', type, 'error_empty_value'];
                                object.editCssPanel('form', customizeLayouts, classNames, function() {
                                    
                                    setCss(customizeLayouts);
                                    
                                });
                                
                            };
                            
                        })(inputPanel, type);
                        
                        
                        return inputPanel;
                        
                    })(key, type, inputs[type]);
                    
                    formPanel.appendChild(inputPanel);
                    
                    
                }
                
                
            }
            
        }
        
        setCss(customizeLayouts);
        var themeButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Themes") );
        var saveButton = object.createButton(null, 'margin-left: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
        var resetButton = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get("Reset") );
        var buttonPanel = object.create('div', null, [themeButton, saveButton, resetButton], null, null, 'bottomButtonPanel', null);
        customizePanel.appendChild(buttonPanel);
        
        if (object._themes === 0) {
            
            themeButton.classList.add('hidden_panel');
            saveButton.setAttribute('style', '');
            
        }
        
        if (account.type === 'hotel') {
            
            themeButton.classList.add('hidden_panel');
            saveButton.setAttribute('style', '');
            
        }
        
        themeButton.onclick = function() {
            
            object._console.log(this);
            var themePanels = [];
            var themes = ['Defult', 'Sunset', 'Warm', 'Dark', 'Green', 'Sea', object._i18n.get('Close')];
            for (var i = 0; i < themes.length; i++) {
                
                var themeLabel = object.create('div', themes[i], null, null, null, 'themeLabel', {status: themes[i], close: '0'} );
                if (i === (themes.length - 1)) {
                    
                    themeLabel.setAttribute('class', 'closeLabel');
                    themeLabel.setAttribute('data-status', '1');
                    
                }
                themePanels.push(themeLabel);
                
            }
            var confirm = new Confirm(object._debug);
            confirm.selectPanelShow(object._i18n.get("Themes"), themePanels, 'both', false, function(selectedTheme) {
                
                object._console.log(selectedTheme.toLowerCase() );
                if (selectedTheme != '1') {
                    
                    var post = {nonce: object._nonce, action: object._action, mode: 'changeCustomizeTheme', accountKey: account.key, selectedTheme: selectedTheme.toLocaleLowerCase() };
                    object._console.log(post);
                    object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                        
                        object._console.log(json);
                        object._loadingPanel.setAttribute("class", "hidden_panel");
                        saveCallback(json);
                        for (var i = 0; i < json.length; i++ ) {
                            
                            if (json[i].key == account.key) {
                                
                                customizePanel.removeChild(table);
                                customizePanel.removeChild(buttonPanel);
                                object.customizeLayouts(json[i], customizePanel, saveCallback, resetCallback);
                                break;
                                
                            }
                            
                        }
                        
                    }, function(text){
                        
                    });
                    
                }
                
            });
            
        };
        
        saveButton.onclick = function(event) {
            
            object._console.log(customizeLayouts);
            var jsonStr = JSON.stringify(customizeLayouts);
            object._console.log(jsonStr);
            var post = {nonce: object._nonce, action: object._action, mode: 'updateCustomizeLayouts', accountKey: account.key, customizeLayouts: jsonStr};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
                saveCallback(json);
                
            }, function(text){
                
            });
            
        };
        
        resetButton.onclick = function(event) {
            
            var post = {nonce: object._nonce, action: object._action, mode: 'resetCustomizeLayouts', accountKey: account.key};
            object._console.log(post);
            object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.xmlHttp = new Booking_App_XMLHttp(object._url, post, object._webApp, function(json){
                
                object._console.log(json);
                object._loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
                resetCallback(json);
                
            }, function(text){
                
            });
            
        };
        
    };
    
    Booking_Package_Customize.prototype.editCssPanel = function(id, customizeLayouts, classNames, callback) {
        
        var object = this;
        object._console.log("editCssPanel");
        object._console.log(id);
        object._console.log(customizeLayouts[id]);
        object._console.log(classNames);
        var load_blockPanel = document.getElementById("load_blockPanel");
        load_blockPanel.classList.remove("hidden_panel");
        load_blockPanel.classList.add("edit_modal_backdrop");
        var cssPanelInLayouts = document.getElementById('cssPanelInLayouts');
        cssPanelInLayouts.classList.remove('hidden_panel');
        var inputPanel = cssPanelInLayouts.querySelector('.inputPanel');
        inputPanel.textContent = null;
        var textareas = [];
        var editors = [];
        /**
        for (var i = 0; i < classNames.length; i++) {
            
            var className = classNames[i];
            var value = '';
            if (customizeLayouts[id][className] != null) {
                
                value = (function(styles) {
                    
                    var style = '';
                    for (var name in styles) {
                        
                        style += name + ': ' + styles[name] + ";\n";
                        
                    }
                    return style;
                    
                })(customizeLayouts[id][className]);
                
            }
            var classNamePanel = object.create('div', object._i18n.get('CSS class selector:') + ' .' + className, null, null, 'margin: 0.5em;', null, null);
            textareas[i] = object.createInputElement('div', null, null, value, null, null, 'editCss_' + className, 'height: 20vh;', null, null);
            inputPanel.appendChild(classNamePanel);
            inputPanel.appendChild(textareas[i]);
            
            editors[i] = CodeMirror.fromTextArea(
                textareas[i], 
                {
                    mode: "css",
                    lineNumbers: true,
                    indentUnit: 4,
                }
            );
            
            
        }
        object._console.log(inputPanel);
        **/
        
        require.config({
            paths: {
                'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs'
            }
        });
        
        require(['vs/editor/editor.main'], function () {
            
            for (var i = 0; i < classNames.length; i++) {
                
                var className = classNames[i];
                var value = '';
                if (customizeLayouts[id][className] != null) {
                    
                    value = (function(styles) {
                        
                        var style = '';
                        for (var name in styles) {
                            
                            style += name + ': ' + styles[name] + ";\n";
                            
                        }
                        return style;
                        
                    })(customizeLayouts[id][className]);
                    
                }
                var classNamePanel = object.create('div', object._i18n.get('CSS class selector:') + ' .' + className, null, null, 'margin: 0.5em;', null, null);
                textareas[i] = object.createInputElement('div', null, null, value, null, null, 'editCss_' + className, 'height: 15vh; padding: 0 1em;', null, null);
                inputPanel.appendChild(classNamePanel);
                inputPanel.appendChild(textareas[i]);
                editors[i] = monaco.editor.create(document.getElementById('editCss_' + className), {
                    value: value,
                    language: 'css',
                    theme: 'vs-light'
                });
                
            
                
            }
            object._console.log(inputPanel);
            
        });
        
        
        document.getElementById("closeCssPanelButton").onclick = function() {
            
            cssPanelInLayouts.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        load_blockPanel.onclick = function() {
            
            cssPanelInLayouts.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        document.getElementById('saveCssPanelButton').onclick = function() {
            
            for (var i = 0; i < classNames.length; i++) {
                
                var className = classNames[i];
                /**
                editors[i].save();
                var value = textareas[i].value.replace(/[\n\t]/g, ' ');
                **/
                var value = editors[i].getValue().replace(/[\n\t]/g, ' ');
                value = value.replace(/\/\*\*(.|\n)*?\*\*\//g, '');
                var styleObject = {};
                if (value) {
                    
                    var stylePairs = value.split(';');
                    for (var a = 0; a < stylePairs.length; a++) {
                        
                        var pair = stylePairs[a].split(':');
                        if (pair.length === 2) {
                            
                            styleObject[pair[0].trim()] = pair[1].trim();
                            
                        }
                        
                    }
                    
                }
                object._console.log(className);
                object._console.log(styleObject);
                customizeLayouts[id][className] = styleObject;
                
            }
            callback();
            
        };
        
        
    };
    
    Booking_Package_Customize.prototype.createInput = function(inputName, input, inputData, account, disabled, eventAction, isExtensionsValid) {
        
        var object = this;
	    
        object._console.log("createInput");
        object._console.log(account);
        object._console.log("isExtensionsValid = " + isExtensionsValid);
        object._console.log(input);
        var list = null;
        if (input['valueList'] != null) {
            
            list = input['valueList'];
            
        }
        object._console.log('disabled = ' + disabled);
        object._console.log(list);
        var valuePanel = document.createElement("div");
        valuePanel.classList.add('valuePanel');
        if (input['inputType'] == 'TEXT') {
            
            var textBox = object.createInputElement('input', 'text', null, null, null, disabled, null, null, 'regular-text', null);
            if (parseInt(input.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                
                textBox.setAttribute('data-disabled', 1);
                
            }
            
            if (input['value'] != null && typeof input['value'] == "string") {
                
                textBox.value = input['value'].replace(/\\/g, "");
                
            } else {
                
                textBox.value = input['value'];
                
            }
            
            if (eventAction != null) {
                    
                textBox.onchange = eventAction;
                    
            }
            
            if (input.actions != null && input.actions[0] != null && typeof input.actions[0] == 'function') {
                
                object._console.log(typeof input.actions[0]);
                textBox.onchange = input.actions[0];
                
            }
            
            valuePanel.id = 'customaize_' + input.key;
            valuePanel.appendChild(textBox);
            inputData[inputName] = {textBox: textBox};
            
        } else if (input['inputType'] == 'TEXTAREA') {
            
            
            
        }
        
        return valuePanel;
        
    };
    
    Booking_Package_Customize.prototype.create = function(elementType, text, childElements, id, style, className, data_x) {
        
        var panel = this._element.create(elementType, text, childElements, id, style, className, data_x);
        return panel;
        
    };
    
    Booking_Package_Customize.prototype.createButtonPanel = function(id, style, className, buttons) {
        
        var buttonPanel = this._element.createButtonPanel(id, style, className, buttons);
        return buttonPanel;
        
    };
    
    Booking_Package_Customize.prototype.createButton = function(id, style, className, data_x, text) {
        
        var button = this._element.createButton(id, style, className, data_x, text);
        return button;
        
    };
    
    Booking_Package_Customize.prototype.createInputElement = function(tagName, type, name, value, text, disabled, id, style, className, data_x) {
        
        var input = this._element.createInputElement(tagName, type, name, value, text, disabled, id, style, className, data_x);
        return input;
        
    };
    
    