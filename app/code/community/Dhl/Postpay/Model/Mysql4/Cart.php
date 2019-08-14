<?php
class Dhl_Postpay_Model_Mysql4_Cart extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Initialize domain model and set primary key
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'postpay/cart', 'cart_id' );
	}
}