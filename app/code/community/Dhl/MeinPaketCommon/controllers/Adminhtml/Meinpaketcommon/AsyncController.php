<?php

/**
 * 
 */
class Dhl_MeinPaketCommon_Adminhtml_Meinpaketcommon_AsyncController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaketcommon/async' )->_addBreadcrumb ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Async' ), Mage::helper ( 'meinpaketcommon' )->__ ( 'Async' ) );
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/meinpaket/async' );
	}
	public function indexAction() {
		$this->_initAction ()->renderLayout ();
	}
	public function exportCsvAction() {
		$fileName = 'async.csv';
		$grid = $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_async_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getCsvFile () );
	}
	public function exportExcelAction() {
		$fileName = 'async.xml';
		$grid = $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_async_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getExcelFile ( $fileName ) );
	}
	public function massDeleteAction() {
		$asyncIds = $this->getRequest ()->getParam ( 'asyncIds' );
		if (! is_array ( $asyncIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'meinpaket' )->__ ( 'Please select async entries.' ) );
		} else {
			try {
				$asyncModel = Mage::getModel ( 'meinpaketcommon/async' );
				foreach ( $asyncIds as $asyncId ) {
					$asyncModel->load ( $asyncId )->delete ();
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'meinpaket' )->__ ( 'Total of %d record(s) were deleted.', count ( $asyncIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * Mass action: schedule
	 *
	 * @return void
	 */
	public function scheduleAction() {
		$cronjobs = $this->getCronjobs ();
		Mage::helper ( 'meinpaketcommon/cron' )->scheduleJobs ( $cronjobs, true );
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * Mass action: run
	 *
	 * @return void
	 */
	public function runAction() {
		$cronjobs = $this->getCronjobs ();
		Mage::helper ( 'meinpaketcommon/cron' )->runJobs ( $cronjobs, true );
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * Get cronjobs from request.
	 *
	 * @return array of cronjobs
	 */
	private function getCronjobs() {
		$cronjob = $this->getRequest ()->getParam ( 'cronjob', 'all' );
		if ($cronjob == 'all') {
			return Dhl_MeinPaket_Model_Cron::$CRONJOBS;
		} else if (in_array ( $cronjob, Dhl_MeinPaketCommon_Model_Cron::$CRONJOBS )) {
			return array (
					$cronjob 
			);
		}
		return array ();
	}
	
	/**
	 * Product grid for AJAX request.
	 * Sort and filter result for example.
	 */
	public function gridAction() {
		$this->loadLayout ();
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_async_grid' )->toHtml () );
	}
}
