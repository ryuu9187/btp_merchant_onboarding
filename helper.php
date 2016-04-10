<?php
	
	// Dependencies
	require_once dirname(__FILE__) . '/braintreeutils.php';
	
    class ModBTPOnboardingHelper
    {
		private static function setupBrainTree() {
			$module = JModuleHelper::getModule('mod_btp_onboarding');
			$modParams = new JRegistry($module->params);

			BrainTreeUtils::configure(
				$modParams['environment_mode'],
				$modParams['master_merchant_id'],
				$modParams['public_key'],
 				$modParams['private_key']);
		}
		
		// Array containg the expected form parameters and if they're required
		private static $formParams = array(
			"firstname" => true,
			"lastname" => true,
			"email" => true,
			"phone" => true,
			"dob" => true,
			"street" => true,
			"state" => true,
			"city" => true,
			"zip" => true,
			"bizname" => true,
			"dba" => false,
			"tax" => false,
			"bizstreet" => true,
			"bizstate" => true,
			"bizcity" => true,
			"bizzip" => true,
			"fundname" => true,
			"fundemail" => true,
			"fundphone" => true,
			"account" => true,
			"routing" => true,
			"masterId" => true,
			"id" => false
		);
		
		private static function getValidationErrors($reqParams) {
			$validationErrors = array();
			
			foreach(static::$formParams as $key => $value) {
				$argument = trim($reqParams->getString($key));
				
				if ($argument != '') {
					$args .= " " . $key . " " . $argument;
				} else if ($value) {
					// Field is required but not set: add validation error
					array_push($validationErrors, $key);
				}
			}
			
			return $validationErrors;
		}
		
        // Default method is getAjax
        // Ajax methods must end in Ajax
        public static function createAjax($params) {
            $app = JFactory::getApplication();
			$postParams = $app->input;
			
			// Validate request
			$validationErrors = self::getValidationErrors($postParams);
			$json = "{ \"success\": ";
			
			// Validation errors?
			if (count($validationErrors) == 0) {
				$result = static::addMerchant($postParams);
				$json .= ($result->success ? "true" :"false");
				
				if (!$result->success) {
					$json .= ", \"message\" : { \"btpErrors\" : [";
					
					$errors = $result->errors->deepAll();
					$prepend = "";
					
					foreach($errors as $err) {
						$json .= $prepend . "\"" . $err->__get("message") . "\"";
						$prepend = ", ";
					}
					
					$json .= "] }";
				}
				
				$json .= "}";
				
				echo $json;
			} else {
				// Return error message
				$json .= "false";
				$json .= ", \"message\" : { \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}}";
				echo $json;
				// echo new JResponseJson(null, JText::_("{ \"validationErrors\" : " .  . "}"), true);
			}
			
			//close the $app
			$app->close();
        }
		
		private static function addMerchant($params) {
			static::setupBrainTree();
			return BrainTreeUtils::addMerchant($params);
		}
		
	}

?>