<?php

/**
 * Partial which represents the 'downloadRequest' element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_DownloadRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	/**
	 * Default Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Create the root element for the document.
	 *
	 * @return DOMNode
	 */
	public function createDocumentElement() {
		$this->node = $this->getDocument ()->createElement ( 'downloadRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_PRODUCTS );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}	
	
	/**
	 * Creates the request XML for a category structure download request.
	 */
	public function addDownloadMarketplaceCategories() {
		$this->getDocumentElement ()->appendChild ( $this->getDocument ()->createElement ( 'catalogstructure', 'marketplace' ) );
	}
	public function addDownloadOffers() {
		$this->getDocumentElement ()->appendChild ( $this->getDocument ()->createElement ( 'getProductOffers' ) );
	}
	public function addVariantConfigurations() {
		$this->getDocumentElement ()->appendChild ( $this->getDocument ()->createElement ( 'variantConfigurations' ) );
	}
	public function addInternationalPrices() {
		$this->getDocumentElement ()->appendChild ( $this->getDocument ()->createElement ( 'getInternationalPrices' ) );
	}
}
