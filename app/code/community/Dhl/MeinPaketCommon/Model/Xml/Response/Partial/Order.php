<?php

/**
 */
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $orderId;
	private $orderDate;
	private $lastModificationDate;
	private $totalPrice;
	private $totalPriceCurrency;
	private $totalDeliveryCosts;
	private $totalDeliveryCostsCurrency;
	private $deliveryMethod;
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order_ContactData
	 */
	private $contactData; // ?
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address
	 */
	private $deliveryAddress;
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address
	 */
	private $billingAddress;
	/**
	 *
	 * @var array
	 */
	private $entries = array ();
	
	/**
	 * Default constructor.
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'order' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'orderId' :
					$this->orderId = $childNode->nodeValue;
					break;
				case 'orderDate' :
					$this->orderDate = $childNode->nodeValue;
					break;
				case 'lastModificationDate' :
					$this->lastModificationDate = $childNode->nodeValue;
					break;
				case 'totalPrice' :
					$this->totalPrice = $childNode->nodeValue;
					$this->totalPriceCurrency = $childNode->getAttribute ( 'currency' );
					break;
				case 'totalDeliveryCosts' :
					$this->totalDeliveryCosts = $childNode->nodeValue;
					$this->totalDeliveryCostsCurrency = $childNode->getAttribute ( 'currency' );
					break;
				case 'deliveryMethod' :
					$this->deliveryMethod = $childNode->nodeValue;
					break;
				case 'contactData' :
					$this->contactData = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order_ContactData ( $childNode );
					break;
				case 'deliveryAddress' :
					$this->deliveryAddress = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address ( $childNode );
					break;
				case 'billingAddress' :
					$this->billingAddress = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address ( $childNode );
					break;
				case 'orderEntry' :
					$this->entries [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order_Entry ( $childNode );
			}
		}
	}
	public function getOrderId() {
		return $this->orderId;
	}
	public function getOrderDate() {
		return $this->orderDate;
	}
	public function getLastModificationDate() {
		return $this->lastModificationDate;
	}
	public function getTotalPrice() {
		return $this->totalPrice;
	}
	public function getTotalPriceCurrency() {
		return $this->totalPriceCurrency;
	}
	public function getTotalDeliveryCosts() {
		return $this->totalDeliveryCosts;
	}
	public function getTotalDeliveryCostsCurrency() {
		return $this->totalDeliveryCostsCurrency;
	}
	public function getDeliveryMethod() {
		return $this->deliveryMethod;
	}
	public function getContactData() {
		return $this->contactData;
	}
	public function getDeliveryAddress() {
		return $this->deliveryAddress;
	}
	public function getBillingAddress() {
		return $this->billingAddress;
	}
	public function getEntries() {
		return $this->entries;
	}
}