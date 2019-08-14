<?php

/**
 * Controller for MeinPaket order import.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Adminhtml
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Adminhtml_OrderImportController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Initialized the controller.
	 *
	 * @return Dhl_MeinPaket_Adminhtml_OrderImportController
	 */
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'meinpaket' );
		$this->_title ( $this->__ ( 'Allyouneed' ) )->_title ( $this->__ ( 'Order Import' ) );
		return $this;
	}
	
	/**
	 * Default controller action.
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * Triggers the import process.
	 *
	 * @return void
	 */
	public function importAction() {
		/* @var $OrderImportService Dhl_MeinPaket_Model_Order_ImportService */
		$OrderImportService = Mage::getModel ( 'meinpaketcommon/Order_ImportService' );
		$startdate = $this->getRequest ()->getParam ( 'startdate', 0 );
		$enddate = $this->getRequest ()->getParam ( 'enddate', 0 );
		
		if ($startdate == 0) {
			$startdate = time () - 3600;
		} else {
			$startdate = $this->parseTime ( $startdate );
		}
		if ($enddate == 0) {
			$enddate = $this->parseTime ( date ( 'm' ) . '/' . date ( 'd' ) . '/' . date ( 'y' ), true );
		} else {
			$enddate = $this->parseTime ( $enddate, true );
		}
		
		try {
			$status = $OrderImportService->importOrders ( $startdate, $enddate );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			Mage::getSingleton ( 'adminhtml/session' )->addError ( 'An error occured: ' . $e->getMessage () );
		}
		
		if ($OrderImportService->wasServiceResponseMalformed ()) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( __ ( 'The MeinPaket server did not deliver a valid response. Have a look at the system log for more information.' ) );
		} else {
			$orderCount = $OrderImportService->getOrderCount ();
			
			if ($orderCount ['imported'] > 0) {
				$message = sprintf ( __ ( '%s order(s) sucessfully imported.' ), $orderCount ['imported'] );
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( $message );
			} elseif ($orderCount ['outOfStock'] === 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( __ ( 'No new orders found.' ) );
			}
			
			if ($orderCount ['duplicates'] > 0) {
				if ($orderCount ['duplicates'] > 1)
					$message = sprintf ( __ ( 'Skipped %s duplicate orders that were already imported previously.' ), $orderCount ['duplicates'] );
				else
					$message = sprintf ( __ ( 'Skipped %s duplicate order that was already imported previously.' ), $orderCount ['duplicates'] );
				
				Mage::getSingleton ( 'adminhtml/session' )->addNotice ( $message );
			}
			
			if ($orderCount ['outOfStock'] > 0) {
				$message = sprintf ( __ ( 'Skipped %s order(s) because: out of stock. Please process the following order(s) at your Allyouneed dealer area:' ), $orderCount ['outOfStock'] );
				$dhlOrderIds = implode ( ', ', $OrderImportService->getOutOfStockOrders () );
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $message . " " . $dhlOrderIds );
			}
			
			if ($orderCount ['disabled'] > 0) {
				$message = sprintf ( __ ( 'Skipped %s order(s) because: some or all of the included products were disabled in Magento after the export to Allyouneed . Please process the following order(s) at your Allyouneed dealer area:' ), $orderCount ['disabled'] );
				$disabledDhlOrderIds = implode ( ', ', $OrderImportService->getDisabledProductOrders () );
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $message . " " . $disabledDhlOrderIds );
			}
			
			if ($orderCount ['invalid'] > 0) {
				$message = sprintf ( __ ( 'Skipped %s order(s) because: some or all of the included products were invalid for import. Please process the following order(s) at your Allyouneed dealer area:' ), $orderCount ['invalid'] );
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $message );
			}
		}
		
		$this->_initAction ();
		$this->renderLayout ();
	}
	
	/**
	 * Converts a date string in the format "MM/DD/YY" into a timestamp.
	 *
	 * @param string $data
	 *        	Must be formatted as "MM/DD/YY"
	 * @param boolean $toEndOfDay
	 *        	If set to true the time of the day will be set to "23:59:59", otherwise "00:00:00".
	 * @return integer timestamp
	 */
	private function parseTime($data, $toEndOfDay = false) {
		$seconds = 0;
		$minutes = 0;
		$hours = 0;
		$timeData = strptime ( $data, '%m/%e/%y' );
		
		if ($toEndOfDay === true) {
			$seconds = 59;
			$minutes = 59;
			$hours = 23;
		}
		
		return mktime ( $hours, $minutes, $seconds, $timeData ['tm_mon'] + 1, $timeData ['tm_mday'], 1900 + $timeData ['tm_year'] );
	}
}
