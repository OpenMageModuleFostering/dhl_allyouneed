<?php

/**
 * Result class which keeps information about a category import process..
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_MarketplaceCategoryImport
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result extends Dhl_MeinPaketCommon_Model_Service_Result_Abstract {
	/**
	 *
	 * @var array
	 */
	protected $newCategories = array ();
	
	/**
	 *
	 * @var array
	 */
	protected $deletedCategories = array ();
	
	/**
	 *
	 * @var array
	 */
	protected $updatedCategories = array ();
	
	/**
	 * Adds a category which was not there before.
	 *
	 * @param integer $categoryId        	
	 * @return Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_ImportService
	 */
	public function addNewCategory(Dhl_MeinPaket_Model_Category $category) {
		$this->newCategories [] = $category;
		return $this;
	}
	
	/**
	 * Adds a category which has been deleted because it is not existent in the
	 * MeinPaket marketplace anymore.
	 *
	 * @param string $name        	
	 * @return Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_ImportService
	 */
	public function addDeletedCategory(Dhl_MeinPaket_Model_Category $category) {
		$this->deletedCategories [] = $category;
		return $this;
	}
	
	/**
	 * Adds a category which has been renamed.
	 *
	 * @param Dhl_MeinPaket_Model_Category $category        	
	 * @return Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_ImportService
	 */
	public function addUpdatedCategory(Dhl_MeinPaket_Model_Category $category) {
		$this->updatedCategories[] = $category;
		return $this;
	}
	
	/**
	 * Returns the ids of the categories which have not been existent in Magento yet.
	 *
	 * @return array
	 */
	public function getNewCategories() {
		return $this->newCategories;
	}
	
	/**
	 * Returns the names of the categories which have been deleted
	 * because they were existent in Magento but don't exist
	 * in Allyouneed marketplace category structure anymore.
	 *
	 * @return array
	 */
	public function getDeletedCategories() {
		return $this->deletedCategories;
	}
	
	/**
	 * Returns an array of arrays which represent the categories which
	 * have been renamed.
	 *
	 * @return array array has the following structure:
	 *         [
	 *         [
	 *         'id',		// entity id of the category
	 *         'oldName'	// The former name of the category
	 *         ],
	 *         ...
	 *         ]
	 */
	public function getUpdatedCategories() {
		return $this->updatedCategories;
	}
	
	/**
	 * Returns the number of categories that have been added.
	 *
	 * @return integer
	 */
	public function getNewCategoriesCount() {
		return sizeof ( $this->newCategories );
	}
	
	/**
	 * Returns the number of categories that have been deleted.
	 *
	 * @return integer
	 */
	public function getDeletedCategoriesCount() {
		return sizeof ( $this->deletedCategories );
	}
	
	/**
	 * Returns the number of categories that have been renamed.
	 *
	 * @return integer
	 */
	public function getUpdatedCategoriesCount() {
		return sizeof ( $this->updatedCategories );
	}
}

