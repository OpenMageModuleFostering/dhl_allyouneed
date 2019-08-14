<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	/**
	 *
	 * @var $orders array
	 */
	private $orders = array ();
	
	/**
	 *
	 * @var $shoppingCartStatusResponses array
	 */
	private $shoppingCartStatusResponses = array ();
	
	/**
	 * Constructor
	 *
	 * @param unknown $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'queryResponse' );
		
		foreach ( $domElement->childNodes as $queryResponseEntries ) {
			switch ($queryResponseEntries->localName) {
				case 'order' :
					$this->orders [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order ( $queryResponseEntries );
					break;
				case 'shoppingCartStatusResponse' :
					$this->parseShoppingCartStatusResponse ( $queryResponseEntries );
					break;
			}
		}
	}
	
	/**
	 * Parse cart status.
	 *
	 * <cartStatus>
	 *
	 * @param DOMElement $domElement        	
	 */
	protected function parseShoppingCartStatusResponse(DOMElement $domElement) {
		assert ( $domElement->localName == 'shoppingCartStatusResponse' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'cartStatus' :
					$this->parseShoppingCartStatusResponseCartStatus ( $childNode );
					break;
			}
		}
	}
	
	/**
	 * Parse cart status elements.
	 *
	 * <externalId>0001</externalId>
	 * <shoppingCartStatusValue>Pending</shoppingCartStatusValue>
	 *
	 * @param DOMElement $domElement        	
	 */
	protected function parseShoppingCartStatusResponseCartStatus(DOMElement $domElement) {
		assert ( $domElement->localName == 'cartStatus' );
		
		$cartId = null;
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'externalId' :
					$cartId = $childNode->nodeValue;
					break;
				case 'shoppingCartStatusValue' :
					if ($cartId != null) {
						$this->shoppingCartStatusResponses [$cartId] = $childNode->nodeValue;
						$cartId = null;
					}
					break;
			}
		}
	}
	
	/**
	 *
	 * @return $orders
	 */
	public function getOrders() {
		return $this->orders;
	}
	
	/**
	 *
	 * @return $shoppingCartStatusResponses
	 */
	public function getShoppingCartStatusResponses() {
		return $this->shoppingCartStatusResponses;
	}
}
