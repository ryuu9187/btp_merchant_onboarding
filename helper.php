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
		// For the create method
		private static $createParams = array(
			"firstname" => true,
			"lastname" => true,
			"email" => true,
			"phone" => true,
			"dob" => true,
			"street" => true,
			"state" => true,
			"city" => true,
			"zip" => true,
			"bizname" => false,
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
		
		private static $findParams = array("id" => true);
		
		private static $updateParams = array("id" => true);
		
		private static function getValidationErrors($formParams, $reqParams) {
			$validationErrors = array();
			
			foreach($formParams as $key => $value) {
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
		
		private static function getMerchantJson($merchantAccount) {
			$id = $merchantAccount != null ? $merchantAccount->id : "Unknown";
			$status = $merchantAccount != null ? $merchantAccount->status : "Unknown";
			
			$json = "{";
			$json .= "\"status\" : \"" . $status . "\"";
			$json .= ", \"id\" : \"" . $id . "\"";
			$json .= " }";
			
			return $json;
		}
		
		private static function getBtpErrorsJson($errors) {
			$prepend = "";
			$deep = $errors->deepAll();
			
			$json = "[";
			foreach($deep as $err) {
				$json .= $prepend . "\"" . $err->__get("message") . "\"";
				$prepend = ", ";
			}
			$json .= "]";
			
			return $json;
		}
		
        // Default method is getAjax
        // Ajax methods must end in Ajax
        public static function createAjax($params) {
            $app = JFactory::getApplication();
			$postParams = $app->input;
			
			// Validate request
			$validationErrors = static::getValidationErrors(static::$createParams, $postParams);
			$json = "{ \"success\": ";
			
			// Validation errors check
			if (count($validationErrors) == 0) {
				$result = static::addMerchant($postParams);
				$json .= ($result->success ? "true" :"false");
				
				// Success
				if ($result->success) {
					$merchant = $result->merchantAccount;
					$master = $merchant != null ? $merchant->masterMerchantAccount : null;
					$json .= ", \"merchantAccount\": " . static::getMerchantJson($merchant);
					$json .= ", \"masterMerchantAccount\": " . static::getMerchantJson($master);
				} else {
					// Failure
					$json .= ", \"message\" : { ";
					$json .= "\"btpErrors\" : " . static::getBtpErrorsJson($result->errors);
					$json .= "}";
				}
				
				$json .= "}";
				
				echo $json;
			} else {
				// Return error message
				$json .= "false";
				$json .= ", \"message\" : { \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}}";
				echo $json;
			}
			
			//close the $app
			$app->close();
        }
		
		public static function updateAjax($params) {
			$app = JFactory::getApplication();
			$postParams = $app->input;
			
			$validationErrors = static::getValidationErrors(static::$updateParams, $postParams);
			$json = "{ \"success\": ";
			
			// Validation errors check
			if (count($validationErrors) == 0) {
				$result = static::updateMerchant($postParams);
				$json .= ($result->success ? "true" : "false");
				
				// Failure
				if (!$result->success) {
					$json .= ", \"message\" : { ";
					$json .= "\"btpErrors\" : " . static::getBtpErrorsJson($result->errors);
					$json .= "}";
				}
				
				$json .= "}";
				
				echo $json;
			} else {
				// Return error message
				$json .= "false";
				$json .= ", \"message\" : { \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}}";
				echo $json;
			}
			
			$app->close();
		}
		
		// TODO: Genericize the json response building
		public static function findAjax($params) {
			$app = JFactory::getApplication();
			$postParams = $app->input;
			
			$validationErrors = static::getValidationErrors(static::$findParams, $postParams);
			$json = "{ \"success\": ";
			
			// Validation errors check
			if (count($validationErrors) == 0) {
				$json .= "true";
				
				$result = static::searchMerchant($postParams);
				
				// Individual
				$indDetails = $result->individualDetails;
				$address = $indDetails->addressDetails;
				$json .= ", \"Individual\": {";
					$json .= "\"firstname\" : \"" . $indDetails->firstName . "\"";
					$json .= ",\"lastname\" : \"" .  $indDetails->lastName . "\"";
					$json .= ",\"email\" : \"" . $indDetails->email . "\"";
					$json .= ",\"dob\" : \"" . $indDetails->dateOfBirth . "\"";
					$json .= ",\"phone\" : \"" . $indDetails->phone . "\"";
					$json .= ",\"street\" : \"" . $address->streetAddress . "\"";
					$json .= ",\"city\" : \"" . $address->locality . "\"";
					$json .= ",\"state\" : \"" . $address->region . "\"";
					$json .= ",\"zip\" : \"" . $address->postalCode . "\"";
				$json .= "}";
				
				// Business
				$bizDetails = $result->businessDetails;
				$address = $bizDetails->addressDetails;
				$json .= ", \"Business\": {";
					$json .= "\"bizname\" : \"" . $bizDetails->legalName . "\"";
					$json .= ",\"dba\" : \"" .  $bizDetails->dbaName . "\"";
					$json .= ",\"tax\" : \"" . $bizDetails->taxId . "\"";
					$json .= ",\"bizstreet\" : \"" . $address->streetAddress . "\"";
					$json .= ",\"bizcity\" : \"" . $address->locality . "\"";
					$json .= ",\"bizstate\" : \"" . $address->region . "\"";
					$json .= ",\"bizzip\" : \"" . $address->postalCode . "\"";
				$json .= "}";
				
				// Funding
				$funDetails = $result->fundingDetails;
				$json .= ", \"Funding\": {";
					$json .= "\"fundname\" : \"" . $funDetails->descriptor . "\"";
					$json .= ",\"fundemail\" : \"" .  $funDetails->email . "\"";
					$json .= ",\"fundphone\" : \"" . $funDetails->mobilePhone . "\"";
					$json .= ",\"account\" : \"" . $funDetails->accountNumberLast4 . "\"";
					$json .= ",\"routing\" : \"" . $funDetails->routingNumber . "\"";
				$json .= "}";
				
				// Merchant
				$masterMerchant = $result->masterMerchantAccount;
				
				$json .= ", \"Merchant\": {";
					$json .= "\"masterId\" : \"" . $masterMerchant->id . "\"";
					$json .= ",\"id\" : \"" . $result->id . "\"";
				$json .= "}";
				
				
				$json .= "}";
				echo $json;
			} else {
				// Return error message
				$json .= "false";
				$json .= ", \"message\" : { \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}}";
				echo $json;
			}
			
			$app->close();
		}
		
		private static function searchMerchant($params) {
			static::setupBrainTree();
			return BrainTreeUtils::findMerchant($params);
		}
		
		private static function addMerchant($params) {
			static::setupBrainTree();
			return BrainTreeUtils::addMerchant($params);
		}
		
		private static function updateMerchant($params) {
			static::setupBrainTree();
			return BrainTreeUtils::updateMerchant($params);
		}
		
	}

?>