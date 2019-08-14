<?php

/**
 * Service class which imports the whole MeinPaket marketplace structure
 * as categories into Magento.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_MarketplaceCategoryImport
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_ImportService extends Varien_Object {
	/**
	 *
	 * @var Mage_Catalog_Model_Category_Api
	 */
	protected $categoryApi = null;
	
	/**
	 *
	 * @var array
	 */
	private $idMapping = array ();
	
	/**
	 *
	 * @var Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result
	 */
	protected $result = null;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->result = Mage::getModel ( 'meinpaket/Service_MarketplaceCategoryImport_Result' );
	}
	
	/**
	 * Imports the marketplace category structure into the local marketplace root category.
	 *
	 * @return void
	 */
	public function importMarketplaceCategoryStructure() {
		/* @var $structure array */
		$structure = null;
		
		/* @var $rootCategory Mage_Catalog_Model_Category */
		$rootCategory = Mage::getModel ( 'catalog/category' );
		
		/* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
		$collection = $rootCategory->getCollection ();
		
		/* @var $memoryLimiter Dhl_MeinPaket_Model_System_MemoryLimiter */
		$memoryLimiter = Mage::getModel ( 'meinpaketcommon/system_memoryLimiter' );
		
		/* @var $requestXml string */
		$requestXml = '';
		
		/* @var $resultXml string */
		$resultXml = '';
		
		/* @var $structure array */
		$structure = null;
		
		/* @var $client Dhl_MeinPaket_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$memoryLimiter->setMemoryLimit ( Dhl_MeinPaketCommon_Model_System_MemoryLimiter::MEMORY_LIMIT_VERY_HIGH );
		
		/* @var $ids array */
		$ids = array ();
		
		$transaction = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
		
		try {
			$transaction->beginTransaction ();
			
			/* @var $request Dhl_MeinPaket_Model_Xml_Request_DownloadRequest */
			$request = Mage::getModel ( 'meinpaketcommon/xml_request_downloadRequest' );
			$request->addDownloadMarketplaceCategories ();
			
			$dom = $client->send ( $request );
			
			$ids = Mage::getResourceModel ( 'meinpaket/category_collection' )->getAllIds ();
			
			$categories = array ();
			
			/* @var $category Dhl_MeinPaket_Model_Xml_Response_Partial_Category */
			foreach ( $dom->getCategories () as $category ) {
				/* @var $model Dhl_MeinPaket_Model_Category */
				$model = Mage::getModel ( 'meinpaket/category' )->load ( $category->getCode (), 'code' );
				
				$model->setName ( trim ( $category->getName () ) );
				$model->setCode ( trim ( $category->getCode () ) );
				$model->setParent ( trim ( $category->getParent () ) );
				$model->setLeaf ( true );
				
				$categories [$model->getCode ()] = $model;
				
				if ($model->getId ()) {
					$ids = array_diff ( $ids, array($model->getId ()) );
				}
			}
			
			foreach ( $categories as $key => $value ) {
				if (array_key_exists ( $value->getParent (), $categories )) {
					$categories [$value->getParent ()]->setLeaf ( false );
				}
			}
			
			foreach ( $categories as $key => $value ) {
				if ($value->getData () != $value->getOrigData ()) {
					$oldId = $value->getId ();
					$value->save ();
					if ($oldId) {
						$this->result->addUpdatedCategory ( $value );
					} else {
						$this->result->addNewCategory ( $value );
					}
				}
			}
			
			foreach ( $ids as $id ) {
				$model = Mage::getModel ( 'meinpaket/category' )->load ( $id );
				if ($model->getId () != null) {
					$this->result->addDeletedCategory ( $model );
					$model->delete ();
				}
			}
			
			$transaction->commit ();
			
			Mage::getModel ( 'meinpaket/entity_attribute_source_meinPaketCategory' )->cleanCache ();
		} catch ( Exception $e ) {
			$transaction->rollback ();
			Mage::logException ( $e );
			throw $e;
		}
		
		return $this->result;
	}
}

