<?php

/**
 * PHP memory limit control.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_System
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_System_MemoryLimiter extends Varien_Object {
	/**
	 *
	 * @var integer
	 */
	const MEMORY_LIMIT_MEDIUM = 256;
	
	/**
	 *
	 * @var integer
	 */
	const MEMORY_LIMIT_HIGH = 512;
	
	/**
	 *
	 * @var integer
	 */
	const MEMORY_LIMIT_VERY_HIGH = 1024;
	
	/**
	 * Sets the memory limit for the current script.
	 * If the current setting from PHP ini is greater than the given value,
	 * the method call will have no effect.
	 *
	 * @param integer $limit        	
	 * @return void
	 *
	 */
	public function setMemoryLimit($limit) {
		if (! in_array ( $limit, $this->getAllowedMemoryLimitValues () )) {
			return;
		}
		
		$currentLimit = $this->getLimitFromIni ();
		
		if ($currentLimit < $limit) {
			ini_set ( 'memory_limit', $limit . 'M' );
		}
	}
	
	/**
	 * Returns the current memory limit in megabytes.
	 *
	 * @return integer
	 */
	protected function getLimitFromIni() {
		return ( integer ) trim ( str_replace ( "M", "", ini_get ( 'memory_limit' ) ) );
	}
	
	/**
	 * Returns the values that are valid as a parameter for method setMemoryLimit.
	 *
	 * @return array
	 */
	protected function getAllowedMemoryLimitValues() {
		return array (
				self::MEMORY_LIMIT_MEDIUM,
				self::MEMORY_LIMIT_HIGH,
				self::MEMORY_LIMIT_VERY_HIGH 
		);
	}
}

