<?php

/**
 * 
 */
class Dhl_MeinPaket_Adminhtml_BestPriceController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaket/bestprice' )->_addBreadcrumb ( Mage::helper ( 'meinpaket' )->__ ( 'Best Price' ), Mage::helper ( 'meinpaket' )->__ ( 'Best Price' ) );
		return $this;
	}
	public function indexAction() {
		$this->_initAction ()->renderLayout ();
	}
	public function exportCsvAction() {
		$fileName = 'bestprice.csv';
		$grid = $this->getLayout ()->createBlock ( 'meinpaket/adminhtml_bestPrice_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getCsvFile () );
	}
	public function exportExcelAction() {
		$fileName = 'bestprice.xml';
		$grid = $this->getLayout ()->createBlock ( 'meinpaket/adminhtml_bestPrice_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getExcelFile ( $fileName ) );
	}
	
	/**
	 * Product grid for AJAX request.
	 * Sort and filter result for example.
	 */
	public function gridAction() {
		$this->loadLayout ();
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'meinpaket/adminhtml_bestPrice_grid' )->toHtml () );
	}
}
