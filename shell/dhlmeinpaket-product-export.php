<?php
require_once 'abstract.php';

/**
 * Exports products to Allyouneed.
 *
 * @category Dhl
 * @package Dhl_MeinPaket
 * @subpackage Dhl_MeinPaket_Shell
 * @version $Id$
 */
class Dhl_MeinPaket_Shell_ProductExport extends Mage_Shell_Abstract {
	/**
	 * Exports products DHL Allyouneed.
	 *
	 * @return Dhl_MeinPaket_Shell_ProductExport
	 */
	public function run() {
		echo " > Starting Export (" . $this->getFormattedDate () . ")\n";
		
		/* @var $exportService Dhl_MeinPaket_Model_Service_Product_Export */
		$exportService = Mage::getModel ( 'meinpaket/service_product_export' );
		
		$result = null;
		
		$commonErrors = null;
		
		// export products
		try {
			$result = $exportService->exportProducts ();
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			echo 'Products could not be exported: ' . $e->getMessage ();
			return $this;
		}
		
		//$this->showResults ( $result );
		
		return $this;
	}
	
	/**
	 * Retrieve Usage Help Message
	 */
	public function usageHelp() {
		return <<<USAGE
Usage:  php -f dhlmeinpaket-product-export.php -- [options]

  all      Exports all products. No matter if they have their synchronize with DHL MeinPaket flag enabled.
  help     This help
\n
USAGE;
	}
	
	/**
	 * Echoes information about the result of the product export process.
	 *
	 * @param Dhl_MeinPaket_Model_Service_Product_Export_Result $result        	
	 * @return void
	 */
	protected function showResults(Dhl_MeinPaket_Model_Service_Product_Export_Result $result) {
		$commonErrors = $result->getCommonErrors ();
		$fullyConfirmedIds = $result->getFullyConfirmedProductIds ();
		$offeredOnly = $result->getProductsWhichCouldOnlyBeOffered ();
		$totallyFailed = $result->getProductsWhichCouldNotBeOfferedOrDescribed ();
		
		echo " > Finished Export (" . $this->getFormattedDate () . ")\n";
		echo "----------------------------------------------------------\n";
		echo "|  RESULT OVERVIEW:                                      |\n";
		echo "----------------------------------------------------------\n";
		echo " > Successfully exported: " . sizeof ( $fullyConfirmedIds ) . "\n";
		echo " > Offered only:          " . sizeof ( $offeredOnly ) . "\n";
		echo " > Not exportable:        " . sizeof ( $totallyFailed ) . "\n";
	}
	
	/**
	 * Returns the current timestamp as a formatted string.
	 *
	 * @return string
	 */
	protected function getFormattedDate() {
		return strftime ( "%Y-%m-%d %H:%M:%S" );
	}
}

$productExporter = new Dhl_MeinPaket_Shell_ProductExport ();
$productExporter->run ();

