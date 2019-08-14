<?php

/**
 * Service class which cancels an order.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaketCommon
 * @subpackage	Model_OrderCancellation
 * @version		$Id$
 */
class Dhl_MeinPaketCommonModel_Service_Order_CancellationService extends Varien_Object {
	/**
	 * Cancels the given MeinPaket order.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 * @throws Dhl_MeinPaketCommon_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException
	 * @return Dhl_MeinPaketCommon_Model_OrderCancellation_Result
	 */
	public function cancelOrder(Mage_Sales_Model_Order $order) {
		
		/* @var $notificationRequest Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest */
		$notificationRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest ();
		
		/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$httpClient = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		/* @var $result Dhl_MeinPaketCommon_Model_Service_Order_CancellationService_Result */
		$result = new Dhl_MeinPaketCommon_Model_Service_Order_CancellationService_Result ();
		
		try {
			$notificationRequest->addCancellation ( $order );
			$responseXml = $httpClient->send ( $notificationRequest );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			throw $e;
		}
		
		return $result;
	}
	
	/**
	 * TODO: broken
	 * 
	 * Cancels a set of items for MeinPaket order.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 * @param array $items
	 *        	of the array have to be associative arrays
	 *        	containing the keys "productId" and "qty" (Quantity to reduce).
	 * @throws Dhl_MeinPaketCommon_Model_Xml_XmlBuildException
	 * @throws Dhl_MeinPaketCommonModel_Client_BadHttpReturnCodeException
	 * @throws Dhl_MeinPaketCommonModel_Client_HttpTimeoutException
	 * @throws Dhl_MeinPaketCommonModel_Xml_InvalidXmlException
	 * @return Dhl_MeinPaketCommonModel_OrderCancellation_Result
	 */
	public function cancelOrderItems(Mage_Sales_Model_Order $order, array $items) {
		$notificationRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest();
		
		/* @var $xmlResponseParser Dhl_MeinPaketCommonModel_Xml_XmlResponseParser */
		$xmlResponseParser = Mage::getModel ( 'meinpaketcommon/Xml_XmlResponseParser' );
		
		/* @var $httpClient Dhl_MeinPaketCommonModel_Client_XmlOverHttp */
		$httpClient = Mage::getModel ( 'meinpaketcommon/Client_XmlOverHttp' );
		
		/* @var $result Dhl_MeinPaketCommonModel_Service_Order_CancellationService_Result */
		$result = Mage::getModel ( 'meinpaketcommon/service_order_cancellationService_result' );
		
		$requestXml = '';
		$responseXml = '';
		
		try {
			// TODO: see Observer line 220
			$response = $httpClient->send ( $xmlRequestFactory->createPartialOrderCancellationRequest ( $order, $items ) );
			$xmlResponseParser->parseOrderCancellationResultFromXml ( $responseXml, $result );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			throw $e;
		}
		
		return $result;
	}
}
