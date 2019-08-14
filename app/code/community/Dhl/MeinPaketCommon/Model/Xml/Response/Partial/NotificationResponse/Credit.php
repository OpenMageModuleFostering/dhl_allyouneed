<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_NotificationResponse_Credit extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	/**
	 *
	 * @var string
	 */
	private $creditId;
	
	/**
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'creditId' :
					$this->creditId = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getCreditId() {
		return $this->creditId;
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
