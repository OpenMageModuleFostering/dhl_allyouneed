<?php

/**
 * Validator for European Article Numbers.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation_Validator
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Validation_Validator_Ean implements Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	/**
	 * Validates an EAN.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value) {
		$length = 0;
		$checkSum = 0;
		$char = '';
		$validLengths = array (
				13 
		); // @todo check why array(13, 8) isn't possible
		
		if (! is_string ( $value )) {
			$value = ( string ) $value;
		}
		
		$length = strlen ( $value );
		
		// check for length
		if (! in_array ( $length, $validLengths )) {
			return false;
		}
		
		// perform checksum check
		for($i = 0; $i < $length; $i ++) {
			
			$char = substr ( $value, $i, 1 );
			
			if (! preg_match ( '~\d~', $char )) {
				return false;
			}
			
			if ($i % 2 == 0) {
				$checkSum += (( integer ) $char) * 1;
			} else {
				$checkSum += (( integer ) $char) * 3;
			}
		}
		if ($checkSum % 10 != 0) {
			return false;
		}
		
		return true;
	}
}
