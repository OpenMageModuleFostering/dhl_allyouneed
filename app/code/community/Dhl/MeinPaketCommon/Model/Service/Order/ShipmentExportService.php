<?php

/**
 * Service which exports an order's shipment to Allyouneed.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service_ShipmentExport
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Order_ShipmentExportService {
	/**
	 * Exports a shipment to Allyouneed.
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment        	
	 * @throws Dhl_MeinPaketCommon_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaketCommon_Model_Xml_Response_NotificationResponse
	 */
	public function exportShipment(Mage_Sales_Model_Order_Shipment $shipment) {
		$notificationRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest ();
		
		/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$notificationRequest->addConsignment ( $shipment );
		
		/* @var $responseXml Dhl_MeinPaketCommon_Model_Xml_Response_NotificationResponse */
		$responseXml = $client->send ( $notificationRequest );
		
		return $responseXml;
	}
	
	/**
	 * Exports the tracking number of a shipment to Allyouneed.
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment
	 *        	The shipment which tracking numbers shall be exported.
	 * @throws Dhl_MeinPaketCommon_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaketCommon_Model_Service_TrackingNumberExport_Result
	 */
	public function exportTrackingNumber(Mage_Sales_Model_Order_Shipment_Track $track) {
		$notificationRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest ();
		
		/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$notificationRequest->addTrackingNumber ( $track );
		
		try {
			$responseXml = $client->send ( $notificationRequest );
			// TODO: parse
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			throw $e;
		}
		
		return null;
	}
}
