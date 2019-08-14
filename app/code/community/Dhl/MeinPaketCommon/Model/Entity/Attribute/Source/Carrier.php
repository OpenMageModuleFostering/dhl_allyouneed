<?php

/**
 * Attribute source model for shipment methods based on the available carriers.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Entity_Attribute_Source_Carrier extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
	/**
	 * Returns the available shipment methods.
	 *
	 * @return array
	 */
	protected function getActiveShipmentCarriers() {
		$carriersArray = array ();
		
		/* @var $shippingConfig Mage_Shipping_Model_Config */
		$shippingConfig = Mage::getModel ( 'shipping/config' );
		
		$activeCarriers = $shippingConfig->getActiveCarriers ();
		
		if (sizeof ( $activeCarriers ) > 0) {
			
			foreach ( $activeCarriers as $carrier ) {
				$code = $carrier->getCarrierCode ();
				$carrierMethods = $carrier->getAllowedMethods ();
				$carrierTitle = Mage::getStoreConfig ( 'carriers/' . $code . '/title' );
				
				foreach ( $carrierMethods as $methodCode => $methodTitle ) {
					$carriersArray [$code . '_' . $methodCode] = $carrierTitle . ' - ' . $methodTitle;
				}
			}
		}
		
		return $carriersArray;
	}
	
	/**
	 * Returns the available shipment methods.
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return $this->getActiveShipmentCarriers ();
	}
	
	/**
	 * Retrieve All options
	 *
	 * @return array
	 */
	public function getAllOptions() {
		if (is_null ( $this->_options )) {
			
			/* @var $shippingConfig Mage_Shipping_Model_Config */
			$shippingConfig = Mage::getModel ( 'shipping/config' );
			
			$activeCarriers = $shippingConfig->getActiveCarriers ();
			
			if (sizeof ( $activeCarriers ) > 0) {
				foreach ( $activeCarriers as $carrier ) {
					$code = $carrier->getCarrierCode ();
					$carrierMethods = $carrier->getAllowedMethods ();
					$carrierTitle = Mage::getStoreConfig ( 'carriers/' . $code . '/title' );
					
					foreach ( $carrierMethods as $methodCode => $methodTitle ) {
						$this->_options = array (
								'label' => $carrierTitle . ' - ' . $methodTitle,
								'value' => $code . '_' . $methodCode 
						);
					}
				}
			}
		}
		
		return $this->_options;
	}
}

