<?php
class Dhl_Postpay_ResponseController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * When postpay returns.
	 */
	public function successAction() {
		Mage::log ( 'Called custom ' . __METHOD__ );
		// if (! $this->_isValidToken ()) {
		// Mage::Log ( 'Token is invalid.' );
		// $this->_redirect ( 'checkout/cart' );
		// }
		
		$this->_redirect ( 'checkout/onepage/success', array (
				'_secure' => true 
		) );
	}
	/**
	 * Handles 'falures' from api
	 * Failure could occur if api system failure, insufficent funds, or system error.
	 *
	 * @throws Exception
	 */
	public function errorAction() {
		Mage::Log ( 'Called ' . __METHOD__ );
		$this->_cancelAction ();
	}
	public function backAction() {
		Mage::Log ( 'Called ' . __METHOD__ );
		$this->_cancelAction ();
	}
	protected function _cancelAction() {
		$orderId = $this->getRequest ()->getParam ( 'order' );
		
		if ($orderId != null) {
			$order = Mage::getModel ( 'sales/order' )->load ( $orderId );
			/* @var $order Mage_Sales_Model_Order */
			if ($order->canCancel ()) {
				$order->cancel ();
			}
		}
		
		$this->_redirect ( 'checkout/onepage/failure', array (
				'_secure' => true 
		) );
	}
}
