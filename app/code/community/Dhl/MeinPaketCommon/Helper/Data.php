<?php

/**
 * Default helper for the Dhl_MeinPaket package.
 * 
 * @category    Dhl
 * @package     Dhl_MeinPaket
 * @subpackage  Helper
 */
class Dhl_MeinPaketCommon_Helper_Data extends Mage_Core_Helper_Abstract {
	
	/**
	 * Configuration for store view.
	 *
	 * @var unknown
	 */
	const STORE_VIEW_CONFIG = 'meinpaket/store/view';
	
	/**
	 * Cache for store
	 *
	 * @var Mage_Core_Model_Store
	 */
	private $_meinpaketStore = null;
	
	/**
	 * Cache for root category
	 *
	 * @var unknown
	 */
	private $_meinpaketRootCategory = null;
	
	/**
	 * Get extension version.
	 */
	public function getExtensionVersion() {
		return ( string ) Mage::getConfig ()->getModuleConfig ( 'Dhl_MeinPaketCommon' )->version;
	}
	
	/**
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getMeinPaketStore() {
		if ($this->_meinpaketStore == null) {
			$this->_meinpaketStore = Mage::app ()->getStore ( Mage::getStoreConfig ( self::STORE_VIEW_CONFIG ) );
		}
		return $this->_meinpaketStore;
	}
	
	/**
	 * Get StoreId
	 */
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
	
	/**
	 * Filter allowed html tags.
	 *
	 * @param unknown $input        	
	 */
	public function filterHTMLTags($input) {
		$filter = new Zend_Filter_StripTags ( array (
				'allowTags' => array (
						'b',
						'br',
						'p',
						'ul',
						'ol',
						'li',
						'hr' 
				) 
		) );
		return $filter->filter ( $input );
	}
}
