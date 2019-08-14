<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_SubmitCartResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $cartId;
	private $redirectURL;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'submitCartResponse' );
		
		foreach ( $domElement->childNodes as $submitCartResponseChildNode ) {
			switch ($submitCartResponseChildNode->localName) {
				case 'cartId' :
					$this->cartId = $submitCartResponseChildNode->nodeValue;
					break;
				case 'redirectURL' :
					$this->redirectURL = $submitCartResponseChildNode->nodeValue;
					break;
			}
		}
	}
	public function getCartId() {
		return $this->cartId;
	}
	public function getRedirectURL() {
		return $this->redirectURL;
	}
}
