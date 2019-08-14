<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_UploadResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $warnings = array ();
	private $categoryDeletionConfirmations = array ();
	private $categoryConfirmations = array ();
	private $productDeletionConfirmations = array ();
	private $productDescriptionConfirmations = array ();
	private $productOfferConfirmations = array ();
	private $variantGroupConfirmations = array ();
	
	/**
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'uploadResponse' );
		
		foreach ( $domElement->childNodes as $uploadResponseEntries ) {
			switch ($uploadResponseEntries->localName) {
				case 'confirmation' :
					foreach ( $uploadResponseEntries->childNodes as $confirmationEntries ) {
						switch ($confirmationEntries->localName) {
							case 'categoryDeletion' :
								$this->categoryDeletionConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_CategoryId ( $confirmationEntries );
								break;
							case 'category' :
								$this->categoryConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_CategoryId ( $confirmationEntries );
								break;
							case 'productDeletion' :
								$this->productDeletionConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId ( $confirmationEntries );
								break;
							case 'productDescription' :
								$this->productDescriptionConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId ( $confirmationEntries );
								break;
							case 'productOffer' :
								$this->productOfferConfirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId ( $confirmationEntries );
								break;
							case 'variantGroup' :
								break;
						}
					}
					break;
				case 'warning' :
					$warnings [] = $uploadResponseEntries->nodeValue;
					break;
			}
		}
	}
	
	/**
	 */
	public function storeMeinPaketIds() {
		foreach ( $this->productDescriptionConfirmations as $conf ) {
			/* @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel ( 'catalog/product' )->load ( $conf->getProductId () );
			if ($product->getData ( 'meinpaket_id' ) != $conf->getMeinPaketId ()) {
				$product->setData ( 'meinpaket_id', $conf->getMeinPaketId () );
				$product->getResource ()->saveAttribute ( $product, 'meinpaket_id' );
			}
		}
		
		foreach ( $this->productDeletionConfirmations as $conf ) {
			/* @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel ( 'catalog/product' )->load ( $conf->getProductId () );
			if ($product->getData ( 'meinpaket_id' ) != $conf->getMeinPaketId ()) {
				$product->setData ( 'meinpaket_id', $conf->getMeinPaketId () );
				$product->getResource ()->saveAttribute ( $product, 'meinpaket_id' );
			}
		}
	}
	
	/**
	 */
	public function deleteMeinPaketIds() {
		/* @var $conf Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId */
		foreach ( $this->productDeletionConfirmations as $conf ) {
			/* @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel ( 'catalog/product' )->load ( $conf->getProductId () );
			$product->setData ( 'meinpaket_id', null );
			$product->getResource ()->saveAttribute ( $product, 'meinpaket_id' );
		}
	}
	
	/**
	 * Process response
	 */
	public function process() {
		$this->deleteMeinPaketIds ();
		$this->storeMeinPaketIds ();
	}
}
