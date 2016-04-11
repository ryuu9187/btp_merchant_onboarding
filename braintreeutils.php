<?php

	// Dependencies
	require_once dirname(__FILE__) . '/lib/Braintree.php';
	
	class BrainTreeUtils {
	
		private static $masterMerchantID = null;
		
		public static function configure($env, $masterMerchantID, $publicKey, $privateKey) {
			static::$masterMerchantID = $masterMerchantID;
			
			Braintree_Configuration::environment($env);
			Braintree_Configuration::merchantId($masterMerchantID);
			Braintree_Configuration::publicKey($publicKey);
			Braintree_Configuration::privateKey($privateKey);
		}

		public static function addMerchant($params) {
			$invidualParams = [
				'firstName' => static::unsanitize($params->get('firstname')),
				'lastName' => static::unsanitize($params->get('lastname')),
				'email' => static::unsanitize($params->get('email')),
				'phone' => static::unsanitize($params->get('phone')),
				'dateOfBirth' => static::unsanitize($params->get('dob')),
				'address' => [
					'streetAddress' => static::unsanitize($params->get('street')),
					'locality' => static::unsanitize($params->get('city')),
					'region' => static::unsanitize($params->get('state')),
					'postalCode' => static::unsanitize($params->get('zip'))
				]
			];
			
			$bizParams = [
					'legalName' => static::unsanitize($params->get('bizname')),
					'dbaName' => static::unsanitize($params->get('dba')),
					'taxId' => static::unsanitize($params->get('tax')),
					'address' => [
						'streetAddress' => static::unsanitize($params->get('bizstreet')),
						'locality' => static::unsanitize($params->get('bizcity')),
						'region' => static::unsanitize($params->get('bizstate')),
						'postalCode' => static::unsanitize($params->get('bizzip'))
					]
			];
			
			$fundParams = [
				'descriptor' => static::unsanitize($params->get('fundname')),
				'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
				'email' => static::unsanitize($params->get('fundemail')),
				'mobilePhone' => static::unsanitize($params->get('fundphone')),
				'accountNumber' => static::unsanitize($params->get('account')),
				'routingNumber' => static::unsanitize($params->get('routing'))
			];
  
			$merchantAccountParams = [
				'individual' => $invidualParams,
				'business' => $bizParams,
				'funding' => $fundParams,
				'tosAccepted' => true,
				'masterMerchantAccountId' => static::unsanitize($params->get('masterId')),
				'id' => static::unsanitize($params->get('id'))
			];
			
			try {
				return Braintree_MerchantAccount::create($merchantAccountParams);
			} catch (Exception $ex) {
				return "An error occured";
			}
		}
		
		private static function unsanitize($data) {
		
			if ($data != null) {
				$data = str_replace("__AT_SYMBOL__", "@", $data);
				$data = str_replace("__NUMBER_SYMBOL__", "#", $data);
				$data = str_replace("__DOLLAR_SYMBOL__", "$", $data);
				$data = str_replace("__PERCENT_SYMBOL__", "%", $data);
				$data = str_replace("__CARROT_SYMBOL__", "^", $data);
				$data = str_replace("__AMPERSAND_SYMBOL__", "&", $data);
				$data = str_replace("__PLUS_SYMBOL__", "+", $data);
				$data = str_replace("__EQUALS_SYMBOL__", "=", $data);
				$data = str_replace("__ASTERISK_SYMBOL__", "*", $data);
				$data = str_replace("__BANG_SYMBOL__", "!", $data);
			}
			
			return $data;
		}
		
	}

?>