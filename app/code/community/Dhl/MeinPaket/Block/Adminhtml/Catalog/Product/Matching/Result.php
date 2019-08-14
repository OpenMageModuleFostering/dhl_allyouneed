<?php
/**
 * Block for the product matching.
 *
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Dhl_MeinPaket_Block_Adminhtml_Catalog_Product_Matching
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_Catalog_Product_Matching_Result extends Mage_Adminhtml_Block_Abstract {
	public function getSuggestions() {
		$product = Mage::registry ( 'current_product' );
		$ean = Mage::registry ( 'sendEan' );
		$name = Mage::registry ( 'sendName' );
		return Mage::getModel ( 'meinpaket/service_productData_requestService' )->getProductData ( $product, $ean, $name );
	}
}
