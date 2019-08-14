<?php

/**
 * Service class which encapsulates the product export process.
 * 
 * @category	Mage
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_Product
 */
class Dhl_MeinPaket_Model_Service_Product_Export extends Dhl_MeinPaketCommon_Model_Service_Abstract {
	/**
	 *
	 * @var Dhl_MeinPaket_Helper_Product
	 */
	protected $_productHelper = null;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_processableProducts = array ();
		$this->_productHelper = Mage::helper ( 'meinpaketcommon/product' );
	}
	
	/**
	 * Exports products.
	 *
	 * @param integer $selectionMode        	
	 * @return Dhl_MeinPaket_Model_Service_Product_Export_Result
	 */
	public function exportProducts() {
		$cycleCount = Mage::getStoreConfig ( 'meinpaket/advanced/cycle_product_count' );
		if (! is_numeric ( $cycleCount )) {
			$cycleCount = 100;
		}
		
		$seenMagentoProducts = array ();
		
		$uploadRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_UploadRequest ();
		
		/* @var $client Dhl_MeinPaket_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$count = 0;
		$currentPage = 1;
		
		/* @var $productBacklogs Dhl_MeinPaket_Model_Mysql4_Backlog_Product_Collection */
		$productBacklogs = Mage::getModel ( 'meinpaket/backlog_product' )->getCollection ();
		$productBacklogs->setPageSize ( $cycleCount );
		$productBacklogs->addOrder ( 'product_id', Varien_Data_Collection::SORT_ORDER_ASC );
		
		$pages = $productBacklogs->getLastPageNumber ();
		
		do {
			$productBacklogs->setCurPage ( $currentPage );
			$productBacklogs->load ();
			if ($productBacklogs->count () <= 0) {
				break;
			}
			
			foreach ( $productBacklogs as $productBacklog ) {
				$productId = $productBacklog->getProductId ();
				$changes = explode ( ',', $productBacklog->getChanges () );
				
				try {
					if (! isset ( $seenMagentoProducts [$productId] )) {
						$seenMagentoProducts [$productId] = true;
						
						/* @var $product Mage_Catalog_Model_Product */
						$product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::helper ( 'meinpaketcommon/data' )->getMeinPaketStoreId () )->load ( $productId );
						
						$syncMode = $product->getData ( 'sync_with_dhl_mein_paket' );
						
						switch ($syncMode) {
							case Dhl_MeinPaket_Model_Entity_Attribute_Source_ProductSyncMode::COMPLETE :
								$uploadRequest->addProductDescription ( $product );
								break;
							case Dhl_MeinPaket_Model_Entity_Attribute_Source_ProductSyncMode::OFFER :
								if ($productBacklog->getRequestDescriptionUpload ()) {
									$uploadRequest->addProductDescription ( $product );
								} else {
									$uploadRequest->addOffer ( $product );
								}
								break;
							default :
								$uploadRequest->removeProduct ( $product );
								break;
						}
					} else {
						Mage::log ( 'Product m' . $productId . ' already synced' );
					}
					
					$productBacklog->delete ();
					$count ++;
				} catch ( Exception $ex ) {
					Mage::logException ( $ex );
					Mage::log ( 'Error syncing product m' . $productId );
				}
			}
			
			/**
			 * We deleted all products on the first page.
			 * If we load this page again, there will be new products.
			 */
			// $currentPage ++;
			$productBacklogs->clear ();
		} while ( $count < $cycleCount );
		
		if ($uploadRequest->isHasData ()) {
			$response = $client->send ( $uploadRequest, true );
		}
		
		if ($currentPage < $pages) {
			Mage::helper ( 'meinpaket/cron' )->scheduleJobs ( array (
					Dhl_MeinPaketCommon_Model_Cron::SYNC_ASYNC 
			), false );
		}
		
		return Mage::helper ( 'meinpaket/data' )->__ ( "Processed %d products", $count );
	}
	
	/**
	 * Exports products.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return Dhl_MeinPaket_Model_Service_Product_Export_Result
	 */
	public function deleteProduct(Mage_Catalog_Model_Product $product) {
		$uploadRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_UploadRequest ();
		
		/* @var $client Dhl_MeinPaket_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$uploadRequest->removeProduct ( $product );
		
		if ($uploadRequest->isHasData ()) {
			$response = $client->send ( $uploadRequest, true );
		}
		
		return Mage::helper ( 'meinpaket/data' )->__ ( "Deleted %d products", 1 );
	}
}
