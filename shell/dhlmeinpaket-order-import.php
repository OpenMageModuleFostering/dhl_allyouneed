<?php
require_once 'abstract.php';

/**
 * Imports the category structure from Allyouneed.
 *
 * @category Dhl
 * @package Dhl_MeinPaket
 * @subpackage Dhl_MeinPaket_Shell
 * @version $Id$
 */
class Dhl_MeinPaketCommon_Shell_OrderImport extends Mage_Shell_Abstract {
	/**
	 *
	 * @var integer
	 */
	const DEFAULT_HOURS = 24;
	
	/**
	 * Imports orders from DHL Allyouneed.
	 *
	 * @return Dhl_MeinPaket_Shell_OrderImport
	 */
	public function run() {
		echo "Starting order import (" . $this->getFormattedDate () . ")\n";
		
		/* @var $service Dhl_MeinPaket_Model_Service_Order_ImportService */
		$service = Mage::getModel ( "meinpaketcommon/service_order_importService" );
		$hours = self::DEFAULT_HOURS;
		$startTime = 0;
		$endTime = 0;
		
		if ($this->getArg ( 'hours' )) {
			$passedHours = ( integer ) $this->getArg ( 'hours' );
			if ($passedHours < 1) {
				echo 'Number of given hours (' . $passedHours . ') is to small. Process cancelled.';
				return $this;
			}
			$hours = $passedHours;
		}
		
		$endTime = time ();
		$startTime = $endTime - ($hours * 3600);
		
		echo "Time range: " . $this->getFormattedDate ( $startTime ) . " GMT - " . $this->getFormattedDate ( $endTime ) . " GMT\n";
		
		try {
			$service->importOrders ( $startTime, $endTime );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			echo 'Order import failed. Exception occured: ' . $e->getMessage ();
		}
		
		echo "Import finished       (" . $this->getFormattedDate () . ")\n";
		echo "Imported " . $service->getOrderCount ()['imported'] . " orders.\n";
		
		return $this;
	}
	
	/**
	 * Retrieve Usage Help Message
	 */
	public function usageHelp() {
		return <<<USAGE
Usage:  php -f dhlmeinpaket-order-import.php -- [options]

  --hours  The time in hours for which to import orders.
           The default setting is 24, which means that the orders of the 
           last 24 hours will be imported.
  help     This help
\n
USAGE;
	}
	
	/**
	 * Returns the current timestamp as a formatted string.
	 *
	 * @return string
	 */
	protected function getFormattedDate($timestamp = null) {
		$format = "%Y-%m-%d %H:%M:%S";
		$date = '';
		
		if ($timestamp !== null) {
			$date = strftime ( $format, $timestamp );
		} else {
			$date = strftime ( $format );
		}
		
		return $date;
	}
}

$orderImporter = new Dhl_MeinPaketCommon_Shell_OrderImport ();
$orderImporter->run ();
