<?php
class Dhl_MeinPaketCommon_Model_Mysql4_Async_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Initialize domain model
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'meinpaketcommon/async' );
	}
}
