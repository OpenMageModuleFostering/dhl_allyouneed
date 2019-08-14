<?php

/**
 * Cron handler for Dhl Allyouneed.
 *
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model
 */
class Dhl_Postpay_Model_Cron {
	const SYNC_ORDERS = 'meinpaket_sync_orders';
	public static $CRONJOBS = array (
			self::SYNC_CATALOG 
	);
	
	/**
	 * Called to download orders.
	 *
	 * @return NULL
	 */
	public function importOrders() {
		try {
			return Mage::getSingleton ( 'postpay/service_order_importService' )->importOrders ();
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
		return null;
	}
}
