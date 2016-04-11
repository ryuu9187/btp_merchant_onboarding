var formFields = [
	{ "Individual" : [
		fieldMeta("firstname", "First Name"),
		fieldMeta("lastname", "Last Name"),
		fieldMeta("email", "Email", "localpart@domain.com"),
		fieldMeta("phone", "Phone #", "XXXXXXXXXX"),
		fieldMeta("dob", "Date of Birth", "YYYY-MM-DD"),
		fieldMeta("street", "Street Address"),
		fieldMeta("city", "City"),
		fieldMeta("state", "State"),
		fieldMeta("zip", "Postal Code")]},
	{ "Business" : [
		fieldMeta("bizname", "Name"),
		fieldMeta("dba", "d.b.a."),
		fieldMeta("tax", "Tax ID", "XX-XXXXXXX"),
		fieldMeta("bizstreet", "Street Address"),
		fieldMeta("bizcity", "City"),
		fieldMeta("bizstate", "State"),
		fieldMeta("bizzip", "Postal Code")]},
	{ "Funding" : [
		fieldMeta("fundname", "Name of Account"),
		fieldMeta("fundemail", "Email", "localpart@domain.com"),
		fieldMeta("fundphone", "Phone #", "XXXXXXXXXX"),
		fieldMeta("account", "Account #"),
		fieldMeta("routing", "Routing #")]},
	{ "Merchant" : [
		fieldMeta("masterId", "Master Merchant ID*"),
		fieldMeta("id", "Merchant ID (create/search/delete)")]}
];

function fieldMeta(value, name, placeholderText) {
	return {
		name : name,
		value : value,
		$node : null,
		phText : placeholderText,
		$requiredEl : jQuery("<div style='display:none;width:50%;padding:2px 10px;' class='alert alert-error'>" + name + " is required</div>")
	};
}

function createForm() {
	for (var s in formFields) {
		var section = formFields[s];
		
		for (var g in section) {
			var fields = section[g];
			var $group = jQuery("<div class='control-group'><p>" + g + "</p></div>");
			
			for (var i = 0; i < fields.length; i++) {
				var $labelContainer = jQuery("<div class='control-label'></div>");
				var $label = jQuery("<label for='" + fields[i].value + "'>" + fields[i].name + "</label>");
				$labelContainer.append($label);
				
				var $fieldContainer = jQuery("<div class='controls'></div>");
				var $field = jQuery("<input type='text' " +
					"name='" + fields[i].value + "'" +
					(fields[i].phText ? (" placeholder='" + fields[i].phText + "'") : "") +
					"/>");
				
				fields[i].$node = $field;
				
				// Prevent field from auto-submitting w/ enter button
				$field.keydown(function(e) {
					if(e.keyCode == 13) {
						return false;
					}
				});
				
				$fieldContainer.append($field);
				$fieldContainer.append(fields[i].$requiredEl); // Required message
				
				$group.append($labelContainer); // Label
				$group.append($fieldContainer); // Field
				
			}
			
			$form.find("#merchantFields").append($group);
		}
	}
}

function findField(name) {
	for (var s in formFields) {
		for (var g in formFields[s]) {
			for(var f in formFields[s][g]) {
				if (formFields[s][g][f].value === name) {
					return formFields[s][g][f];
				}
			}
		}
	}
	return null;
}

function clearFields() {
	for (var s in formFields) {
		for (var g in formFields[s]) {
			for(var f in formFields[s][g]) {
				var field = formFields[s][g][f];
				field.$node.val("");
			}
		}
	}
}

function clearValidationErrors() {
	for (var s in formFields) {
		for (var g in formFields[s]) {
			for(var f in formFields[s][g]) {
				var field = formFields[s][g][f];
				field.$requiredEl.hide();
			}
		}
	}
}

function toggleReqMessage(fieldName, show) {
	var field = findField(fieldName);
	field && (show ? field.$requiredEl.show() : field.$requiredEl.hide());
}

function createMerchant() {
	function getSuccessMessage(json) {
		var successMsg = "Merchant queued for creation!"
		successMsg += "\nId: " + json.merchantAccount.id;
		successMsg += "\nStatus: " + json.merchantAccount.status;
				
		successMsg += "\n";
		successMsg += "\nMaster Merchant Id: " + json.masterMerchantAccount.id;
		successMsg += "\nMaster Merchant Status: " + json.masterMerchantAccount.status;
		
		return successMsg;
	}

	function sanitize(data) {
		return data
			.replace(/%40/g, "__AT_SYMBOL__")
			.replace(/%23/g, "__NUMBER_SYMBOL__")
			.replace(/%24/g, "__DOLLAR_SYMBOL__")
			.replace(/%25/g, "__PERCENT_SYMBOL__")
			.replace(/%5E/g, "__CARROT_SYMBOL__")
			.replace(/%26/g, "__AMPERSAND_SYMBOL__")
			.replace(/%2B/g, "__PLUS_SYMBOL__")
			.replace(/%3D/g, "__EQUALS_SYMBOL__")
			.replace(/\*/g, "__ASTERISK_SYMBOL__")
			.replace(/!/g, "__BANG_SYMBOL__");
	}

	// Clear form validation errors
	clearValidationErrors();

	var formData = $form.serialize();
	
	// Send request
	jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_ajax&module=btp_onboarding&method=create&format=json",
		data: sanitize(formData),
		success: function(response){
			try {
				var json = JSON.parse(response);
			
				if (json && json.success) {
					var successMsg = getSuccessMessage(json);
					alert(successMsg); // Alert user
					console.log(successMsg); // Log
					clearFields(); // Update UI
				} else {
					var errorObj = json.message;
				
					if (errorObj.validationErrors) {
						var reqFields = errorObj.validationErrors.split(";");
						for (var f in reqFields) {
							reqFields[f] && toggleReqMessage(reqFields[f], true);
						}
					} else if (errorObj.btpErrors) {
						var errorMsg = errorObj.btpErrors.join("\n");
						console.error(errorMsg);
						alert("Create Merchant failed for the following reasons:\n\n" + errorMsg);
					} else {
						var errorMsg = "An unknown error occurred." + errorObj;
						console.error(errorMsg);
						alert(errorMsg);
					}
				}
			} catch (ex) {
				var errorMsg = response && response.message || ("An unknown error occurred: " + response);
				console.error(errorMsg);
				alert(errorMsg);
			}
		}
	});
}

jQuery(document).ready(function() {
	$form = jQuery("#createMerchantForm");
	createForm();
});