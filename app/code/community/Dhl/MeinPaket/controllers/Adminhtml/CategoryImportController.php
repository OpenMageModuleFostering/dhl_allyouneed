<?php

/**
 * Controller for Allyouneed marketplace category structure import.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Adminhtml
 * @version		$Id$
 */
class Dhl_MeinPaket_Adminhtml_CategoryImportController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Initializes the controller.
	 *
	 * @return Dhl_MeinPaket_Adminhtml_CategoryImportController
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaket' );
		$this->_title ( $this->__ ( 'Allyouneed' ) )->_title ( $this->__ ( 'Category Import' ) );
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/meinpaket/category_import' );
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
	 * Processes the category import and displays the results.
	 *
	 * @return void
	 */
	public function importAction() {
		/* @var $service Dhl_MeinPaket_Model_MarketplaceCategoryStructureImportService */
		$service = Mage::getModel ( 'meinpaket/Service_MarketplaceCategoryImport_ImportService' );
		
		/* @var $result Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result */
		$result = null;
		
		$errorMsg = '';
		
		try {
			$result = $service->importMarketplaceCategoryStructure ();
		} catch ( Dhl_MeinPaket_Model_Client_BadHttpReturnCodeException $e ) {
			Mage::logException ( $e );
			$errorMsg = $this->__ ( 'Connecting to Allyouneed server failed.' );
		} catch ( Dhl_MeinPaket_Model_Client_HttpTimeoutException $e ) {
			Mage::logException ( $e );
			$errorMsg = $this->__ ( 'Connection to Allyouneed server timed out.' );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			$errorMsg = $this->__ ( 'Unknown error' ) . '. (' . $e->getMessage () . ')';
		}
		
		if (strlen ( $errorMsg ) > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Error' ) . ': ' . $errorMsg );
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( $this->__ ( 'Successfully imported marketplace categories.' ) );
		}
		
		if ($result !== null) {
			
			$countNew = $result->getNewCategoriesCount ();
			$countRenamed = $result->getUpdatedCategoriesCount ();
			$countDeleted = $result->getDeletedCategoriesCount ();
			
			if ($countNew > 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( sprintf ( $this->__ ( 'Added %s new categories.' ), $countNew ) );
			}
			if ($countRenamed > 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( sprintf ( $this->__ ( 'Renamed %s categories.' ), $countRenamed ) );
			}
			if ($countDeleted > 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( sprintf ( $this->__ ( 'Deleted %s categories.' ), $countDeleted ) );
			}
		}
		
		$this->_initAction ();
		
		if ($result !== null) {
			$this->getLayout ()->getBlock ( 'meinpaket.adminhtml_categoryImport_import' )->setResult ( $result );
		}
		
		$this->renderLayout ();
	}
}
