<?php

/**
 * Block for the export step of the category structure import.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Block_Adminhtml_ProductExport
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_ProductExport_Export extends Mage_Adminhtml_Block_Template {
	/**
	 *
	 * @var Dhl_MeinPaket_Model_Service_Product_Export_Result
	 */
	protected $results = array ();
	
	/**
	 *
	 * @var integer
	 */
	private $overallCount;
	
	/**
	 *
	 * @var integer
	 */
	private $sucessfullCount;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct ();
		$this->assign ( 'startLabel', 'Start again' );
	}
	
	/**
	 * Sets the result object which encapsulates information about the export process.
	 *
	 * @param Dhl_MeinPaket_Model_Service_Product_Export_Result $results        	
	 * @return void
	 */
	public function setResults($results) {
		$this->results = $results;
	}
	
	/**
	 * Returns an <a>-Tag which links to the given product in the Magento backend.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return string
	 */
	protected function getEditLink(Mage_Catalog_Model_Product $product) {
		return '<a href="' . $this->getUrl ( 'adminhtml/catalog_product/edit/', array (
				'id' => $product->getId () 
		) ) . '" target="_blank">' . $this->__ ( 'edit product' ) . '</a>';
	}
	
	/**
	 * Returns an translated error description.
	 *
	 * @param string $errorType        	
	 * @param string $errorCode        	
	 * @return string
	 */
	protected function getErrorDescription($errorType, $errorCode = '') {
		return Mage::helper ( 'meinpaket/product' )->getErrorDescription ( $errorType, $errorCode );
	}
}
