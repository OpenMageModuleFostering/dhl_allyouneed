<?php

/**
 * Block for the import step of the category structure import.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Block_Adminhtml_CategoryImport
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_CategoryImport_Import extends Mage_Adminhtml_Block_Template {
	/**
	 *
	 * @var Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result
	 */
	protected $result = null;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Sets the result object which encapsulates the information about the import process.
	 *
	 * @param Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result $result        	
	 * @return void
	 */
	public function setResult(Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result $result) {
		$this->result = $result;
	}
	
	/**
	 *
	 * @return Dhl_MeinPaket_Model_Service_MarketplaceCategoryImport_Result
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Returns an "<a>"-Tag which is a backend link to the given category.
	 *
	 * @param Mage_Catalog_Model_Category $category        	
	 * @return string
	 */
	public function getLinkToCategory(Mage_Catalog_Model_Category $category) {
		return '<a href="' . $this->getUrl ( 'adminhtml/catalog_category/edit/', array (
				'id' => $category->getId () 
		) ) . '" target="_blank">' . $this->__ ( 'edit category' ) . '</a>';
	}
}
