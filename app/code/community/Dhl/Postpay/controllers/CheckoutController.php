<?php
class Dhl_Postpay_CheckoutController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Forward to postpay.
	 */
	public function indexAction() {
		Mage::log ( 'Called custom ' . __METHOD__ );
		
		$quote = Mage::helper ( 'meinpaketcommon/data' )->getQuoteFiltered ();
		
		if ($quote == null) {
			$this->_redirect ( 'checkout/cart' );
		}
		
		$request = new Dhl_MeinPaketCommon_Model_Xml_Request_SubmitCartRequest ();
		
		$cart = Mage::getModel ( 'postpay/cart' );
		$cart->generateNotificationId ();
		$cart->save ();
		
		$notificationId = $request->addCart ( $quote, $cart );
		
		if ($request->isHasData ()) {
			
			// $this->getInfoInstance ()->setData ( 'postpay_notification_id' );
			
			$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
			/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
			
			try {
				$response = $client->send ( $request );
				/* @var $response Dhl_MeinPaketCommon_Model_Xml_Response_SubmitCartResponse */
				
				// $this->getInfoInstance ()->setData ( 'postpay_cart_id', $response->getCartId () );
				
				$quote->delete ();
				$this->_redirectUrl ( $response->getRedirectURL () );
				return;
			} catch ( Exception $e ) {
				Mage::logException ( $e );
			}
		}
		$this->_redirect ( 'checkout/cart' );
	}
}
