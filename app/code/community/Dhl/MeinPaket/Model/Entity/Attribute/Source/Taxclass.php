<?php

/**
 * Tax class model.
 * Reads tax classes from database and provides them as config array.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Source_Taxclass extends Mage_Eav_Model_Entity_Attribute_Abstract {
	/**
	 * returns tax classes from database
	 *
	 * @return bool
	 */
	protected function getTaxClasses() {
		$taxClasses = array ();
		
		$db = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
		$table_prefix = Mage::getConfig ()->getTablePrefix ();
		
		$result = $db->query ( "
      SELECT class_id, class_name
      FROM {$table_prefix}tax_class
      WHERE class_type = 'PRODUCT'
      ORDER BY class_id
    " );
		
		if ($result) {
			while ( $row = $result->fetch ( PDO::FETCH_ASSOC ) ) {
				$taxClasses [] = array (
						'value' => $row ['class_id'],
						'label' => $row ['class_name'] 
				);
			}
		}
		
		return $taxClasses;
	}
	
	/**
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return $this->getTaxClasses ();
	}
}

