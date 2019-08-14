<?php

/**
 * Default helper for the Dhl_MeinPaket package.
 * 
 * @category    Dhl
 * @package     Dhl_MeinPaket
 * @subpackage  Helper
 */
class Dhl_MeinPaketCommon_Helper_Data extends Mage_Core_Helper_Abstract {
	const STORE_VIEW_CONFIG = 'meinpaket/store/view';
	private $_meinpaketStore = null;
	private $_meinpaketRootCategory = null;
	public function getExtensionVersion() {
		return ( string ) Mage::getConfig ()->getModuleConfig ( 'Dhl_MeinPaketCommon' )->version;
	}
	public function getMeinPaketStore() {
		if ($this->_meinpaketStore == null) {
			$this->_meinpaketStore = Mage::app ()->getStore ( Mage::getStoreConfig ( self::STORE_VIEW_CONFIG ) );
		}
		return $this->_meinpaketStore;
	}
	public function getMeinPaketStoreId() {
		$store = $this->getMeinPaketStore ();
		if ($store == null) {
			return null;
		} else {
			return $store->getId ();
		}
	}
	
	/**
	 * Get filtered quote from session.
	 *
	 * @return NULL|Mage_Sales_Model_Quote
	 */
	public function getQuoteFiltered() {
		$quote = Mage::getSingleton ( 'checkout/session' )->getQuote ();
		/* @var $quote Mage_Sales_Model_Quote */
		
		if ($quote === null) {
			return null;
		}
		
		foreach ( $quote->getAllVisibleItems () as $item ) {
			if (! $this->checkItem ( $item )) {
				return null;
			}
		}
		
		return $quote;
	}
	
	/**
	 * Check for usable items.
	 *
	 * @return boolean
	 */
	public function checkItem(Mage_Core_Model_Abstract $item) {
		return (! ($item instanceof Mage_Catalog_Model_Product_Configuration_Item_Interface) || count ( Mage::helper ( 'catalog/product_configuration' )->getCustomOptions ( $item ) ) <= 0) && ! $item->getIsNominal () && ! $item->getIsVirtual () && ! $item->getIsRecurring ();
	}
	
	/**
	 * Calculate price without tax
	 * 
	 * @param float $price
	 *        	with tax
	 * @param float $tax
	 *        	tax amount. If $tax > 1 $tax is assumed to be in percent.
	 */
	public function priceWithoutTax($price, $tax) {
		if ($tax > 1) {
			$tax = $tax / 100;
		}
		
		return $price / (1 + $tax);
	}
}
