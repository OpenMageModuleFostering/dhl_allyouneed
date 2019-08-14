<?php
class Dhl_Postpay_ResponseController extends Mage_Core_Controller_Front_Action {
	public function pushAction() {
		// externalCartId=[CartId]&ordered=[ordered]&status=[Status]&notificationId=[notificationId]
		$cart = $Mage::getModel ( 'postpay/cart' )->load ( $this->getRequest ()->getParam ( 'externalCartId' ) );
		
		if (! $cart->getId () && $cart->getOrderId () != null) {
			return;
		}
		
		$order = Mage::getModel ( 'sales/order' )->load ( $cart->getOrderId () );
		/* @var $order Mage_Sales_Model_Order */
		
		if ($order && $order->getPayment ()->getMethodInstance ()->getCode () == 'postpay' && $order->getPayment ()->getMethodInstance ()->getInfoInstance ()->getData ( 'postpay_order_id' ) == $this->getRequest ()->getParam ( 'orderId' ) && $order->getPayment ()->getMethodInstance ()->getInfoInstance ()->getData ( 'postpay_notification_id' ) == $this->getRequest ()->getParam ( 'notificationId' )) {
			$status = $this->getRequest ()->getParam ( 'status', 'Pending' );
			$order->getPayment ()->getMethodInstance ()->getInfoInstance ()->setData ( 'postpay_status', $status );
			
			switch ($status) {
				case 'CreatedOrder' :
					if ($order->canInvoice ()) {
						$invoice = $order->prepareInvoice ();
						
						$invoice->setRequestedCaptureCase ( Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE );
						$invoice->register ();
						$transactionSave = Mage::getModel ( 'core/resource_transaction' )->addObject ( $invoice )->addObject ( $invoice->getOrder () );
						$transactionSave->save ();
					}
					break;
				case 'Canceled' :
					if ($order->canCancel ()) {
						$order->cancel ();
						$order->save ();
					}
					break;
				default :
				// Nothing to do
			}
		}
	}
}
