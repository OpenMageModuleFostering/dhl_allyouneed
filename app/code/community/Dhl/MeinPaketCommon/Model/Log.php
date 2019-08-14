<?php
class Dhl_MeinPaketCommon_Model_Log extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'meinpaketcommon/log' );
	}
	
	/**
	 * Set received data.
	 * Filter if response is too big for mysql.
	 * 
	 * @param unknown $data        	
	 */
	public function setReceived($data) {
		if (count ( $data ) < 1024 * 1024 * 8) {
			parent::setReceived ( $data );
		} else {
			parent::setReceived ( 'Response too big for mysql' );
		}
	}
}