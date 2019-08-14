<?php

/**
 *
 */
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_ProductData extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $entryId;
	private $type;
	private $marketplaceCategory;
	private $name;
	private $shortDescription;
	private $longDescription;
	private $manufacturerName;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'productData' );
		
		$this->entryId = $domElement->getAttribute ( 'entryId' );
		$this->type = $domElement->getAttribute ( 'type' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'marketplaceCategory' :
					$this->marketplaceCategory = $childNode->getAttribute ( 'code' );
					break;
				case 'name' :
					$this->name = $childNode->nodeValue;
					break;
				case 'shortDescription' :
					$this->shortDescription = $childNode->nodeValue;
					break;
				case 'longDescription' :
					$this->longDescription = $childNode->nodeValue;
					break;
				case 'manufacturerName' :
					$this->manufacturerName = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getEntityId() {
		return $this->entryId;
	}
	public function getMarketplaceCategory() {
		return $this->marketplaceCategory;
	}
	public function getName() {
		return $this->name;
	}
	public function getShortDescription() {
		return $this->shortDescription;
	}
	public function getLongDescription() {
		return $this->longDescription;
	}
	public function getManufacturerName() {
		return $this->manufacturerName;
	}
	public function getType() {
		return $this->type;
	}
}