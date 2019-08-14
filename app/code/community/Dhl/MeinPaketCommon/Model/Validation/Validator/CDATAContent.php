<?php

/**
 * Validator for CDATA block content.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation_Validator
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Validation_Validator_CDATAContent implements Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	/**
	 * Validates a string to be valid content of a CDATA block.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value) {
		return (preg_match ( '~' . preg_quote ( ']]>' ) . '~', $value ) === 0);
	}
}
