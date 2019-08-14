<?php

/**
 * Validator chain class which validates a value using a set of validators.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Validation
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Validation_ValidatorChain implements Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface {
	/**
	 *
	 * @var array
	 */
	protected $validators = array ();
	
	/**
	 * Returns the chained result of all validators.
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	public function isValid($value) {
		if (sizeof ( $this->validators ) > 0) {
			foreach ( $this->validators as $validator ) {
				if (! $validator->isValid ( $value )) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Adds a validator to the end of the chain.
	 *
	 * @param Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface $validator        	
	 * @return Dhl_MeinPaketCommon_Model_Validation_ValidatorChain
	 */
	public function addValidator(Dhl_MeinPaketCommon_Model_Validation_ValidatorInterface $validator) {
		$this->validators [] = $validator;
		return $this;
	}
}

