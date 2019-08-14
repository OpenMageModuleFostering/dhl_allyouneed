<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $productId;
	private $meinPaketId;
	private $ean;
	private $manufacturerName;
	private $manufacturerPN;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'productId' :
					$this->productId = $childNode->nodeValue;
					break;
				case 'meinPaketId' :
					$this->meinPaketId = $childNode->nodeValue;
					break;
				case 'ean' :
					$this->ean = $childNode->nodeValue;
					break;
				case 'manufacturerName' :
					$this->manufacturerName = $childNode->nodeValue;
					break;
				case 'manufacturerPN' :
					$this->manufacturerPN = $childNode->nodeValue;
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
	public function getManufacturerName() {
		return $this->manufacturerName;
	}
	public function getManufacturerPN() {
		return $this->manufacturerPN;
	}
	
	/**
	 * Return string for this object.
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->productId . ' ' . $this->meinPaketId . ' ' . $this->ean . ' ' . $this->manufacturerName . ' ' . $this->manufacturerPN;
	}
}