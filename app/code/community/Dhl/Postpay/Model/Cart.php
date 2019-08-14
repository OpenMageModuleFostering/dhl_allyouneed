<?php
class Dhl_Postpay_Model_Cart extends Mage_Core_Model_Abstract {
	const STATE_PENDING = 'PENDING';
	const STATE_CREATEDORDER = 'CREATEDORDER';
	const STATE_CANCELED = 'CANCELED';
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Varien_Object::_construct()
	 */
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'postpay/cart' );
	}
	
	/**
	 */
	public function generateNotificationId() {
		$this->setNotificationId ( $this->generateRandomString () );
	}
	
	/**
	 *
	 * @param number $length        	
	 * @return string
	 */
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen ( $characters );
		$randomString = '';
		for($i = 0; $i < $length; $i ++) {
			$randomString .= $characters [rand ( 0, $charactersLength - 1 )];
		}
		return $randomString;
	}
	
	/**
	 * Processing object before save data
	 *
	 * @return Mage_Core_Model_Abstract
	 */
	protected function _beforeSave() {
		if (strlen ( $this->getNotificationId () ) <= 0) {
			$this->generateNotificationId ();
		}
		
		if (! $this->getCreatedAt ()) {
			$this->setCreatedAt ( time () );
		}
		
		return parent::_beforeSave ();
	}
}