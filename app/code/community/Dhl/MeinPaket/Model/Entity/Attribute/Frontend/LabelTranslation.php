<?php

/**
 * Custom label translation for frontend.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Frontend
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Frontend_LabelTranslation extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract {
	
	/**
	 * Returns the label of the field.
	 *
	 * @return string
	 */
	public function getLabel() {
		return Mage::helper ( 'Dhl_MeinPaket' )->__ ( parent::getLabel () );
	}
}
