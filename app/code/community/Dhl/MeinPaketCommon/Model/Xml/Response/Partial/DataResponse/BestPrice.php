<?php

/**
 *
 */
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_BestPrice extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	const OwNERSHIP_OWN = "OWN";
	const OwNERSHIP_FOREIGN = "FOREIGN";
	private $productId;
	private $price;
	private $priceCurrency;
	private $deliveryCost;
	private $deliveryCostCurrency;
	private $deliveryTime;
	private $ownership;
	private $owningDealerCode;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'bestPrice' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'productId' :
					$this->productId = $childNode->nodeValue;
					break;
				case 'price' :
					$this->price = $childNode->nodeValue;
					$this->priceCurrency = $childNode->getAttribute ( 'currency' );
					break;
				case 'deliveryCost' :
					$this->deliveryCost = $childNode->nodeValue;
					$this->deliveryCostCurrency = $childNode->getAttribute ( 'currency' );
					break;
				case 'deliveryTime' :
					$this->deliveryTime = $childNode->nodeValue;
					break;
				case 'activeOffers' :
					$this->activeOffers = $childNode->nodeValue;
					break;
				case 'ownership' :
					switch ($childNode->nodeValue) {
						case self::OwNERSHIP_OWN :
							$this->ownership = self::OwNERSHIP_OWN;
							break;
						case self::OwNERSHIP_FOREIGN :
							$this->ownership = self::OwNERSHIP_FOREIGN;
							break;
					}
					break;
				case 'owningDealerCode' :
					$this->owningDealerCode = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getActiveOffers() {
		return $this->activeOffers;
	}
	public function getProductId() {
		return $this->productId;
	}
	public function getPrice() {
		return $this->price;
	}
	public function getPriceCurrency() {
		return $this->priceCurrency;
	}
	public function getDeliveryCost() {
		return $this->deliveryCost;
	}
	public function getDeliveryCostCurrency() {
		return $this->deliveryCostCurrency;
	}
	public function getDeliveryTime() {
		return $this->deliveryTime;
	}
	public function getOwnership() {
		return $this->ownership;
	}
	public function getOwningDealerCode() {
		return $this->owningDealerCode;
	}
}