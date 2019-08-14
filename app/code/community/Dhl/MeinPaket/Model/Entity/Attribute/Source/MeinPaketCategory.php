<?php

/**
 * Attribute source model for shipment methods based on the available carriers.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Entity_Attribute_Source
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Entity_Attribute_Source_MeinPaketCategory extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
	/**
	 * Cache key for list
	 * 
	 * @var unknown
	 */
	const CACHE_KEY = 'meinpaket_categories';
	
	/**
	 * Returns the MeinPaket categories.
	 *
	 * @return array
	 */
	protected function getMeinPaketCategories() {
		$cache = Mage::app ()->getCache ();
		
		//$this->cleanCache ();
		
		$categories = $cache->load ( self::CACHE_KEY );
		
		if (! $categories) {
			$categories = array ();
			$categoryCollection = Mage::getModel ( 'meinpaket/category' )->getCollection ();
			$categoryCollection->setOrder ( 'code', 'ASC' );
			
			$currentParents = array ();
			
			foreach ( $categoryCollection as $cat ) {
				// Fill lookup table
				$currentParents [$cat->getCode ()] = array (
						'label' => $cat->getName (),
						'value' => $cat->getCode (),
						'code' => $cat->getCode (),
						'parent' => $cat->getParent () 
				);
			}
			
			foreach ( $currentParents as $key => &$value ) {
				
				if (strlen ( $value ['parent'] ) <= 0) {
					// This category does not have a parent. add it as default.
					$categories [] = &$value;
				} else if (array_key_exists ( $value ['parent'], $currentParents )) {
					// We already have a parent. add it.
					if (! is_array ( $currentParents [$value ['parent']] ['value'] )) {
						$currentParents [$value ['parent']] ['value'] = array ();
					}
					$currentParents [$value ['parent']] ['value'] [] = &$value;
				} else {
					$categories [] = &$value;
				}
				unset ( $value ['parent'] );
			}
			
			if (count ( $categories ) > 0) {
				$cache->save ( serialize ( $categories ), self::CACHE_KEY );
			}
		} else {
			$categories = unserialize ( $categories );
		}
		
		return $categories;
	}
	
	/**
	 * Returns the Dhl MeinPaket categories.
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return $this->getMeinPaketCategories ();
	}
	public function getAllOptions() {
		return $this->getMeinPaketCategories ();
	}
	public function cleanCache() {
		Mage::app ()->getCache ()->remove ( self::CACHE_KEY );
	}
}

