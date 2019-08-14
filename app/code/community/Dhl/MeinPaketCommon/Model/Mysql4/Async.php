<?php
class Dhl_MeinPaketCommon_Model_Mysql4_Async extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * Initialize domain model and set primary key
	 *
	 * @return void
	 */
	protected function _construct() {
		$this->_init ( 'meinpaketcommon/async', 'async_id' );
	}
}