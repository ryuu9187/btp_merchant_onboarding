<?php
    
    // No direct access
    defined('_JEXEC') or die;
	
	// Include the syndicate functions only once
    require_once dirname(__FILE__) . '/helper.php';
	
	// Store module params
	ModBTPOnboardingHelper::$environment = $params->get('environment_mode');
	ModBTPOnboardingHelper::$masterMerchantId = $params->get('maste_merchant_id');
	ModBTPOnboardingHelper::$publicKey = $params->get('public_key');
	ModBTPOnboardingHelper::$privateKey = $params->get('private_key');
	
    require JModuleHelper::getLayoutPath('mod_btp_onboarding');
?>