<?php

/**
 * Postpay payment method for imported orders.
 * 
 * @category	Mage
 * @package		Dhl_Postpay
 * @subpackage	Payment_Method
 */
class Dhl_Postpay_Model_Payment_Method_Express extends Mage_Payment_Model_Method_Abstract {
	/**
	 *
	 * @var string
	 */
	protected $_code = 'postpay_express';
	
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
	protected $_canUseCheckout = false;
	
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
}
