<?php
    defined('_JEXEC') or die;
    
    // Load jQuery
    JHtml::_('jquery.framework', false);
    
    $document = JFactory::getDocument();
    $document->addScript('/dev/modules/mod_btp_onboarding/js/main.js');
?>

<form id="createMerchantForm" class="form-validate form-horizontal well">
	<div id="merchantFields"></div>
	<button onclick="createMerchant(); return false;" class="btn btn-primary">Create Merchant</button>
</form>