<?php

/**
 * Observer for all events the DHL MeinPaket extension has to catch.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Observer {
	/**
	 * Important attributes for stock.
	 *
	 * @var array
	 */
	private $STOCK_ATTRIBUTES = array (
			'qty',
			'manage_stock',
			'is_in_stock' 
	);
	
	/**
	 * Triggered when product is duplicated.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	public function productDuplicate(Varien_Event_Observer $observer) {
		$newProduct = $observer->getEvent ()->getNewProduct ();
		$newProduct->setData ( 'meinpaket_id', '' );
		$newProduct->setData ( 'meinpaket_export', 0 );
		return $this;
	}
	
	/**
	 * Is triggered after a product has been deleted.
	 * If the product has been exported to MeinPake.de once before, the
	 * product's status will be set to disabled, and then be saved to
	 * trigger the catalog_product_save_after event again.
	 *
	 * @see Dhl_MeinPaket_Model_Observer::catalogProductSaveAfter()
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	public function catalogProductDeleteBefore(Varien_Event_Observer $observer) {
		try {
			/**
			 *
			 * @var $product Mage_Catalog_Model_Product
			 */
			$product = $observer->getEvent ()->getProduct ();
			
			if ($product == null) {
				return $this;
			}
			
			$product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::helper ( 'meinpaketcommon/data' )->getMeinPaketStoreId () )->load ( $product->getId () );
			if ($product->hasData ( 'was_exported_for_dhl_mein_paket' ) && (( boolean ) $product->getData ( 'product_was_exported_for_dhl' )) === true) {
				$product->setStatus ( Mage_Catalog_Model_Product_Status::STATUS_DISABLED )->save ();
			}
			
			$catalogService = Mage::getSingleton ( 'meinpaket/service_product_export' );
			$catalogService->deleteProduct ( $product );
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		
		return $this;
	}
	
	/**
	 * Triggered before product is saved.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	public function catalogProductSaveBefore(Varien_Event_Observer $observer) {
		/**
		 *
		 * @var $product Mage_Catalog_Model_Product
		 */
		$product = $observer->getEvent ()->getProduct ();
		
		if ($product == null) {
			return $this;
		}
		
		if (! $product->getId ()) {
			/*
			 * Delete meinpaket data for new and as such not synchronized objects. It's assumed that meinpaket_export is either set by the user or productDuplicate in this class. This is for example needed for quick created variants.
			 */
			$product->setData ( 'meinpaket_id', '' );
		}
		
		if ($product->hasDataChanges ()) {
			try {
				$changes = array ();
				foreach ( $product->getData () as $attribute => $newValue ) {
					// Find changed products
					$oldValue = $product->getOrigData ( $attribute );
					
					if (is_array ( $newValue ) && is_array ( $oldValue )) {
						// Ignored
					} else if ($newValue != $oldValue) {
						$changes [] = $attribute;
					}
				}
				
				if (count ( $changes )) {
					// Flag schedule in backlog.
					$product->setData ( 'meinpaket_backlog_changes', $changes );
				}
			} catch ( Exception $e ) {
				Mage::logException ( $e );
			}
		}
		return $this;
	}
	
	/**
	 * Triggered after product is saved.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	public function catalogProductSaveAfter(Varien_Event_Observer $observer) {
		try {
			$product = $observer->getEvent ()->getProduct ();
			
			if ($product != null && is_array ( $product->getData ( 'meinpaket_backlog_changes' ) )) {
				// Schedule the product for later
				$changes = $product->getData ( 'meinpaket_backlog_changes' );
				if (count ( $changes ) && $product->getId ()) {
					$typeInstance = $product->getTypeInstance ();
					if ($typeInstance instanceof Mage_Catalog_Model_Product_Type_Configurable) {
						Mage::helper ( 'meinpaket/backlog' )->createChildrenBacklog ( $product->getId () );
					} else {
						Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $product->getId (), implode ( ',', $changes ) );
					}
				}
			}
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		
		return $this;
	}
	
	/**
	 * Triggered before product massaction.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	public function catalogProductAttributeUpdateBefore(Varien_Event_Observer $observer) {
		$attributesData = $observer->getEvent ()->getAttributesData ();
		$productIds = $observer->getEvent ()->getProductIds ();
		
		if ($attributesData == null || $productIds == null) {
			return $this;
		}
		
		$changes = implode ( ',', array_keys ( $attributesData ) );
		
		foreach ( $productIds as $id ) {
			$count = Mage::helper ( 'meinpaket/backlog' )->createChildrenBacklog ( $id );
			if ($count <= 0) {
				Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $id, $changes );
			}
		}
		return $this;
	}
	
	/**
	 * Trigger when stock changes.
	 */
	public function catalogInventoryStockItemSaveCommitAfter(Varien_Event_Observer $observer) {
		try {
			$stockItem = $observer->getEvent ()->getItem ();
			/* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
			
			if ($stockItem != null) {
				$changes = array ();
				foreach ( $this->STOCK_ATTRIBUTES as $attribute ) {
					if ($stockItem->getData ( $attribute ) != $stockItem->getOrigData ( $attribute )) {
						$changes [] = $attribute;
					}
				}
				
				if (! empty ( $changes )) {
					$product = $stockItem->getProduct ();
					
					if ($product != null && $product->getData ( 'meinpaket_id' )) {
						$typeInstance = $product->getTypeInstance ();
						if ($typeInstance instanceof Mage_Catalog_Model_Product_Type_Configurable) {
							Mage::helper ( 'meinpaket/backlog' )->createChildrenBacklog ( $product->getId () );
						} else {
							Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $product->getId (), implode ( ',', $changes ) );
						}
					}
				}
			}
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		return $this;
	}
	
	/**
	 * Trigger when stock changes.
	 */
	public function salesModelServiceQuoteSubmitBefore_Failure(Varien_Event_Observer $observer) {
		try {
			/* @var $quote Mage_Sales_Model_Quote */
			$quote = $observer->getEvent ()->getQuote ();
			if ($quote != null) {
				foreach ( $quote->getAllItems () as $item ) {
					Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $item->getProductId () );
				}
			}
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		return $this;
	}
	
	/**
	 * Trigger when stock changes.
	 */
	public function salesOrderCreditmemoSaveAfter(Varien_Event_Observer $observer) {
		try {
			/* @var $quote Mage_Sales_Model_Order_Creditmemo */
			$creditmemo = $observer->getEvent ()->getCreditmemo ();
			if ($creditmemo != null) {
				foreach ( $creditmemo->getAllItems () as $item ) {
					Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $item->getProductId () );
				}
			}
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		return $this;
	}
	
	/**
	 * Trigger when stock changes
	 */
	public function salesOrderItemCancel(Varien_Event_Observer $observer) {
		try {
			/* @var $item Mage_Sales_Model_Order_Item */
			$item = $observer->getEvent ()->getItem ();
			if ($item != null) {
				Mage::helper ( 'meinpaket/backlog' )->createBacklog ( $item->getProductId () );
			}
		} catch ( Exception $ex ) {
			Mage::logException ( $ex );
		}
		return $this;
	}
	
	/**
	 *
	 * @param unknown $observer        	
	 */
	public function addMeinPaketAttributes($observer) {
		$fieldset = $observer->getForm ()->getElement ( 'base_fieldset' );
		$attribute = $observer->getAttribute ();
		$fieldset->addField ( 'meinpaket_attribute', 'select', array (
				'name' => 'meinpaket_attribute',
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Allyouneed Attribute' ),
				'title' => Mage::helper ( 'meinpaket' )->__ ( 'Allyouneed Attribute' ),
				'values' => Mage::getModel ( 'meinpaket/system_config_source_attributes' )->toOptionArray () 
		) );
	}
	
	/**
	 * Triggered before Layout is prepared.
	 * So no parent set yet - argh.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaket_Model_Observer
	 */
	function addProductGridMassaction(Varien_Event_Observer $observer) {
		$block = $observer->getEvent ()->getBlock ();
		if ($block != null && $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction && $block->getRequest ()->getControllerName () == 'catalog_product') {
			$block->addItem ( 'massAddToBacklog', array (
					'label' => Mage::helper ( 'meinpaket' )->__ ( 'Add to Allyouneed backlog' ),
					'url' => Mage::app ()->getStore ()->getUrl ( 'adminhtml/meinpaket_backlog_product/massAddToBacklog' ) 
			) );
		}
		
		return $this;
	}
}