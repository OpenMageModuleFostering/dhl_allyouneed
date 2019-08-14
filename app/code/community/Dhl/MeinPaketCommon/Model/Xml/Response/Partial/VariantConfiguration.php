<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_VariantConfiguration extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $code;
	private $indexAsGroup;
	private $variantSelectionRule;
	private $description;
	private $requiredAttributes = array ();
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'variantConfiguration' );
		
		$this->code = $domElement->getAttribute ( "code" );
		$this->indexAsGroup = $domElement->getAttribute ( "indexAsGroup" ) == 'true';
		$this->variantSelectionRule = $domElement->getAttribute ( "variantSelectionRule" );
		
		$attributes = array ();
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'description' :
					$this->description = $childNode->nodeValue;
					break;
				case 'requiredAttribute' :
					$this->requiredAttributes [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Attribute ( $childNode );
					break;
			}
		}
	}
	public function getCode() {
		return $this->code;
	}
	public function getIndexAsGroup() {
		return $this->indexAsGroup;
	}
	public function getVariantSelectionRule() {
		return $this->variantSelectionRule;
	}
	public function getDescription() {
		return $this->description;
	}
	public function getRequiredAttributes() {
		return $this->requiredAttributes;
	}
	
	/**
	 * Parses the result from the given XML.
	 *
	 * @param SimpleXMLElement $dom
	 *        	The raw XML result returned by the webservice.
	 */
	public function parseVariantConfigurationsDownloadResponse(SimpleXMLElement $dom) {
		$variantConfigurations = array ();
		
		if (isset ( $dom->variantConfiguration )) {
			/* @var $variantConfiguration SimpleXMLElement */
			foreach ( $dom->variantConfiguration as $variantConfiguration ) {
				$variantAttributes = ( array ) ($variantConfiguration->attributes ());
				$variantAttributes = array_pop ( $variantAttributes );
				$variantCode = ( string ) $variantAttributes ['code'];
				$variantConfigurations [$variantCode] = array (
						'indexAsGroup' => ( boolean ) $variantAttributes ['indexAsGroup'],
						'variantSelectionRule' => ( string ) $variantAttributes ['variantSelectionRule'],
						'description' => ( string ) $variantConfiguration->description 
				);
				if (isset ( $variantConfiguration->requiredAttribute )) {
					foreach ( $variantConfiguration->requiredAttribute as $requiredAttribute ) {
						$requiredAttributeAttributes = ( array ) ($requiredAttribute->attributes ());
						$requiredAttributeAttributes = array_pop ( $requiredAttributeAttributes );
						$attributeCode = ( string ) $requiredAttributeAttributes ['code'];
						$variantConfigurations [$variantCode] ['requiredAttributes'] [$attributeCode] = array (
								'name' => ( string ) $requiredAttribute->name,
								'values' => array () 
						);
						if (isset ( $requiredAttribute->variantAtributeValue )) {
							foreach ( $requiredAttribute->variantAtributeValue as $variantAttributeValue ) {
								$variantAttributeValueAttributes = ( array ) ($variantAttributeValue->attributes ());
								$variantAttributeValueAttributes = array_pop ( $variantAttributeValueAttributes );
								$variantConfigurations [$variantCode] ['requiredAttributes'] [$attributeCode] ['values'] [] = ( string ) $variantAttributeValueAttributes ['value'];
							}
						}
					}
				}
			}
		}
		
		return $variantConfigurations;
	}
}