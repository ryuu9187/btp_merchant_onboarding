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
	<button onclick="updateMerchant(); return false;" class="btn btn-primary">Update Merchant</button>
	<button onclick="searchMerchant(); return false;" class="btn btn-primary">Search Merchant</button>
	<button onclick="alert('This feature has not yet been enabled.'); return false;" class="btn btn-primary">Delete Merchant</button>
</form>