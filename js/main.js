var formFields = [
	{ "Individual" : [
		fieldMeta("firstname", "First Name"),
		fieldMeta("lastname", "Last Name"),
		fieldMeta("email", "Email"),
		fieldMeta("phone", "Phone #"),
		fieldMeta("dob", "Date of Birth"),
		fieldMeta("street", "Street Address"),
		fieldMeta("city", "City"),
		fieldMeta("state", "State"),
		fieldMeta("zip", "Postal Code")]},
	{ "Business" : [
		fieldMeta("bizname", "Name"),
		fieldMeta("dba", "d.b.a."),
		fieldMeta("tax", "Tax ID"),
		fieldMeta("bizstreet", "Street Address"),
		fieldMeta("bizcity", "City"),
		fieldMeta("bizstate", "State"),
		fieldMeta("bizzip", "Postal Code")]},
	{ "Funding" : [
		fieldMeta("fundname", "Name of Account"),
		fieldMeta("fundemail", "Email"),
		fieldMeta("fundphone", "Phone #"),
		fieldMeta("account", "Account #"),
		fieldMeta("routing", "Routing #")]},
	{ "Merchant" : [
		fieldMeta("id", "Merchant ID")]}
];

function fieldMeta(value, name) {
	return {
		name : name,
		value : value,
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
				var $field = jQuery("<input type='text' name='" + fields[i].value + "'/>");
				
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

function toggleReqMessage(fieldName, show) {
	var field = findField(fieldName);
	field && (show ? field.$requiredEl.show() : field.$requiredEl.hide());
}

function createMerchant() {
	jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_ajax&module=btp_onboarding&method=create&format=json",
		data: $form.serialize(),
		success: function(response){
			try {
				var json = JSON.parse(response);
			
				if (json && json.success) {
					alert("Merchant queued for creation");
					console.log("Merchant queued for creation");
					console.log(json);
					// TODO: Clear validation errors + (clear fields?)
				} else {
					var errorObj = JSON.parse(json.message);
				
					if (errorObj.validationErrors) {
						var reqFields = errorObj.validationErrors.split(";");
						for (var f in reqFields) {
							reqFields[f] && toggleReqMessage(reqFields[f], true);
						}
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