<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Category extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $code;
	private $name;
	private $shortDescription;
	private $parent;
	private $deprecated;
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'category' );
		
		$this->code = $domElement->getAttribute ( "code" );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'name' :
					$this->name = $childNode->nodeValue;
					break;
				case 'shortDescription' :
					$this->shortDescription = $childNode->nodeValue;
					break;
				case 'parent' :
					 $this->parent = $childNode->getAttribute ( "code" );
					break;
				case 'deprecated' :
					$this->deprecated = $childNode->nodeValue == "true";
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
	public function getShortDescription() {
		return $this->shortDescription;
	}
	public function getParent() {
		return $this->parent;
	}
	public function getDeprecated() {
		return $this->deprecated;
	}
}