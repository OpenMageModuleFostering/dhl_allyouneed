<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_NotificationResponse_Consignment extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	/**
	 *
	 * @var string
	 */
	private $consignmentId;
	
	/**
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		$this->consignmentId = $domElement->nodeValue;
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'consignmentId' :
					$this->consignmentId = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getConsignmentId() {
		return $this->consignmentId;
	}
	
	/**
	 * Return string for this object.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->consignmentId;
	}
}
