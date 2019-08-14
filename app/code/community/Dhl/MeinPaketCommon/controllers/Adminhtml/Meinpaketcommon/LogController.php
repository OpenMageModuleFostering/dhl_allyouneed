<?php

/**
 * 
 */
class Dhl_MeinPaketCommon_Adminhtml_Meinpaketcommon_LogController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaketcommon/log' )->_addBreadcrumb ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Log' ), Mage::helper ( 'meinpaketcommon' )->__ ( 'Log' ) );
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed() {
		return Mage::getSingleton ( 'admin/session' )->isAllowed ( 'admin/meinpaket/log' );
	}
	public function indexAction() {
		$this->_initAction ()->renderLayout ();
	}
	public function exportCsvAction() {
		$fileName = 'log.csv';
		$grid = $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_log_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getCsvFile () );
	}
	public function exportExcelAction() {
		$fileName = 'log.xml';
		$grid = $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_log_grid' );
		$this->_prepareDownloadResponse ( $fileName, $grid->getExcelFile ( $fileName ) );
	}
	public function massDeleteAction() {
		$logIds = $this->getRequest ()->getParam ( 'logIds' );
		if (! is_array ( $logIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Please select log entries.' ) );
		} else {
			try {
				$logModel = Mage::getModel ( 'meinpaketcommon/log' );
				foreach ( $logIds as $logId ) {
					$logModel->load ( $logId )->delete ();
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Total of %d record(s) were deleted.', count ( $logIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	public function massProcessAction() {
		$logIds = $this->getRequest ()->getParam ( 'logIds' );
		if (! is_array ( $logIds )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Please select log entries.' ) );
		} else {
			try {
				$logModel = Mage::getModel ( 'meinpaketcommon/log' );
				foreach ( $logIds as $logId ) {
					$logModel->load ( $logId );
					$xmlDocument = new DOMDocument ();
					$xmlDocument->loadXML ( $logModel->getReceived () );
					Mage::getModel ( 'meinpaketcommon/xml_xmlResponseParser' )->parseResponse ( $xmlDocument );
				}
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'meinpaketcommon' )->__ ( 'Total of %d record(s) were deleted.', count ( $logIds ) ) );
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
			}
		}
		$this->_redirect ( '*/*/index' );
	}
	
	/**
	 * Product grid for AJAX request.
	 * Sort and filter result for example.
	 */
	public function gridAction() {
		$this->loadLayout ();
		$this->getResponse ()->setBody ( $this->getLayout ()->createBlock ( 'meinpaketcommon/adminhtml_log_grid' )->toHtml () );
	}
}
