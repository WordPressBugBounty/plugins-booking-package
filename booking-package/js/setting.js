/* globals Booking_App_XMLHttp */
/* globals scriptError */
/* globals Booking_App_Calendar */
/* globals FORMAT_COST */
/* globals Booking_Package_Console */
/* globals Booking_Package_Input */
/* globals Confirm */
/* globals Booking_Package_DatabaseUpdateErrors */
/* globals Booking_Package_Elements */

/**
import { EditorState } from "https://cdn.skypack.dev/@codemirror/state";
import { EditorView, keymap } from "https://cdn.skypack.dev/@codemirror/view";
import { defaultKeymap } from "https://cdn.skypack.dev/@codemirror/commands"
//import { css } from "https://cdn.skypack.dev/@codemirror/lang-css";
**/

var setting_data = setting_data;
var booking_package_dictionary = booking_package_dictionary;

document.addEventListener('DOMContentLoaded', function() {
    
    window.addEventListener('load', function(){
        
        if (setting_data != null && booking_package_dictionary != null) {
	    	
	    	var setting = new Booking_Package_Settings(setting_data, booking_package_dictionary, false);
	    	setting.loadTabFrame();
	    	
	    }
        
    });
    
});
/**
window.addEventListener('load', function(){
	
	if(setting_data != null && booking_package_dictionary != null){
    	
    	var setting = new SETTING(setting_data, booking_package_dictionary, false);
    	setting.loadTabFrame();
    	
    }
	
});
**/
window.addEventListener('error', function(event) {
    
    var error = new scriptError(setting_data, booking_package_dictionary, event.message, event.filename, event.lineno, event.colno, event.error, false);
    error.send();
    
}, false);

	function Booking_Package_Settings(setting_data, booking_package_dictionary, webApp) {
		
		var object = this;
	    this._debug = new Booking_Package_Console(setting_data.debug);
	    this._console = {};
	    this._console.log = this._debug.getConsoleLog();
	    this._element = new Booking_Package_Elements(setting_data.debug);
		this._webApp = webApp;
		this._setting_data = setting_data;
		this._url = setting_data['url'];
		this._nonce = setting_data['nonce'];
		this._action = setting_data['action'];
		this._settingList = setting_data['list'];
		this._bookingSyncList = setting_data['bookingSyncList'];
		this._isExtensionsValid = parseInt(setting_data.isExtensionsValid);
		this._siteToken = setting_data.siteToken;
		this._startOfWeek = setting_data.startOfWeek;
		this._function = {name: "root", post: {}};
		this._url = setting_data['url'];
		this._is_owner_site = parseInt(setting_data.is_owner_site);
		this._countCssPanel = 0;
		this._countJavascriptPanel = 0;
		this._jsEditor = null;
		this._tab = null;
		this._prefix = setting_data.prefix;
		this._my_subscription_url = setting_data.my_subscription_url
		if (setting_data.tab != null) {
			
			this._tab = setting_data.tab;
			
		}
		
		object._console.log(setting_data);
		
		
		
		this._getSubscription = document.createElement('div');
		if (document.getElementById('booking_packaeg_paid_subscription') != null) {
			
			this._getSubscription = document.getElementById('booking_packaeg_paid_subscription');
			if (document.getElementById('upgradeSubmit') != null) {
				
				document.getElementById('upgradeSubmit').setAttribute('style', 'margin-left: 10px;');
				
			}
			object._console.log(this._getSubscription);
			
		}
		
		if (document.getElementById('booking_package_databaseUpdateErrors') != null) {
			
			object._console.log(document.getElementById('booking_package_databaseUpdateErrors'));
			var databaseUpdateErrors = new Booking_Package_DatabaseUpdateErrors(document.getElementById('booking_package_databaseUpdateErrors'));
			
		}
		
		this._i18n = new I18n(setting_data.locale);
		this._i18n.setDictionary(booking_package_dictionary);
		
		this._blockPanel = document.getElementById("blockPanel");
		this._editPanel = document.getElementById("editPanel");
		this._loadingPanel = document.getElementById("loadingPanel");
		
		
		this._timezoneGroup = document.getElementById("timezone_choice").getElementsByTagName("optgroup");
		this._timezoneGroup = [].slice.call(this._timezoneGroup);
		this._timezoneGroup.pop();
		this._timezoneOptions = document.getElementById("timezone_choice").getElementsByTagName("option");
		this._blockPanel.onclick = function(){
	    	
	    	this.editPanelShow(false);
	    	
		}
		
		document.getElementById("media_modal_close").onclick = function(){
	    	
	    	this.editPanelShow(false);
	    
		}
		
	};
	
	Booking_Package_Settings.prototype.setFunction = function(name, post){
        
        this._function = {name: name, post: post};
        
    };
    
    Booking_Package_Settings.prototype.getFunction = function(){
        
        return this._function;
        
    };
	
	Booking_Package_Settings.prototype.loadTabFrame = function(){
		
		var object = this;
		
		
		var menuList = {settingLink: 'settingPanel', holidayLink: 'holidayPanel', nationalHolidayLink: 'nationalHolidayPanel', blockEmailListsLink: 'blockEmailListsPanel', memberLink: "memberPanel", /**formLink: 'formPanel', courseLink: 'coursePanel', emailLink: 'emailPanel',**/ syncLink: "syncPanel", cssLink: "cssPanel", javascriptLink: "javascriptPanel", subscriptionLink: 'subscriptionPanel'};
		
		if (object._is_owner_site == 0) {
			
			delete menuList.subscriptionLink;
			object._tab = null;
			var subscriptionLink = document.getElementById("subscriptionLink");
			subscriptionLink.textContent = null;
			subscriptionLink.setAttribute("class", "");
			
		}
		
		object.createSettingPanel();
		if (object._tab == 'subscriptionLink') {
			
			object.subscriptionDiteilPanel();
			document.getElementById('settingLink').setAttribute('class', 'menuItem');
			document.getElementById('settingPanel').setAttribute('class', 'hidden_panel');
			document.getElementById('subscriptionLink').setAttribute('class', 'menuItem active');
			document.getElementById('subscriptionPanel').classList.remove('hidden_panel');
			
		}
		
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
					if (clickKey == key) {
						
						link.setAttribute("class", "menuItem active");
						panel.setAttribute("class", "");
						
						if (clickKey == 'formLink') {
							
							object.createFormPanel();
							
						} else if (clickKey == 'courseLink') {
							
							object.createCoursePanel();
							
						} else if (clickKey == 'emailLink') {
							
							object.emailSettingPanel();
							
						} else if (clickKey == 'subscriptionLink') {
							
							object.subscriptionDiteilPanel();
							
						} else if (clickKey == 'syncLink') {
							
							object.syncPanel();
							
						} else if (clickKey == 'memberLink') {
							
							object.memberPanel();
							
						} else if (clickKey == 'holidayLink') {
							
							object.holidayPanel("share");
							
						} else if (clickKey == 'nationalHolidayLink') {
							
							object.holidayPanel("national");
							
						} else if (clickKey == 'cssLink') {
							
							object.cssPanel();
							
						} else if (clickKey == 'javascriptLink') {
							
							object.javascriptPanel();
							
						} else if (clickKey == 'blockEmailListsLink') {
							
							
							var loadingPanel = document.getElementById("loadingPanel");
							loadingPanel.classList.remove("hidden_panel");
							var postData = {mode: "getBlockEmailLists", nonce: object._nonce, action: object._action};
							var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
								
								object._console.log(json);
								object.blockEmailListsPanel(json);
								loadingPanel.classList.add("hidden_panel");
								
							});
							
						}
						
					}else{
						
						link.setAttribute("class", "menuItem");
						panel.setAttribute("class", "hidden_panel");
						
					}
					
				}
				
			};
			
		}
		
	};
	
	Booking_Package_Settings.prototype.subscriptionDiteilPanel = function(){
		
		var object = this;
		//var upgrade = new Upgrade();
		object._console.log(object._setting_data.customer_id_for_subscriptions);
		object._console.log(object._setting_data.id_for_subscriptions);
		object._loadingPanel.classList.add("hidden_panel");
		var showBool = true;
		if (object._setting_data.customer_id_for_subscriptions == null && object._setting_data.id_for_subscriptions == null) {
			
			showBool = false;
			
		}
		
		var subscriptionData = {
			subscription_statu: object._i18n.get(object._setting_data.subscription_status),
			customer_id_for_subscriptions: object._setting_data.customer_id_for_subscriptions,
			id_for_subscriptions: object._setting_data.id_for_subscriptions,
			/** invoice_id_for_subscriptions: object._setting_data.invoice_id_for_subscriptions, **/
			expiration_date_for_subscriptions: object._setting_data.expiration_date_for_subscriptions,
			expiration_date: object._setting_data.expiration_date,
			customer_email_for_subscriptions: object._setting_data.customer_email_for_subscriptions
		};
		
		var nameList = {
			subscription_statu: object._i18n.get('Subscription status'),
			customer_id_for_subscriptions: object._i18n.get("ID"),
			id_for_subscriptions: object._i18n.get("Subscription ID"),
			customer_email_for_subscriptions: object._i18n.get("Email address"),
			expiration_date: object._i18n.get("Expiration date"),
			
		};
		object._console.log(subscriptionData);
		
		var subscriptionPanel = document.getElementById("subscriptionPanel");
		subscriptionPanel.textContent = null;
		
		var canceledMessage = object.create('div', object._i18n.get('The subscription has been canceled, so the paid subscription will end after the current expiration date unless this plugin is uninstalled.'), null, null, 'color: #ff3333;', null, null);
		var resetSubscriptionMessage = object.create('div', object._i18n.get('Reset the subscription immediately.'), null, null, 'color: #ff3333; text-decoration: underline; cursor: pointer;', null, null);
		var table = object.create('table', null, null, 'subscriptionTable', null, 'emails_table table_option wp-list-table widefat fixed striped', null);
		subscriptionPanel.appendChild(table);
		
		for (var key in nameList) {
			
			var valueLabel = object.create('div', subscriptionData[key], null, null, null, null, null);
			var nameTh = object.create('th', nameList[key], null, null, null, null, null);
			var valueTd = object.create('td', null, [valueLabel], null, null, null, null);
			if (showBool == false && key !== 'subscription_statu') {
				
				valueTd.textContent = "";
				
			}
			
			if (key === 'expiration_date' && object._setting_data.subscription_status === 'Canceled') {
				
				valueTd.appendChild(canceledMessage);
				valueTd.appendChild(resetSubscriptionMessage);
				
			}
			
			var tr = object.create('tr', null, [nameTh, valueTd], null, null, null, null);
			table.appendChild(tr);
			
		}
		
		var cancelSubscription = object.createButton(null, null, 'media-button button-primary button-large media-button-insert deleteButton', null, this._i18n.get("Cancel my subscription") );
		var updateSubscriptionPayments = object.createButton(null, 'margin-left: 10px;', 'w3tc-button-save button-primary', null, this._i18n.get("My billing") );
		var updateSubscription = object.createButton(null, 'margin-left: 10px;', 'w3tc-button-save button-primary', null, this._i18n.get("Update my subscription") );
		
		var getSubscription = null;
		if (this._getSubscription != null) {
			
			getSubscription = this._getSubscription;
			
		}
		
		var buttonPanel = object.create('div', null, null, null, null, 'buttonPanel', null);
		if (showBool == true) {
			
			buttonPanel.appendChild(updateSubscriptionPayments);
			
		}
		buttonPanel.appendChild(updateSubscription);
		if (showBool === false) {
			
			getSubscription.classList.remove('hidden_panel');
			buttonPanel.appendChild(getSubscription);
			
		}
		
		subscriptionPanel.appendChild(buttonPanel);
		if (showBool == true) {
			
			buttonPanel.appendChild(cancelSubscription);
			
		}
		
		resetSubscriptionMessage.onclick = function(event) {
			
			var confirm = new Confirm(object._debug);
			confirm.dialogPanelShow(object._i18n.get("Warning"), object._i18n.get("Resetting the subscription will deactivate all premium features. Are you sure you want to reset the subscription?"), false, 0, function(response) {
				
				object._console.log(response);
				if (response == true) {
					
					object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
					var postData = {mode: "resetSubscription", nonce: object._nonce, action: object._action, customer_id: subscriptionData.customer_id_for_subscriptions, subscriptions_id: subscriptionData.id_for_subscriptions};
					
					var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
						
						if (json['status'] === true) {
							
							object.subscriptionDiteilPanel();
							window.location.replace(object._setting_data.subscriptionLink);
							
						}
						object._loadingPanel.setAttribute("class", "hidden_panel");
						
					});
					
				}
				
			});
			
		};
		
		cancelSubscription.onclick = function(event){
			
			object._console.log(subscriptionData);
			
			var confirm = new Confirm(object._debug);
			confirm.dialogPanelShow(object._i18n.get("Attention"), object._i18n.get("Do you really cancel the subscription?"), false, 0, function(response) {
				
				object._console.log(response);
				if (response == true) {
					
					object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
					var postData = {mode: "upgradePlan", type: "delete", nonce: object._nonce, action: object._action, customer_id: subscriptionData.customer_id_for_subscriptions, subscriptions_id: subscriptionData.id_for_subscriptions, delete_customer_id: 1, subscription_mode: 'cancelSubscription'};
					object.setFunction("subscriptionDiteilPanel", postData);
					var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
						
						if (json['status'] != 'error') {
							
							object.subscriptionDiteilPanel();
							window.location.replace(object._setting_data.subscriptionLink);
							//window.location.reload();
							
						}
						object._loadingPanel.setAttribute("class", "hidden_panel");
						
					});
					
				}
				
			});
			
		};
		
		updateSubscription.onclick = function() {
			
			updateSubscriptionPayments.classList.add("hidden_panel");
			table.classList.add("hidden_panel");
			updateSubscription.classList.add("hidden_panel");
			cancelSubscription.classList.add("hidden_panel");
			if (getSubscription != null) {
				
				getSubscription.classList.add('hidden_panel');
				
			}
			
			var updateTable = object.create('table', null, null, null, null, 'emails_table table_option wp-list-table widefat fixed striped', null);
			subscriptionPanel.appendChild(updateTable);
			
			var subscriptionData = {
				customer_id_for_subscriptions: null,
				subscriptions_id_for_subscriptions: null,
			};
			
			var nameList = {
				customer_id_for_subscriptions: object._i18n.get("ID"),
				subscriptions_id_for_subscriptions: object._i18n.get("Subscription ID"),
			};
			
			for (var key in nameList) {
				
				var nameTh = object.create('th', nameList[key], null, null, null, null, null);
				var input = object.createInputElement('input', 'text', null, null, null, null, key, null, 'regular-text', null);
				
				if (key == 'customer_id_for_subscriptions') {
					
					input.value = object._setting_data.customer_id_for_subscriptions;
					subscriptionData[key] = object._setting_data.customer_id_for_subscriptions;
					
				} else if (key == 'subscriptions_id_for_subscriptions') {
					
					input.value = object._setting_data.id_for_subscriptions;
					subscriptionData[key] = object._setting_data.id_for_subscriptions;
					
				}
				
				input.onchange = function() {
					
					var input = this;
					subscriptionData[input.id] = input.value;
					
				};
				
				var valueTd = object.create('td', null, [input], null, null, null, null);
				var tr = object.create('tr', null, [nameTh, valueTd], null, null, null, null);
				updateTable.appendChild(tr);
				
			}
			
			var updateSubscriptionButton = object.createButton(null, 'margin-top: 1em;', 'w3tc-button-save button-primary tokenButton', null, object._i18n.get("Update") );
			subscriptionPanel.appendChild(updateSubscriptionButton);
			updateSubscriptionButton.onclick = function() {
				
				object._console.log(subscriptionData);
				var postData = {mode: "lookingForSubscription", nonce: object._nonce, action: object._action, url: this._url};
				var send = true;
				for (var key in subscriptionData) {
					
					if (subscriptionData[key] == null || subscriptionData[key].length == 0) {
						
						send = false;
						window.alert(object._i18n.get("There are blank fields."));
						break;
						
					} else {
						
						postData[key] = subscriptionData[key];
						
					}
					
				}
				
				if (send == true) {
					
					object._console.log(send);
					object._console.log(postData);
					object.setFunction("subscriptionDiteilPanel", postData);
					object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
					new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json) {
						
						object._console.log(json);
						object._loadingPanel.setAttribute("class", "hidden_panel");
						if (parseInt(json.status) === 1) {
							
							window.location.reload();
							
						} else {
							
							if (json.errorMessage.length > 0) {
								
								window.alert(json.errorMessage);
								
							} else {
								
								window.alert(object._i18n.get("We could not find your information"));
								
							}
							
						}
						
					});
					
				}
				
			};
			
		};
		
		updateSubscriptionPayments.onclick = function() {
			
			object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
			var form = document.createElement("form");
			form.method = "post";
			form.action = object._my_subscription_url;
			subscriptionPanel.appendChild(form);
			var subscriptionDiteils = {
				customer_id: object._setting_data.customer_id_for_subscriptions,
				subscriptions_id: object._setting_data.id_for_subscriptions,
				email: object._setting_data.customer_email_for_subscriptions,
				local: object._setting_data.locale,
				return_url: object._setting_data.return_url,
			}
			
			for (var key in subscriptionDiteils) {
				
				var hiddenPanel = object.createInputElement('input', 'hidden', key, subscriptionDiteils[key], null, null, null, null, null, null);
				form.appendChild(hiddenPanel);
				
			}
			form.submit();
			
		};
		
		
		if (object._setting_data.subscription_status === 'Canceled') {
			
			cancelSubscription.disabled = true;
			updateSubscription.disabled = true;
			cancelSubscription.onclick = function(){};
			updateSubscription.onclick = function(){};
			
		}
	
	};
	
	Booking_Package_Settings.prototype.sortData = function(key, className, list, panel, mode){
		
		var object = this;
		object._console.log(list);
		var sortBool = false;
		var panelList = panel.getElementsByClassName(className);
		for(var i = 0; i < list.length; i++){
			
			var index = parseInt(panelList[i].getAttribute("data-key"));
			if(i != index){
				
				sortBool = true;
				break;
				
			}
			
		}
		
		var keyList = [];
		var indexList = [];
		if(sortBool === true){
			
			for(var i = 0; i < panelList.length; i++){
				
				keyList.push(list[i][key]);
				var index = parseInt(panelList[i].getAttribute("data-key"));
				indexList.push(index);
				object._console.log(panelList[i]);
				
			}
			
		}
		
		object._console.log(keyList);
		object._console.log(indexList);
		return sortBool;
		
	};
	
	Booking_Package_Settings.prototype.changeRank = function(key, className, list, panel, mode, callback){
		
		var object = this;
		object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
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
		
		var postData = {mode: mode, nonce: object._nonce, action: object._action, keyList: keyList.join(","), indexList: indexList.join(",")};
		object.setFunction("changeRank", postData);
		var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
			
			callback(json);
			object._loadingPanel.setAttribute("class", "hidden_panel");
			
		});
		
		return newList;
		
	};
	
	Booking_Package_Settings.prototype.isJSON = function(arg) {
		
		arg = (typeof arg === "function") ? arg() : arg;
		if (typeof arg  !== "string") {
			return false;
		}
    	
		try {
			arg = (!JSON) ? eval("(" + arg + ")") : JSON.parse(arg);
			return true;
		} catch (e) {
			return false;
		}
		
	};
	
	Booking_Package_Settings.prototype.blockEmailListsPanel = function(emails) {
		
		var object = this;
		object._console.log(emails);
		var blockEmailListsTable = document.getElementById('blockEmailListsTable');
		blockEmailListsTable.textContent = null;
		
		var valueField = document.getElementById(object._prefix + 'newEmail');
		var addButton = document.getElementById(object._prefix + 'addBlockEmail');
		if (object._isExtensionsValid == 0) {
			
			valueField.disabled = true;
			addButton.disabled = true;
			
		}
		
		addButton.onclick = function() {
			
			object._console.log(this);
			var value = valueField.value;
			object._console.log(value);
			if (value.length == 0) {
				
				return null;
				
			}
			var loadingPanel = document.getElementById("loadingPanel");
			loadingPanel.classList.remove("hidden_panel");
			var postData = {mode: "addBlockEmail", nonce: object._nonce, action: object._action, email: value};
			var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
				
				object._console.log(json);
				loadingPanel.classList.add("hidden_panel");
				if (json.status == 'error') {
					
					window.alert(json.message);
					
				} else {
					
					document.getElementById(object._prefix + 'newEmail').value = null;
					object.blockEmailListsPanel(json.blocskList);
					
				}
				
			});
			
		};
		
		if (object._isExtensionsValid == 0) {
			
			return null;
			
		}
		
		var emailTd = object.create('td', object._i18n.get('Email'), null, null, null, null, null);
		var dateTd = object.create('td', object._i18n.get('Date'), null, null, null, null, null);
		var deleteTd = object.create('td', object._i18n.get('Delete'), null, null, null, 'headerDelete', null);
		var header = object.create('tr', null, [emailTd, dateTd, deleteTd], null, null, null, null);
		blockEmailListsTable.appendChild(header);
		
		for (var i = 0; i < emails.length; i++) {
			
			var email = emails[i];
			object._console.log(email);
			var dateTd = object.create('td',email.date, null, null, null, null, null);
			var valueLabel = object.create('span',email.value, null, null, null, null, null);
			var emailTd = object.create('td', null, [valueLabel], null, null, null, null);
			var deleteButton = object.create('label', 'delete', null, null, null, 'material-icons deleteLink', {key: email.key} );
			var deleteTd = object.create('td', null, [deleteButton], null, null, null, null);
			var tr = object.create('tr', null, [emailTd, dateTd, deleteTd], 'delete_' + email.ke, null, null, null);
			deleteButton.addEventListener('click', function() {
				
				var key = this.getAttribute('data-key');
				var tr = document.getElementById('delete_' + key);
				object._console.log(key);
				object._console.log(tr);
				
				var loadingPanel = document.getElementById("loadingPanel");
				loadingPanel.classList.remove("hidden_panel");
				var postData = {mode: "deleteBlockEmail", nonce: object._nonce, action: object._action, key: parseInt(key)};
				var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
					
					object._console.log(json);
					loadingPanel.classList.add("hidden_panel");
					object.blockEmailListsPanel(json);
					
				});
				
			});
			
			
			blockEmailListsTable.appendChild(tr);
			
		}
		
	};
	
	Booking_Package_Settings.prototype.syncPanel = function(){
		
		var object = this;
		var google_calendar_api_panel = function(messagePanel, parse_url, bookingSyncList){
    		
    		object._console.log(bookingSyncList.booking_package_googleCalendar_json);
    		object._console.log(parse_url);
    		var sync_url = parse_url.scheme + "://" + parse_url.host + "/?webhook=google";
    		object._console.log("sync_url = " + sync_url);
    		
    		var client_email = "no value";
    		var booking_package_googleCalendar_json = bookingSyncList.booking_package_googleCalendar_json.value;
    		if(booking_package_googleCalendar_json != null && booking_package_googleCalendar_json.length != 0){
    			
    			if(object.isJSON(booking_package_googleCalendar_json)){
    				
    				var json = JSON.parse(booking_package_googleCalendar_json);
    				client_email = json.client_email;
    				object._console.log(client_email);
    				
    			}
    			
    		}
    		
    		var google_calendar_api = document.getElementById("google_calendar_api").cloneNode(true);
    		google_calendar_api.classList.remove("hidden_panel");
    		google_calendar_api.setAttribute("class", "");
    		messagePanel.appendChild(google_calendar_api);
    		
    		
    	}
		
		object._console.log(object._bookingSyncList);
		var bookingSync_table = document.getElementById("bookingSync_table");
		bookingSync_table.textContent = null;
		var inputData = {};
		var messageList = {};
		messageList["Google_Calendar"] = "";

    	for(var i in object._bookingSyncList){
    		
    		var disabled = false;
    		
    		if(i == 'Google_Calendar'){
    			
    			if(typeof ExtensionsFunction != "function"){
    				
    				disabled = true;
    				
    			}
    			
    		}
    		
    		var title = object.create('div', 'iCalendar', null, null, null, 'title', null);
    		bookingSync_table.appendChild(title);
    		
    		if(messageList[i] != null){
    			
    			var message = object.create('div', messageList[i], null, null, null, null, null);
    			bookingSync_table.appendChild(message);
    			
    			
    		}
    		
    		var table = object.create('table', null, null, null, null, 'form-table', null);
    		bookingSync_table.appendChild(table);
    		for(var key in object._bookingSyncList[i]){
    			
    			object._console.log(object._bookingSyncList[i]);
    			var list = object._bookingSyncList[i][key];
    			var th = object.create('th', list.name, null, null, null, null, null);
    			th.setAttribute("scope", "row");
				
				var inputPanel = object.createInput(key, list, inputData, disabled, false);
				if(list.inputType == "CUSTOMIZE"){
					
					object._console.log("CUSTOMIZE");
					var tokenDate = list;
					var tokenButton = object.createButton(null, null, 'w3tc-button-save button-primary tokenButton', {key: key}, this._i18n.get("Refresh token") );
					var tokenValue = object.createInputElement('input', 'text', null, tokenDate.home + "?ical=" + tokenDate.value + '&site=' + object._siteToken, null, null, null, null, 'tokenValue', null);
					tokenValue.setAttribute("readonly", "readonly");
					tokenValue.style.width = "100%";
					inputPanel.appendChild(tokenValue);
					inputPanel.appendChild(tokenButton);
					tokenValue.onclick = function(){
                        
                        this.focus();
                        this.select();
                        
                    }
					
					tokenButton.onclick = function(){
						
						var key = this.getAttribute("data-key");
						object.refreshToken(key, function(new_token){
							
							object._console.log(new_token);
							tokenValue.value = tokenDate.home + "?ical=" + new_token.token  + '&site=' + object._siteToken;
							
						});
						
					}
					
				}
				
				var td = object.create('td', null, [inputPanel], null, null, null, null);
				if(list['type'] == 'radio' || list['type'] == 'check' || list['type'] == 'select'){
					
					object._console.log(list['type']);
					if(typeof list['valueList'] == 'object'){
						
						object._console.log(list['valueList']);
						
					}
				
				}
				
				var tr = object.create('tr', null, [th, td], null, null, null, null);
				tr.setAttribute("valign", "top");
				table.appendChild(tr);
    			
    		}
    		
		}
		
		object._console.log(inputData);
    	if(inputData.booking_app_googleCalendar_json){
    		
    		object._console.log(inputData.booking_app_googleCalendar_json.textBox);
    		var errorMessage = 'To display "Client ID" here, enter JSON in "Service account".';
    		var client_id_for_google = document.getElementById("client_id_for_google");
    		var value = inputData.booking_app_googleCalendar_json.textBox.value;
    		if(value.length != 0){
    			
    			var value = JSON.parse(value);
    			var client_email = value.client_email;
    			client_id_for_google.value = client_email;
    			object._console.log(client_id_for_google);
    			
    		}else{
    			
    			client_id_for_google.value = errorMessage;
    			
    		}
    		
    		inputData.booking_app_googleCalendar_json.textBox.onchange = function(){
    			
    			var value = inputData.booking_app_googleCalendar_json.textBox.value;
    			var value = JSON.parse(value);
    			if(value.client_email){
    				
    				var client_email = value.client_email;
    				client_id_for_google.value = client_email;
    				object._console.log(client_email);
    				object._console.log(client_id_for_google);
    				
    			}else{
    				
    				client_id_for_google.value = errorMessage;
    				
    			}
    			
   			}
   			
   		}
   		
		document.getElementById("save_bookingSync").onclick = function(){
        	
        	var postData = {mode: 'setting', type: 'bookingSync', nonce: object._nonce, action: object._action};
        	var input = new Booking_Package_Input(object._debug);
        	object._console.log(inputData);
        	var valueList = {};
        	
        	for(var i in object._bookingSyncList){
        		
        		for(var key in object._bookingSyncList[i]){
					
					if(inputData[key] != null){
						
						object._console.log(key);
						object._console.log(object._bookingSyncList[i][key]);
						object._console.log(inputData[key]);
						var bool = input.inputCheck(key, object._bookingSyncList[i][key], inputData[key], valueList);
						var value = valueList[key].join(',');
						if(value.length == 0 && Object.keys(inputData[key]).length == 1 && object._bookingSyncList[i][key].inputType == "CHECK"){
							value = 0;
						}
						
						postData[key] = value;
						
					}
        		
				}
				
        	}
			object._console.log(valueList);
			object._console.log(postData);
			
			if(postData.booking_package_googleCalendar_json != null && postData.booking_package_googleCalendar_json.length != 0 && object.isJSON(postData.booking_package_googleCalendar_json) == false){
				
				var confirm = new Confirm(object._debug);
				confirm.alertPanelShow(object._i18n.get("Error"), "The Service account must be in JSON format.", false, function(){});
				return null;
				
			}
			
			var loadingPanel = document.getElementById("loadingPanel");
			loadingPanel.classList.remove("hidden_panel");
			var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
				
				object._console.log(json);
				object._console.log(object._bookingSyncList);
				object._bookingSyncList.iCal.booking_package_ical_active.value = json.iCal.booking_package_ical_active;
				object._bookingSyncList.iCal.booking_package_syncPastCustomersForIcal.value = json.iCal.booking_package_syncPastCustomersForIcal;
				loadingPanel.classList.add("hidden_panel");
				
			});
        	
    	}
		
	};
	
	Booking_Package_Settings.prototype.holidayPanel = function(mode){
		
		var object = this;
		object._console.log("holidayPanel");
		var holidayPanel = document.getElementById("holidayPanel");
		var regularHolidays = object._setting_data.regularHolidays;
		if (mode == 'national') {
			
			holidayPanel = document.getElementById("nationalHolidayPanel");
			regularHolidays = object._setting_data.nationalHolidays;
			
		}
		var month = parseInt(regularHolidays.date.month);
		var year = parseInt(regularHolidays.date.year);
		object._console.log(regularHolidays);
		var weekName = [object._i18n.get('Sun'), object._i18n.get('Mon'), object._i18n.get('Tue'), object._i18n.get('Wed'), object._i18n.get('Thu'), object._i18n.get('Fri'), object._i18n.get('Sat')];
        var calendar = new Booking_App_Calendar(weekName, object._setting_data.dateFormat, object._setting_data.positionOfWeek, object._setting_data.positionTimeDate, object._startOfWeek, object._i18n, object._debug);
		
		var calendarPanel = document.getElementById("holidaysCalendarPanel");
		calendarPanel.classList.add("hidden_panel");
		calendarPanel.textContent = null;
		if (mode == 'national') {
			
			calendarPanel = document.getElementById("nationalHolidaysCalendarPanel");
			calendarPanel.classList.add("hidden_panel");
			calendarPanel.textContent = null;
			
		}
		
		calendar.holidayPanel(mode, holidayPanel, calendarPanel, month, year, regularHolidays, function(postData, callback) {
			
			postData.nonce = object._nonce;
			postData.action = object._action;
			object._console.log(postData);
			var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
				
				object._console.log(json);
				if (mode == 'share') {
					
					object._setting_data.regularHolidays = json;
					
				} else if (mode == 'national') {
					
					object._setting_data.nationalHolidays = json;
					
				}
				
				callback(true, json);
				
			});
			
		});
		
	};
	
	Booking_Package_Settings.prototype.memberPanel = function(){
		
		var object = this;
		object._console.log("memberPanel");
		var memberSetting = object._setting_data.memberSetting;
		object._console.log(memberSetting);
		var memberPanel = document.getElementById("memberPanel");
		if(parseInt(object._isExtensionsValid) == 1){
			
			var tags = memberPanel.getElementsByClassName("extensionsValid");
			object._console.log(tags);
			for(var i = 0; i < tags.length; i++){
				
				tags[i].classList.add("hidden_panel");
				
			}
			
		}
		//memberPanel.textContent = "Member";
		for(var key in memberSetting){
			
			var data = memberSetting[key];
			if(data.value != null && typeof data.value == "string"){
				
				data.value = data.value.replace(/\\/g, "");
				
			}
			
			object._console.log(data);
			var inputElement = document.getElementById(key);
			object._console.log(inputElement);
			if(inputElement.tagName == "INPUT" && inputElement.type.toLocaleUpperCase() == "CHECKBOX"){
				
				object._console.log(inputElement.type.toLocaleUpperCase());
				if(parseInt(data.value) == 1){
					
					inputElement.checked = true;
					
				}
				
			}else if(inputElement.tagName == "INPUT" && inputElement.type.toLocaleUpperCase() == "TEXT"){
				
				object._console.log(inputElement.type.toLocaleUpperCase());
				inputElement.value = data.value;
				
			}else if(inputElement.tagName == "TEXTAREA"){
				
				inputElement.textContent = data.value;
				
			}
			
		}
		
		var save_member_setting_button = document.getElementById("save_member_setting_button");
		if(parseInt(object._isExtensionsValid) == 1){
			
			save_member_setting_button.removeEventListener("click", null);
			save_member_setting_button.onclick = function(){
				
				var postData = {mode: 'updateMemberSetting', nonce: object._nonce, action: object._action};
				for(var key in memberSetting){
					
					var data = memberSetting[key];
					var inputElement = document.getElementById(key);
					if(data.inputType.toLocaleUpperCase() == "CHECK"){
						
						postData[key] = 0;
						if(inputElement.checked == true){
							
							postData[key] = 1;
							
						}
						
					}else if(data.inputType.toLocaleUpperCase() == "TEXT"){
						
						postData[key] = inputElement.value;
						
					}else if(data.inputType.toLocaleUpperCase() == "TEXTAREA"){
						
						postData[key] = inputElement.value;
						
					}
					
				}
				
				object._console.log(postData);
				var loadingPanel = document.getElementById("loadingPanel");
	        	loadingPanel.classList.remove("hidden_panel");
	        	object.setFunction("memberPanel", postData);
	        	var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
					
					object._console.log(json);
					object._setting_data.memberSetting = json;
					loadingPanel.classList.add("hidden_panel");
										
				});
				
			}
			
		}else{
			
			save_member_setting_button.disabled = true;
			
		}
		
	};
	
	Booking_Package_Settings.prototype.cssPanel = function() {
		
		var object = this;
		object._console.log("cssPanel");
		var css_textarea = document.getElementById("css");
		var editor = null;
		if (object._countCssPanel == 0) {
			/**
			object._jsEditor = CodeMirror.fromTextArea(
				css_textarea, 
				{
					mode: "css",
					lineNumbers: true,
					indentUnit: 4,
				}
			);
			**/
			
			require.config({
				paths: {
					'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs'
				}
			});
			
			require(['vs/editor/editor.main'], function () {
				
				editor = monaco.editor.create(document.getElementById('css_editor'), {
					value: css_textarea.value,
					language: 'css',
					theme: 'vs-light'
				});
			});
			
			window.addEventListener('resize', () => {
				editor.layout();
		    });
	    	
		}
		
		object._countCssPanel++;
		
		
		document.getElementById("save_css").onclick = function() {
			
			//object._jsEditor.save();
			//var value = css_textarea.value;
			if (editor == null) {
				
				return null;
				
			}
			const value = editor.getValue();
			object._console.log(value);
			var loadingPanel = document.getElementById("loadingPanel");
        	loadingPanel.classList.remove("hidden_panel");
        	
        	var postData = {mode: 'updateCss', nonce: object._nonce, action: object._action, value: value};
			object._console.log(postData);
        	var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
				
				object._console.log(json);	
				loadingPanel.classList.add("hidden_panel");
									
			});
			
		}
		
	};
	
	Booking_Package_Settings.prototype.javascriptPanel = function() {
		
		var object = this;
		object._console.log("javascriptPanel");
		object._console.log("_isExtensionsValid = " + object._isExtensionsValid);
		var javascript_textarea = document.getElementById("javascript_booking_package");
		var editor = null;
		if (object._countJavascriptPanel == 0) {
			/**
			object._jsEditor = CodeMirror.fromTextArea(
				javascript_textarea, 
				{
					mode: "javascript",
					lineNumbers: true,
					indentUnit: 4,
				}
			);
			**/
			
			require.config({
				paths: {
					'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.39.0/min/vs'
				}
			});
			
			require(['vs/editor/editor.main'], function () {
				
				editor = monaco.editor.create(document.getElementById('javascript_editor'), {
					value: javascript_textarea.value,
					language: 'javascript',
					theme: 'vs-light'
				});
			});
			
			window.addEventListener('resize', () => {
				editor.layout();
		    });
			
		}
		
		object._countJavascriptPanel++;
		
		if (object._isExtensionsValid == 1) {
			
			document.getElementById("save_javascript").onclick = function() {
				
				//object._jsEditor.save();
				//var value = javascript_textarea.value;
				if (editor == null) {
					
					return null;
					
				}
				const value = editor.getValue();
				object._console.log(value);
				var loadingPanel = document.getElementById("loadingPanel");
				loadingPanel.classList.remove("hidden_panel");
				
				var postData = {mode: 'updateJavaScript', nonce: object._nonce, action: object._action, value: value};
				object._console.log(postData);
				var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
					
					object._console.log(json);	
					loadingPanel.classList.add("hidden_panel");
					
				});
				
			}
			
		} else {
			
			document.getElementById("save_javascript").disabled = true;
			
		}
		
	};
	
	Booking_Package_Settings.prototype.createSettingPanel = function() {
		
		var object = this;
		
		var links = {
			Mailgun: "https://www.mailgun.com/",
			twilio: "https://www.twilio.com/",
			Stripe: "https://stripe.com",
			PayPal: "https://developer.paypal.com",
			reCAPTCHA: "https://www.google.com/recaptcha/",
			hCaptcha: "https://www.hcaptcha.com/",
		}
		
		object._loadingPanel.classList.add("hidden_panel");
		object._console.log(object._settingList);
		document.getElementById("settingPanel").classList.remove("hidden_panel");
		var setting_table = document.getElementById("setting_table");
		var inputData = {};
		var messageList = {};
		
		for(var i in object._settingList){
			
			var disabled = false;
			if (links[i] != null) {
				
				var title = object.create('a', i, null, null, null, 'title', null);
				title.href = links[i];
				title.target = "_blank";
				setting_table.appendChild(title);
				
				if (i == 'twilio') {
					
					title.textContent = i + ' SMS';
					
				}
				
			} else {
				
				var title = object.create('div', object._i18n.get(i), null, null, null, 'title', null);
				setting_table.appendChild(title);
				
			}
			
			if (messageList[i] != null) {
				
				var message = object.create('div', messageList[i], null, null, null, null, null);
				setting_table.appendChild(message);
				
			}
			
			var table = object.create('table', null, null, null, null, 'form-table', null);
			setting_table.appendChild(table);
			for (var key in object._settingList[i]) {
				
				var list = object._settingList[i][key];
				disabled = false;
				if (object._isExtensionsValid == 0 && parseInt(list.isExtensionsValid) == 1) {
					
					disabled = true;
					
				}
				
				var settingNameSpan = object.create('span', list.name, null, null, null, null, null);
				var th = object.create('th', null, [settingNameSpan], null, null, null, null);
				th.setAttribute("scope", "row");
				if (list.deprecated != null && list.deprecated === true) {
					
					object._console.log(list.deprecated);
					var deprecatedSpan = object.create('span', object._i18n.get('Deprecated'), null, null, null, 'deprecatedSpan', null);
					th.appendChild(deprecatedSpan);
					
				}
				
				var idBool = false;
				if (i == 'Design') {
					idBool = true;
				}
				
				var inputPanel = object.createInput(key, list, inputData, disabled, idBool);
				var td = object.create('td', null, [inputPanel], null, null, null, null);
				if (list['type'] == 'radio' || list['type'] == 'check' || list['type'] == 'select') {
					
					object._console.log(list['type']);
					object._console.log(list.valueList);
					if (typeof list['valueList'] == 'object') {
						
						object._console.log(list['valueList']);
						
					}
				
				}
				
				var tr = object.create('tr', null, [th, td], null, null, null, null);
				tr.setAttribute("valign", "top");
				table.appendChild(tr);
				
				if(i == 'Design' && list.js != null){
					
					(function( $ ) {
						
						$(function() {
							
							object._console.log("key = " + key);
							object._console.log($('#' + key));
							object._console.log(typeof $('#' + key).wpColorPicker);
							if (typeof $('#' + key).wpColorPicker == 'function') {
								
								object._console.log(typeof $('#' + key).wpColorPicker());
								$('#' + key).wpColorPicker();
								
							}
							
						});
						
					})( jQuery );
					
				}
				
			}
			
		}
		
		document.getElementById('booking_package_googleReCAPTCHA_active_1').onclick = function() {
			
			object._console.log(this);
			var hCaptcha_0 = document.getElementById('booking_package_hCaptcha_active_0');
			var hCaptcha_1 = document.getElementById('booking_package_hCaptcha_active_1');
			if (this.checked === true) {
				
				if (hCaptcha_0 != null) {
					
					hCaptcha_0.checked = true;
					hCaptcha_1.checked = false;
					
				}
				
			}
			
		};
		
		document.getElementById('booking_package_hCaptcha_active_1').onclick = function() {
			
			object._console.log(this);
			var reCaptcha_0 = document.getElementById('booking_package_googleReCAPTCHA_active_0');
			var reCaptcha_1 = document.getElementById('booking_package_googleReCAPTCHA_active_1');
			if (this.checked === true) {
				
				if (reCaptcha_0 != null) {
					
					reCaptcha_0.checked = true;
					reCaptcha_1.checked = false;
					
				}
				
			}
			
		};
    	
    	document.getElementById("save_setting").onclick = function(){
        	
        	var loadingPanel = document.getElementById("loadingPanel");
        	loadingPanel.classList.remove("hidden_panel");
        	
        	var postData = {mode: 'setting', nonce: object._nonce, action: object._action};
        	var input = new Booking_Package_Input(object._debug);
        	object._console.log(inputData);
        	var valueList = {};
        	
        	for (var i in object._settingList) {
        		
        		for (var key in object._settingList[i]) {
					
					if (inputData[key] != null) {
						
						object._console.log(key);
						object._console.log(object._settingList[i][key]);
						object._console.log(inputData[key]);
						if (object._settingList[i][key]['inputType'] !== 'MULTIPLE_FIELDS') {
							
							var bool = input.inputCheck(key, object._settingList[i][key], inputData[key], valueList);
							
						} else {
							
							(function(options, inputObject, postData) {
								
								for (var i = 0; i < options.length; i++) {
									
									var key = options[i].key;
									var bool = input.inputCheck(key, options[i], inputObject[i].inputObjects, valueList);
									postData[key] = options[i].value;
									if (inputObject[i].inputType === 'TEXT') {
										
										postData[key] = inputObject[i].inputObjects[0].value;
										
									}
									
									
								}
								
								
								
							})(object._settingList[i][key]['valueList'], inputData[key], postData);
							
						}
						
						object._console.log(valueList);
						object._console.log(typeof valueList[key]);
						var value = [];
						if (typeof valueList[key] == 'object') {
							
							value = valueList[key].join(',');
							
						} else if (valueList[key] != null) {
							
							value = JSON.parse(valueList[key]);
							
						}
						
						if(value.length == 0 && Object.keys(inputData[key]).length == 1 && object._settingList[i][key].inputType == "CHECK"){
							value = 0;
						}
						
						postData[key] = value;
						
					}
        			
				}
				
        	}
        	
        	object._console.log(valueList);
        	object._console.log(postData);
        	var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
				
				object._console.log(json);	
				loadingPanel.classList.add("hidden_panel");
									
			});
        	
    	}
		
	};
	
	Booking_Package_Settings.prototype.getInputData = function(inputTypeList, inputData){
		
		var object = this;
		var postData = {};
		for(var key in inputTypeList){
			
			object._console.log(key);
			var values = [];
			var inputType = inputTypeList[key];
			object._console.log(inputType);
			for (var inputKey in inputData[key]) {
				
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
					
					if (inputData[key][inputKey].checked == true) {
						
						values.push(inputData[key][inputKey].value);
						
					}
					
				} else if (inputType['inputType'] == 'SELECT') {
					
					var index = inputData[key][inputKey].selectedIndex;
					values.push(inputData[key][inputKey].options[index].value);
					
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
                        
                    }
                    
                    console.error(postData);
                    
                }
				
			}
			
			if (bool === true) {
				
				postData[key] = values.join(",");
				
			} else {
				
				postData[key] = false;
				
			}
			
			//postData[key] = false;
			object._console.log(inputData[key]);
			
		}
		
		return postData;
		
	};
	
	Booking_Package_Settings.prototype.createInput = function(inputName, input, inputData, disabled, idBool){
		
		var object = this;
		object._console.log("createInput");
		object._console.log(input);
		if(typeof input['value'] == "string" && inputName != 'booking_package_googleCalendar_json'){
			
			input['value'] = input['value'].replace(/\\/g, "");
			
		}
		
		var isExtensionsValid = 0;
		if(input.isExtensionsValid != null){
			
			isExtensionsValid = parseInt(input.isExtensionsValid);
			
		}
		var list = null;
		if(input['valueList'] != null){
			
			list = input['valueList'];
			
		}
		
		var deprecated = false;
		if (input.deprecated != null && input.deprecated === true) {
			
			deprecated = true;
			object._console.log(input.deprecated);
			
		}
		
		object._console.log(list);
		var valuePanel = object.create('div', null, null, null, null, 'valuePanel', null);
		if (isExtensionsValid == 1 && object._isExtensionsValid == 0) {
			
			object._console.log("isExtensionsValid = " + isExtensionsValid);
			var extensionsValidPanel = object.create('div', object._i18n.get("Paid plan subscription required."), null, null, null, 'extensionsValid', null);
            valuePanel.appendChild(extensionsValidPanel);
			
		}
		
		if (input['inputType'] == 'MULTIPLE_FIELDS') {
			
			object._console.log('MULTIPLE_FIELDS');
            object._console.log(input);
            object._console.log(input.value);
            var timeColorPicker =[];
            valuePanel.id = 'setting_' + input.key;
            inputData[inputName] = [];
            var fields = input.valueList;
            for (var i = 0; i < fields.length; i++) {
                
                var fieldKey = i;
                var field = fields[i];
                
                var fieldPanel = document.createElement('div');
                if (field.inputType == 'COLOR') {
                    
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
                    
                } else if (field.inputType == 'TEXT') {
                	
                	
                	
                }
                
            }
			
			
		} if (input['inputType'] == 'TEXT') {
			
			var textBox = object.createInputElement('input', 'text', null, input['value'], null, disabled, null, null, 'regular-text', null);
			if (idBool == true) {
				
				textBox.id = inputName;
				
			}
			
			if (input.placeholder != null) {
				
				textBox.placeholder = input.placeholder;
				
			}
			
			valuePanel.appendChild(textBox);
			inputData[inputName] = {textBox: textBox};
			
		} else if (input['inputType'] == 'SELECT') {
			
			var selectBox = object.createInputElement('select', null, null, null, null, disabled, null, null, null, null);
			object._console.log(typeof list);
			object._console.log(list);
			for (var key in list) {
				
				var optionBox = object.createInputElement('option', null, null, key, list[key], null, null, null, null, null);
				if (key == input['value']) {
					
					//object._console.log("value = " + input['value']);
					optionBox.selected = true;
					
				}
				
				selectBox.appendChild(optionBox);
				
			}
			
			valuePanel.appendChild(selectBox);
			inputData[inputName] = {selectBox: selectBox};
			
		} else if (input['inputType'] == 'SELECT_GROUP') {
			
			var selectBox = object.createInputElement('select', null, null, null, null, disabled, null, null, null, null);
			object._console.log(typeof list);
			object._console.log(list);
			
			var selectedvalue = null;
			for (var key in list) {
				
				if(list[key]['alpha-2'] == input['value']){
					
					selectedvalue = key;
					break;
					
				}
				
			}
			
			if (selectedvalue == null) {
				
				selectedvalue = "United States of America";
				
			}
			
			var selectedCountry = list[selectedvalue];
			object._console.log(selectedCountry)
			
			var optionBox = object.createInputElement('option', null, null, selectedCountry['alpha-2'], selectedCountry.name, null, null, null, null, null);
			
			var optgroup = document.createElement("optgroup");
			optgroup.setAttribute("label", this._i18n.get("Selected country"));
			optgroup.appendChild(optionBox);
			selectBox.appendChild(optgroup);
			
			var frequently = ["Canada", "France", "Germany", "Italy", "Japan", "United Kingdom of Great Britain and Northern Ireland", "United States of America"];
			var optgroup = document.createElement("optgroup");
			optgroup.setAttribute("label", this._i18n.get("Frequently used countries"));
			selectBox.appendChild(optgroup);
			
			object._console.log("selectedvalue = " + selectedvalue);
			for (var i = 0; i < frequently.length; i++) {
				
				var key = frequently[i];
				if (list[key].name != selectedvalue) {
					
					var optionBox = object.createInputElement('option', null, null, list[key]['alpha-2'], list[key].name, null, null, null, null, null);
					optgroup.appendChild(optionBox);
					
				}
				
			}
			
			var optgroup = document.createElement("optgroup");
			optgroup.setAttribute("label", this._i18n.get("Other countries"));
			selectBox.appendChild(optgroup);
			
			for (var key in list) {
				
				var optionBox = object.createInputElement('option', null, null, list[key]['alpha-2'], list[key].name, null, null, null, null, null);
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
			inputData[inputName] = {selectBox: timezoneSelect};
			
		} else if (input['inputType'] == 'OPTION') {
			
			object._console.log("inputType = " + input['inputType']);
			object._console.log(input);
			var options = [];
			if(input['value'] != null){
				
				options = input['value'].split(",");
				
			}
			
			object._console.log(options);
			var addButton = object.create('label', this._i18n.get("Add"), null, null, null, 'addLink', null);
			valuePanel.appendChild(addButton);
			var table = object.create('table', null, null, null, null, 'table_option wp-list-table widefat fixed striped', null);
			valuePanel.appendChild(table);
			
			inputData[inputName] = {};
			var tr_index = 0;
			var table_tr = {};
			
			for(var i = 0; i < options.length; i++){
				
				create_tr(tr_index, table, options[i]);
				tr_index++;
				
			}
			
			addButton.onclick = function(){
				
				create_tr(tr_index, table, null);
				tr_index++;
				
			}
			
		} else if (input['inputType'] == 'CHECK') {
			
			inputData[inputName] = {};
			for (var key in list) {
				
				object._console.log("key = " + key + " value = " + list[key])
				var valueName = object.create('span', list[key], null, null, null, 'radio_title', null);
				var checkBox = object.createInputElement('input', 'checkbox', inputName, list[key], null, disabled, null, null, null, {value: key} );
				if (input['value'] == key) {
					
					checkBox.checked = "checked";
					
				}
				
				var label = object.create('label', null, [checkBox, valueName], null, null, null, null);
				valuePanel.appendChild(label);
				inputData[inputName][key] = checkBox;
				
			}
			
		} else if (input['inputType'] == 'RADIO') {
				
			inputData[inputName] = {};
			for (var key in list) {
				
				object._console.log(key + " = " + list[key]);
				var valueName = object.create('span', list[key], null, null, null, 'radio_title', null);
				var radioBox = object.createInputElement('input', 'radio', inputName, list[key], null, disabled, inputName + '_' + key, null, null, {value: key} );
				if (input['value'] == key) {
					
					object._console.log("value = " + input['value']);
					radioBox.checked = "checked";
					
				}
				
				var label = object.create('label', null, [radioBox, valueName], null, null, null, null);
				valuePanel.appendChild(label);
				inputData[inputName][key] = radioBox;
				
			}
			
		}else if(input['inputType'] == 'TEXTAREA'){
			
			var textareaBox = object.createInputElement('textarea', null, null, input['value'], null, disabled, null, null, null, null);
			valuePanel.appendChild(textareaBox);
			inputData[inputName] = {textBox: textareaBox};
			
		} if (input['inputType'] == "MULTIPLE_FIELDS") {
            
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
                    
                    var valueName = object.create('span', field.name, null, null, null, null, null);
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
                        var checkBox = object.createInputElement('input', 'checkbox', field.key, value, null, disabled, null, null, null, {value: key} );
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
                        var radioBox = object.createInputElement('input', 'radio', field.key, value, null, disabled, null, null, null, {value: key} );
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
                    var inputText = object.createInputElement('input', 'text', null, field.value, null, null, null, null, null, null);
                    field.inputObjects[0] = inputText;
                    if (field.placeholder != null) {
						
						inputText.placeholder = field.placeholder;
						
					}
                    
                    
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
                        var optionBox = object.createInputElement('option', null, null, option.key, option.name, null, null, null, null, null);
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
                    valuePanel.appendChild(fieldPanel);
                    
                }
                
                if (field.message != null && typeof field.message == "string") {
                    
                    var messageLabel = object.create('div', null, null, null, null, 'messageLabel', null);
                    messageLabel.insertAdjacentHTML("beforeend", field.message);
                    valuePanel.appendChild(messageLabel);
                    
                    
                }
                
                if (field.extensionsValidMessage != null && parseInt(field.extensionsValidMessage) == 1 && parseInt(field.isExtensionsValid) == 1 && isExtensionsValid == 0) {
                    
                    var extensionsValidPanel = object.create('div', object._i18n.get("Paid plan subscription required."), null, null, null, 'extensionsValid', null);
                    fieldPanel.insertAdjacentElement("afterbegin", extensionsValidPanel);
                    
                }
                
            }
            
            object._console.log(inputData[inputName]);
            
        };
		
		function create_tr(tr_index, table, value){
			
			var textBox = object.createInputElement('input', 'text', null, null, null, null, null, 'width: 100%;', 'regular-text', {key: tr_index} );
			var filedTd = object.create('td', null, [textBox], null, null, 'td_option', null);
			var deleteButton = object.create('label', 'delete', null, null, null, 'material-icons deleteLink', {key: tr_index} );
			var deleteTd = object.create('td', null, [deleteButton], null, null, 'td_delete td_option', null);
			var tr = object.create('tr', null, [filedTd, deleteTd], null, null, 'tr_option', null);
			table_tr[tr_index] = tr;
			table.appendChild(tr);
			
			var checkBox = object.createInputElement('input', 'checkbox', tr_index, null, null, null, null, null, null, {value: tr_index} );
			inputData[inputName][tr_index] = checkBox;
			if (value != null && value.length != 0) {
			
				textBox.value = value;
				checkBox.value = value;
				checkBox.checked = true;
				
			}
			
			textBox.onchange = function(){
				
				var dataKey = this.getAttribute("data-key");
				var value = this.value;
				var bool = false;
				if(value.length != 0){
					bool = true;
				}else{
					value = null;
				}
				
				inputData[inputName][dataKey].value = value;
				inputData[inputName][dataKey].checked = bool;
				object._console.log("dataKey = " + dataKey + " value = " + value + " bool = " + bool);
				
			}
			
			deleteButton.onclick = function(){
				
				var result = false;
				var dataKey = this.getAttribute("data-key");
				var length = inputData[inputName][dataKey].value.length;
				if(length == 0){
					
					result = true;
					
				}
				
				if(result === false){
					
					result = confirm(this._i18n.get("Do you delete the \"%s\"?", [inputData[inputName][dataKey].value]));
					
				}
				
				if(result === true){
					
					var tr = table_tr[parseInt(dataKey)];
					table.removeChild(tr);
					delete table_tr[dataKey];
					delete inputData[inputName][dataKey];
					object._console.log(tr);
					object._console.log(table_tr);
					object._console.log(inputData[inputName]);
					object._console.log("tr_index = " + tr_index);
					
				}
				
			}
			
		}
		
		return valuePanel;
		
	};
	
	Booking_Package_Settings.prototype.refreshToken = function(key, callback){
		
		var object = this;
		object._console.log(key);
		var postData = {mode: "refreshToken", nonce: object._nonce, action: object._action, key: key};
		object._loadingPanel.setAttribute("class", "loading_modal_backdrop");
    	var xmlHttp = new Booking_App_XMLHttp(object._url, postData, object._webApp, function(json){
			
			if(json['status'] != 'error'){
				
				callback(json);
				
			}
			object._loadingPanel.setAttribute("class", "hidden_panel");
							
		});
		
	};
	
	Booking_Package_Settings.prototype.editPanelShow = function(showBool){
    	
    	var object = this;
    	var body = document.getElementsByTagName("body")[0];
    	object._console.log(body);
    	if(showBool == true){
        	
        	body.classList.add("modal-open");
        	object.editPanel.setAttribute("class", "edit_modal");
        	object.blockPanel.setAttribute("class", "edit_modal_backdrop");
			
    	}else{
        	
        	body.classList.remove("modal-open");
        	object.editPanel.setAttribute("class", "hidden_panel");
        	object.blockPanel.setAttribute("class", "hidden_panel");
			
    	}
		
	};
	
	Booking_Package_Settings.prototype.create = function(elementType, text, childElements, id, style, className, data_x) {
        
        var panel = this._element.create(elementType, text, childElements, id, style, className, data_x);
        return panel;
        
    };
    
    Booking_Package_Settings.prototype.createButtonPanel = function(id, style, className, buttons) {
        
        var buttonPanel = this._element.createButtonPanel(id, style, className, buttons);
        return buttonPanel;
        
    };
    
    Booking_Package_Settings.prototype.createButton = function(id, style, className, data_x, text) {
        
        var button = this._element.createButton(id, style, className, data_x, text);
        return button;
        
    };
    
    Booking_Package_Settings.prototype.createInputElement = function(tagName, type, name, value, text, disabled, id, style, className, data_x) {
        
        var input = this._element.createInputElement(tagName, type, name, value, text, disabled, id, style, className, data_x);
        return input;
        
    };
	

