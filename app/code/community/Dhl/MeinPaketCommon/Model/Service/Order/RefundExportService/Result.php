<?php

/**
 * Result class which encapsulates information concerning the refund export process.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_RefundExport
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Order_RefundExportService_Result extends Dhl_MeinPaketCommon_Model_Service_Result_Abstract {
	/**
	 *
	 * @var string
	 */
	protected $originalRefundId = '-1';
	
	/**
	 *
	 * @var string
	 */
	protected $returnedRefundId = '-2';
	
	/**
	 *
	 * @param string $refundId        	
	 * @return void
	 */
	public function setOriginalRefundId($refundId) {
		if (! is_string ( $refundId )) {
			$refundId = ( string ) $refundId;
		}
		$this->originalRefundId = $refundId;
	}
	
	/**
	 *
	 * @param string $refundId        	
	 * @return void
	 */
	public function setReturnedRefundId($refundId) {
		if (! is_string ( $refundId )) {
			$refundId = ( string ) $refundId;
		}
		$this->returnedRefundId = $refundId;
	}
	
	/**
	 * Tells if the refund has been accepted by Allyouneed.
	 *
	 * @return boolean
	 */
	public function hasBeenAccepted() {
		return ($this->originalRefundId === $this->returnedRefundId);
	}
}

