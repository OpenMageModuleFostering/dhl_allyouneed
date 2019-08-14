<?php

/**
 * MeinPaket payment method for imported orders.
 * 
 * @category	Mage
 * @package		Dhl_MeinPaket
 * @subpackage	Payment_Method
 */
class Dhl_MeinPaketCommon_Model_Payment_Method_Meinpaket extends Mage_Payment_Model_Method_Abstract {
	/**
	 *
	 * @var string
	 */
	protected $_code = 'meinpaket';
	
	/**
	 * Can use this payment method in administration panel?
	 * @var boolean
	 */
	protected $_canUseInternal = false;
	
	/**
	 * Can show this payment method as an option on checkout payment page?
	 * @var boolean
	 */
	protected $_canUseCheckout = false;
	
}
