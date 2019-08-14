<?php

/**
 * Parses raw XML responses returned by the MeinPaket webservice.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_XmlResponseParser extends Varien_Object {
	/**
	 *
	 * @param DOMDocument $dom        	
	 * @return Dhl_MeinPaketCommon_Model_Xml_Response_Abstract
	 */
	public function parseResponse(DOMDocument $dom) {
		if (! isset ( $dom->documentElement )) {
			return null;
		}
		
		$documentElement = $dom->documentElement;
		
		/* @var $result Dhl_MeinPaketCommon_Model_Xml_Response_Abstract */
		$result = null;
		
		switch ($documentElement->localName) {
			case 'asynchronousStatusResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_AsynchronousStatusResponse ( $documentElement );
				break;
			case 'downloadResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_DownloadResponse ( $documentElement );
				break;
			case 'uploadResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_UploadResponse ( $documentElement );
				break;
			case 'dataResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_DataResponse ( $documentElement );
				break;
			case 'notificationResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_NotificationResponse ( $documentElement );
				break;
			case 'queryResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse ( $documentElement );
				break;
			case 'submitCartResponse' :
				$result = new Dhl_MeinPaketCommon_Model_Xml_Response_SubmitCartResponse ( $documentElement );
				break;
		}
		
		if ($result != null) {
			$result->process ();
		}
		
		return $result;
	}
}

