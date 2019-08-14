<?php

/**
 *
 */
class Dhl_MeinPaket_Adminhtml_Meinpaket_MatchingController extends Mage_Adminhtml_Controller_Action {
	/**
	 * (non-PHPdoc)
	 * 
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/meinpaket/matching' );
	}
	
	/**
	 * Get custom products grid and serializer block
	 */
	public function indexAction() {
		Mage::register ( 'sendName', $this->getRequest ()->getParam ( 'name', false ) );
		Mage::register ( 'sendEan', $this->getRequest ()->getParam ( 'ean', false ) );
		
		$this->_initProduct ();
		$this->loadLayout ();
		$this->renderLayout ();
	}
	public function applyAction() {
		$product = $this->_initProduct ();
		$categoryString = $this->getRequest ()->getParam ( 'category' );
		
		if (strlen ( $categoryString )) {
			$product->setMeinpaketCategory ( $categoryString );
			$product->save ();
			Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'Category assigned ' );
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( 'No category given' );
		}
		
		$this->_redirect ( 'adminhtml/catalog_product/edit', array (
				'id' => $product->getId () 
		) );
	}
	
	/**
	 * Try to load product from request.
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _initProduct() {
		$productId = ( int ) $this->getRequest ()->getParam ( 'id' );
		$product = Mage::getModel ( 'catalog/product' )->setStoreId ( $this->getRequest ()->getParam ( 'store', 0 ) );
		
		if ($productId) {
			$product->load ( $productId );
		}
		
		Mage::register ( 'product', $product );
		Mage::register ( 'current_product', $product );
		return $product;
	}
}