/* globals Booking_App_XMLHttp */
/* globals scriptError */
/* globals Booking_App_Calendar */
/* globals FORMAT_COST */
/* globals Confirm */
/* globals I18n */
/* globals Booking_Package_Console */
/* globals Booking_Package_DatabaseUpdateErrors */
/* globals Booking_Package_Elements */

var schedule_data = schedule_data;
var booking_package_dictionary = booking_package_dictionary;
var calendarSetting = null;

document.addEventListener('DOMContentLoaded', function() {
    
    window.addEventListener('load', function(){
        
        if(schedule_data != null && booking_package_dictionary != null){
            
            calendarSetting = new SCHEDULE(schedule_data, booking_package_dictionary, false);
            calendarSetting.getCalendarAccountListData(parseInt(schedule_data.month), 1, parseInt(schedule_data.year));
            
        }
        
    });
    
});

window.addEventListener('error', function(event) {
    
    var error = new scriptError(schedule_data, booking_package_dictionary, event.message, event.filename, event.lineno, event.colno, event.error, false);
    error.setResponseText(calendarSetting.getResponseText());
    error.send();
    
}, false);

    function SCHEDULE(schedule_data, booking_package_dictionary, webApp) {
        
        var object = this;
        this._debug = new Booking_Package_Console(schedule_data.debug);
        this._console = {};
        this._console.log = this._debug.getConsoleLog();
        
        this.schedule_data = schedule_data;
        this._isExtensionsValid = parseInt(schedule_data.isExtensionsValid);
        this._webApp = webApp;
        this.url = schedule_data['url'];
        this.nonce = schedule_data['nonce'];
        this.action = schedule_data['action'];
        this.dateFormat = schedule_data.dateFormat;
        this.clockFormat = schedule_data.clockFormat;
        this.positionOfWeek = schedule_data.positionOfWeek;
        this.positionTimeDate = schedule_data.positionTimeDate;
        this.settingList = schedule_data['list'];
        this._isExtensionsValid = parseInt(schedule_data.isExtensionsValid);
        this._locale = schedule_data.locale;
        this.upgradeData = {url: schedule_data['url'], nonce: schedule_data['nonce'], action: schedule_data['action'], locale: schedule_data.locale};
        this.xmlHttp = null;
        this.template_schedule_list = {};
        this.date = {};
        this.weekKey = 0;
        this.mode = null;
        this._function = {name: "root", post: {}};
        this._prefix = schedule_data.prefix;
        this._timestamp = schedule_data.timestamp;
        this._startOfWeek = schedule_data.startOfWeek;
        this._currency = schedule_data.currency;
        this._cancellationByVisitors = 1;
        this._taxForDay = parseInt(schedule_data.taxForDay);
        this._timezone = schedule_data.timezone;
        this._defaultEmail = schedule_data.defaultEmail;
        this._responseText = "";
        this._accountList = null;
        this._siteToken = schedule_data.siteToken;
        this._servicesExcludedGuestsInEmail = 0;
        this._newTaxesAndExtraCharges = 0;
        this._bookPublishingDate = {month: null, day: null, year: null};
        this._enabledStaff = 0;
        this._themes = 0;
        this._customizeLayouts = 0;
        this._element = new Booking_Package_Elements(schedule_data.debug);
        this._numberFormatter = false;
        if (parseInt(schedule_data.numberFormatter) === 1) {
            
            this._numberFormatter = true;
            
        }
        this._currencies = schedule_data.currencies;
        this._currency_info = {locale: this._locale, currency: this._currency, info: this._currencies[this._currency]};
        
        if (parseInt(schedule_data.themes) === 1) {
            
            this._themes = 1;
            
        }
        
        if (parseInt(schedule_data.customizeLayouts) === 1) {
            
            this._customizeLayouts = 1;
            
        }
        
        if (parseInt(schedule_data.enabledStaff) === 1) {
            
            this._enabledStaff = 1;
            
        }
        
        
        if (schedule_data.servicesExcludedGuestsInEmail != null) {
            
            this._servicesExcludedGuestsInEmail = parseInt(schedule_data.servicesExcludedGuestsInEmail);
            object._console.log('this._servicesExcludedGuestsInEmail = ' + this._servicesExcludedGuestsInEmail);
            
        }
        
        if (schedule_data.newTaxesAndExtraCharges != null) {
            
            this._newTaxesAndExtraCharges = parseInt(schedule_data.newTaxesAndExtraCharges);
            object._console.log('this._newTaxesAndExtraCharges = ' + this._newTaxesAndExtraCharges);
            
        }
        
        if (schedule_data.cancellationByVisitors != null) {
            
            this._cancellationByVisitors = parseInt(schedule_data.cancellationByVisitors);
            
        }
        
        if (document.getElementById('booking_package_databaseUpdateErrors') != null) {
            
            object._console.log(document.getElementById('booking_package_databaseUpdateErrors'));
            var databaseUpdateErrors = new Booking_Package_DatabaseUpdateErrors(document.getElementById('booking_package_databaseUpdateErrors'));
            
        }
        
        object._console.log("this._isExtensionsValid = " + this._isExtensionsValid);
        object._console.log(this.schedule_data);
        object._console.log(booking_package_dictionary);
        
        this._i18n = new I18n(schedule_data.locale);
        this._i18n.setDictionary(booking_package_dictionary);
        this._format = new FORMAT_COST(this._i18n, this._debug, this._numberFormatter, this._currency_info);
        
        this._customize = new Booking_Package_Customize(schedule_data, booking_package_dictionary);
        this._customize.setThemes(object._themes);
        
        this.blockPanel = document.getElementById("blockPanel");
        this.editPanel = document.getElementById("editPanelForSchedule");
        this.loadingPanel = document.getElementById("loadingPanel");
        this.buttonPanel = document.getElementById("buttonPanel_for_schedule");
        
        document.getElementById("media_modal_close_for_schedule").onclick = function() {
            
            document.getElementById("edit_schedule_for_hotel").classList.add("hidden_panel");
            document.getElementById("email_edit_panel").classList.add("hidden_panel");
            object.buttonPanel.textContent = null;
            object.editPanelShow(false);
            
        };
        
        this._timezoneGroup = document.getElementById("timezone_choice").getElementsByTagName("optgroup");
        this._timezoneGroup = [].slice.call(this._timezoneGroup);
        this._timezoneGroup.pop();
        this._timezoneOptions = document.getElementById("timezone_choice").getElementsByTagName("option");
        
        this._htmlTitle = null;
        this._htmlOriginTitle = null;
        if (document.getElementsByTagName("title") != null && document.getElementsByTagName("title").length > 0) {
            
            this._htmlTitle = document.getElementsByTagName("title")[0];
            this._htmlOriginTitle = this._htmlTitle.textContent;
            
        }
        this.blockPanel.onclick = function() {
            
            document.getElementById("edit_schedule_for_hotel").classList.add("hidden_panel");
            document.getElementById("email_edit_panel").classList.add("hidden_panel");
            document.getElementById("deletePublishedSchedulesPanel").classList.add("hidden_panel");
            document.getElementById("loadSchedulesPanel").classList.add("hidden_panel");
            document.getElementById("createClonePanel").classList.add("hidden_panel");
            object.buttonPanel.textContent = null;
            object.editPanelShow(false);
            
        };
        
    };

    
    SCHEDULE.prototype.setResponseText = function(responseText){
        
        this._responseText = responseText;
        
    };
    
    SCHEDULE.prototype.getResponseText = function(){
        
        return this._responseText;
        
    };
    
    SCHEDULE.prototype.getMode = function(){
        
        return this.mode;
        
    };
    
    SCHEDULE.prototype.getCalendarDate = function(){
        
        return this.date;
        
    };

    SCHEDULE.prototype.setCalendarDate = function(newDate){
        
        this.date = newDate;
        
    };
    
    SCHEDULE.prototype.setFunction = function(name, post){
        
        this._function = {name: name, post: post};
        
    };
    
    SCHEDULE.prototype.getFunction = function(){
        
        return this._function;
        
    };
    
    SCHEDULE.prototype.setCloneCalendarList = function(accountList) {
        
        var selectedClone = document.getElementById("selectedClone");
        selectedClone.textContent = null;
        for (var key in accountList) {
            
            var option = document.createElement("option");
            option.value = accountList[key].key;
            option.textContent = accountList[key].name;
            selectedClone.appendChild(option);
            
        }
        
    };
    
    SCHEDULE.prototype.setAccountList = function(accountList) {
        
        this._accountList = accountList;
        
    };
    
    SCHEDULE.prototype.getAccountList = function() {
        
        return this._accountList;
        
    };
    
    SCHEDULE.prototype.setTargetCalendar = function(calendarType, accountKey) {
        
        var object = this;
        var accountList = this._accountList;
        if (object.schedule_data.elementForCalendarAccount.calendar_sharing != null) {
            
            var calendar_sharing = {};
            for (var i = 0; i < accountList.length; i++) {

                var account = accountList[i];
                if (parseInt(account.schedulesSharing) == 0 && account.type == calendarType && parseInt(account.key) != accountKey) {
                    
                    calendar_sharing[account.key] = account;
                    
                }
                
            }
            
            object.schedule_data.elementForCalendarAccount.calendar_sharing.valueList[1].valueList = calendar_sharing;
            object._console.log(calendar_sharing);
            object._console.log(object.schedule_data.elementForCalendarAccount);
            
        }
        
    };

    SCHEDULE.prototype.getCalendarAccountListData = function(month, day, year){
        
        var object = this;
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {nonce: object.nonce, action: object.action, mode: 'getCalendarAccountListData', year: year, month: month, day: day};
        object.setFunction("getCalendarAccountListData", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(accountList){
                
            object.loadingPanel.setAttribute("class", "hidden_panel");
            object.createCalendarAccountList(accountList, month, day, year);
            object.setCloneCalendarList(accountList);
            //object.setTargetCalendar(accountList);
            object.setAccountList(accountList);
            
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
    };

    SCHEDULE.prototype.getAccountScheduleData = function(month, day, year, account, createSchedules){
        
        var object = this;
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {nonce: object.nonce, action: object.action, mode: 'getAccountScheduleData', year: year, month: month, day: day, createSchedules: parseInt(createSchedules)};
        if (account != null) {
            
            post.accountKey = account.key;
            
        }
        object.setFunction("getAccountScheduleData", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(calendarData){
                
            object.loadingPanel.setAttribute("class", "hidden_panel");
            object._console.log(calendarData);
            object.createCalendar(calendarData, month, day, year, account);
            
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
    };

    SCHEDULE.prototype.createCalendarAccountList = function(accountList, month, day, year){
        
        var object = this;
        object._console.log(accountList);
        var calendarAccountList = document.getElementById("calendarAccountList");
        calendarAccountList.classList.remove("hidden_panel");
        
        var styleButtons = object.create('style', null, null, 'booking-package_customizeButtons', null, null, null);
        document.head.appendChild(styleButtons);
        var styleTag = object.create('style', null, null, 'booking-package_customizeStyle', null, null, null);
        document.getElementById('wpwrap').appendChild(styleTag);
        
        document.getElementById('booking_package_calendar_accounts').setAttribute('style', '');
        document.getElementById('copyAndPasteOnCalendarSetting').classList.remove('hidden_panel');
        
        var table = document.getElementById("calendar_list_table");
        table.textContent = null;
        
        var titleTr = document.createElement("tr");
        table.appendChild(titleTr);
        
        var list = {key: 'ID', name: object._i18n.get('Name'), status: object._i18n.get('Status'), type: object._i18n.get('Type'), shortCode: object._i18n.get('Shortcode')};
        object._console.log("schedule_data.calendarAccountType = " + parseInt(object.schedule_data.calendarAccountType));
        
        for (var key in list) {
            
            var elementName = object.create('div', list[key], null, null, null, null, null);
            var td = object.create('td', null, [elementName], null, null, null, null);
            if (key == 'key') {
                
                td.style.width = "10%";
                
            } else if (key == 'status' || key == 'type') {
                
                td.style.width = "15%";
                
            }
            titleTr.appendChild(td);
            
        }
        
        var calendarAccountClick = true;
        for (var i = 0; i < accountList.length; i++) {
            
            var account = accountList[i];
            object._console.log(account);
            var tr = object.create('tr', null, null, null, null, 'pointer', {key: i} );
            table.appendChild(tr);
            for (var key in list) {
                
                var elementName = document.createElement("div");
                elementName.textContent = account[key];
                
                if (key == 'shortCode') {
                    
                    var shortCodeText = document.createElement("input");
                    shortCodeText.style.width = "100%";
                    shortCodeText.type = "text";
                    shortCodeText.value = "[booking_package id=" + account['key'] + "]";
                    shortCodeText.setAttribute("readonly", "readonly");
                    elementName.appendChild(shortCodeText);
                    shortCodeText.onclick = function(){
                        
                        this.focus();
                        this.select();
                        calendarAccountClick = false;
                        
                    }
                    
                    shortCodeText.onmouseout = function(){
                        
                        calendarAccountClick = true;
                        
                    }
                    
                } else if (key == 'status') {
                    
                    elementName.textContent = object._i18n.get('Enabled');
                    if (account[key] === 'closed') {
                        
                        tr.classList.add('disabled');
                        elementName.setAttribute("style", "color: #ff0000;");
                        elementName.textContent = object._i18n.get('Disabled');
                        
                    }
                    
                } else if (key == 'type') {
                    
                    elementName.textContent = object._i18n.get('Time Slot Bookings');
                    if (account[key] === 'hotel') {
                        
                        elementName.textContent = object._i18n.get('Multi-night Bookings');
                        
                    }
                    
                } else {
                    
                    account[key] = account[key].replace(/\\/g, "");
                    elementName.textContent = account[key];
                    if(key != 'name'){
                        
                        elementName.textContent = account[key].toUpperCase();
                        
                    }
                    
                }
                
                var td = object.create('td', null, [elementName], null, null, null, null);
                tr.appendChild(td);
                tr.onclick = function(){
                    
                    if(calendarAccountClick === true){
                        
                        document.getElementById('copyAndPasteOnCalendarSetting').classList.add('hidden_panel');
                        calendarAccountList.classList.add("hidden_panel");
                        var accountId = parseInt(this.getAttribute("data-key"));
                        object.loadTabFrame(accountList, month, day, year, accountList[accountId]);
                        var calendarNamePanel = document.getElementById("calendarName");
                        calendarNamePanel.classList.remove("hidden_panel");
                        calendarNamePanel.textContent = accountList[accountId].name;
                        
                        var calendarTimeZone = document.getElementById('calendarTimeZone');
                        calendarTimeZone.classList.remove("hidden_panel");
                        calendarTimeZone.textContent = object._i18n.get('Timezone') + ': ' + accountList[accountId].timezone;
                        
                        if (object._htmlTitle != null) {
                            
                            object._htmlTitle.textContent = accountList[accountId].name;
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
        var addCalendarAccountButton = document.getElementById("add_new_calendar");
        var createCloneButton = document.getElementById("create_clone");
        
        addCalendarAccountButton.onclick = function() {
            
            var dayButton = object.createButton(null, null, 'calendarTypeLabel', {status: 'day', close: 0}, object._i18n.get("Time Slot Bookings (e.g., hair salon, hospital)"));
            var hotelButton = object.createButton(null, null, 'calendarTypeLabel', {status: 'hotel', close: 0}, object._i18n.get("Multi-night Bookings (e.g., accommodations like hotels)"));
            var closeButton = object.createButton(null, null, 'closeLabel', {status: 'CANCELED', close: 1}, object._i18n.get("Close").toUpperCase());
            
            var selectButtonList = [dayButton, hotelButton, closeButton];
            var confirm = new Confirm(object._debug);
            confirm.selectPanelShow(object._i18n.get("Select booking calendar type"), selectButtonList, 'both', false, function(calendarType){
                
                object._console.log(calendarType);
                if (typeof calendarType == 'string') {
                    
                    object.setTargetCalendar(calendarType, null);
                    addCalendarAccountButton.classList.add("hidden_panel");
                    createCloneButton.classList.add("hidden_panel");
                    table.classList.add("hidden_panel");
                    object.addItem(calendarAccountList, 'addCalendarAccount', {type: calendarType}, object.schedule_data['elementForCalendarAccount'], calendarType, function(json){
                        
                        object._console.log(json);
                        if (json != 'close') {
                            
                            object.createCalendarAccountList(json, month, day, year);
                            object.setCloneCalendarList(json);
                            //object.setTargetCalendar(json);
                            object.setAccountList(json);
                            
                        }
                        
                        addCalendarAccountButton.classList.remove("hidden_panel");
                        createCloneButton.classList.remove("hidden_panel");
                        table.classList.remove("hidden_panel");
                        
                    });
                    
                }
                
            });
            
        };
        
        createCloneButton.onclick = function() {
            
            object.blockPanel.classList.remove("hidden_panel");
            object.blockPanel.classList.add("edit_modal_backdrop");
            var createClonePanel = document.getElementById("createClonePanel");
            createClonePanel.classList.remove("hidden_panel");
            var targetList = createClonePanel.getElementsByClassName("target");
            var selectedClone = document.getElementById("selectedClone");
            if (selectedClone.options.length > 0) {
                
                document.getElementById("createCloneButton").onclick = function() {
                    
                    var cloneKey = selectedClone.options[selectedClone.selectedIndex].value;
                    object._console.log("cloneKey = " + cloneKey);
                    var post = {nonce: object.nonce, action: object.action, mode: 'createCloneCalendar', accountKey: cloneKey, all: 0};
                    for (var i = 0; i < targetList.length; i++) {
                        
                        if (targetList[i].checked == true) {
                            
                            object._console.log(targetList[i].value);
                            post[targetList[i].value] = 1;
                            
                        }
                        
                    }
                    
                    object._console.log(post);
                    object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                            
                        object.loadingPanel.setAttribute("class", "hidden_panel");
                        createClonePanel.classList.add("hidden_panel");
                        object.blockPanel.classList.add("hidden_panel");
                        object.blockPanel.classList.remove("edit_modal_backdrop");
                        object.createCalendarAccountList(json, month, day, year);
                        object.setCloneCalendarList(json);
                        //object.setTargetCalendar(json);
                        object.setAccountList(json);
                        
                    }, function(text){
                        
                        object.setResponseText(text);
                        
                    });
                    
                };
                
            }
            
            document.getElementById("createClonePanel_return_button").onclick = function() {
                
                createClonePanel.classList.add("hidden_panel");
                object.blockPanel.classList.add("hidden_panel");
                object.blockPanel.classList.remove("edit_modal_backdrop");
                
                
            };
            
        };
        
    };

    SCHEDULE.prototype.loadTabFrame = function(accountList, month, day, year, account){
        
        var object = this;
        object._console.log(account);
        var tabFrame = document.getElementById("tabFrame");
        tabFrame.classList.remove("hidden_panel");
        var menuList = {
            calendarLink: 'schedulePage', 
            closedDaysLink: 'closedDaysPanel', 
            formLink: 'formPanel', 
            courseLink: 'coursePanel', 
            staffLink: 'staffPanel', 
            guestsLink: 'guestsPanel', 
            couponsLink: 'couponsPanel', 
            optionsForHotelLink: 'optionsForHotelPanel', 
            taxLink: 'taxPanel', 
            taxesLink: 'taxesPanel',
            extraChargesLink: 'extraChargesPanel',
            emailLink: 'emailPanel', 
            syncLink: 'syncPanel', 
            customizeLink: 'customizePanel',
            settingLink: 'settingPanel'
        };
        
        for (var key in menuList) {
            
            document.getElementById(menuList[key]).setAttribute("class", "hidden_panel");

        }
        
        
        if (account.type == 'day') {
            
            delete menuList.optionsForHotelLink;
            
            document.getElementById("optionsForHotelLink").classList.add("hidden_panel");
            document.getElementById("taxLink").classList.remove("hidden_panel");
            document.getElementById("guestsLink").classList.remove("hidden_panel");
            document.getElementById("couponsLink").classList.remove("hidden_panel");
            document.getElementById("staffLink").classList.remove("hidden_panel");
            
            if (object._enabledStaff === 0) {
                
                delete menuList.staffLink;
                document.getElementById("staffLink").classList.add("hidden_panel");
                
            }
            
        } else {
            
            delete menuList.courseLink;
            delete menuList.staffLink;
            delete menuList.couponsLink;
            document.getElementById("courseLink").classList.add("hidden_panel");
            document.getElementById("staffLink").classList.add("hidden_panel");
            document.getElementById("couponsLink").classList.add("hidden_panel");
            
            
        }
        
        if (object._newTaxesAndExtraCharges == 0) {
            
            delete menuList.taxesLink;
            delete menuList.extraChargesLink;
            document.getElementById('taxesLink').setAttribute("class", "hidden_panel");
            document.getElementById('extraChargesLink').setAttribute("class", "hidden_panel");
            
        } else {
            
            delete menuList.taxLink;
            document.getElementById('taxLink').setAttribute("class", "hidden_panel");
            
        }
        
        let column = 1;
        for (var key in menuList) {
            
            var button = document.getElementById(key);
            button.setAttribute("data-key", key);
            if (key == 'calendarLink') {
                
                document.getElementById(key).setAttribute("class", "menuItem active");
                document.getElementById(menuList[key]).setAttribute("class", "");
                
            } else {
                
                document.getElementById(key).setAttribute("class", "menuItem");
                document.getElementById(menuList[key]).setAttribute("class", "hidden_panel");
                
            }
            
            column++;
            document.getElementById(key).setAttribute("style", 'grid-column-start: ' + column + '; grid-column-end: ' + (column + 1) + ';');
            
            button.onclick = function(event) {
                
                var clickKey = this.getAttribute("data-key");
                object._console.log(clickKey);
                /**
                document.getElementById("serviceDisabled").classList.add("hidden_panel");
                document.getElementById('guestsDisabled').classList.add('hidden_panel');
                **/
                for (var key in menuList) {
                    
                    var link = document.getElementById(key);
                    var panel = document.getElementById(menuList[key]);
                    if (clickKey == key) {
                        
                        link.setAttribute("class", "menuItem active");
                        panel.setAttribute("class", "");
                        object._console.log("clickKey = " + key);
                        if (clickKey == 'calendarLink') {
                            
                            object.getAccountScheduleData(month, day, year, account, 1);
                            
                        } else if (clickKey == 'formLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getForm', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(formList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(formList);
                                object.createFormPanel(formList, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                        
                        } else if (clickKey == 'courseLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getCourseList', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(courseList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(courseList);
                                object.createCoursePanel(courseList, account, function(json){
                                    
                                    accountList = json;
                                    for(var i in json){
                                        
                                        if (json[i].key == account.key) {
                                            
                                            account = json[i];
                                            break;
                                            
                                        }
                                        
                                    }
                                    
                                });
                            
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if (clickKey == 'staffLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getStaffList', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(courseList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(courseList);
                                object.createStaffPanel(courseList, account, function(json){
                                    
                                    accountList = json;
                                    for(var i in json){
                                        
                                        if (json[i].key == account.key) {
                                            
                                            account = json[i];
                                            break;
                                            
                                        }
                                        
                                    }
                                    
                                });
                            
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                            
                        } else if (clickKey == 'guestsLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getGuestsList', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(guestsList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(guestsList);
                                object.createGuestsPanel(guestsList, account, function(json) {
                                    
                                    accountList = json;
                                    for(var i in json){
                                        
                                        if (json[i].key == account.key) {
                                            
                                            account = json[i];
                                            break;
                                            
                                        }
                                        
                                    }
                                    
                                })
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if (clickKey == 'couponsLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getCouponsList', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(couponsList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(couponsList);
                                object.createCouponsListPanel(couponsList, account, function(json) {
                                    
                                    accountList = json;
                                    for(var i in json){
                                        
                                        if (json[i].key == account.key) {
                                            
                                            account = json[i];
                                            break;
                                            
                                        }
                                        
                                    }
                                    
                                })
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if (clickKey == 'emailLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getEmailMessageList', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.emailSettingPanel(json.emailMessageList, json.formData, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
					        
					    } else if (clickKey == 'syncLink') {
					        
					        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getIcalToken', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.syncPanel(json.ical, json.syncPastCustomersForIcal, json.icalToken, json.home, account, function(json){
                                    
                                });
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
						    
                        } else if (clickKey == 'closedDaysLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getRegularHolidays', accountKey: account.key, month: month, year: year};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.holidayPanel(account.key, json);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        }  else if (clickKey == 'customizeLink') {
                            
                            object.customizePanel(accountList, month, day, year, account, function(json){
                                
                                accountList = json;
                                for(var i in json){
                                    
                                    if (json[i].key == account.key) {
                                        
                                        account = json[i];
                                        break;
                                        
                                    }
                                    
                                }
                            
                            });
						    
                        } else if (clickKey == 'settingLink') {
                            
                            object.settingPanel(accountList, month, day, year, account, function(json){
                                
                                accountList = json;
                                for(var i in json){
                                    
                                    if (json[i].key == account.key) {
                                        
                                        account = json[i];
                                        break;
                                        
                                    }
                                    
                                }
                            
                            });
						    
                        } else if(clickKey == 'taxLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getTaxes', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.taxPanel(json, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if(clickKey == 'taxesLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getTaxes', accountKey: account.key, taxType: 'tax'};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.taxesPanel(json, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if(clickKey == 'extraChargesLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getTaxes', accountKey: account.key, taxType: 'surcharge'};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.extraChargesPanel(json, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        } else if(clickKey == 'optionsForHotelLink') {
                            
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            var post = {nonce: object.nonce, action: object.action, mode: 'getOptionsForHotel', accountKey: account.key};
                            object.setFunction("loadTabFrame", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(json);
                                object.optionsForHotelPanel(json, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        }
                        
                    } else {
                        
                        link.setAttribute("class", "menuItem");
                        panel.setAttribute("class", "hidden_panel");
                        /**
                        if (object._newTaxesAndExtraCharges == 0) {
                            
                            document.getElementById('taxesLink').setAttribute("class", "hidden_panel");
                            document.getElementById('extraChargesLink').setAttribute("class", "hidden_panel");
                            
                        } else {
                            
                            document.getElementById('taxLink').setAttribute("class", "hidden_panel");
                            
                        }
                        **/
				        
				    }
				    
			    }
			    
		    }
		    
	    }
	    
	    var return_to_calendar_list = document.getElementById("return_to_calendar_list");
	    return_to_calendar_list.onclick = function(){
	        
	        var calendarNamePanel = document.getElementById("calendarName");
	        calendarNamePanel.classList.add("hidden_panel");
	        calendarNamePanel.textContent = null;
	        
	        var calendarTimeZone = document.getElementById('calendarTimeZone');
            calendarTimeZone.classList.add("hidden_panel");
            calendarTimeZone.textContent = null;
	        if (object._htmlTitle != null) {
                
                object._htmlTitle.textContent = object._htmlOriginTitle;
                
	        }
	        tabFrame.classList.add("hidden_panel");
	        object.createCalendarAccountList(accountList, month, day, year);
	        
	    }
	    
    	object.getAccountScheduleData(month, day, year, account, 1);
	    
    };
    
    SCHEDULE.prototype.createCalendar = function(calendarData, month, day, year, account) {
        
        var object = this;
        object._console.log(account);
        object._startOfWeek = account.startOfWeek;
        this.date = {month: month, day: day, year: year};
        var calendarPanel = document.getElementById("schedulePage");
        calendarPanel.textContent = null;
        
        if (parseInt(account.schedulesSharing) === 1) {
            
            var calendarAccountList = object.getAccountList();
            var calendarName = '';
            for (var key in calendarAccountList) {
                
                var targetCalendar = calendarAccountList[key];
                object._console.log(targetCalendar);
                if (parseInt(targetCalendar.key) == parseInt(account.targetSchedules)) {
                    
                    calendarName = targetCalendar.name;
                    break;
                    
                }
                
            }
            object._console.log(calendarName);
            
            var schedulesSharingPanel = object.create('div', object._i18n.get('This calendar shares the schedules of the "%s".', [calendarName]), null, null, null, 'schedulesSharingPanel', null);
            calendarPanel.appendChild(schedulesSharingPanel);
            
            return null;
            
        }
        
        var deletePublishedScheduleButton = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get("Delete"));
        if(account.type == "hotel"){
            
            deletePublishedScheduleButton.classList.add("hidden_panel");
            
        }
        
        var actionButtonsPanelForSchedules = object.create('div', null, null, 'actionButtonsPanelForSchedules', null, null, null);
        calendarPanel.appendChild(actionButtonsPanelForSchedules);
        
        var multipleDaysButtonPanelForSchedules = object.create('div', null, null, 'multipleDaysButtonPanelForSchedules', null, 'hidden_panel', null);
        calendarPanel.appendChild(multipleDaysButtonPanelForSchedules);
        
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        
        var save_setting = null;
        if (document.getElementById("save_setting") == null) {
            
            save_setting = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Weekly schedule templates"));
            actionButtonsPanelForSchedules.appendChild(save_setting);
            save_setting.onclick = function() {
                
                object.editTemplateSchedule(0, account);
                
            };
            
            var selectMultipleDays = object.createButton(null, null, 'w3tc-button-save button-primary button_margin_left_10px', null, object._i18n.get("Select multiple days"));
            actionButtonsPanelForSchedules.appendChild(selectMultipleDays);
            selectMultipleDays.onclick = function() {
                
                object.selectMultipleDays(calendarData, month, day, year, calendarPanel, account);
                
            };
            
            actionButtonsPanelForSchedules.appendChild(deletePublishedScheduleButton);
            deletePublishedScheduleButton.onclick = function(){
                
                object._console.log("deletePublishedScheduleButton");
                var period_after_date = document.getElementById("period_after_date");
                var period_within_date = document.getElementById("period_within_date");
                period_within_date.classList.add("hidden_panel");
                
                var delete_incomplete = document.getElementById('delete_incomplete');
                delete_incomplete.checked = true;
                delete_incomplete.disabled = false;
                
                var delete_perfect = document.getElementById('delete_perfect');
                delete_perfect.disabled = false;
                
                document.getElementById("deletePublishedSchedulesButton").textContent = object._i18n.get("Delete");
                document.getElementById("action_delete").checked = true;
                document.getElementById("period_after").checked = true;
                document.getElementById("period_after").onclick = function(){
                    
                    if(this.checked == true){
                        
                        period_within_date.classList.add("hidden_panel");
                        
                    }
                    
                }
                
                document.getElementById("period_within").onclick = function(){
                    
                    if(this.checked == true){
                        
                        period_within_date.classList.remove("hidden_panel");
                        
                    }
                    
                }
                
                document.getElementById("action_delete").onclick = function(){
                    
                    var action_delete = this;
                    document.getElementById("deletePublishedSchedulesButton").textContent = object._i18n.get("Delete");
                    if (action_delete.checked === true) {
                        
                        delete_incomplete.disabled = false;
                        delete_perfect.disabled = false;
                        
                    }
                    
                };
                
                document.getElementById("action_stop").onclick = function(){
                    
                    var action_stop = this;
                    document.getElementById("deletePublishedSchedulesButton").textContent = object._i18n.get("Paused");
                    if (action_stop.checked === true) {
                        
                        delete_incomplete.disabled = true;
                        delete_perfect.disabled = true;
                        
                    }
                    
                };
                
                object.blockPanel.classList.remove("hidden_panel");
                object.blockPanel.classList.add("edit_modal_backdrop");
                var deletePublishedSchedulesPanel = document.getElementById("deletePublishedSchedulesPanel");
                deletePublishedSchedulesPanel.classList.remove("hidden_panel");
                var timestamp = object._timestamp;
                var date_list = {deletePublishedSchedules_from_month: "n", deletePublishedSchedules_from_day: "j", deletePublishedSchedules_from_year: "Y", deletePublishedSchedules_to_month: "n", deletePublishedSchedules_to_day: "j", deletePublishedSchedules_to_year: "Y"};
                for (var key in date_list) {
                    
                    var value = date_list[key];
                    var options = document.getElementById(key).options;
                    for (var i = 0; i < options.length; i++) {
                        
                        if (parseInt(timestamp[value]) == parseInt(options[i].value)) {
                            
                            options[i].selected = true;
                            break;
                            
                        }
                        
                    }
                    
                    if (object._isExtensionsValid == 0) {
                        
                        document.getElementById(key).disabled = true;
                        
                    }
                    
                }
                
                if (object._isExtensionsValid == 0) {
                    
                    document.getElementById("deletePublishedSchedules_freePlan").classList.remove("hidden_panel");
                    document.getElementById("period_within").disabled = true;
                    period_within_date.classList.add("hidden_panel");
                    
                }
                
                document.getElementById("deletePublishedSchedulesButton").onclick = function(){
                    
                    var post = {
                        nonce: object.nonce, 
                        action: object.action, 
                        mode: 'deletePublishedSchedules', 
                        type: account.type,
                        accountKey: account.key, 
                        period: "period_all",
                        deletePublishedSchedules_from_month: timestamp.n,
                        deletePublishedSchedules_from_day: timestamp.j,
                        deletePublishedSchedules_from_year: timestamp.Y,
                        deletePublishedSchedules_to_month: timestamp.n,
                        deletePublishedSchedules_to_day: timestamp.j,
                        deletePublishedSchedules_to_year: timestamp.Y,
                    };
                    
                    if(object._isExtensionsValid == 1){
                        
                        for(var key in date_list){
                            
                            var select = document.getElementById(key);
                            post[key] = select.value;
                            
                        }
                        
                    }
                    
                    if (delete_incomplete.checked === true) {
                        
                        post.deletionType = 'incomplete';
                        
                    } else {
                        
                        post.deletionType = 'perfect';
                        
                    }
                    
                    if (document.getElementById("period_after").checked === true) {
                        
                        post.period = "period_after";
                        
                    }
                    
                    if (document.getElementById("period_within").checked === true) {
                        
                        post.period = "period_within";
                        
                    }
                    
                    if (document.getElementById("action_delete").checked === true) {
                        
                        post.delete_action = "delete";
                        
                    }
                    
                    if (document.getElementById("action_stop").checked === true) {
                        
                        post.delete_action = "stop";
                        
                    }
                    
                    var requestDeletePublishedSchedules = function(account, post, month, year) {
                        
                        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(response){
                            
                            object.loadingPanel.setAttribute("class", "hidden_panel");
                            if(response.status == "success"){
                                
                                document.getElementById("deletePublishedSchedulesPanel").classList.add("hidden_panel");
                                object.blockPanel.classList.add("hidden_panel");
                                object.getAccountScheduleData(month, 1, year, account, 1);
                                
                            }
                            
                        }, function(text){
                            
                            object.setResponseText(text);
                            
                        });
                        
                    }
                    
                    var today = new Date();
                    var fromDate = calendar.formatBookingDate((today.getMonth() + 1).toString().padStart(2, '0'), today.getDate().toString().padStart(2, '0'), today.getFullYear(), null, null, null, null, 'text');
                    var endDate = new Date(today.getTime() + (parseInt(account.maxAccountScheduleDay) - 1) * 24 * 60 * 60 * 1000);
                    today = '' + today.getFullYear() + (today.getMonth() + 1).toString().padStart(2, '0') + today.getDate().toString().padStart(2, '0');
                    var toDate = calendar.formatBookingDate((endDate.getMonth() + 1).toString().padStart(2, '0'), endDate.getDate().toString().padStart(2, '0'), endDate.getFullYear(), null, null, null, null, 'text');
                    endDate = '' + endDate.getFullYear() + (endDate.getMonth() + 1).toString().padStart(2, '0') + endDate.getDate().toString().padStart(2, '0');
                    var selectedStart = post.deletePublishedSchedules_from_year + post.deletePublishedSchedules_from_month.padStart(2, '0') + post.deletePublishedSchedules_from_day.padStart(2, '0');
                    var selectedEnd = post.deletePublishedSchedules_to_year + post.deletePublishedSchedules_to_month.padStart(2, '0') + post.deletePublishedSchedules_to_day.padStart(2, '0');
                    object._console.log(today);
                    object._console.log(endDate);
                    object._console.log(selectedStart);
                    object._console.log(selectedEnd);
                    if (post.delete_action == 'delete') {
                        
                        var remakeMessage = '';
                        if (post.deletionType == 'perfect' && ( post.period == 'period_all' || ( post.period == 'period_after' && parseInt(selectedStart) <= parseInt(endDate) ) || post.period == 'period_within' && ( parseInt(selectedStart) <= parseInt(endDate) && parseInt(selectedEnd) >= parseInt(today) ) ) ) {
                            
                            if (post.period != 'period_all' && parseInt(selectedStart) >= parseInt(today)) {
                                
                                fromDate = calendar.formatBookingDate(selectedStart.toString().substring(4, 6), selectedStart.toString().substring(6, 8), selectedStart.toString().substring(0, 4), null, null, null, null, 'text');
                                
                            }
                            
                            if (post.period == 'period_within' && parseInt(selectedEnd) <= parseInt(endDate)) {
                                
                                toDate = calendar.formatBookingDate(selectedEnd.toString().substring(4, 6), selectedEnd.toString().substring(6, 8), selectedEnd.toString().substring(0, 4), null, null, null, null, 'text');
                                
                            }
                            
                            remakeMessage = object._i18n.get('Based on the value of %s, schedules will be re-registered from %s to %s.', [object._i18n.get('"%s" and "%s"', [object._i18n.get('Weekly schedule templates'), object._i18n.get('Public days from today')]), fromDate, toDate]) + "\n";
                            
                            if (post.period == 'period_within' && parseInt(selectedStart) == parseInt(selectedEnd)) {
                                
                                remakeMessage = object._i18n.get('Based on the value of %s, schedules will be re-registered on %s.', [object._i18n.get('"%s" and "%s"', [object._i18n.get('Weekly schedule templates'), object._i18n.get('Public days from today')]), fromDate]) + "\n";
                                
                            }
                            
                        }
                        object._console.log(post);
                        object._console.log(remakeMessage);
                        var confirm = new Confirm(object._debug);
                        confirm.dialogPanelShow(object._i18n.get("Warning"), remakeMessage + object._i18n.get("Are you sure you want to delete the selected days?"), false, 1, function(result) {
                            
                            object._console.log(result);
                            if (result === true) {
                                
                                requestDeletePublishedSchedules(account, post, month, year);
                                
                            }
                            
                        });
                        
                    } else {
                        
                        requestDeletePublishedSchedules(account, post, month, year);
                        
                    }
                    
                    /**
                    object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(response){
                        
                        object.loadingPanel.setAttribute("class", "hidden_panel");
                        if(response.status == "success"){
                            
                            document.getElementById("deletePublishedSchedulesPanel").classList.add("hidden_panel");
                            object.blockPanel.classList.add("hidden_panel");
                            object.getAccountScheduleData(month, 1, year, account, 1);
                            
                        }
                        
                    }, function(text){
                        
                        object.setResponseText(text);
                        
                    });
                    **/
                    
                }
                
            };
            
            var deletePublishedSchedulesPanel_return_button = document.getElementById("deletePublishedSchedulesPanel_return_button");
            deletePublishedSchedulesPanel_return_button.onclick = function(){
                
                document.getElementById("deletePublishedSchedulesPanel").classList.add("hidden_panel");
                object.blockPanel.classList.add("hidden_panel");
                
            }
            
        }
        
        var deleteDaysForHotelButton = null;
        var editButton = null;
        var clearButton = null;
        
        if (account.type == "day") {
            
            save_setting.classList.remove("hidden_panel");
            
        } else if (account.type == "hotel") {
            
            save_setting.classList.add("hidden_panel");
            selectMultipleDays.classList.add('hidden_panel');
            
            if (document.getElementById("edit_schedule_button") == null) {
                
                editButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Edit"));
                editButton.disabled = true;
                actionButtonsPanelForSchedules.appendChild(editButton);
                editButton.onclick = function() {
                    
                    object._console.log(scopeOfDay);
                    object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    var post = {nonce: object.nonce, action: object.action, mode: 'getRangeOfSchedule', accountKey: account.key, year: year, month: month, start: scopeOfDay.start, end: scopeOfDay.end};
                    object.setFunction("createCalendar", post);
                    object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(scheduleList){
                        
                        object.loadingPanel.setAttribute("class", "hidden_panel");
                        object._console.log(scheduleList);
                        object.editScheduleForHotel(month, scopeOfDay, year, account, scheduleList, function(calendarData){
                            
                            object.createCalendar(calendarData, month, day, year, account);
                            
                        });
                        
                    }, function(text){
                        
                        object.setResponseText(text);
                        
                    });
                    
                };
                
                deleteDaysForHotelButton = object.createButton(null, 'margin-left: 1em;', 'w3tc-button-save button-primary deleteButton', null, object._i18n.get("Delete"));
                deleteDaysForHotelButton.disabled = true;
                actionButtonsPanelForSchedules.appendChild(deleteDaysForHotelButton);
                
                deleteDaysForHotelButton.onclick = function(event) {
                    
                    object._console.log(this);
                    object._console.log(scopeOfDay);
                    var remakeMessage = '';
                    if (parseInt(account.maxAccountScheduleDay) > 0) {
                        
                        var today = new Date();
                        var fromDate = calendar.formatBookingDate((today.getMonth() + 1).toString().padStart(2, '0'), today.getDate().toString().padStart(2, '0'), today.getFullYear(), null, null, null, null, 'text');
                        var endDate = new Date(today.getTime() + (parseInt(account.maxAccountScheduleDay) - 1) * 24 * 60 * 60 * 1000);
                        today = '' + today.getFullYear() + (today.getMonth() + 1).toString().padStart(2, '0') + today.getDate().toString().padStart(2, '0');
                        var toDate = calendar.formatBookingDate((endDate.getMonth() + 1).toString().padStart(2, '0'), endDate.getDate().toString().padStart(2, '0'), endDate.getFullYear(), null, null, null, null, 'text');
                        endDate = '' + endDate.getFullYear() + (endDate.getMonth() + 1).toString().padStart(2, '0') + endDate.getDate().toString().padStart(2, '0');
                        object._console.log(today);
                        object._console.log(endDate);
                        if (scopeOfDay.start <= parseInt(endDate) && scopeOfDay.end >= parseInt(today)) {
                            
                            if (scopeOfDay.start >= parseInt(today)) {
                                
                                fromDate = calendar.formatBookingDate(scopeOfDay.start.toString().substring(4, 6), scopeOfDay.start.toString().substring(6, 8), scopeOfDay.start.toString().substring(0, 4), null, null, null, null, 'text');
                                
                            }
                            
                            if (scopeOfDay.end <= parseInt(endDate)) {
                                
                                toDate = calendar.formatBookingDate(scopeOfDay.end.toString().substring(4, 6), scopeOfDay.end.toString().substring(6, 8), scopeOfDay.end.toString().substring(0, 4), null, null, null, null, 'text');
                                
                            }
                            object._console.log('fromDate = ' + fromDate);
                            object._console.log('toDate = ' + toDate);
                            
                            remakeMessage = object._i18n.get('Based on the value of %s, schedules will be re-registered from %s to %s.', [object._i18n.get('"%s"', [object._i18n.get('Public days from today')]), fromDate, toDate]) + "\n";
                            if (fromDate == toDate) {
                                
                                remakeMessage = object._i18n.get('Based on the value of %s, schedules will be re-registered on %s.', [object._i18n.get('"%s"', [object._i18n.get('Public days from today')]), fromDate]) + "\n";
                                
                            }
                            
                        }
                        
                    }
                    
                    var confirm = new Confirm(object._debug);
                    confirm.dialogPanelShow(object._i18n.get("Warning"), remakeMessage + object._i18n.get("Are you sure you want to delete the selected days?"), false, 1, function(result) {
                        
                        object._console.log(result);
                        if (result === true) {
                            
                            var deletePublishedSchedules_from_year = scopeOfDay.start.toString().substring(0, 4);
                            var deletePublishedSchedules_from_month = scopeOfDay.start.toString().substring(4, 6);
                            var deletePublishedSchedules_from_day = scopeOfDay.start.toString().substring(6, 8);
                            
                            var deletePublishedSchedules_to_year = scopeOfDay.end.toString().substring(0, 4);
                            var deletePublishedSchedules_to_month = scopeOfDay.end.toString().substring(4, 6);
                            var deletePublishedSchedules_to_day = scopeOfDay.end.toString().substring(6, 8);
                            
                            var post = {nonce: object.nonce, action: object.action, mode: 'deletePublishedSchedules', accountKey: account.key, type: account.type, deletePublishedSchedules_from_year: deletePublishedSchedules_from_year, deletePublishedSchedules_from_month: deletePublishedSchedules_from_month, deletePublishedSchedules_from_day: deletePublishedSchedules_from_day, deletePublishedSchedules_to_year: deletePublishedSchedules_to_year, deletePublishedSchedules_to_month: deletePublishedSchedules_to_month, deletePublishedSchedules_to_day: deletePublishedSchedules_to_day, period: 'period_within', deletionType: 'perfect', delete_action: 'delete'};
                            object._console.log(post);
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            object.setFunction("createCalendar", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(scheduleList){
                                
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                object._console.log(scheduleList);
                                object.getAccountScheduleData(month, 1, year, account, 0);
                                //object.createCalendar(calendarData, month, day, year, account);
                                
                            }, function(text){
                                
                                object.setResponseText(text);
                                
                            });
                            
                        }
                        
                    });
                    
                };
                
                clearButton = object.createButton('clearSchedule', 'margin-left: 1em;', 'w3tc-button-save button-primary', null, object._i18n.get("Cancel"));
                clearButton.disabled = true;
                actionButtonsPanelForSchedules.appendChild(clearButton);
                clearButton.onclick = function(){
                    
                    editButton.disabled = true;
                    deleteDaysForHotelButton.disabled = true;
                    clearButton.disabled = true;
                    for(var key in dayPanelList){
                        
                        dayPanelList[key].classList.remove("selected_day_slot");
                        
                    }
                    
                }
                
            }
            
        }
        
        var dayHeight = parseInt(calendarPanel.clientWidth / 7);
        
        var returnLabel = document.createElement("label");
        var nextLabel = document.createElement("label");
        var topPanel = calendar.createHeader(month, year, 0, true);
        if (topPanel.querySelector('#change_calendar_return') != null) {
            
            returnLabel = topPanel.querySelector('#change_calendar_return');
            
        }
        
        if (topPanel.querySelector('#change_calendar_next') != null) {
            
            nextLabel = topPanel.querySelector('#change_calendar_next');
            
        }
        
        calendarPanel.appendChild(topPanel);
        
        var clickPoint = {start: null, end: null};
        var scopeOfDay = {start: null, end: null};
        var days = {};
        var dayPanelList = {};
        object._console.log(calendarData);
        var deletedCalendar = calendarData.deletedCalendar;
        calendar.create(calendarPanel, calendarData, month, day, year, '', function(callback){
            
            object._console.log(callback);
            days[callback.key] = callback;
            var dayPanel = callback['eventPanel'];
            dayPanel.setAttribute("data-click", callback.bool);
            if (callback.bool == 1) {
                
                dayPanelList[callback.key] = dayPanel;
                
            }
            
            if (callback.status == 0) {
                
                dayPanel.classList.add("closeDay");
                
            }
            
            dayPanel.setAttribute('data-hasIncompletelySchedules', '0');
            if (callback.status == 0 && parseInt(deletedCalendar[callback.key].status) == 1) {
                
                object._console.log(deletedCalendar[callback.key]);
                
                var incompletelySchedules = object.create('div', 'warning', null, null, null, 'material-icons incompletelySchedules', null);
                incompletelySchedules.title = object._i18n.get('This schedule has not been perfectly deleted.');
                dayPanel.appendChild(incompletelySchedules);
                dayPanel.setAttribute('data-hasIncompletelySchedules', '1');
                
            }
            
            if (callback.publishingDate != null) {
                
                var publishingDatePanel = object.create('div', callback.publishingDate.date, null, null, null, 'publishingDatePanel', null);
                dayPanel.appendChild(publishingDatePanel);
                
            }
            
            dayPanel.onclick = function(){
                
                if(parseInt(this.getAttribute("data-click")) == 0){
                    
                    return null;
                    
                }
                
                var dateKey = this.getAttribute("data-key");
                var monthKey = this.getAttribute("data-month");
                var dayKey = this.getAttribute("data-day");
                var yearKey = this.getAttribute("data-year");
                var week = this.getAttribute("data-week");
                var hasIncompletelySchedules = parseInt(this.getAttribute('data-hasIncompletelySchedules'));
                object._console.log(days[dateKey]);
                object.setCalendarDate({month: monthKey, day: dayKey, year: yearKey});
                object.editPublicSchedule(monthKey, dayKey, yearKey, week, account, hasIncompletelySchedules, days[dateKey].publishingDate, function(callback){
                    
                    object._console.log("editPublicSchedule callback");
                    
                });
            
            };
            
            if (account.type == "hotel") {
                
                dayPanel.classList.add("pointer");
                if (parseInt(callback.stop) === 1) {
                    
                    dayPanel.setAttribute("style", "color: #ff0000;");
                    dayPanel.classList.add('disabled');
                    
                }
                
                
                dayPanel.onclick = function(){
                    
                    if (parseInt(this.getAttribute("data-click")) == 0) {
                        
                        return null;
                        
                    }
                    
                    var dayKey = parseInt(this.getAttribute("data-key"));
                    if (clickPoint.start == null) {
                        
                        editButton.disabled = true;
                        deleteDaysForHotelButton.disabled = true;
                        clearButton.disabled = true;
                        clickPoint.start = parseInt(dayKey);
                        for (var key in dayPanelList) {
                            
                            dayPanelList[key].classList.remove("selected_day_slot");
                            
                        }
                        dayPanelList[dayKey].classList.add("selected_day_slot");
                        
                    } else if (clickPoint.start != null && clickPoint.end == null) {
                        
                        var publishedDayForHotel = 0;
                        editButton.disabled = false;
                        deleteDaysForHotelButton.disabled = false;
                        clearButton.disabled = false;
                        if(clickPoint.start > dayKey){
                            
                            clickPoint.end = clickPoint.start;
                            clickPoint.start = dayKey;
                            
                        } else {
                            
                            clickPoint.end = dayKey;
                            
                        }
                        
                        for (var key in dayPanelList) {
                            
                            if (clickPoint.start <= parseInt(key) && clickPoint.end >= parseInt(key)) {
                                
                                dayPanelList[key].classList.add("selected_day_slot");
                                if (parseInt(days[key].status) === 1) {
                                    
                                    publishedDayForHotel++;
                                    
                                }
                                object._console.log(dayPanelList[key]);
                                
                            } else {
                                
                                dayPanelList[key].classList.remove("selected_day_slot");
                                
                            }
                            
                        }
                        
                        if (publishedDayForHotel === 0) {
                            
                            deleteDaysForHotelButton.disabled = true;
                            
                        }
                        
                        scopeOfDay.start = clickPoint.start;
                        scopeOfDay.end = clickPoint.end;
                        clickPoint.start = null;
                        clickPoint.end = null;
                        
                    }
                    object._console.log(clickPoint);
                    
                };
                
                dayPanel.onmouseover = function(){
                    
                    var dayKey = parseInt(this.getAttribute("data-key"));
                    if (clickPoint.start != null) {
                        
                        for (var key in dayPanelList) {
                            
                            if (clickPoint.start > dayKey) {
                                
                                if (clickPoint.start >= key && dayKey <= key) {
                                    
                                    dayPanelList[key].classList.add("selected_day_slot");
                                
                                } else {
                                    
                                    dayPanelList[key].classList.remove("selected_day_slot");
                                    
                                }
                            
                            } else {
                                
                                if (clickPoint.start <= key && dayKey >= key) {
                                    
                                    dayPanelList[key].classList.add("selected_day_slot");
                                
                                } else {
                                    
                                    dayPanelList[key].classList.remove("selected_day_slot");
                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                };
                    
            }
        
        });
        
        returnLabel.onclick = function(){
            
            if(month == 1){
                
                year--;
                month = 12;
                
            }else{
                
                month--;
                
            }
            
            object.getAccountScheduleData(month, 1, year, account, 0);
        
        }
        
        nextLabel.onclick = function(){
            
            if(month == 12){
                
                year++;
                month = 1;
                
            }else{
                
                month++;
                
            }
            
            object.getAccountScheduleData(month, 1, year, account, 0);
            
        }
        
    };
    
    SCHEDULE.prototype.editScheduleForHotel = function(month, scopeOfDay, year, account, scheduleList, callback){
        
        var object = this;
        object._console.log(scheduleList);
        var editSchedulePanel = document.getElementById("edit_schedule_for_hotel");
        editSchedulePanel.classList.remove("hidden_panel");
        var media_frame_content = document.getElementById("media_frame_content_for_schedule");
        media_frame_content.classList.add('hidden_panel');
        media_frame_content.textContent = null;
        document.getElementById("media_title_for_schedule").classList.add("media_left_zero");
        document.getElementById("media_router_for_schedule").classList.add("hidden_panel");
        document.getElementById("menu_panel_for_schedule").classList.add("hidden_panel");
        document.getElementById("frame_toolbar_for_schedule").setAttribute("class", "media_frame_toolbar media_left_zero");
        
        document.getElementById("edit_title_for_schedule").textContent = object._i18n.get("Schedules");
        var incompletelyDeletedScheduleAlertPanel = document.getElementById('incompletelyDeletedScheduleAlertPanel');
        incompletelyDeletedScheduleAlertPanel.classList.add('hidden_panel');
        
        object.editPanelShow(true);
        
        var scheduleEditTable = document.getElementById("scheduleEditTable");
        scheduleEditTable.textContent = null;
        var titleList = [object._i18n.get("Date"), object._i18n.get("Status"), object._i18n.get('Publication date'), object._i18n.get("Charges"), object._i18n.get("Available room slots"), object._i18n.get('Remaining slots')];

        var tr = document.createElement("tr");
        for(var i = 0; i < titleList.length; i++){
            
            var td = object.create('td', titleList[i], null, null, null, null, null);
            tr.appendChild(td);
            
        }
        
        var thead = object.create('thead', null, [tr], null, 'width: 100%; background: #fff; position: sticky; top: 0; left: 0; z-index: 1;', null, null);
        scheduleEditTable.appendChild(thead);
        var tbody = document.createElement('tbody');
        scheduleEditTable.appendChild(tbody);
        
        var statusValues = {false: object._i18n.get('Enabled'), true: object._i18n.get('Disabled')};
        
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        calendar.setShortMonthNameBool(true);
        calendar.setShortWeekNameBool(true);
        for (var key in scheduleList) {
            
            var schedule = scheduleList[key];
            var tdDate = object.create('td', calendar.formatBookingDate(schedule.month, schedule.day, schedule.year, null, null, null, schedule.weekKey, 'text'), null, 'multi_night_' + key, null, null, null);
            if (schedule.stop === 'true') {
                
                tdDate.classList.add('disabled');
                
            }
            
            var selectStatus = document.createElement('select');
            selectStatus.setAttribute("data-key", key);
            selectStatus = (function(selectStatus, statusValues, defaultValue){
                
                object._console.log(defaultValue);
                object._console.log(statusValues);
                for (var key in statusValues) {
                    
                    object._console.log(typeof key);
                    var optionBox = document.createElement("option");
                    optionBox.value = key;
                    optionBox.textContent = statusValues[key];
                    if (defaultValue == key) {
                        
                        optionBox.selected = true;
                        
                    }
                    
                    selectStatus.appendChild(optionBox);
                    
                }
                return selectStatus;
                
            })(selectStatus, statusValues, schedule.stop);
            object._console.log(selectStatus);
            
            var tdReceptionist = object.create('td', null, [selectStatus], null, null, null, null);
            var publishingDatePanel = object.create('span', object._i18n.get('Book the publication date'), null, null, "cursor: pointer;", "material-icons noTime", {key: key});
            if (parseInt(schedule.publishingDate) === 0) {
                
                publishingDatePanel.textContent = 'edit_calendar';
                
            } else {
                
                publishingDatePanel.classList.remove('material-icons');
                publishingDatePanel.textContent = calendar.formatBookingDate(schedule.publishingDateObjects.month, schedule.publishingDateObjects.day, schedule.publishingDateObjects.year, schedule.publishingDateObjects.hour, 0, null, schedule.publishingDateObjects.week, 'text');
                
            }
            
            var publishingDateTd = document.createElement('td');
            publishingDateTd.appendChild(publishingDatePanel);
            
            var textPrice = document.createElement("input");
            textPrice.setAttribute("data-key", key);
            textPrice.type = "text";
            textPrice.value = schedule.cost;
            var tdPrice = object.create('td', null, [textPrice], null, null, null, null);
            
            var textCapacity = document.createElement("input");
            textCapacity.setAttribute("data-key", key);
            textCapacity.type = "text";
            textCapacity.value = schedule.capacity;
            var tdCapacity = object.create('td', null, [textCapacity], null, null, null, null);
            
            var textRemainder = document.createElement("input");
            textRemainder.setAttribute("data-key", key);
            textRemainder.type = "text";
            textRemainder.value = schedule.remainder;
            var tdRemainder = object.create('td', null, [textRemainder], null, null, null, null);
            
            var tr = document.createElement("tr");
            tr.appendChild(tdDate);
            tr.appendChild(tdReceptionist);
            tr.appendChild(publishingDateTd);
            tr.appendChild(tdPrice);
            tr.appendChild(tdCapacity);
            tr.appendChild(tdRemainder);
            tbody.appendChild(tr);
            
            selectStatus.onchange = function() {
                
                var dataKey = this.getAttribute("data-key");
                var option = this.options[this.selectedIndex];
                scheduleList[dataKey].stop = option.value;
                object._console.log(option.value);
                var tdDate = document.getElementById('multi_night_' + dataKey);
                tdDate.classList.remove('disabled');
                if (option.value === 'true') {
                    
                    tdDate.classList.add('disabled');
                    
                }
                
            };
            
            publishingDatePanel.onclick = function() {
                
                object._console.log(this);
                var publishingDatePanel = this;
                var dataKey = this.getAttribute("data-key");
                var publishingDateObjects = null;
                var schedule = scheduleList[dataKey];
                object._console.log(schedule);
                object.bookAndSaveDays(account, schedule.publishingDateObjects, function(publishingDate) {
                    
                    object._console.log(publishingDate);
                    if (publishingDate != null) {
                        
                        publishingDatePanel.classList.remove('material-icons');
                        publishingDatePanel.textContent = calendar.formatBookingDate(publishingDate.month, publishingDate.day, publishingDate.year, publishingDate.time, 0, null, publishingDate.week, 'text');
                        scheduleList[dataKey].publishingDate = publishingDate.date;
                        
                    } else {
                        
                        publishingDatePanel.classList.add('material-icons');
                        publishingDatePanel.textContent = 'edit_calendar';
                        scheduleList[dataKey].publishingDate = 0;
                        scheduleList[dataKey].publishingDateObjects = null;
                        
                    }
                    object._console.log(scheduleList[dataKey]);
                    
                });
                
                
            };
            
            textPrice.onchange = function() {
                
                var dataKey = this.getAttribute("data-key");
                var value = this.value;
                object._console.log("dataKey = " + dataKey + " value = " + parseInt(value));
                scheduleList[dataKey].cost = parseInt(value);
                object._console.log(scheduleList[dataKey]);
                
            };
            
            textCapacity.onchange = function() {
                
                var dataKey = this.getAttribute("data-key");
                var value = this.value;
                object._console.log("dataKey = " + dataKey + " value = " + value);
                scheduleList[dataKey].capacity = parseInt(value);
                object._console.log(scheduleList[dataKey]);
                
            };
            
            textRemainder.onchange = function() {
                
                var dataKey = this.getAttribute("data-key");
                var value = this.value;
                object._console.log("dataKey = " + dataKey + " value = " + value);
                scheduleList[dataKey].remainder = parseInt(value);
                object._console.log(scheduleList[dataKey]);
                
            };
            
        }
        
        var saveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert', null, object._i18n.get("Save"));
        var buttonPanel = document.getElementById("buttonPanel_for_schedule");
        buttonPanel.textContent = null;
        buttonPanel.appendChild(saveButton);
        
        saveButton.onclick = function(){
            
            object._console.log(scheduleList);
            var json = JSON.stringify(scheduleList);
            object._console.log(json);
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            var post = {nonce: object.nonce, action: object.action, mode: 'updateRangeOfSchedule', accountKey: account.key, year: year, month: month, start: scopeOfDay.start, end: scopeOfDay.end, json: json};
            object.setFunction("editScheduleForHotel", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(scheduleList){
                
                object._console.log(scheduleList);
                object.loadingPanel.setAttribute("class", "hidden_panel");
                object.editScheduleForHotel(month, scopeOfDay, year, account, scheduleList.getRangeOfSchedule, callback);
                callback(scheduleList.getAccountScheduleData);
                
                document.getElementById("edit_schedule_for_hotel").classList.add("hidden_panel");
                document.getElementById("email_edit_panel").classList.add("hidden_panel");
                object.buttonPanel.textContent = null;
                object.editPanelShow(false);
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            
        }
        
    };
    
    SCHEDULE.prototype.editPublicSchedule = function(month, day, year, week, account, hasIncompletelySchedules, addedPublishingDate, callback){
        
        var object = this;
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
    	var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
    	object._console.log(addedPublishingDate);
        object._console.log(weekName);
        document.getElementById("media_frame_content_for_schedule").textContent = null;
        var edit_title = document.getElementById("edit_title_for_schedule");
        edit_title.textContent = calendar.formatBookingDate(month, day, year, null, null, null, week, 'text');
        
        document.getElementById("media_router_for_schedule").classList.remove("hidden_panel");
        document.getElementById("menu_panel_for_schedule").setAttribute("class", "media_frame_menu hidden_panel");
        document.getElementById("media_title_for_schedule").classList.add("media_left_zero");
        document.getElementById("media_router_for_schedule").classList.add("media_left_zero");
        document.getElementById("media_frame_content_for_schedule").setAttribute("class", "media_left_zero");
        document.getElementById("frame_toolbar_for_schedule").setAttribute("class", "media_frame_toolbar media_left_zero");
        
        var incompletelyDeletedScheduleAlertPanel = document.getElementById('incompletelyDeletedScheduleAlertPanel');
        incompletelyDeletedScheduleAlertPanel.classList.add('hidden_panel');
        if (hasIncompletelySchedules == 1) {
            
            incompletelyDeletedScheduleAlertPanel.classList.remove('hidden_panel');
            
        }
        
        incompletelyDeletedScheduleAlertPanel.onclick = function() {
            
            this.classList.add('hidden_panel');
            
        }
        
        object.getPublishedTimeSlots(month, day, year, account, hasIncompletelySchedules, addedPublishingDate, callback);
        object.editPanelShow(true);
        
    };
    
    SCHEDULE.prototype.editTemplateSchedule = function(weekKey, account){
        
        var object = this;
        document.getElementById("media_frame_content_for_schedule").textContent = null;
        var edit_title = document.getElementById("edit_title_for_schedule");
        var media_menu = document.getElementById("media_menu_for_schedule");
        media_menu.textContent = null;
        
        document.getElementById("media_router_for_schedule").classList.remove("hidden_panel");
        document.getElementById("menu_panel_for_schedule").setAttribute("class", "media_frame_menu");
        document.getElementById("media_title_for_schedule").classList.remove("media_left_zero");
        document.getElementById("media_router_for_schedule").classList.remove("media_left_zero");
        document.getElementById("media_frame_content_for_schedule").setAttribute("class", "");
        document.getElementById("frame_toolbar_for_schedule").setAttribute("class", "media_frame_toolbar");
        
        var incompletelyDeletedScheduleAlertPanel = document.getElementById('incompletelyDeletedScheduleAlertPanel');
        incompletelyDeletedScheduleAlertPanel.classList.add('hidden_panel');
        
        var weekPanelList = [];
        var weekList = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        for (var i = 0; i < weekList.length; i++) {
            
            var div = object.create('div', object._i18n.get(weekList[i]), null, null, null, null, {key: i});
            div.onclick = function(){
                
                for (var i = 0; i < weekPanelList.length; i++) {
                    
                    weekPanelList[i].setAttribute("class", "");
                    
                }
                var key = this.getAttribute("data-key");
                edit_title.textContent = object._i18n.get("Every %s", [object._i18n.get(weekList[key])]);
                this.setAttribute("class", "media_active");
                document.getElementById("media_frame_content_for_schedule").textContent = null;
                object.getTemplateSchedule(key, account);
                
            }
            
            weekPanelList.push(div);
            media_menu.appendChild(div);
            
        }
        
        if (weekKey != null) {
            
            var key = parseInt(weekKey);
            edit_title.textContent = object._i18n.get("Every %s", [object._i18n.get(weekList[key])]);
            weekPanelList[key].setAttribute("class", "media_active");
            object.getTemplateSchedule(key, account);
            
        }
        
        object.editPanelShow(true);
        
    };
    
    SCHEDULE.prototype.selectMultipleDays = function(calendarData, month, day, year, calendarPanel, account) {
        
        var object = this;
        object._console.log('selectMultipleDays');
         object._console.log('month = ' + month + ' year = ' + year);
         var schedulesPanels = calendarPanel.querySelectorAll('.calendar');
        object._console.log(schedulesPanels);
        schedulesPanels.forEach(function(element) {
            
            object._console.log(element);
            element.classList.add('hidden_panel');
            
        });
        
        var actionButtonsPanelForSchedules = document.getElementById('actionButtonsPanelForSchedules');
        actionButtonsPanelForSchedules.classList.add('hidden_panel');
        
        var multipleDaysButtonPanelForSchedules = document.getElementById('multipleDaysButtonPanelForSchedules');
        multipleDaysButtonPanelForSchedules.classList.remove('hidden_panel');
        multipleDaysButtonPanelForSchedules.textContent = null;
        
        var selectedCount = 0;
        var selectedDays = {};
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        calendar.create(calendarPanel, calendarData, month, day, year, '', function(callback){
            
            object._console.log(callback);
            callback.selected = 0;
            selectedDays[callback.key] = callback;
            if (callback.status === 1) {
                
                callback.eventPanel.classList.remove('pointer');
                callback.eventPanel.classList.add('closeDay');
                
            } else {
                
                callback.eventPanel.onclick = function(event) {
                    
                    var dayPanel = this;
                    object._console.log(dayPanel);
                    var key = parseInt(dayPanel.getAttribute("data-key"));
                    if (selectedDays[key].selected === 0) {
                        
                        selectedCount++;
                        selectedDays[key].selected = 1;
                        dayPanel.classList.add('selected_day_slot');
                        
                    } else {
                        
                        selectedCount--;
                        selectedDays[key].selected = 0;
                        dayPanel.classList.remove('selected_day_slot');
                        
                    }
                    
                    if (selectedCount === 0) {
                        
                        addTimeSlotsButton.disabled = true;
                        clearButton.disabled = true;
                        
                    } else {
                        
                        addTimeSlotsButton.disabled = false;
                        clearButton.disabled = false;
                        
                    }
                    
                    object._console.log(selectedCount);
                    object._console.log(selectedDays[key]);
                    
                };
                
            }
            
        });
        
        var arrowLeft = calendarPanel.querySelectorAll('.arrowLeft');
        arrowLeft.forEach(function(element) {
            
            object._console.log(element);
            element.classList.add('hidden_panel');
            
        });
        
        var arrowRight = calendarPanel.querySelectorAll('.arrowRight');
        arrowRight.forEach(function(element) {
            
            object._console.log(element);
            element.classList.add('hidden_panel');
            
        });
        
        var calendarHeader = calendarPanel.querySelectorAll('.calendarHeader');
        calendarHeader.forEach(function(element) {
            
            element.setAttribute('style', 'justify-content: center;');
            
        });
        
        var calendarTitle = calendarPanel.querySelectorAll('.calendarData');
        calendarTitle.forEach(function(element) {
            
            object._console.log(element);
            element.classList.add('selectAllDays');
            element.setAttribute('style', 'color: #0073aa;');
            element.onclick = function(event) {
                
                object._console.log(this);
                selectedCount = 0;
                for (var key in selectedDays) {
                    
                    if (selectedDays[key].status === 0 && selectedDays[key].month === month && selectedDays[key].year === year) {
                        
                        selectedCount++;
                        selectedDays[key].selected = 1;
                        selectedDays[key].eventPanel.classList.add('selected_day_slot');
                        object._console.log(selectedDays[key]);
                        
                    }
                    
                }
                
                if (selectedCount > 0) {
                    
                    addTimeSlotsButton.disabled = false;
                    clearButton.disabled = false;
                    
                }
                
            };
            
        });
        
        var addTimeSlotsButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get('Add time slots') );
        addTimeSlotsButton.disabled = true;
        multipleDaysButtonPanelForSchedules.appendChild(addTimeSlotsButton);
        addTimeSlotsButton.onclick = function(event) {
            
            var addDays = [];
            for (var key in selectedDays) {
                
                var dayPanel = selectedDays[key];
                if (dayPanel.selected === 1) {
                    
                    object._console.log(dayPanel);
                    addDays.push(dayPanel.key);
                    
                }
                
            }
            
            object._console.log(addDays);
            object.createSchedulesForMultipleDays(account, addDays.join(','), function(callback) {
                
                
                
            });
            
        };
        
        var clearButton = object.createButton(null, null, 'w3tc-button-save button-primary button_margin_left_10px', null, object._i18n.get('Clear') );
        clearButton.disabled = true;
        //multipleDaysButtonPanelForSchedules.appendChild(clearButton);
        clearButton.onclick = function(event) {
            
            selectedCount = 0;
            addTimeSlotsButton.disabled = true;
            clearButton.disabled = true;
            for (var key in selectedDays) {
                
                var dayPanel = selectedDays[key];
                dayPanel.selected = 0;
                dayPanel.eventPanel.classList.remove('selected_day_slot');
                object._console.log(dayPanel);
                
            }
            
        };
        
        var closeButton = object.createButton(null, null, 'w3tc-button-save button-primary button_margin_left_10px', null, object._i18n.get('Cancel') );
        multipleDaysButtonPanelForSchedules.appendChild(closeButton);
        closeButton.onclick = function(event) {
            
            selectedCount = 0;
            actionButtonsPanelForSchedules.classList.remove('hidden_panel');
            multipleDaysButtonPanelForSchedules.classList.add('hidden_panel');
            multipleDaysButtonPanelForSchedules.textContent = null;
            var calendarHeader = calendarPanel.querySelectorAll('.calendarHeader');
            calendarHeader.forEach(function(element) {
                
                element.setAttribute('style', null);
                
            });
            
            var schedulesPanels = calendarPanel.querySelectorAll('.calendar');
            var countSchedulePanels = 0;
            object._console.log(schedulesPanels);
            schedulesPanels.forEach(function(element) {
                
                if (countSchedulePanels === 0) {
                    
                    element.classList.remove('hidden_panel');
                    
                } else {
                    
                    calendarPanel.removeChild(element);
                    
                }
                countSchedulePanels++;
                
            });
            
            arrowLeft.forEach(function(element) {
                
                element.classList.remove('hidden_panel');
                
            });
            
            arrowRight.forEach(function(element) {
                
                element.classList.remove('hidden_panel');
                
            });
            
            var calendarTitle = calendarPanel.querySelectorAll('.calendarData');
            calendarTitle.forEach(function(element) {
                
                object._console.log(element);
                element.classList.remove('selectAllDays');
                element.setAttribute('style', '');
                element.onclick = null;
                
            });
            
        };
        
        var messagePanel = object.create('div', object._i18n.get('Select multiple days to add booking time slots.'), null, null, null, null, null);
        if (object._isExtensionsValid === 0) {
            
            messagePanel.classList.add('extensionsValid');
            messagePanel.textContent = object._i18n.get('Paid plan subscription required.');
            
        }
        multipleDaysButtonPanelForSchedules.appendChild(messagePanel);
        
    };
    
    SCHEDULE.prototype.createSchedulesForMultipleDays = function(account, multipleDays, callback) {
        
        var object = this;
        var confirm = new Confirm(object._debug);
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
    	var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        object._console.log(weekName);
        document.getElementById("media_frame_content_for_schedule").textContent = null;
        var edit_title = document.getElementById("edit_title_for_schedule");
        edit_title.textContent = object._i18n.get('Multiple days');
        
        document.getElementById("media_router_for_schedule").classList.remove("hidden_panel");
        document.getElementById("menu_panel_for_schedule").setAttribute("class", "media_frame_menu hidden_panel");
        document.getElementById("media_title_for_schedule").classList.add("media_left_zero");
        document.getElementById("media_router_for_schedule").classList.add("media_left_zero");
        document.getElementById("media_frame_content_for_schedule").setAttribute("class", "media_left_zero");
        document.getElementById("frame_toolbar_for_schedule").setAttribute("class", "media_frame_toolbar media_left_zero");
        
        var incompletelyDeletedScheduleAlertPanel = document.getElementById('incompletelyDeletedScheduleAlertPanel');
        incompletelyDeletedScheduleAlertPanel.classList.add('hidden_panel');
        
        incompletelyDeletedScheduleAlertPanel.onclick = function() {
            
            this.classList.add('hidden_panel');
            
        }
        
        object.buttonPanel.textContent = null;
        
        var bookAndSaveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert', null, object._i18n.get("Book the publication date") );
        var saveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert button_margin_left_10px', null, object._i18n.get("Save") );
        object.buttonPanel.appendChild(bookAndSaveButton);
        object.buttonPanel.appendChild(saveButton);
        
        bookAndSaveButton.onclick = function(event) {
            
            object._console.log(this);
            object.bookAndSaveDays(account, null, function(publishingDate) {
                
                object._console.log(publishingDate);
                object.updateAccountSchedule(object.template_schedule_list, "addAccountSchedule", account, multipleDays, publishingDate.date, null, function(response) {
                    
                    object._console.log(response);
                    if (response.status != null && response.status == 'success') {
                        
                        document.getElementById("editPanelForSchedule").setAttribute("class", "hidden_panel");
                        document.getElementById("blockPanel").setAttribute("class", "hidden_panel");
                        document.getElementsByTagName("body")[0].classList.remove("modal-open");
                        
                    }
                    
                });
                
            });
            
        };
        
        saveButton.onclick = function(event) {
            
            if (object._isExtensionsValid === 0) {
                
                confirm.alertPanelShow(object._i18n.get("Warning"), object._i18n.get("Paid plan subscription required."), false, null);
                return null;
                
            }
            object.updateAccountSchedule(object.template_schedule_list, "addAccountSchedule", account, multipleDays, null, null, function(response) {
                
                object._console.log(response);
                if (response.status != null && response.status == 'success') {
                    
                    document.getElementById("editPanelForSchedule").setAttribute("class", "hidden_panel");
                    document.getElementById("blockPanel").setAttribute("class", "hidden_panel");
                    document.getElementsByTagName("body")[0].classList.remove("modal-open");
                    
                }
                
            });
            
        };
        object.createSchedulePanel(object.weekKey, [], 'getPublishedTimeSlots');
        
        object.editPanelShow(true);
        
    };
    
    SCHEDULE.prototype.getPublishedTimeSlots = function(month, day, year, account, hasIncompletelySchedules, addedPublishingDate, callback){
        
        var object = this;
        object._console.log(account);
        object._console.log(addedPublishingDate);
        
        
        var deleteButton = object.createButton(null, 'margin-right: 10px;', 'button media-button button-primary button-large media-button-insert', null, object._i18n.get("Delete") );
        var bookAndSaveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert', null, object._i18n.get("Book the publication date") );
        var saveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert button_margin_left_10px', null, object._i18n.get("Save") );
        var deleteSchedulesButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get("Delete") );
        
        object.buttonPanel.textContent = null;
        if (hasIncompletelySchedules == 1) {
            
            object.buttonPanel.appendChild(deleteSchedulesButton);
            
        }
        
        object.buttonPanel.appendChild(bookAndSaveButton);
        object.buttonPanel.appendChild(saveButton);
        
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {nonce: object.nonce, action: object.action, mode: 'getPublishedTimeSlots', month: month, day: day, year: year};
        if(account != null){
            
            post.accountKey = account.key;
            
        }
        object.setFunction("getPublishedTimeSlots", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
            object._console.log(json);
            object.createSchedulePanel(object.weekKey, json, 'getPublishedTimeSlots');
            object.loadingPanel.setAttribute("class", "hidden_panel");
                
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
        deleteButton.onclick = function(event) {
            
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            var post = {nonce: object.nonce, action: object.action, mode: "deletePublicSchedule", month: month, day: day, year: year};
            object.setFunction("getPublishedTimeSlots", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                callback(json);
                object.loadingPanel.setAttribute("class", "hidden_panel");
                object._console.log(json);
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            
        };
        
        bookAndSaveButton.onclick = function(event) {
            
            object._console.log(this);
            object.bookAndSaveDays(account, addedPublishingDate, function(publishingDate) {
                
                object._console.log(publishingDate);
                object.updateAccountSchedule(object.template_schedule_list, "updateAccountSchedule", account, null, publishingDate.date, null, function(response) {
                    
                    object._console.log(response);
                    if (response.status != null && response.status == 'success') {
                        
                        document.getElementById("editPanelForSchedule").setAttribute("class", "hidden_panel");
                        document.getElementById("blockPanel").setAttribute("class", "hidden_panel");
                        document.getElementsByTagName("body")[0].classList.remove("modal-open");
                        
                    }
                    
                });
                
            });
            
        };
        
        saveButton.onclick = function(event) {
            
            object.updateAccountSchedule(object.template_schedule_list, "updateAccountSchedule", account, null, null, null, function(response) {
                
                object._console.log(response);
                if (response.status != null && response.status == 'success') {
                    
                    document.getElementById("editPanelForSchedule").setAttribute("class", "hidden_panel");
                    document.getElementById("blockPanel").setAttribute("class", "hidden_panel");
                    document.getElementsByTagName("body")[0].classList.remove("modal-open");
                    
                }
                
            });
            
        };
        
        deleteSchedulesButton.onclick = function(event) {
            
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            var post = {nonce: object.nonce, action: object.action, mode: "deletePerfectPublicSchedule", month: month, day: day, year: year, accountKey: account.key};
            object.setFunction("getPublishedTimeSlots", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                object.loadingPanel.setAttribute("class", "hidden_panel");
                object._console.log(json);
                if (json.status != null && json.status == 'success') {
                    
                    document.getElementById("editPanelForSchedule").setAttribute("class", "hidden_panel");
                    document.getElementById("blockPanel").setAttribute("class", "hidden_panel");
                    document.getElementsByTagName("body")[0].classList.remove("modal-open");
                    
                }
                
                var date = object.getCalendarDate();
                object._console.log(date);
                object.getAccountScheduleData(parseInt(date.month), 1, parseInt(date.year), account, 1);
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            
        };
        
    };
    
    SCHEDULE.prototype.bookAndSaveDays = function(account, addedPublishingDate, callback) {
        
        var object = this;
        object._console.log(addedPublishingDate);
        var calendarPanelForBookingDate = document.getElementById('calendarPanelForBookingDate');
        calendarPanelForBookingDate.classList.remove('hidden_panel');
        var topPanel = document.createElement("div");
        var calendarPanel = document.createElement('div');
        
        var inputPanel = calendarPanelForBookingDate.getElementsByClassName('inputPanel')[0];
        inputPanel.textContent = null;
        
        object._console.log(calendarPanelForBookingDate);
        object._console.log(inputPanel);
        //object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var load_blockPanel = document.getElementById("load_blockPanel");
        load_blockPanel.classList.remove("hidden_panel");
        load_blockPanel.classList.add("edit_modal_backdrop");
        
        var saveForBookingTime = document.getElementById('saveForBookingTime');
        var deleteForBookingDateButton = document.getElementById('deleteForBookingDate');
        deleteForBookingDateButton.classList.add('hidden_panel');
        deleteForBookingDateButton.disabled = false;
        var saveForBookingDateButton = document.getElementById('saveForBookingDate');
        saveForBookingDateButton.textContent = object._i18n.get('Save');
        if (account.type == 'hotel') {
            
            deleteForBookingDateButton.classList.remove('hidden_panel');
            saveForBookingDateButton.textContent = object._i18n.get('Apply');
            
        }
        if (object._isExtensionsValid === 0) {
            
            saveForBookingDateButton.disabled = true;
            
        }
        
        document.getElementById("calendarPanelForBookingDate_return_button").onclick = function() {
            
            calendarPanelForBookingDate.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        load_blockPanel.onclick = function() {
            
            calendarPanelForBookingDate.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        var today = new Date();
        object._bookPublishingDate.day = null;
        if (object._bookPublishingDate.month == null && object._bookPublishingDate.year == null) {
            
            object._bookPublishingDate.month = today.getMonth() + 1;
            object._bookPublishingDate.year = today.getFullYear();
            
        }
        
        if (addedPublishingDate != null) {
            
            object._bookPublishingDate.month = parseInt(addedPublishingDate.month);
            object._bookPublishingDate.year = parseInt(addedPublishingDate.year);
            object._bookPublishingDate.day = parseInt(addedPublishingDate.day);
            
            var saveForBookingTime = document.getElementById('saveForBookingTime');
            var options = saveForBookingTime.options;
            object._console.log(options);
            for (var i = 0; i < options.length; i++) {
                
                if (parseInt(options[i].value) === parseInt(addedPublishingDate.hour)) {
                    
                    object._console.log(options[i]);
                    options[i].selected = true;
                    
                }
                
            }
            
        } else {
            
            deleteForBookingDateButton.disabled = true;
            
        }
        
        object._console.log(object._bookPublishingDate);
        
        var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
        //calendar.setClock(object.clockFormat);
        calendar.setShortMonthNameBool(true);
        calendar.setShortWeekNameBool(true);
        var calendarData = calendar.createCalendarData(object._bookPublishingDate.month, object._bookPublishingDate.year);
        
        var datePanel = document.createElement("div");
        var returnLabel = document.createElement("label");
        var nextLabel = document.createElement("label");
        var topPanel = calendar.createHeader(calendarData.date.month, calendarData.date.year, 0, false);
        if (topPanel.querySelector('#current_date_in_header') != null) {
            
            datePanel = topPanel.querySelector('#current_date_in_header');
            console.error(datePanel);
            
        }
        
        if (topPanel.querySelector('#change_calendar_return') != null) {
            
            returnLabel = topPanel.querySelector('#change_calendar_return');
            
        }
        
        if (topPanel.querySelector('#change_calendar_next') != null) {
            
            nextLabel = topPanel.querySelector('#change_calendar_next');
            
        }
        inputPanel.appendChild(topPanel);
        inputPanel.appendChild(calendarPanel);
        
        var selectedDate = null;
        var createCalendar = function(calendar, calendarData, addedPublishingDate, selected) {
            
            object._console.log(addedPublishingDate);
            var days = {};
            calendar.create(calendarPanel, calendarData, calendarData.date.month, 1, calendarData.date.year, '', function(callback){
                
                //object._console.log(callback);
                if (addedPublishingDate != null && parseInt(addedPublishingDate.day) === callback.day && parseInt(addedPublishingDate.month) === callback.month && parseInt(addedPublishingDate.year) === callback.year) {
                    
                    callback.eventPanel.classList.add('selected_day_slot');
                    selected(callback);
                    
                }
                days[callback.key] = callback;
                callback.eventPanel.onclick = function() {
                    
                    object._console.log(this);
                    for (var key in days) {
                        
                        days[key].eventPanel.classList.remove('selected_day_slot');
                        
                    }
                    this.classList.add('selected_day_slot');
                    object._console.log(this.getAttribute('data-key'));
                    selected(days[this.getAttribute('data-key')]);
                    
                };
                
            });
            
            
        };
        
        var setDate = function(calendar, selectedDate) {
            
            var setPublicationDate = document.getElementById('setPublicationDate');
            setPublicationDate.textContent = null;
            if (selectedDate != null) {
                
                var saveForBookingTime = document.getElementById('saveForBookingTime');
                var options = saveForBookingTime.options;
                var time = options[saveForBookingTime.selectedIndex].value;
                object._console.log(selectedDate);
                object._console.log(time);
                setPublicationDate.textContent = calendar.formatBookingDate(selectedDate.month, selectedDate.day, selectedDate.year, time, 0, null, selectedDate.week, 'text');
                document.getElementById('deleteForBookingDate').disabled = false;
                
            }
            
        };
        setDate(calendar, null);
        
        createCalendar(calendar, calendarData, addedPublishingDate, function(selected) {
            
            selectedDate = selected;
            setDate(calendar, selectedDate);
            
        });
        object._console.log(calendarData);
        
        saveForBookingTime.onchange = function() {
            
            setDate(calendar, selectedDate);
            
        };
        
        returnLabel.onclick = function() {
            
            object._console.log(this);
            object._bookPublishingDate.month--;
            if (object._bookPublishingDate.month === 0) {
                
                object._bookPublishingDate.month = 12;
                object._bookPublishingDate.year--;
                
            }
            object._console.log('month = ' + object._bookPublishingDate.month + ' year = ' + object._bookPublishingDate.year);
            var calendarData = calendar.createCalendarData(object._bookPublishingDate.month, object._bookPublishingDate.year);
            datePanel.textContent = calendar.formatBookingDate(calendarData.date.month, null, calendarData.date.year, null, null, null, null, 'text');
            calendarPanel.textContent = null;
            createCalendar(calendar, calendarData, addedPublishingDate, function(selected) {
                
                selectedDate = selected;
                setDate(calendar, selectedDate);
                
            });
            
        };
        
        nextLabel.onclick = function() {
            
            object._console.log(this);
            object._bookPublishingDate.month++;
            if (object._bookPublishingDate.month === 13) {
                
                object._bookPublishingDate.month = 1;
                object._bookPublishingDate.year++;
                
            }
            object._console.log('month = ' + object._bookPublishingDate.month + ' year = ' + object._bookPublishingDate.year);
            var calendarData = calendar.createCalendarData(object._bookPublishingDate.month, object._bookPublishingDate.year);
            datePanel.textContent = calendar.formatBookingDate(calendarData.date.month, null, calendarData.date.year, null, null, null, null, 'text');
            calendarPanel.textContent = null;
            createCalendar(calendar, calendarData, addedPublishingDate, function(selected) {
                
                selectedDate = selected;
                setDate(calendar, selectedDate);
                
            });
            
        };
        
        deleteForBookingDateButton.onclick = function(event) {
            
            calendarPanelForBookingDate.classList.add('hidden_panel');
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            callback(null);
            
        };
        
        saveForBookingDateButton.onclick = function(event) {
            
            object._console.log(this);
            var options = saveForBookingTime.options;
            var time = options[saveForBookingTime.selectedIndex].value;
            object._console.log(time);
            if (selectedDate != null) {
                
                calendarPanelForBookingDate.classList.add('hidden_panel');
                load_blockPanel.classList.add("hidden_panel");
                load_blockPanel.classList.remove("edit_modal_backdrop");
                selectedDate.date = selectedDate.key + time + '00';
                selectedDate.time = time;
                callback(selectedDate);
                
            }
            
        };
        
    };
    
    SCHEDULE.prototype.getTemplateSchedule = function(weekKey, account){
        
        var object = this;
        var saveButton = object.createButton(null, null, 'button media-button button-primary button-large media-button-insert', null, object._i18n.get("Save") );
        object.buttonPanel.textContent = null;
        object.buttonPanel.appendChild(saveButton);
        
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {nonce: object.nonce, action: object.action, mode: 'getTemplateSchedule', weekKey: weekKey};
        if (account != null) {
            
            post.accountKey = account.key;
            
        }
        
        object.setFunction("getTemplateSchedule", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
            object._console.log(json);
            object.createSchedulePanel(weekKey, json, 'getTemplateSchedule');
            object.loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
            
            saveButton.onclick = function(event){
                
                object.updateAccountSchedule(object.template_schedule_list, "updateAccountTemplateSchedule", account, null, null, json, function(response) {
                    
                    object._console.log(response);
                    if (response.status == 'success') {
                        
                        object.getTemplateSchedule(weekKey, account);
                        
                    }
                    
                });
                
            };
                
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
    };

    SCHEDULE.prototype.createSchedulePanel = function(weekKey, scheduleData, mode){
        
        var object = this;
        object._console.log('createSchedulePanel');
        object._console.log("weekKey = " + weekKey);
        object._console.log("mode = " + mode);
        object._console.log(scheduleData);
        this.mode = mode;
        this.weekKey = weekKey;
        var media_frame_content = document.getElementById("media_frame_content_for_schedule");
        media_frame_content.classList.remove('hidden_panel');
        media_frame_content.textContent = null;
        object.template_schedule_list = {};
        
        var timePanelList = {};
        var hourBlockList = {};
        var minBlockList = {};
        var deadlineTimeList = {};
        var capacityBlockList = {};
        var remainderBlockList = {};
        var dotList = {};
        
        if (scheduleData.length == 0) {
            
            var loadSchedulesPanel = document.getElementById("loadSchedulesPanel");
            loadSchedulesPanel.classList.remove("hidden_panel");
            var load_blockPanel = document.getElementById("load_blockPanel");
            load_blockPanel.classList.remove("hidden_panel");
            load_blockPanel.classList.add("edit_modal_backdrop");
            document.getElementById("loadSchedulesPanel_return_button").onclick = function() {
                
                loadSchedulesPanel.classList.add("hidden_panel");
                load_blockPanel.classList.add("hidden_panel");
                load_blockPanel.classList.remove("edit_modal_backdrop");
                
            };
            
            load_blockPanel.onclick = function() {
                
                loadSchedulesPanel.classList.add("hidden_panel");
                load_blockPanel.classList.add("hidden_panel");
                load_blockPanel.classList.remove("edit_modal_backdrop");
                
            };
            
            document.getElementById("readSchedulesButton").onclick = function() {
                
                var from = document.getElementById("read_from_on_time");
                var [fromHours, fromMinutes] = from.value.split(":");
                fromHours = parseInt(fromHours, 10);
                fromMinutes = parseInt(fromMinutes, 10);
                
                var to = document.getElementById("read_to_on_time");
                var [toHours, toMinutes] = to.value.split(":");
                toHours = parseInt(toHours, 10);
                toMinutes = parseInt(toMinutes, 10);
                
                /**
                var from = document.getElementById("read_from_hour_on_time");
                object._console.log(from.selectedIndex);
                if (from.options[from.selectedIndex].value == null) {
                    
                    from = from.options[0].value;
                    
                } else {
                    
                    from = from.options[from.selectedIndex].value;
                    
                }
                
                var fromMinutes = document.getElementById("read_from_min_on_time");
                if (fromMinutes.options[fromMinutes.selectedIndex].value == null) {
                    
                    fromMinutes = 0;
                    
                } else {
                    
                    fromMinutes = fromMinutes.options[fromMinutes.selectedIndex].value;
                    
                }
                
                var to = document.getElementById("read_to_hour_on_time");
                object._console.log(to.selectedIndex);
                to = to.options[to.selectedIndex].value;
                object._console.log(to);
                
                var toMinutes = document.getElementById("read_to_min_on_time");
                if (toMinutes.options[toMinutes.selectedIndex].value == null) {
                    
                    toMinutes = 0;
                    
                } else {
                    
                    toMinutes = toMinutes.options[toMinutes.selectedIndex].value;
                    
                }
                **/
                
                var interval = document.getElementById("interval_min_on_time");
                object._console.log(interval.selectedIndex);
                interval = interval.options[interval.selectedIndex].value;
                var load_deadline_time = document.getElementById("load_deadline_time_on_time");
                object._console.log(load_deadline_time.selectedIndex);
                load_deadline_time = load_deadline_time.options[load_deadline_time.selectedIndex].value;
                var load_capacity = document.getElementById("load_capacity");
                load_capacity = load_capacity.options[load_capacity.selectedIndex].value;
                object._console.log("fromHours = " + fromHours + " toHours = " + toHours + " interval = " + interval + " load_deadline_time = " + load_deadline_time + " load_capacity = " + load_capacity);
                var readList = [];
                var base_min = 0;
                for (var time = (parseInt(fromHours) * 60) + parseInt(fromMinutes); time <= (parseInt(toHours) * 60) + parseInt(toMinutes); time += parseInt(interval)) {
                    
                    readList.push({hour: Math.floor(time / 60), min: time % 60, deadline: load_deadline_time, capacities: load_capacity, remainders: load_capacity});
                    
                 }
                
                object._console.log(readList);
                var i = 0;
                var count = readList.length;
                for (var key in timePanelList) {
                    
                    if (i < count && readList[i] != null) {
                        
                        var read = readList[i];
                        var timePanel = timePanelList[key];
                        timePanel.classList.add("hidden_panel");
                        hourBlockList[key].classList.remove("hidden_panel");
                        hourBlockList[key].classList.add("hour");
                        hourBlockList[key].setAttribute("data-key", read.hour);
                        hourBlockList[key].textContent = ("0" + read.hour).slice(-2);
                        dotList[key].setAttribute("class", "dot");
                        minBlockList[key].classList.remove("hidden_panel");
                        minBlockList[key].classList.add("hour");
                        minBlockList[key].setAttribute("data-key", read.min);
                        minBlockList[key].textContent = ("0" + read.min).slice(-2);
                        
                        deadlineTimeList[key].setAttribute("class", "min");
                        deadlineTimeList[key].setAttribute("data-key", read.deadline);
                        deadlineTimeList[key].textContent = object._i18n.get("%s min before", [read.deadline]);
                        
                        capacityBlockList[key].setAttribute("class", "person");
                        capacityBlockList[key].setAttribute("data-key", read.capacities);
                        capacityBlockList[key].textContent = read.capacities;
                        
                        remainderBlockList[key].setAttribute("class", "person");
                        remainderBlockList[key].setAttribute("data-key", read.remainders);
                        remainderBlockList[key].textContent = read.remainders;
                        
                    } else {
                        
                        break;
                        
                    }
                    
                    i++;
                    
                }
                
                loadSchedulesPanel.classList.add("hidden_panel");
                load_blockPanel.classList.add("hidden_panel");
                load_blockPanel.classList.remove("edit_modal_backdrop");
                
            };
            
        }
        
        var th = object.create('td', 'No', null, null, null, null, null);
        var tdTime = object.create('td', object._i18n.get('Time'), null, null, null, null, null);
        var tdDeadlineTime = object.create('td', object._i18n.get('Booking end time'), null, 'deadlineTime', null, null, null);
        var tdTitle = object.create('td', object._i18n.get('Title'), null, null, null, null, null);
        var tdCapacities = object.create('td', object._i18n.get('Available slots'), null, null, null, null, null);
        var tdRemaining = object.create('td', object._i18n.get('Remaining slots'), null, 'remainder', null, null, null);
        var stopSlots = object.create('div', object._i18n.get('Paused'), null, null, null, 'deletePanel', null);
        var tdStop = object.create('td', null, [stopSlots], 'stop', null, null, null);
        var deleteSlots = object.create('div', object._i18n.get('Delete'), null, null, null, 'deletePanel', null);
        var tdDelete = object.create('td', null, [deleteSlots], 'allScheduleDelete', null, null, null);
        
        var thead = object.create('thead', null, [th, tdTime, tdDeadlineTime, tdTitle, tdCapacities], null, 'width: 100%; background: #fff; position: sticky; top: 0; left: 0; z-index: 1;', null, null);
        if (mode == 'getPublishedTimeSlots') {
            
            thead.appendChild(tdRemaining);
            
        }
        thead.appendChild(tdStop);
        thead.appendChild(tdDelete);
        
        var tbody = document.createElement('tbody');
        
        var table = object.create('table', null, [thead, tbody], 'time_slots_table', 'border: 0;', 'wp-list-table widefat fixed striped', null);
        media_frame_content.appendChild(table);
        for(var i = 0; i < 300; i++){
            
            var th = object.create('th', (i + 1), null, null, null, null, null);
            var hourBlock = object.create('div', '', null, null, null, 'hour', {select: i} );
            hourBlock.onclick = function(event){
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "deadline", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            };
            
            var dot = object.create('span', ' : ', null, null, null, 'dot', null);
            var minBlock = object.create('div', '', null, null, null, 'min', {select: i} );
            minBlock.onclick = function(event){
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "deadline", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            };
            
            var timePanel = object.create('div', 'access_time', [hourBlock, dot, minBlock], null, null, 'material-icons noTime', {select: i} );
            timePanel.onclick = function() {
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "deadline", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            };
            
            var timeTd = object.create('td', null, [timePanel, hourBlock, dot, minBlock], 'time_slot_id_' + i, null, 'timeTd', null);
            /**
            timeTd.onclick = function(event) {
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "deadline", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            };
            **/
            var deadlineTimeBlock = object.create('div', 'access_time', null, null, null, 'material-icons noTime', {select: i} );
            var deadlineTimeTd = object.create('td', null, [deadlineTimeBlock], null, null, 'td_width_100_px', null);
            deadlineTimeBlock.onclick = function(){
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "deadline", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
                
            };
            
            var titleBox = object.create('input', null, null, null, null, 'regular-text title_text_box', null);
            titleBox.type = "text";
            var titleTd = object.create('td', null, [titleBox], null, null, null, null);
            
            var capacityBlock = object.create('div', 'person_add', null, null, null, 'material-icons noPerson', {select: i} );
            var capacityTd = object.create('td', null, [capacityBlock], null, null, 'td_width_50_px', null);
            capacityBlock.onclick = function(event){
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "capacitys", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            }
            
            var remainderBlock = object.create('div', 'person_add', null, null, null, 'material-icons noPerson', {select: i} );
            var remainderTd = object.create('td', null, [remainderBlock], null, null, 'td_width_50_px', null);
            remainderBlock.onclick = function(event){
                
                object._console.log(this);
                var select = this.getAttribute("data-select");
                var values = getScheduleObject(this, "remainders", mode, object.template_schedule_list[select]);
                object._console.log(values);
                
            }
            
            timePanelList[i] = timePanel;
            hourBlockList[i] = hourBlock;
            minBlockList[i] = minBlock;
            deadlineTimeList[i] = deadlineTimeBlock;
            dotList[i] = dot;
            capacityBlockList[i] = capacityBlock;
            remainderBlockList[i] = remainderBlock;
            
            var checkBox = document.createElement("input");
            checkBox.setAttribute('data-key', i);
            checkBox.name = "check_" + i;
            checkBox.type = "checkbox";
            checkBox.value = "true";
            checkBox.onclick = function(event) {
                
                var key = this.getAttribute('data-key');
                var timeId = document.getElementById('time_slot_id_' + key);
                timeId.classList.remove('disabled');
                if (this.checked === true) {
                    
                    timeId.classList.add('disabled');
                    
                }
                
            };
            var stopTd = object.create('td', null, [checkBox], null, null, 'td_width_50_px', null);
            
            var deletekBox = document.createElement("input");
            deletekBox.name = "check_" + i;
            deletekBox.type = "checkbox";
            deletekBox.value = "true";
            var deleteTd = object.create('td', null, [deletekBox], null, null, 'td_width_50_px', null);
            
            var tr = object.create('tr', null, [th, timeTd, deadlineTimeTd, titleTd, capacityTd, remainderTd, stopTd, deleteTd], 'time_slots_' + i, null, null, null);
            tbody.appendChild(tr);
            
            if (mode != 'getPublishedTimeSlots') {
                
                tr.removeChild(remainderTd);
                
            }
            
            var schedule_data = {hour: hourBlock, min: minBlock, title: titleBox, cost: null, capacity: capacityBlock, remainder: remainderBlock, stop: checkBox, delete: deletekBox, deadlineTime: deadlineTimeBlock};
            
            if (scheduleData[i] != null) {
                
                object._console.log(scheduleData[i]);
                
                timePanel.setAttribute("class", "hidden_panel");
                
                hourBlock.textContent = (0 + scheduleData[i]['hour']).slice(-2);
                hourBlock.setAttribute("data-key", scheduleData[i]['hour']);
                hourBlock.setAttribute("data-default", scheduleData[i]['hour']);
                hourBlock.setAttribute("class", "hour");
                
                dot.setAttribute("class", "dot");
                
                minBlock.textContent = (0 + scheduleData[i]['min']).slice(-2);
                minBlock.setAttribute("data-key", scheduleData[i]['min']);
                minBlock.setAttribute("data-default", scheduleData[i]['min']);
                minBlock.setAttribute("class", "min");
                
                var deadlineTime = scheduleData[i]['deadlineTime'];
                deadlineTimeBlock.textContent = object._i18n.get("%s min before", [deadlineTime]);
                deadlineTimeBlock.setAttribute("data-key", scheduleData[i]['deadlineTime']);
                deadlineTimeBlock.setAttribute("data-default", scheduleData[i]['deadlineTime']);
                deadlineTimeBlock.setAttribute("class", "min");
                
                if (typeof scheduleData[i]['title'] == "string") {
                    
                    titleBox.value = scheduleData[i]['title'].replace(/\\/g, "");
                    
                }
                
                capacityBlock.textContent = scheduleData[i]['capacity'];
                capacityBlock.setAttribute("data-key", scheduleData[i]['capacity']);
                capacityBlock.setAttribute("data-default", scheduleData[i]['capacity']);
                capacityBlock.setAttribute("class", "person");
                
                if (scheduleData[i]['stop'] == 'true') {
                    
                    timeTd.classList.add('disabled');
                    checkBox.checked = true;
                    
                }
                
                if (mode == 'getPublishedTimeSlots') {
                    
                    remainderBlock.textContent = scheduleData[i]['remainder'];
                    remainderBlock.setAttribute("data-key", scheduleData[i]['remainder']);
                    remainderBlock.setAttribute("data-default", scheduleData[i]['remainder']);
                    remainderBlock.setAttribute("class", "person");
                    
                    if (scheduleData[i]['delete'] != null && scheduleData[i]['delete'] == 'true') {
                    
                        checkBox.checked = true;
                    
                    }
                    
                    if (scheduleData[i]['key'] != null) {
                        
                        schedule_data['key'] = scheduleData[i]['key'];
                        
                    }
                    
                } else {
                    
                    if (scheduleData[i]['key'] != null) {
                        
                        schedule_data['key'] = scheduleData[i]['key'];
                        
                    }
                    
                }
                
            } else {
                
                timePanel.setAttribute("class", "material-icons noTime");
                hourBlock.setAttribute("class", "hidden_panel");
                dot.setAttribute("class", "hidden_panel");
                minBlock.setAttribute("class", "hidden_panel");
                
            }
            
            object._console.log(schedule_data);
            object.template_schedule_list[i] = schedule_data;
            
            
        }
        
        function getScheduleObject(panel, default_key, mode, scheduleObject, callback) {
            
            object._console.log(scheduleObject);
            object._console.log("mode = " + mode);
            var values = {};
            var list = {hour: "hours", min: "minutes", deadlineTime: "deadline", capacity: "capacitys", remainder: "remainders"};
            for (var key in list) {
                
                var value = list[key];
                object._console.log(scheduleObject[key]);
                if (scheduleObject[key].getAttribute("data-key") == null) {
                    
                    values[value] = 0;
                    
                } else {
                    
                    values[value] = parseInt(scheduleObject[key].getAttribute("data-key"));
                    
                }
                
            }
            
            if (mode == 'getTemplateSchedule') {
                
                values.remainders = null;
                
            }
            
            object._console.log(panel);
            //var panel = this;
            var select = panel.getAttribute("data-select");
            object._console.log(values);
            object.selectionSchedulePanel(default_key, values.hours, values.minutes, values.deadline, values.capacitys, values.remainders, function(response) {
                
                object._console.log(response);
                var list = {hour: "hours", min: "minutes", deadlineTime: "deadline", capacity: "capacitys", remainder: "remainders"};
                if (response == null) {
                    
                    object.template_schedule_list[select].delete.checked = true;
                    timePanelList[select].textContent = "access_time";
                    timePanelList[select].setAttribute("class", "material-icons noTime");
                    dotList[select].setAttribute("class", "hidden_panel");
                    for (var key in list) {
                        
                        var value = list[key];
                        object.template_schedule_list[select][key].removeAttribute("data-key");
                        object.template_schedule_list[select][key].textContent = "";
                        if (key == "deadlineTime") {
                            
                            object.template_schedule_list[select][key].setAttribute("class", "material-icons noTime");
                            object.template_schedule_list[select][key].textContent = "access_time";
                            
                        } else if (key == "capacity" || key == "remainder") {
                            
                            object.template_schedule_list[select][key].setAttribute("class", "material-icons noPerson");
                            object.template_schedule_list[select][key].textContent = "person_add";
                            
                        } else {
                            
                            object.template_schedule_list[select][key].setAttribute("class", "hidden_panel");
                            object.template_schedule_list[select][key].textContent = "";
                            
                        }
                        
                    }
                    
                } else {
                    
                    object.template_schedule_list[select].delete.checked = false;
                    panel.setAttribute("class", "hidden_panel");
                    dotList[select].setAttribute("class", "dot");
                    for (var key in list) {
                        
                        var value = list[key];
                        object.template_schedule_list[select][key].setAttribute("data-key", response[value]);
                        
                        object.template_schedule_list[select][key].setAttribute("class", "hour");
                        if (key == "deadlineTime") {
                            
                            object.template_schedule_list[select][key].textContent = object._i18n.get("%s min before", [response[value]]);
                            
                        } else if (key == 'hour' || key == 'min') {
                            
                            object.template_schedule_list[select][key].textContent = ("0" + response[value]).slice(-2);;
                            
                        } else {
                            
                            object.template_schedule_list[select][key].textContent = response[value];
                            
                        }
                        
                    }
                    
                }
                
            });
            
            return values;
            
        };
        
        var selectAction = function(panel, targetPanel, title, classTrue, classFalse, max, interval, callback){
            
            var key = null;
            if(panel.getAttribute("data-key") != null){
                
                key = panel.getAttribute("data-key");
            
            }
            var rect = panel.getBoundingClientRect();
            var confirm = new Confirm(object._debug);
            title = object._i18n.get("Choose %s", [object._i18n.get(title)]);
            confirm.selectListPanel(panel, title, 0, max, parseInt(interval), 5, key, function(response){
                
                object._console.log(response);
                if(response !== false && callback != null){
                    
                    object._console.log("response = " + response);
                    if(response != "--" && response != "close"){
                        
                        targetPanel.textContent = ("0" + response).slice(-2);
                        targetPanel.setAttribute("data-key", response);
                        targetPanel.setAttribute("class", classTrue);
                        callback(true);
                        
                    }else if(response == "close"){
                        
                        callback(response);
                        
                    }else{
                        
                        //panel.textContent = "--";
                        targetPanel.removeAttribute("data-key");
                        targetPanel.setAttribute("class", classFalse);
                        callback(false);
                        
                    }
                    
                    
                }else if(response === false){
                    
                    callback(response);
                    
                }
                
            });
            
        }
        
        if(document.getElementById("allScheduleDelete") != null){
            
            var deleteBool = true;
            var deleteButton = document.getElementById("allScheduleDelete");
            deleteButton.removeEventListener("click", null);
            deleteButton.onclick = function(event){
                
                object._console.log("deleteButton.onclick");
                for (var key in object.template_schedule_list) {
                    
                    if (
                        hourBlockList[parseInt(key)].classList.contains("hidden_panel") === false && 
                        minBlockList[parseInt(key)].classList.contains("hidden_panel") === false && 
                        capacityBlockList[parseInt(key)].classList.contains("hidden_panel") === false
                    ) {
                        
                        object.template_schedule_list[key]["delete"].checked = deleteBool;
                        
                    }
                    
                }
                
                if (deleteBool == false) {
                        
                    deleteBool = true;
                    
                } else {
                    
                    deleteBool = false;
                    
                }
                
            };
            
        }
        
        
        var stopBool = true;
        var stopButton = document.getElementById("stop");
        stopButton.removeEventListener("click", null);
        stopButton.onclick = function(){
            
            object._console.log("stopButton.onclick");
            for (var key in object.template_schedule_list) {
                
                if (
                    hourBlockList[parseInt(key)].classList.contains("hidden_panel") === false && 
                    minBlockList[parseInt(key)].classList.contains("hidden_panel") === false && 
                    capacityBlockList[parseInt(key)].classList.contains("hidden_panel") === false
                ) {
                    
                    object.template_schedule_list[key]["stop"].checked = stopBool;
                    var key = object.template_schedule_list[key]["stop"].getAttribute('data-key');
                    var timeId = document.getElementById('time_slot_id_' + key);
                    timeId.classList.remove('disabled');
                    if (stopBool === true) {
                        
                        timeId.classList.add('disabled');
                        
                    }
                    
                }
                
            }
            
            if (stopBool == false) {
                    
                stopBool = true;
                
            } else {
                
                stopBool = false;
                
            }
            
        };
        
    };
    
    SCHEDULE.prototype.selectionSchedulePanel = function(default_key ,hours, minutes, deadline, capacitys, remainders, callback) {
        
        var object = this;
        object._console.log("default_key = " + default_key);
        var schedule = {hours: hours, minutes: minutes, time: String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0'), deadline: deadline, capacitys: capacitys, remainders: remainders};
        object._console.log(schedule);
        var last_key = "remainders";
        if (remainders == null) {
            
            last_key = "capacitys";
            
        }
        var load_blockPanel = document.getElementById("load_blockPanel");
        load_blockPanel.classList.remove("hidden_panel");
        load_blockPanel.classList.add("edit_modal_backdrop");
        
        var selectionSchedule = document.getElementById("selectionSchedule");
        let input_time = document.getElementById('change_booking_time');
        input_time.value = '00:00';
        
        var items = {};
        var itemsList = selectionSchedule.getElementsByClassName("items");
        for (var i = 0; i < itemsList.length; i++) {
            
            var key = itemsList[i].getAttribute("data-key");
            object._console.log(key);
            object._console.log(schedule[key]);
            
            
            
            if (remainders == null && key == "remainders") {
                
                itemsList[i].classList.add("hidden_panel");
                
            } else {
                
                itemsList[i].classList.remove("hidden_panel");
                
            }
            items[key] = itemsList[i];
            
            if (key === 'time') {
                
                input_time.value = schedule.time;
                
            } else {
                
                itemsList[i].getElementsByTagName("span")[0].textContent = schedule[key];
                if (key === 'deadline') {
                    
                    items[key].getElementsByTagName("span")[0].textContent = object._i18n.get('%s min before', [ schedule[key] ]);
                    
                }
                
            }
            
            
            itemsList[i].onclick = function() {
                
                var key = this.getAttribute("data-key");
                object._console.log(key);
                for (var valueKey in values) {
                    
                    var value = values[valueKey];
                    //value.classList.remove("closed");
                    if (key === 'time') {
                        
                        return null;
                        
                    }
                    
                    if (key == valueKey) {
                        
                        object._console.log(schedule[key])
                        value.classList.remove("closed");
                        value.classList.add("openAnimation");
                        value.classList.remove("closeAnimation");
                        
                    } else {
                        
                        if (value.classList.contains("openAnimation")) {
                            
                            value.classList.add("closeAnimation");
                            value.classList.remove("openAnimation");
                            
                        }
                        
                    }
                    
                }
                
            };
            
        }
        
        var values = {};
        var valuesList = selectionSchedule.getElementsByClassName("selectPanel");
        for (var i = 0; i < valuesList.length; i++) {
            
            valuesList[i].classList.add("closed");
            valuesList[i].classList.remove("openAnimation");
            valuesList[i].classList.remove("closeAnimation");
            
        }
        for (var i = 0; i < valuesList.length; i++) {
            
            var key = valuesList[i].getAttribute("data-key");
            
            if (key == default_key) {
                
                valuesList[i].classList.add("openAnimation");
                valuesList[i].classList.remove("closeAnimation");
                valuesList[i].classList.remove("closed");
                
            } else {
                
                
                
            }
            
            values[key] = valuesList[i];
            var spans = valuesList[i].getElementsByTagName("span");
            for (var a = 0; a < spans.length; a++) {
                
                if (parseInt(schedule[key]) == parseInt(spans[a].getAttribute("data-value"))) {
                    
                    spans[a].classList.add("selectedItem");
                    
                } else {
                    
                    spans[a].classList.remove("selectedItem");
                    
                }
                spans[a].onclick = function() {
                    
                    object._console.log(this);
                    var key = this.getAttribute("data-key");
                    var value = parseInt(this.getAttribute("data-value"));
                    schedule[key] = value;
                    object._console.log(key);
                    object._console.log(schedule);
                    
                    items[key].getElementsByTagName("span")[0].textContent = value;
                    if (key === 'deadline') {
                        
                        items[key].getElementsByTagName("span")[0].textContent = object._i18n.get('%s min before', [value]);
                        
                    }
                    
                    var spans = values[key].getElementsByTagName("span");
                    for (var a = 0; a < spans.length; a++) {
                        
                        if (parseInt(schedule[key]) == parseInt(spans[a].getAttribute("data-value"))) {
                            
                            spans[a].classList.add("selectedItem");
                            
                        } else {
                            
                            spans[a].classList.remove("selectedItem");
                            
                        }
                        
                    }
                    
                    var nextPanel = false;
                    for (var valueKey in values) {
                        
                        object._console.log(valueKey);
                        if (nextPanel == true) {
                            
                            if (values[valueKey].classList.contains("closed")) {
                                
                                values[valueKey].classList.remove("closed");
                                
                            }
                            
                            values[valueKey].classList.remove("closeAnimation");
                            values[valueKey].classList.add("openAnimation");
                            break;
                            
                        }
                        
                        if (valueKey == key && valueKey != last_key) {
                            
                            values[valueKey].classList.remove("openAnimation");
                            values[valueKey].classList.add("closeAnimation");
                            nextPanel = true;
                            
                        }
                        
                    }
                    
                };
                
            }
            
        }
        
        selectionSchedule.classList.remove("hidden_panel");
        
        var selectionSchedule_hours = document.getElementById("selectionSchedule_hours");
        
        var selectionSchedule_minutes = document.getElementById("selectionSchedule_minutes");
        
        var selectionSchedule_capacitys = document.getElementById("selectionSchedule_capacitys");
        
        var selectionSchedule_remainders = document.getElementById("selectionSchedule_remainders");
        
        load_blockPanel.onclick = function() {
            
            selectionSchedule.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        document.getElementById("selectionScheduleButton").onclick = function() {
            
            if (callback != null) {
                
                let [hours, minutes] = input_time.value.split(':');
                schedule.hours = hours;
                schedule.minutes = minutes;
                callback(schedule);
                
            }
            selectionSchedule.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        document.getElementById("selectionScheduleResetButton").onclick = function() {
            
            if (callback != null) {
                
                callback(null);
                
            }
            selectionSchedule.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        document.getElementById("selectionSchedule_return_button").onclick = function() {
            
            selectionSchedule.classList.add("hidden_panel");
            load_blockPanel.classList.add("hidden_panel");
            load_blockPanel.classList.remove("edit_modal_backdrop");
            
        };
        
        
    };
    

    SCHEDULE.prototype.updateAccountSchedule = function(template_schedule_list, mode, account, multipleDays, publishingDate, oldTimeSlots, callback){
        
        var object = this;
        object._console.log(object.weekKey);
        object._console.log(template_schedule_list);
        object._console.log(multipleDays);
        var i = 0;
        var post = {nonce: object.nonce, action: object.action, mode: mode, weekKey: object.weekKey};
        if (mode == 'updateAccountSchedule') {
            
            var date = object.getCalendarDate();
            post = {nonce: object.nonce, action: object.action, mode: mode, weekKey: object.weekKey, month: date['month'], day: date['day'], year: date['year']};
            
        }
        
        if (account != null) {
            
            post.accountKey = account.key;
            
        }
        
        if (multipleDays != null) {
            
            post.multipleDays = multipleDays;
            
        }
        
        if (publishingDate != null) {
            
            post.publishingDate = publishingDate;
            
        }
        
        for (var key in template_schedule_list) {
            
            //object._console.log(data);
            var data = template_schedule_list[key];
            var hourValue = null;
            var minValue = null;
            var capacityValue = null;
            var deadlineTimeValue = 0;
            if (data['hour'].getAttribute("data-key") != null && data['min'].getAttribute("data-key") != null && data['capacity'].getAttribute("data-key") != null) {
                    
                hourValue = data['hour'].getAttribute("data-key");
                minValue = data['min'].getAttribute("data-key");
                capacityValue = data['capacity'].getAttribute("data-key");
                
            } else {
                
                if (data['hour'].getAttribute("data-default") != null && data['min'].getAttribute("data-default") != null && data['capacity'].getAttribute("data-default") != null) {
                    
                    hourValue = data['hour'].getAttribute("data-default");
                    minValue = data['min'].getAttribute("data-default");
                    capacityValue = data['capacity'].getAttribute("data-default");
                    
                }
                
            }
            
            if (data['deadlineTime'].getAttribute("data-default") != null) {
                
                deadlineTimeValue = data['deadlineTime'].getAttribute("data-default");
                
            }
            
            if (data['deadlineTime'].getAttribute("data-key") != null != null) {
                
                deadlineTimeValue = data['deadlineTime'].getAttribute("data-key");
                
            }
            
            if (hourValue != null && minValue != null && capacityValue != null) {
                
                var titleValue = data['title'].value;
                var stopValue = "false";
                if(data['stop'].checked == true){
                    
                    stopValue = "true";
                    
                }
                
                object._console.log("key = " + template_schedule_list[key]['key'] + " " + hourValue + ":" + minValue + " deadlineTime = " + deadlineTimeValue + " capacity = " + capacityValue + " title = " + titleValue + " stop = " + stopValue);
                var postSchedule = {
                    hour: hourValue,
                    min: minValue,
                    deadlineTime: deadlineTimeValue,
                    title: titleValue,
                    cost: "",
                    capacity: capacityValue,
                    remainder: capacityValue,
                    stop: stopValue,
                    delete: "false",
                }
                
                if (mode == 'updateAccountSchedule') {
                    
                    if (template_schedule_list[key]['key'] != null) {
                        
                        postSchedule.key = template_schedule_list[key]['key'];
                        
                    }
                    
                    var remainder = data['remainder'];
                    if (remainder.getAttribute("data-key") != null) {
                        
                        postSchedule.remainder = remainder.getAttribute("data-key");
                        
                    } else {
                        
                        postSchedule.remainder = remainder.getAttribute("data-key");
                        if (remainder.getAttribute("data-default") == null) {
                            
                            postSchedule.remainder = capacityValue;
                            
                        }
                        
                    }
                    
                    var deleteValue = "false";
                    if (data['delete'].checked == true) {
                        
                        deleteValue = "true";
                        
                    }
                    postSchedule.delete = deleteValue;
                    
                } else {
                    
                    postSchedule.key = (i + 1);
                    if (template_schedule_list[key]['key'] != null) {
                        
                        postSchedule.scheduleKey = template_schedule_list[key]['key'];
                        
                    }
                    var deleteValue = "false";
                    if (data['delete'].checked == true) {
                        
                        deleteValue = "true";
                        
                    }
                    postSchedule.delete = deleteValue;
                    
                }
                
                post['schedule' + i] = JSON.stringify(postSchedule);
                
                i++;
                post['timeCount'] = i;
                
            }
            
            
        }
        
        object._console.log(post);
        
        const sendToServer = function(post, callback) {
            
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.setFunction("updateAccountSchedule", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                object.loadingPanel.setAttribute("class", "hidden_panel");
                object._console.log(json);
                if (callback != null) {
                    
                    callback(json);
                    
                }
                
                var date = object.getCalendarDate();
                object._console.log(date);
                object.getAccountScheduleData(parseInt(date.month), 1, parseInt(date.year), account, 1);
                
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            
        }
        
        if (i != 0) {
            
            const verifyItemsAffectingSchedules = (function(mode, newPost, oldTimeSlots) {
                
                if (mode !== 'updateAccountTemplateSchedule') {
                    
                    return false;
                    
                }
                
                object._console.log(oldTimeSlots);
                const sortedTimeSlots = [];
                const newTimeSlots = {};
                const count = parseInt(newPost.timeCount);
                for (var i = 0; i < count; i++) {
                    
                    var timeSlot = JSON.parse(newPost['schedule' + i]);
                    if (timeSlot.delete === 'false') {
                        
                        const key = timeSlot['hour'].toString().padStart(2, '0') + timeSlot['min'].toString().padStart(2, '0');
                        newTimeSlots[key] = timeSlot;
                        
                    }
                    
                }
                
                const keys = Object.keys(newTimeSlots);
                keys.sort();
                keys.forEach(key => {
                    sortedTimeSlots.push(newTimeSlots[key]);
                });
                object._console.log(sortedTimeSlots);
                if (oldTimeSlots.length > 0 && sortedTimeSlots.length !== oldTimeSlots.length) {
                    
                    return true;
                    
                }
                
                for (var i = 0; i < oldTimeSlots.length; i++) {
                    
                    const oldTimeSlot = oldTimeSlots[i];
                    const timeSlot = sortedTimeSlots[i];
                    if (oldTimeSlot.hour !== timeSlot.hour || oldTimeSlot.min !== timeSlot.min || oldTimeSlot.deadlineTime !== timeSlot.deadlineTime || oldTimeSlot.title !== timeSlot.title || oldTimeSlot.capacity !== timeSlot.capacity || oldTimeSlot.stop !== timeSlot.stop) {
                        
                        return true;
                        
                    }
                    
                }
                
                return false;
                
            })(mode, post, oldTimeSlots);
            
            object._console.log(verifyItemsAffectingSchedules);
            if (verifyItemsAffectingSchedules === true) {
                
                object._console.log(post);
                let message = object._i18n.get('The changed values will be reflected in new schedules published in the future.') + "\n";
                message += object._i18n.get('To modify already published schedules, adjustments to the values saved for the respective day need to be made in the "%s" tab.', [object._i18n.get('Schedules')]);
                var confirm = new Confirm(object._debug);
                confirm.alertPanelShow(object._i18n.get("Attention"), message, false, function(bool){
                    
                    sendToServer(post, callback);
                    
                });
                
            } else {
                
                sendToServer(post, callback);
                
            }
            
            
            /**
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.setFunction("updateAccountSchedule", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                object.loadingPanel.setAttribute("class", "hidden_panel");
                object._console.log(json);
                if (callback != null) {
                    
                    callback(json);
                    
                }
                
                var date = object.getCalendarDate();
                object._console.log(date);
                object.getAccountScheduleData(parseInt(date.month), 1, parseInt(date.year), account, 1);
                
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            **/
            
        }
        
        
    };
    
    SCHEDULE.prototype.createCouponsListPanel = function(couponsList, account, callback) {
        
        var editBool = true;
        var object = this;
        
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item"));
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order"));
        saveButton.disabled = true;
        
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton]);
        var coursePanel = document.getElementById("couponsPanel");
        coursePanel.textContent = null;
        
        let enableFunction = false;
        var enableFunctionPanel = object.create('div', object._i18n.get('Despite having valid items, the functionality of "%s" is disabled.', [object._i18n.get('Coupons')]), null, null, 'color: #ff3333;', 'hidden_panel', null);
        var directlySwitchPanel = object.createSwitchPanel( account, 'couponsBool', 'directlySwitchForGuests', 'switch2', 'Enable the function', enableFunctionPanel, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            for (var i in json) {
                
                if (json[i].key == account.key) {
                    
                    account = json[i];
                    object._console.log(account);
                    if (parseInt(account.couponsBool) === 0 && enableFunction === true) {
                        
                        enableFunctionPanel.classList.remove('hidden_panel');
                        
                    } else {
                        
                        enableFunctionPanel.classList.add('hidden_panel');
                        
                    }
                    break;
                    
                }
                
            }
            callback(json);
            
        });
        
        if (account.type == 'hotel') {
            
            directlySwitchPanel.textContent = null;
            
        }
        
        var mainPanel = document.getElementById("couponsPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(directlySwitchPanel);
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'guestsSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {name: 'Name', email: 'Email', zip: 'Zip', D: 'D'};

        for (var key in couponsList) {
            
            if (typeof couponsList[key]['name'] == "string") {
                
                couponsList[key]['name'] = couponsList[key]['name'].replace(/\\/g, "");
                
            }
            
            if (parseInt(couponsList[key].active) === 1) {
                
                enableFunction = true;
                
            }
            
            var contentPanel = object.create('div', couponsList[key]['name'], null, null, null, 'content_block', null);
            var discountValue = object._format.formatCost(couponsList[key]['value'], object._currency);
            if (couponsList[key]['method'] == 'multiplication') {
                
                discountValue = couponsList[key]['value'] + '%';
                
            }
            object._console.log(object._currency);
            
            var discountPanel = object.create('div', object._i18n.get('Discount value') + ': ' + discountValue, null, null, null, 'content_block', null);
            if (parseInt(couponsList[key]["active"]) != 1) {
    			
    			contentPanel.classList.add("dnd_content_unactive");
    			
    		}
            
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var couponId = document.createElement('input');
            couponId.classList.add('urlQuery');
            couponId.readonly = 'readonly';
            couponId.type = 'text';
            couponId.value = couponsList[key]['id'];
            couponId.onclick = function(){
                
                this.focus();
                this.select();
                
            }
            
            var optionPanel = object.create('div', null, [editLabel, deleteLabel, couponId], null, null, 'dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, discountPanel, optionPanel], null, null, 'dnd_coupon_column', {key: key} );
            columns[key] = column;
            panel.appendChild(column);
            
            editLabel.onclick = function(event){
                
                if(editBool === true){
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for(var formKey in columns){
                        
                        if(formKey != key){
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object._console.log(couponsList[key]);
                    object.editItem(columns, key, mainPanel, panel, 'updateCoupons', account, couponsList[key], object.schedule_data['couponsInputType'], function(action){
                        
                        editBool = true;
                        if (action != 'cancel') {
                            
                            couponsList = action;
                            object.createCouponsListPanel(couponsList, account, callback);
                            
                        }
                        
                        addButton.disabled = false;
                        for(var formKey in columns){
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if(editBool === true){
                    
                    editBool = false;
                    var dataKey = parseInt(this.getAttribute("data-key"));
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [couponsList[dataKey].name]));
                    if(result === true){
                        
                        object.deleteItem(couponsList[dataKey].key, "deleteCouponsItem", account, function(json){
                            
                            couponsList = json;
                            object.createCouponsListPanel(couponsList, account, callback);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        if (parseInt(account.couponsBool) === 0 && enableFunction === true) {
            
            enableFunctionPanel.classList.remove('hidden_panel');
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            object._console.log(object.schedule_data);
            if(editBool === true){
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addCoupons', account, object.schedule_data['couponsInputType'], null, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                couponsList = action;
                                object.createCouponsListPanel(couponsList, account, callback);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
        
    };
    
    SCHEDULE.prototype.createStaffPanel = function(staffList, account, callback){
        
        var editBool = true;
        var object = this;
        
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var coursePanel = document.getElementById("coursePanel");
        coursePanel.textContent = null;
        
        let enableFunction = false;
        var enableFunctionPanel = object.create('div', object._i18n.get('Despite having valid items, the functionality of "%s" is disabled.', [object._i18n.get('Staff')]), null, null, 'color: #ff3333;', 'hidden_panel', null);
        var directlySwitchPanel = object.createSwitchPanel( account, 'staffBool', 'directlySwitchForGuests', 'switch3', 'Enable the function', enableFunctionPanel, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            for (var i in json) {
                
                if (json[i].key == account.key) {
                    
                    account = json[i];
                    break;
                    
                }
                
            }
            callback(json);
            
        });
        
        var mainPanel = document.getElementById("staffPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(directlySwitchPanel);
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'guestsSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        for (var key in staffList) {
            
            if (typeof staffList[key]['name'] == "string") {
                
                staffList[key]['name'] = staffList[key]['name'].replace(/\\/g, "");
                
            }
            
            var staffObj = staffList[key];
            var extra = JSON.parse(staffObj.json);
            object._console.log(staffObj);
            object._console.log(extra);
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', staffList[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(staffList[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            if (staffObj["active"] != "true") {
    			
    			contentPanel.classList.add("dnd_content_unactive");
    			
    		}
            
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if(formKey != key){
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object._console.log(staffList[key]);
                    object.editItem(columns, key, mainPanel, panel, 'updateStaff', account, staffList[key], object.schedule_data['guestsInputType'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            staffList = action;
                            object.createStaffPanel(staffList, account, callback);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var dataKey = parseInt(this.getAttribute("data-key"));
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [staffList[dataKey].name]));
                    if (result === true) {
                        
                        object.deleteItem(staffList[dataKey].key, "deleteStaffItem", account, function(json){
                            
                            staffList = json;
                            object.createStaffPanel(staffList, account, callback);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
    	
        addButton.onclick = function(event){
            
            object._console.log(object.schedule_data);
            if(editBool === true){
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addStaff', account, object.schedule_data['guestsInputType'], null, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                staffList = action;
                                object.createStaffPanel(staffList, account, callback);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
        
        saveButton.onclick = function(event){
            
            staffList = object.changeRank('key', 'dnd_column', staffList, panel, 'changeStaffRank', account, function(json){
                
                staffList = json;
                saveButton.disabled = true;
                var panelList = panel.getElementsByClassName('dnd_column');
                object.reviewPanels(panelList);
                object.createStaffPanel(staffList, account, callback);
                
            });
            
            object._console.log(staffList);
            
        };
        
    };

    SCHEDULE.prototype.createGuestsPanel = function(guestsList, account, callback){
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var coursePanel = document.getElementById("coursePanel");
        coursePanel.textContent = null;
    	
    	let enableFunction = false;
        var enableFunctionPanel = object.create('div', object._i18n.get('Despite having valid items, the functionality of "%s" is disabled.', [object._i18n.get('Guests')]), null, null, 'color: #ff3333;', 'hidden_panel', null);
    	var directlySwitchPanel = object.createSwitchPanel( account, 'guestsBool', 'directlySwitchForGuests', 'switch1', 'Enable the function', enableFunctionPanel, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            for (var i in json) {
                
                if (json[i].key == account.key) {
                    
                    account = json[i];
                    if (parseInt(account.guestsBool) === 0 && enableFunction === true) {
                        
                        enableFunctionPanel.classList.remove('hidden_panel');
                        
                    } else {
                        
                        enableFunctionPanel.classList.add('hidden_panel');
                        
                    }
                    break;
                    
                }
                
            }
            callback(json);
            
        });
        
        if (account.type == 'hotel') {
            
            directlySwitchPanel.textContent = null;
            
        }
        
        var mainPanel = document.getElementById("guestsPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(directlySwitchPanel);
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'guestsSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {name: 'Name', email: 'Email', zip: 'Zip', D: 'D'};

        for (var key in guestsList) {
            
            if (typeof guestsList[key]['name'] == "string") {
                
                guestsList[key]['name'] = guestsList[key]['name'].replace(/\\/g, "");
                
            }
            
            if (guestsList[key].active === 'true') {
                
                enableFunction = true;
                
            }
            
            var guestsObj = guestsList[key];
            var extra = JSON.parse(guestsObj.json);
            object._console.log(guestsObj);
            object._console.log(extra);
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', guestsList[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(guestsList[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if(formKey != key){
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object._console.log(guestsList[key]);
                    
                    object.editItem(columns, key, mainPanel, panel, 'updateGuests', account, guestsList[key], object.schedule_data['guestsInputType'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            guestsList = action;
                            object.createGuestsPanel(guestsList, account, callback);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var dataKey = parseInt(this.getAttribute("data-key"));
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [guestsList[dataKey].name]));
                    if (result === true) {
                        
                        object.deleteItem(guestsList[dataKey].key, "deleteGuestsItem", account, function(json){
                            
                            guestsList = json;
                            object.createGuestsPanel(guestsList, account, callback);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        if (parseInt(account.guestsBool) === 0 && enableFunction === true) {
            
            enableFunctionPanel.classList.remove('hidden_panel');
            
        }
        
        mainPanel.appendChild(panel);
    	
        addButton.onclick = function(event){
            
            object._console.log(object.schedule_data);
            if(editBool === true){
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addGuests', account, object.schedule_data['guestsInputType'], null, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                guestsList = action;
                                object.createGuestsPanel(guestsList, account, callback);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
        
        saveButton.onclick = function(event){
            
            guestsList = object.changeRank('key', 'dnd_column', guestsList, panel, 'changeGuestsRank', account, function(json){
                
                guestsList = json;
                saveButton.disabled = true;
                var panelList = panel.getElementsByClassName('dnd_column');
                object.reviewPanels(panelList);
                object.createGuestsPanel(guestsList, account, callback);
                
            });
            
            object._console.log(guestsList);
            
        };
        
        
    };

    SCHEDULE.prototype.createFormPanel = function(formData, account){
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        
        var formDataList = formData;
        object._console.log(formDataList);
        
        var mainPanel = document.getElementById("formPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'formSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {name: 'Name', email: 'Email', zip: 'Zip', D: 'D'};
        //for(var key in formDataList){
        for (var key = 0; key < formDataList.length; key++) {
            
            object._console.log(formDataList[key]);
            if (typeof formDataList[key]['name'] == "string") {
                
                formDataList[key]['name'] = formDataList[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', formDataList[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if (formDataList[key].required == 'true' || formDataList[key].required == 'true_frontEnd') {
                
                contentPanel.classList.add("dnd_required");
                
            }
            
            if(formDataList[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object._console.log(formDataList[key]);
                    
                    object.editItem(columns, key, mainPanel, panel, 'updateForm', account, formDataList[key], object.schedule_data['formInputType'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            formData = action;
                            object.createFormPanel(formData, account);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var dataKey = parseInt(this.getAttribute("data-key"));
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [formDataList[dataKey].name]));
                    if (result === true) {
                        
                        object.deleteItem(dataKey, "deleteFormItem", account, function(json){
                            
                            formData = json;
                            object.createFormPanel(formData, account);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addForm', account, object.schedule_data['formInputType'], null, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                formData = action;
                                object.createFormPanel(formData, account);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }

        };
        
        saveButton.onclick = function(event){
            
            formDataList = object.changeRank('name', 'dnd_column', formDataList, panel, 'changeFormRank', account, function(json){
                
                formData = json;
                saveButton.disabled = true;
                var panelList = panel.getElementsByClassName('dnd_column');
                object.reviewPanels(panelList);
                object.createFormPanel(formData, account);
                
            });
            
            object._console.log(formDataList);
            
        };
        
    };
    
    SCHEDULE.prototype.createSwitchPanel = function (account, key, id, switchId, name, enableFunctionPanel, mode, callback) {
        
        var object = this;
        var switchCheck = document.createElement('input');
        switchCheck.type = 'checkbox';
        switchCheck.id = switchId;
        switchCheck.value = 1;
        if (parseInt(account[key]) == 1) {
            
            switchCheck.checked = true;
            
        }
        
        if (enableFunctionPanel === null) {
            
            enableFunctionPanel = object.create('div', '', null, null, null, 'hidden_panel', null);
            
        }
        
        var switchSpan = document.createElement('span');
        var switchLabel = object.create('label', null, [switchSpan], null, null, null, null);
        switchLabel.setAttribute('for', switchId);
        var switchImg = object.create('div', null, null, null, null, 'switchImg', null);
        var switchArea = object.create('div', null, [switchCheck, switchLabel, switchImg], null, null, 'switchArea', null);
        var namePanel = object.create('div', object._i18n.get(name), [enableFunctionPanel], null, null, null, null);
        var valuePanel = object.create('div', null, [switchArea], null, null, null, null);
        var panel = object.create('div', null, [namePanel, valuePanel], id, null, 'switchPanel', null);
        switchCheck.onclick = function() {
            
            var checkBox = this;
            object._console.log(checkBox);
            var value = 0;
            if (checkBox.checked === true) {
                
                value = 1;
                
            }
            object._console.log(value);
            
            var postData = {nonce: object.nonce, action: object.action, mode: mode, accountKey: account.key, name: key, value: value};
            object._console.log(postData);
    		object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
    		object.xmlHttp = new Booking_App_XMLHttp(object.url, postData, object._webApp, function(json){
                
                if (callback != null) {
                    
                    callback(json);
                    
                }
                object.loadingPanel.setAttribute("class", "hidden_panel");
                
			}, function(text){
                
                object.setResponseText(text);
                
            });
            
        };
        
        return panel;
        
    };

    SCHEDULE.prototype.createCoursePanel = function(courseList, account, callback){
        
        var editBool = true;
        var object = this;
        object._console.log(account);
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        
        var guestsPanel = document.getElementById("guestsPanel");
        guestsPanel.textContent = null;
        
        let enableFunction = false;
        var enableFunctionPanel = object.create('div', object._i18n.get('Despite having valid items, the functionality of "%s" is disabled.', [object._i18n.get('Services')]), null, null, 'color: #ff3333;', 'hidden_panel', null);
        var directlySwitchPanel = object.createSwitchPanel( account, 'courseBool', 'directlySwitchForServices', 'switch1', 'Enable the function', enableFunctionPanel, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            for (var i in json) {
                
                if (json[i].key == account.key) {
                    
                    account = json[i];
                    object._console.log(account);
                    if (parseInt(account.courseBool) === 0 && enableFunction === true) {
                        
                        enableFunctionPanel.classList.remove('hidden_panel');
                        
                    } else {
                        
                        enableFunctionPanel.classList.add('hidden_panel');
                        
                    }
                    break;
                    
                }
                
            }
            callback(json);
            
        });
        
        
        
        var mainPanel = document.getElementById("coursePanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(directlySwitchPanel);
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'courseSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {name: 'Name', email: 'Email', zip: 'Zip', D: 'D', Test: 'Test', planA: 'plan A', area: 'area', G: 'G'};
        list = {};
        object._console.log(courseList);
        
        for (var i = 0; i < courseList.length; i++) {
            
            object._console.log(courseList[i]);
            if (typeof courseList[i]['name'] == "string") {
                
                list[courseList[i]['key']] = courseList[i]['name'].replace(/\\/g, "");
                
            }
            
            if (courseList[i].active === 'true') {
                
                enableFunction = true;
                
            }
            
        }
        
        object._console.log(enableFunction);
        if (parseInt(account.courseBool) === 0 && enableFunction === true) {
            
            enableFunctionPanel.classList.remove('hidden_panel');
            
        }
        
        object._console.log(list);
        for(var key = 0; key < courseList.length; key++){
            
            if(typeof courseList[key]['name'] == "string"){
                
                courseList[key]['name'] = courseList[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', courseList[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(courseList[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var copyLabel = object.create('label', object._i18n.get("Copy"), null, null, null, 'dnd_copy', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var urlQuery = document.createElement('input');
            urlQuery.classList.add('urlQuery');
            urlQuery.readonly = 'readonly';
            urlQuery.type = 'text';
            urlQuery.value = '?services=' + courseList[key]['key'];
            urlQuery.onclick = function(){
                
                this.focus();
                this.select();
                
            }
            
            var shortCode = document.createElement('input');
            shortCode.classList.add('urlQuery');
            shortCode.readonly = 'readonly';
            shortCode.type = 'text';
            shortCode.value = '[booking_package id=' + account.key + ' services=' + courseList[key]['key'] + ']';
            shortCode.onclick = function(){
                
                this.focus();
                this.select();
                
            }
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, copyLabel, deleteLabel, shortCode, urlQuery], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            column.setAttribute("draggable", "true");
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            
            editLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    object._console.log(key);
                    object.editItem(columns, key, mainPanel, panel, object._prefix + 'updateCourse', account, courseList[key], object.schedule_data['courseData'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            courseList = action;
                            object.createCoursePanel(courseList, account, callback);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            copyLabel.onclick = function(event) {
                
                if (editBool === true) {
                    
                    editBool = false;
                    var courseInfo = courseList[parseInt(this.getAttribute("data-key"))];
                    var result = confirm(object._i18n.get('Do you copy the "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.copyItem(courseInfo.key, "copyCourse", account, function(json){
                            
                            courseList = json;
                            object.createCoursePanel(courseList, account, callback);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var courseInfo = courseList[parseInt(this.getAttribute("data-key"))];
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.deleteItem(courseInfo.key, "deleteCourse", account, function(json){
                            
                            courseList = json;
                            object.createCoursePanel(courseList, account, callback);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, object._prefix + 'addCourse', account, object.schedule_data['courseData'], null, function(action){
                
                editBool = true;
                if (action == "close") {
                    
                    addButton.disabled = false;
                    
                } else {
                    
                    object._console.log(typeof action);
                    if (typeof action == 'object') {
                        
                        if (action['status'] != 'error') {
                            
                            object._console.log(action);
                            courseList = action;
                            object.createCoursePanel(courseList, account, callback);
                            
                        }
                        
                    }
                    
                }
                
                panel.classList.remove("hidden_panel");
                
                });
                
            }
            
        };
        
        saveButton.onclick = function(event){
            
                courseList = object.changeRank('key', 'dnd_column', courseList, panel, 'changeCourseRank', account, function(json){
                courseList = json;
                saveButton.disabled = true;
                var panelList = panel.getElementsByClassName('dnd_column');
                object.reviewPanels(panelList);
                
            });
            
            object._console.log(courseList);
            
        };
        
    };
    
    SCHEDULE.prototype.getRankingUpAndDownKeys = function (obj, key) {
        
        let entries = Object.entries(obj);
        let index = entries.findIndex(entry => entry[0] === key);
        let upKey = index > 0 ? entries[index - 1][0] : null;
        let downKey = index < entries.length - 1 ? entries[index + 1][0] : null;
        return {up: upKey, down: downKey};
        
    }
    
    SCHEDULE.prototype.moveRankingUp = function (obj, key) {
        
        let entries = Object.entries(obj);
        let index = entries.findIndex(entry => entry[0] === key);
        if (index > 0) {
            
            [entries[index], entries[index - 1]] = [entries[index - 1], entries[index]];
            
        }
        
        return Object.fromEntries(entries);
        
    };
    
    SCHEDULE.prototype.moveRankingDown = function (obj, key) {
        
        let entries = Object.entries(obj);
        let index = entries.findIndex(entry => entry[0] === key);
        if (index !== -1 && index < entries.length - 1) {
            
            [entries[index], entries[index + 1]] = [entries[index + 1], entries[index]];
            
        }
        
        return Object.fromEntries(entries);
        
    };
    
    SCHEDULE.prototype.taxesPanel = function(taxes, account) {
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var guestsPanel = document.getElementById("guestsPanel");
        guestsPanel.textContent = null;
        var mainPanel = document.getElementById("taxesPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'taxesSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {};
        object._console.log(taxes);
        for(var i = 0; i < taxes.length; i++){
            
            if (typeof taxes[i]['name'] == "string") {
                
                list[taxes[i]['key']] = taxes[i]['name'].replace(/\\/g, "");
                
            }
            
        }
        object._console.log(list);
        
        for (var key = 0; key < taxes.length; key++) {
            
            if(typeof taxes[key]['name'] == "string"){
                
                taxes[key]['name'] = taxes[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', taxes[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(taxes[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            column.setAttribute("draggable", "true");
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
    			
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    object._console.log(taxes[key]);
                    object._console.log(key);
                    object.editItem(columns, key, mainPanel, panel, 'updateTax', account, taxes[key], object.schedule_data['taxColumns'], function(action){
                        
                        editBool = true;
                        object._console.log(action);
                        if (action !== 'cancel') {
                            
                            taxes = action;
                            object.taxesPanel(taxes, account);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var courseInfo = taxes[parseInt(this.getAttribute("data-key"))];
                    var result = confirm(object._i18n.get('Do you delete "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.deleteItem(courseInfo.key, "deleteTax", account, function(json){
                            
                            taxes = json;
                            object.taxesPanel(taxes, account);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addTax', account, object.schedule_data['taxColumns'], null, function(action) {
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                taxes = action;
                                object.taxesPanel(taxes, account);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
    	
        if (object._isExtensionsValid == 1) {
            
            saveButton.onclick = function(event){
                
                taxes = object.changeRank('key', 'dnd_column', taxes, panel, 'changeTaxesRank', account, function(json){
                    
                    taxes = json;
                    saveButton.disabled = true;
                    var panelList = panel.getElementsByClassName('dnd_column');
                    object.reviewPanels(panelList);
                    
                });
                
                object._console.log(taxes);
                
            };
            
        } else {
            
            saveButton.disable = true;
            
        }
        
    };
    
    SCHEDULE.prototype.extraChargesPanel = function(extraCharges, account) {
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var guestsPanel = document.getElementById("guestsPanel");
        guestsPanel.textContent = null;
        var mainPanel = document.getElementById("extraChargesPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'extraChargesSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {};
        object._console.log(extraCharges);
        for(var i = 0; i < extraCharges.length; i++){
            
            if (typeof extraCharges[i]['name'] == "string") {
                
                list[extraCharges[i]['key']] = extraCharges[i]['name'].replace(/\\/g, "");
                
            }
            
        }
        object._console.log(list);
        
        for (var key = 0; key < extraCharges.length; key++) {
            
            if(typeof extraCharges[key]['name'] == "string"){
                
                extraCharges[key]['name'] = extraCharges[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', extraCharges[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(extraCharges[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            column.setAttribute("draggable", "true");
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
    			
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object.editItem(columns, key, mainPanel, panel, 'updateExtraCharge', account, extraCharges[key], object.schedule_data['extraChargeColumns'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            extraCharges = action;
                            object.extraChargesPanel(extraCharges, account);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var courseInfo = extraCharges[parseInt(this.getAttribute("data-key"))];
                    //var result = confirm("Do you delete service of \"" + courseInfo.name + "\"?");
                    var result = confirm(object._i18n.get('Do you delete "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.deleteItem(courseInfo.key, "deleteExtraCharge", account, function(json){
                            
                            extraCharges = json;
                            object.extraChargesPanel(extraCharges, account);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addExtraCharge', account, object.schedule_data['extraChargeColumns'], null, function(action) {
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                extraCharges = action;
                                object.extraChargesPanel(extraCharges, account);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
    	
        if (object._isExtensionsValid == 1) {
            
            saveButton.onclick = function(event){
                
                extraCharges = object.changeRank('key', 'dnd_column', extraCharges, panel, 'changeTaxesRank', account, function(json){
                    
                    extraCharges = json;
                    saveButton.disabled = true;
                    var panelList = panel.getElementsByClassName('dnd_column');
                    object.reviewPanels(panelList);
                    
                });
                
                object._console.log(extraCharges);
                
            };
            
        } else {
            
            saveButton.disable = true;
            
        }
        
    };
    
    SCHEDULE.prototype.taxPanel = function(taxes, account) {
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var guestsPanel = document.getElementById("guestsPanel");
        guestsPanel.textContent = null;
        var mainPanel = document.getElementById("taxPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'taxSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {};
        object._console.log(taxes);
        for(var i = 0; i < taxes.length; i++){
            
            if (typeof taxes[i]['name'] == "string") {
                
                list[taxes[i]['key']] = taxes[i]['name'].replace(/\\/g, "");
                
            }
            
        }
        object._console.log(list);
        for(var key = 0; key < taxes.length; key++){
            
            if (typeof taxes[key]['name'] == "string") {
                
                taxes[key]['name'] = taxes[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', taxes[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(taxes[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            column.setAttribute("draggable", "true");
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for (var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object.editItem(columns, key, mainPanel, panel, 'updateTaxes', account, taxes[key], object.schedule_data['taxesData'], function(action){
                        
                        editBool = true;
                        
                        if (action !== 'cancel') {
                            
                            taxes = action;
                            object.taxPanel(taxes, account);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    var courseInfo = taxes[parseInt(this.getAttribute("data-key"))];
                    var result = confirm(object._i18n.get('Do you delete "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.deleteItem(courseInfo.key, "deleteTaxes", account, function(json){
                            
                            taxes = json;
                            object.taxPanel(taxes, account);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addTaxes', account, object.schedule_data['taxesData'], null, function(action){
                
                editBool = true;
                if (action == "close") {
                    
                    addButton.disabled = false;
                    
                } else {
                    
                    object._console.log(typeof action);
                    if (typeof action == 'object') {
                        
                        if (action['status'] != 'error') {
                            
                            object._console.log(action);
                            taxes = action;
                            object.taxPanel(taxes, account);
                            
                        }
                        
                    }
                    
                }
                
                panel.classList.remove("hidden_panel");
                
                });
                
            }
            
        };
        
        if (object._isExtensionsValid == 1) {
            
            saveButton.onclick = function(event) {
                
                taxes = object.changeRank('key', 'dnd_column', taxes, panel, 'changeTaxesRank', account, function(json){
                    
                    taxes = json;
                    saveButton.disabled = true;
                    var panelList = panel.getElementsByClassName('dnd_column');
                    object.reviewPanels(panelList);
                    
                });
                
                object._console.log(taxes);
                
            };
            
        } else {
            
            saveButton.disable = true;
            
        }
        
    };
    
    SCHEDULE.prototype.optionsForHotelPanel = function(options, account) {
        
        var editBool = true;
        var object = this;
        var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
        addButton.disabled = false;
        var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
        saveButton.disabled = true;
        var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
        var mainPanel = document.getElementById("optionsForHotelPanel");
        mainPanel.textContent = null;
        mainPanel.appendChild(buttonPanel);
        var panel = object.create('div', null, null, 'optionsForHotelSort', null, 'dnd', null);
        var buttons = {};
        var columns = {};
        var ranking_columns = {};
        var list = {};
        object._console.log(options);
        for (var i = 0; i < options.length; i++) {
            
            if (typeof options[i]['name'] == "string") {
                
                list[options[i]['key']] = options[i]['name'].replace(/\\/g, "");
                
            }
            
        }
        object._console.log(list);
        for (var key = 0; key < options.length; key++) {
            
            if (typeof options[key]['name'] == "string") {
                
                options[key]['name'] = options[key]['name'].replace(/\\/g, "");
                
            }
            
            var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var content_name = object.create('div', options[key]['name'], null, null, null, 'content_name', null);
            var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
            if(options[key]["active"] != "true"){
                
                content_name.classList.add("dnd_content_unactive");
                
            }
            
            var rank_down_button = object.create('div', 'expand_more', null, null, null, 'material-icons rank_up_down_button', {key: key});
            var editLabel = object.create('label', object._i18n.get("Edit"), null, null, null, 'dnd_edit', {key: key} );
            var deleteLabel = object.create('label', object._i18n.get("Delete"), null, null, null, 'dnd_delete', {key: key} );
            var optionPanel = object.create('div', null, [rank_down_button, editLabel, deleteLabel], null, null, 'content_block dnd_optionBox', null);
            var column = object.create('div', null, [contentPanel, optionPanel], null, null, 'dnd_column', {key: key} );
            column.setAttribute("draggable", "true");
            columns[key] = column;
            ranking_columns['key_' + key] = column;
            panel.appendChild(column);
            rank_up_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.up];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingUp(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(ranking_columns[key], target_column);
                //panel.insertBefore(target_column, child3.ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            rank_down_button.onclick = function() {
                
                const key = 'key_' + this.getAttribute("data-key");
                const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
                const target_column = ranking_columns[keys.down];
                console.log(target_column);
                if (target_column == null) {
                    
                    return null;
                    
                }
                ranking_columns = object.moveRankingDown(ranking_columns, key);
                console.log(ranking_columns);
                panel.insertBefore(target_column, ranking_columns[key]);
                saveButton.disabled = false;
                
            };
            
            editLabel.onclick = function(event) {
                
                if (editBool === true) {
                    
                    addButton.disabled = true;
                    editBool = false;
                    var key = this.getAttribute("data-key");
                    for( var formKey in columns) {
                        
                        if (formKey != key) {
                            
                            columns[formKey].classList.add("hidden_panel");
                            
                        }
                        
                    }
                    
                    object.editItem(columns, key, mainPanel, panel, 'updateOptionsForHotel', account, options[key], object.schedule_data['optionsForHotelData'], function(action){
                        
                        editBool = true;
                        if (action !== 'cancel') {
                            
                            options = action;
                            object.optionsForHotelPanel(options, account);
                            
                        }
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    });
                    
                }
                
            };
            
            deleteLabel.onclick = function(event){
                
                if(editBool === true){
                    
                    editBool = false;
                    var courseInfo = options[parseInt(this.getAttribute("data-key"))];
                    var result = confirm(object._i18n.get('Do you delete "%s"?', [courseInfo.name]));
                    if (result === true) {
                        
                        object._console.log(courseInfo);
                        object.deleteItem(courseInfo.key, "deleteOptionsForHotel", account, function(json){
                            
                            options = json;
                            object.optionsForHotelPanel(options, account);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        }
        
        mainPanel.appendChild(panel);
        
        addButton.onclick = function(event){
            
            if (editBool === true) {
                
                panel.classList.add("hidden_panel");
                editBool = false;
                addButton.disabled = true;
                object.addItem(mainPanel, 'addOptionsForHotel', account, object.schedule_data['optionsForHotelData'], null, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        
                    } else {
                        
                        object._console.log(typeof action);
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                options = action;
                                object.optionsForHotelPanel(options, account);
                                
                            }
                            
                        }
                    
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
        
        if (object._isExtensionsValid == 1) {
            
            saveButton.onclick = function(event){
                
                options = object.changeRank('key', 'dnd_column', options, panel, 'changeOptionsForHotelRank', account, function(json){
                    
                    options = json;
                    saveButton.disabled = true;
                    var panelList = panel.getElementsByClassName('dnd_column');
                    object.reviewPanels(panelList);
                    
                });
                object._console.log(options);
                
            };
            
        } else {
            
            saveButton.disable = true;
            
        }
        
    };
    
    SCHEDULE.prototype.syncPanel = function(ical, syncPastCustomersForIcal, icalToken, home, account, callback) {
        
        var object = this;
        object._console.log(ical);
        object._console.log(account);
        var title = object.create('div', "iCalendar", null, null, null, 'title', null);
        var table = object.create('table', null, null, null, null, 'form-table', null);
        var syncPanel = document.getElementById("syncPanel");
        syncPanel.textContent = null;
        syncPanel.appendChild(title);
    	syncPanel.appendChild(table);
        
        var inputData = {};
        var active = {name: 'Active', value: parseInt(ical), inputLimit: 1, inputType: 'RADIO', valueList: {1: object._i18n.get("Enabled"), 0: object._i18n.get("Disabled")}};
        var activePanel = object.createInput('ical', active, inputData, account, false, null, 0);
        var th = object.create('th', object._i18n.get("Status"), null, null, null, null, null);
        th.setAttribute("scope", "row");
        var td = object.create('td', null, [activePanel], null, null, null, null);
        var tr = object.create('tr', null, [th, td], null, null, null, null);
        tr.setAttribute("valign", "top");
        table.appendChild(tr);
        var syncPastCustomersForIcalValue = {name: 'Active', value: parseInt(syncPastCustomersForIcal), inputLimit: 1, inputType: 'SELECT', valueList: 
            {
                7: object._i18n.get("Last %s days", [7]), 
                14: object._i18n.get("Last %s days", [14]), 
                30: object._i18n.get("Last %s days", [30]), 
                60: object._i18n.get("Last %s days", [60]), 
                90: object._i18n.get("Last %s days", [90]), 
                180: object._i18n.get("Last %s days", [180]), 
                365: object._i18n.get("Last %s days", [365]), 
            }
        };
        var syncPastCustomersForIcalPanel = object.createInput('syncPastCustomersForIcal', syncPastCustomersForIcalValue, inputData, account, false, null, 0);
        
        var th = object.create('th', object._i18n.get("Period"), null, null, null, null, null);
        th.setAttribute("scope", "row");
        var td = object.create('td', null, [syncPastCustomersForIcalPanel], null, null, null, null);
        var tr = object.create('tr', null, [th, td], null, null, null, null);
        tr.setAttribute("valign", "top");
        table.appendChild(tr);
        
        th = object.create('th', object._i18n.get("URL"), null, null, null, null, null);
        th.setAttribute("scope", "row");
        var tokenButton = object.createButton(null, null, 'w3tc-button-save button-primary tokenButton', null, object._i18n.get("Refresh token"));
		var tokenValue = document.createElement("input");
		tokenValue.type = "text";
		tokenValue.value = home + "?id=" + account.key + "&ical=" + icalToken + '&site=' + object._siteToken;
		tokenValue.setAttribute("class", "tokenValue");
		tokenValue.setAttribute("readonly", "readonly");
		tokenValue.style.width = "100%";
		var inputPanel = object.create('div', null, [tokenValue, tokenButton], null, null, null, null);
        td = object.create('td', null, [inputPanel], null, null, null, null);
        tr = object.create('tr', null, [th, td], null, null, null, null);
        tr.setAttribute("valign", "top");
        table.appendChild(tr);
        
        tokenValue.onclick = function(){
            
            this.focus();
            this.select();
            
        }
		
		tokenButton.onclick = function(){
			
			object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            var post = {nonce: object.nonce, action: object.action, mode: 'refreshToken', accountKey: account.key};
            object.setFunction("loadTabFrame", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                object.loadingPanel.setAttribute("class", "hidden_panel");
                tokenValue.value = home + "?id=" + account.key + "&ical=" + json.token + '&site=' + object._siteToken;
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
			
		}
		
		var saveButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
        var buttonPanel = object.create('div', null, [saveButton], null, 'margin: 10px 10px 10px 0px;', null, null);
        syncPanel.appendChild(buttonPanel);
        saveButton.onclick = function() {
            
            var post = {nonce: object.nonce, action: object.action, mode: 'updateIcalToken', accountKey: account.key, ical: 0};
            var ical = inputData.ical;
            
            for (var i in ical) {
                
                if (ical[i].checked == true) {
                    
                    post.ical = i;
                    break;
                    
                }
                
            }
            
            var syncPastCustomersForIcal = inputData.syncPastCustomersForIcal.selectBox;
            
            object._console.log(syncPastCustomersForIcal);
            object._console.log(syncPastCustomersForIcal.selectedIndex);
            object._console.log(syncPastCustomersForIcal.options[syncPastCustomersForIcal.selectedIndex].value);
            post.syncPastCustomersForIcal = parseInt(syncPastCustomersForIcal.options[syncPastCustomersForIcal.selectedIndex].value);
            
            object._console.log(post);
            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
            object.setFunction("loadTabFrame", post);
            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                
                object.loadingPanel.setAttribute("class", "hidden_panel");
                
            }, function(text){
                
                object.setResponseText(text);
                
            });
            
        };
        
    };
    
    SCHEDULE.prototype.holidayPanel = function(accountKey, regularHolidays){
		
		var object = this;
		object._console.log("holidayPanel");
		object._console.log(regularHolidays);
		var holidayPanel = document.getElementById("closedDaysPanel");
		var month = parseInt(regularHolidays.date.month);
		var year = parseInt(regularHolidays.date.year);
		object._console.log(regularHolidays);
		var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object.dateFormat, object.positionOfWeek, object.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
		
		var calendarPanel = document.getElementById("holidaysCalendarPanel");
		calendarPanel.classList.add("hidden_panel");
		calendarPanel.textContent = null;
		calendar.holidayPanel(accountKey, holidayPanel, calendarPanel, month, year, regularHolidays, function(postData, callback) {
			
			postData.nonce = object.nonce;
			postData.action = object.action;
			object._console.log(postData);
			var xmlHttp = new Booking_App_XMLHttp(object.url, postData, object._webApp, function(json){
				
				object._console.log(json);
				callback(true, json);
				
			});
			
		});
		
	};
    
    SCHEDULE.prototype.customizePanel = function(accountList, month, day, year, account, callback) {
        
        var object = this;
        var customizeLabels = account.customizeLabels;
        var inputData = {};
        var customizePanel = document.getElementById("customizePanel");
        customizePanel.textContent = null;
        object._console.log('customizePanel');
        object._console.log(account);
        object._console.log(customizeLabels);
        
        var update = function(updateAccounts, account, callback) {
            
            callback(updateAccounts);
            for (var i = 0; i < updateAccounts.length; i++) {
                
                if (parseInt(updateAccounts[i].key) === parseInt(account.key)) {
                    
                    return updateAccounts[i];
                    
                }
                
            }
            
        };
        
        var label = object.create('button', object._i18n.get('Labels'), null, null, null, 'selectedButton', {key: 'label'} );
        var button = object.create('button', object._i18n.get('Buttons'), null, null, null, null, {key: 'button'} );
        var layout = object.create('button', object._i18n.get('Layouts'), null, null, null, null, {key: 'layout'} );
        var toggleButtonPanel = document.createElement('div');
        var buttons = [label, button, layout];
        toggleButtonPanel.id = 'toggleButtonPanelOnCustomize1';
        toggleButtonPanel.appendChild(label);
        toggleButtonPanel.appendChild(button);
        if (object._customizeLayouts) {
            
            toggleButtonPanel.appendChild(layout);
            
        }
        
        customizePanel.appendChild(toggleButtonPanel);
        for (var i = 0; i < buttons.length; i++) {
            
            buttons[i].onclick = function(event) {
                
                for (var i = 0; i < buttons.length; i++) {
                    
                    buttons[i].classList.remove('selectedButton');
                    
                }
                this.classList.add('selectedButton');
                var key = this.getAttribute('data-key');
                object._console.log(key);
                if (key === 'label') {
                    
                    object.customizeLabelsPanel(accountList, month, day, year, account, customizeContentPanel, function(updateAccounts) {
                        
                        account = update(updateAccounts, account, callback);
                        
                    });
                    
                } else if (key === 'button') {
                    
                    object.customizeButtonsPanel(accountList, month, day, year, account, customizeContentPanel, function(updateAccounts) {
                        
                        account = update(updateAccounts, account, callback);
                        
                    });
                    
                } else if (key === 'layout') {
                    
                    object.customizeLayoutPanel(accountList, month, day, year, account, customizeContentPanel, function(updateAccounts) {
                        
                        account = update(updateAccounts, account, callback);
                        
                    });
                    
                }
                
            };
            
        }
        
        var customizeContentPanel = document.createElement('div');
        customizePanel.appendChild(customizeContentPanel);
        object.customizeLabelsPanel(accountList, month, day, year, account, customizeContentPanel, function(updateAccounts) {
            
            account = update(updateAccounts, account, callback);
            
        });
        
        
    }
    
    SCHEDULE.prototype.customizeLabelsPanel = function(accountList, month, day, year, account, customizePanel, callback) {
        
        var object = this;
        object._console.log(customizePanel)
        customizePanel.textContent = null;
        var directlySwitchPanel = object.createSwitchPanel( account, 'customizeLabelsBool', 'directlySwitchForGuests', 'switch4', 'Enable the function', null, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            callback(json);
            
        });
        customizePanel.appendChild(directlySwitchPanel);
        
        object._customize.customizeLabels(account, customizePanel, 
            function(json) {
                
                /** Save action **/
                object.setAccountList(json);
                callback(json);
                
            }, 
            function(json) {
                
                /** Reset action **/
                object.setAccountList(json);
                for (var i = 0; i < json.length; i++) {
                    
                    if (parseInt(json[i].key) === parseInt(account.key)) {
                        
                        object._console.log(json[i]);
                        object.customizePanel(json, month, day, year, json[i], callback);
                        
                    }
                    
                }
                callback(json);
                
            }
        );
        
    };
    
    SCHEDULE.prototype.customizeButtonsPanel = function(accountList, month, day, year, account, customizePanel, callback) {
        
        var object = this;
        var customizeButtons = account.customizeButtons;
        var inputData = {};
        customizePanel.textContent = null;
        var disabled = false;
        var directlySwitchPanel = object.createSwitchPanel( account, 'customizeButtonsBool', 'directlySwitchForGuests', 'switch5', 'Enable the function', null, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            callback(json);
            
        });
        customizePanel.appendChild(directlySwitchPanel);
        
        object._customize.customizeButtons(account, customizePanel, 
            function(json) {
                
                /** Save action **/
                object.setAccountList(json);
                callback(json);
                
            }, 
            function(json) {
                
                /** Reset action **/
                object.setAccountList(json);
                for (var i = 0; i < json.length; i++) {
                    
                    if (parseInt(json[i].key) === parseInt(account.key)) {
                        
                        object._console.log(json[i]);
                        //object.customizePanel(json, month, day, year, json[i], callback);
                        object.customizeButtonsPanel(json, month, day, year, json[i], customizePanel, callback);
                        
                    }
                    
                }
                callback(json);
                
            }
        );
        
    };
    
    SCHEDULE.prototype.customizeLayoutPanel = function(accountList, month, day, year, account, customizePanel, callback) {
        
        var object = this;
        var customizeLayouts = account.customizeLayouts;
        var inputData = {};
        customizePanel.textContent = null;
        object._console.log('customizeLayoutPanel');
        object._console.log(customizeLayouts);
        
        var disabled = false;
        if (parseInt(account.customizeLabelsBool) === 0) {
            
            disabled = true;
            
        }
        
        var directlySwitchPanel = object.createSwitchPanel( account, 'customizeLayoutsBool', 'directlySwitchForGuests', 'switch5', 'Enable the function', null, 'updateAccountFunction', function(json) {
            
            object.setAccountList(json);
            callback(json);
            
        });
        customizePanel.appendChild(directlySwitchPanel);
        
        object._customize.customizeLayouts(account, customizePanel, 
            function(json) {
                
                /** Save action **/
                object.setAccountList(json);
                callback(json);
                
            }, 
            function(json) {
                
                /** Reset action **/
                object.setAccountList(json);
                for (var i = 0; i < json.length; i++) {
                    
                    if (parseInt(json[i].key) === parseInt(account.key)) {
                        
                        object._console.log(json[i]);
                        object.customizeLayoutPanel(json, month, day, year, json[i], customizePanel, callback);
                        
                    }
                    
                }
                callback(json);
                
            }
        );
        
    };

    SCHEDULE.prototype.settingPanel = function(accountList, month, day, year, account, callback){
        
        var object = this;
        var settingPanel = document.getElementById("settingPanel");
        settingPanel.textContent = null;
        const oldMaxAccountScheduleDay = parseInt(account.maxAccountScheduleDay);
        object._console.log('settingPanel');
        object._console.log('oldMaxAccountScheduleDay = ' + oldMaxAccountScheduleDay);
        
        var addPanel = document.createElement("div");
        
        settingPanel.appendChild(addPanel);
    	addPanel.id = "addCoursePanel";
    	
    	var calendarNamePanel = document.getElementById("calendarName");
        var inputData = {};
        var inputTypeList = object.schedule_data['elementForCalendarAccount'];
        object._console.log(inputTypeList);
        
        var table = document.createElement("table");
    	table.setAttribute("class", "form-table");
    	addPanel.appendChild(table);
    	
    	if (account.enableFixCalendar != null && account.monthForFixCalendar != null && account.yearForFixCalendar) {
    	    
    	    account.fixCalendar = {enableFixCalendar: account.enableFixCalendar, monthForFixCalendar: account.monthForFixCalendar, yearForFixCalendar: account.yearForFixCalendar};
    	    
    	}
    	
    	if (inputTypeList.hotelCharges != null) {
    	    
    	    var valueList = inputTypeList.hotelCharges.valueList;
    	    for (var i = 0; i < valueList.length; i++) {
    	        
    	        object._console.log(valueList[i]);
    	        var key = valueList[i].key;
    	        if (account[key] != null) {
    	            
    	            valueList[i].value = parseInt(account[key]);
    	            
    	        }
    	        
    	    }
    	    
    	}
    	
    	if (inputTypeList.messagingService != null) {
            
            inputTypeList.messagingService.value = account.messagingService;
            
        }
        
        inputTypeList.sendBookingVerificationCode.valueList[0].value = account.bookingVerificationCode;
	    inputTypeList.sendBookingVerificationCode.valueList[1].value = account.bookingVerificationCodeToUser;
	    inputTypeList.sendBookingVerificationCode.valueList[0].actions = null;
	    inputTypeList.sendBookingVerificationCode.valueList[1].actions = null;
	    
	    inputTypeList.cancellation_of_booking.valueList[0].value = account.cancellationOfBooking;
	    inputTypeList.cancellation_of_booking.valueList[1].value = account.allowCancellationVisitor;
	    inputTypeList.cancellation_of_booking.valueList[2].value = account.refuseCancellationOfBooking;
	    inputTypeList.cancellation_of_booking.valueList[0].actions = null;
	    inputTypeList.cancellation_of_booking.valueList[1].actions = null;
	    inputTypeList.cancellation_of_booking.valueList[2].actions = null;
	    
	    inputTypeList.insertCustomPage.valueList[0].value = account.calenarPage;
        inputTypeList.insertCustomPage.valueList[1].value = account.schedulesPage;
        inputTypeList.insertCustomPage.valueList[2].value = account.servicesPage;
        inputTypeList.insertCustomPage.valueList[3].value = account.visitorDetailsPage;
        inputTypeList.insertCustomPage.valueList[4].value = account.confirmDetailsPage;
        inputTypeList.insertCustomPage.valueList[5].value = account.thanksPage;
        
        if (inputTypeList.account_email_address != null) {
            
            inputTypeList.account_email_address.valueList[0].value = account.email_to;
            inputTypeList.account_email_address.valueList[1].value = account.email_from_title;
            inputTypeList.account_email_address.valueList[2].value = account.email_from;
            
        }
    	
    	if (inputTypeList.calendar_sharing != null) {
    	    
    	    object.setTargetCalendar(account.type, parseInt(account.key));
    	    inputTypeList.calendar_sharing.valueList[0].value = account.schedulesSharing;
    	    inputTypeList.calendar_sharing.valueList[1].value = account.targetSchedules;
    	    inputTypeList.calendar_sharing.valueList[0].actions = null;
    	    inputTypeList.calendar_sharing.valueList[1].actions = null;
    	    
    	}
    	
    	if (inputTypeList.minimum_guests != null) {
            
            inputTypeList.minimum_guests.valueList[0].value = parseInt(account.limitNumberOfGuests.minimumGuests.enabled);
            inputTypeList.minimum_guests.valueList[1].value = parseInt(account.limitNumberOfGuests.minimumGuests.included);
            inputTypeList.minimum_guests.valueList[2].value = parseInt(account.limitNumberOfGuests.minimumGuests.number);
            
        }
        
        if (inputTypeList.maximum_guests != null) {
            
            inputTypeList.maximum_guests.valueList[0].value = parseInt(account.limitNumberOfGuests.maximumGuests.enabled);
            inputTypeList.maximum_guests.valueList[1].value = parseInt(account.limitNumberOfGuests.maximumGuests.included);
            inputTypeList.maximum_guests.valueList[2].value = parseInt(account.limitNumberOfGuests.maximumGuests.number);
            
        }
        
        if (inputTypeList.preparationTimeSetting != null) {
            
            inputTypeList.preparationTimeSetting.valueList[0].value = parseInt(account.preparationTime) / 5;
            inputTypeList.preparationTimeSetting.valueList[1].value = account.positionPreparationTime;
            
        }
        
        if (inputTypeList.servicesFunction != null) {
            
            inputTypeList.servicesFunction.valueList[0].value = parseInt(account.courseBool);
            inputTypeList.servicesFunction.valueList[1].value = account.flowOfBooking;
            inputTypeList.servicesFunction.valueList[2].value = parseInt(account.hasMultipleServices);
            
        }
        
        if (inputTypeList.maxBookingSlotsPerDay != null) {
            
            console.error(inputTypeList.maxBookingSlotsPerDay);
            console.error(account.maxBookingSlotsPerDay);
            inputTypeList.maxBookingSlotsPerDay.valueList[0].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsPerDayStatus);
            inputTypeList.maxBookingSlotsPerDay.valueList[1].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnMonday);
            inputTypeList.maxBookingSlotsPerDay.valueList[2].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnTuesday);
            inputTypeList.maxBookingSlotsPerDay.valueList[3].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnWednesday);
            inputTypeList.maxBookingSlotsPerDay.valueList[4].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnThursday);
            inputTypeList.maxBookingSlotsPerDay.valueList[5].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnFriday);
            inputTypeList.maxBookingSlotsPerDay.valueList[6].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnSaturday);
            inputTypeList.maxBookingSlotsPerDay.valueList[7].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnSunday);
            inputTypeList.maxBookingSlotsPerDay.valueList[8].value = parseInt(account.maxBookingSlotsPerDay.maxBookingSlotsOnNationalHoliday);
            
        }
        
        if (inputTypeList.displayRemainingSlots != null) {
            
            //displayRemainingCapacityInCalendar
            
            inputTypeList.displayRemainingSlots.valueList[0].value = 0;
            if (parseInt(account.displayRemainingCapacityInCalendar) === 1) {
                
                inputTypeList.displayRemainingSlots.valueList[0].value = 'text';
                
            }
            
            if (parseInt(account.displayRemainingCapacityInCalendarAsNumber) === 1) {
                
                inputTypeList.displayRemainingSlots.valueList[0].value = 'int';
                
            }
            
            inputTypeList.displayRemainingSlots.valueList[1].value = account.displayThresholdOfRemainingCapacity;
            inputTypeList.displayRemainingSlots.valueList[2].value = account.displayRemainingCapacityHasMoreThenThreshold;
            inputTypeList.displayRemainingSlots.valueList[3].value = account.displayRemainingCapacityHasLessThenThreshold;
            inputTypeList.displayRemainingSlots.valueList[4].value = account.displayRemainingCapacityHas0;
            
        }
    	
    	if (inputTypeList.preparationTime != null) {
            
            var preparationTimeList = {};
        	for (var i = 0; i <= 180; i += 5) {
        		
        		preparationTimeList[i] = object._i18n.get("%s min", [i]);
        		
        	}
        	inputTypeList.preparationTime.valueList = preparationTimeList;
            
        }
        
        inputTypeList.redirect_Page.valueList[0].value = account.redirectMode;
        inputTypeList.redirect_Page.valueList[1].value = account.redirectPage;
        inputTypeList.redirect_Page.valueList[2].value = account.redirectURL;
    	
    	object._console.log(account);
    	object._console.log(inputTypeList);
    	var trList = {}
    	for(var key in inputTypeList){
    		
    		object._console.log(key);
            var data = inputTypeList[key];
            data.value = account[key];
            if(data.optionValues != null){
                
                for(var optionKey in data.optionValues){
                    
                    if(account[optionKey] != null){
                        
                        object._console.log(optionKey + " = " + account[optionKey]);
                        data.optionValues[optionKey] = account[optionKey];
                        
                    }
                    
                }
                
            }
            
            var th = object.create('th', object._i18n.get(inputTypeList[key].name), null, null, null, null, null);
            th.setAttribute("scope", "row");
            var inputPanel = object.createInput(key, data, inputData, account, false, null, object._isExtensionsValid);
            var td = object.create('td', null, [inputPanel], null, null, null, null);
            var tr = object.create('tr', null, [th, td], null, null, null, null);
            tr.setAttribute("valign", "top");
            trList[key] = tr;
            table.appendChild(tr);
            
            if (key == 'type' || key == 'timezone') {
                
                tr.classList.add("hidden_panel");
                table.removeChild(tr);
                
            }
            
            if (key == 'calendar_sharing') {
                
                tr.classList.add("hidden_panel");
                
            }
            
            if (account.type == 'day' && data.target != 'both' && data.target != 'day') {
                
                tr.classList.add('hidden_panel');
                
            } else if (account.type == 'hotel' && data.target != 'both' && data.target != 'hotel') {
                
                tr.classList.add('hidden_panel');
                
            }
            
            if (parseInt(account.schedulesSharing) == 1 && key == 'maxAccountScheduleDay') {
                
                tr.classList.add("hidden_panel");
                
            }
            
            if (parseInt(account.schedulesSharing) == 1 && key == 'hotelCharges') {
                
                tr.classList.add("hidden_panel");
                
            }
            
    	}
        
        if (inputData.id && inputData.id.textBox) {
            
            object._console.log(inputData.id);
            inputData.id.textBox.disabled = true;
            
        }
        
        
        
        var saveButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
        var deleteButton = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, object._i18n.get("Delete") );
        var buttonPanel = object.create('div', null, [saveButton, deleteButton], null, null, 'bottomButtonPanel', null);
        addPanel.appendChild(buttonPanel);
        var confirm = new Confirm(object._debug);
        saveButton.onclick = function(){
            
            var post = {nonce: object.nonce, action: object.action, mode: 'updateCalendarAccount', accountKey: account.key};
            var response = object.getInputData(inputTypeList, inputData);
            object._console.log(account);
            object._console.log(response);
            for (var key in response) {
                
                if (typeof response[key] == 'boolean') {
        			
        			object._console.log("error key = " + key + " bool = " + response[key]);
        			trList[key].classList.add("errorPanel");
        			post = false;
        			break;
        			
        		} else {
        			
        			post[key] = response[key];
        			if (trList[key] != null) {
        			    
        			    trList[key].classList.remove("errorPanel");
        			    
        			}
        			
        		}
                
            }
            
            const verifyItemsAffectingSchedules = (function(account, chagendValues) {
                
                var response = {status: false, items: []};
                if (account.type === 'hotel') {
                    
                    const verifyItems = {
                        maxAccountScheduleDay: object._i18n.get('Public days from today'), 
                        hotelChargeOnDayBeforeNationalHoliday: object._i18n.get('Charges'), 
                        hotelChargeOnFriday: object._i18n.get('Charges'), 
                        hotelChargeOnMonday: object._i18n.get('Charges'), 
                        hotelChargeOnNationalHoliday: object._i18n.get('Charges'), 
                        hotelChargeOnSaturday: object._i18n.get('Charges'), 
                        hotelChargeOnSunday: object._i18n.get('Charges'), 
                        hotelChargeOnThursday: object._i18n.get('Charges'), 
                        hotelChargeOnTuesday: object._i18n.get('Charges'), 
                        hotelChargeOnWednesday: object._i18n.get('Charges'), 
                        numberOfRoomsAvailable: object._i18n.get('Available room slots'), 
                    }
                    
                    if (object._isExtensionsValid === 0) {
                        
                        delete(verifyItems.hotelChargeOnDayBeforeNationalHoliday);
                        delete(verifyItems.hotelChargeOnNationalHoliday);
                        
                    }
                    
                    for (var key in verifyItems) {
                        
                        if (key === 'maxAccountScheduleDay') {
                            
                            if (parseInt(account[key]) > parseInt(chagendValues[key])) {
                                
                                response.status = true;
                                response.items.push(verifyItems[key]);
                                
                            }
                            
                        } else {
                            
                            if (parseInt(account[key]) !== parseInt(chagendValues[key])) {
                                
                                response.status = true;
                                response.items.push(verifyItems[key]);
                                
                            }
                            
                        }
                        
                        
                    }
                    
                } else {
                    
                    if (parseInt(account['maxAccountScheduleDay']) > parseInt(chagendValues['maxAccountScheduleDay'])) {
                        
                        response.status = true;
                        response.items.push(object._i18n.get('Public days from today'));
                        
                    }
                    
                }
                
                response.items = Array.from(new Set(response.items));
                return response;
                
            })(account, response);
            object._console.log(verifyItemsAffectingSchedules);
            
            const saveSettings = function(post) {
                
                object._console.log(post);
                if (post !== false) {
                    
                    object._console.log(post);
                    object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    object.setFunction("settingPanel", post);
                    object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                        
                        object._console.log(json);
                        object.loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
                        if (json.status != null && parseInt(json.status) == 0) {
                            
                            confirm.alertPanelShow(object._i18n.get("Error"), object._i18n.get("An unknown cause of error occurred"), false, function(callback){
                                
                            });
                            
                        } else {
                            
                            calendarNamePanel.textContent = post.name;
                            if (object._htmlTitle != null) {
                                
                                object._htmlTitle.textContent = post.name;
                                
                            }
                            //object.setTargetCalendar(json);
                            for (var i = 0; i < json.length; i++) {
                                
                                if (parseInt(account.key) === parseInt(json[i].key)) {
                                    
                                    object._console.log(json[i]);
                                    account = json[i];
                                    break;
                                    
                                }
                                
                            }
                            
                            object.setAccountList(json);
                            callback(json);
                            
                            
                        }
                        
                    }, function(text){
                        
                        object.setResponseText(text);
                        
                    });
                    
                }
                
            };
            
            if (verifyItemsAffectingSchedules.status === false) {
                
                saveSettings(post);
                
            } else {
                
                var message = object._i18n.get('Changing the values of the following items will not be reflected in already published schedules.') + " ";
                message += object._i18n.get('The changed values will be reflected in new schedules published in the future.') + "\n";
                //message += object._i18n.get('Items to be changed: %s', [verifyItemsAffectingSchedules.items.join(', ')]) + "\n\n";
                message += object._i18n.get('Items to be changed: %s', ['']) + "\n";
                for (var i = 0; i < verifyItemsAffectingSchedules.items.length; i++) {
                    
                    message += (i + 1) + '. ' + verifyItemsAffectingSchedules.items[i] + "\n";
                    
                }
                message += object._i18n.get('To modify already published schedules, adjustments to the values saved for the respective day need to be made in the "%s" tab.', [object._i18n.get('Schedules')]);
                confirm.alertPanelShow(object._i18n.get("Attention"), message, false, function(bool){
                    
                    object._console.log(bool);
                    saveSettings(post);
                    
                });
                
            }
            
        };
        
        deleteButton.onclick = function(){
            
            confirm.dialogPanelShow(object._i18n.get("Warning"), object._i18n.get('Do you delete the "%s"?', [account.name]), false, 1, function(bool){
                
                object._console.log(bool);
                if (bool === true) {
                    
                    object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                    var post = {nonce: object.nonce, action: object.action, mode: 'deleteCalendarAccount', accountKey: account.key};
                    object.setFunction("settingPanel", post);
                    object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
                        
                        object._console.log(json);
                        if (json.error == null) {
                            
                            calendarNamePanel.classList.add("hidden_panel");
                            calendarNamePanel.textContent = null;
                            if (object._htmlTitle != null) {
                                
                                object._htmlTitle.textContent = object._htmlOriginTitle;
                                
                            }
                            
                            document.getElementById("tabFrame").classList.add("hidden_panel");
                            object.createCalendarAccountList(json, month, day, year);
                            object.setCloneCalendarList(json);
                            //object.setTargetCalendar(json);
                            object.setAccountList(json);
                            
                        } else {
                            
                            window.alert(json.message);
                            
                        }
                        
                        object.loadingPanel.setAttribute("class", "hidden_panel", 'getTemplateSchedule');
                        
                    }, function(text){
                        
                        object.setResponseText(text);
                        
                    });
                    
                }
                
            });
            
        }
        
    };

    SCHEDULE.prototype.addItem = function(mainPanel, mode, account, inputTypeList, calendarType, callback){
    	
    	var object = this;
    	object._console.log(mainPanel);
    	object._console.log("mode = " + mode);
    	object._console.log(inputTypeList);
    	object._console.log("calendarType = " + calendarType);
    	object._console.log(account)
    	var closeHeghit = mainPanel.clientHeight;
    	var addPanel = object.create('div', null, null, 'addCoursePanel', null, null, null);
    	mainPanel.appendChild(addPanel);
    	
        for (var key in inputTypeList) {
        	
        	inputTypeList[key]['value'] = "";
        	
        }
        
        object._console.log(inputTypeList);
        
        if (mode == object._prefix + 'addCourse') {
            
            var courseTimeList = {};
            for (var i = 5; i < 1440; i += 5) {
                
                courseTimeList[i] = object._i18n.get("%s min", [i]);
                
            }
            inputTypeList['time']['valueList'] = courseTimeList;
            
            if (inputTypeList.options != null) {
                
                var json = [{name: "", /**cost: 0,**/ cost_1: 0, cost_2: 0, cost_3: 0, cost_4: 0, cost_5: 0, cost_6: 0, time: "0"}];
                inputTypeList.options.value = JSON.stringify(json);
                
            }
            
            if (inputTypeList.target != null) {
                
                inputTypeList.target.value = "visitors_users";
                
            }
            
        	if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = 0;
                
            }
            
            if (inputTypeList.cost != null) {
                
                inputTypeList.cost.value = 0;
                
            }
            
            if (inputTypeList.stopService != null) {
                
                inputTypeList.stopService.valueList[0].value = 'doNotStop';
                inputTypeList.stopService.valueList[1].value = 'hasNotException';
                inputTypeList.stopService.valueList[2].value = 'timeSlot';
                inputTypeList.stopService.valueList[3].value = '0';
                
            }
            
            var timeToProvide = {};
            for (var day = 0; day < 8; day++) {
                
                var hours = {};
                for (var i = 0; i < 24; i++) {
                    
                    var time = i * 60;
                    hours[time] = 1;
                    
                }
                timeToProvide[day] = hours;
                
            }
            inputTypeList.timeToProvide.value = timeToProvide;
            
        } else if (mode == 'addForm') {
            
            for (var key in inputTypeList) {
                
                if (key == "required" || key == "isEmail" || key == "isName" || key == "isAddress" || key == "isTerms" || key == "isSMS") {
                    
                    inputTypeList[key].value = "false";
                    
                } else if (key == "type") {
                    
                    inputTypeList[key].value = "TEXT";
                    
                } else if (key == "targetCustomers") {
                    
                    inputTypeList[key].value = "customersAndUsers";
                    
                } else if (key == 'isAutocomplete') {
                    
                    inputTypeList[key].value = "true";
                    
                }
                
            }
            
            inputTypeList.placeholder.value = '';
            
        } else if (mode == 'addCalendarAccount') {
            
            inputTypeList.name.value = "New Calendar";
            if (inputTypeList.timezone != null) {
                
                inputTypeList.timezone.value = object._timezone;
                
            }
            
            inputTypeList.type.value = calendarType;
            if (inputTypeList.cost != null) {
                inputTypeList.cost.value = 0;
            }
            
            if (inputTypeList.messagingService != null) {
                
                inputTypeList.messagingService.value = 0;
                
            }
            
            if (inputTypeList.hasMultipleServices != null) {
                
                inputTypeList.hasMultipleServices.value = 0;
                
            }
            
            if (inputTypeList.numberOfRoomsAvailable != null) {
                inputTypeList.numberOfRoomsAvailable.value = 1;
            }
            
            if (inputTypeList.numberOfPeopleInRoom != null) {
                inputTypeList.numberOfPeopleInRoom.value = 2;
            }
            
            if (inputTypeList.includeChildrenInRoom != null) {
                inputTypeList.includeChildrenInRoom.value = 0;
            }
            
            if (inputTypeList.formatNightDay != null) {
                inputTypeList.formatNightDay.value = 0;
            }
            
            if (inputTypeList.expressionsCheck != null) {
                inputTypeList.expressionsCheck.value = 0;
            }
            
            if (inputTypeList.multipleRooms != null) {
                
                inputTypeList.multipleRooms.value = 0;
                
            }
            
            if (inputTypeList.displayRemainingCapacity.value != null){
                inputTypeList.displayRemainingCapacity.value = 0;
            }
            
            if (inputTypeList.maxAccountScheduleDay.value != null){
                inputTypeList.maxAccountScheduleDay.value = 30;
            }
            
            if (inputTypeList.cancellationOfBooking != null){
                inputTypeList.cancellationOfBooking.value = 0;
            }
            
            if (inputTypeList.flowOfBooking != null){
                inputTypeList.flowOfBooking.value = "calendar";
            }
            
            if (inputTypeList.displayDetailsOfCanceled != null) {
                
                inputTypeList.displayDetailsOfCanceled.value = 1;
                
            }
            
            if (inputTypeList.displayRemainingCapacityInCalendar != null) {
                
                inputTypeList.displayRemainingCapacityInCalendar.value = 0;
                if (object._locale == 'ja' || object._locale == 'ja-jp' || object._locale == 'ja_jp') {
                    
                    inputTypeList.displayThresholdOfRemainingCapacity.value = "50";
                    inputTypeList.displayRemainingCapacityHasMoreThenThreshold.value = "panorama_fish_eye";
                    inputTypeList.displayRemainingCapacityHasLessThenThreshold.value = "change_history";
                    inputTypeList.displayRemainingCapacityHas0.value = "cancel";
                    
                }
                
            }
            
            if (inputTypeList.preparationTime != null) {
                
                var preparationTimeList = {};
            	for (var i = 0; i <= 180; i += 5) {
            		
            		preparationTimeList[i] = object._i18n.get("%s min", [i]);
            		
            	}
            	inputTypeList.preparationTime.valueList = preparationTimeList;
                
            }
            
            if (inputTypeList.sendBookingVerificationCode != null) {
                
                inputTypeList.sendBookingVerificationCode.valueList[0].value = 'false';
                inputTypeList.sendBookingVerificationCode.valueList[1].value = 'false';
                inputTypeList.sendBookingVerificationCode.valueList[0].actions = null;
                inputTypeList.sendBookingVerificationCode.valueList[1].actions = null;
                
            }
            
            if (inputTypeList.cancellation_of_booking != null) {
                
                inputTypeList.cancellation_of_booking.valueList[0].value = 0;
                inputTypeList.cancellation_of_booking.valueList[1].value = 30;
                inputTypeList.cancellation_of_booking.valueList[2].value = 'not_refuse';
                inputTypeList.cancellation_of_booking.valueList[0].actions = null;
                inputTypeList.cancellation_of_booking.valueList[1].actions = null;
                inputTypeList.cancellation_of_booking.valueList[2].actions = null;
                
            }
            
            if (inputTypeList.minimum_guests != null) {
                
                inputTypeList.minimum_guests.valueList[0].value = 0;
                inputTypeList.minimum_guests.valueList[1].value = 0;
                inputTypeList.minimum_guests.valueList[2].value = 0;
                
            }
            
            if (inputTypeList.maximum_guests != null) {
                
                inputTypeList.maximum_guests.valueList[0].value = 0;
                inputTypeList.maximum_guests.valueList[1].value = 0;
                inputTypeList.maximum_guests.valueList[2].value = 0;
                
            }
            
            if (inputTypeList.calendar_sharing != null) {
                
                inputTypeList.calendar_sharing.valueList[0].value = null;
                inputTypeList.calendar_sharing.valueList[1].value = 0;
                inputTypeList.calendar_sharing.valueList[0].actions = {
                    1: function onclick(event) {
                        
                        var checkBox = this;
                        var textBox = inputData['maxAccountScheduleDay'].textBox;
                        var timezone = inputData['timezone'].selectBox;
                        textBox.disabled = false;
                        timezone.disabled = false;
                        object._console.log(timezone);
                        object._console.log(checkBox);
                        object._console.log(checkBox.checked);
                        if (checkBox.checked === true) {
                            
                            textBox.disabled = true;
                            timezone.disabled = true;
                            
                        }
                        
                    },
                    
                };
                
                inputTypeList.calendar_sharing.valueList[1].actions = [
                    function onchange(event) {
                        
                        var selectBox = this;
                        object._console.log(selectBox);
                        
                    },
                ];
        	    
        	}
        	
        	if (inputTypeList.guestsBool != null) {
        	    
        	    inputTypeList.guestsBool.value = 0;
        	    
        	}
        	
        	if (inputTypeList.insertConfirmedPage != null) {
        	    
        	    inputTypeList.insertConfirmedPage.value = 0;
        	    
        	}
        	
        	if (inputTypeList.bookingVerificationCode != null) {
        	    
        	    inputTypeList.bookingVerificationCode.value = 'false';
        	    
        	}
        	
        	if (inputTypeList.bookingVerificationCodeToUser != null) {
        	    
        	    inputTypeList.bookingVerificationCodeToUser.value = 'false';
        	    
        	}
        	
        	if (inputTypeList.displayRemainingCapacityInCalendarAsNumber != null) {
        	    
        	    inputTypeList.displayRemainingCapacityInCalendarAsNumber.value = 0;
        	    
        	}
        	
        	if (inputTypeList.displayRemainingSlots != null) {
                
                inputTypeList.displayRemainingSlots.valueList[0].value = 0;
                
            }
            
            inputTypeList.status.value = "open";
            if (inputTypeList.courseBool != null) {
                
                inputTypeList.courseBool.value = 0;
                
            }
            
            if (inputTypeList.preparationTimeSetting != null) {
                
                inputTypeList.preparationTimeSetting.valueList[0].value = 0;
                inputTypeList.preparationTimeSetting.valueList[1].value = 'before_after';
                
            }
            
            if (inputTypeList.insertCustomPage != null) {
                
                inputTypeList.insertCustomPage.valueList[0].value = null;
                inputTypeList.insertCustomPage.valueList[1].value = null;
                inputTypeList.insertCustomPage.valueList[2].value = null;
                inputTypeList.insertCustomPage.valueList[3].value = null;
                inputTypeList.insertCustomPage.valueList[4].value = null;
                inputTypeList.insertCustomPage.valueList[5].value = null;
                
            }
            
            if (inputTypeList.servicesFunction != null) {
                
                inputTypeList.servicesFunction.valueList[0].value = parseInt(0);
                inputTypeList.servicesFunction.valueList[1].value = 'calendar';
                inputTypeList.servicesFunction.valueList[2].value = parseInt(0);
                
            }
            
            
            inputTypeList.minimumNights.value = 0;
            inputTypeList.maximumNights.value = 0;
            //inputTypeList.clock.value = 24;
            inputTypeList.redirect_Page.valueList[0].value = 'page';
            inputTypeList.redirect_Page.valueList[2].value = '';
            inputTypeList.blockSameTimeBookingByUser.value = '0';
            
            //_defaultEmail
            /**
            inputTypeList.email_to.value = object._defaultEmail.email_to;
            inputTypeList.email_from.value = object._defaultEmail.email_from;
            inputTypeList.email_from_title.value = object._defaultEmail.email_from_title;
            **/
            
            if (inputTypeList.account_email_address != null) {
                
                inputTypeList.account_email_address.valueList[0].value = object._defaultEmail.email_to;
                inputTypeList.account_email_address.valueList[1].value = object._defaultEmail.email_from_title;
                inputTypeList.account_email_address.valueList[2].value = object._defaultEmail.email_from;
                
            }
            
            if (inputTypeList.maxBookingSlotsPerDay != null) {
                
                inputTypeList.maxBookingSlotsPerDay.valueList[0].value = 0;
                inputTypeList.maxBookingSlotsPerDay.valueList[1].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[2].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[3].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[4].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[5].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[6].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[7].value = 10;
                inputTypeList.maxBookingSlotsPerDay.valueList[8].value = 0;
                
            }
            
        } else if (mode == 'addGuests') {
            
            var json = [{number: 1, price: 0, name: "1 Person"}];
            if (inputTypeList.json.optionsType.prices != null) {
                
                var prices = inputTypeList.json.optionsType.prices.options;
                for (var i = 0; i < prices.length; i++) {
                    
                    prices[i].value = 0;
                    json[0][prices[i].key] = 0;
                    
                }
                object._console.log(inputTypeList.json.optionsType.prices.options);
                
            }
            
            
            inputTypeList.json.value = JSON.stringify(json);
            inputTypeList.active.value = 'true';
            inputTypeList.target.value = "adult";
            inputTypeList.required.value = "0";
            inputTypeList.guestsInCapacity.value = 'excluded';
            inputTypeList.reflectService.value = 0;
            inputTypeList.reflectAdditional.value = 0;
            
            
            
        } else if (mode == 'addCoupons') {
            
            inputTypeList.id.inputLimit = 2;
            inputTypeList.name.value = '';
            inputTypeList.active.value = '1';
            inputTypeList.target.value = "visitors";
            inputTypeList.limited.value = "unlimited";
            inputTypeList.method.value = "subtraction";
            inputTypeList.value.value = 100;
            inputTypeList.target.actions = {
                'visitors': function onclick(event) {
                    
                    object._console.log('visitors');
                    var limited = inputData.limited;
                    object._console.log(limited);
                    limited.unlimited.checked = true;
                    limited.limited.disabled = true;
                    
                },
                'users': function onclick(event) {
                    
                    object._console.log('users');
                    var limited = inputData.limited;
                    object._console.log(limited);
                    limited.limited.disabled = false;
                    
                }
                
            };
            inputTypeList.limited.actions = {
                'unlimited': function onclick(event) {
                    
                    var target = inputData.target;
                    object._console.log(target);
                    
                },'limited': function onclick(event) {
                    
                    var target = inputData.target;
                    object._console.log(target);
                    object._console.log(target.visitors.checked);
                    if (target.visitors.checked === true) {
                        
                        var limited = inputData.limited;
                        limited.unlimited.checked = true;
                        
                    }
                    
                }
            };
            
            
        } else if (mode == 'addSubscriptions') {
            
            inputTypeList.active.value = "true";
            inputTypeList.renewal.value = 1;
            inputTypeList.limit.value = 1;
            inputTypeList.numberOfTimes.value = 1;
            
        } else if (mode == 'addTaxes') {
            
            inputTypeList.active.value = "true";
            inputTypeList.type.value = "tax";
            inputTypeList.tax.value = "tax_exclusive";
            inputTypeList.target.value = "room";
            inputTypeList.method.value = "addition";
            inputTypeList.scope.value = "day";
            inputTypeList.value.value = 1000;
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = 0;
                
            }
            
        } else if (mode == 'addTax') {
            
            inputTypeList.active.value = "true";
            inputTypeList.tax.value = "tax_exclusive";
            inputTypeList.target.value = "room";
            inputTypeList.method.value = "addition";
            inputTypeList.value.value = 1000;
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = 0;
                
            }
            
            inputTypeList.method.actions = {
                
                'addition': function onclick(event) {
                    
                    object._console.log('addition');
                    var displayFormat = document.getElementsByClassName('displayFormat_extraChargeValueAndTaxValue')[0];
                    var tax_exclusive = document.getElementsByClassName('tax_exclusive');
                    var tax_inclusive = document.getElementsByClassName('tax_inclusive');
                    var target_room = document.getElementsByClassName('target_room');
                    var target_guest = document.getElementsByClassName('target_guest');
                    var valueName = document.getElementById('booking-package_value').getElementsByTagName('th')[0];
                    var inputText = document.getElementById('booking-package_value').getElementsByTagName('input')[0];
                    var value = object._format.formatCost(parseInt(inputText.value), object._currency);
                    tax_exclusive[0].disabled = true;
                    tax_inclusive[0].disabled = true;
                    target_room[0].disabled = false;
                    target_guest[0].disabled = false;
                    valueName.textContent = object._i18n.get('Fixed tax amount');
                    displayFormat.textContent = object._i18n.get("Display format: %s", [value]);
                    
                },
                'multiplication': function onclick(event) {
                    
                    object._console.log('multiplication');
                    var displayFormat = document.getElementsByClassName('displayFormat_extraChargeValueAndTaxValue')[0];
                    var tax_exclusive = document.getElementsByClassName('tax_exclusive');
                    var tax_inclusive = document.getElementsByClassName('tax_inclusive');
                    var target_room = document.getElementsByClassName('target_room');
                    var target_guest = document.getElementsByClassName('target_guest');
                    var valueName = document.getElementById('booking-package_value').getElementsByTagName('th')[0];
                    var inputText = document.getElementById('booking-package_value').getElementsByTagName('input')[0];
                    var value = inputText.value + '%';
                    tax_exclusive[0].disabled = false;
                    tax_inclusive[0].disabled = false;
                    target_room[0].disabled = true;
                    target_guest[0].disabled = true;
                    valueName.textContent = object._i18n.get('Tax rate');
                    displayFormat.textContent = object._i18n.get("Display format: %s", [value]);
                    
                    object._console.log(object._prefix);
                    object._console.log(valueName);
                    object._console.log(inputText);
                    
                }
                
            };
            
            inputTypeList.value.actions = [
                
                function onchange(evnent) {
                    
                    var value = this.value;
                    var method = 'addition';
                    object._console.log(value);
                    var displayFormat = document.getElementsByClassName('displayFormat_extraChargeValueAndTaxValue')[0];
                    var calculationMethod = document.getElementsByClassName('calculationMethod');
                    for (var i = 0; i < calculationMethod.length; i++) {
                        
                        if (calculationMethod[i].checked === true) {
                            
                            method = calculationMethod[i].getAttribute('data-value');
                            object._console.log(calculationMethod[i]);
                            
                        }
                        
                    }
                    var cost = object._format.formatCost(parseInt(value), object._currency);
                    if (method == 'multiplication') {
                        
                        cost = value + '%';
                        
                    }
                    object._console.log(method);
                    object._console.log(cost);
                    displayFormat.textContent = object._i18n.get("Display format: %s", [cost]);
                    
                }
                
            ];
            
        } else if (mode == 'addExtraCharge') {
            
            inputTypeList.active.value = "true";
            inputTypeList.target.value = "room";
            inputTypeList.scope.value = "day";
            inputTypeList.value.value = 1000;
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = 0;
                
            }
            
            
        } else if (mode == 'addOptionsForHotel') {
            
            inputTypeList.required.value = 'true';
            inputTypeList.target.value = 'guests';
            inputTypeList.range.value = 'allDays';
            inputTypeList.target.actions = {
                'guests': function onclick(event) {
                    
                    object._console.log('guests');
                    
                    var optionWithChargeAdult = document.getElementsByClassName('optionWithChargeAdult');
                    var optionWithChargeChild = document.getElementsByClassName('optionWithChargeChild');
                    var optionWithChargeRoom = document.getElementsByClassName('optionWithChargeRoom');
                    for (var i = 0; i < optionWithChargeAdult.length; i++) {
                        
                        optionWithChargeAdult[i].disabled = false;
                        
                    }
                    for (var i = 0; i < optionWithChargeChild.length; i++) {
                        
                        optionWithChargeChild[i].disabled = false;
                        
                    }
                    for (var i = 0; i < optionWithChargeRoom.length; i++) {
                        
                        optionWithChargeRoom[i].disabled = true;
                        
                    }
                    
                },
                'room': function onclick(event) {
                    
                    object._console.log('room');
                    
                    var optionWithChargeAdult = document.getElementsByClassName('optionWithChargeAdult');
                    var optionWithChargeChild = document.getElementsByClassName('optionWithChargeChild');
                    var optionWithChargeRoom = document.getElementsByClassName('optionWithChargeRoom');
                    for (var i = 0; i < optionWithChargeAdult.length; i++) {
                        
                        optionWithChargeAdult[i].disabled = true;
                        
                    }
                    for (var i = 0; i < optionWithChargeChild.length; i++) {
                        
                        optionWithChargeChild[i].disabled = true;
                        
                    }
                    for (var i = 0; i < optionWithChargeRoom.length; i++) {
                        
                        optionWithChargeRoom[i].disabled = false;
                        
                    }
                    
                }
                
            };
            
            inputTypeList.json.actions = function () {
                
                object._console.log('add');
                object._console.log(inputData.target);
                
                var optionWithChargeAdult = document.getElementsByClassName('optionWithChargeAdult');
                var optionWithChargeChild = document.getElementsByClassName('optionWithChargeChild');
                var optionWithChargeRoom = document.getElementsByClassName('optionWithChargeRoom');
                
                for (var key in inputData.target) {
                    
                    object._console.log(key);
                    var optionValue = inputData.target[key].checked;
                    if (key == 'guests' && optionValue === true) {
                        
                        object._console.log('disabled rooms');
                        for (var i = 0; i < optionWithChargeAdult.length; i++) {
                            
                            optionWithChargeAdult[i].disabled = false;
                            
                        }
                        for (var i = 0; i < optionWithChargeChild.length; i++) {
                            
                            optionWithChargeChild[i].disabled = false;
                            
                        }
                        for (var i = 0; i < optionWithChargeRoom.length; i++) {
                            
                            optionWithChargeRoom[i].disabled = true;
                            
                        }
                        
                        
                    } else if (key == 'room' && optionValue === true) {
                        
                        object._console.log('disabled guests');
                        for (var i = 0; i < optionWithChargeAdult.length; i++) {
                            
                            optionWithChargeAdult[i].disabled = true;
                            
                        }
                        for (var i = 0; i < optionWithChargeChild.length; i++) {
                            
                            optionWithChargeChild[i].disabled = true;
                            
                        }
                        for (var i = 0; i < optionWithChargeRoom.length; i++) {
                            
                            optionWithChargeRoom[i].disabled = false;
                            
                        }
                        
                    }
                    
                }
                
            };
            
        }
        
    	object._console.log(inputTypeList);
    	var inputData = {};
    	var table = object.create('table', null, null, null, null, 'form-table', null);
    	var trList = {};
    	for (var key in inputTypeList) {
    		
    		object._console.log(key);
            var data = inputTypeList[key];
            if (mode == 'addTaxes' && account.type == 'day' && (key == 'target' || key == 'scope') ) {
                
                continue;
                
            }
            
            if ( (mode == 'addTax' || mode == 'addExtraCharge') && account.type == 'day' && (key == 'target' || key == 'scope') ) {
                
                continue;
                
            }
            
            var th = document.createElement("th");
            th.setAttribute("scope", "row");
            th.textContent = object._i18n.get(inputTypeList[key].name);
            
            
            var eventAction = null;
            if (parseInt(data.option) == 1) {
                
                eventAction = function(event){
                    
                    object._console.log(this);
                    var value = this.getAttribute("data-value");
                    var name = this.name;
                    object._console.log(inputData);
                    object._console.log("value = " + value);
                    object._console.log(inputTypeList[name]);
                    if (value == 'tax') {
                        
                        for (var optionKey in inputTypeList[name].optionsList) {
                            
                            object._console.log(inputData[optionKey]);
                            if(inputData[optionKey] != null){
                                
                                object._console.log(inputTypeList[optionKey]);
                                for(var key in inputData[optionKey]){
                                    
                                    var disabled = true;
                                    if(parseInt(inputTypeList[name].optionsList[optionKey]) == 1){
                                        
                                        disabled = false;
                                        
                                    }
                                    object._console.log("disabled = " + disabled);
                                    var elements = inputData[optionKey][key];
                                    object._console.log(elements);
                                    elements.disabled = disabled;
                                    
                                }
                                
                            }
                            
                        }
                        
                    } else if (value == 'surcharge') {
                        
                        for (var optionKey in inputTypeList[name].optionsList) {
                            
                            object._console.log(inputData[optionKey]);
                            if(inputData[optionKey] != null){
                                
                                object._console.log(inputTypeList[optionKey]);
                                for(var key in inputData[optionKey]){
                                    
                                    var disabled = false;
                                    if(parseInt(inputTypeList[name].optionsList[optionKey]) == 1){
                                        
                                        disabled = true;
                                        
                                    }
                                    object._console.log("disabled = " + disabled);
                                    var elements = inputData[optionKey][key];
                                    object._console.log(elements);
                                    elements.disabled = disabled;
                                    if (optionKey == 'tax') {
                                        
                                        if (key == 'tax_inclusive') {
                                            
                                            elements.checked = true;
                                            
                                        } else {
                                            
                                            elements.checked = false;
                                            
                                        }
                                        
                                    }
                                    
                                    if (optionKey == 'method') {
                                        
                                        if (key == 'addition') {
                                            
                                            elements.checked = true;
                                            
                                        } else {
                                            
                                            elements.checked = false;
                                            
                                        }
                                        
                                    }
                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                };
                
            }
            
            var upperPanel = null;
            if (key == "cost") {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel', null);
                eventAction = function(event){
                    
                    var value = parseInt(this.value);
                    var cost = object._format.formatCost(value, object._currency);
                    var upperPanel = this.parentElement.getElementsByClassName("upperPanel")[0];
                    upperPanel.textContent = cost;
                    this.value = value;
                    object._console.log("value = " + value);
                    
                };
                
            }
            
            if (mode == 'addTaxes' && key == 'value') {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel', null);
                eventAction = function(event){
                    
                    var value = this.value;
                    var cost = object._format.formatCost(parseInt(value), object._currency);
                    var upperPanel = this.parentElement.getElementsByClassName("upperPanel")[0];
                    upperPanel.textContent = object._i18n.get("%s or %s", [cost, parseFloat(value) + '%']);
                    this.value = value;
                    object._console.log("value = " + value);
                    
                };
                
            }
            
            if (mode == 'addTax' && key == 'value') {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel displayFormat_extraChargeValueAndTaxValue', null);
                
            }
            
            if (mode == 'addCoupons' && key == 'value') {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel', null);
                object._console.log(upperPanel);
                eventAction = function(event){
                    
                    var value = this.value;
                    var cost = object._format.formatCost(parseInt(value), object._currency);
                    var upperPanel = this.parentElement.getElementsByClassName("upperPanel")[0];
                    this.value = value;
                    object._console.log("value = " + value);
                    
                };
                
            }
            
            var disabled = false;
            if(data.disabled != null && parseInt(data.disabled) == 1){
                
                disabled = true;
                
            }
            
            var inputPanel = object.createInput(key, data, inputData, account, disabled, eventAction, object._isExtensionsValid);
            if (upperPanel != null) {
                
                if (key == "cost") {
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    upperPanel.textContent = cost;
                    inputPanel.insertAdjacentElement("afterbegin", upperPanel);
                    
                } else if (mode == 'addTaxes' && key == 'value') {
                    
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    upperPanel.textContent = object._i18n.get("%s or %s", [cost, data.value + '%']);
                    inputPanel.insertAdjacentElement("beforeend", upperPanel);
                    
                } else if (mode == 'addTax' && key == 'value') {
                    
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    upperPanel.textContent = object._i18n.get("Display format: %s", [cost]);
                    inputPanel.insertAdjacentElement("beforeend", upperPanel);
                    
                } else if (mode == 'addCoupons' && key == 'value') {
                    
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    inputPanel.insertAdjacentElement("beforeend", upperPanel);
                    
                }
                
                
            }
            
            if (mode == 'addCoupons' && key == 'limited') {
                
                var limited = inputData.limited;
                limited.limited.disabled = true;
                object._console.log(limited);
                
            }
            
            if ((mode == object._prefix + 'addCourse' || mode == 'addCoupons') && key == 'expirationDate' && object._locale == 'ja') {
                
                object._console.log(inputPanel);
                var expirationDateFrom = inputPanel.getElementsByClassName('expirationDateFrom');
                var formTitleLabel = expirationDateFrom[0].getElementsByTagName('span')[0];
                formTitleLabel.classList.add('from-ja');
                expirationDateFrom[2].insertAdjacentElement('beforeend', formTitleLabel);
                var expirationDateTo = inputPanel.getElementsByClassName('expirationDateTo');
                var toTitleLabel = expirationDateTo[0].getElementsByTagName('span')[0];
                toTitleLabel.classList.add('to-ja');
                expirationDateTo[2].insertAdjacentElement('beforeend', toTitleLabel);
                
            }
            
            var td = object.create('td', null, [inputPanel], null, null, null, null);
            var tr = object.create('tr', null, [th, td], "booking-package_" + key, null, null, null);
            tr.setAttribute("valign", "top");
            trList[key] = tr;
            table.appendChild(tr);
            
            if (mode == 'addCoupons' && key == 'id') {
                
                tr.classList.add('hidden_panel');
                
            }
            
            if (mode == 'addTax' && key == 'scope') {
                
                tr.classList.add('hidden_panel');
                
            }
            
            if(inputTypeList[key].class != null){
                
                tr.setAttribute("class", inputTypeList[key].class);
                
            }
            
            if (mode == 'addCalendarAccount' && data.target != 'both' && data.target != calendarType) {
                
                tr.classList.add('hidden_panel');
                
            }
            
            if (mode == 'addGuests' && data.target != 'both' && data.target != account.type) {
                
                tr.classList.add('hidden_panel');
                
            }
    		
    	}
    	
    	object._console.log(inputData);
        
        addPanel.appendChild(table);
        
        if (mode == object._prefix + 'addCourse') {
            
            var stopServicePanel = document.getElementById('setting_stopService');
            if (stopServicePanel != null) {
                
                object._console.log(stopServicePanel);
                var stopServiceUnderFollowingConditionsPanel = stopServicePanel.getElementsByClassName('stopServiceUnderFollowingConditions')[0];
                var targetRabel1 = stopServiceUnderFollowingConditionsPanel.getElementsByTagName('label')[1];
                var doNotStopServiceAsExceptionPanel = stopServicePanel.getElementsByClassName('doNotStopServiceAsException')[0];
                object._console.log(stopServiceUnderFollowingConditionsPanel);
                object._console.log(doNotStopServiceAsExceptionPanel);
                object._console.log(targetRabel1);
                targetRabel1.appendChild(doNotStopServiceAsExceptionPanel);
                
                var targetRabel2 = stopServiceUnderFollowingConditionsPanel.getElementsByTagName('label')[4];
                var stopServiceForDayOfTimesPanel = stopServicePanel.getElementsByClassName('stopServiceForDayOfTimes')[0];
                targetRabel2.appendChild(stopServiceForDayOfTimesPanel);
                var stopServiceForSpecifiedNumberOfTimesPanel = stopServicePanel.getElementsByClassName('stopServiceForSpecifiedNumberOfTimes')[0];
                targetRabel2.appendChild(stopServiceForSpecifiedNumberOfTimesPanel);
                
            }
            
        }
        
        
        var saveButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
        var cancelButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Cancel") );
        var buttonPanel = object.create('div', null, [saveButton, cancelButton], null, null, 'bottomButtonPanel', null);
        addPanel.appendChild(buttonPanel);
        if (object._isExtensionsValid === 0 && mode == 'addOptionsForHotel') {
            
            saveButton.disabled = true;
            var extensionsValidPanel = object.create('div', object._i18n.get("Paid plan subscription required."), null, null, null, 'extensionsValid', null);
            buttonPanel.insertAdjacentElement("afterbegin", extensionsValidPanel);
            
        }
        
        object._console.log("top = " + addPanel.offsetTop);
        object._console.log("closeHeghit = " + closeHeghit);
    	window.scrollTo(0, addPanel.offsetTop);
        
        cancelButton.onclick = function(event){
        	
        	mainPanel.removeChild(addPanel);
        	mainPanel.style.height = null;
        	object._console.log("cancelButton closeHeghit = " + closeHeghit);
        	callback("close");
        	
        };
        
        saveButton.onclick = function(event){
        	
        	var response = null;
        	
        	var postData = {mode: mode, nonce: object.nonce, action: object.action};
        	if (account != null) {
        	    
        	    postData.accountKey = account.key;
        	    
        	}
        	
        	if (mode == object._prefix + 'addCourse') {
        		
        		object._console.log("rank = " + (document.getElementById("courseSort").childNodes.length + 1));
        		postData['rank'] = document.getElementById("courseSort").childNodes.length + 1;
        		response = object.getInputData(inputTypeList, inputData);
        		
            } else if (mode == 'addForm') {
                
                response = object.getInputData(inputTypeList, inputData);
                object._console.log(response);
                if ((response.type == 'CHECK' || response.type == 'SELECT' || response.type == 'RADIO') && response.options.length == 0) {
                    
                    response.options = false;
                    
                }
                
            } else if (mode == 'addCalendarAccount') {
                
                response = object.getInputData(inputTypeList, inputData);
                
            } else if (mode == 'addGuests') {
                
                response = object.getInputData(inputTypeList, inputData);
                object._console.log("json = " + JSON.parse(response.json).length);
                object._console.log(response.json);
                if (JSON.parse(response.json).length == null || JSON.parse(response.json).length == 0) {
                    
                    response.json = false;
                    
                } else {
                    
                    var guests = JSON.parse(response.json);
                    for (var key in guests) {
                        
                        var guest = guests[key];
                        object._console.log(guest);
                        if (guest.price.length == 0) {
                            
                            guest.price = 0;
                            
                        }
                        if (guest.number.length == 0 || guest.price.length == 0 || guest.name.length == 0) {
                            
                            response.json = false;
                            break;
                            
                        }
                        
                    }
                    
                }
            
            } else if (mode == 'addCoupons') {
                
                response = object.getInputData(inputTypeList, inputData);
                
            } else if (mode == 'addSubscriptions') {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['rank'] = document.getElementById("courseSort").childNodes.length + 1;
                
            } else if (mode == 'addTaxes') {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['rank'] = document.getElementById("taxSort").childNodes.length + 1;
                if (account.type == 'day') {
                    
                    response.scope = 'booking';
                    response.target = 'room';
                    
                }
                
            } else if (mode == 'addTax' || mode == 'addExtraCharge') {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['rank'] = document.getElementById("taxesSort").childNodes.length + 1;
                if (account.type == 'day') {
                    
                    response.scope = 'booking';
                    response.target = 'room';
                    
                }
                
            } else if (mode == 'addOptionsForHotel') {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['rank'] = document.getElementById("optionsForHotelSort").childNodes.length + 1;
                
            }
        	
        	object._console.log(response);
        	var post = true;
        	for(var key in response){
        			
        		if(typeof response[key] == 'boolean'){
        			
        			object._console.log("error key = " + key + " bool = " + response[key]);
        			if (trList[key] != null) {
        			    
        			    trList[key].classList.add("errorPanel");
        			    
        			}
        			post = false;
        			
        		}else{
        			
        			postData[key] = response[key];
        			if(trList[key] != null){
        			    
        			    trList[key].classList.remove("errorPanel");
        			    
        			}
        			
        		}
        		
        	}
        	
        	if(post === true){
        		
        		object._console.log(postData);
        		object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        		object.setFunction("addItem", post);
        		object.xmlHttp = new Booking_App_XMLHttp(object.url, postData, object._webApp, function(json){
                    
                    if(json['status'] != 'error'){
                        
                        mainPanel.removeChild(addPanel);
                        mainPanel.style.height = null;
                        object._console.log(json);
                        callback(json);
                        
                    }else{
                        
                        alert(json["message"]);
                        
                    }
                    object.loadingPanel.setAttribute("class", "hidden_panel");
	                
    			}, function(text){
                    
                    object.setResponseText(text);
                    
                });
        		
        	}
        	
    		
        };
    	
    };

    SCHEDULE.prototype.editItem = function(columns, editKey, mainPanel, columnsPanel, mode, account, itemData, inputTypeList, callback){
    	
    	var object = this;
    	object._console.log(account);
    	object._console.log("editKey = " + editKey);
    	object._console.log(columns[editKey]);
    	object._console.log(itemData);
    	object._console.log(inputTypeList);
    	var addPanel = document.createElement("div");
    	addPanel.id = "addCoursePanel";
    	
        var inputData = {};
        if (mode == object._prefix + 'updateCourse') {
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.description.value = itemData.description;
            inputTypeList.active.value = itemData.active;
            if (inputTypeList.selectOptions != null) {
                
                inputTypeList.selectOptions.value = itemData.selectOptions;
                
            }
            
            if (inputTypeList.cost != null) {
                
                inputTypeList.cost.value = parseInt(itemData.cost);
                
            }
            
            if (inputTypeList.options != null) {
                
                inputTypeList.options.value = itemData.options;
                
            }
            
            if (inputTypeList.target != null) {
                
                inputTypeList.target.value = itemData.target;
                
            }
            
            if (inputTypeList.stopService != null) {
                
                inputTypeList.stopService.valueList[0].value = itemData.stopServiceUnderFollowingConditions;
                inputTypeList.stopService.valueList[1].value = itemData.doNotStopServiceAsException;
                inputTypeList.stopService.valueList[2].value = itemData.stopServiceForDayOfTimes;
                inputTypeList.stopService.valueList[3].value = itemData.stopServiceForSpecifiedNumberOfTimes;
                
            }
            
            inputTypeList.time.value = parseInt(itemData.time);
            var courseTimeList = {};
            for(var i = 5; i < 1440; i += 5){
                
                courseTimeList[i] = object._i18n.get("%s min", [i]);
                
            }
            inputTypeList.time.valueList = courseTimeList;
            
        	if (itemData.timeToProvide == null || itemData.timeToProvide.length == 0 || typeof itemData.timeToProvide == 'string') {
        	    
        	    var timeToProvide = {};
            	for(var day = 0; day < 8; day++){
            	    
            	    var hours = {};
            	    for(var i = 0; i < 24; i++){
                	    
                	    var time = i * 60;
                	    hours[time] = 1;
                	    
                	}
                	timeToProvide[day] = hours;
            	    
            	}
            	inputTypeList.timeToProvide.value = timeToProvide;
        	    
            } else {
                
                inputTypeList.timeToProvide.value = itemData.timeToProvide;
                
            }
            
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = parseInt(itemData.expirationDateStatus);
                inputTypeList.expirationDate.valueList[1].value = parseInt(itemData.expirationDateFrom.substr(4, 2));
                inputTypeList.expirationDate.valueList[2].value = parseInt(itemData.expirationDateFrom.substr(6, 2));
                inputTypeList.expirationDate.valueList[3].value = parseInt(itemData.expirationDateFrom.substr(0, 4));
                inputTypeList.expirationDate.valueList[4].value = parseInt(itemData.expirationDateTo.substr(4, 2));
                inputTypeList.expirationDate.valueList[5].value = parseInt(itemData.expirationDateTo.substr(6, 2));
                inputTypeList.expirationDate.valueList[6].value = parseInt(itemData.expirationDateTo.substr(0, 4));
                
            }
            
            if (inputTypeList.costs != null) {
                
                 inputTypeList.costs.valueList[0].value = parseInt(itemData.cost_1);
                 inputTypeList.costs.valueList[1].value = parseInt(itemData.cost_2);
                 inputTypeList.costs.valueList[2].value = parseInt(itemData.cost_3);
                 inputTypeList.costs.valueList[3].value = parseInt(itemData.cost_4);
                 inputTypeList.costs.valueList[4].value = parseInt(itemData.cost_5);
                 inputTypeList.costs.valueList[5].value = parseInt(itemData.cost_6);
                
            }
        	
        } else if (mode == 'updateForm') {
        	
        	inputTypeList["id"]["value"] = itemData["id"];
        	inputTypeList["name"]["value"] = itemData["name"];
        	inputTypeList["description"]["value"] = itemData["description"];
        	inputTypeList["type"]["value"] = itemData["type"];
        	inputTypeList["active"]["value"] = itemData["active"];
        	inputTypeList["required"]["value"] = itemData["required"];
        	inputTypeList["isName"]["value"] = itemData["isName"];
        	inputTypeList["isAddress"]["value"] = itemData["isAddress"];
        	inputTypeList["isEmail"]["value"] = itemData["isEmail"];
        	inputTypeList["options"]["value"] = itemData["options"];
        	inputTypeList['placeholder']['value'] = itemData['placeholder'];
        	
        	if (inputTypeList.isSMS != null) {
        	    
        	    inputTypeList.isSMS.value = itemData.isSMS;
        	    
        	}
        	
        	if (itemData.isAutocomplete != null) {
        	    
        	    inputTypeList.isAutocomplete.value = itemData.isAutocomplete;
        	    
        	} else {
        	    
        	    inputTypeList.isAutocomplete.value = 'true';
        	    
        	}
        	
        	if (itemData.uri != null) {
        	    
        	    inputTypeList.uri.value = itemData.uri;
        	    
        	}
        	
        	if (itemData.isTerms != null) {
        	    
        	    inputTypeList.isTerms.value = itemData.isTerms;
        	    
        	} else {
        	    
        	    inputTypeList.isTerms.value = "false";
        	    
        	}
        	
        	if (itemData.targetCustomers != null) {
        	    
        	    inputTypeList.targetCustomers.value = itemData.targetCustomers;
        	    
        	} else {
        	    
        	    inputTypeList.targetCustomers.value = "customersAndUsers";
        	    
        	}
        	
        } else if (mode == 'updateGuests') {
            
            if (inputTypeList.json.optionsType.prices != null) {
                
                var prices = inputTypeList.json.optionsType.prices.options;
                var guests = JSON.parse(itemData.json);
                for (var i = 0; i < guests.length; i++) {
                    
                    guests[i] = (function(guest, prices) {
                        
                        for (var i = 0; i < prices.length; i++) {
                            
                            var key = prices[i].key;
                            if (guest[key] == null) {
                                
                                guest[key] = guest.price;
                                
                            }
                            
                        }
                        
                        return guest;
                        
                    })(guests[i], prices);
                    object._console.log(guests[i]);
                    
                }
                itemData.json = JSON.stringify(guests);
                object._console.log(itemData.json);
                
            }
            
            
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.target.value = itemData.target;
            inputTypeList.active.value = itemData.active;
            inputTypeList.json.value = itemData.json;
            inputTypeList.required.value = itemData.required;
            inputTypeList.guestsInCapacity.value = itemData.guestsInCapacity;
            inputTypeList.reflectService.value = itemData.reflectService;
            inputTypeList.reflectAdditional.value = itemData.reflectAdditional;
            inputTypeList.description.value = itemData.description;
            
            if (inputTypeList.costInServices != null) {
                
                inputTypeList.costInServices.value = itemData.costInServices;
                
            }
            
        } else if (mode == 'updateCoupons') {
            
            inputTypeList.id.inputLimit = 1;
            inputTypeList.id.value = itemData.id;
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            inputTypeList.target.value = itemData.target;
            inputTypeList.limited.value = itemData.limited;
            inputTypeList.method.value = itemData.method;
            inputTypeList.value.value = itemData.value;
            inputTypeList.description.value = itemData.description;
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = parseInt(itemData.expirationDateStatus);
                inputTypeList.expirationDate.valueList[1].value = parseInt(itemData.expirationDateFrom.substr(4, 2));
                inputTypeList.expirationDate.valueList[2].value = parseInt(itemData.expirationDateFrom.substr(6, 2));
                inputTypeList.expirationDate.valueList[3].value = parseInt(itemData.expirationDateFrom.substr(0, 4));
                inputTypeList.expirationDate.valueList[4].value = parseInt(itemData.expirationDateTo.substr(4, 2));
                inputTypeList.expirationDate.valueList[5].value = parseInt(itemData.expirationDateTo.substr(6, 2));
                inputTypeList.expirationDate.valueList[6].value = parseInt(itemData.expirationDateTo.substr(0, 4));
                
            }
            inputTypeList.target.actions = {
                'visitors': function onclick(event) {
                    
                    object._console.log('visitors');
                    var limited = inputData.limited;
                    object._console.log(limited);
                    limited.unlimited.checked = true;
                    limited.limited.disabled = true;
                    
                },
                'users': function onclick(event) {
                    
                    object._console.log('users');
                    var limited = inputData.limited;
                    object._console.log(limited);
                    limited.limited.disabled = false;
                    
                }
                
            };
            inputTypeList.limited.actions = {
                'unlimited': function onclick(event) {
                    
                    var target = inputData.target;
                    object._console.log(target);
                    
                },'limited': function onclick(event) {
                    
                    var target = inputData.target;
                    object._console.log(target);
                    object._console.log(target.visitors.checked);
                    if (target.visitors.checked === true) {
                        
                        var limited = inputData.limited;
                        limited.unlimited.checked = true;
                        
                    }
                    
                }
            };
            
        } else if (mode == 'updateSubscriptions') {
            
            inputTypeList.subscription.value = itemData.subscription;
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            inputTypeList.renewal.value = parseInt(itemData.renewal);
            inputTypeList.limit.value = parseInt(itemData.limit);
            inputTypeList.numberOfTimes.value = parseInt(itemData.numberOfTimes);
            
        } else if (mode == 'updateTaxes') {
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            inputTypeList.type.value = itemData.type;
            inputTypeList.tax.value = itemData.tax;
            //inputTypeList.type.inputType = "TEXT";
            inputTypeList.method.value = itemData.method;
            inputTypeList.target.value = itemData.target;
            inputTypeList.scope.value = itemData.scope;
            inputTypeList.value.value = itemData.value;
            
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = parseInt(itemData.expirationDateStatus);
                inputTypeList.expirationDate.valueList[1].value = parseInt(itemData.expirationDateFrom.substr(4, 2));
                inputTypeList.expirationDate.valueList[2].value = parseInt(itemData.expirationDateFrom.substr(6, 2));
                inputTypeList.expirationDate.valueList[3].value = parseInt(itemData.expirationDateFrom.substr(0, 4));
                inputTypeList.expirationDate.valueList[4].value = parseInt(itemData.expirationDateTo.substr(4, 2));
                inputTypeList.expirationDate.valueList[5].value = parseInt(itemData.expirationDateTo.substr(6, 2));
                inputTypeList.expirationDate.valueList[6].value = parseInt(itemData.expirationDateTo.substr(0, 4));
                
            }
            
        } else if (mode == 'updateExtraCharge') {
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            //inputTypeList.type.inputType = "TEXT";
            inputTypeList.target.value = itemData.target;
            inputTypeList.scope.value = itemData.scope;
            inputTypeList.value.value = itemData.value;
            
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = parseInt(itemData.expirationDateStatus);
                inputTypeList.expirationDate.valueList[1].value = parseInt(itemData.expirationDateFrom.substr(4, 2));
                inputTypeList.expirationDate.valueList[2].value = parseInt(itemData.expirationDateFrom.substr(6, 2));
                inputTypeList.expirationDate.valueList[3].value = parseInt(itemData.expirationDateFrom.substr(0, 4));
                inputTypeList.expirationDate.valueList[4].value = parseInt(itemData.expirationDateTo.substr(4, 2));
                inputTypeList.expirationDate.valueList[5].value = parseInt(itemData.expirationDateTo.substr(6, 2));
                inputTypeList.expirationDate.valueList[6].value = parseInt(itemData.expirationDateTo.substr(0, 4));
                
            }
            
            
        } else if (mode == 'updateTax') {
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            inputTypeList.tax.value = itemData.tax;
            //inputTypeList.type.inputType = "TEXT";
            inputTypeList.method.value = itemData.method;
            inputTypeList.target.value = itemData.target;
            inputTypeList.scope.value = itemData.scope;
            inputTypeList.value.value = itemData.value;
            
            if (inputTypeList.expirationDate != null) {
                
                inputTypeList.expirationDate.valueList[0].value = parseInt(itemData.expirationDateStatus);
                inputTypeList.expirationDate.valueList[1].value = parseInt(itemData.expirationDateFrom.substr(4, 2));
                inputTypeList.expirationDate.valueList[2].value = parseInt(itemData.expirationDateFrom.substr(6, 2));
                inputTypeList.expirationDate.valueList[3].value = parseInt(itemData.expirationDateFrom.substr(0, 4));
                inputTypeList.expirationDate.valueList[4].value = parseInt(itemData.expirationDateTo.substr(4, 2));
                inputTypeList.expirationDate.valueList[5].value = parseInt(itemData.expirationDateTo.substr(6, 2));
                inputTypeList.expirationDate.valueList[6].value = parseInt(itemData.expirationDateTo.substr(0, 4));
                
            }
            
            inputTypeList.value.actions = [
                
                function onchange(evnent) {
                    
                    
                    
                }
                
            ];
            
        } else if (mode == 'updateOptionsForHotel') {
            
            inputTypeList.name.value = itemData.name;
            inputTypeList.active.value = itemData.active;
            inputTypeList.range.value = itemData.range;
            inputTypeList.required.value = 'false';
            inputTypeList.description.value = itemData.description;
            if (parseInt(itemData.required) == 1) {
                
                inputTypeList.required.value = 'true';
                
            }
            inputTypeList.target.value = itemData.target;
            
            inputTypeList.target.actions = null;
            
            if (inputTypeList.json != null) {
                
                inputTypeList.json.value = itemData.json;
                
            }
            
            inputTypeList.json.actions = function () {
                
                object._console.log('add');
                object._console.log(inputData.target);
                var optionWithChargeAdult = document.getElementsByClassName('optionWithChargeAdult');
                var optionWithChargeChild = document.getElementsByClassName('optionWithChargeChild');
                var optionWithChargeRoom = document.getElementsByClassName('optionWithChargeRoom');
                for (var key in inputData.target) {
                    
                    object._console.log(key);
                    var optionValue = inputData.target[key].checked;
                    if (key == 'guests' && optionValue === true) {
                        
                        object._console.log('disabled rooms');
                        for (var i = 0; i < optionWithChargeAdult.length; i++) {
                            
                            optionWithChargeAdult[i].disabled = false;
                            
                        }
                        for (var i = 0; i < optionWithChargeChild.length; i++) {
                            
                            optionWithChargeChild[i].disabled = false;
                            
                        }
                        for (var i = 0; i < optionWithChargeRoom.length; i++) {
                            
                            optionWithChargeRoom[i].disabled = true;
                            
                        }
                        
                        
                    } else if (key == 'room' && optionValue === true) {
                        
                        object._console.log('disabled guests');
                        for (var i = 0; i < optionWithChargeAdult.length; i++) {
                            
                            optionWithChargeAdult[i].disabled = true;
                            
                        }
                        for (var i = 0; i < optionWithChargeChild.length; i++) {
                            
                            optionWithChargeChild[i].disabled = true;
                            
                        }
                        for (var i = 0; i < optionWithChargeRoom.length; i++) {
                            
                            optionWithChargeRoom[i].disabled = false;
                            
                        }
                        
                    }
                    
                }
                
            };
            
            
        }
        
        var index = 0;
    	object._console.log(inputTypeList);
    	
    	var table = document.createElement("table");
    	table.setAttribute("class", "form-table");
    	
    	var trList = {}
    	for (var key in inputTypeList) {
    		
    		object._console.log(key);
            var data = inputTypeList[key];
            if ( (mode == 'updateTaxes' || mode == 'updateExtraCharge') && account.type == 'day' && (key == 'target' || key == 'scope') ) {
                
                //continue;
                
            }
            
            var eventAction = null;
            var upperPanel = null;
            if (key == "cost") {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel', null);
                eventAction = function(event){
                    
                    var value = parseInt(this.value);
                    var cost = object._format.formatCost(value, object._currency);
                    var upperPanel = this.parentElement.getElementsByClassName("upperPanel")[0];
                    upperPanel.textContent = cost;
                    this.value = value;
                    object._console.log("value = " + value);
                    
                }
                
            }
            
            if (mode == 'updateTaxes' && key == 'value') {
                
                upperPanel = object.create('div', null, null, null, null, 'upperPanel', null);
                eventAction = function(event){
                    
                    var value = this.value;
                    var cost = object._format.formatCost(parseInt(value), object._currency);
                    var upperPanel = this.parentElement.getElementsByClassName("upperPanel")[0];
                    upperPanel.textContent = object._i18n.get("%s or %s", [cost, parseFloat(value) + '%']);
                    this.value = value;
                    object._console.log("value = " + value);
                    
                }
                
            }
            
            if (mode == 'updateTaxes' && key == 'type') {
                
                eventAction = function(event) {
                    
                    object._console.log(this);
                    var value = this.getAttribute("data-value");
                    var name = this.name;
                    object._console.log(inputData);
                    object._console.log("name = " + name);
                    object._console.log("value = " + value);
                    object._console.log(inputTypeList[name]);
                    if (value == 'tax') {
                        
                        for (var optionKey in inputTypeList[name].optionsList) {
                            
                            object._console.log(inputData[optionKey]);
                            if (inputData[optionKey] != null) {
                                
                                object._console.log(inputTypeList[optionKey]);
                                for (var key in inputData[optionKey]) {
                                    
                                    var disabled = true;
                                    if (parseInt(inputTypeList[name].optionsList[optionKey]) == 1) {
                                        
                                        disabled = false;
                                        
                                    }
                                    object._console.log("disabled = " + disabled);
                                    var elements = inputData[optionKey][key];
                                    object._console.log(elements);
                                    elements.disabled = disabled;
                                    
                                }
                                
                            }
                            
                        }
                        
                    } else if (value == 'surcharge') {
                        
                        for (var optionKey in inputTypeList[name].optionsList) {
                            
                            object._console.log(inputData[optionKey]);
                            if(inputData[optionKey] != null){
                                
                                object._console.log(inputTypeList[optionKey]);
                                for(var key in inputData[optionKey]){
                                    
                                    var disabled = false;
                                    if(parseInt(inputTypeList[name].optionsList[optionKey]) == 1){
                                        
                                        disabled = true;
                                        
                                    }
                                    object._console.log("disabled = " + disabled);
                                    var elements = inputData[optionKey][key];
                                    object._console.log(elements);
                                    elements.disabled = disabled;
                                    if (optionKey == 'tax') {
                                        
                                        if (key == 'tax_inclusive') {
                                            
                                            elements.checked = true;
                                            
                                        } else {
                                            
                                            elements.checked = false;
                                            
                                        }
                                        
                                    }
                                    
                                    if (optionKey == 'method') {
                                        
                                        if (key == 'addition') {
                                            
                                            elements.checked = true;
                                            
                                        } else {
                                            
                                            elements.checked = false;
                                            
                                        }
                                        
                                    }
                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                };
                
            }
            
            var th = object.create('th', object._i18n.get(inputTypeList[key].name), null, null, null, null, null);
            th.setAttribute("scope", "row");
            
            var disabled = false;
            if ( (mode === 'updateTax' || mode === 'updateExtraCharge') && parseInt(itemData.generation) === 1 && (key !== 'name' && key !== 'active') ) {
                
                disabled = true;
                
            }
            
            var inputPanel = object.createInput(key, data, inputData, account, disabled, eventAction, object._isExtensionsValid);
            if (upperPanel != null) {
                
                if (key == "cost") {
                    
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    upperPanel.textContent = cost;
                    inputPanel.insertAdjacentElement("afterbegin", upperPanel);
                    
                } else if (mode == 'updateTaxes' && key == 'value') {
                    
                    object._console.log(data);
                    var cost = object._format.formatCost(data.value, object._currency);
                    upperPanel.textContent = object._i18n.get("For addition, it is %s.", [cost]) + " \n" + object._i18n.get("For multiplication, it is %s percent.", [data.value]);
                    upperPanel.textContent = object._i18n.get("%s or %s", [cost, data.value + '%']);
                    inputPanel.insertAdjacentElement("beforeend", upperPanel);
                    
                }
                
            }
            
            if (mode == 'updateCoupons' && key == 'limited') {
                
                var target = inputData.target;
                var limited = inputData.limited;
                object._console.log(target);
                object._console.log(limited);
                if (target.visitors.checked === true) {
                    
                    limited.unlimited.checked = true;
                    limited.limited.disabled = true;
                    
                }
                
            }
            
            if (mode == 'updateTaxes' && key == 'tax') {
                
                var timer = setInterval(function(){
                    
                    object._console.log(inputData);
                    if(inputData.type.surcharge.checked == true) {
                        
                        for (var optionKey in inputData.tax) {
                            
                            if (optionKey == 'tax_inclusive') {
                                
                                inputData.tax[optionKey].checked = true;
                                
                            } else {
                                
                                inputData.tax[optionKey].checked = false;
                                
                            }
                            inputData.tax[optionKey].disabled = true;
                            
                        }
                        
                        for (var optionKey in inputData.method) {
                            
                            if (optionKey == 'addition') {
                                
                                inputData.method[optionKey].checked = true;
                                
                            } else {
                                
                                inputData.method[optionKey].checked = false;
                                
                            }
                            inputData.method[optionKey].disabled = true;
                            
                        }
                        
                    }
                    object._console.log(inputData);
                    clearInterval(timer);
                    
                }, 100);
                
               
            }
            
            if ((mode == object._prefix + 'updateCourse' || mode == 'updateCoupons') && key == 'expirationDate' && object._locale == 'ja') {
                
                object._console.log(inputPanel);
                var expirationDateFrom = inputPanel.getElementsByClassName('expirationDateFrom');
                var formTitleLabel = expirationDateFrom[0].getElementsByTagName('span')[0];
                formTitleLabel.classList.add('from-ja');
                expirationDateFrom[2].insertAdjacentElement('beforeend', formTitleLabel);
                var expirationDateTo = inputPanel.getElementsByClassName('expirationDateTo');
                var toTitleLabel = expirationDateTo[0].getElementsByTagName('span')[0];
                toTitleLabel.classList.add('to-ja');
                expirationDateTo[2].insertAdjacentElement('beforeend', toTitleLabel);
                
            }
            
            var td = object.create('td', null, [inputPanel], null, null, null, null);
            var tr = object.create('tr', null, [th, td], null, null, null, null);
            tr.setAttribute("valign", "top");
            trList[key] = tr;
            table.appendChild(tr);
            
            if ((mode == 'updateGuests') && data.target != 'both' && data.target != account.type) {
                
                tr.classList.add('hidden_panel');
                
            }
            
            if (mode == 'updateExtraCharge') {
                
                if (parseInt(itemData.generation) === 1) {
                    
                    
                    if (key == 'scope') {
                        
                        (function(inputPanel, list, value) {
                            
                            for (var key in list) {
                                
                                if (value === 'day') {
                                    
                                    inputPanel.textContent = object._i18n.get('Per day');
                                    
                                } else if (value === 'booking') {
                                    
                                    inputPanel.textContent = object._i18n.get('Per one booking');
                                    
                                } else {
                                    
                                    inputPanel.textContent = object._i18n.get('Per one booking for all guests');
                                    
                                }
                                if (key == value) {
                                    
                                    inputPanel.textContent = object._i18n.get(list[key]);
                                    
                                }
                                
                            }
                            
                        })(inputPanel, inputTypeList[key].valueList, itemData[key]);
                        
                    }
                    
                } else if (parseInt(itemData.generation) === 2 && account.type == 'day' && (key === 'scope' || key === 'target')) {
                    
                    tr.classList.add('hidden_panel');
                    
                }
                
            }
            
            if (mode == 'updateTax') {
                
                if (itemData.method == 'multiplication' && key == 'value') {
                    
                    th.textContent = object._i18n.get('Tax rate');
                    
                }
                
                if (parseInt(itemData.generation) === 2) {
                    
                    object._console.log(itemData);
                    object._console.log(itemData.method + ' ' + key);
                    if (key == 'method') {
                        
                        (function(inputPanel, list, value) {
                            
                            for (var key in list) {
                                
                                if (key == value) {
                                    
                                    inputPanel.textContent = object._i18n.get(list[key]);
                                    
                                }
                                
                            }
                            
                        })(inputPanel, inputTypeList[key].valueList, itemData[key]);
                        
                    }
                    
                    if (itemData.method == 'multiplication' && key == 'target') {
                        
                        tr.classList.add('hidden_panel');
                        
                    }
                    
                    if (itemData.method == 'addition' && key == 'tax') {
                        
                        tr.classList.add('hidden_panel');
                        
                    }
                    
                    if (key == 'scope') {
                        
                        tr.classList.add('hidden_panel');
                        
                    }
                    
                }
                
                
            }
            
            if (mode == 'updateOptionsForHotel') {
                
                if (key == 'target') {
                    
                    (function(inputPanel, list, value) {
                        
                        for (var key in list) {
                            
                            if (key == value) {
                                
                                inputPanel.textContent = object._i18n.get(list[key]);
                                
                            }
                            
                        }
                        
                    })(inputPanel, inputTypeList.target.valueList, itemData.target);
                    
                } else if (key == 'json') {
                    
                    var timer = setInterval(function(){
                        
                        var optionWithChargeAdult = document.getElementsByClassName('optionWithChargeAdult');
                        var optionWithChargeChild = document.getElementsByClassName('optionWithChargeChild');
                        var optionWithChargeRoom = document.getElementsByClassName('optionWithChargeRoom');
                        for (var targetKey in inputData.target) {
                            
                            object._console.log(targetKey);
                            var optionValue = inputData.target[targetKey].checked;
                            if (targetKey == 'guests' && optionValue === true) {
                                
                                object._console.log('disabled rooms');
                                for (var i = 0; i < optionWithChargeAdult.length; i++) {
                                    
                                    optionWithChargeAdult[i].disabled = false;
                                    
                                }
                                for (var i = 0; i < optionWithChargeChild.length; i++) {
                                    
                                    optionWithChargeChild[i].disabled = false;
                                    
                                }
                                for (var i = 0; i < optionWithChargeRoom.length; i++) {
                                    
                                    optionWithChargeRoom[i].disabled = true;
                                    
                                }
                                
                            } else if (targetKey == 'room' && optionValue === true) {
                                
                                object._console.log('disabled guests');
                                for (var i = 0; i < optionWithChargeAdult.length; i++) {
                                    
                                    optionWithChargeAdult[i].disabled = true;
                                    
                                }
                                for (var i = 0; i < optionWithChargeChild.length; i++) {
                                    
                                    optionWithChargeChild[i].disabled = true;
                                    
                                }
                                for (var i = 0; i < optionWithChargeRoom.length; i++) {
                                    
                                    optionWithChargeRoom[i].disabled = false;
                                    
                                }
                                
                            }
                            
                        }
                        
                        clearInterval(timer);
                        
                    }, 500);
                    
                }
                
            }
            
            if (inputTypeList[key].class != null) {
                
                tr.setAttribute("class", inputTypeList[key].class);
                
            }
            
            
        }
        
        if (inputData.id && inputData.id.textBox) {
            
            object._console.log(inputData.id);
            inputData.id.textBox.disabled = true;
            
        }
        
        if (mode == 'updateCoupons') {
            
            inputData.id.textBox.disabled = false;
            
        }
        
        if (inputData.subscription && inputData.subscription.textBox) {
            
            object._console.log(inputData.subscription);
            inputData.subscription.textBox.disabled = true;
            
        }
        
        addPanel.appendChild(table);
        
        var saveButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
        var cancelButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Cancel") );
        var buttonPanel = object.create('div', null, [saveButton, cancelButton], null, null, 'bottomButtonPanel', null);
        addPanel.appendChild(buttonPanel);
        if (object._isExtensionsValid === 0 && mode == 'updateOptionsForHotel') {
            
            saveButton.disabled = true;
            var extensionsValidPanel = object.create('div', object._i18n.get("Paid plan subscription required."), null, null, null, 'extensionsValid', null);
            buttonPanel.insertAdjacentElement("afterbegin", extensionsValidPanel);
            
        }
        
        var plusHeight = addPanel.clientHeight - columns[editKey].clientHeight;
        var height = addPanel.clientHeight;
        
        object._console.log(columns[editKey]);
        columns[editKey].setAttribute("class", "hidden_panel");
        columnsPanel.insertBefore(addPanel, columns[editKey]);
        
        if (mode == object._prefix + 'updateCourse') {
            
            var stopServicePanel = document.getElementById('setting_stopService');
            object._console.log(stopServicePanel);
            if (stopServicePanel != null) {
                
                object._console.log(stopServicePanel);
                var stopServiceUnderFollowingConditionsPanel = stopServicePanel.getElementsByClassName('stopServiceUnderFollowingConditions')[0];
                var targetRabel1 = stopServiceUnderFollowingConditionsPanel.getElementsByTagName('label')[1];
                var doNotStopServiceAsExceptionPanel = stopServicePanel.getElementsByClassName('doNotStopServiceAsException')[0];
                object._console.log(stopServiceUnderFollowingConditionsPanel);
                object._console.log(doNotStopServiceAsExceptionPanel);
                object._console.log(targetRabel1);
                targetRabel1.appendChild(doNotStopServiceAsExceptionPanel);
                
                var targetRabel2 = stopServiceUnderFollowingConditionsPanel.getElementsByTagName('label')[4];
                var stopServiceForDayOfTimesPanel = stopServicePanel.getElementsByClassName('stopServiceForDayOfTimes')[0];
                targetRabel2.appendChild(stopServiceForDayOfTimesPanel);
                var stopServiceForSpecifiedNumberOfTimesPanel = stopServicePanel.getElementsByClassName('stopServiceForSpecifiedNumberOfTimes')[0];
                targetRabel2.appendChild(stopServiceForSpecifiedNumberOfTimesPanel);
                
            }
            
        }
        
        var index = 0;
        for(var key in columns){
            
            if(key == editKey){
                break;
            }
            
        }
        
        object._console.log(columns);
        object._console.log("height = " + height);
        columnsPanel.style.height = height + "px";
        
        cancelButton.onclick = function(event){
        	
        	columnsPanel.removeChild(addPanel);
        	columns[key].setAttribute("class", "dnd_column ui-sortable-handle");
        	callback('cancel');
        	
        }
        
        saveButton.onclick = function(event){
        	
        	var response = null;
        	var postData = {mode: mode, nonce: object.nonce, action: object.action};
        	if(account != null){
        	    
        	    postData.accountKey = account.key;
        	    
        	}
        	
        	if(mode == object._prefix + 'updateCourse'){
        		
        		object._console.log(inputData);
        		postData['key'] = itemData.key;
        		response = object.getInputData(inputTypeList, inputData);
    			
        	} else if(mode == 'updateForm') {
                
                postData['key'] = editKey;
                response = object.getInputData(inputTypeList, inputData);
                response.id = itemData.id;
                object._console.log(response);
                if ((response.type == 'CHECK' || response.type == 'SELECT' || response.type == 'RADIO') && response.options.length == 0) {
                    
                    response.options = false;
                    
                }
                
            } else if(mode == "updateGuests") {
                
                postData['key'] = itemData.key;
                response = object.getInputData(inputTypeList, inputData);
                object._console.log("json = " + JSON.parse(response.json).length);
                object._console.log(response.json);
                if (JSON.parse(response.json).length == null || JSON.parse(response.json).length == 0) {
                    
                    response.json = false;
                    
                } else {
                    
                    var guests = JSON.parse(response.json);
                    object._console.log(guests);
                    for (var key in guests) {
                        
                        var guest = guests[key];
                        object._console.log(guest);
                        if (guest.price == null) {
                            
                            guest.price = 0;
                            
                        }
                        
                        if (guest.price.length == 0) {
                            
                            guest.price = 0;
                            
                        }
                        if (guest.number.length == 0 || guest.price.length == 0 || guest.name.length == 0) {
                            
                            response.json = false;
                            break;
                            
                        }
                    
                    }
                
                }
                
            } else if (mode == 'updateCoupons') {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                
            } else if (mode == "updateSubscriptions") {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                
            } else if (mode == "updateTaxes") {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                if (account.type == 'day') {
                    
                    response.scope = 'booking';
                    response.target = 'room';
                    
                }
                
            } else if (mode == "updateTax") {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                if (account.type == 'day') {
                    
                    response.scope = 'booking';
                    response.target = 'room';
                    
                }
                
            } else if (mode == "updateExtraCharge") {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                
            } else if (mode == "updateOptionsForHotel") {
                
                response = object.getInputData(inputTypeList, inputData);
                postData['key'] = itemData.key;
                
            }
            
            var post = true;
            for (var key in response) {
                
                if (typeof response[key] == 'boolean') {
                    
                    object._console.log("error key = " + key + " bool = " + response[key]);
                    if (trList[key] != null) {
                        
                        trList[key].classList.add("errorPanel");
                        
                    }
                    post = false;
                    
                } else {
                    
                    postData[key] = response[key];
                    if (trList[key] != null) {
                        
                        trList[key].classList.remove("errorPanel");
                        
                    }
                    
                }
                
            }
            
            if (post === true) {
                
                object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                object._console.log(itemData);
                object._console.log(postData);
                object.setFunction("addItem", post);
                object.xmlHttp = new Booking_App_XMLHttp(object.url, postData, object._webApp, function(json){
                    
                    if (json['status'] != 'error') {
                        
                        mainPanel.style.height = null;
                        object._console.log(json);
                        callback(json);
                        object.loadingPanel.setAttribute("class", "hidden_panel");
                        
                    } else {
                        
                        alert(json["message"]);
                        
                    }
                    			
                }, function(text){
                    
                    object.setResponseText(text);
                    
                });
                
            }
        	
        }
    	
    };
    
    SCHEDULE.prototype.copyItem = function(key, mode, account, callback){
        
        var object = this;
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {mode: mode, nonce: object.nonce, action: object.action, key: key, accountKey: account.key};
        object.setFunction("deleteItem", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
            
            if (json['status'] != 'error') {
                
                callback(json);
                
            }
            
            object.loadingPanel.setAttribute("class", "hidden_panel");
            
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
    };
        
    SCHEDULE.prototype.deleteItem = function(key, mode, account, callback){
        
        var object = this;
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var post = {mode: mode, nonce: object.nonce, action: object.action, key: key, accountKey: account.key};
        object.setFunction("deleteItem", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
            
            if(json['status'] != 'error'){
                
                callback(json);
                
            }
            
            object.loadingPanel.setAttribute("class", "hidden_panel");
            
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
    };

    SCHEDULE.prototype.sortData = function(key, className, list, panel, mode){
        
        var object = this;
        object._console.log(key);
        object._console.log(list);
        var sortBool = false;
        var panelList = panel.getElementsByClassName(className);
        for(var i = 0; i < list.length; i++){
            
            if (panelList[i] != null) {
                
                var index = parseInt(panelList[i].getAttribute("data-key"));
                if (i != index) {
                    
                    sortBool = true;
                    break;
                    
                }
                
            }
            
        }
        
        var keyList = [];
        var indexList = [];
        object._console.log(panelList);
        if (sortBool === true) {
            
            for (var i = 0; i < panelList.length; i++) {
            //for (var i in list) {
                
                object._console.log(list[i]);
                keyList.push(list[i][key]);
                if (panelList[i] != null) {
                    
                    var index = parseInt(panelList[i].getAttribute("data-key"));
                    indexList.push(index);
                    object._console.log(panelList[i]);
                    
                }
                
                
            }
            
        }
        
        object._console.log(keyList);
        object._console.log(indexList);
        return sortBool;
        
    };
    
    SCHEDULE.prototype.changeRank = function(key, className, list, panel, mode, account, callback){
        
        var object = this;
        object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
        var newList = [];
        var keyList = [];
        var indexList = [];
        
        var panelList = panel.getElementsByClassName(className);
        for(var i = 0; i < panelList.length; i++){
            
            var panelKey = parseInt(panelList[i].getAttribute("data-key"));
            newList.push(list[panelKey]);
            keyList.push(list[panelKey][key]);
            indexList.push(i);
            object._console.log(panelList[i]);
        
        }
        
        var post = {mode: mode, nonce: object.nonce, action: object.action, keyList: keyList.join(","), indexList: indexList.join(","), accountKey: account.key};
        object.setFunction("changeRank", post);
        object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json){
            
            callback(json);
            object.loadingPanel.setAttribute("class", "hidden_panel");
            
        }, function(text){
            
            object.setResponseText(text);
            
        });
        
        return newList;
        
    };
    
    SCHEDULE.prototype.emailSettingPanel = function(emailMessageList, formData, account){
        
        var object = this;
        object._console.log(account);
        object._console.log(emailMessageList);
        var mailSettingPanel = document.getElementById("mailSettingPanel");
        var content_area = document.getElementById("content_area");
        content_area.textContent = null;
        
        var table = document.createElement("table");
        table.setAttribute("class", "emails_table table_option wp-list-table widefat fixed striped")
        content_area.appendChild(table);
        
        var mail_message = {
            mailAdmin: "mail_new_admin",
            /**mailVisitor: "mail_new_visitor",**/ 
            mailApproved: "mail_approved", 
            mailPending: "mail_pending", 
            mailUpdated: "mail_updated", 
            mailReminder: "mail_reminder", 
            mailCanceled: "mail_canceled_by_visitor_user",
            mailDeleted: "mail_deleted",
        };
        
        var valueTdList = {};
        for (var id in mail_message) {
            
            if (mail_message[id] == null || emailMessageList[mail_message[id]] == null) {
                
                continue;
                
            }
            
            var mailMessageData = emailMessageList[mail_message[id]];
            object._console.log(mailMessageData);
            var nameTh = document.createElement("th");
            nameTh.textContent = object._i18n.get(mailMessageData.title);
            if (parseInt(mailMessageData.enable) === 0 && parseInt(mailMessageData.enableSMS) === 0) {
                
                nameTh.classList.add("disableTh");
                
            }
            
            var subjectLabel = object.create('div', null, null, null, null, 'subjectLabel', null);
            subjectLabel.innerHTML = "<strong>" + object._i18n.get("Subject") + "</strong><br>" + mailMessageData.subject.replace(/\\/g, "");
            var content = mailMessageData.content.replace(/\\/g, "");
            var contentLabel = document.createElement("div");
            contentLabel.innerHTML = "<strong>" + object._i18n.get("Content") + "</strong><br>" + content.replace(/\\/g, "");
            var valueTd = object.create('td', null, [subjectLabel, contentLabel], null, null, null, null);
            var tr = object.create('tr', null, [nameTh], id, null, null, null);
            table.appendChild(tr);
            valueTdList[id] = valueTd;
            tr.onclick = function(){
                
                var id = mail_message[this.id];
                var editer_id = "textarea";
                var mailMessageData = emailMessageList[id];
                var textarea = document.getElementById("emailContent");
                object._console.log("id = " + id);
                object._console.log(mailMessageData);
                
                var for_visitor = document.getElementById('for_visitor');
                var for_administrator = document.getElementById('for_administrator');
                var for_icalendar = document.getElementById('for_icalendar');
                var edit_visitor_message = document.getElementById('edit_visitor_message');
                var edit_administrator_message = document.getElementById('edit_administrator_message');
                var edit_icalendar_message = document.getElementById('edit_icalendar_message');
                var attachICalendar = document.getElementById('attachICalendar');
                attachICalendar.checked = false;
                if (id === 'mail_canceled_by_visitor_user' || id === 'mail_deleted') {
                    
                    attachICalendar.disabled = true;
                    for_icalendar.classList.add('hidden_panel');
                    edit_icalendar_message.classList.add('hidden_panel');
                    
                } else {
                    
                    attachICalendar.disabled = false;
                    for_icalendar.classList.remove('hidden_panel');
                    edit_icalendar_message.classList.remove('hidden_panel');
                    if (parseInt(mailMessageData.attachICalendar) === 1) {
                        
                        attachICalendar.checked = true;
                        
                    }
                    
                }
                
                
                for_visitor.classList.add('active');
                for_administrator.classList.remove('active');
                for_icalendar.classList.remove('active');
                edit_visitor_message.classList.remove('hidden_panel');
                edit_administrator_message.classList.add('hidden_panel');
                edit_icalendar_message.classList.add('hidden_panel');
                for_visitor.onclick = function() {
                    
                    for_visitor.classList.add('active');
                    for_administrator.classList.remove('active');
                    for_icalendar.classList.remove('active');
                    edit_visitor_message.classList.remove('hidden_panel');
                    edit_administrator_message.classList.add('hidden_panel');
                    edit_icalendar_message.classList.add('hidden_panel');
                    
                };
                
                for_administrator.onclick = function() {
                    
                    for_visitor.classList.remove('active');
                    for_administrator.classList.add('active');
                    for_icalendar.classList.remove('active');
                    edit_visitor_message.classList.add('hidden_panel');
                    edit_administrator_message.classList.remove('hidden_panel');
                    
                    edit_icalendar_message.classList.add('hidden_panel');
                    
                };
                
                for_icalendar.onclick = function() {
                    
                    for_visitor.classList.remove('active');
                    for_administrator.classList.remove('active');
                    for_icalendar.classList.add('active');
                    edit_visitor_message.classList.add('hidden_panel');
                    edit_administrator_message.classList.add('hidden_panel');
                    edit_icalendar_message.classList.remove('hidden_panel');
                    
                };
                
                var emailEdit = document.getElementById("email_edit_panel");
                emailEdit.classList.remove("hidden_panel");
                var media_frame_content = document.getElementById("media_frame_content_for_schedule");
                media_frame_content.classList.remove('hidden_panel');
                media_frame_content.textContent = null;
                document.getElementById("media_title_for_schedule").classList.add("media_left_zero");
                document.getElementById("media_router_for_schedule").classList.add("hidden_panel");
                document.getElementById("menu_panel_for_schedule").classList.add("hidden_panel");
                document.getElementById("frame_toolbar_for_schedule").setAttribute("class", "media_frame_toolbar media_left_zero");
                //media_frame_content.appendChild(emailEdit);
                
                document.getElementById("edit_title_for_schedule").textContent = object._i18n.get(mailMessageData.title);
                var enableEmailCheck = document.getElementById("mailEnable");
                enableEmailCheck.checked = false;
                if (parseInt(mailMessageData.enable) === 1) {
                    
                    enableEmailCheck.checked = true;
                    
                }
                
                var enableSmsCheck = document.getElementById("smsEnable");
                enableSmsCheck.checked = false;
                if (parseInt(mailMessageData.enableSMS) === 1) {
                    
                    enableSmsCheck.checked = true;
                    
                }
                
                document.getElementById("emailFormatHtml").checked = true;
                if (mailMessageData.format == "text") {
                    
                    document.getElementById("emailFormatText").checked = true;
                    
                }
                
                var notifyAdministratorCheck = document.getElementById('notifyAdministrator');
                notifyAdministratorCheck.checked = false;
                if (parseInt(mailMessageData.notifyAdministrator) === 1) {
                    
                    notifyAdministratorCheck.checked = true;
                    
                }
                
                var subject_filed = document.getElementById("subject");
                subject_filed.value = mailMessageData.subject.replace(/\\/g, "");
                
                var mail_message_area_left = document.getElementById("mail_message_area_left");
                
                var contentId = "emailContent";
                document.getElementById(contentId).value = mailMessageData.content.replace(/\\/g, "");
                object._console.log(mailMessageData.content);
                
                var subject_filed_for_admin = document.getElementById("subjectForAdmin");
                if (mailMessageData.subjectForAdmin != null) {
                    
                    subject_filed_for_admin.value = mailMessageData.subjectForAdmin.replace(/\\/g, "");
                    
                } else {
                    
                    subject_filed_for_admin.value = "";
                    
                }
                
                var contentIdForAdmin = "emailContentForAdmin";
                if (mailMessageData.contentForAdmin != null) {
                    
                    document.getElementById(contentIdForAdmin).value = mailMessageData.contentForAdmin.replace(/\\/g, "");
                    
                } else {
                    
                    document.getElementById(contentIdForAdmin).value = "";
                    
                }
                object._console.log(mailMessageData.contentForAdmin);
                var subjectForIcalendar = document.getElementById("subjectForIcalendar");
                if (mailMessageData.subjectForIcalendar != null) {
                    
                    subjectForIcalendar.value = mailMessageData.subjectForIcalendar.replace(/\\/g, "");
                    
                } else {
                    
                    subjectForIcalendar.value = "";
                    
                }
                
                var locationForIcalendar = document.getElementById("locationForIcalendar");
                if (mailMessageData.locationForIcalendar != null) {
                    
                    locationForIcalendar.value = mailMessageData.locationForIcalendar.replace(/\\/g, "");
                    
                } else {
                    
                    locationForIcalendar.value = "";
                    
                }
                
                var contentForIcalendar = "contentForIcalendar";
                if (mailMessageData.contentForIcalendar != null) {
                    
                    document.getElementById(contentForIcalendar).value = mailMessageData.contentForIcalendar.replace(/\\/g, "");
                    
                } else {
                    
                    document.getElementById(contentForIcalendar).value = "";
                    
                }
                object._console.log(mailMessageData.contentForIcalendar);
                
                if (textarea != null) {
                    
                    textarea.textContent = mailMessageData.content;
                    
                }
                
                var mail_message_area_right = document.getElementById("mail_message_area_right");
                mail_message_area_right.textContent = null;
                
                var mail_message_area_right_title = document.createElement("div");
                mail_message_area_right_title.setAttribute("class", "mail_message_area_right_title");
                mail_message_area_right_title.textContent = object._i18n.get("Help");
                mail_message_area_right.appendChild(mail_message_area_right_title);
                
                var str = object._i18n.get("You can use following shortcodes in content editer.");
                var help_message_label = document.createElement("div");
                help_message_label.setAttribute("class", "help_message_label");
                help_message_label.textContent = str;
                mail_message_area_right.appendChild(help_message_label);
                
                var shortcodes = {
                    id: object._i18n.get("ID"), 
                    /** date: object._i18n.get("Booking date"), **/
                    bookingDateAndTime: object._i18n.get("Booking date and time"),
                    bookingDate: object._i18n.get("Booking date"),
                    bookingTime: object._i18n.get("Booking time"),
                    bookingTitle: object._i18n.get("Booking title"),
                    services: object._i18n.get("Services"), 
                    servicesExcludedGuests: object._i18n.get("Services excluded guests"), 
                    servicesExcludedGuestsAndCosts: object._i18n.get("Services excluded guests and costs"), 
                    guests: object._i18n.get("Guests"), 
                    paymentMethod: object._i18n.get('Payment method'),
                    
                };
                
                if (object._servicesExcludedGuestsInEmail == 0) {
                    
                    delete shortcodes.servicesExcludedGuests;
                    delete shortcodes.servicesExcludedGuestsAndCosts;
                    
                }
                
                if (account.type == 'hotel') {
                    
                    shortcodes = {id: object._i18n.get("ID"), bookingDetails: object._i18n.get("Booking details"), checkIn: object._i18n.get("Arrival (Check-in)"), checkOut: object._i18n.get("Departure (Check-out)"), options: object._i18n.get("Options"), guests: object._i18n.get("Guests"), paymentMethod: object._i18n.get('Payment method')};
                    
                }
                
                shortcodes.submissionDate = object._i18n.get('Submission date');
                shortcodes.surcharges = object._i18n.get('Surcharges');
                shortcodes.taxes = object._i18n.get('Taxes');
                //shortcodes.totalPaymentAmount = object._i18n.get('Total payment amount');
                shortcodes.totalAmount = object._i18n.get('Total amount');
                shortcodes.couponCode = object._i18n.get('Coupon code');
                shortcodes.couponName = object._i18n.get('Coupon name');
                shortcodes.couponDiscount = object._i18n.get('Discount');
                //shortcodes.site_name = object._i18n.get('Your site name');
                shortcodes.bookingCancellationUrl = object._i18n.get('Cancellation URL');
                shortcodes.receivedUrl = object._i18n.get('Received URL');
                shortcodes.customerDetails = object._i18n.get("Customer details");
                shortcodes.customerDetailsUrl = object._i18n.get("URL of customer details for administrator");
                
                for (var key in shortcodes) {
                    
                    
                    var codeLabel = object.create('div', object._i18n.get("[%s] is inserting \"%s\"", [key, shortcodes[key]]), null, null, null, null, null);
                    var formFiledPanel = object.create('div', null, [codeLabel], null, null, 'formFiledPanel', null);
                    mail_message_area_right.appendChild(formFiledPanel);
                    
                }
                
                for (var i = 0; i < formData.length; i++) {
                    
                    var filedData = formData[i];
                    if (filedData.active != 'true') {
                        
                        continue;
                        
                    }
                    
                    var codeLabel = object.create('div', object._i18n.get("[%s] is inserting \"%s\"", [filedData.id, filedData.name]), null, null, null, null, null);
                    var formFiledPanel = object.create('div', null, [codeLabel], null, null, 'formFiledPanel', null);
                    mail_message_area_right.appendChild(formFiledPanel);
                    
                }
                
                var editPanel = document.getElementById("editPanelForSchedule");
                var mail_message_save_button = document.createElement("button");
                mail_message_save_button.setAttribute("class", "button media-button button-primary button-large media-button-insert");
                mail_message_save_button.textContent = object._i18n.get("Save");
                
                document.getElementById('incompletelyDeletedScheduleAlertPanel').classList.add('hidden_panel');
                
                var buttonPanel = document.getElementById("buttonPanel_for_schedule");
                buttonPanel.textContent = null;
                buttonPanel.appendChild(mail_message_save_button);
                
                object.editPanelShow(true);
                mail_message_save_button.onclick = function() {
                    
                    var value = document.getElementById(contentId).value;
                    object._console.log(value);
                    var valueForAdmin = document.getElementById(contentIdForAdmin).value;
                    object._console.log(valueForAdmin);
                    var valueForIcalendar = document.getElementById(contentForIcalendar).value;
                    object._console.log(valueForIcalendar);
                    
                    var enableEmail = 0;
                    if (enableEmailCheck.checked === true) {
                        
                        enableEmail = 1;
                        
                    }
                    
                    var enableSms = 0;
                    if (enableSmsCheck.checked === true) {
                        
                        enableSms = 1;
                        
                    }
                    
                    var format = "html";
                    if (document.getElementById("emailFormatText").checked === true) {
                        
                        format = "text";
                        
                    }
                    
                    var notifyAdministrator = 0;
                    if (notifyAdministratorCheck.checked === true) {
                        
                        notifyAdministrator = 1;
                        
                    }
                    
                    var attachICalendarValue = 0;
                    if (document.getElementById("attachICalendar").checked === true) {
                        
                        attachICalendarValue = 1;
                        
                    }

                    let attachCancellationURL = false;
                    var content = [value, valueForAdmin, contentForIcalendar];
                    for (var i = 0; i < content.length; i++) {
                        
                        attachCancellationURL = content[i].includes("[bookingCancellationUrl]");
                        if (attachCancellationURL === true) {
                            
                            if (parseInt(account.cancellationOfBooking) === 1) {
                                
                                attachCancellationURL = false;
                                
                            }
                            break;
                            
                        }
                        
                    }
                    
                    let skipAlertPanel = true;
                    if (attachCancellationURL === true) {
                        
                        skipAlertPanel = false;
                        
                    }
                    
                    let message = object._i18n.get('The cancellation URL shortcode has been inserted into the text area.') + "\n";
                    message += object._i18n.get('Please enable the "%s" item in the "Settings" tab.', [ object._i18n.get('Cancel a booking by your customer') ] );
                    var confirm = new Confirm(object._debug);
                    confirm.alertPanelShow(object._i18n.get("Attention"), message, skipAlertPanel, function(bool) {
                        
                        var slipRemainder = true;
                        let message = object._i18n.get('Please enable the "%s" item in the "Settings" tab.', [ object._i18n.get('Booking reminder') ] );
                        if (mailMessageData.key === 'mail_reminder' && object._isExtensionsValid === 0 && (enableEmail === 1 || enableSms === 1)) {
                            
                            slipRemainder = false;
                            object._console.log(slipRemainder);
                            
                        }
                        
                        confirm.alertPanelShow(object._i18n.get("Attention"), message, slipRemainder, function(bool) {
                            
                            var post = {mode: "updataEmailMessageForCalendarAccount", nonce: object.nonce, action: object.action, mail_id: mailMessageData.key, subjectForIcalendar: subjectForIcalendar.value, locationForIcalendar: locationForIcalendar.value, contentForIcalendar: valueForIcalendar, subject: subject_filed.value, content: value, subjectForAdmin: subject_filed_for_admin.value, contentForAdmin: valueForAdmin, enableEmail: enableEmail, enableSms: enableSms, format: format, notifyAdministrator: notifyAdministrator, attachICalendar: attachICalendarValue, accountKey: account.key};
                            object.loadingPanel.setAttribute("class", "loading_modal_backdrop");
                            object._console.log(post);
                            object.setFunction("emailSettingPanel", post);
                            object.xmlHttp = new Booking_App_XMLHttp(object.url, post, object._webApp, function(json) {
                                
                                object.emailSettingPanel(json.emailMessageList, json.formData, account);
                                object.loadingPanel.setAttribute("class", "hidden_panel");
                                
                            }, function(text) {
                                
                                object.setResponseText(text);
                                
                            });
                            
                        });
                        
                    });
                    
                    
                }
                
            }
            
        }
        
    };


    SCHEDULE.prototype.getInputData = function(inputTypeList, inputData){
        
        var object = this;
        var postData = {};
        object._console.log(inputData);
        for(var key in inputTypeList){
            
            object._console.log(key);
            var values = [];
            var inputType = inputTypeList[key];
            object._console.log(inputType);
            for(var inputKey in inputData[key]){
                
                object._console.log(inputKey);
                object._console.log(inputData[key][inputKey]);
                var bool = true;
                if (inputType['inputType'] == 'TEXT' || inputType['inputType'] == 'TEXTAREA') {
                    
                    if (inputData[key][inputKey].value.length == 0 && inputType.inputLimit == '1') {
                        
                        bool = false;
                        
                    } else {
                        
                        values.push(inputData[key][inputKey].value);
                        
                    }
                    
                } else if (inputType['inputType'] == 'CHECK' || inputType['inputType'] == 'RADIO') {
                    
                    if (inputData[key][inputKey].checked == true) {
                        
                        values.push(inputData[key][inputKey].getAttribute("data-value"));
                        
                    }
                    
                } else if (inputType['inputType'] == 'OPTION') {
                    
                    object._console.log(inputData[key][inputKey]);
                    values.push(inputData[key][inputKey].value);
                    
                } else if (inputType['inputType'] == 'EXTRA') {
                    
                    object._console.log(inputData[key][inputKey]);
                    if (key == 'options') {
                        
                        var option = JSON.parse(inputData[key][inputKey].json);
                        object._console.log(option);
                        if (option.name != null && option.name.length > 0) {
                            
                            values.push(option);
                            
                        }
                        
                    } else {
                        
                        values.push(JSON.parse(inputData[key][inputKey].json));
                        
                    }
                    
                } else if (inputType['inputType'] == 'REMAINING_CAPACITY') {
                    
                    values.push(JSON.stringify(inputData[key][inputKey]));
                    
                } else if (inputType['inputType'] == 'SELECT' || inputType['inputType'] == 'SELECT_TIMEZONE') {
                    
                    var index = inputData[key][inputKey].selectedIndex;
                    values.push(inputData[key][inputKey].options[index].value);
                    
                } else if (inputType['inputType'] == 'FIX_CALENDAR') {
                    
                    object._console.log(inputData['monthForFixCalendar']);
                    var indexMonth = inputData['monthForFixCalendar'].selectedIndex;
                    var valueMonth = inputData['monthForFixCalendar'].options[indexMonth].value;
                    values.push(valueMonth);
                    postData['monthForFixCalendar'] = valueMonth;
                    
                    var indexYear = inputData['yearForFixCalendar'].selectedIndex;
                    var valueYear = inputData['yearForFixCalendar'].options[indexYear].value;
                    values.push(valueYear);
                    postData['yearForFixCalendar'] = valueYear;
                    
                    var enableFixCalendar = 0;
                    if (inputData['enableFixCalendar'].checked == true) {
                        
                        enableFixCalendar = 1;
                        
                    }
                    postData['enableFixCalendar'] = enableFixCalendar;
                    values.push(enableFixCalendar);
                    
                } else if (inputType['inputType'] == 'HOTEL_CHARGES') {
                    
                    object._console.log(key);
                    object._console.log(inputKey);
                    object._console.log(inputType);
                    if (key == 'hotelCharges') {
                        
                        if (inputData[key][inputKey].type.toLowerCase() == 'text') {
                            
                            object._console.log(inputData[key][inputKey].value);
                            values.push(inputData[key][inputKey].value);
                            postData[inputKey] = inputData[key][inputKey].value;
                            
                        }
                        
                    }
                    
                    
                } else if (inputType['inputType'] == 'MULTIPLE_FIELDS') {
                    
                    object._console.log('MULTIPLE_FIELDS');
                    object._console.log(key);
                    object._console.log(inputKey);
                    object._console.log(inputType);
                    var field = inputData[key][inputKey];
                    var inputObjects = field.inputObjects;
                    object._console.log(field);
                    object._console.log(inputObjects);
                    if (field.inputType == 'CHECK' || field.inputType == 'RADIO') {
                        
                        var checkValues = [];
                        for (var objectKey in inputObjects) {
                            
                            var inputObject = inputObjects[objectKey];
                            if (inputObject.checked === true) {
                                
                                checkValues.push(inputObject.getAttribute("data-value"));
                                
                            }
                            
                        }
                        
                        postData[field.key] = checkValues.join(',');
                        
                    } else if (field.inputType == 'SELECT') {
                        
                        var inputObject = inputObjects[0];
                        var index = inputObject.selectedIndex;
                        object._console.log(inputObject)
                        object._console.log(index);
                        if (inputObject.options[index] != null) {
                            
                            object._console.log(inputObject.options[index].value);
                            postData[field.key] = inputObject.options[index].value;
                            
                        }
                        
                    } else if (field.inputType == 'TEXT') {
                        
                        var inputObject = inputObjects[0];
                        postData[field.key] = inputObject.value;
                        
                    } else if (field.inputType == 'REMAINING_CAPACITY') {
                        
                        var inputObject = inputObjects[0];
                        postData[field.key] = JSON.stringify(inputObject);
                        
                    } else if (field.inputType == 'COLOR') {
                        
                        postData[field.key] = field.value;
                        
                    }
                    
                } else if (inputType['inputType'] == 'SUBSCRIPTION') {
                    
                    for (var optionKey in inputType.optionKeys) {
                        
                        var option = inputType.optionKeys[optionKey];
                        if (option.inputType == "TEXT") {
                            
                            object._console.log(inputData[optionKey].textBox.value);
                            values = inputData[optionKey].textBox.value;
                            
                        } else if (option.inputType == "CHECKBOX") {
                            
                            var enable = 0;
                            if (inputData[optionKey].checked == true) {
                                
                                enable = 1;
                                
                            }
                            postData[optionKey] = enable;
                            
                        }
                        
                    }
                    
                    /**
                    object._console.log(inputData["subscriptionIdForStripe"].textBox.value);
                    
                    var enableSubscriptionForStripe = 0;
                    if(inputData['enableSubscriptionForStripe'].checked == true){
                        
                        enableSubscriptionForStripe = 1;
                        
                    }
                    values = inputData["subscriptionIdForStripe"].textBox.value;
                    postData['enableSubscriptionForStripe'] = enableSubscriptionForStripe;
                    **/
                    
                } else if (inputType['inputType'] == 'TIME_TO_PROVIDE' || inputType['inputType'] == 'WEEK_TO_PROVIDE') {
                    
                    values.push(JSON.stringify(inputData[key][inputKey]));
                    
                }
                
            }
            
            if (bool === true) {
                
                if (inputType['inputType'] == 'EXTRA' || (inputType['inputType'] == 'OPTION' && inputType.format == 'jsonString')) {
                    
                    postData[key] = JSON.stringify(values);
                    
                } else {
                    
                    object._console.log(typeof values);
                    object._console.log(values);
                    postData[key] = values
                    if (typeof values == "object") {
                        
                        postData[key] = values.join(",");
                        
                    }
                    
                }
                
            } else {
                
                postData[key] = false;
                
            }
            
            object._console.log(inputData[key]);
            
        }
        
        return postData;
        
    };
    
    SCHEDULE.prototype.reviewPanels = function(panelList) {
        
        for (var i = 0; i < panelList.length; i++) {
			
			panelList[i].setAttribute("data-key", i);
			var optionBox = panelList[i].getElementsByClassName("dnd_optionBox")[0];
			(function(optionBox, key) {
			    
			    var panel = optionBox.getElementsByTagName("label");
			    for (var i = 0; i < panel.length; i++) {
			        
			        panel[i].setAttribute("data-key", key);
			        
			    }
			    
			    
			})(optionBox, i);
			
		}
        
    };
    
    SCHEDULE.prototype.insertElementsBeforeAndAfterInput = function(input, valuePanel, beforeText, afterText) {
        
        var object = this;
        let text = '';
        if (input.insertBeforeElement != null) {
            
            if (beforeText !== null) {
                
                text = object._i18n.get(input.insertBeforeElement, [beforeText]);
                
            } else {
                
                text = object._i18n.get(input.insertBeforeElement, ['']);
                
            }
            
            let beforeElement = object.create('span', text, null, null, null, 'insertBeforeElement', null);
            valuePanel.insertAdjacentElement('beforebegin', beforeElement);
            
        }
        
        if (input.insertAfterElement != null) {
            
            if (afterText !== null) {
                
                text = object._i18n.get(input.insertAfterElement, [afterText]);
                
            } else {
                
                text = object._i18n.get(input.insertAfterElement, ['']);
                
            }
            
            let afterElement = object.create('span', text, null, null, null, 'insertAfterElement', null);
            valuePanel.insertAdjacentElement('afterend', afterElement);
            
        }
        
    };

    SCHEDULE.prototype.createInput = function(inputName, input, inputData, account, disabled, eventAction, isExtensionsValid){
	    
	    var object = this;
	    
        object._console.log("createInput");
        object._console.log(account);
        object._console.log("isExtensionsValid = " + isExtensionsValid);
        object._console.log('disabled = ' + disabled);
        object._console.log(input);
        var list = null;
        if (input['valueList'] != null) {
            
            list = input['valueList'];
            
        }
        
        if (parseInt(input.isExtensionsValid) == 1 && isExtensionsValid == 0) {
            
            disabled = true;
            object._console.log('disabled = ' + disabled);
            
        }
        object._console.log('disabled = ' + disabled);
        object._console.log(list);
        var valuePanel = object.create('div', null, null, null, null, 'valuePanel', null);
        if (input['inputType'] == 'TEXT') {
            
            var textBox = document.createElement("input");
            textBox.setAttribute("class", "regular-text");
            textBox.type = "text";
            textBox.disabled = disabled;
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
            
            valuePanel.id = 'setting_' + input.key;
            valuePanel.appendChild(textBox);
            object.insertElementsBeforeAndAfterInput(input, textBox, null, null);
            inputData[inputName] = {textBox: textBox};
            
        } else if (input['inputType'] == 'SELECT') {
            
            var selectBox = document.createElement("select");
            selectBox.disabled = disabled;
            for (var key in list) {
                
                var optionBox = document.createElement("option");
                optionBox.value = key;
                optionBox.textContent = list[key];
                
                object._console.log("key = " + key + " content = " + list[key]);
                if (key == input['value']) {
                    
                    object._console.log("value = " + input['value']);
                    optionBox.selected = true;
                    
                }
                
                selectBox.appendChild(optionBox);
                
            }
            
            valuePanel.appendChild(selectBox);
            object.insertElementsBeforeAndAfterInput(input, selectBox, null, null);
            inputData[inputName] = {selectBox: selectBox};
            
        } else if (input['inputType'] == 'SELECT_GROUP') {
            
            var selectBox = document.createElement("select");
            selectBox.disabled = disabled;
            var selectedvalue = null;
            for (var key in list) {
                
                if (list[key]['alpha-2'] == input['value']) {
                    
                    selectedvalue = key;
                    break;
                    
                }
                
            }
            
            if (selectedvalue == null) {
                
                selectedvalue = "United States of America";
                
            }
            
            var selectedCountry = list[selectedvalue];
            object._console.log(selectedCountry)
            
            var optionBox = document.createElement("option");
            optionBox.value = selectedCountry['alpha-2'];
            optionBox.textContent = selectedCountry.name;
            
            var optgroup = document.createElement("optgroup");
            optgroup.setAttribute("label", object._i18n.get("Selected country"));
            optgroup.appendChild(optionBox);
            selectBox.appendChild(optgroup);
            
            var frequently = ["Canada", "France", "Germany", "Italy", "Japan", "United Kingdom of Great Britain and Northern Ireland", "United States of America"];
            
            var optgroup = document.createElement("optgroup");
            optgroup.setAttribute("label", object._i18n.get("Frequently used countries"));
            selectBox.appendChild(optgroup);
            
            object._console.log("selectedvalue = " + selectedvalue);
            for (var i = 0; i < frequently.length; i++) {
                
                var key = frequently[i];
                if (list[key].name != selectedvalue) {
                    
                    var optionBox = document.createElement("option");
                    optionBox.value = list[key]['alpha-2'];
                    optionBox.textContent = list[key].name;
                    optgroup.appendChild(optionBox);
                    
                }
                
            }
            
            var optgroup = document.createElement("optgroup");
            optgroup.setAttribute("label", object._i18n.get("Other countries"));
            selectBox.appendChild(optgroup);
            
            for (var key in list) {
                
                var optionBox = document.createElement("option");
                optionBox.value = list[key]['alpha-2'];
                optionBox.textContent = list[key].name;
                
                if (key == input['value']) {
                    
                    object._console.log("value = " + input['value']);
                    optionBox.selected = true;
                    
                }
                
                optgroup.appendChild(optionBox);
                
            }
            
            valuePanel.appendChild(selectBox);
            inputData[inputName] = {selectBox: selectBox};
            
        } else if (input['inputType'] == 'SELECT_TIMEZONE') {
            
            var timezoneGroup = object._timezoneGroup;
			var options = [];
			var timezoneSelect = document.createElement("select");
			for (var i = 0; i < timezoneGroup.length; i++) {
				
				var group = timezoneGroup[i];
				var optionsInGroup = group.getElementsByTagName("option");
				optionsInGroup = [].slice.call(optionsInGroup);
				options = options.concat(optionsInGroup);
				
				timezoneSelect.appendChild(group);
				
			}
			
			for (var i = 0; i < options.length; i++) {
				
				var option = options[i];
				if (option.value == input.value) {
					
					option.selected = true;
					break;
					
				} else {
					
					option.selected = false;
					
				}
				
			}
            
            valuePanel.appendChild(timezoneSelect);
            object.insertElementsBeforeAndAfterInput(input, timezoneSelect, null, null);
			inputData[inputName] = {selectBox: timezoneSelect};
            
        } else if (input['inputType'] == 'OPTION') {
            
            var options = {};
            if (input.format == null && input.value != null) {
                
                options = input.value.split(",");
                
            } else if (input.format == 'jsonString' && input.value != null && typeof input.value.split == 'function') {
                
                options = input.value.split(",");
                
            } else if (input.format == 'jsonString' && input.value != null && typeof input.value == 'object') {
                
                options = input.value;
                
            }
            
            object._console.log(options);
            
            var addButton = object.create('div', object._i18n.get("Add"), null, null, null, 'addLink', null);
            var table = object.create('table', null, null, null, null, 'table_option wp-list-table widefat fixed striped', null);
            valuePanel.appendChild(addButton);
            valuePanel.appendChild(table);
            inputData[inputName] = {};
            var tr_index = 0;
            var table_tr = {};
            
            for (var i = 0; i < options.length; i++) {
                
                create_tr(tr_index, table, input, account, options[i]);
                tr_index++;
                
            }
            
            addButton.onclick = function(){
                
                create_tr(tr_index, table, input, account, "");
                tr_index++;
                
            }
            
        } else if (input['inputType'] == 'EXTRA') {
		    
		    object._console.log(account);
            var options = [];
            if (input['value'] != null && input['value'].length > 0) {
                
                options = JSON.parse(input['value']);
                
            }
            
            object._console.log(options);
            var addButton = object.create('div', object._i18n.get("Add"), null, null, null, 'addLink', null);
            valuePanel.appendChild(addButton);
            if (object._isExtensionsValid == 0 && input.isExtensionsValidPanel != null && parseInt(input.isExtensionsValidPanel) == 0) {
                
                var extensionsValidPanel = document.createElement("div");
                extensionsValidPanel.classList.add("freePlan");
                extensionsValidPanel.textContent = object._i18n.get('Paid plan subscription required.', [object._i18n.get(input.name)]);
                valuePanel.appendChild(extensionsValidPanel);
                
            }
            
            var table = object.create('table', null, null, null, null, 'table_option wp-list-table widefat fixed striped', null);
            valuePanel.appendChild(table);
            
            var titleList = input.titleList;
            var optionsType = input.optionsType;
            object._console.log(optionsType);
            var tr = object.create('tr', null, null, null, null, 'tr_option', null);
            table.appendChild(tr);
            for (var key in titleList) {
                
                object._console.log(key);
                object._console.log(optionsType[key]);
                var td = object.create('td', titleList[key], null, null, null, 'td_option', null);
                tr.appendChild(td);
                if (optionsType[key].target != 'both' && optionsType[key].target != account.type) {
                    
                    td.classList.add('hidden_panel');
                    
                }
                
            }
            
            var td = object.create('td', null, null, null, null, 'td_delete td_option', null);
            tr.appendChild(td);
            inputData[inputName] = {};
            var tr_index = 0;
            var table_tr = {};
            object._console.log(options);
            for (var i = 0; i < options.length; i++) {
                
                create_tr(tr_index, table, input, account, options[i]);
                tr_index++;
                
            }
            
            addButton.onclick = function() {
                
                var titleList = {};
                for (var key in input.titleList) {
                    
                    titleList[key] = "";
                    
                }
                
                create_tr(tr_index, table, input, account, titleList);
                tr_index++;
                
                object._console.log(input);
                if (input.actions != null && input.actions && typeof input.actions == 'function') {
                    
                    input.actions();
                    
                }
                
                object._console.log(tr_index);
                
            };
            
        } else if (input['inputType'] == 'CHECK') {
            
            inputData[inputName] = {};
            for (var key in list) {
                
                object._console.log("key = " + key + " value = " + list[key])
                var valueName = object.create('span', object._i18n.get(list[key]), null, null, null, 'radio_title', null);
                var checkBox = document.createElement("input");
                checkBox.disabled = disabled;
                checkBox.setAttribute("data-value", key);
                checkBox.name = inputName;
                checkBox.type = "checkbox";
                checkBox.value = list[key];
                if (input['value'] == key) {
                    
                    checkBox.checked = "checked";
                    
                }
                
                if (input.value != null) {
                    
                    var checkValues = input.value.split(",");
                    if (checkValues.length > 1) {
                        
                        for (var i = 0; i < checkValues.length; i++) {
                            
                            if (checkValues[i] == key) {
                                
                                checkBox.checked = "checked";
                                break;
                                
                            }
                            
                        }
                        
                    }
                    
                }
                
                var label = object.create('label', null, [checkBox, valueName], null, null, null, null);
                valuePanel.appendChild(label);
                inputData[inputName][key] = checkBox;
                
            }
            
        } else if (input['inputType'] == 'RADIO') {
            
            inputData[inputName] = {};
            for (var key in list) {
                
                object._console.log(key + " = " + list[key]);
                var valueName = object.create('span', object._i18n.get(list[key]), null, null, null, 'radio_title', null);
                var radioBox = document.createElement("input");
                radioBox.disabled = disabled;
                radioBox.setAttribute("data-value", key);
                radioBox.name = inputName;
                radioBox.type = "radio";
                radioBox.value = list[key];
                if (input['value'] == key) {
                    
                    object._console.log("value = " + input['value']);
                    radioBox.checked = "checked";
                    
                }
                
                if (input.valueClasses != null && input.valueClasses[key] != null) {
                    
                    radioBox.classList.add(input.valueClasses[key]);
                    
                }
                
                if (eventAction != null) {
                    
                    radioBox.onchange = eventAction;
                    
                } else if (input.actions != null && input.actions[key] && typeof input.actions[key] == 'function') {
                    
                    object._console.log(typeof input.actions[key]);
                    radioBox.onchange = input.actions[key];
                    
                }
                
                var label = object.create('label', null, [radioBox, valueName], null, null, null, null);
                valuePanel.appendChild(label);
                inputData[inputName][key] = radioBox;
                
            }
            
        } else if (input['inputType'] == 'TEXTAREA') {
            
            var textareaBox = document.createElement("textarea");
            if (input['value'] != null && typeof input['value'].replace == 'function') {
                
                textareaBox.value = input['value'].replace(/\\/g, "");
                
            }
            
            textareaBox.disabled = disabled;
            valuePanel.appendChild(textareaBox);
            inputData[inputName] = {textBox: textareaBox};
        
        } else if (input['inputType'] == 'REMAINING_CAPACITY') {
            
            var value = {json: {symbol: "", color: "#969696"}};
            object._console.log(input['value']);
            object._console.log(object.isJSON(input['value']));
            if (object.isJSON(input['value']) == true) {
                
                value.json = JSON.parse(input['value']);
                
            }
            
            var symbol = document.createElement("input");
            symbol.type = "text";
            symbol.setAttribute("data-key", input.key);
            symbol.value = value.json.symbol;
            var tdSymbol = object.create('td', null, [symbol], null, null, null, null);
            var color = document.createElement("input");
            color.type = "text";
            color.id = input.key + "_color";
            color.setAttribute("data-key", input.key);
            color.value = value.json.color;
            var tdColor = object.create('td', null, [color], null, null, null, null);
            var tr = object.create('tr', null, [tdSymbol, tdColor], null, null, null, null);
            var table = object.create('table', null, [tr], null, null, null, null);
            valuePanel.appendChild(table);
            symbol.onchange = function() {
                
                var key = this.getAttribute("data-key");
                var value = this.value;
                if (typeof inputData[key].json != 'object') {
                    
                    inputData[key].json = {};
                    
                }
                object._console.log(value);
                inputData[key].json["symbol"] = value;
                
            };
            
            var timer = setInterval(function(){
                
                (function( $ ) {
                    
                    $(function() {
                        
                        $('#' + input.key + "_color").wpColorPicker({
                            defaultColor: false,
                            change: function(event, ui){
                                
                                var key = this.getAttribute("data-key");
                                var color = "#" + ui.color._color.toString(16);
                                if (typeof inputData[key].json != 'object') {
                                    
                                    inputData[key].json = {};
                                    
                                }
                                inputData[key].json.color = color;
                            },
                            clear: function(){
                            
                            }
                            
                        });
                        
                    });
                    
                })( jQuery );
                
                clearInterval(timer);
            
            }, 300);
            
            inputData[inputName] = value;
            
        } else if (input['inputType'] == 'FIX_CALENDAR') {
            
            object._console.log(input.value);
            inputData[inputName] = {FIX_CALENDAR: null};
            var monthSelectBox = document.createElement("select");
            monthSelectBox.setAttribute("style", "margin-right: 1em;");
            var monthFullName = [object._i18n.get('January'), object._i18n.get('February'), object._i18n.get('March'), object._i18n.get('April'), object._i18n.get('May'), object._i18n.get('June'), object._i18n.get('July'), object._i18n.get('August'), object._i18n.get('September'), object._i18n.get('October'), object._i18n.get('November'), object._i18n.get('December')];
            for (var i = 0; i < monthFullName.length; i++) {
                
                var optionBox = document.createElement("option");
                optionBox.value = i;
                optionBox.textContent = monthFullName[i];
                monthSelectBox.appendChild(optionBox);
                
            }
            
            if (input.value.monthForFixCalendar != null && monthSelectBox.options[parseInt(input.value.monthForFixCalendar)] != null) {
                
                monthSelectBox.options[parseInt(input.value.monthForFixCalendar)].selected = true;
                
            }
            
            valuePanel.appendChild(monthSelectBox);
            inputData['monthForFixCalendar'] = monthSelectBox;
            
            var year = parseInt(new Date().getFullYear());
            var maxYear = year + 2;
            var selected = false;
            var yearSelectBox = document.createElement("select");
            yearSelectBox.setAttribute("style", "margin-right: 1em;");
            for (var i = 0; i < 3; i++) {
                
                var valueYear = year + i;
                var optionBox = document.createElement("option");
                optionBox.value = valueYear;
                optionBox.textContent = valueYear;
                if (input.value.yearForFixCalendar != null && parseInt(input.value.yearForFixCalendar) == valueYear) {
                    
                    optionBox.selected = true;
                    selected = true;
                    
                }
                yearSelectBox.appendChild(optionBox);
                
            }
            
            if (selected == false && input.value.length > 0 && parseInt(input.value.yearForFixCalendar) != 0) {
                
                var optionBox = document.createElement("option");
                optionBox.value = input.value.yearForFixCalendar;
                optionBox.textContent = input.value.yearForFixCalendar;
                optionBox.selected = true;
                yearSelectBox.insertAdjacentElement('afterbegin', optionBox);
                
            }
            
            valuePanel.appendChild(yearSelectBox);
            inputData['yearForFixCalendar'] = yearSelectBox;
            var valueName = object.create('span', object._i18n.get("Enable"), null, null, null, 'radio_title', null);
            var checkBox = document.createElement("input");
            checkBox.name = inputName;
            checkBox.type = "checkbox";
            checkBox.value = 1;
            if (input.value.enableFixCalendar != null && parseInt(input.value.enableFixCalendar) == 1) {
                
                checkBox.checked = true;
                
            }
            var label = object.create('label', null, [checkBox, valueName], null, 'display: inline;', null, null);
            valuePanel.appendChild(label);
            inputData['enableFixCalendar'] = checkBox;
            
        } else if (input['inputType'] == "HOTEL_CHARGES") {
            
            object._console.log(input);
            object._console.log(input.value);
            valuePanel.id = 'setting_' + input.key;
            inputData[input.key] = {};
            var charges = input.valueList;
            for (var i = 0; i < charges.length; i++) {
                
                var charge = charges[i];
                object._console.log(charge);
                var label = object.create('label', charge.name + ': ', null, null, null, 'chargeLabel', null);
                valuePanel.appendChild(label);
                var cost = object._format.formatCost(charge.value, object._currency);
                var costPanel = object.create('label', cost, null, 'costPanel_' + charge.key, null, 'chargeLabel', null);
                valuePanel.appendChild(costPanel);
                var textBox = document.createElement("input");
                textBox.id = 'textBox_' + charge.key;
                textBox.setAttribute('data-key', charge.key);
                textBox.setAttribute("class", "regular-text");
                textBox.type = "text";
                textBox.disabled = disabled;
                valuePanel.appendChild(textBox);
                textBox.value = charge.value;
                inputData[charge.key] = {textBox: textBox};
                inputData[input.key][charge.key] = textBox;
                
                if (eventAction != null) {
                        
                    textBox.onchange = eventAction;
                        
                }
                
                if (parseInt(charge.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                    
                    if (charge.key == 'hotelChargeOnDayBeforeNationalHoliday' || charge.key == 'hotelChargeOnNationalHoliday') {
                        
                        textBox.disabled = true;
                        textBox.setAttribute('style', 'color: #ff4646;');
                        textBox.setAttribute('data-disabled', 1);
                        textBox.value = object._i18n.get("Paid plan subscription required.");
                        
                    }
                    
                }
                
                textBox.onchange = function() {
                    
                    var key = this.getAttribute('data-key');
                    var value = parseInt(this.value);
                    var cost = object._format.formatCost(value, object._currency);
                    var costPanel = document.getElementById('costPanel_' + key);
                    costPanel.textContent = cost;
                    this.value = value;
                    object._console.log("value = " + value);
                    
                };
                
            }
            
            
        } else if (input['inputType'] == "MULTIPLE_FIELDS") {
            
            object._console.log('MULTIPLE_FIELDS');
            object._console.log(input);
            object._console.log(input.value);
            var timeColorPicker =[];
            //inputData[inputName][key] = radioBox;
            valuePanel.id = 'setting_' + input.key;
            inputData[inputName] = [];
            var fields = input.valueList;
            for (var i = 0; i < fields.length; i++) {
                
                var fieldKey = i;
                var field = fields[i];
                field.inputObjects = {};
                inputData[inputName][i] = field;
                object._console.log(field);
                
                var fieldPanel = document.createElement('div');
                
                if (field.target != null && field.target != 'both' && field.target != account.type) {
                    
                    fieldPanel.classList.add('hidden_panel');
                    
                }
                
                if (field.inputType == 'CHECK') {
                    
                    var valueName = object.create('div', field.name, null, null, null, null, null);
                    if (typeof field.class == 'string') {
                        
                        fieldPanel.setAttribute('class', field.class);
                        
                    }
                    
                    if (field.className != null && typeof field.className == "string") {
                        
                        fieldPanel.setAttribute('class', field.className);
                        
                    }
                    
                    fieldPanel.appendChild(valueName);
                    object._console.log(field.value);
                    for (var key in field.valueList) {
                        
                        object._console.log(key);
                        var value = field.valueList[key];
                        var valueName = object.create('span', value, null, null, null, 'radio_title', null);
                        var checkBox = document.createElement("input");
                        checkBox.disabled = disabled;
                        checkBox.setAttribute("data-value", key);
                        checkBox.name = field.key;
                        checkBox.type = "checkbox";
                        checkBox.value = value;
                        if (parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                            
                            checkBox.disabled = true;
                            
                        }
                        //object._console.log(parseInt(field.value));
                        if (isNaN(parseInt(field.value)) === true) {
                            
                            if (field.value == key) {
                                
                                checkBox.checked = "checked";
                                
                            }
                            
                        } else {
                            
                            if (parseInt(field.value) == key) {
                                
                                checkBox.checked = "checked";
                                
                            }
                            
                        }
                        
                        if (field.actions != null && field.actions[key] != null && typeof field.actions[key] == 'function') {
                            
                            object._console.log(typeof field.actions[key]);
                            checkBox.onclick = field.actions[key];
                            
                        } else {
                            
                            checkBox.onclick = function(event) {
                                
                                var checkBox = this;
                                object._console.log(checkBox);
                                
                            };
                            
                        }
                        
                        field.inputObjects[key] = checkBox;
                        var label = object.create('label', null, [checkBox, valueName], null, null, null, null);
                        fieldPanel.appendChild(label);
                        valuePanel.appendChild(fieldPanel);
                        
                    }
                    
                } else if (field.inputType == 'RADIO') {
                    
                    var raidoField = document.createElement('div');
                    var valueName = object.create('div', field.name, null, null, null, null, null);
                    raidoField.appendChild(valueName);
                    
                    for (var key in field.valueList) {
                        
                        var value = field.valueList[key];
                        var valueName = object.create('span', value, null, null, null, 'radio_title', null);
                        var radioBox = document.createElement("input");
                        radioBox.disabled = disabled;
                        radioBox.setAttribute("data-value", key);
                        radioBox.name = field.key;
                        radioBox.type = "radio";
                        radioBox.value = value;
                        if (field.value == key) {
                            
                            radioBox.checked = "checked";
                            
                        }
                        
                        if (parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                            
                            radioBox.disabled = true;
                            
                        }
                        
                        if (field.actions != null && field.actions[key] != null && typeof field.actions[key] == 'function') {
                            
                            object._console.log(typeof field.actions[key]);
                            radioBox.onclick = field.actions[key];
                            
                        } else {
                            
                            radioBox.onclick = function(event) {
                                
                                var radioBox = this;
                                object._console.log(radioBox);
                                
                            };
                            
                        }
                        
                        field.inputObjects[key] = radioBox;
                        var label = object.create('label', null, [radioBox, valueName], null, null, null, null);
                        fieldPanel.appendChild(label);
                        raidoField.appendChild(fieldPanel);
                        
                    }
                    
                    if (field.className != null && typeof field.className == "string") {
                        
                        raidoField.setAttribute('class', field.className);
                        
                    }
                    
                    if (field.class != null && typeof field.class == "string") {
                        
                        raidoField.setAttribute('class', field.class);
                        
                    }
                    valuePanel.appendChild(raidoField);
                    
                } else if (field.inputType == 'TEXT') {
                    
                    var valueName = object.create('span', field.name, null, null, null, null, null);
                    var inputText = document.createElement('input');
                    inputText.type = 'text';
                    inputText.value = field.value;
                    field.inputObjects[0] = inputText;
                    if (parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                        
                        inputText.disabled = true;
                        inputText.setAttribute('style', 'color: #ff4646;');
                        inputText.setAttribute('data-disabled', 1);
                        inputText.value = object._i18n.get('Paid plan subscription required.');
                        
                    }
                    
                    var label = object.create('div', null, [valueName, inputText], null, 'margin-top: 0.5em;', null, null);
                    if (field.className != null && typeof field.className == "string") {
                        
                        label.setAttribute('class', field.className);
                        
                    }
                    
                    if (field.class != null && typeof field.class == "string") {
                        
                        label.setAttribute('class', field.class);
                        
                    }
                    fieldPanel.appendChild(label);
                    object.insertElementsBeforeAndAfterInput(field, inputText, null, null);
                    valuePanel.appendChild(fieldPanel);
                    
                } else if (field.inputType == 'SELECT') {
                    
                    var valueName = object.create('span', field.name, null, null, null, null, null);
                    var select = document.createElement('select');
                    if (parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                        
                        select.disabled = true;
                        
                    }
                    field.inputObjects[0] = select;
                    for (var key in field.valueList) {
                        
                        var option = field.valueList[key];
                        object._console.log(option);
                        var optionBox = document.createElement('option');
                        optionBox.value = option.key;
                        optionBox.textContent = option.name;
                        if (isNaN(parseInt(option.key)) === false) {
                            
                            if (parseInt(field.value) == key) {
                                
                                optionBox.selected = true;
                                
                            }
                            
                        } else {
                            
                            if (field.value == option.key) {
                                
                                optionBox.selected = true;
                                
                            }
                            
                        }
                        
                        select.appendChild(optionBox);
                        
                    }
                    
                    if (field.actions != null && field.actions[0] != null && typeof field.actions[0] == 'function') {
                        
                        object._console.log(typeof field.actions[0]);
                        select.onchange = field.actions[0];
                        
                    } else {
                        
                        select.onchange = function(event) {
                            
                            var select = this;
                            object._console.log(select);
                            
                        }
                        
                    }
                    
                    if (field.isExtensionsValid != null && parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                        
                        select.disabled = true;
                        
                    }
                    
                    var label = object.create('div', null, [valueName, select], null, null, 'multiple_fields_margin_top', null);
                    if (field.className != null && typeof field.className == "string") {
                        
                        label.setAttribute('class', field.className);
                        
                    }
                    fieldPanel.appendChild(label);
                    object.insertElementsBeforeAndAfterInput(field, select, null, null);
                    valuePanel.appendChild(fieldPanel);
                    
                } else if (field.inputType == 'COLOR') {
                    
                    var valueName = object.create('div', field.name, null, null, null, 'multiple_fields_margin_top', null);
                    valuePanel.appendChild(valueName);
                    
                    var color = document.createElement("input");
                    color.type = "text";
                    color.id = field.key + "_color";
                    color.setAttribute("data-key", fieldKey);
                    color.value = field.value;
                    valuePanel.appendChild(color);
                    (function( $, timeColorPicker, field) {
                        
                        
                        timeColorPicker[field.key] = setInterval(function(){
                            
                            object._console.log(field);
                            $(function() {
                                
                                $('#' + field.key + "_color").wpColorPicker({
                                    defaultColor: false,
                                    change: function(event, ui){
                                        
                                        var key = this.getAttribute("data-key");
                                        var color = "#" + ui.color._color.toString(16);
                                        if (color === '#0') {
                                        	
                                        	color = '#000000';
                                        	
                                        }
                                        inputData[inputName][key].value = color;
                                        object._console.log(inputData[inputName][key]);
                                        
                                    },
                                    clear: function(){
                                    
                                    }
                                    
                                });
                                
                            });
                            
                            clearInterval(timeColorPicker[field.key]);
                        
                        }, 500);
                        
                    })( jQuery, timeColorPicker, field);
                    
                } else if (field.inputType == 'REMAINING_CAPACITY') {
                    
                    var valueName = object.create('div', field.name, null, null, null, 'multiple_fields_margin_top', null);
                    valuePanel.appendChild(valueName);
                    var value = {symbol: "", color: "#969696"};
                    object._console.log(field.value);
                    object._console.log(object.isJSON(field.value));
                    if (object.isJSON(field.value) == true) {
                        
                        value = JSON.parse(field.value);
                        
                    }
                    field.inputObjects[0] = value;
                    
                    var symbol = document.createElement("input");
                    symbol.type = "text";
                    symbol.setAttribute("data-key", fieldKey);
                    symbol.value = value.symbol;
                    var tdSymbol = object.create('td', null, [symbol], null, null, null, null);
                    
                    var color = document.createElement("input");
                    color.type = "text";
                    color.id = field.key + "_color";
                    color.setAttribute("data-key", fieldKey);
                    color.value = value.color;
                    var tdColor = object.create('td', null, [color], null, null, null, null);
                    var tr = object.create('tr', null, [tdSymbol, tdColor], null, null, null, null);
                    var table = object.create('table', null, [tr], null, null, null, null);
                    valuePanel.appendChild(table);
                    symbol.onchange = function() {
                        
                        var key = this.getAttribute("data-key");
                        var value = this.value;
                        inputData[inputName][key].inputObjects[0].symbol = value;
                        object._console.log(inputData[inputName][key]);
                        
                    };
                    
                    (function( $, timeColorPicker, field) {
                        
                        
                        timeColorPicker[field.key] = setInterval(function(){
                            
                            object._console.log(field);
                            $(function() {
                                
                                $('#' + field.key + "_color").wpColorPicker({
                                    defaultColor: false,
                                    change: function(event, ui){
                                        
                                        var key = this.getAttribute("data-key");
                                        var color = "#" + ui.color._color.toString(16);
                                        object._console.log(color);
                                        if (color === '#0') {
                                        	
                                        	color = '#000000';
                                        	
                                        }
                                        inputData[inputName][key].inputObjects[0].color = color;
                                        object._console.log(inputData[inputName][key]);
                                        
                                    },
                                    clear: function(){
                                    
                                    }
                                    
                                });
                                
                            });
                            
                            clearInterval(timeColorPicker[field.key]);
                        
                        }, 500);
                        
                    })( jQuery, timeColorPicker, field);
                    
                }
                
                if (field.message != null && typeof field.message == "string") {
                    
                    var messageLabel = object.create('div', null, null, null, null, 'messageLabel', null);
                    messageLabel.insertAdjacentHTML("beforeend", field.message);
                    valuePanel.appendChild(messageLabel);
                    
                }
                
                if (field.extensionsValidMessage != null && parseInt(field.extensionsValidMessage) == 1 && parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                    
                    var extensionsValidPanel = document.createElement("div");
                    extensionsValidPanel.classList.add("extensionsValid");
                    extensionsValidPanel.textContent = object._i18n.get("Paid plan subscription required.");
                    fieldPanel.insertAdjacentElement("afterbegin", extensionsValidPanel);
                    
                }
                
            }
            
            object._console.log(inputData[inputName]);
            
        } else if (input['inputType'] == "SUBSCRIPTION") {
            
            object._console.log(input);
            object._console.log(input.value);
            object._console.log(inputData);
            
            for (var key in input.optionKeys) {
                
                var option = input.optionKeys[key];
                object._console.log(option);
                if (option.inputType == 'TEXT') {
                    
                    var title = object.create('span', option.title + " :", null, null, null, null, null);
                    valuePanel.appendChild(title);
                    
                    var textBox = document.createElement("input");
                    textBox.setAttribute("class", "subscription regular-text");
                    textBox.type = "text";
                    textBox.disabled = disabled;
                    valuePanel.appendChild(textBox);
                    if (input['value'] != null && typeof input['value'] == "string") {
                        
                        textBox.value = input['value'].replace(/\\/g, "");
                        
                    } else {
                        
                        textBox.value = input['value'];
                        
                    }
                    
                } else if (option.inputType == 'CHECKBOX') {
                    
                    var valueName = object.create('span', object._i18n.get("Enable"), null, null, null, 'radio_title', null);
                    var checkBox = document.createElement("input");
                    checkBox.name = inputName;
                    checkBox.type = "checkbox";
                    checkBox.value = 1;
                    if (input.value != null && input.value[key] != null && parseInt(input.value[key]) == 1) {
                        
                        checkBox.checked = true;
                        
                    }
                    
                    if (input.optionValues != null && parseInt(input.optionValues[key]) == 1) {
                        
                        checkBox.checked = true;
                        
                    }
                    
                    var label = object.create('label', null, [checkBox, valueName], null, 'display: inline;', null, null);
                    valuePanel.appendChild(label);
                    inputData[inputName] = {textBox: textBox, checkBox: checkBox};
                    inputData[key] = checkBox;
                    object._console.log(inputData);
                    
                }
                
            }
            
        } else if (input['inputType'] == "TIME_TO_PROVIDE") {
            
            var menuList = object.create('div', null, null, null, 'grid-template-columns: auto; overflow: hidden;', 'menuList', null);
            var content = object.create('div', null, null, null, null, 'content', null);
            var overflow = object.create('div', null, [menuList, content], null, 'overflow-x: auto;', null, null);
            valuePanel.appendChild(overflow);
            
            var weeks = [object._i18n.get("Sun"), object._i18n.get("Mon"), object._i18n.get("Tue"), object._i18n.get("Wed"), object._i18n.get("Thu"), object._i18n.get("Fri"), object._i18n.get("Sat"), object._i18n.get("Public holiday")];
            var values = input.value;
            for (var day = 0; day < 8; day++) {
                
                var tab = object.create('div', weeks[day], null, "day_of_week_of_tab_by_" + day, 'margin: 1px -1px 0; grid-column-start: ' + (day + 1) + ';', null, {week: day} );
                var timeZone = object.create('div', null, null, "day_of_week_by_" + day, null, 'weekPanel', {week: day} );
                if (day == 0) {
                    
                    tab.setAttribute("class", "menuItem active");
                    tab.setAttribute("style", 'margin: 0px -1px 0; grid-column-start: ' + (day + 1) + ';');
                    timeZone.classList.remove("hidden_panel");
                    
                } else {
                    
                    tab.setAttribute("class", "menuItem");
                    timeZone.classList.add("hidden_panel");
                    
                }
                
                tab.onclick = function(){
                    
                    var selected = parseInt(this.getAttribute("data-week"));
                    for (var day = 0; day < 8; day++) {
                        
                        var tab = document.getElementById("day_of_week_of_tab_by_" + day);
                        var timeZone = document.getElementById("day_of_week_by_" + day);
                        if (selected != day) {
                            
                            tab.classList.remove("active");
                            tab.setAttribute("style", 'margin: 1px -1px 0;; grid-column-start: ' + (day + 1) + ';');
                            timeZone.classList.add("hidden_panel");
                            
                        } else {
                            
                            tab.classList.add("active");
                            tab.setAttribute("style", 'margin: 0px -1px 0;; grid-column-start: ' + (day + 1) + ';');
                            timeZone.classList.remove("hidden_panel");
                            
                        }
                        
                    }
                    
                };
                
                menuList.appendChild(tab);
                content.appendChild(timeZone);
                
                var checkedChooseAllTime = false;
                for (var i = 0; i < 24; i++) {
                    
                    var checkBox = document.createElement("input");
                    checkBox.id = 'service_time_' + day +'_' + (i * 60);
                    checkBox.setAttribute("data-hours", i * 60);
                    checkBox.setAttribute("data-week", day);
                    checkBox.setAttribute('data-inputName', inputName);
                    checkBox.type = "checkbox";
                    checkBox.disabled = disabled;
                    checkBox.value = i * 60;
                    if (values[day] != null && values[day][i * 60] != null && parseInt(values[day][i * 60]) == 1) {
                        
                        checkBox.checked = true;
                        checkedChooseAllTime = true;
                        
                    }
                    
                    checkBox.onchange = function(){
                        
                        var inputName = this.getAttribute('data-inputName');
                        var week = parseInt(this.getAttribute("data-week"));
                        var hours = parseInt(this.getAttribute("data-hours"));
                        
                        if (inputData[inputName]['json'][week] == null) {
                            
                            inputData[inputName]['json'][week] = {};
                            
                        }
                        
                        if (inputData[inputName]['json'][week][hours] != null) {
                            
                            inputData[inputName]['json'][week][hours] = 0;
                            if (this.checked == true) {
                                
                                inputData[inputName]['json'][week][hours] = 1;
                                
                            }
                            
                        } else {
                            
                            inputData[inputName]['json'][week][hours] = 1;
                            
                        }
                        
                        object._console.log(inputData[inputName]);
                        
                    };
                    
                    var text = object.create('span', ("0" + i).slice(-2) + ":00 ", null, null, null, null, null);
                    var label = object.create('label', null, [checkBox, text], null, null, 'timeAndWeek', null);
                    timeZone.appendChild(label);
                    
                }
                
                var chooseAllTime = document.createElement("input");
                chooseAllTime.id = 'chooseAllTime_' + day;
                chooseAllTime.setAttribute("data-week", day);
                chooseAllTime.setAttribute('data-inputName', inputName);
                chooseAllTime.type = "checkbox";
                chooseAllTime.checked = checkedChooseAllTime;
                chooseAllTime.disabled = disabled;
                var text = object.create('span', object._i18n.get('Select all time slots'), null, null, null, null, null);
                var label = object.create('label', null, [chooseAllTime, text], null, null, 'chooseAllTime', null);
                timeZone.appendChild(label);
                
                chooseAllTime.onclick = function() {
                    
                    var checked = true;
                    if (this.checked === false) {
                        
                        checked = false;
                        
                    }
                    object._console.log('checked = ' + checked);
                    var inputName = this.getAttribute('data-inputName');
                    var day = this.getAttribute('data-week');
                    for (var i = 0; i < 24; i++) {
                        
                        var checkBox = document.getElementById('service_time_' + day +'_' + (i * 60));
                        object._console.log(typeof checkBox);
                        if (typeof checkBox == 'object') {
                            
                            checkBox.checked = checked;
                            var week = parseInt(checkBox.getAttribute("data-week"));
                            var hours = parseInt(checkBox.getAttribute("data-hours"));
                            if (inputData[inputName]['json'][week] == null) {
                                
                                inputData[inputName]['json'][week] = {};
                                
                            }
                            
                            if (inputData[inputName]['json'][week][hours] != null) {
                                
                                inputData[inputName]['json'][week][hours] = 0;
                                if (checked === true) {
                                    
                                    inputData[inputName]['json'][week][hours] = 1;
                                    
                                }
                                
                            } else {
                                
                                inputData[inputName]['json'][week][hours] = 1;
                                
                            }
                            
                            object._console.log(inputData[inputName]);
                            
                        }
                        
                    }
                    
                };
                
            }
            
            inputData[inputName] = {json: values};
            object._console.log(values);
            
        }
        
        if (input.message != null && typeof input.message == "string") {
            
            var messageLabel = document.createElement("div");
            messageLabel.classList.add("messageLabel");
            messageLabel.insertAdjacentHTML("beforeend", input.message);
            valuePanel.appendChild(messageLabel);
            
        }
        
        if (parseInt(input.isExtensionsValid) == 1 && isExtensionsValid == 0) {
            
            var extensionsValidPanel = document.createElement("div");
            extensionsValidPanel.classList.add("extensionsValid");
            extensionsValidPanel.textContent = object._i18n.get("Paid plan subscription required.");
            valuePanel.insertAdjacentElement("afterbegin", extensionsValidPanel);
            if (input.isExtensionsValidPanel != null && parseInt(input.isExtensionsValidPanel) == 0) {
                
                valuePanel.removeChild(extensionsValidPanel);
                
            }
            
        }
        
        function add_class(input, className) {
            
            object._console.log(className);
            if (className != null) {
                
                for (var i = 0; i < className.length; i++) {
                    
                    input.classList.add(className[i]);
                    
                }
                
            }
            
            return input;
            
        };
        
        function create_tr(tr_index, table, input, account, valueList){
            
            if (typeof valueList == "string") {
                
                valueList = [valueList];
                
            }
            object._console.log(valueList);
            
            var isExtensionsValid = 0;
            if (input.isExtensionsValid != null && parseInt(input.isExtensionsValid) == 1) {
                
                isExtensionsValid = 1;
                
            }
            object._console.log(inputName);
            object._console.log(input);
            object._console.log(input.optionsType);
            object._console.log(valueList);
            object._console.log('isExtensionsValid =' + isExtensionsValid);
            var tr = object.create('tr', null, null, null, null, 'tr_option', null);
            inputData[inputName][tr_index] = {};
            for (var key in input.optionsType) {
                
                object._console.log('key = ' + key);
                var type = {type: "TEXT"};
                if (input.optionsType != null && input.optionsType[key] != null) {
                    
                    type = input.optionsType[key];
                    
                } else {
                    
                    continue;
                    
                }
                object._console.log(type);
                
                var value = '';
                if (valueList != null) {
                    
                    value = valueList[key];
                    
                }
                object._console.log("value = " + value);
                object._console.log(input.optionsType[key]);
                
                var filedTd = object.create('td', null, null, null, null, 'td_option', null);
                if (input.optionsType != null && input.optionsType[key].target != 'both' && input.optionsType[key].target != account.type) {
                    
                    filedTd.classList.add('hidden_panel');
                    
                }
                
                if (type.type == "dayOfTheWeek") {
                    
                    filedTd.classList.add('td_option_grid');
                    var options = type.options;
                    for (var i = 0; i < options.length; i++) {
                        
                        var option = options[i];
                        var isExtensionsValidForOption = 0;
                        if (option.isExtensionsValid != null && parseInt(option.isExtensionsValid) == 1) {
                            
                            isExtensionsValidForOption = 1;
                            
                        }
                        object._console.log(option);
                        object._console.log(valueList[option.key]);
                        object._console.log('isExtensionsValidForOption = ' + isExtensionsValidForOption);
                        
                        var dayName = object.create('div', option.name + ':', null, null, null, 'dayOfTheWeekName', null);
                        filedTd.appendChild(dayName);
                        var textBox = document.createElement("input");
                        textBox.id = tr_index + "_" + option.key;
                        textBox.name = option.key;
                        textBox.setAttribute("data-key", tr_index);
                        textBox.setAttribute("data-name", inputName);
                        textBox.setAttribute("data-type", type.type);
                        textBox.setAttribute("class", "regular-text dayOfTheWeekValue");
                        textBox.type = "text";
                        if (valueList[option.key] != null) {
                            
                            textBox.value = valueList[option.key];
                            inputData[inputName][tr_index][option.key] = valueList[option.key];
                            
                        }
                        filedTd.appendChild(textBox);
                        if (object._isExtensionsValid == 0 && isExtensionsValidForOption == 1) {
                            
                            textBox.setAttribute('style', 'color: #ff4646;');
                            textBox.disabled = true;
                            textBox.value = object._i18n.get("Paid plan subscription required.");
                            
                        } else {
                            
                            textBox.onchange = function() {
                                
                                var textBox = this;
                                var id = textBox.getAttribute('data-key');
                                var name = textBox.getAttribute('data-name');
                                var optionName = textBox.name;
                                var value = textBox.value;
                                value = parseInt(value);
                                if (isNaN(value)) {
                                    
                                    value = 0;
                                    
                                }
                                
                                var json = JSON.parse(inputData[name][id].json);
                                json[optionName] = value;
                                inputData[name][id].json = JSON.stringify(json);
                                inputData[name][id][optionName] = value;
                                
                                object._console.log(name);
                                object._console.log(id);
                                object._console.log(optionName);
                                object._console.log(inputData);
                                
                            };
                            
                        }
                        
                    }
                    
                }
                
                if (type.type == "TEXT") {
                    
                    var textBox = document.createElement("input");
                    textBox.id = tr_index + "_" + key;
                    textBox.setAttribute("data-key", tr_index);
                    textBox.setAttribute("data-type", type.type);
                    textBox.setAttribute("class", "regular-text");
                    textBox = add_class(textBox, type.class);
                    
                    textBox.type = "text";
                    if (value != null && value.length != 0) {
                        
                        textBox.value = value;
                        inputData[inputName][tr_index].value = value;
                        
                    }
                    filedTd.appendChild(textBox);
                    object._console.log("object._isExtensionsValid = " + object._isExtensionsValid);
                    if (object._isExtensionsValid == 0 && isExtensionsValid == 1) {
                        
                        textBox.disabled = true;
                        
                    } else {
                        
                        textBox.onchange = function() {
                            
                            var dataKey = this.getAttribute("data-key");
                            var value = this.value;
                            var valueList = JSON.parse(inputData[inputName][dataKey].json);
                            object._console.log(valueList);
                            for (var key in valueList) {
                                
                                if (document.getElementById(tr_index + "_" + key) == null) {
                                    
                                    continue;
                                    
                                }
                                
                                var textValue = document.getElementById(tr_index + "_" + key).value;
                                if (key == 'cost' || key == 'cost_1' || key == 'cost_2' || key == 'cost_3' || key == 'cost_4' || key == 'cost_5' || key == 'cost_6') {
                                    
                                    textValue = parseInt(textValue);
                                    if (isNaN(textValue)) {
                                        
                                        textValue = 0;
                                        
                                    }
                                    document.getElementById(tr_index + "_" + key).value = textValue;
                                    
                                } else {
                                    
                                    value = value.replace(/\"/g, "'");
                                    textValue = textValue.replace(/\"/g, "'");
                                    
                                }
                                valueList[key] = textValue;
                                object._console.log("key = " + key);
                                object._console.log("textValue = " + textValue);
                                
                            }
                            
                            var json = JSON.stringify(valueList);
                            inputData[inputName][dataKey].json = json;
                            inputData[inputName][dataKey].value = value;
                            object._console.log(valueList);
                            object._console.log(json);
                            object._console.log(inputData);
                            object._console.log("dataKey = " + dataKey + " value = " + value);
                            
                        };
                        
                    }
                    
                    object._console.log(textBox);
                    
                    
                } else if (type.type == "SELECT") {
                    
                    var selectBox = document.createElement("select");
                    selectBox.id = tr_index + "_" + key;
                    selectBox.setAttribute("data-key", tr_index);
                    selectBox.setAttribute("data-type", type.type);
                    filedTd.appendChild(selectBox);
                    for (var i = parseInt(type.start); i < parseInt(type.end); i = i + parseInt(type.addition)) {
                        
                        var optionBox = document.createElement("option");
                        optionBox.value = i;
                        optionBox.textContent = object._i18n.get(type.unit, [i]);
                        if (value != null && parseInt(value) == i) {
                            
                            optionBox.selected = true;
                            inputData[inputName][tr_index].value = value;
                            
                        }
                        selectBox.appendChild(optionBox);
                        
                    }
                    
                    if (object._isExtensionsValid == 0 && isExtensionsValid == 1) {
                        
                        selectBox.disabled = true;
                        
                    } else {
                        
                        selectBox.onchange = function(){
                            
                            var dataKey = this.getAttribute("data-key");
                            var value = this.value;
                            for (var key in valueList) {
                                
                                var textValue = document.getElementById(tr_index + "_" + key).value;
                                valueList[key] = textValue;
                                object._console.log("textValue = " + textValue);
                                
                            }
                            
                            var json = JSON.stringify(valueList);
                            inputData[inputName][dataKey].json = json;
                            inputData[inputName][dataKey].value = value;
                            object._console.log(valueList);
                            object._console.log(json);
                            object._console.log("dataKey = " + dataKey + " value = " + value);
                            
                        };
                        
                    }
                    
                }
                
                tr.appendChild(filedTd);
                
                
            }
            
            object._console.log(tr.hasChildNodes());
            if (tr.hasChildNodes() === true) {
                
                inputData[inputName][tr_index].json = "";
                if (JSON.stringify(valueList)) {
                    
                    inputData[inputName][tr_index].json = JSON.stringify(valueList);
                    
                }
                
                var deleteButton = object.create('label', 'delete', null, null, null, 'material-icons deleteLink', {key: tr_index} );
                var deleteTd = object.create('td', null, [deleteButton], null, null, 'td_delete td_option', null);
                tr.appendChild(deleteTd);
                table.appendChild(tr);
                table_tr[tr_index] = tr;
                deleteButton.onclick = function(){
                    
                    var result = false;
                    var dataKey = this.getAttribute("data-key");
                    object._console.log(dataKey);
                    var json = JSON.parse(inputData[inputName][dataKey].json);
                    object._console.log(json);
                    
                    var tr = table_tr[parseInt(dataKey)];
                    object._console.log(tr);
                    table.removeChild(tr);
                    delete table_tr[dataKey];
                    delete inputData[inputName][dataKey];
                    object._console.log(tr);
                    object._console.log(table_tr);
                    object._console.log(inputData[inputName]);
                    object._console.log("tr_index = " + tr_index);
                
                };
                
            }
            
        };
        
        return valuePanel;
        
    };
    
    SCHEDULE.prototype.create = function(elementType, text, childElements, id, style, className, data_x) {
        
        var panel = this._element.create(elementType, text, childElements, id, style, className, data_x);
        return panel;
        
    };
    
    SCHEDULE.prototype.createButtonPanel = function(id, style, className, buttons) {
        
        var buttonPanel = this._element.createButtonPanel(id, style, className, buttons);
        return buttonPanel;
        
    };
    
    SCHEDULE.prototype.createButton = function(id, style, className, data_x, text) {
        
        var button = this._element.createButton(id, style, className, data_x, text);
        return button;
        
    };
    

    SCHEDULE.prototype.editPanelShow = function(showBool){
        
        var body = document.getElementsByTagName("body")[0];
        if(showBool == true){
            
            body.classList.add("modal-open");
            this.editPanel.setAttribute("class", "edit_modal");
            this.blockPanel.setAttribute("class", "edit_modal_backdrop");
            
        }else{
            
            body.classList.remove("modal-open");
            this.editPanel.setAttribute("class", "hidden_panel");
            this.blockPanel.setAttribute("class", "hidden_panel");
            
        }
        
    };
    
    SCHEDULE.prototype.isJSON = function(arg){
		
		arg = (typeof arg === "function") ? arg() : arg;
	    if(typeof arg  !== "string") {
	        return false;
	    }
	    
	    try{
	    	arg = (!JSON) ? eval("(" + arg + ")") : JSON.parse(arg);
			return true;
	    }catch(e){
			return false;
	    }
		
	};
    
    
