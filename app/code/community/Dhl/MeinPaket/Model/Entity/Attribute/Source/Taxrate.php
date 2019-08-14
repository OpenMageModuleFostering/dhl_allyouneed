<?php

/**
 * Tax rate model.
 * Reads tax rates from database and provides them as config array.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Source_Taxrate extends Mage_Eav_Model_Entity_Attribute_Abstract {
	/**
	 * returns tax rates from database
	 *
	 * @return bool
	 */
	protected function getTaxRates() {
		$taxRates = array ();
		
		$db = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
		$table_prefix = Mage::getConfig ()->getTablePrefix ();
		
		$result = $db->query ( "
      SELECT tax_calculation_rate_id, rate
      FROM {$table_prefix}tax_calculation_rate
      ORDER BY tax_calculation_rate_id
    " );
		
		if ($result) {
			while ( $row = $result->fetch ( PDO::FETCH_ASSOC ) ) {
				$taxRates [] = array (
						'value' => $row ['tax_calculation_rate_id'],
						'label' => $row ['rate'] 
				);
			}
		}
		
		return $taxRates;
	}
	
	/**
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return $this->getTaxRates ();
	}
}