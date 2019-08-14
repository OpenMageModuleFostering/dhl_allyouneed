<?php
class Dhl_MeinPaketCommon_Helper_Attribute extends Mage_Core_Helper_Abstract {
	protected $_ignoredAttributeCodes = array (
			'status' 
	);
	public function isExportableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute) {
		$isExportable = false;
		$data = $attribute->getData ( '' );
		
		if (! in_array ( $data ['attribute_code'], $this->_ignoredAttributeCodes ) && (! array_key_exists ( 'is_configurable', $data ) || $data ['is_configurable'])) {
			$isExportable = true;
		}
		
		return $isExportable;
	}
}
