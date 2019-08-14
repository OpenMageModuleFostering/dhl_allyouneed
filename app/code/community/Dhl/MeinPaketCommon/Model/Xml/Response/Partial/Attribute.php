<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Attribute extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $code;
	private $name;
	private $variantAtributeValues = array ();
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		// assert ( $domElement->localName == 'variantConfiguration' );
		
		$this->code = $domElement->getAttribute ( "code" );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'name' :
					$this->name = $childNode->nodeValue;
					break;
				case 'requiredAttribute' :
					$this->variantAtributeValues [] = array (
							'value' => $childNode->getAttribute ( 'value' ),
							'unit' => $childNode->getAttribute ( 'unit' ) 
					);
					break;
			}
		}
	}
	public function getCode() {
		return $this->code;
	}
	public function getName() {
		return $this->name;
	}
	public function getVariantAtributeValues() {
		return $this->variantAtributeValues;
	}
}