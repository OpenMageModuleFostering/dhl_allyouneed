<?php

/**
 *
 * @author stephan
 *        
 */
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_Confirmation extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $customerId;
	private $email;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'confirmation' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'customerId' :
					$this->customerId = $childNode->nodeValue;
					break;
				case 'email' :
					$this->email = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getCustomerId() {
		return $this->customerId;
	}
	public function getEmail() {
		return $this->email;
	}
}