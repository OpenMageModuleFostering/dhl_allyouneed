<?php

/**
 * Product attributes model.
 * Reads product attributes and provides them as config array.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Source_ProductAttribute extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
	protected $attributes;
	public function __construct() {
		$this->attributes = array (
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'None' ),
						'value' => '' 
				) 
		);
		
		foreach ( Mage::getResourceModel ( 'catalog/product_attribute_collection' ) as $attribute ) {
			if (strlen ( $attribute->getFrontendLabel () ) > 0) {
				$this->attributes [] = array (
						'label' => $attribute->getFrontendLabel (),
						'value' => $attribute->getAttributecode () 
				);
			}
		}
	}
	public function toOptionArray($addEmpty = true) {
		return $this->attributes;
	}
	public function toSelectArray() {
		$result = array ();
		
		foreach ( $this->attributes as $attribute ) {
			$result [$attribute ['value']] = $attribute ['label'];
		}
		
		return $result;
	}
	
	/**
	 * Retrieve All options
	 *
	 * @return array
	 */
	public function getAllOptions() {
		if (is_null ( $this->_options )) {
			$this->_options = $this->attributes;
		}
		
		return $this->_options;
	}
}
