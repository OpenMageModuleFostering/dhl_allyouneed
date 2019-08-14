<?php

/**
 * MeinPaket Category Model.
 *
 * @category   Mage
 * @package    Dhl_MeinPaket
 */
class Dhl_MeinPaket_Model_Category extends Mage_Core_Model_Abstract {
	/**
	 * Constructor.
	 *
	 * @see Varien_Object::_construct()
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'meinpaket/category' );
	}
}
