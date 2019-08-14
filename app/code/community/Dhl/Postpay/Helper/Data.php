<?php

/**
 * Default helper for the Dhl_Postpay package.
 * 
 * @category    Dhl
 * @package     Dhl_Postpay
 * @subpackage  Helper
 */
class Dhl_Postpay_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 *
	 * @return string
	 */
	public function getExtensionVersion() {
		return ( string ) Mage::getConfig ()->getModuleConfig ( 'Dhl_Postpay' )->version;
	}
	
	/**
	 * Is postpay active?
	 * 
	 * @return bool
	 */
	public function isActive() {
		return ( bool ) Mage::getStoreConfigFlag ( 'payment/postpay_express/active' );
	}
}
