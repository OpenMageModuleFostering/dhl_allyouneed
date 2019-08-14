<?php

/**
 * Shipping model which handles the import of orders from DHL MeinPaket.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model
 */
class Dhl_MeinPaketCommon_Model_Carrier_Allyouneed extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
	/**
	 *
	 * @var string
	 */
	protected $_code = 'allyouneed';
	
	/**
	 *
	 * @var float
	 */
	protected static $_deliveryCosts = 0.0;
	
	/**
	 * Tells if the carrier is locked for use.
	 *
	 * @var boolean
	 */
	protected static $_isLocked = true;
	
	/**
	 * Locks the carrier for use.
	 *
	 * @return void
	 */
	public static function lock() {
		self::$_isLocked = true;
	}
	
	/**
	 * Unlocks the carrier for use.
	 *
	 * @return void
	 */
	public static function unlock() {
		self::$_isLocked = false;
	}
	
	/**
	 * Returns the allowed methods for this carrier.
	 *
	 * @see Mage_Shipping_Model_Carrier_Interface::getAllowedMethods()
	 * @return array
	 */
	public function getAllowedMethods() {
		return array (
				'standard' => 'Allyouneed',
				'method1' => 'Allyouneed' 
		);
	}
	
	/**
	 * Tells if tracking of this shipment is available.
	 *
	 * @see Mage_Shipping_Model_Carrier_Abstract::isTrackingAvailable()
	 * @return boolean Will always return false
	 */
	public function isTrackingAvailable() {
		return false;
	}
	
	/**
	 * Collect the rates.
	 *
	 * @see Mage_Shipping_Model_Carrier_Abstract::collectRates()
	 * @param Mage_Shipping_Model_Rate_Request $request        	
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		// avoid usage as a general carrier method
		if (self::$_isLocked) {
			return false;
		}
		
		/* @var $result Mage_Shipping_Model_Rate_Result */
		$result = Mage::getModel ( 'shipping/rate_result' );
		
		/* @var $method Mage_Shipping_Model_Rate_Result_Method */
		$method = Mage::getModel ( 'shipping/rate_result_method' );
		
		$method->setCarrier ( $this->_code );
		$method->setCarrierTitle ( $this->getConfigData ( 'title' ) );
		$method->setMethod ( 'method' );
		$method->setMethodTitle ( 'Allyouneed' );
		
		$method->setPrice ( self::$_deliveryCosts );
		
		$result->append ( $method );
		
		return $result;
	}
	
	/**
	 * Sets the total delivery costs
	 *
	 * @param float $deliveryCosts        	
	 * @return void
	 */
	public static function setDeliveryCosts($deliveryCosts) {
		self::$_deliveryCosts = ( float ) $deliveryCosts;
	}
}
