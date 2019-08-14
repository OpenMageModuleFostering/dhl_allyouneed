<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_AsynchronousStatusResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $requestId;
	private $status;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'asynchronousStatusResponse' );
		
		foreach ( $domElement->childNodes as $asynchronousStatusResponseNodes ) {
			switch ($asynchronousStatusResponseNodes->localName) {
				case 'getRequestStatusResponse' :
					foreach ( $asynchronousStatusResponseNodes->childNodes as $getRequestStatusResponseNode ) {
						switch ($getRequestStatusResponseNode->localName) {
							case 'requestId' :
								$this->requestId = $getRequestStatusResponseNode->nodeValue;
								break;
							case 'status' :
								$this->status = $getRequestStatusResponseNode->nodeValue;
								break;
						}
					}
					break;
			}
		}
	}
	public function setRequestId(string $requestId) {
		$this->requestId = $requestId;
	}
	public function getRequestId() {
		return $this->requestId;
	}
	public function setStatus(string $status) {
		$this->status = $status;
	}
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * Parses the response of a sync request
	 *
	 * @return Dhl_MeinPaketCommon_Model_Xml_Response_AsynchronousStatusResponse
	 */
	public function createOrUpdateAsync() {
		if (strlen ( $this->requestId ) > 0) {
			/* @var $request Dhl_MeinPaketCommon_Model_Async */
			$request = Mage::getModel ( 'meinpaketcommon/async' )->load ( $this->requestId, 'request_id' );
			$request->setCreatedAt ( Varien_Date::now () );
			$request->setUpdatedAt ( Varien_Date::now () );
			$request->setRequestId ( $this->requestId );
			$request->setStatus ( $this->status );
			$request->save ();
		}
		
		return $this;
	}
	
	/**
	 * Process response
	 */
	public function process() {
		$this->createOrUpdateAsync ();
	}
}