<?php
    
    // No direct access
    defined('_JEXEC') or die;
	
	// Include the syndicate functions only once
    require_once dirname(__FILE__) . '/helper.php';
	
	// Store module params
	ModHelloWorldHelper::$environment = $params->get('environment_mode');
	ModHelloWorldHelper::$masterMerchantId = $params->get('maste_merchant_id');
	ModHelloWorldHelper::$publicKey = $params->get('public_key');
	ModHelloWorldHelper::$privateKey = $params->get('private_key');
	
    require JModuleHelper::getLayoutPath('mod_helloworld');
?>