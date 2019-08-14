<?php

/**
 * Order model related helper methods.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Util
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Helper_Order extends Mage_Core_Helper_Abstract {
	/**
	 * Tells if the given order is an order that was imported from Allyouneed.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 * @return boolean
	 */
	public function isMeinPaketOrder(Mage_Sales_Model_Order $order) {
		return ($order->hasData ( 'dhl_mein_paket_order_id' ) && $order->getData ( 'dhl_mein_paket_order_id' ));
	}
}
