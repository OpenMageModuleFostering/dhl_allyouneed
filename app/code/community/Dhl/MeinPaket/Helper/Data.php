<?php

/**
 * Default helper for the Dhl_MeinPaket package.
 * 
 * @category    Dhl
 * @package     Dhl_MeinPaket
 * @subpackage  Helper
 */
class Dhl_MeinPaket_Helper_Data extends Mage_Core_Helper_Abstract {
	const STORE_VIEW_CONFIG = 'meinpaket/store/view';
	private $_meinpaketStore = null;
	private $_meinpaketRootCategory = null;
	public function getExtensionVersion() {
		return ( string ) Mage::getConfig ()->getModuleConfig ( 'Dhl_MeinPaket' )->version;
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
	public function getMeinPaketRootCategoryId() {
		return $this->getMeinPaketStore ()->getRootCategoryId ();
	}
	public function getMeinPaketRootCategory() {
		if ($this->_meinpaketRootCategory == null) {
			$this->_meinpaketRootCategory = Mage::getModel ( 'catalog/category' )->setStoreId ( $this->getMeinPaketStoreId () )->load ( $this->getMeinPaketRootCategoryId () );
		}
		return $this->_meinpaketRootCategory;
	}
	
	/**
	 * Is this module active?
	 *
	 * @return bool
	 */
	public function isActive() {
		return ( bool ) Mage::getStoreConfigFlag ( 'meinpaket/credentials/active' );
	}
}
