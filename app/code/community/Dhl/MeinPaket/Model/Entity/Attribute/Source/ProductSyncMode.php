<?php

/**
 * Status flags for product sync.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Source_ProductSyncMode extends Mage_Eav_Model_Entity_Attribute_Abstract {
	/**
	 * Disabled.
	 *
	 * @var unknown
	 */
	const DISABLED = 0;
	/**
	 * Offer.
	 *
	 * @var unknown
	 */
	const OFFER = 1;
	/**
	 * Complete.
	 *
	 * @var unknown
	 */
	const COMPLETE = 2;
	
	/**
	 * Options
	 *
	 * @var array
	 */
	protected $options;
	
	/**
	 * Default constructor.
	 */
	public function __construct() {
		$this->options = array (
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Disabled' ),
						'value' => self::DISABLED 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Offer' ),
						'value' => self::OFFER 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Product Data + Offer' ),
						'value' => self::COMPLETE 
				) 
		);
	}
	
	/**
	 * Get array of options.
	 *
	 * @param string $addEmpty        	
	 * @return multitype:
	 */
	public function toOptionArray($addEmpty = true) {
		return $this->options;
	}
	
	/**
	 * Get array of options.
	 *
	 * @param string $addEmpty        	
	 * @return multitype:
	 */
	public function getAllOptions() {
		return $this->options;
	}
	
	/**
	 * To select array.
	 *
	 * @return multitype:unknown
	 */
	public function toSelectArray() {
		$result = array ();
		
		foreach ( $this->options as $option ) {
			$result [$option ['value']] = $option ['label'];
		}
		
		return $result;
	}
}
