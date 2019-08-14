<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_CategoryId extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $categoryCode;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		$this->categoryCode = $domElement->getAttribute ( 'code' );
	}
	public function getCategoryCode() {
		return $this->productCode;
	}
	
	/**
	 * Return string for this object.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->categoryCode;
	}
}