<?php

/**
 * Allyouneed category collection.
 *
 * @category   Dhl
 * @package    Dhl_MeinPaket
 */
class Dhl_MeinPaket_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Constructor.
	 *
	 * @see Mage_Core_Model_Mysql4_Collection_Abstract::_construct()
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'meinpaket/category' );
	}
}
