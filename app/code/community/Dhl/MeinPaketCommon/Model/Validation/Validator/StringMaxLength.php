<?php

/**
 * Validator for maximum length of strings.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation_Validator
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Validation_Validator_StringMaxLength implements Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	/**
	 *
	 * @var integer
	 */
	const LENGTH_INFINITE = - 1;
	
	/**
	 *
	 * @var integer
	 */
	protected $maxLength = self::LENGTH_INFINITE;
	
	/**
	 * Validates a string not to be longer than the set length.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value) {
		$value = @(( string ) $value);
		$valid = true;
		
		if (! is_string ( $value )) {
			return false;
		}
		
		if ($this->maxLength !== self::LENGTH_INFINITE) {
			$valid = (strlen ( $value ) <= $this->maxLength);
		}
		
		return $valid;
	}
	
	/**
	 * Sets the maximum allowed length.
	 *
	 * @param integer $length        	
	 * @return Dhl_MeinPaketCommon_Model_Validation_Validator_StringMaxLength
	 */
	public function setMaxLength($length) {
		if (is_integer ( $length ) && $length >= 0) {
			$this->maxLength = $length;
		}
		return $this;
	}
}
