<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_DownloadResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $categories = array ();
	private $productOffers = array ();
	private $variantConfigurations = array ();
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'downloadResponse' );
		
		foreach ( $domElement->childNodes as $downloadResponseEntries ) {
			switch ($downloadResponseEntries->localName) {
				case 'category' :
					$this->categories [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Category ( $downloadResponseEntries );
					break;
				case 'productOffers' :
					// $this->productOfferConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId ( $downloadResponseEntries );
					break;
				case 'variantConfiguration' :
					$this->variantConfigurations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_VariantConfiguration ( $downloadResponseEntries );
					break;
			}
		}
	}
	
	/**
	 *
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}
	/**
	 *
	 * @return array
	 */
	public function getProductOffers() {
		return $this->productOffers;
	}
	/**
	 *
	 * @return array
	 */
	public function getVariantConfigurations() {
		return $this->variantConfigurations;
	}
}
