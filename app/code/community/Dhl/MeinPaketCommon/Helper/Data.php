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
	 * Creates/extends JavaScript language object by the given labels.
	 * The given labels array must have the structure array('key1'=>'label1','key2'=>'label2',...).
	 * Key is the key under which the label will be accessible in frontend JavaScript.
	 * Label is the untranslated label.
	 * Example: Element is 'foo'=>'Bar'. So use MeinPaket.locale.foo .
	 *
	 * @param array $labels        	
	 * @return string
	 */
	public function createLocaleJS(array $labels) {
		$js = 'if(typeof MeinPaket === "undefined"){var MeinPaket={};}';
		$js .= 'if(typeof MeinPaket.locale === "undefined"){MeinPaket.locale={};}';
		$js .= 'Object.extend(MeinPaket.locale,{';
		
		$firstItem = true;
		if (sizeof ( $labels ) > 0) {
			foreach ( $labels as $key => $label ) {
				$js .= $this->_addJSLocaleLabel ( $key, $this->__ ( $label ), $firstItem );
				if ($firstItem) {
					$firstItem = false;
				}
			}
		}
		
		$js .= '});';
		
		return $js;
	}
	
	/**
	 * Creates a json property.
	 *
	 * @param string $key        	
	 * @param string $label        	
	 * @param boolean $isFirst
	 *        	Tells if the label is the first in the list.
	 * @return string
	 */
	protected function _addJSLocaleLabel($key, $label, $isFirst = false) {
		$labelProperty = '';
		
		if (! $isFirst) {
			$labelProperty .= ',';
		}
		
		$labelProperty .= $key . ':"' . $label . '"';
		
		return $labelProperty;
	}
	public function getExtensionVersion() {
		return ( string ) Mage::getConfig ()->getModuleConfig ( 'Dhl_MeinPaketCommon' )->version;
	}
	const STORE_VIEW_CONFIG = 'meinpaket/store/view';
	private $_meinpaketStore = null;
	private $_meinpaketRootCategory = null;
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
	 * Get filtered quote from session.
	 *
	 * @return boolean
	 */
	public function checkItem(Mage_Sales_Model_Order_Item $item) {
		$options = Mage::helper ( 'catalog/product_configuration' )->getCustomOptions ( $item );
		// $options = $item->getProduct ()->getTypeInstance ( true )->getOrderOptions ( $item->getProduct () );
		
		if (count ( $options ) || $item->getIsNominal () || $item->getIsVirtual () || $item->getIsRecurring ()) {
			return false;
		}
		// Mage::log ( Zend_Debug::dump ( $options ) );
		
		if ($item->getParentItemId ()) {
			true;
		}
		
		if ($item->getProduct ()->isVirtual () || $item->getProduct ()->isRecurring ()) {
			return false;
		}
		
		return true;
	}
}
