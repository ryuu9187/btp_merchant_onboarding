<?php

	// Dependencies
	require_once dirname(__FILE__) . '/lib/Braintree.php';
	
	class BrainTreeGateway {
		
		private $merchantID = null;
		
		public function __construct($env, $merchantID, $publicKey, $privateKey) {
			$this->configure($env, $merchantID, $publicKey, $privateKey);
			$this->merchantID = $merchantID;
		}
		
		private function configure($env, $merchantID, $publicKey, $privateKey) {
			Braintree_Configuration::environment($env);
			Braintree_Configuration::merchantId($merchantID);
			Braintree_Configuration::publicKey($publicKey);
			Braintree_Configuration::privateKey($privateKey);
		}

	}

?>