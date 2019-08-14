<?php

/**
 * Validates an interger value to be greater than 0.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation_Validator
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Validation_Validator_IntGreaterZero implements Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	/**
	 * Validates an integer value to be greater than zero.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value) {
		return (( integer ) $value > 0);
	}
}
