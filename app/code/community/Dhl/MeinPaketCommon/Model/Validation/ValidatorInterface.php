<?php

/**
 * Common interface for all validator classes.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation
 * @version		$Id$
 */
interface Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	
	/**
	 * Validates the given value.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value);
}
