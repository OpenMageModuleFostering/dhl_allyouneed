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
class Dhl_MeinPaket_Shell_CategoryImport extends Mage_Shell_Abstract {
	/**
	 * Imports the marketplace category structure from DHL Allyouneed.
	 *
	 * @return Dhl_MeinPaket_Shell_CategoryImport
	 */
	public function run() {
		/* @var $result Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result */
		$result = null;
		
		echo "Starting Category Import (" . $this->getFormattedDate () . ")\n";
		
		try {
			/* @var $service Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_ImportService */
			$service = Mage::getModel ( 'meinpaket/Service_MarketplaceCategoryImport_ImportService' );
			
			$result = $service->importMarketplaceCategoryStructure ();
		} catch ( Exception $e ) {
			echo 'Category Import failed. Exception occured: ' . $e->getMessage ();
			Mage::logException ( $e );
			return $this;
		}
		
		echo "Finished Category Import (" . $this->getFormattedDate () . ")\n";
		
		echo "Number of new categories:     " . $result->getNewCategoriesCount() . "\n";
		echo "Number of updated categories: " . $result->getUpdatedCategoriesCount() . "\n";
		echo "Number of deleted categories: " . $result->getDeletedCategoriesCount() . "\n";
		
		return $this;
	}
	
	/**
	 * Retrieve Usage Help Message
	 */
	public function usageHelp() {
		return <<<USAGE
Usage:  php -f dhlmeinpaket-order-import.php -- [options]

  help     This help
\n
USAGE;
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

$categoryImporter = new Dhl_MeinPaket_Shell_CategoryImport ();
$categoryImporter->run ();
