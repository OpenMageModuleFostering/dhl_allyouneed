<?php

/**
 * Basic exception class for invalid data.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Exception
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Exception_InvalidDataException extends Varien_Exception {
	/**
	 *
	 * @var string
	 */
	protected $errorType;
	
	/**
	 *
	 * @var string
	 */
	protected $fieldName;
	
	/**
	 *
	 * @var integer
	 */
	protected $entityId;
	
	/**
	 * Constructor.
	 *
	 * @param integer $entityId
	 *        	of the model entity on which the error occured.
	 * @param string $fieldName
	 *        	of the attribute which is invalid.
	 * @param string $errorType
	 *        	type defined in Dhl_MeinPaketCommon_Model_Validation_ValidationInterface.
	 * @return void
	 */
	public function __construct($entityId, $fieldName, $errorType = Dhl_MeinPaketCommon_Model_Validation_ValidationInterface::ERROR_FIELD_IS_INVALID) {
		parent::__construct ( 'Invalid data field "' . $fieldName . '" for entity (id=' . $entityId . '). Error type is "' . $errorType . '".' );
		
		$this->entityId = $entityId;
		$this->fieldName = $fieldName;
		$this->errorType = $errorType;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getFieldName() {
		return $this->fieldName;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getEntityId() {
		return $this->entityId;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getErrorType() {
		return $this->errorType;
	}
}
