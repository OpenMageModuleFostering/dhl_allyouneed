<?php

/**
 * Basic exception class for invalid data.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Exception
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Exception_MissingDataException extends Dhl_MeinPaketCommon_Model_Exception_InvalidDataException {
	/**
	 * Constructor.
	 *
	 * @param integer $entityId
	 *        	of the model entity on which the error occured.
	 * @param string $fieldName
	 *        	of the attribute that is missing.
	 * @return void
	 */
	public function __construct($entityId, $fieldName) {
		parent::__construct ( $entityId, $fieldName, Dhl_MeinPaketCommon_Model_Validation_ValidationInterface::ERROR_REQUIRED_FIELD_IS_EMPTY );
	}
}

