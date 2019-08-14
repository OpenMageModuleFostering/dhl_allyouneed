<?php
class Dhl_MeinPaket_Model_Mysql4_Backlog_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Initialize domain model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'meinpaket/backlog_product' );
	}
}