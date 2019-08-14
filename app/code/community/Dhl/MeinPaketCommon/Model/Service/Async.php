<?php

/**
 * Result class which encapsulates information concerning the shipment export process.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_ShipmentExport
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Async extends Dhl_MeinPaketCommon_Model_Service_Abstract {
	/**
	 * Exports products.
	 *
	 * @param integer $selectionMode        	
	 * @return Dhl_MeinPaketCommon_Model_Service_Product_Export_Result
	 */
	public function process() {
		$seenMagentoProducts = array ();
		
		/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		/* var $logCollection Dhl_MeinPaketCommon_Model_Mysql4_Log_Collection */
		$asyncCollection = Mage::getModel ( 'meinpaketcommon/async' )->getCollection ();
		
		$count = 0;
		
		/* @var $async Dhl_MeinPaketCommon_Model_Async */
		foreach ( $asyncCollection as $async ) {
			/* @var $statusRequest Dhl_MeinPaketCommon_Model_Xml_Request_AsynchronousStatusRequest */
			$statusRequest = Mage::getModel ( 'meinpaketcommon/xml_request_asynchronousStatusRequest' );
			$statusRequest->addRequestStatus ( $async->getRequestId () );
			
			$response = null;
			if ($statusRequest->isHasData ()) {
				try {
					$response = $client->send ( $statusRequest );
				} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
					Mage::logException ( $e );
				}
			}
			
			if ($response != null && $response instanceof Dhl_MeinPaketCommon_Model_Xml_Response_AsynchronousStatusResponse) {
				/* @var $response Dhl_MeinPaketCommon_Model_Xml_Response_AsynchronousStatusResponse */
				$async->setRequestId ( $response->getRequestId () );
				$async->setStatus ( $response->getStatus () );
				$async->setUpdatedAt ( Varien_Date::now () );
				$async->save ();
			} else {
				$async->delete ();
			}
			
			$count ++;
		}
		
		return Mage::helper ( 'meinpaket/data' )->__ ( "Processed %d async jobs", $count );
	}
}

