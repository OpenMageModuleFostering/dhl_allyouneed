<?php

/**
 * Allyouneed category resource model
 *
 * @category   Dhl
 * @package    Dhl_MeinPaket
 */
class Dhl_MeinPaket_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {
		$this->_init ( 'meinpaket/category', 'category_id' );
	}
}

