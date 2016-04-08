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
				'firstName' => $params->get('firstname'),
				'lastName' => $params->get('lastname'),
				'email' => $params->get('email'),
				'phone' => $params->get('phone'),
				'dateOfBirth' => $params->get('dob'),
				'address' => [
					'streetAddress' => $params->get('street'),
					'locality' => $params->get('city'),
					'region' => $params->get('state'),
					'postalCode' => $params->get('zip')
				]
			];
			
			$bizParams = [
					'legalName' => $params->get('bizname'),
					'dbaName' => $params->get('dba'),
					'taxId' => $params->get('tax'),
					'address' => [
						'streetAddress' => $params->get('bizstreet'),
						'locality' => $params->get('bizcity'),
						'region' => $params->get('bizstate'),
						'postalCode' => $params->get('bizzip')
					]
			];
			
			$fundParams = [
				'descriptor' => $params->get('fundname'),
				'destination' => Braintree_MerchantAccount::FUNDING_DESTINATION_BANK,
				'email' => $params->get('fundemail'),
				'mobilePhone' => $params->get('fundphone'),
				'accountNumber' => $params->get('account'),
				'routingNumber' => $params->get('routing')
			];
  
			$merchantAccountParams = [
				'individual' => $invidualParams,
				'business' => $bizParams,
				'funding' => $fundParams,
				'tosAccepted' => true,
				'masterMerchantAccountId' => static::$masterMerchantID,
				'id' => $params->get('id')
			];
			
			try {
				return Braintree_MerchantAccount::create($merchantAccountParams);
			} catch (Exception $ex) {
				return "An error occured";
			}
		}
	}

?>