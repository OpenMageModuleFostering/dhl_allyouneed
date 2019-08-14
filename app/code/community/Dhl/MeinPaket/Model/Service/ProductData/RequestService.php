<?php

/**
 * Service which exports an order's shipment to Allyouneed.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_ShipmentExport
 * @version		$Id$
 */
class Dhl_MeinPaket_Model_Service_ProductData_RequestService {
	/**
	 * Exports a shipment to Allyouneed.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @throws Dhl_MeinPaket_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaket_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaket_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaket_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaket_Model_Service_ShipmentExport_Result
	 */
	public function getProductData(Mage_Catalog_Model_Product $product, $sendEAN = false, $sendName = false) {
		/* @var $uploadRequest Dhl_MeinPaket_Model_Xml_Request_ProductDataRequest */
		$productDataRequest = Mage::getModel ( 'meinpaketcommon/xml_request_dataRequest' );
		
		/* @var $client Dhl_MeinPaket_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		try {
			$productDataRequest->addProduct ( $product, $sendEAN, $sendName );
			return $response = $client->send ( $productDataRequest );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			
			// return $this->_result;
		}
		
		return null;
	}
	
	/**
	 * Request best prices from MeinPaket.
	 *
	 * @param Mage_Catalog_Model_Product $product        	
	 * @throws Dhl_MeinPaket_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaket_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaket_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaket_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaket_Model_Service_ShipmentExport_Result
	 */
	public function requestBestPrices() {
		/* @var $uploadRequest Dhl_MeinPaket_Model_Xml_Request_ProductDataRequest */
		$productDataRequest = Mage::getModel ( 'meinpaketcommon/xml_request_dataRequest' );
		
		/* @var $client Dhl_MeinPaket_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		try {
			/* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
			$collection = Mage::getModel ( 'catalog/product' )->getCollection ();
			
			$collection->addAttributeToFilter ( 'meinpaket_id', array (
					'neq' => '' 
			) );
			$collection->addAttributeToFilter ( 'sync_with_dhl_mein_paket', array (
					'gt' => '0' 
			) );
			
			foreach ( $collection as $productId ) {
				$productDataRequest->addBestPriceProduct ( $productId );
			}
			return $response = $client->send ( $productDataRequest, true );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
		}
		
		return null;
	}
}
