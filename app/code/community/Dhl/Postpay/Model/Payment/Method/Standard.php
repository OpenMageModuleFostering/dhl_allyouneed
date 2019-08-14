<?php

/**
 * Postpay payment method for imported orders.
 * 
 * @category	Mage
 * @package		Dhl_Postpay
 * @subpackage	Payment_Method
 */
class Dhl_Postpay_Model_Payment_Method_Standard extends Mage_Payment_Model_Method_Abstract {
	/**
	 *
	 * @var string
	 */
	protected $_code = 'postpay_standard';
	protected $_infoBlockType = 'postpay/payment_info_standard';
	protected $_formBlockType = 'postpay/payment_form_standard';
	
	/**
	 * Is this payment method a gateway (online auth/charge) ?
	 */
	protected $_isGateway = true;
	
	/**
	 * Can authorize online?
	 */
	protected $_canAuthorize = true;
	
	/**
	 * Can capture funds online?
	 */
	protected $_canCapture = true;
	
	/**
	 * Can capture partial amounts online?
	 */
	protected $_canCapturePartial = false;
	
	/**
	 * Can refund online?
	 */
	protected $_canRefund = false;
	
	/**
	 * Can void transactions online?
	 */
	protected $_canVoid = true;
	
	/**
	 * Can use this payment method in administration panel?
	 */
	protected $_canUseInternal = false;
	
	/**
	 * Can show this payment method as an option on checkout payment page?
	 */
	protected $_canUseCheckout = true;
	
	/**
	 * Is this payment method suitable for multi-shipping checkout?
	 */
	protected $_canUseForMultishipping = false;
	
	/**
	 * Can save credit card information for future processing?
	 */
	protected $_canSaveCc = false;
	
	/**
	 * Is this method already authorized for the current order.
	 *
	 * @var unknown
	 */
	protected $_isAuthorized = false;
	
	/**
	 * Here you will need to implement authorize, capture and void public methods
	 *
	 * @see examples of transaction specific public methods such as
	 *      authorize, capture and void in Mage_Paygate_Model_Authorizenet
	 */
	
	/**
	 * Return URL to redirect the customer to.
	 * Called after 'place order' button is clicked.
	 * Called after order is created and saved.
	 *
	 * @return string
	 */
	public function getOrderPlaceRedirectUrl() {
		// Get and clear value from session
		$url = Mage::getSingleton ( 'checkout/session' )->getData ( 'postpay_redirect_url', true );
		return $url;
	}
	
	/**
	 * Send authorize request to gateway
	 *
	 * @param Mage_Payment_Model_Info $payment        	
	 * @param decimal $amount        	
	 * @return Mage_Paygate_Model_Authorizenet
	 */
	public function authorize(Varien_Object $payment, $amount) {
		if ($this->_isAuthorized) {
			return $this;
		}
		
		if ($amount <= 0) {
			Mage::throwException ( Mage::helper ( 'postpay' )->__ ( 'Invalid amount for authorization.' ) );
		}
		
		$request = new Dhl_MeinPaketCommon_Model_Xml_Request_SubmitCartRequest ();
		
		$cart = Mage::getModel ( 'postpay/cart' );
		$cart->generateNotificationId ();
		$cart->setOrderId ( $payment->getOrder ()->getId () );
		$cart->save ();
		
		$notificationId = $request->addCart ( $payment->getOrder (), $cart );
		
		$this->getInfoInstance ()->setData ( 'postpay_notification_id', $cart->getNotificationId () );
		
		if ($request->isHasData ()) {
			$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
			/* @var $client Dhl_Postpay_Model_Client_XmlOverHttp */
			
			$response = $client->send ( $request );
			/* @var $response Dhl_Postpay_Model_Xml_Response_SubmitCartResponse */
			
			$this->getInfoInstance ()->setData ( 'postpay_cart_id', $response->getCartId () );
			
			Mage::getSingleton ( 'checkout/session' )->setData ( 'postpay_redirect_url', $response->getRedirectURL () );
		} else {
			throw new Exception ( 'Cannot authorize order' );
		}
		
		return $this;
	}
	
	/**
	 * Send capture request to gateway
	 *
	 * @param Mage_Payment_Model_Info $payment        	
	 * @param decimal $amount        	
	 * @return Mage_Paygate_Model_Authorizenet
	 */
	public function capture(Varien_Object $payment, $amount) {
		/*
		 * if ($amount <= 0) {
		 * Mage::throwException ( Mage::helper ( 'postpay' )->__ ( 'Invalid amount for capture.' ) );
		 * }
		 */
		return $this;
	}
	
	/**
	 * Void the payment through gateway
	 *
	 * @param Mage_Payment_Model_Info $payment        	
	 * @return Mage_Paygate_Model_Authorizenet
	 */
	public function void(Varien_Object $payment) {
		return $this;
	}
}
