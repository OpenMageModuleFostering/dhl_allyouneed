<?php
class Dhl_Postpay_Model_Mysql4_Cart_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Initialize domain model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'postpay/cart' );
	}
}
