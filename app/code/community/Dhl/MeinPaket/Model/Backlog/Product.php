<?php
class Dhl_MeinPaket_Model_Backlog_Product extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'meinpaket/backlog_product' );
	}
}