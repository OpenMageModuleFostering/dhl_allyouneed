<?php

/**
 * Cron handler for Dhl Allyouneed.
 *
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model
 */
class Dhl_MeinPaket_Model_Cron {
	const SYNC_CATALOG = 'meinpaket_sync_catalog';
	public static $CRONJOBS = array (
			self::SYNC_CATALOG,
	);
	
	/**
	 * Called to synchronize catalog.
	 *
	 * @return NULL
	 */
	public function exportProducts() {
		try {
			return Mage::getSingleton ( 'meinpaket/service_product_export' )->exportProducts ();
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
		return null;
	}
	
	/**
	 * Called to request best prices.
	 *
	 * @return NULL
	 */
	public function getBestPrice() {
		try {
			return Mage::getSingleton ( 'meinpaket/service_productData_requestService' )->requestBestPrices ();
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
		return null;
	}
}