<?php

/**
 * Default helper for the Dhl_MeinPaket package.
 * 
 * @category    Dhl
 * @package     Dhl_MeinPaket
 * @subpackage  Helper
 */
class Dhl_MeinPaketCommon_Helper_Address extends Mage_Core_Helper_Abstract {
	const STREET_HOUSENUMBER = '/^(?P<street>\D+)\s*(?P<houseNumber>\d.*)$/';
	const HOUSENUMBER_STREET = '/^(?P<houseNumber>\d\S*)\s*(?P<street>\D.*)$/';
	
	/**
	 * Parse address and return array with keys "street" and "houseNumber".
	 *
	 * @return array
	 */
	public function parseAddress(Mage_Sales_Model_Order_Address $address) {
		return $this->parseStreetHouseNumber ( $address->getStreet1 () );
	}
	
	/**
	 * Parse address and return array with keys "street" and "houseNumber".
	 *
	 * @return array
	 */
	public function parseStreetHouseNumber($address) {
		if ($address == null) {
			return null;
		}
		
		$matches = null;
		
		if (preg_match ( self::STREET_HOUSENUMBER, $address, $matches )) {
			return $matches;
		}
		
		if (preg_match ( self::HOUSENUMBER_STREET, $address, $matches )) {
			return $matches;
		}
		
		return array (
				0 => $address,
				1 => $address,
				2 => "",
				"street" => $address,
				"houseNumber" => "" 
		);
	}
}
