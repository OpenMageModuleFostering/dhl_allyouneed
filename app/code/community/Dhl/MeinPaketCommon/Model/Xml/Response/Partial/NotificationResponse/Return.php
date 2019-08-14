<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_NotificationResponse_Return extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	/**
	 *
	 * @var string
	 */
	private $returnId;
	
	/**
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'returnId' :
					$this->returnId = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getReturnId() {
		return $this->returnId;
	}
	
	/**
	 * Return string for this object.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->returnId;
	}
}
