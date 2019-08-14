<?php

/**
 * Represents the XML structure of an <uploadRequest> element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @subpackage	Dhl_MeinPaketCommon_Model_Xml_Partial
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_UploadRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	/**
	 *
	 * @var DOMNode
	 */
	protected $productDescriptions = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $offers = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $variantGroups = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $trackingNumbers = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $categories = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $deletions = null;
	
	/**
	 *
	 * @var Mage_Core_Helper_Data
	 */
	protected $coreHelper = null;
	
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Helper_Product
	 */
	protected $productHelper = null;
	
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Helper_Data
	 */
	protected $dataHelper = null;
	
	/**
	 * Default Constructor.
	 */
	public function __construct() {
		parent::__construct ();
		$this->productHelper = Mage::helper ( 'meinpaketcommon/product' );
		$this->dataHelper = Mage::helper ( 'meinpaketcommon/data' );
		$this->coreHelper = Mage::helper ( 'core/data' );
	}
	
	/**
	 * Create the root element for the document.
	 *
	 * @return DOMNode
	 */
	public function createDocumentElement() {
		$this->node = $this->getDocument ()->createElement ( 'uploadRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_PRODUCTS );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addProductDescription(Mage_Catalog_Model_Product $product) {
		switch ($product->getTypeId ()) {
			case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE :
				return $this->handleSimpleProduct ( $product );
			case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE :
				return $this->handleConfigurableProduct ( $product );
			default :
				Mage::log ( 'Unhandled typeId ' . $product->getTypeId () );
		}
	}
	
	/**
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return DOMElement
	 */
	public function addOffer(Mage_Catalog_Model_Product $product, Mage_Catalog_Model_Product $configurableProduct = null, $complete = true) {
		$offer = $this->getDocument ()->createElement ( 'productOffer' );
		
		if (! $product->getData ( 'sync_with_dhl_mein_paket' )) {
			$this->removeProduct ( $product );
			return false;
		}
		
		// product id
		$offer->appendChild ( $this->getDocument ()->createElement ( 'common:productId', $product->getId () ) );
		
		$meinPaketId = $product->getData ( 'meinpaket_id' );
		
		// price
		// Use final price to use timing
		$price = Mage::getStoreConfigFlag ( 'meinpaket/product_attributes/use_available_special_price' ) && $product->getFinalPrice () > 0 ? $product->getFinalPrice () : $product->getPrice ();
		$priceWithTax = Mage::helper ( 'tax' )->getPrice ( $product, $price );
		$offer->appendChild ( $this->getDocument ()->createElement ( 'price', $priceWithTax ) );
		
		if ($this->coreHelper->isModuleEnabled ( 'DerModPro_BasePrice' )) {
			$this->addDerModProBasePrice ( $offer, $product, $priceWithTax );
		}
		
		// tax group
		$taxGroup = $this->productHelper->getMeinPaketTaxGroup ( $product );
		$offer->appendChild ( $this->getDocument ()->createElement ( 'taxGroup', $taxGroup ) );
		
		// availability
		$availability = $this->productHelper->getMeinPaketStock ( $product );
		$offer->appendChild ( $this->getDocument ()->createElement ( 'availability', $availability ) );
		
		// abuse enddate for activation and deactivation
		// $offer->appendChild ( $this->getDocument ()->createElement ( 'endDate', $this->getIsoDateTime ( now () ) ) );
		
		// deliverytime
		$deliveryTime = $this->productHelper->getMeinPaketDeliveryTime ( $product );
		if ($deliveryTime !== false) {
			$offer->appendChild ( $this->getDocument ()->createElement ( 'deliverytime', $deliveryTime ) );
		}
		
		if (strlen ( $meinPaketId ) <= 0 && $complete) {
			/*
			 * // marketplace category
			 * $meinpaketCategory = $product->getData ( 'meinpaket_category' );
			 * if (strlen ( $meinpaketCategory ) > 0) {
			 * $mNode = $this->getDocument ()->createElement ( 'marketplaceCategory' );
			 * $mNode->setAttribute ( 'code', $meinpaketCategory );
			 * $offer->appendChild ( $mNode );
			 * } else {
			 * throw new Dhl_MeinPaketCommon_Model_Exception_MissingDataException ( $product->getId (), 'dhl_marketplace_category_id' );
			 * }
			 */
			
			if ($configurableProduct == null) {
				$type = Mage::getModel ( 'catalog/product_type_configurable' );
				$parentIdArray = $type->getParentIdsByChild ( $product->getId () );
				if (isset ( $parentIdArray [0] )) {
					$configurableProduct = Mage::getModel ( 'catalog/product' )->setStoreId ( $this->dataHelper->getMeinPaketStoreId () )->load ( $parentIdArray [0] );
				}
			}
			
			$this->exportAttributes ( $product, $offer, $this->productHelper->getConfigurableAttributes ( $configurableProduct ) );
		}
		
		$this->getOffers ()->appendChild ( $offer );
		$this->setHasData ();
		
		return $offer;
	}
	
	/**
	 * Remove given product.
	 *
	 * @param unknown $product        	
	 */
	public function removeProduct(Mage_Catalog_Model_Product $product) {
		if ($product->getData ( 'meinpaket_id' )) {
			/* @var $productDeletion DOMNode */
			$productDeletionNode = $this->getDocument ()->createElement ( 'productDeletion' );
			$this->getDeletions ()->appendChild ( $productDeletionNode );
			$productIdNode = $this->getDocument ()->createElement ( 'common:productId', $product->getId () );
			$productDeletionNode->appendChild ( $productIdNode );
			
			$this->setHasData ();
		}
	}
	
	/**
	 * Add category
	 * TODO:
	 *
	 * @param unknown $product        	
	 */
	public function addCategory(Mage_Catalog_Model_Product $product) {
	}
	
	/**
	 * Remove category
	 * TODO:
	 *
	 * @param unknown $product        	
	 */
	public function removeCategory(Mage_Catalog_Model_Product $product) {
	}
	
	/**
	 * Add baseprice using DerModPro_BasePrice
	 *
	 * @param DOMElement $offer
	 *        	to add baseprice to
	 * @param Mage_Catalog_Product $product
	 *        	to get baseprice for
	 * @param unknown $productPrice
	 *        	to use
	 */
	protected function addDerModProBasePrice(DOMElement $offer, Mage_Catalog_Model_Product $product, $productPrice) {
		if (! ($productAmount = $product->getBasePriceAmount ())) {
			return;
		}
		
		if (! ($referenceAmount = $product->getBasePriceBaseAmount ())) {
			return;
		}
		
		if (! ($productPrice)) {
			return;
		}
		
		if (! is_numeric ( $productAmount ) || ! is_numeric ( $referenceAmount ) || ! is_numeric ( $productPrice )) {
			return;
		}
		
		/* @var $basePriceHelper DerModePro_BasePrice_Helper_Data */
		$basePriceHelper = Mage::helper ( 'baseprice/data' );
		
		$productUnit = $product->getBasePriceUnit ();
		$referenceUnit = $product->getBasePriceBaseUnit ();
		$unit = null;
		
		switch ($referenceUnit) {
			case 'PCS' :
				$unit = 'per_piece';
				$referenceAmount = 1;
				break;
			case 'KG' :
			case 'LB' :
				$unit = 'per_1kg';
				$referenceAmount = 1;
				break;
			case 'G' :
				$unit = 'per_100g';
				$referenceAmount = 100;
				break;
			case 'L' :
				$unit = 'per_1l';
				$referenceAmount = 1;
				break;
			case 'ML' :
				$unit = 'per_100ml';
				$referenceAmount = 100;
				break;
			case 'MM' :
			case 'CM' :
			case 'IN' :
			case 'M' :
				$unit = 'per_1m';
				$referenceAmount = 1;
				break;
			case 'SQM' :
				$unit = 'per_1m2';
				$referenceAmount = 1;
				break;
			case 'CBM' :
				$unit = 'per_1m3';
				$referenceUnit = 1;
				break;
		}
		
		if (! $unit) {
			return;
		}
		
		/* @var $basePriceModel DerModePro_BasePrice_Model_BasePrice */
		$basePriceModel = Mage::getModel ( 'baseprice/baseprice', array (
				'reference_unit' => $referenceUnit,
				'reference_amount' => $referenceAmount 
		) );
		
		$unitPrice = round ( $basePriceModel->getBasePrice ( $productAmount, $productUnit, $productPrice ), 2 );
		
		$unitPriceElement = $this->getDocument ()->createElement ( 'unitprice', $unitPrice );
		$unitPriceElement->setAttribute ( 'unit', $unit );
		$offer->appendChild ( $unitPriceElement );
	}
	protected function getProductDescriptions() {
		if ($this->productDescriptions == null) {
			$this->productDescriptions = $this->getDocument ()->createElement ( 'descriptions' );
			$this->getDocumentElement ()->appendChild ( $this->productDescriptions );
		}
		return $this->productDescriptions;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getOffers() {
		if ($this->offers == null) {
			$this->offers = $this->getDocument ()->createElement ( 'offers' );
			$this->getDocumentElement ()->appendChild ( $this->offers );
		}
		return $this->offers;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getVariantGroups() {
		if ($this->variantGroups == null) {
			$this->variantGroups = $this->getDocument ()->createElement ( 'variantGroups' );
			$this->getDocumentElement ()->appendChild ( $this->variantGroups );
		}
		return $this->variantGroups;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getTrackingNumbers() {
		if ($this->trackingNumbers == null) {
			$this->trackingNumbers = $this->getDocument ()->createElement ( 'trackingNumbers' );
			$this->getDocumentElement ()->appendChild ( $this->trackingNumbers );
		}
		return $this->trackingNumbers;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getCategories() {
		if ($this->categories == null) {
			$this->categories = $this->getDocument ()->createElement ( 'categories' );
			$this->getDocumentElement ()->appendChild ( $this->categories );
		}
		return $this->categories;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getDeletions() {
		if ($this->deletions == null) {
			$this->deletions = $this->getDocument ()->createElement ( 'deletions' );
			$this->getDocumentElement ()->appendChild ( $this->deletions );
		}
		return $this->deletions;
	}
	
	/**
	 * Export a single simple product.
	 *
	 * @param Mage_Catalog_Model_Product $product
	 *        	to be exported
	 * @param string $variantGroup
	 *        	the product belongs to if available
	 * @throws Dhl_MeinPaketCommon_Model_Exception_InvalidDataException
	 * @throws Dhl_MeinPaketCommon_Model_Exception_MissingDataException
	 * @return DOMNode
	 */
	protected function handleSimpleProduct(Mage_Catalog_Model_Product $product, Mage_Catalog_Model_Product $configurableProduct = null) {
		if ($configurableProduct == NULL) {
			/* @var $type Mage_Catalog_Model_Product_Type_Configurable */
			$type = Mage::getModel ( 'catalog/product_type_configurable' );
			$parentIdArray = $type->getParentIdsByChild ( $product->getId () );
			
			if (is_array ( $parentIdArray )) {
				foreach ( $parentIdArray as $parentId ) {
					$aProduct = Mage::getModel ( 'catalog/product' )->setStoreId ( $this->dataHelper->getMeinPaketStoreId () )->load ( $parentId );
					
					// check resolved parent for configurable
					if ($aProduct->getTypeId () == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
						$configurableProduct = $aProduct;
						break;
					}
				}
			}
		}
		
		$configurableProductFallback = Mage::getStoreConfigFlag ( 'meinpaket/product_attributes/configurable_product_fallback' );
		$includeConfigurableProductImages = Mage::getStoreConfigFlag ( 'meinpaket/product_attributes/include_configurable_product_images' );
		
		$meinpaketCategory = $product->getData ( 'meinpaket_category' );
		if (empty ( $meinpaketCategory ) && $configurableProductFallback && $configurableProduct != null) {
			$meinpaketCategory = $configurableProduct->getData ( 'meinpaket_category' );
		}
		
		if (! $this->productHelper->isActive ( $product ) || empty ( $meinpaketCategory )) {
			$this->removeProduct ( $product );
			return false;
		}
		
		$variantInfo = null;
		
		/* @var $productDescription DOMNode */
		$productDescription = $this->getDocument ()->createElement ( 'productDescription' );
		
		// product id
		$productId = $this->getDocument ()->createElement ( 'common:productId', $product->getId () );
		$productDescription->appendChild ( $productId );
		
		// ean (optional)
		$ean = $this->productHelper->getEan ( $product );
		if (! empty ( $ean )) {
			$productDescription->appendChild ( $this->getDocument ()->createElement ( 'common:ean', $ean ) );
		}
		
		// manufacturer
		$manufacturer = $product->getAttributeText ( 'manufacturer' );
		if (! empty ( $manufacturer )) {
			$productDescription->appendChild ( $this->getCDATANode ( 'common:manufacturerName', $manufacturer ) );
		}
		
		$configurableAttributes = array ();
		
		if ($configurableProduct != NULL && ! empty ( $configurableAttributes )) {
			$configurableAttributes = $this->productHelper->getConfigurableAttributes ( $configurableProduct );
			$variantGroupInfoNode = $this->getDocument ()->createElement ( "variantGroupInfo" );
			$variantGroupInfoNode->setAttribute ( "code", $configurableProduct->getId () );
			$productDescription->appendChild ( $variantGroupInfoNode );
		}
		
		// name
		$name = $product->getName ();
		if (empty ( $name ) && $configurableProductFallback && $configurableProduct != null) {
			$shortDescription = $this->escapeStringForMeinPaket ( $configurableProduct->getName () );
		}
		
		if (! empty ( $name )) {
			$productDescription->appendChild ( $this->getCDATANode ( 'name', $product->getName () ) );
		} else {
			throw new Dhl_MeinPaketCommon_Model_Exception_InvalidDataException ( $product->getId (), 'name' );
		}
		
		// shortdescription
		$shortDescription = $this->escapeStringForMeinPaket ( $product->getShortDescription () );
		
		if (empty ( $shortDescription ) && $configurableProductFallback && $configurableProduct != null) {
			$shortDescription = $this->escapeStringForMeinPaket ( $configurableProduct->getShortDescription () );
		}
		
		if (! empty ( $shortDescription )) {
			$productDescription->appendChild ( $this->getCDATANode ( 'shortDescription', $shortDescription ) );
		} else {
			throw new Dhl_MeinPaketCommon_Model_Exception_MissingDataException ( $product->getId (), 'shortDescription' );
		}
		
		// long description (optional) && (strlen > 0)
		$description = $this->escapeStringForMeinPaket ( $product->getDescription () );
		if (empty ( $description ) && $configurableProductFallback && $configurableProduct != null) {
			$description = $this->escapeStringForMeinPaket ( $configurableProduct->getDescription () );
		}
		
		if (! empty ( $description )) {
			$productDescription->appendChild ( $this->getCDATANode ( 'longDescription', $description ) );
		}
		
		// image
		if (((! $this->exportImage ( $product, $productDescription ) && $configurableProductFallback) || $includeConfigurableProductImages) && $configurableProduct != null) {
			$this->exportImage ( $configurableProduct, $productDescription );
		}
		
		// marketplace category
		$mNode = $this->getDocument ()->createElement ( 'marketplaceCategory' );
		$mNode->setAttribute ( 'code', $meinpaketCategory );
		$productDescription->appendChild ( $mNode );
		
		$this->exportAttributes ( $product, $productDescription, $configurableAttributes );
		$this->getProductDescriptions ()->appendChild ( $productDescription );
		$this->addOffer ( $product, $configurableProduct, false );
		
		$this->setHasData ();
		
		return $productDescription;
	}
	
	/**
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @param unknown $productDescription        	
	 */
	protected function exportImage(Mage_Catalog_Model_Product $product, $productDescription) {
		// Export images
		$imagesExported = false;
		
		$images = $product->getMediaGalleryImages ();
		if ($images != null) {
			foreach ( $images as $image ) {
				$smallOrImage = false;
				
				if ($image->getDisabled ()) {
					continue;
				}
				
				$imageNode = $this->getDocument ()->createElement ( "image" );
				$productDescription->appendChild ( $imageNode );
				$imageNode->appendChild ( $this->getDocument ()->createElement ( "url", $image->getUrl () ) );
				
				$imagesExported = true;
				
				$label = $image->getLabel ();
				if (! empty ( $label )) {
					$imageNode->appendChild ( $this->getDocument ()->createElement ( "caption", $image->getLabel () ) );
				}
			}
		}
		
		return $imagesExported;
	}
	
	/**
	 * Export product attributes.
	 */
	protected function exportAttributes(Mage_Catalog_Model_Product $product, DOMNode $node, array $configurableAttributes = array()) {
		foreach ( $product->getAttributes () as $attribute ) {
			/* @var $attribute Mage_Eav_Model_Attribute */
			
			if (strlen ( $attribute->getMeinpaketAttribute () ) <= 0 || $attribute->getMeinpaketAttribute () == 'None') {
				continue;
			}
			
			$productValue = $product->getData ( $attribute->getAttributeCode () );
			
			if ($attribute->isValueEmpty ( $productValue ) || empty ( $productValue )) {
				continue;
			}
			
			$frontendValue = $attribute->getFrontend ()->getValue ( $product );
			
			if (empty ( $frontendValue )) {
				continue;
			}
			
			$storeLabel = $attribute->getFrontendLabel ();
			
			if (strlen ( $storeLabel ) <= 0) {
				continue;
			}
			
			$attributeNode = $this->getDocument ()->createElement ( "attribute" );
			if (in_array ( $attribute->getAttributeCode (), $configurableAttributes )) {
				$attributeNode->setAttribute ( "variant", "true" );
			}
			$attributeNode->setAttribute ( "code", $attribute->getMeinpaketAttribute () == 'Default' ? $attribute->getAttributeCode () : $attribute->getMeinpaketAttribute () );
			
			$nameNode = $this->getDocument ()->createElement ( "name", $storeLabel );
			$attributeNode->appendChild ( $nameNode );
			$valueNode = $this->getDocument ()->createElement ( "value", $frontendValue );
			$attributeNode->appendChild ( $valueNode );
			
			$node->appendChild ( $attributeNode );
		}
	}
	
	/**
	 * Export a configurable product.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 */
	protected function handleConfigurableProduct(Mage_Catalog_Model_Product $product) {
		if (! $this->productHelper->isActive ( $product )) {
			$this->removeProduct ( $product );
			return false;
		}
		
		if ($product->getTypeId () != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
			return false;
		}
		
		$typeInstance = $product->getTypeInstance ( true );
		$configurableType = $typeInstance->setProduct ( $product );
		$variantGroupNode = null;
		
		$variantGroupNode = $this->getDocument ()->createElement ( "variantGroup" );
		$variantGroupNode->setAttribute ( "code", $product->getId () );
		// $this->getVariantGroups ()->appendChild ( $variantGroupNode );
		
		/*
		 * $configurationNode = $this->getDocument ()->createElement ( "configuration" ); $variantGroupNode->appendChild ( $configurationNode ); $configurationNode->setAttribute ( "code", $variantMapping->getMeinpaketVariantId () );
		 */
		
		$titleNode = $this->getDocument ()->createElement ( "title" );
		$variantGroupNode->appendChild ( $titleNode );
		$titleNode->appendChild ( $this->getDocument ()->createTextNode ( $product->getName () ) );
		
		$this->setHasData ();
		
		$simpleCollection = $configurableType->getUsedProductCollection ()->addAttributeToSelect ( '*' )->addFilterByRequiredOptions ();
		foreach ( $simpleCollection as $simpleProduct ) {
			$this->handleSimpleProduct ( $simpleProduct, $product );
		}
		
		return $variantGroupNode;
	}
}
