/* globals Booking_Package_Elements */

	function Booking_Package_Input(debug) {
		
		this._userInformation = {};
		this._userEmail = null;
		this._prefix = "";
		
		this._debug = null;
		this._admin = false;
        this._console = {};
        this._console.log = console.log;
        if (debug != null && typeof debug.getConsoleLog == 'function') {
            
            this._debug = debug;
            this._console.log = debug.getConsoleLog();
            
        }
        
        this._element = new Booking_Package_Elements(debug);
		
	};
	
	Booking_Package_Input.prototype.setAdmin = function(admin) {
		
		this._admin = admin;
		
	};
	
	Booking_Package_Input.prototype.getAdmin = function() {
		
		return this._admin;
		
	};
	
	Booking_Package_Input.prototype.setUserInformation = function(userInformation){
		
		this._userInformation = userInformation;
		
	};
	
	Booking_Package_Input.prototype.setUserEmail = function(userEmail) {
		
		this._userEmail = userEmail;
		
	};
	
	Booking_Package_Input.prototype.setPrefix = function(prefix){
		
		this._prefix = prefix;
		
	};
    
    Booking_Package_Input.prototype.createInput = function(inputName, input, inputData, eventAction){
		
		var object = this;
		object._console.log(input);
		var userInformation = object._userInformation;
		object._console.log(userInformation);
		var list = null;
		
		if (typeof input.options == 'string' && input.options != null) {
			
			list = input.options.split(",");
			
		} else if (typeof input.options == 'object' && input.options != null) {
			
			list = input.options;
			
		}
		
		if (input.json != null) {
			
			list = input.values;
			
		}
		
		object._console.log(list);
		var userValuePanel = object.create('div', null,null, object._prefix + "value_" + input.id, null, 'hidden_panel', null);
		var valuePanel = object.create('div', null, [userValuePanel], null, null, null, null);
		var inputId = object._prefix + "input_" + input.id;
		var userValue = null;
		if (userInformation[input['type']] != null && userInformation[input['type']][input['id']] != null && userInformation[input['type']][input['id']].value != null) {
			
			userValue = userInformation[input['type']][input['id']].value;
			
		} else {
			
			if (input.isEmail == 'true' && object._userEmail != null) {
				
				userValue = object._userEmail;
				
			}
			
		}
		
		if (typeof userValue == "string") {
			
			userValue = userValue.replace(/\\/g, "");
			
		}
		
		if (input.isAutocomplete == 'false') {
			
			userValue = null;
			
		}
		
		if (input['type'] == 'TEXT') {
			
			var textBox = object.createInputElement('input', 'text', null, input['value'], null, null, inputId, null, 'regular-text form_text', null);
			object._console.log(typeof input.placeholder);
			if (input.placeholder == null) {
				
				input.placeholder = '';
				
			}
			
			if ((typeof input.placeholder == 'string' || typeof input.placeholder == 'number') && String(input.placeholder).length > 0) {
				
				textBox.setAttribute('placeholder', input.placeholder);
				//textBox.placeholder = String(input.placeholder);
				
			}
			
			if (inputData[inputName] != null && inputData[inputName].textBox != null) {
				
				textBox = inputData[inputName]["textBox"];
				
			}
			
			if (eventAction != null) {
				    
				textBox.onchange = eventAction;
				
			}
			
			if (userValue != null && userValue.length != 0) {
				
				textBox.value = userValue;
				textBox.classList.add("hidden_panel");
				valuePanel.setAttribute("data-edit", "1");
				userValuePanel.classList.remove("hidden_panel");
				userValuePanel.textContent = userValue;
				if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
					
					textBox.classList.remove("hidden_panel");
					userValuePanel.classList.add("hidden_panel");
					valuePanel.removeAttribute("data-edit");
					
				}
				
				textBox.onchange = function() {
					
					userValuePanel.textContent = textBox.value;
					
					
				};
				
			}
			
			valuePanel.appendChild(textBox);
			inputData[inputName] = {textBox: textBox};
			
		} else if (input['type'] == 'SELECT') {
			
			object._console.log(input.value);
			var selectBox = object.createInputElement('select', null, null, null, null, null, inputId, null, 'form_select', null);
			//for(var i = 0; i < list.length; i++){
			for (var key in list) {
				
				if (typeof list[key] == "string") {
					
					list[key] = list[key].replace(/\\/g, "");
					
				}
				
				var optionBox = object.createInputElement('option', null, null, list[key], list[key], null, null, null, null, {optionsKey: key} );
				if (list[key] == input['value']) {
					
					object._console.log("value = " + input['value']);
					optionBox.selected = true;
					
				}
				
				if (input.index != null && input.index == key) {
					
					optionBox.selected = true;
					
				}
				
				if (userValue == list[key]) {
					
					optionBox.selected = true;
					
				}
				
				selectBox.appendChild(optionBox);
				
				
			}
			
			if (userValue != null && inputData[inputName] != null && inputData[inputName].selectBox != null) {
				
				selectBox = inputData[inputName].selectBox;
				
			}
			
			if (eventAction != null) {
				    
		    	selectBox.onchange = eventAction;
			    
			}
			
			if (userValue != null && userValue.length != 0) {
				
				selectBox.classList.add("hidden_panel");
				valuePanel.setAttribute("data-edit", "1");
				userValuePanel.classList.remove("hidden_panel");
				userValuePanel.textContent = userValue;
				if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
					
					selectBox.classList.remove("hidden_panel");
					userValuePanel.classList.add("hidden_panel");
					valuePanel.removeAttribute("data-edit");
					
				}
				
				selectBox.onchange = function() {
					
					var index = selectBox.selectedIndex;
					userValuePanel.textContent = selectBox.options[index].value;
					
				};
				
			}
			
			valuePanel.appendChild(selectBox);
			inputData[inputName] = {selectBox: selectBox};
			
		} else if (input['type'] == 'CHECK') {
			
			if (inputData[inputName] == null) {
				
				inputData[inputName] = {};
				
			}
			
			var valueList = input.value;
			if (typeof input.value.split == 'function') {
				
				valueList = input.value.split(",");
				
			}
			
			if (userValue != null && typeof userValue.split == 'function') {
				
				valueList = userValue.split(",");
				
			} else if (userValue != null && typeof userValue == 'object') {
				
				valueList = userValue;
				
			}
			
			if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
				
				valueList = [];
				
			}
			object._console.log(valueList);
			
			var checkBoxPanel = object.create('div', null, null, inputId, null, null, null);
			valuePanel.appendChild(checkBoxPanel);
			
			for (var i = 0; i < list.length; i++) {
				
				if (typeof list[i] == "string") {
					
					list[i] = list[i].replace(/\\/g, list[i]);
					
				}
				
				var valueName = object.create('span', list[i], null, null, null, 'radio_title', null);
				var checkBox = object.createInputElement('input', 'checkbox', inputName, list[i], null, null, null, null, 'form_checkbox', {value: i} );
				checkBox.checked = false;
				for (var a = 0; a < valueList.length; a++) {
					
					object._console.log(valueList[a]);
					if (typeof valueList[a] == "string") {
						
						valueList[a] = valueList[a].replace(/\\/g, "");
						
					}
					
					if (valueList[a] == list[i]) {
						
						checkBox.checked = "checked";
						
					}
					
				}
				
				if (inputData[inputName] != null && inputData[inputName][i] != null) {
				
					checkBox = inputData[inputName][i];
					
				}
				
				checkBox.onchange = function() {
					
					var checkValue = "";
					for (var key in inputData[inputName]) {
						
						var checkBox = inputData[inputName][key];
						if (checkBox.checked == true) {
							
							checkValue += checkBox.value + "<br />";
							
						}
						
					}
					
					userValuePanel.textContent = null;
					userValuePanel.insertAdjacentHTML("beforeend", checkValue);
					
				};
				
				var label = object.create('label', null, [checkBox, valueName], null, null, null, null);
				checkBoxPanel.appendChild(label);
				inputData[inputName][i] = checkBox;
				
				if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
					
					checkBox.checked = false;
					
				}
				//checkBox.checked = false;
				object._console.log(checkBox.checked);
				
			}
			
			if (userValue != null && userValue.length != 0) {
				
				object._console.log(valueList);
				object._console.log(input);
				checkBoxPanel.classList.add("hidden_panel");
				valuePanel.setAttribute("data-edit", "1");
				var checkValue = "";
				for (var i = 0; i < valueList.length; i++) {
					
					checkValue += valueList[i] + "<br />";
					
				}
				
				userValuePanel.classList.remove("hidden_panel");
				userValuePanel.insertAdjacentHTML("beforeend", checkValue);
				if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
					
					checkBoxPanel.classList.remove("hidden_panel");
					userValuePanel.classList.add("hidden_panel");
					valuePanel.removeAttribute("data-edit");
					for (var i in inputData[inputName]) {
						
						object._console.log(inputData[inputName][i]);
						inputData[inputName][i].checked = "";
						
					}
					
				}
				
			}
			
		} else if (input['type'] == 'RADIO') {
			
			if (inputData[inputName] == null) {
				
				inputData[inputName] = {};
				
			}
			
			if (typeof input['value'] == "string") {
				
				input['value'] = input['value'].replace(/\\/g, "");
				
			}
			
			var radioBoxPanel = object.create('div', null, null, inputId, null, null, null);
			valuePanel.appendChild(radioBoxPanel);
			
			for (var i = 0; i < list.length; i++) {
			//for(var key in list){
				
				object._console.log(i + " = " + list[i]);
				if (typeof list[i] == "string") {
					
					list[i] = list[i].replace(/\\/g, "");
					
				}
				var valueName = object.create('span', list[i], null, null, null, 'radio_title', null);
				var radioBox = object.createInputElement('input', 'radio', inputName, list[i], null, null, null, null, 'form_radio', {value: i} );
				if (i == 0) {
					
					radioBox.checked = "checked";
					
				}
				
				if (input['value'] == list[i]) {
					
					object._console.log("value = " + input['value']);
					radioBox.checked = "checked";
					
				}
				
				if (userValue == list[i]) {
					
					radioBox.checked = "checked";
					
				}
				
				if (inputData[inputName] != null && inputData[inputName][i] != null) {
				
					radioBox = inputData[inputName][i];
					
				}
				
				radioBox.onchange = function(){
					
					var radioBox = this;
					userValuePanel.textContent = radioBox.value;
					if (typeof eventAction == 'function') {
						
						eventAction(this);
						
					}
					
				};
				
				var label = object.create('label', null, [radioBox, valueName], null, null, null, null);
				radioBoxPanel.appendChild(label);
				inputData[inputName][i] = radioBox;
				
			}
			
			if (userValue != null && userValue.length != 0) {
				
				radioBoxPanel.classList.add("hidden_panel");
				valuePanel.setAttribute("data-edit", "1");
				userValuePanel.classList.remove("hidden_panel");
				userValuePanel.textContent = userValue;
				if (input.isTerms != null && input.isTerms == 'true' && object.getAdmin() === false) {
					
					radioBoxPanel.classList.remove("hidden_panel");
					userValuePanel.classList.add("hidden_panel");
					valuePanel.removeAttribute("data-edit");
					
				}
				
			}
			
		} else if(input['type'] == 'TEXTAREA') {
			
			var testareaBox = object.create('textarea', null, null, inputId, null, 'form_textarea', null);
			if (inputData[inputName] != null && inputData[inputName].textBox != null) {
				
				testareaBox = inputData[inputName].textBox;
				
			}
			
			object._console.log(typeof input.placeholder);
			if (typeof input.placeholder == 'string' && input.placeholder.length > 0) {
				
				testareaBox.setAttribute('placeholder', input.placeholder);
				
			}
			
			if (valueList != null) {
				
				
				
			}
			
			if (input.value != null && (typeof input.value == 'string' || typeof input.value == 'number')) {
				
				testareaBox.textContent = input.value;
				
			}
			
			valuePanel.appendChild(testareaBox);
			inputData[inputName] = {textBox: testareaBox};
			
		}
		
		if (input.description != null) {
			
			var description = object.create('div', input.description.replace(/\\/g, ""), null, null, null, 'description', null);
			valuePanel.appendChild(description);
			
		}
		
		return valuePanel;
		
	};
	
	Booking_Package_Input.prototype.checkEmailAddress = function(email) {
		
		if (email.length == 0) {
			
			return true;
			
		}
		
		if (email.match(/.+@.+\..+/) == null) {
			
			return false;
			
		} else {
			
			return true;
			
		}
		
	};
    
    Booking_Package_Input.prototype.inputCheck = function(keyName, input, inputBox, valueList, requestPage){
		
		var object = this;
		var array = [];
		var bool = false;
		for(var key in inputBox){
			
			object._console.log(inputBox[key]);
			var inputBool = false;
			if (
				parseInt(input['inputLimit']) == 0 || 
				parseInt(input['inputLimit']) == 1 || 
				parseInt(input['inputLimit']) == 2 || 
				input['required'] != null
			) {
				
				inputBool = true;
				if (parseInt(input['inputLimit']) == 2 || input['required'] == 'false') {
					
					inputBool = false;
					
				}
				
				if (requestPage == 'dashboard' && input.required == 'true_frontEnd') {
					
					inputBool = false;
					
				}
				
				object._console.log("inputBool = " + inputBool);
				if (input['inputType'] == 'TEXT' || input['type'] == 'TEXT') {
					
					object._console.log(input);
					var text = inputBox[key].value.replace(/\s+/g, "");
					if (text.length != 0 && inputBool == true) {
						
						array.push(inputBox[key].value);
						bool = true;
						
					} else if (inputBool == false){
						
						array.push(inputBox[key].value);
						bool = true;
						
					}
					
					if (input['isEmail'] == 'true') {
						
						bool = this.checkEmailAddress(text);
						if (bool === true && inputBool === true && text.length == 0) {
							
							bool = false;
							
						}
						
					}
					
				} else if (input['inputType'] == 'SELECT' || input['type'] == 'SELECT' || input['inputType'] == 'SELECT_GROUP' || input['inputType'] == 'SELECT_TIMEZONE') {
					
					object._console.log(input['inputType']);
					var selectKey = inputBox[key].selectedIndex;
					object._console.log("selectKey = " + selectKey);
					if (selectKey >= 0) {
						
						if (selectKey != null && inputBool == true) {
							
							array.push(inputBox[key].options[selectKey].value);
							bool = true;
							
						} else if (selectKey != null && inputBool == false) {
							
							array.push(inputBox[key].options[selectKey].value);
							bool = true;
							
						}
						
					} else {
						
						bool = true;
						
					}
					
				} else if (input['inputType'] == 'CHECK' || input['type'] == 'CHECK') {
					
					var value = null;
					if (inputBox[key].checked == true && inputBool == true) {
						
						value = inputBox[key].value;
						array.push(inputBox[key].value);
						bool = true;
						
					} else if(inputBool == false) {
						
						if (inputBox[key].checked == true) {
							
							value = inputBox[key].value;
							array.push(inputBox[key].value);
							
						}
						bool = true;
						
					}
					
					if (value != null && input['valueList'] != null) {
						
						array = [];
						object._console.log("value = " + value);
						for(var valueKey in input['valueList']){
							
							if (input['valueList'][valueKey] == value) {
								
								array.push(valueKey);
								break;
								
							}
							
						}
						
						object._console.log(valueKey);
						
					}
					
			    } else if (input['inputType'] == 'RADIO' || input['type'] == 'RADIO') {
					
					var value = null;
					object._console.log(inputBox[key].checked);
					if (inputBox[key].checked == true && inputBool == true) {
						
						value = inputBox[key].value;
						array.push(inputBox[key].value);
						bool = true;
						
					} else if (inputBool == false) {
						
						if (inputBox[key].checked == true) {
							value = inputBox[key].value;
							array.push(inputBox[key].value);
						}
						bool = true;
						
					}
					
					if (value != null && input['valueList'] != null) {
						
						array = [];
						object._console.log("value = " + value);
						for(var valueKey in input['valueList']){
							
							if (input['valueList'][valueKey] == value) {
								
								array.push(valueKey);
								break;
								
							}
							
						}
						
						object._console.log(valueKey);
						
					}
					
			    } else if (input['inputType'] == 'TEXTAREA' || input['type'] == 'TEXTAREA') {
				    
				    if (inputBox[key].value.length != 0 && inputBool == true) {
					    
					    array.push(inputBox[key].value);
					    bool = true;
					    
				    } else if(inputBool == false) {
					    
					    array.push(inputBox[key].value);
					    bool = true;
					    
				    }
				    
			    }
			    
		    }
		    
		    if (bool == true) {
			    
			    valueList[keyName] = array;
			    if (input['inputType'] == 'CHECK' || input['type'] == 'CHECK') {
			    	
			    	valueList[keyName] = JSON.stringify(array);
			    	
			    }
			    
		    } else {
				
				valueList[keyName] = [];
				
			}
			
		}
		
		return bool;
		
    };
    
    Booking_Package_Input.prototype.create = function(elementType, text, childElements, id, style, className, data_x) {
        
        var panel = this._element.create(elementType, text, childElements, id, style, className, data_x);
        return panel;
        
    };
    
    Booking_Package_Input.prototype.createButtonPanel = function(id, style, className, buttons) {
        
        var buttonPanel = this._element.createButtonPanel(id, style, className, buttons);
        return buttonPanel;
        
    };
    
    Booking_Package_Input.prototype.createButton = function(id, style, className, data_x, text) {
        
        var button = this._element.createButton(id, style, className, data_x, text);
        return button;
        
    };
    
    Booking_Package_Input.prototype.createInputElement = function(tagName, type, name, value, text, disabled, id, style, className, data_x) {
        
        var input = this._element.createInputElement(tagName, type, name, value, text, disabled, id, style, className, data_x);
        return input;
        
    };
    
    
