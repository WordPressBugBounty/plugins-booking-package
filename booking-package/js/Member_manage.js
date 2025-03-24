/* globals scriptError */
/* globals I18n */
/* globals Booking_App_Calendar */
/* globals Booking_App_XMLHttp */
/* globals FORMAT_COST */
/* globals setting_data */
/* globals users_data */
/* globals booking_package_dictionary */
/* globals Booking_Package_Console */
/* globals Booking_Package_Hotel */
/* globals Booking_Package_DatabaseUpdateErrors */
/* globals Booking_Package_Elements */

document.addEventListener('DOMContentLoaded', function() {
    
    window.addEventListener('load', function(){
        
        new Member_manage(setting_data, users_data, booking_package_dictionary);
        
    });
    
    window.addEventListener('error', function(event) {
        
        var error = new scriptError(setting_data, booking_package_dictionary, event.message, event.filename, event.lineno, event.colno, event.error, false);
        error.send();
        
    }, false);
    
});

function Member_manage(setting_data, users_data, booking_package_dictionary){
    
    var object = this;
    this._setting_data = setting_data;
    this._booking_package_dictionary = booking_package_dictionary;
    this._element = new Booking_Package_Elements(setting_data.debug);
    this._debug = new Booking_Package_Console(setting_data.debug);
    this._console = {};
    this._console.log = this._debug.getConsoleLog();
    object._console.log(setting_data);
    this._i18n = new I18n(null);
    this._i18n.setDictionary(booking_package_dictionary);
    this._url = setting_data.url;
    this._nonce = setting_data.nonce;
    this._action = setting_data.action;
    this._isExtensionsValid = parseInt(setting_data.isExtensionsValid);
    this._currency = setting_data.currency;
    this._limit = parseInt(setting_data.limit);
    this._offset = 0;
    this._blockPanel = document.getElementById("blockPanel");
    this._loadingPanel = document.getElementById("loadingPanel");
    this._editPanel = document.getElementById("editPanel");
    this._media_modal_close = document.getElementById("media_modal_close");
    this._usersInfo = users_data;
    this._users = {};
    this._authority = "user";
    this._keywords = null;
    this._calendar = null;
    this._selectedKey = null;
    this._dateFormat = setting_data.dateFormat;
    this._startOfWeek = setting_data.startOfWeek;
    this._positionOfWeek = setting_data.positionOfWeek;
    this._positionTimeDate = setting_data.positionTimeDate;
    this._clock = setting_data.clock;
    this._calendarAccountList = {};
    this._emailEnableList = {};
    this._locale = setting_data.locale;
    this._numberFormatter = false;
    this._managementUsersV2 = 0;
    this._inputType = setting_data.inputType;
    this._userFunctions = setting_data.userFunctions;
    this._userFormFields = setting_data.userFormFields;
    if (parseInt(setting_data.numberFormatter) === 1) {
        
        this._numberFormatter = true;
        
    }
    
    if (parseInt(setting_data.managementUsersV2) === 1) {
        
        this._managementUsersV2 = 1;
        
    }
    
    if (typeof this._userFormFields === 'undefined' || setting_data.formFields === null) {
        
        this._userFormFields = [];
        
    }
    object._console.log(this._userFormFields);
    
    this._currencies = setting_data.currencies;
    this._currency_info = {locale: this._locale, currency: this._currency, info: this._currencies[this._currency]};
    
    this._weekName = [this._i18n.get('Sun'), this._i18n.get('Mon'), this._i18n.get('Tue'), this._i18n.get('Wed'), this._i18n.get('Thu'), this._i18n.get('Fri'), this._i18n.get('Sat')];
    this._hotel = new Booking_Package_Hotel(this._currency, this._weekName, this._dateFormat, this._positionOfWeek, this._positionTimeDate, this._startOfWeek, this._numberFormatter, this._currency_info, booking_package_dictionary, this._debug);
    this._booking_manage = new Booking_manage(setting_data, booking_package_dictionary);
    this._defaultName = {'user_login': object._i18n.get('Username'), 'user_email': object._i18n.get('Email'), 'user_pass': object._i18n.get('Password')};
    this._contentPanel = document.getElementById("media_frame_reservation_content");
    
    for (var key in setting_data.calendarAccountList) {
        
        var account = setting_data.calendarAccountList[key];
        object._calendarAccountList[parseInt(account.key)] = account;
        
    }
    
    for (var key in setting_data.emailEnableList) {
        
        var account = setting_data.emailEnableList[key];
        object._emailEnableList[parseInt(key)] = account;
        
    }
    
    if (document.getElementById('booking_package_databaseUpdateErrors') != null) {
		
		object._console.log(document.getElementById('booking_package_databaseUpdateErrors'));
		var databaseUpdateErrors = new Booking_Package_DatabaseUpdateErrors(document.getElementById('booking_package_databaseUpdateErrors'));
		
	}
    
    var user_form_close = document.getElementById("booking-package-register_user_return_button");
    var user_edit_form_close = document.getElementById("booking-package-edit_user_return_button");
    user_form_close.classList.remove("hidden_panel");
    user_edit_form_close.classList.remove("hidden_panel");
    
    this._member_list_table_list = document.getElementById("member_list_table").getElementsByTagName("tr");
    object._console.log(this._member_list_table_list);
    for (var i = 0; i < this._member_list_table_list.length; i++) {
        
        var tr = this._member_list_table_list[i];
        if (tr.id.length == 0) {
            
            continue;
            
        }
        
        this._users[tr.id] = tr;
        object._console.log(tr);
        
    }
    
    user_form_close.onclick = function(){
        
        object._blockPanel.classList.add("hidden_panel");
        document.getElementById("booking-package-user-form").classList.add("hidden_panel");
        document.getElementById("booking-package-user-edit-form").classList.add("hidden_panel");
        var body = document.getElementsByTagName("body")[0];
        body.classList.remove("modal-open");
        
    };
    
    user_edit_form_close.onclick = function(){
        
        object._blockPanel.classList.add("hidden_panel");
        document.getElementById("booking-package-user-form").classList.add("hidden_panel");
        document.getElementById("booking-package-user-edit-form").classList.add("hidden_panel");
        var body = document.getElementsByTagName("body")[0];
        body.classList.remove("modal-open");
        
    };
    
    var add_member = document.getElementById("add_member");
    if (parseInt(this._isExtensionsValid) == 1) {
        
        add_member.onclick = function(){
            
            object.userForm();
            
        };
        
    } else {
        
        add_member.disabled = true;
        
    }
    
    this._media_modal_close.onclick = function(event) {
        
        object._editPanel.classList.add("hidden_panel");
        object._blockPanel.classList.add("hidden_panel");
        if (document.getElementById("userInfoPanel") != null) {
            
            document.getElementById("userInfoPanel").remove("show_panel");
            
        }
        
        var body = document.getElementsByTagName("body")[0];
        body.classList.remove("modal-open");
        
    };
    
    this._blockPanel.onclick = function(event) {
        
        var close = true;
        object._console.log(event);
        var path = event.path;
        if (path != null) {
            
            for (var i = 0; i < path.length; i++) {
                
                if (path[i].id == "booking-package-user-form" || path[i].id == "booking-package-user-edit-form") {
                    
                    close = false;
                    break;
                    
                }
                
            }
            
            if (close == true) {
                
                this.classList.add("hidden_panel");
                object._editPanel.classList.add("hidden_panel");
                document.getElementById("booking-package-user-form").classList.add("hidden_panel");
                document.getElementById("booking-package-user-edit-form").classList.add("hidden_panel");
                var body = document.getElementsByTagName("body")[0];
                body.classList.remove("modal-open");
                
            }
            
        }
        
    };
    
    var member_limit = document.getElementById("member_limit");
    for(var i = 0; i < member_limit.options.length; i++){
        
        var option = parseInt(member_limit[i].value);
        if(option == this._limit){
            
            member_limit[i].selected = true;
            break;
            
        }
        
    }
    
    var swich_authority = document.getElementById("swich_authority");
    swich_authority.onchange = function(){
        
        var swich_authority = this;
        var value = swich_authority.value;
        object._console.log(value);
        object._authority = value;
        object._offset = 0;
        var index = member_limit.selectedIndex;
        var limit = member_limit.options[index].value;
        object.movePage("before_page", limit, null);
        
    };
    
    document.getElementById("before_page").onclick = function(){
        
        var index = member_limit.selectedIndex;
        var limit = member_limit.options[index].value;
        object.movePage("before_page", limit, null);
        
    };
    
    document.getElementById("next_page").onclick = function(){
        
        var index = member_limit.selectedIndex;
        var limit = member_limit.options[index].value;
        object.movePage("next_page", limit, null);
        
    };
    
    this.loadingPanel(0);
    object._console.log(this._users);
    //this.start();
    this.loadTabFrame();
    this.lookingForUsers(member_limit.options[member_limit.selectedIndex].value);
    
};

Member_manage.prototype.loadingPanel = function(mode){
    
    if (mode == 1) {
        
        this._loadingPanel.setAttribute("class", "loading_modal_backdrop");
        
    } else {
        
        this._loadingPanel.setAttribute("class", "hidden_panel");
        
    }
    
};

Member_manage.prototype.setUsersInfo = function(userInfo){
    
    this._usersInfo = userInfo;
    
};

Member_manage.prototype.getUsersInfo = function(){
    
    return this._usersInfo;
    
};

Member_manage.prototype.serachUser = function(id){
    
    if (this._usersInfo[id] != null) {
        
        return this._usersInfo[id];
        
    } else {
        
        return 0;
        
    }
    
};

Member_manage.prototype.setUsers = function(users){
    
    this._users = users;
    
};

Member_manage.prototype.getUsers = function(){
    
    return this._users;
    
};

Member_manage.prototype.setKeywords = function(keywords) {
    
    this._keywords = keywords;
    
};

Member_manage.prototype.getKeywords = function() {
    
    return this._keywords;
    
};

Member_manage.prototype.setSelectedKey = function(_selectedKey){
    
    this._selectedKey = _selectedKey;
    
};

Member_manage.prototype.getSelectedKey = function(){
    
    return this._selectedKey;
    
};

Member_manage.prototype.loadTabFrame = function() {
    
    var object = this;
	var menuList = {usersLink: 'member_list', formFieldsLink: 'formFieldsPanel', settingLink: 'userSettingPanel'};
	object._console.log('_managementUsersV2 = ' + object._managementUsersV2);
	
	if (object._managementUsersV2 === 0) {
	    
	    document.getElementById('menuList').classList.add('hidden_panel');
	    document.getElementById('member_list').classList.remove('hidden_panel');
	    object.start();
	    return null;
	    
	}
	
	object.start();
	for (var key in menuList) {
		
		var button = document.getElementById(key);
		button.classList.remove("hidden_panel");
		button.setAttribute("data-key", key);
		button.onclick = function(event) {
			
			var clickKey = this.getAttribute("data-key");
			object._console.log(clickKey);
			for (var key in menuList) {
				
				var link = document.getElementById(key);
				var panel = document.getElementById(menuList[key]);
				console.log(panel);
				if (clickKey == key) {
					
					link.setAttribute("class", "menuItem active");
					panel.setAttribute("class", "");
					
					if (clickKey == 'usersLink') {
						
						object.start();
						
					} else if (clickKey == 'formFieldsLink') {
					    
					    var loadingPanel = document.getElementById("loadingPanel");
						loadingPanel.classList.remove("hidden_panel");
						var postData = {mode: "getUserInputFields", nonce: object._nonce, action: object._action};
						var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
							
							object._console.log(json);
							object.formFieldsPanel(json);
							loadingPanel.classList.add("hidden_panel");
							
						});
						
					} else if (clickKey == 'settingLink') {
						
						object.userSettingPanel();
						
					}
					
				} else {
					
					link.setAttribute("class", "menuItem");
					panel.setAttribute("class", "hidden_panel");
					
				}
				
			}
			
		};
		
	}
    
};

Member_manage.prototype.formFieldsPanel = function(formFields) {
    
    var object = this;
    object._userFormFields = formFields;
    var editBool = true;
    object._console.log('formFieldsPanel');
    object._console.log(formFields);
    
    var addButton = object.createButton(null, null, 'w3tc-button-save button-primary', null, object._i18n.get("Add new item") );
    addButton.disabled = false;
    var saveButton = object.createButton(null, 'float: right;', 'w3tc-button-save button-primary', null, object._i18n.get("Save the changed order") );
    saveButton.disabled = true;
    var buttonPanel = object.createButtonPanel(null, 'padding-bottom: 10px;', null, [addButton, saveButton] );
    
    var mainPanel = document.getElementById("formFieldsPanel");
    mainPanel.textContent = null;
    mainPanel.appendChild(buttonPanel);
    
    var panel = object.create('div', null, null, 'formSort', null, 'dnd', null);
    var buttons = {};
    var columns = {};
    var ranking_columns = {};
    
    for (var key = 0; key < formFields.length; key++) {
        
        object._console.log(formFields[key]);
        if (typeof formFields[key].name == "string") {
            
            formFields[key].name = formFields[key].name.replace(/\\/g, "");
            
        }
        
        if (object._defaultName.hasOwnProperty(formFields[key].id) === true) {
            
            formFields[key].name = object._defaultName[formFields[key].id];
            
        }
        
        var rank_up_button = object.create('div', 'expand_less', null, null, null, 'material-icons rank_up_down_button', {key: key});
        var content_name = object.create('div', formFields[key]['name'], null, null, null, 'content_name', null);
        var contentPanel = object.create('div', null, [rank_up_button, content_name], null, null, 'content_block', null);
        if (formFields[key].required == 'true' || formFields[key].required == 'true_frontEnd') {
            
            contentPanel.classList.add("dnd_required");
            
        }
        
        if(formFields[key]["active"] != "true"){
            
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
            if (target_column == null) {
                
                return null;
                
            }
            ranking_columns = object.moveRankingUp(ranking_columns, key);
            panel.insertBefore(ranking_columns[key], target_column);
            saveButton.disabled = false;
            
        };
        
        rank_down_button.onclick = function() {
            
            const key = 'key_' + this.getAttribute("data-key");
            const keys = object.getRankingUpAndDownKeys(ranking_columns, key);
            const target_column = ranking_columns[keys.down];
            if (target_column == null) {
                
                return null;
                
            }
            ranking_columns = object.moveRankingDown(ranking_columns, key);
            panel.insertBefore(target_column, ranking_columns[key]);
            saveButton.disabled = false;
            
        };
        
        editLabel.onclick = function(event){
            
            if (editBool === true) {
                
                addButton.disabled = true;
                editBool = false;
                var key = this.getAttribute("data-key");
                for (var formKey in columns) {
                    
                    columns[formKey].classList.add("hidden_panel");
                    
                }
                object._console.log(formFields);
                object._console.log(formFields[key]);
                let inputType = JSON.parse(JSON.stringify(object._inputType));
                (function(item, inputType) {
                    
                    for (var key in item) {
                        
                        if (inputType[key] != null) {
                            
                            inputType[key].value = item[key];
                            
                        }
                        
                        if (key === 'active' || key === 'required') {
                            
                            if (item[key] === 'true') {
                                
                                inputType[key].value = [object._i18n.get('Enabled')];
                                
                            } else {
                                
                                inputType[key].value = [];
                                
                            }
                            
                        }
                        
                    }
                    
                    return inputType;
                    
                })(formFields[key], inputType);
                
                inputType.id.disabled = 1;
                if (object._defaultName.hasOwnProperty(formFields[key].id) === true) {
                    
                    inputType.active.disabled = 1;
                    inputType.name.disabled = 1;
                    inputType.required.disabled = 1;
                    inputType.type.disabled = 1;
                    
                }
                
                
                object.addItem(mainPanel, 'editUserInput', inputType, function(action){
                    
                    editBool = true;
                    if (action == "close") {
                        
                        addButton.disabled = false;
                        for (var formKey in columns) {
                            
                            columns[formKey].classList.remove("hidden_panel");
                            
                        }
                        
                    } else {
                        
                        if (typeof action == 'object') {
                            
                            if (action['status'] != 'error') {
                                
                                object._console.log(action);
                                object.formFieldsPanel(action);
                                
                            }
                            
                        }
                        
                    }
                    
                    panel.classList.remove("hidden_panel");
                    
                });
                
            }
            
        };
        
        if (object._defaultName.hasOwnProperty(formFields[key].id) === false) {
            
            deleteLabel.onclick = function(event){
                
                if (editBool === true) {
                    
                    editBool = false;
                    const key = parseInt(this.getAttribute("data-key"));
                    var result = confirm(object._i18n.get('Do you delete the "%s"?', [formFields[key].name]));
                    if (result === true) {
                        
                        var loadingPanel = document.getElementById("loadingPanel");
                        loadingPanel.classList.remove("hidden_panel");
                        var postData = {mode: 'deleteUserInput', nonce: object._nonce, action: object._action, index: key};
                        new Booking_App_XMLHttp(object._url, postData, false, function(json){
                            
                            loadingPanel.classList.add("hidden_panel");
                            object._console.log(json);
                            object.formFieldsPanel(json);
                            
                        });
                        
                    }
                    editBool = true;
                    
                }
                
            };
            
        } else {
            
            deleteLabel.setAttribute('style', 'text-decoration: line-through; color: #ff3333; cursor: auto;');
            
        }
        
        
        
    }
    
    mainPanel.appendChild(panel);
    
    addButton.onclick = function(event){
        
        if (editBool === true) {
            
            let inputType = JSON.parse(JSON.stringify(object._inputType));
            inputType.active.value = [object._i18n.get('Enabled')];
            inputType.required.value = [object._i18n.get('Enabled')];
            panel.classList.add("hidden_panel");
            editBool = false;
            addButton.disabled = true;
            object.addItem(mainPanel, 'addUserInput', inputType, function(action){
                
                editBool = true;
                if (action == "close") {
                    
                    addButton.disabled = false;
                    
                } else {
                    
                    object._console.log(typeof action);
                    if (typeof action == 'object') {
                        
                        if (action['status'] != 'error') {
                            
                            object._console.log(action);
                            object.formFieldsPanel(action);
                            
                        }
                        
                    }
                    
                }
                
                panel.classList.remove("hidden_panel");
                
            });
            
        }

    };
    
    saveButton.onclick = function(event){
        
        var keys = [];
        var panelList = panel.getElementsByClassName('dnd_column');
        for(var i = 0; i < panelList.length; i++){
            
            var panelKey = parseInt(panelList[i].getAttribute("data-key"));
            keys.push(formFields[panelKey].id);
        
        }
        
        var postData = {mode: 'changedOrderUserInput', nonce: object._nonce, action: object._action, keys: JSON.stringify(keys)};
        object._console.log(postData);
        var loadingPanel = document.getElementById("loadingPanel");
        loadingPanel.classList.remove("hidden_panel");
        new Booking_App_XMLHttp(object._url, postData, false, function(json){
            
            loadingPanel.classList.add("hidden_panel");
            object.formFieldsPanel(json);
            
        });
        
    };
    
    
}

Member_manage.prototype.getRankingUpAndDownKeys = function (obj, key) {
    
    let entries = Object.entries(obj);
    let index = entries.findIndex(entry => entry[0] === key);
    let upKey = index > 0 ? entries[index - 1][0] : null;
    let downKey = index < entries.length - 1 ? entries[index + 1][0] : null;
    return {up: upKey, down: downKey};
    
}

Member_manage.prototype.moveRankingUp = function (obj, key) {
    
    let entries = Object.entries(obj);
    let index = entries.findIndex(entry => entry[0] === key);
    if (index > 0) {
        
        [entries[index], entries[index - 1]] = [entries[index - 1], entries[index]];
        
    }
    
    return Object.fromEntries(entries);
    
};

Member_manage.prototype.moveRankingDown = function (obj, key) {
    
    let entries = Object.entries(obj);
    let index = entries.findIndex(entry => entry[0] === key);
    if (index !== -1 && index < entries.length - 1) {
        
        [entries[index], entries[index + 1]] = [entries[index + 1], entries[index]];
        
    }
    
    return Object.fromEntries(entries);
    
};

Member_manage.prototype.addItem = function (mainPanel, mode, inputType, callback) {
    
    var object = this;
    const inputTypeList = inputType;
    const input = new Booking_Package_Input(object._debug);
    object._console.log(mode);
    object._console.log(mainPanel);
    object._console.log(inputTypeList);
    var inputData = {};
    var closeHeghit = mainPanel.clientHeight;
    var addPanel = object.create('div', null, null, 'addCoursePanel', null, null, null);
    mainPanel.appendChild(addPanel);
    var table = object.create('table', null, null, null, null, 'form-table', null);
	var trList = {};
    for (var key in inputTypeList) {
        
        object._console.log(key);
        object._console.log(inputTypeList[key]);
        var data = inputTypeList[key];
        
        var value = document.createElement('div');
        if (data.inputType === 'OPTION') {
            
            var valuePanel = object.create('div', null, null, null, null, 'valuePanel', null);
            var options = data.value;
            object._console.log(options);
            
            var addButton = object.create('div', object._i18n.get("Add"), null, null, null, 'addLink', null);
            let table = object.create('table', null, null, null, null, 'table_option wp-list-table widefat fixed striped', null);
            valuePanel.appendChild(addButton);
            valuePanel.appendChild(table);
            inputData[data.id] = {};
            var tr_index = 0;
            var table_tr = {};
            
            for (var i = 0; i < options.length; i++) {
                
                create_tr(tr_index, table, data, options[i]);
                tr_index++;
                
            }
            
            addButton.onclick = function(){
                
                create_tr(tr_index, table, data, "");
                tr_index++;
                
            }
            
            value = valuePanel;
            
        } else {
            
            value = input.createInput(data.id, data, inputData, null);
            
        }
        //var inputPanel = object.createInputRowPanel(data.name, value, data.id, data.required, null);
        var th = document.createElement("th");
        th.setAttribute("scope", "row");
        th.textContent = data.name;
        var td = object.create('td', null, [value], null, null, null, null);
        var tr = object.create('tr', null, [th, td], "booking-package_input_" + key, null, null, null);
        tr.setAttribute("valign", "top");
        trList[key] = tr;
        table.appendChild(tr);
        
        
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
    
    function create_tr(tr_index, table, input, valueList){
        
        if (typeof valueList == "string") {
            
            valueList = [valueList];
            
        }
        object._console.log(valueList);
        
        var inputName = input.id;
        
        object._console.log(inputName);
        object._console.log(input);
        object._console.log(input.optionsType);
        object._console.log(valueList);
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
                textBox.onchange = function () {
                    
                    var dataKey = this.getAttribute("data-key");
                    var value = this.value;
                    var valueList = JSON.parse(inputData[inputName][dataKey].json);
                    for (var key in valueList) {
                        
                        if (document.getElementById(tr_index + "_" + key) == null) {
                            
                            continue;
                            
                        }
                        
                        var textValue = document.getElementById(tr_index + "_" + key).value;
                        value = value.replace(/\"/g, "'");
                        textValue = textValue.replace(/\"/g, "'");
                        valueList[key] = textValue;
                        
                    }
                    
                    var json = JSON.stringify(valueList);
                    inputData[inputName][dataKey].json = json;
                    inputData[inputName][dataKey].value = value;
                    object._console.log(valueList);
                    object._console.log(json);
                    object._console.log(inputData);
                    object._console.log("dataKey = " + dataKey + " value = " + value);
                    
                };
                
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
                
                selectBox.onchange = function () {
                    
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
                    
                };
                
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
            
            };
            
        }
        
    };
    
    addPanel.appendChild(table);
    
    var saveButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
    var cancelButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Cancel") );
    var buttonPanel = object.create('div', null, [saveButton, cancelButton], null, null, 'bottomButtonPanel', null);
    addPanel.appendChild(buttonPanel);
    
    window.scrollTo(0, addPanel.offsetTop);
    
    cancelButton.onclick = function(event){
        
        mainPanel.removeChild(addPanel);
        mainPanel.style.height = null;
        object._console.log("cancelButton closeHeghit = " + closeHeghit);
        callback("close");
        
    };

    saveButton.onclick = function(event){
        
        object._console.log(inputTypeList);
        object._console.log(inputData);
        const input = new Booking_Package_Input(object._debug);
        var response = null;
        var postData = {mode: mode, nonce: object._nonce, action: object._action};
        var jsonObj = {};
        for (let key in inputTypeList) {
            
            var parentPanel = document.getElementById('booking-package_input_' + key);
            parentPanel.classList.remove('errorPanel');
            const inputFormat = inputTypeList[key];
            jsonObj[key] = input.getUserFieldValue(inputFormat, inputData[key]);
            
        }
        
        const optionsPanel = document.getElementById('booking-package_input_options');
        if ((jsonObj.type == 'CHECK' || jsonObj.type == 'SELECT' || jsonObj.type == 'RADIO') && jsonObj.options.length === 0 && optionsPanel != null) {
            
            optionsPanel.classList.add('errorPanel');
            
        }
        
        postData.jsonStr = JSON.stringify(jsonObj);
        var loadingPanel = document.getElementById("loadingPanel");
        loadingPanel.classList.remove("hidden_panel");
        new Booking_App_XMLHttp(object._url, postData, false, function(json){
                
            if (json['status'] != 'error') {
                
                mainPanel.removeChild(addPanel);
                mainPanel.style.height = null;
                object._console.log(json);
                callback(json);
                
            } else {
                
                alert(json["message"]);
                
            }
            loadingPanel.classList.add("hidden_panel");
            
		});
    	
		
    };
}

Member_manage.prototype.getUserFieldValue = function (inputFormat, elements) {
    
    var object = this;
    var values = null;
    object._console.log(inputFormat);
    object._console.log(elements);
    if (inputFormat.type === 'TEXT' || inputFormat.type === 'TEXTAREA') {
        
        values = elements['textBox'].value.trim();
        
        
    } else if (inputFormat.type === 'CHECK') {
        
        values = (function (inputElements) {
            
            const values = [];
            for (var key in elements) {
                
                if (elements[key].checked === true) {
                    
                    values.push(elements[key].getAttribute("data-value"));
                    
                }
                
            }
            
            return values;
            
        })(elements);
        
    } else if (inputFormat.type === 'RADIO') {
        
        values = (function (elements) {
            
            const values = [];
            for (var key in elements) {
                
                if (elements[key].checked === true) {
                    
                    values.push(elements[key].getAttribute("data-value"));
                    
                }
                
            }
            
            return values;
            
        })(elements);
        
    } else if (inputFormat.type === 'SELECT') {
        
        var inputObject = elements['selectBox'];
        var index = inputObject.selectedIndex;
        if (inputObject.options[index] != null) {
            
            values = inputObject.options[index].value;
            
        }
        
        
    } else if (inputFormat.type === 'OPTION') {
        
        values = (function (elements) {
            
            const values = [];
            for (var key in elements) {
                
                if (elements[key].value != null && elements[key].value.trim().length !== 0) {
                    
                    values.push(elements[key].value.trim());
                    
                }
                
            }
            
            return values;
            
        })(elements);
        
        
    }
    
    return values;
    
}

Member_manage.prototype.createInputRowPanel = function (name, value, id, required, actionElement) {
    
    var object = this;
    if (typeof name == "string") {
        
        name = name.replace(/\\/g, "");
        
    }
    object._console.log(value);
    if (typeof value == "string") {
        
        value = value.replace(/\\/g, "");
        
    } else if (typeof value == 'object' && typeof value.join == 'function') {
        
        value = value.join('\n');
        
    }
    object._console.log(value);
    
    var namePanel = document.createElement("div");
    namePanel.setAttribute("class", "name");
    namePanel.textContent = name;
    if ( (typeof required == "string" && required == 'true') || (typeof required == "number" && required == 1) ) {
        
        namePanel.setAttribute("class", "name required");
        
    }
    
    var inputPanel = null;
    if (typeof value == 'string' || typeof value == 'number') {
        
        inputPanel = document.createElement("div");
        inputPanel.textContent = value;
        if (id != null) {
            
            inputPanel.id = id;
            
        }
        
    } else {
        
        inputPanel = value;
        if (id != null) {
            
            inputPanel.id = id;
            
        }
        
    }
    inputPanel.setAttribute("class", "value");
    var rowPanel = document.createElement("div");
    rowPanel.setAttribute("class", "row");
    rowPanel.appendChild(namePanel);
    rowPanel.appendChild(inputPanel);
    
    return rowPanel;
    
}

Member_manage.prototype.userSettingPanel = function () {
    
    var object = this;
    object._console.log('userSettingPanel');
    object._console.log(object._userFunctions);
    var table = object.create('table', null, null, null, null, 'form-table', null);
    var mainPanel = document.getElementById("userSettingPanel");
    mainPanel.textContent = null;
    mainPanel.appendChild(table);
    var saveButton = object.createButton(null, 'margin-right: 10px;', 'w3tc-button-save button-primary', null, object._i18n.get("Save") );
    var buttonPanel = object.create('div', null, [saveButton], null, null, 'bottomButtonPanel', null);
    mainPanel.appendChild(buttonPanel);
    
    for (var key in object._userFunctions) {
        
        const userFunction = object._userFunctions[key];
        object._console.log(userFunction);
        
        var settingNameSpan = object.create('span', userFunction.name, null, null, null, null, null);
		var th = object.create('th', null, [settingNameSpan], null, null, null, null);
		th.setAttribute("scope", "row");
        let extensionsValidPanel = object.create('div', object._i18n.get('Paid plan subscription required.'), null, null, null, 'extensionsValid hidden_panel', null);
        let valueName = object.create('span', object._i18n.get("Enabled"), null, null, null, 'radio_title', null);
        let checkBox = object.createInputElement('input', 'checkbox', userFunction.name, null, null, false, key, null, null, null);
        if (parseInt(userFunction.value) === 1) {
            
            checkBox.value = 1;
            checkBox.checked = true;
            
        }
        
        if (parseInt(object._isExtensionsValid) == 0) {
            
            extensionsValidPanel.classList.remove('hidden_panel');
            checkBox.disabled = true;
            saveButton.disabled = true;
            
        }
        
        const label = object.create('label', null, [extensionsValidPanel, checkBox, valueName], null, null, null, null);
        var td = object.create('td', null, [label], null, null, null, null);
        var tr = object.create('tr', null, [th, td], null, null, null, null);
		tr.setAttribute("valign", "top");
		table.appendChild(tr);
        object._console.log(label);
        
    }
    
    
    saveButton.onclick = function() {
        
        var postData = {mode: 'updateMemberSetting', nonce: object._nonce, action: object._action};
        for (var key in object._userFunctions) {
            
            object._console.log(key);
            const radioButton = document.getElementById(key);
             postData[key] = 0;
             if (radioButton.checked === true) {
                 
                 postData[key]  = 1;
                 
             }
            
        }
        
        
        object._console.log(postData);
        var loadingPanel = document.getElementById("loadingPanel");
        loadingPanel.classList.remove("hidden_panel");
        new Booking_App_XMLHttp(object._url, postData, false, function(json){
            
            loadingPanel.classList.add("hidden_panel");
            object._userFunctions = json;
            object.userSettingPanel();
            
        });
        
    };
    
}

Member_manage.prototype.start = function(){
    
    var object = this;
    var user_form = document.getElementById("booking-package-user-form");
    var user_edit_form = document.getElementById("booking-package-user-edit-form");
    var users = object.getUsers();
    for (var key in users) {
        
        var user = users[key];
        user.onclick = function() {
            
            object._console.log(this);
            var id = this.id;
            var userInfo = object.serachUser(id);
            object._console.log(userInfo);
            if (userInfo != 0) {
                
                object.editForm(this, userInfo);
                object._blockPanel.classList.remove("hidden_panel");
                if (object._contentPanel != null && document.getElementById("showUserInfo_blockPanel") != null) {
                    
                    object._contentPanel.removeChild(document.getElementById("showUserInfo_blockPanel"));
                    
                }
                object.bookedList(this, userInfo, 0);
                
                
            }
            
        };
        
    }
    
}

Member_manage.prototype.movePage = function(page, number, keywords){
    
    var object = this;
    object._console.log(page + " " + number);
    var offset = object._offset;
    number = parseInt(number);
    if(page == 'before_page'){
        
        if(offset > 0){
            
            offset = offset - object._limit;
            
        }
        
    }else if(page == "next_page"){
        
        offset = offset + object._limit;
        
    }
    
    object._console.log("offset = " + offset);
    
    object.loadingPanel(1);
    var user_edit_form = document.getElementById("booking-package-user-edit-form");
    var member_list_table = document.getElementById("member_list_table");
    var member_list_tbody = document.getElementById("member_list_tbody");
    var post = {mode: 'getMembers', nonce: object._nonce, action: object._action, page: page, number: number, offset: offset, authority: object._authority};
    keywords = object.getKeywords();
    if (keywords != null && keywords.length > 0) {
        
        post.keywords = keywords;
        
    }
    
    new Booking_App_XMLHttp(object._url, post, false, function(new_users){
        
        if (new_users.status == null && new_users.length != 0) {
            
            if (page != null) {
                
                object._limit = number;
                object._offset = offset;
                
            }
            
            var users = object.getUsers();
            object._console.log(users);
            for (var key in users) {
                
                var user = users[key];
                object._console.log(user);
                user.textContent = null;
                member_list_tbody.removeChild(user);
                
                
            }
            
            var userInfoList = {};
            var userList = {};
            for (var i = 0; i < new_users.length; i++) {
                
                var user = new_users[i];
                userInfoList["user_id_" + user.ID] = user;
                
                
                var idSpan = object.create('span', user.ID, null, null, null, 'userId', null);
                var tdId = object.create('td', null, [idSpan], null, null, null, null);
                object._console.log(user);
                if (parseInt(user.status) == 0) {
                    
                    var priority_highSpan = object.create('span', 'priority_high', null, null, null, 'material-icons priority_high', null);
                    tdId.appendChild(priority_highSpan);
                    
                }
                
                var tdName = object.create('td', user.user_login, null, null, null, null, null);
                var tdEmail = object.create('td', user.user_email, null, null, null, null, null);
                var tdDate = object.create('td', user.user_registered, null, null, null, null, null);
                var tr = object.create('tr', null, [tdId, tdName, tdEmail, tdDate], "user_id_" + user.ID, null, 'tr_user', null);
                member_list_tbody.appendChild(tr);
                userList["user_id_" + user.ID] = tr;
                tr.onclick = function(){
                    
                    var id = this.id;
                    var userInfo = object.serachUser(id);
                    object._console.log(userInfo);
                    if (userInfo != 0) {
                        
                        object.editForm(this, userInfo);
                        object._blockPanel.classList.remove("hidden_panel");
                        if (object._contentPanel != null && document.getElementById("showUserInfo_blockPanel") != null) {
                            
                            object._contentPanel.removeChild(document.getElementById("showUserInfo_blockPanel"));
                            
                        }
                        object.bookedList(this, userInfo, 0);
                        
                    }
                    
                };
                
            }
            
            object.setUsersInfo(userInfoList);
            object.setUsers(userList);
            
        }
        
        object.loadingPanel(0);
        
    });
    
    
};

Member_manage.prototype.bookedList = function(tr, userAccount, offset) {
    
    var object = this;
    object._console.log(userAccount);
    object._console.log("offset = " + offset);
    object._console.log(object._weekName);
    object._console.log(object._calendarAccountList);
    object._calendar = new Booking_App_Calendar(object._weekName, object._dateFormat, object._positionOfWeek, object._positionTimeDate, 0, object._i18n, object._debug);
    object._calendar.setClock(object._clock);
    object._editPanel.classList.remove("hidden_panel");
    var edit_title = document.getElementById("edit_title");
    edit_title.textContent = userAccount.user_login;
    
    var reservation_usersPanel = document.getElementById("reservation_usersPanel");
    reservation_usersPanel.classList.remove("hidden_panel");
    reservation_usersPanel.textContent = null;
    
    var user_detail_panel = document.getElementById("user_detail_panel");
    user_detail_panel.classList.add("hidden_panel");
    
    var leftButtonPanel = document.getElementById("leftButtonPanel");
    leftButtonPanel.classList.remove("hidden_panel");
    
    var rightButtonPanel = document.getElementById("rightButtonPanel");
    rightButtonPanel.classList.add("hidden_panel");
    
    var selectedTab = "booked_list";
    var booked_list_tab = document.getElementById("booked_list");
    booked_list_tab.classList.add("active");
    booked_list_tab.removeEventListener("click", null);
    booked_list_tab.onclick = function(){
        
        object._console.log(this.id);
        if (selectedTab != this.id) {
            
            selectedTab = this.id;
            booked_list_tab.classList.add("active");
            edit_user_tab.classList.remove("active");
            reservation_usersPanel.classList.remove("hidden_panel");
            user_detail_panel.classList.add("hidden_panel");
            leftButtonPanel.classList.remove("hidden_panel");
            rightButtonPanel.classList.add("hidden_panel");
            
        }
        
    };
    
    var edit_user_tab = document.getElementById("edit_user");
    edit_user_tab.classList.remove("active");
    edit_user_tab.removeEventListener("click", null);
    edit_user_tab.onclick = function(){
        
        object._console.log(this.id);
        if (selectedTab != this.id) {
            
            selectedTab = this.id;
            booked_list_tab.classList.remove("active");
            edit_user_tab.classList.add("active");
            reservation_usersPanel.classList.add("hidden_panel");
            user_detail_panel.classList.remove("hidden_panel");
            object.editUser(tr, userAccount);
            leftButtonPanel.classList.add("hidden_panel");
            rightButtonPanel.classList.remove("hidden_panel");
            
        }
        
    };
    
    var beforButton = document.getElementById("beforButton");
    beforButton.removeEventListener("click", null);
    
    var nextButton = document.getElementById("nextButton");
    nextButton.removeEventListener("click", null);
    
    var body = document.getElementsByTagName("body")[0];
    body.classList.add("modal-open");
    
    var post = {mode: 'getUsersBookedList', nonce: object._nonce, action: object._action, user_id: userAccount.ID, offset: offset, authority: object._authority};
    object.loadingPanel(1);
    new Booking_App_XMLHttp(object._url, post, false, function(response){
        
        object._console.log(response);
        var responseOffset = 0;
        var responseLimit = 0;
        var nextClick = 0;
        var table = object.create('table', null, null, null, null, 'wp-list-table widefat fixed striped', null);
        reservation_usersPanel.appendChild(table);
        if (response.status == 'success') {
            
            var bookedList = response.bookedList;
            responseOffset = parseInt(response.offset);
            responseLimit = parseInt(response.limit);
            nextClick = parseInt(response.next);
            for(var i = 0; i < bookedList.length; i++) {
            
                var userInfo = bookedList[i];
                object._console.log(userInfo);
                
                
                var th = object.create('td', userInfo.key, null, null, null, null, null);
                th.setAttribute("scope", "row");
                var dateTd = object.create('td', null, null, null, null, null, null);
                dateTd.setAttribute("scope", "row");
                dateTd.textContent = object._calendar.formatBookingDate(userInfo.date.month, userInfo.date.day, userInfo.date.year, userInfo.date.hour, userInfo.date.min, userInfo.scheduleTitle, userInfo.date.week, 'text');
                var accountTd = object.create('td', userInfo.accountKey, null, null, null, null, null);
                accountTd.setAttribute("scope", "row");
                
                var line_tr = object.create('tr', null, [th, dateTd, accountTd], null, null, null, {key: i} );
                line_tr.setAttribute("valign", "top");
                
                table.appendChild(line_tr);
                if (object._calendarAccountList[parseInt(userInfo.accountKey)] != null) {
                    
                    if (object._calendarAccountList[parseInt(userInfo.accountKey)].type == 'hotel') {
                        
                        dateTd.textContent = object._calendar.formatBookingDate(userInfo.date.month, userInfo.date.day, userInfo.date.year, null, null, null, userInfo.date.week, 'text');
                        
                    } else {
                        
                        dateTd.textContent = object._calendar.formatBookingDate(userInfo.date.month, userInfo.date.day, userInfo.date.year, userInfo.date.hour, userInfo.date.min, userInfo.scheduleTitle, userInfo.date.week, 'text');
                        
                    }
                    
                    accountTd.textContent = object._calendarAccountList[parseInt(userInfo.accountKey)].name;
                    
                }
                
                var status = object.create('div', object._i18n.get(userInfo.status.toLowerCase()), null, null, null, null, {key: i} );
                var statusClassName = "pendingLabel";
                if (userInfo.status.toLowerCase() == "approved") {
                    
                    statusClassName = "approvedLabel";
                    
                } else if (userInfo.status.toLowerCase() == "canceled") {
                    
                    statusClassName = "canceledLabel";
                    
                }
                status.classList.add(statusClassName);
                
                var td = object.create('td', null, [status], null, null, null, null);
                td.setAttribute("scope", "row");
                line_tr.appendChild(td);
                
                var statusClick = false;
                status.onclick = function(){
                    
                    var status = this;
                    var key = parseInt(this.getAttribute("data-key"));
                    object._console.log(key);
                    object._console.log(bookedList[key]);
                    if (bookedList[key].status != "canceled") {
                        
                        statusClick = true;
                        var emailEnableList = object._emailEnableList[parseInt(bookedList[key].accountKey)].emailMessageList;
                        object._console.log(emailEnableList);
                        object._console.log("enable = " + Boolean(parseInt(emailEnableList.mail_new_admin.enable)));
                        object._booking_manage.setEmailEnableList(emailEnableList);
                        object._booking_manage.changeStatus(bookedList[key].accountKey, bookedList[key], statusClick, false, function(response){
                            
                            object._console.log(bookedList[key]);
                            object._console.log(response);
                            statusClick = false;
                            if (response.status != null) {
                                
                                bookedList[key].status = response.status.toLowerCase();
                                var statusClassName = "pendingLabel";
                                if (response.status.toLowerCase() == "approved") {
                                    
                                    statusClassName = "approvedLabel";
                                    
                                } else if (response.status.toLowerCase() == "canceled") {
                                    
                                    statusClassName = "canceledLabel";
                                    
                                }
                                status.textContent = object._i18n.get(response.status.toLowerCase());
                                status.setAttribute("class", statusClassName);
                                
                            }
                            
                        });
                        
                    }
                    
                };
                
                line_tr.onclick = function(){
                    
                    if(statusClick == false){
                        
                        var key = parseInt(this.getAttribute("data-key"));
                        object._console.log(key);
                        object._console.log(bookedList[key]);
                        object.setSelectedKey(key);
                        object.showUserInfo(key, bookedList[key], true, bookedList[key].accountKey, function(response){
                            
                            object._console.log(response);
                            if(response.status == "returnButton"){
                                
                                object.reservation_users(reservation_usersPanel, response.month, response.day, response.year, calendarData, accountKey, callback);
                                
                            }else{
                                
                                //callback(response);
                                
                            }
                            
                        });
                        
                    }
                    
                };
                
            }
            
            beforButton.onclick = function() {
                
                offset = responseOffset - responseLimit;
                if (offset <= 0) {
                    
                    offset = 0;
                    
                }
                object._console.log("offset = " + offset);
                object._console.log(userAccount);
                object.bookedList(tr, userAccount, offset);
                
            };
            
            nextButton.onclick = function() {
                
                if (nextClick == 0) {
                    
                    return null;
                    
                }
                offset = responseOffset + responseLimit;
                object._console.log("offset = " + offset);
                object._console.log(userAccount);
                object.bookedList(tr, userAccount, offset);
                
            };
            
        }
        
        object.loadingPanel(0);
        
    });
    
    
};

Member_manage.prototype.showUserInfo = function(key, reservationData, animation, accountKey, callback) {
    
    var object = this;
    var options = "[]";
    object._buttonAction = "showUserInfo";
    object._console.log("key = " + key);
    object._console.log("buttonAction = " + object._buttonAction);
    object._console.log(reservationData);
    object._console.log(object._calendarAccountList);
    
    var calendarAccount = object._calendarAccountList[parseInt(reservationData.accountKey)];
    object._console.log(calendarAccount);
    var infoPanel = null;
    if (document.getElementById("userInfoPanel") == null) {
        
        var infoPanel = object.create('div', null, null, 'userInfoPanel', null, null, null);
        object._contentPanel.appendChild(infoPanel);
        
    } else {
        
        infoPanel = document.getElementById("userInfoPanel");
        
        
    }
    
    var blockPanel = null;
    if (animation === true) {
        
        infoPanel.setAttribute("class", "show_panel");
        blockPanel = object.create('div', null, null, 'showUserInfo_blockPanel', null, 'blockPanel', null);
        object._contentPanel.insertBefore(blockPanel, infoPanel);
        
    }
    
    if (blockPanel != null) {
        
        blockPanel.onclick = function(event) {
            
            object._console.log("blockPanel click");
            object._buttonAction = "reservation_users";
            if (document.getElementById("changePanel") != null) {
                
                document.getElementById("changePanel").setAttribute("class", "return_panel");
                
            }
            infoPanel.setAttribute("class", "return_panel");
            object._contentPanel.removeChild(blockPanel);
            
        };
        
    }
    
    var bookingTimeChange = object.create('div', object._i18n.get("Change"), null, null, null, 'change hidden_panel', {status: '1'} );
    var courseChange = object.create('div', object._i18n.get("Change"), null, null, null, 'change hidden_panel', {status: '1'} );
    var response = object._booking_manage.showUserDetails(key, {account: calendarAccount, schedule: {}}, reservationData, calendarAccount.key, false, infoPanel, bookingTimeChange, courseChange, function(callback){
        
        object._console.log(callback);
        
    });
    
    
};

Member_manage.prototype.lookingForUsers = function(number) {
    
    var object = this;
    var offset = 0;
    var search_users_text = document.getElementById("search_users_text");
    var search_user_button = document.getElementById("search_user_button");
    var clear_user_button = document.getElementById("clear_user_button");
    var input_bool = true;
    if (search_users_text == null) {
        
        return false;
        
    }
    
    
    search_users_text.onkeydown = function(event) {
        
        object._console.log(event.key);
        var text = this;
        if (event.key != null && event.key.toLocaleLowerCase() == 'enter') {
            
            send(text);
            
        }
        
    };
    
    search_user_button.onclick = function() {
        
        send(search_users_text);
        
    };
    
    clear_user_button.onclick = function() {
        
        search_users_text.value = null;
        var member_limit = document.getElementById("member_limit");
        var index = member_limit.selectedIndex;
        var limit = member_limit.options[index].value;
        object._offset = 0;
        object.setKeywords(null);
        object.movePage(null, limit, null);
        
    };
    
    function send(text) {
        
        var keywords = text.value;
        keywords = keywords.replace(/[\u{20}\u{3000}]/ug ,' ');
        keywords = keywords.replace(/[\x20\u3000]/g ,' ');
        keywords = keywords.trim(keywords);
        if (keywords.length == 0) {
            
            object.setKeywords(null);
            return null;
            
        } else {
            
            var member_limit = document.getElementById("member_limit");
            var index = member_limit.selectedIndex;
            var limit = member_limit.options[index].value;
            object._offset = 0;
            object.setKeywords(keywords);
            object.movePage(null, limit, keywords);
            
        }
        object._console.log(number);
        object._console.log(keywords);
        
    };
    
};

Member_manage.prototype.editUser = function(tr, user){
    
    var object = this;
    const userProfile = user.profile;
    object._console.log(user);
    object._console.log(userProfile);
    object._console.log(tr);
    object._console.log(object._userFormFields);
    const input = new Booking_Package_Input(object._debug);
    const tr_input_field_user_email = document.getElementById('profile_tr_input_field_user_email');
    const tr_input_field_user_pass = document.getElementById('profile_tr_input_field_user_pass');
    var user_login = document.getElementById("user_edit_login");
    var user_email = document.getElementById("user_edit_email");
    var user_status = document.getElementById("user_edit_status");
    var user_pass = document.getElementById("user_edit_pass");
    var user_pass_change_button = document.getElementById("user_edit_change_password_button");
    var upload_button = document.getElementById("edit_user_button");
    var delete_button = document.getElementById("edit_user_delete_button");
    
    user_pass.textContent = null;
    user_pass.classList.add("hidden_panel");
    user_pass_change_button.classList.remove("hidden_panel");
    
    user_login.textContent = user.user_login;
    user_email.value = user.user_email;
    if (parseInt(user.status) == 1) {
        
        user_status.checked = true;
        
    } else {
        
        user_status.checked = false;
        
    }
    
    var inputData = {};
    const formFields = JSON.parse(JSON.stringify(object._userFormFields));
    const custom_title = object.create('div', object._i18n.get('Custom form fields'), null, null, '', 'custom_title', null);
    if (object._managementUsersV2 === 0) {
        
        custom_title.textContent = null;
        
    }
    const table = input.createUserFieldPanel(formFields, userProfile, inputData, 'table', object._defaultName);
    table.classList.add('fixed');
    const customFormFieldPanel = document.getElementById('editCustomFormFieldPanel');
    customFormFieldPanel.textContent = null;
    customFormFieldPanel.appendChild(custom_title);
    customFormFieldPanel.appendChild(table);
    
    user_pass_change_button.onclick = function(event){
        
        user_pass.textContent = null;
        user_pass.classList.remove("hidden_panel");
        user_pass_change_button.classList.add("hidden_panel");
        
    };
    
    upload_button.onclick = function(event) {
        
        const response = input.validateInputValues(formFields, inputData);
        let updata = response.updata;
        let customUserFields = response.customUserFields;
        object._console.log(customUserFields);
        
        tr_input_field_user_email.classList.remove('errorPanel');
        tr_input_field_user_pass.classList.remove('errorPanel');
        
        user_pass.parentElement.classList.remove("errorPanel");
        if (!user_email.value.match(/.+@.+\..+/)) {
            
            updata = false;
            tr_input_field_user_email.classList.add("errorPanel");
            
        }
        
        if (user_pass.value.length > 0 && user_pass.value.length < 8) {
            
            object._console.log(user_pass.value);
            updata = false;
            tr_input_field_user_pass.classList.add("errorPanel");
            
        }
        
        var post = {mode: 'updateUser', nonce: object._nonce, action: object._action, user_login: user.user_login, customUserFields: JSON.stringify(customUserFields)};
        if (user_status.checked == true) {
            
            post.status = 1;
            
        } else {
            
            post.status = 0;
            
        }
        object._console.log(post);
        if (updata == true) {
            
            post.user_email = user_email.value;
            if (user_pass.value.length != 0) {
                
                post.user_pass = user_pass.value;
                
            }
            object._console.log(post);
            
            object.loadingPanel(1);
            new Booking_App_XMLHttp(object._url, post, false, function(response){
                
                object._console.log(response);
                if (response.status == 'success') {
                    
                    user.user_email = post.user_email;
                    user.status = post.status;
                    user.profile = customUserFields;
                    object._console.log(user);
                    /**
                    user.user_email = post.user_email;
                    user.status = post.status;
                    tr.textContent = null;
                    var tdList = ["ID", "user_login", "user_email", "user_registered"];
                    for (var i = 0; i < tdList.length; i++) {
                        
                        var td = object.create('td', null, null, null, null, null, null);
                        if (tdList[i] == "ID") {
                            
                            var idSpan = object.create('span', user[tdList[i]], null, null, null, 'userId', null);
                            td.appendChild(idSpan);
                            if (parseInt(user.status) == 0) {
                                
                                var priority_highSpan = object.create('span', 'priority_high', null, null, null, 'material-icons priority_high', null);
                                td.appendChild(priority_highSpan);
                                
                            }
                            
                        } else {
                            
                            td.textContent = user[tdList[i]];
                            
                        }
                        
                        tr.appendChild(td);
                        
                    }
                    **/
                    
                } else if (response.status == 'error') {
                    
                    window.alert(response.error_messages);
                    
                }
                
                object.loadingPanel(0);
                
            });
            
        }
        
    };
    
    delete_button.onclick = function(event){
        
        var post = {mode: 'deleteUser', nonce: object._nonce, action: object._action, user_login: user.user_login};
        if(window.confirm(object._i18n.get('Do you delete the "%s"?', [user.user_login]))){
            
            object._console.log(post);
            object.loadingPanel(1);
            new Booking_App_XMLHttp(object._url, post, false, function(response){
                
                object._console.log(response);
                if(response.status == 'success'){
                    
                    object._editPanel.classList.add("hidden_panel");
                    object._blockPanel.classList.add("hidden_panel");
                    var body = document.getElementsByTagName("body")[0];
                    body.classList.remove("modal-open");
                    
                    var member_limit = document.getElementById("member_limit");
                    var index = member_limit.selectedIndex;
                    var limit = member_limit.options[index].value;
                    object.movePage(null, limit, null);
                    
                }else if(response.status == 'error'){
                    
                    window.alert(response.error_messages);
                    
                }
                
                object.loadingPanel(0);
                
            });
            
        }
        
    };
    
};

Member_manage.prototype.editForm = function(tr, user){
    
    var object = this;
    object._console.log('editForm');
    object._console.log(user);
    var tabFrame = document.getElementById("booking-package-tabFrame");
    var user_profile_tab = document.getElementById("booking-package-user_profile_tab");
    var user_subscribed_tab = document.getElementById("booking-package-user_subscribed_tab");
    var user_profile = document.getElementById("booking-package-user-profile");
    var user_subscribed = document.getElementById("booking-package-user-subscribed");
    var user_subscribed_tbody = document.getElementById("booking-package-user_subscribed_tbody");
    
    var user_edit_form = document.getElementById("booking-package-user-edit-form");
    var user_login = document.getElementById("booking-package-user_edit_login");
    var user_email = document.getElementById("booking-package-user_edit_email");
    var user_status = document.getElementById("booking-package-user_edit_status");
    var user_pass = document.getElementById("booking-package-user_edit_pass");
    var user_pass_change_button = document.getElementById("booking-package-user_edit_change_password_button");
    var upload_button = document.getElementById("booking-package-edit_user_button");
    var delete_button = document.getElementById("booking-package-edit_user_delete_button");
    var user_form_close = document.getElementById("booking-package-edit_user_return_button");
    var user_edit_form_close = document.getElementById("booking-package-edit_user_delete_button");
    
    tabFrame.classList.remove("hidden_panel");
    user_profile_tab.classList.add("active");
    user_subscribed_tab.classList.remove("active");
    user_profile.classList.remove("hidden_panel");
    user_subscribed.classList.add("hidden_panel");
    user_form_close.classList.remove("hidden_panel");
    user_edit_form_close.classList.remove("hidden_panel");
    
    user_pass_change_button.setAttribute("class", "w3tc-button-save button-primary");
    upload_button.setAttribute("class", "w3tc-button-save button-primary sendButton");
    user_pass.classList.add("hidden_panel");
    user_pass_change_button.classList.remove("hidden_panel");
    
    delete_button.setAttribute("class", "w3tc-button-save button-primary return_button");
    
    var tabs = {user_profile_tab: {tab: user_profile_tab, panel: user_profile, options: [upload_button, delete_button]}, user_subscribed_tab: {tab: user_subscribed_tab, panel: user_subscribed, options: []}};
    for(var key in tabs){
        
        var menu = tabs[key];
        menu.tab.onclick = function(){
            
            var id = this.id;
            object._console.log(id);
            for(var key in tabs){
                
                var menu = tabs[key];
                var options = menu.options;
                if(menu.tab.id == id){
                    
                    menu.tab.classList.add("active");
                    menu.panel.classList.remove("hidden_panel");
                    for(var i = 0; i < options.length; i++){
                        
                        options[i].classList.remove("hidden_panel");
                        
                    }
                    
                }else{
                    
                    menu.tab.classList.remove("active");
                    menu.panel.classList.add("hidden_panel");
                    for(var i = 0; i < options.length; i++){
                        
                        options[i].classList.add("hidden_panel");
                        
                    }
                    
                }
                
            }
            
        };
        
    }
    
    user_login.value = user.user_login;
    user_email.value = user.user_email;
    if(parseInt(user.status) == 1){
        
        user_status.checked = true;
        
    }else{
        
        user_status.checked = false;
        
    }
    
    user_subscribed_tbody.textContent = null;
    var cost = new FORMAT_COST(object._i18n, object._debug, object._numberFormatter, object._currency_info);
    var subscription_list = user.subscription_list;
    if(subscription_list == null){
        
        subscription_list = {};
        
    }
    object._console.log(subscription_list);
    if(Object.keys(subscription_list).length == 0){
        
        user_subscribed_tbody.textContent = object._i18n.get("There are no items subscribed.");
        
    }
    
    for (var key in subscription_list) {
        
        var product = subscription_list[key];
        var items = product.items;
        var currency = null;
        var amount = 0;
        object._console.log(product);
        for (var i = 0; i < items.length; i++) {
            
            var item = items[i];
            currency = item.currency;
            amount += parseInt(item.amount);
            object._console.log(item);
            
        }
        
        amount = cost.formatCost(amount, currency);
        var amountPanel = object.create('div', object._i18n.get("%s per month", [amount]), null, null, null, null);
        var expirationDate = object.create('div', object._i18n.get("Expiration date: %s", [product.period_end_date]), null, null, null, null, null);
        var itemPanel = object.create('div', product.name, null, null, null, null, null);
        var itemTd = object.create('td', null, [itemPanel, amountPanel, expirationDate], null, null, null, null);
        var deleteTd = object.create('td', 'delete', null, null, "font-family: 'Material Icons' !important;", 'material-icons delete_icon', {key: key} );
        var tr = object.create('tr', null, [itemTd, deleteTd], object._prefix + key, null, null, null);
        if (product.canceled != null && parseInt(product.canceled) == 1) {
            
            tr.classList.add("canceled");
            deleteTd.setAttribute("style", "cursor: not-allowed; font-family: 'Material Icons' !important;");
            
        }
        
        
        user_subscribed_tbody.appendChild(tr);
        
        deleteTd.onclick = function(){
            
            var deleteTd = this;
            var key = this.getAttribute("data-key");
            var product = subscription_list[key];
            object._console.log(product);
            if(product.canceled != null && parseInt(product.canceled) == 1){
                
                return null;
                
            }
            
            if(window.confirm(object._i18n.get('If you cancel the "%s" subscription, you can not make a reservation after the deadline of %s. Do you really want to cancel the subscription?', [product.name, product.period_end_date]))){
                
                deleteSubscription(user, product, function(response){
                    
                    var tr = document.getElementById(object._prefix + key);
                    if(response.status == 1){
                        
                        subscription_list[key]['canceled'] = 1;
                        tr.classList.add("canceled");
                        deleteTd.setAttribute("style", "cursor: not-allowed; font-family: 'Material Icons' !important;");
                        
                    }else{
                        
                        window.alert(response.error);
                        delete subscription_list[key];
                        user_subscribed_tbody.removeChild(tr);
                        
                    }
                    
                });
                
            }
            
        };
        
    }
    
    var deleteSubscription = function(user, product, callback){
        
        object._console.log(user);
        object._console.log(product);
        
        var post = {mode: 'deleteSubscription', nonce: object._nonce, action: object._action, userId: user.ID, product: product.product};
        object._console.log(post);
        
        object.loadingPanel(1);
        new Booking_App_XMLHttp(object._url, post, false, function(response){
            
            object._console.log(response);
            if(parseInt(response.status) == 1){
                
                callback({stats: 1, error: null});
                
            }else{
                
                callback({stats: 0, error: response.error});
                
            }
            
            object.loadingPanel(0);
            
        });
        
    };
    
    user_pass_change_button.onclick = function(event){
        
        user_pass.classList.remove("hidden_panel");
        user_pass_change_button.classList.add("hidden_panel");
        
    };
    
    upload_button.onclick = function(event){
        
        var updata = true;
        user_email.parentElement.classList.remove("errorPanel");
        user_pass.parentElement.classList.remove("errorPanel");
        if(!user_email.value.match(/.+@.+\..+/)){
            
            updata = false;
            user_email.parentElement.classList.add("errorPanel");
            
        }
        
        if(user_pass.value.length > 0 && user_pass.value.length < 8){
            
            updata = false;
            user_pass.parentElement.classList.add("errorPanel");
            
        }
        
        var post = {mode: 'updateUser', nonce: object._nonce, action: object._action, user_login: user.user_login};
        if(user_status.checked == true){
            
            post.status = 1;
            
        }else{
            
            post.status = 0;
            
        }
        
        
        if(updata == true){
            
            post.user_email = user_email.value;
            if(user_pass.value.length != 0){
                
                post.user_pass = user_pass.value;
                
            }
            object._console.log(post);
            
            object.loadingPanel(1);
            new Booking_App_XMLHttp(object._url, post, false, function(response){
                
                object._console.log(response);
                if(response.status == 'success'){
                    
                    user.user_email = post.user_email;
                    user.status = post.status;
                    tr.textContent = null;
                    var tdList = ["ID", "user_login", "user_email", "user_registered"];
                    for(var i = 0; i < tdList.length; i++){
                        
                        var td = object.create('td', null, null, null, null, null, null);
                        if(tdList[i] == "ID"){
                            
                            var idSpan = object.create('span', user[tdList[i]], null, null, null, 'userId', null);
                            td.appendChild(idSpan);
                            if(parseInt(user.status) == 0){
                                
                                var priority_highSpan = object.create('span', 'priority_high', null, null, null, 'material-icons priority_high', null);
                                td.appendChild(priority_highSpan);
                                
                            }
                            
                        }else{
                            
                            td.textContent = user[tdList[i]];
                            
                        }
                        
                        tr.appendChild(td);
                        
                    }
                    
                }
                
                user_edit_form.classList.add("hidden_panel");
                object._blockPanel.classList.add("hidden_panel");
                object.loadingPanel(0);
                
            });
            
        }
        
    };
    
    delete_button.onclick = function(){
        
        var post = {mode: 'deleteUser', nonce: object._nonce, action: object._action, user_login: user.user_login};
        if(window.confirm(object._i18n.get('Do you delete the "%s"?', [user.user_login]))){
            
            object._console.log(post);
            object.loadingPanel(1);
            new Booking_App_XMLHttp(object._url, post, false, function(response){
                
                object._console.log(response);
                if(response.status == 'success'){
                    
                    user_edit_form.classList.add("hidden_panel");
                    object._blockPanel.classList.add("hidden_panel");
                    
                    var member_limit = document.getElementById("member_limit");
                    var index = member_limit.selectedIndex;
                    var limit = member_limit.options[index].value;
                    object.movePage(null, limit, null);
                    
                }else if(response.status == 'error'){
                    
                    window.alert(response.error_messages);
                    
                }
                
                object.loadingPanel(0);
                
            });
            
        }
        
    };
    
};

Member_manage.prototype.userForm = function(){
    
    var object = this;
    object._console.log("userForm");
    if (parseInt(object._isExtensionsValid) == 1) {
        
        const input = new Booking_Package_Input(object._debug);
        const documentHeight = document.documentElement.clientHeight - 240;
        const customFormFieldPanel = document.getElementById('addCustomFormFieldPanel');
        customFormFieldPanel.setAttribute('style', 'max-height: ' + documentHeight + 'px;');
        object._console.log('documentHeight = ' + documentHeight + 'px');
        object._console.log(customFormFieldPanel);
        
        
        document.getElementById("booking-package-user_regist_message").textContent = null;
        var user_form = document.getElementById("booking-package-user-form");
        user_form.classList.remove("hidden_panel");
        var user_form_close = document.getElementById("booking-package-edit_user_return_button");
        user_form_close.classList.remove("hidden_panel");
        object._blockPanel.classList.remove("hidden_panel");
        var register_button = document.getElementById("booking-package-register_user_button");
        register_button.setAttribute("class", "w3tc-button-save button-primary sendButton");
        //document.getElementById("booking-package-user_pass").autocomplete = 'new-password';
        
        var inputData = {};
        const formFields = JSON.parse(JSON.stringify(object._userFormFields));
        object._console.log(formFields);
        const custom_title = object.create('div', object._i18n.get('Custom form fields'), null, null, '', 'custom_title', null);
        if (object._managementUsersV2 === 0) {
            
            custom_title.textContent = null;
            
        }
        const table = input.createUserFieldPanel(formFields, {}, inputData, 'table', {});
        object._console.log(table);
        const addCustomFormFieldPanel = document.getElementById('addCustomFormFieldPanel');
        addCustomFormFieldPanel.textContent = null;
        addCustomFormFieldPanel.appendChild(table);
        
        
        document.getElementById("input_user_pass").type = 'text';
        document.getElementById("input_user_pass").value = null;
        register_button.onclick = function() {
            
            
            const response = input.validateInputValues(formFields, inputData);
            let updata = response.updata;
            let customUserFields = response.customUserFields;
            object._console.log(response);
            if (updata === false) {
                
                return null;
                
            }
            
            var user_data = {
                user_login: document.getElementById("input_user_login").value,
                user_email: document.getElementById("input_user_email").value,
                user_pass: document.getElementById("input_user_pass").value,
            };
            user_data['user_pass'].type = 'text';
            object._console.log(user_data['user_pass']);
            var registering = true;
            var post = {mode: 'createUser', nonce: object._nonce, action: object._action, user_login: user_data.user_login, user_email: user_data.user_email, user_pass: user_data.user_pass, customUserFields: JSON.stringify(customUserFields)};
            for(var key in user_data){
                
                let errorPanel = null;
                var value = user_data[key];
                object._console.log(key + " = " + value);
                
                if (key == 'user_login' && (value.length < 4 || !value.match(/^[A-Za-z0-9.-_]*$/))) {
                    
                    registering = false;
                    errorPanel = document.getElementById('tr_input_field_user_login');
                    console.error("error key = " + key + " value = " + value);
                    
                }
                
                if (key == 'user_email' && !value.match(/.+@.+\..+/)) {
                    
                    registering = false;
                    errorPanel = document.getElementById('tr_input_field_user_email');
                    console.error("error key = " + key + " value = " + value);
                    
                }
                
                if (key == 'user_pass' && value.length < 8) {
                    
                    registering = false;
                    errorPanel = document.getElementById('tr_input_field_user_pass');
                    
                    console.error("error key = " + key + " value = " + value);
                    
                }
                
                if (registering === false) {
                    
                    errorPanel.classList.add('errorPanel');
                    errorPanel.scrollIntoView();
                    break;
                    
                }
                
            }
            
            object._console.log(post);
            if (registering == true) {
                
                object._console.log(post);
                object.loadingPanel(1);
                new Booking_App_XMLHttp(object._url, post, false, function(response) {
                    
                    object._console.log(response);
                    if (response.status == 'success') {
                        
                        user_form.classList.add("hidden_panel");
                        object._blockPanel.classList.add("hidden_panel");
                        
                        var member_limit = document.getElementById("member_limit");
                        var index = member_limit.selectedIndex;
                        var limit = member_limit.options[index].value;
                        object.movePage(null, limit, null);
                        
                    } else if (response.status == 'error') {
                        
                        window.alert(response.error_messages);
                        
                    }
                    
                    object.loadingPanel(0);
                    
                });
                
            }
            
        }
        
    }
    
    
};

Member_manage.prototype.create = function(elementType, text, childElements, id, style, className, data_x) {
    
    var panel = this._element.create(elementType, text, childElements, id, style, className, data_x);
    return panel;
    
};

Member_manage.prototype.createButtonPanel = function(id, style, className, buttons) {
    
    var buttonPanel = this._element.createButtonPanel(id, style, className, buttons);
    return buttonPanel;
    
};

Member_manage.prototype.createButton = function(id, style, className, data_x, text) {
    
    var button = this._element.createButton(id, style, className, data_x, text);
    return button;
    
};

Member_manage.prototype.createInputElement = function(tagName, type, name, value, text, disabled, id, style, className, data_x) {
    
    var input = this._element.createInputElement(tagName, type, name, value, text, disabled, id, style, className, data_x);
    return input;
    
};