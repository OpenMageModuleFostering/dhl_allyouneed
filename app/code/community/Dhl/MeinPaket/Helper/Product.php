<?php
class Dhl_MeinPaket_Helper_Product extends Mage_Core_Helper_Abstract {
	/**
	 *
	 * @var string
	 */
	const TAX_CLASS_STANDARD = 'Standard';
	
	/**
	 *
	 * @var string
	 */
	const TAX_CLASS_REDUCED = 'Reduced';
	
	/**
	 *
	 * @var string
	 */
	const TAX_CLASS_FREE = 'Free';
	
	/**
	 *
	 * @var Dhl_MeinPaket_Model_Validation_Validator_Ean
	 */
	protected $_eanValidator = null;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_eanValidator = Mage::getSingleton ( 'meinpaketcommon/validation_validator_ean' );
	}
	
	/**
	 * Return true if the product should be listed.
	 * 
	 * @param Mage_Catalog_Model_Product $product to be checked
	 * @return boolean true if the product should be listed
	 */
	public function isActive(Mage_Catalog_Model_Product $product) {
		return $product->getData ( 'sync_with_dhl_mein_paket' ) > 0;
	}
	
	/**
	 * Tells if the given product has a valid ean.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return boolean
	 */
	public function hasValidEan(Mage_Catalog_Model_Product $product) {
		return $this->_eanValidator->isValid ( $this->getEan ( $product ) );
	}
	
	/**
	 * Returns the EAN of the given product.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return string
	 */
	public function getEan(Mage_Catalog_Model_Product $product) {
		$eanAttributeCode = Mage::getStoreConfig ( 'meinpaket/product_attributes/ean_attribute' );
		
		if (! empty ( $eanAttributeCode ) && $product->hasData ( $eanAttributeCode ) && $this->_eanValidator->isValid ( $product->getData ( $eanAttributeCode ) )) {
			return $product->getData ( $eanAttributeCode );
		} else {
			return null;
		}
	}
	
	/**
	 * Returns the images for the given product.
	 * The items of the returned array are arrays themselves and have the
	 * following structure:
	 * array(
	 * 'url' => 'the url of the image',
	 * 'caption' => 'the images description'
	 * )
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return array
	 */
	public function getImages(Mage_Catalog_Model_Product $product) {
		$images = array ();
		$galleryImages = Mage::getModel ( 'catalog/product' )->load ( $product->getId () )->getMediaGalleryImages ();
		$imageUrl = '';
		$imageCaption = '';
		
		if (is_object ( $galleryImages ) && $galleryImages->count () > 0) {
			foreach ( $galleryImages as $image ) {
				$images [] = array (
						'url' => Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA ) . 'catalog/product' . $image ['file'],
						'caption' => $image ['label'] 
				);
			}
		}
		
		return $images;
	}
	
	/**
	 * Returns the MeinPaket marketplace category code which is assigned to the
	 * product.
	 * If no marketplace category is assigned, an empty string will be
	 * returned.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return string
	 */
	public function getMeinPaketMarketplaceCategoryCode(Mage_Catalog_Model_Product $product) {
		$category = $this->getFirstMarketplaceCategory ( $product );
		
		return (is_object ( $category ) && get_class ( $category ) === 'Mage_Catalog_Model_Category') ? $category->getDhlMarketplaceCategoryId () : '';
	}
	
	/**
	 * Returns all marketplace categories the product exists in.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
	 */
	public function getMarketplaceCategories(Mage_Catalog_Model_Product $product) {
		return $product->getCategoryCollection ()->addAttributeToSelect ( 'dhl_marketplace_category_id' )->addAttributeToFilter ( 'dhl_marketplace_category_id', array (
				'notnull' => 1 
		) )->load ();
	}
	
	/**
	 * Returns the first marketplace category for the given product.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return Mage_Catalog_Model_Category
	 */
	public function getFirstMarketplaceCategory(Mage_Catalog_Model_Product $product) {
		/* @var $category Mage_Catalog_Model_Category */
		$category = null;
		
		$collection = $this->getMarketplaceCategories ( $product );
		
		if ($collection->count () > 0) {
			$category = $collection->getFirstItem ();
		}
		
		return $category;
	}
	
	/**
	 * Returns the price of the given product depending on tax settings.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return float
	 */
	public function getPriceConsideringTaxes(Mage_Catalog_Model_Product $product) {
		/* @var $taxHelper Mage_Tax_Helper_Data */
		$taxHelper = Mage::helper ( 'tax' );
		$priceIncludesTax = ( bool ) Mage::getStoreConfig ( 'tax/calculation/price_includes_tax' ) ? true : null;
		
		return $taxHelper->getPrice ( $product, $product->getPrice (), $priceIncludesTax );
	}
	
	/**
	 * Returns the MeinPaket tax group of the product.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return string May be "Free", "Reduced" or "Standard".
	 */
	public function getMeinPaketTaxGroup(Mage_Catalog_Model_Product $product) {
		$taxGroup = self::TAX_CLASS_STANDARD;
		
		if ($product->hasData ( 'tax_class_id' )) {
			$taxGroups = array (
					'0' => self::TAX_CLASS_FREE,
					Mage::getStoreConfig ( 'meinpaket/taxrates/default_tax_rate' ) => self::TAX_CLASS_STANDARD,
					Mage::getStoreConfig ( 'meinpaket/taxrates/reduced_tax_rate' ) => self::TAX_CLASS_REDUCED 
			);
			if (array_key_exists ( $product->getTaxClassId (), $taxGroups )) {
				$taxGroup = $taxGroups [$product->getTaxClassId ()];
			}
		}
		
		return $taxGroup;
	}
	
	/**
	 * Returns the stock count of the product which can be used for MeinPaket.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return integer
	 */
	public function getMeinPaketStock(Mage_Catalog_Model_Product $product) {
		$customDhlMeinPaketStock = 0;
		$hasCustomMeinPaketStock = false;
		$defaultStock = 0;
		$stock = 0;
		
		if ($product->hasData ( 'max_stock_for_dhl_mein_paket' )) {
			$hasCustomMeinPaketStock = (strlen ( $product->getMaxStockForDhlMeinPaket () ) > 0);
			$customDhlMeinPaketStock = ( integer ) $product->getMaxStockForDhlMeinPaket ();
		}
		
		/* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
		$stockItem = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $product->getId () );
		
		if (! $stockItem->getIsInStock ()) {
			return 0;
		}
		
		$defaultStock = ( integer ) $stockItem->getQty ();
		
		if ($hasCustomMeinPaketStock && is_integer ( $customDhlMeinPaketStock ) && $customDhlMeinPaketStock >= 0) {
			$stock = $customDhlMeinPaketStock;
			if ($stock > $defaultStock) {
				$stock = $defaultStock;
			}
		} else {
			$stock = $defaultStock;
		}
		
		return $stock;
	}
	
	/**
	 * Returns a date from which on the product won't be available on MeinPaket
	 * anymore.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return Zend_Date
	 */
	public function getMeinPaketEndDate(Mage_Catalog_Model_Product $product) {
		$endDate = new Zend_Date ();
		
		if ($product->getStatus () == Mage_Catalog_Model_Product_Status::STATUS_DISABLED/* || 
			$product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE*/
		) {
			$endDate->setTimestamp ( time () - 86400 );
		} else {
			$endDate->setTimestamp ( time () + 31536000 );
		}
		
		return $endDate;
	}
	
	/**
	 * Returns the delivery time for orders from MeinPaket for the given product.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @return integer The delivery time in days
	 */
	public function getMeinPaketDeliveryTime(Mage_Catalog_Model_Product $product) {
		$attributeId = Mage::getStoreConfig ( 'meinpaket/product_attributes/delivery_time' );
		$deliveryTime = Mage::getStoreConfig ( 'meinpaket/product_attributes/default_delivery_time' );
		
		if (strlen ( $attributeId ) > 0 && $product->hasData ( $attributeId ) && @preg_match ( "/^[0-9]+$/", $product->getData ( $attributeId ) )) {
			$deliveryTime = $product->getData ( $attributeId );
		}
		
		return $deliveryTime;
	}
	
	/**
	 * Returns the parent configurable product of the given product.
	 * Returns null if the given product isn't a simple one or it has
	 * no parent configurable.
	 *
	 * @param Mage_Catalog_Model_Product $simpleProduct        	
	 * @return Mage_Catalog_Model_Product
	 */
	public function getParentConfigurable(Mage_Catalog_Model_Product $simpleProduct) {
		$parentConfigurable = null;
		
		if ($simpleProduct->getTypeId () === Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
			$parentIds = Mage::getModel ( 'catalog/product_type_configurable' )->getParentIdsByChild ( $simpleProduct->getId () );
			if (isset ( $parentIds [0] )) {
				$parentConfigurable = Mage::getModel ( 'catalog/product' )->setStoreId(Mage::helper ( 'meinpaketcommon/data' )->getMeinPaketStoreId ())->load ( $parentIds [0] );
			}
		}
		
		return $parentConfigurable;
	}
	
	/**
	 * Returns an translated error description.
	 *
	 * @param string $errorType        	
	 * @param string $errorCode        	
	 * @return string
	 */
	public function getErrorDescription($errorType, $errorCode = '') {
		$description = '';
		
		switch ($errorType) {
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_FIELD_IS_INVALID :
				$description = $this->__ ( 'Invalid value for field' ) . ' "<i><b>' . $this->__ ( $this->getLabelForFieldName ( $errorCode ) ) . '</b></i>".';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_REQUIRED_FIELD_IS_EMPTY :
				$description = $this->__ ( 'Missing value for field' ) . ' "<i><b>' . $this->__ ( $this->getLabelForFieldName ( $errorCode ) ) . '</b></i>".';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_PRODUCT_NOT_EXISTS_IN_MEINPAKET :
				$description = $this->__ ( 'Product is unknown in Allyouneed marketplace' ) . '.';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_PRODUCT_NEGATIVE_STOCK :
				$description = $this->__ ( 'Product stock is lower than zero.' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_MEINPAKET_SERVER_ERROR :
				$description = $this->__ ( 'Internal error on Allyouneed server' ) . '.';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_NOT_AUTHORIZED :
				$description = $this->__ ( 'You are not authorized to execute the requested functionality on Allyouneed' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_INVALID_DATA :
				$description = $this->__ ( 'The provided data was incorrect' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_INVALID_MODIFICATION :
				$description = $this->__ ( 'Invalid modification of element' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_NO_CATEGORIZATION :
				$description = $this->__ ( 'The product is not mapped to neither a marketplace nor a shop category' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_PRODUCT_NOT_SELLABLE :
				$description = $this->__ ( 'The referenced product cannot be sold' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_MARKETPLACE_CATEGORY_NOT_FOUND :
				$description = $this->__ ( 'The referenced marketplace category could not be found at Allyouneed' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_SHOP_CATEGORY_NOT_FOUND :
				$description = $this->__ ( 'The referenced shop category could not be found at Allyouneed' );
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_MISSING_VALUE_FOR_ATTRIBUTE :
				$description = $this->__ ( 'Missing value mapping for attribute' ) . ' "' . $errorCode . '".';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_VARIANT_GROUP_NOT_EXISTS :
				$description = $this->__ ( 'Variant group does not exist on Allyouneed' ) . ' "' . $errorCode . '".';
				break;
			case Dhl_MeinPaket_Model_Validation_ValidationInterface::ERROR_UNDEFINED :
			default :
				$description = $this->__ ( 'Undefined Error' );
		}
		
		return $description;
	}
	
	/**
	 * Returns the frontend label for a given attribute.
	 *
	 * @param string $fieldName        	
	 * @return string
	 */
	public function getLabelForFieldName($fieldName) {
		$productAttributeModel = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $fieldName );
		$categoryAttributeModel = Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_category', $fieldName );
		
		$label = $productAttributeModel->getFrontendLabel ();
		
		if (! ( bool ) $label)
			$label = $categoryAttributeModel->getFrontendLabel ();
		
		if (! ( bool ) $label)
			$label = $fieldName;
		
		return $label;
	}
}
