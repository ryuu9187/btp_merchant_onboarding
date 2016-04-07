<?php
	// Dependencies
	require_once dirname(__FILE__) . '/braintreeutils.php';
	
    class ModHelloWorldHelper
    {
		
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
			
            // $config = static::getBrainTreeParams();
            $app = JFactory::getApplication();
			$validationErrors = self::getValidationErrors($app->input);
			
			if (count($validationErrors) == 0) {
				echo json_encode(BrainTreeUtils::test());
			} else {
				echo new JResponseJson(null,
					JText::_("{ \"validationErrors\" : \"" . implode(";", $validationErrors) . "\"}"), true);
			}
			
			//close the $app
			$app->close();
        }
		
    }

?>