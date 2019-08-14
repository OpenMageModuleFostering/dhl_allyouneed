<?php
require_once 'abstract.php';

/**
 * Imports the variant configurations from Allyouneed.
 *
 * @category Dhl
 * @package Dhl_MeinPaket
 * @subpackage Dhl_MeinPaket_Shell
 * @version $Id$
 */
class Dhl_MeinPaket_Shell_RequestBestPrice extends Mage_Shell_Abstract {
	/**
	 * Imports the variant configurations from DHL Allyouneed.
	 *
	 * @return Dhl_MeinPaket_Shell_VariantConfigurationsDownload
	 */
	public function run() {
		/* @var $service Dhl_MeinPaket_Model_Service_ProductData_RequestService */
		$service = Mage::getModel ( 'meinpaket/service_productData_requestService' );
		
		/* @var $result Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result */
		$result = null;
		
		echo "Requesting \n";
		
		try {
			$result = $service->requestBestPrices ();
		} catch ( Exception $e ) {
			echo 'Request failed. Exception occured: "' . $e->getMessage () . '"' . "\n";
			Mage::logException ( $e );
			return $this;
		}
		
		return $this;
	}
	
	/**
	 * Retrieve Usage Help Message
	 */
	public function usageHelp() {
		return <<<USAGE
Usage:  php -f dhlmeinpaket-request-bestprice.php -- [options]

  help     This help
\n
USAGE;
	}
}

$shll = new Dhl_MeinPaket_Shell_RequestBestPrice ();
$shll->run ();

