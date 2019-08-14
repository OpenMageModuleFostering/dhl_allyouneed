<?php
class Dhl_MeinPaket_Helper_Backlog extends Mage_Core_Helper_Abstract {
	
	/**
	 * Create backlogs for every parent of given product
	 *
	 * @param int $productId
	 *        	to create backlog for
	 * @return integer count
	 */
	public function createParentBacklog($productId, $changes = '', $requestDescriptionUpload = false) {
		$count = 0;
		
		if ($productId) {
			$id = is_object ( $productId ) ? $productId->getId () : $productId;
			
			foreach ( $this->getParentIds ( $id ) as $productId ) {
				$this->createBacklog ( $productId, $changes, $requestDescriptionUpload );
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
	public function createChildrenBacklog($productId, $changes = '', $requestDescriptionUpload = false) {
		$count = 0;
		
		if ($productId) {
			$id = is_object ( $productId ) ? $productId->getId () : $productId;
			
			$childIds = Mage::getModel ( 'catalog/product_type_configurable' )->getChildrenIds ( $id );
			
			foreach ( $childIds [0] as $key => $val ) {
				$this->createBacklog ( $val, $changes, $requestDescriptionUpload );
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
	public function createBacklog($productId, $changes = '', $requestDescriptionUpload = false) {
		if ($productId) {
			$id = is_object ( $productId ) ? $productId->getId () : $productId;
			
			$backlog = Mage::getModel ( 'meinpaket/backlog_product' );
			$backlog->product_id = $id;
			$backlog->created_at = time ();
			$backlog->changes = $changes;
			$backlog->request_description_upload = $requestDescriptionUpload;
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
		$id = is_object ( $productId ) ? $productId->getId () : $productId;
		
		$parentIdsGrouped = Mage::getModel ( 'catalog/product_type_grouped' )->getParentIdsByChild ( $id );
		$parentIdsConfigurable = Mage::getModel ( 'catalog/product_type_configurable' )->getParentIdsByChild ( $id );
		return array_unique ( array_merge ( $parentIdsGrouped, $parentIdsConfigurable ) );
	}
}
