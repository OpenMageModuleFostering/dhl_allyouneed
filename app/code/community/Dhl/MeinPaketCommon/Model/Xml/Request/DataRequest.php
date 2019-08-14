<?php

/**
 * Represents the XML structure of an <productDataRequest> element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @subpackage	Dhl_MeinPaketCommon_Model_Xml_Partial
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_DataRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Helper_Product
	 */
	protected $productHelper = null;
	protected $productDataRequest = null;
	protected $bestPriceRequest = null;
	
	/**
	 * Default Constructor.
	 */
	public function __construct() {
		parent::__construct ();
		$this->productHelper = Mage::helper ( 'meinpaket/product' );
	}
	
	/**
	 * Create the root element for the document.
	 *
	 * @return DOMNode
	 */
	public function createDocumentElement() {
		$this->node = $this->getDocument ()->createElement ( 'dataRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_DATA );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @param boolean $sendEAN
	 *        	send ean value
	 * @param boolean $sendName
	 *        	send name value
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addProduct(Mage_Catalog_Model_Product $product, $sendEAN = false, $sendName = false) {
		$productNode = $this->getDocument ()->createElement ( 'product' );
		$this->getProductDataRequest ()->appendChild ( $productNode );
		$productNode->setAttribute ( 'entryId', $product->getId () );
		
		$ean = $this->productHelper->getEan ( $product );
		
		if ($sendEAN) {
			$eanNode = $this->getDocument ()->createElement ( 'ean', $ean );
			$productNode->appendChild ( $eanNode );
		}
		
		if ($sendName) {
			$nameNode = $this->getDocument ()->createElement ( 'productName', $product->getName () );
			$productNode->appendChild ( $nameNode );
		}
		
		return $this;
	}
	
	/**
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @param boolean $sendEAN
	 *        	send ean value
	 * @param boolean $sendName
	 *        	send name value
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addBestPriceProduct(Mage_Catalog_Model_Product $product) {
		$productNode = $this->getDocument ()->createElement ( 'productId', $product->getId () );
		$this->getBestPriceRequest ()->appendChild ( $productNode );
		return $this;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getProductDataRequest() {
		if ($this->productDataRequest == null) {
			$this->productDataRequest = $this->getDocument ()->createElement ( 'productDataRequest' );
			$this->getDocumentElement ()->appendChild ( $this->productDataRequest );
		}
		return $this->productDataRequest;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getBestPriceRequest() {
		if ($this->bestPriceRequest == null) {
			$this->bestPriceRequest = $this->getDocument ()->createElement ( 'bestPriceRequest' );
			$this->getDocumentElement ()->appendChild ( $this->bestPriceRequest );
		}
		return $this->bestPriceRequest;
	}
}
