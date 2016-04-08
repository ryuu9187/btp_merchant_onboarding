<?php
	
	// Dependencies
	require_once dirname(__FILE__) . '/braintreeutils.php';
	
    class ModBTPOnboardingHelper
    {
			// Accessible variables from module.php
		public static $environment = null;
		public static $masterMerchantId = null;
		public static $publicKey = null;
		public static $privateKey = null;
		
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
			"routing" => true
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
				// Add Merchant
				echo json_encode(static::addMerchant($app->input));
			} else {
				// Return error message
				echo new JResponseJson(null,
					JText::_("{ \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}"), true);
			}
			
			//close the $app
			$app->close();
        }
		
		private static function addMerchant($postParameters) {
			$gateway = new BrainTreeGateway(
				static::$environment,
				static::$masterMerchantId,
				static::$publicKey,
				static::$privateKey);
					/*$config->getString("environment_mode"),
					$config->getString("master_merchant_id"),
					$config->getString("public_key") ,
					$config->getString("private_key"));*/
			
			return $gateway->test();
		}
		
    }

?>