<?php

/**
 */
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order_Entry extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $productId;
	private $meinPaketId;
	private $ean;
	private $name;
	private $quantity;
	private $basePrice;
	private $basePriceCurrency;
	private $totalPrice;
	private $totalPriceCurrency;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'orderEntry' );
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'productId' :
					$this->productId = $childNode->nodeValue;
					break;
				case 'meinPaketId' :
					$this->meinPaketId = $childNode->nodeValue;
					break;
				case 'ean' :
					$this->ean = strtotime ( $childNode->nodeValue );
					break;
				case 'name' :
					$this->name = $childNode->nodeValue;
					break;
				case 'quantity' :
					$this->quantity = $childNode->nodeValue;
					break;
				case 'basePrice' :
					$this->basePrice = $childNode->nodeValue;
					$this->basePriceCurrency = $childNode->getAttribute ( 'currency' );
					break;
				case 'totalPrice' :
					$this->totalPrice = $childNode->nodeValue;
					$this->totalPriceCurrency = $childNode->getAttribute ( 'currency' );
					break;
			}
		}
	}
	public function getProductId() {
		return $this->productId;
	}
	public function getMeinPaketId() {
		return $this->meinPaketId;
	}
	public function getEan() {
		return $this->ean;
	}
	public function getName() {
		return $this->name;
	}
	public function getQuantity() {
		return $this->quantity;
	}
	public function getBasePrice() {
		return $this->basePrice;
	}
	public function getBaseCurrency() {
		return $this->basePriceCurrency;
	}
	public function getTotalPrice() {
		return $this->totalPrice;
	}
	public function getTotalPriceCurrency() {
		return $this->totalPriceCurrency;
	}
}