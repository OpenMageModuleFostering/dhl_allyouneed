<?php

/**
 * Service class which sends a refund to Allyouneed.
 * 
 * @category    Dhl
 * @package     Dhl_MeinPaket
 * @subpackage  Model_Service_RefundExport
 * @version     $Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Order_RefundExportService {
	/**
	 * Exports the given refunded creditmemo to Allyouneed.
	 *
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo        	
	 * @throws Dhl_MeinPaketCommon_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaketCommon_Model_Sevice_RefundExport_Result
	 */
	public function exportRefund(Mage_Sales_Model_Order_Creditmemo $creditmemo) {
		/* @var $httpClient Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$httpClient = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		/* @var $notificationRequest Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest */
		$notificationRequest = Mage::getModel ( 'meinpaketcommon/xml_request_notificationRequest' );
		
		$order = $creditmemo->getOrder ();
		if ($order->hasShipments ()) {
			foreach ( $order->getShipmentsCollection () as $shipment ) {
				$notificationRequest->addCreditMemo ( $creditmemo, $shipment );
			}
		} else {
			$notificationRequest->addCancellation ( $creditmemo->getOrder () );
		}
		
		try {
			if ($notificationRequest->isHasData ()) {
				return $httpClient->send ( $notificationRequest );
			}
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			throw $e;
		}
		
		return null;
	}
}

