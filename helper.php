<?php
	
	// Dependencies
	require_once dirname(__FILE__) . '/braintreeutils.php';
	
    class ModBTPOnboardingHelper
    {
		// Accessible variables from mod_btp_onboarding.php
		public static $environment = null;
		public static $masterMerchantId = null;
		public static $publicKey = null;
		public static $privateKey = null;
		private static $configuredBrainTree = false;
		
		private static function setupBrainTree() {
			if (!$configuredBrainTree) {
				BrainTreeUtils::configure($environment, $masterMerchantId, $publicKey, $privateKey);
				$configuredBrainTree = true;
			}
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
			
			// Validation errors?
			if (count($validationErrors) == 0) {
				echo json_encode(static::addMerchant($app->input));
			} else {
				// Return error message
				echo new JResponseJson(null,
					JText::_("{ \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}"), true);
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