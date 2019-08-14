<?php
class Dhl_MeinPaket_Helper_Backlog extends Mage_Core_Helper_Abstract {
	
	/**
	 * Create backlogs for every parent of given product
	 *
	 * @param int $productId
	 *        	to create backlog for
	 * @return integer count
	 */
	public function createParentBacklog($productId, $changes = '') {
		$count = 0;
		
		if ($productId) {
			foreach ( $this->getParentIds ( $productId ) as $productId ) {
				$this->createBacklog ( $productId, $changes );
				$count ++;
			}
		}
		
		return $count;
	}
	
	/**
	 * Create backlogs for every children product
	 *
	 * @param int $productId
	 *        	to create backlog for
	 * @return integer count
	 */
	public function createChildrenBacklog($productId, $changes = '') {
		$count = 0;
		
		if ($productId) {
			$childIds = Mage::getModel ( 'catalog/product_type_configurable' )->getChildrenIds ( $productId );
			
			foreach ( $childIds [0] as $key => $val ) {
				$this->createBacklog ( $val, $changes );
				$count ++;
			}
		}
		
		return $count;
	}
	
	/**
	 * Create a backlog for given product using changes.
	 *
	 * @param int $productId
	 *        	to create backlog for.
	 * @param string $changes
	 *        	to set
	 */
	public function createBacklog($productId, $changes = '') {
		if ($productId) {
			$backlog = Mage::getModel ( 'meinpaket/backlog_product' );
			$backlog->product_id = $productId;
			$backlog->created_at = time ();
			$backlog->changes = $changes;
			$backlog->save ();
		}
	}
	
	/**
	 * Get all parent ids for a single product given by $productId.
	 *
	 * @param int $productId
	 *        	to search for
	 * @return array
	 */
	public function getParentIds($productId) {
		$parentIdsGrouped = Mage::getModel ( 'catalog/product_type_grouped' )->getParentIdsByChild ( $productId );
		$parentIdsConfigurable = Mage::getModel ( 'catalog/product_type_configurable' )->getParentIdsByChild ( $productId );
		return array_unique ( array_merge ( $parentIdsGrouped, $parentIdsConfigurable ) );
	}
}
