<?php

/**
 * Encapsulates information about the results of a order cancellation process.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_OrderCancellation
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Order_CancellationService_Result extends Dhl_MeinPaketCommon_Model_Service_Result_Abstract {
	/**
	 *
	 * @var string
	 */
	const ERROR_XML_REQUEST_BUILDING_FAILED = 'xmlRequestBuildingFailed';
	
	/**
	 *
	 * @var string
	 */
	const ERROR_SENDING_REQUST_TO_MEIN_PAKET_SERVER_FAILED = 'sendingRequestFailed';
	
	/**
	 *
	 * @var string
	 */
	const ERROR_INVALID_XML_RESPONSE_RECEIVED = 'invalidXmlResponse';
	
	/**
	 *
	 * @var string
	 */
	protected $errorCode = '';
	
	/**
	 *
	 * @var array
	 */
	protected $consignmentIds = array ();
	
	/**
	 * Sets the error that occured.
	 *
	 *
	 * @param string $errorCode
	 *        	has to be the errorCode
	 *        	that was returned from the Allyouneed
	 *        	webservice.
	 * @return void
	 */
	public function setError($errorCode) {
		$this->errorCode = $errorCode;
	}
	
	/**
	 * Tells if an error occured during the process.
	 *
	 * @return boolean
	 */
	public function hasError() {
		return (strlen ( $this->errorCode ) > 0);
	}
	
	/**
	 * Returns the errorCode of the error that has occured.
	 * If there was no error, the method will return an empty string.
	 *
	 * @return string
	 */
	public function getError() {
		return $this->errorCode;
	}
	
	/**
	 * Adds a consignment id for a cancelled product.
	 *
	 * @param string $consignmentId        	
	 * @return void
	 */
	public function addConsignmentId($consignmentId) {
		$this->consignmentIds [] = $consignmentId;
	}
	
	/**
	 * Returns all consignment ids that have been returned by Allyouneed.
	 *
	 * @return array
	 */
	public function getConsignmentIds() {
		return $this->consignmentIds;
	}
}
