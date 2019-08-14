<?php

class Dhl_MeinPaket_Helper_Attribute extends Mage_Core_Helper_Abstract
{
	protected $_ignoredAttributeCodes = array('status');
	
	public function isExportableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
	{
		$isExportable	= false;
		$data			= $attribute->getData('');
    	
    	if(
    		!in_array($data['attribute_code'], $this->_ignoredAttributeCodes) && 
    		(!array_key_exists('is_configurable', $data) || $data['is_configurable']) /* && 
    		$data['frontend_input'] === 'select' && 
    		$data['is_required'] && 
    		$data['is_visible'] && 
    		$data['is_searchable']*/
    	) {
        	$isExportable = true;	
        }
		
		return $isExportable;
	}
}
