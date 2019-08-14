<?php

/**
 * Controls the export of local products to Allyouneed.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Adminhtml
 * @version		$Id$
 */
class Dhl_MeinPaket_Adminhtml_Meinpaket_ProductExportController extends Mage_Adminhtml_Controller_Action {
	/**
	 *
	 * @var integer
	 */
	const PRODUCT_SELECTION_MODE_SYNCED_ONLY = 1;
	
	/**
	 *
	 * @var integer
	 */
	const PRODUCT_SELECTION_MODE_ALL = 2;
	
	/**
	 *
	 * @var integer
	 */
	protected $_defaultSelectionMode = self::PRODUCT_SELECTION_MODE_SYNCED_ONLY;
	
	/**
	 * Initialization.
	 *
	 * @return Dhl_MeinPaket_Adminhtml_ProductExportController
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaket' );
		
		$this->_title ( $this->__ ( 'Allyouneed' ) )->_title ( $this->__ ( 'Product Export' ) );
		
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/meinpaket/product_export' );
	}
	
	/**
	 * Default action.
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * Shows a list of the selected products.
	 *
	 * @return void
	 */
	public function listAction() {
		$this->_initAction ();
		$this->getLayout ()->getBlock ( 'meinpaket.adminhtml_productExport_list' )->assignProductCollection ( $this->getProductCollection () );
		
		$this->renderLayout ();
	}
	
	/**
	 * Transfers descriptions and offers of the selected products to Allyouneed.
	 *
	 * @return void
	 */
	public function exportAction() {
		/* @var $exportService Dhl_MeinPaket_Model_Service_Product_Export */
		$exportService = Mage::getModel ( 'meinpaket/service_product_export' );
		
		/* @var $results Dhl_MeinPaket_Model_Service_Product_Export_Result */
		$results = null;
		
		try {
			$results = $exportService->exportProducts ();
			
			if ($debugMode) {
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'DEBUG:' . $results->debugOutput () );
			}
			if (count ( $results->getFullyConfirmedProductIds () ) > 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( sprintf ( $this->__ ( '%s of %s products were successfully exported' ), count ( $results->getFullyConfirmedProductIds () ), $collection->count () ) );
			} else {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( $this->__ ( 'There were no products that could be exported.' ) );
			}
		} catch ( Dhl_MeinPaket_Model_Xml_XmlBuildException $xmlBuildException ) {
			Mage::logException ( $xmlBuildException );
			$exceptionMsg = $this->__ ( 'Product export failed. Request could not be built.' );
			Mage::logException ( $xmlBuildException );
		} catch ( Dhl_MeinPaket_Model_Client_HttpException $httpException ) {
			Mage::logException ( $httpException );
			$exceptionMsg = $this->__ ( 'Product export failed. Failed connecting to MeinPaket server.' );
			Mage::logException ( $httpException );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			$exceptionMsg = $this->__ ( 'Product export failed for unknown reason.' );
		}
		
		if (strlen ( $exceptionMsg ) > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $exceptionMsg );
		}
		
		$this->_initAction ();
		$block = $this->getLayout ()->getBlock ( 'meinpaket.adminhtml_productExport_export' );
		/*
		 * if ($results !== null) {
		 * $block->setResults ( $results );
		 * }
		 */
		$this->renderLayout ();
	}
	
	/**
	 * Do nothin.
	 *
	 * @return void
	 */
	public function emptyAction() {
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * Returns a collection of the products which have to be exported.
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	private function getProductCollection() {
		/* @var $selectService Dhl_MeinPaket_Model_Service_Product_Export_Select */
		$selectService = Mage::getModel ( 'meinpaket/service_product_export_select' );
		$productselection = $this->getRequest ()->getParam ( 'productselection' );
		
		if ($productselection == 2) {
			return $selectService->getProductsForExport ( FALSE );
		} else {
			return $selectService->getProductsForExport ( TRUE );
		}
	}
}
