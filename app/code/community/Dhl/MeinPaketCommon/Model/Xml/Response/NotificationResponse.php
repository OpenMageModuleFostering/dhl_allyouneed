<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_NotificationResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $confirmations = array ();
	
	/**
	 * Default constructor.
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'notificationResponse' );
		
		foreach ( $domElement->childNodes as $notificationResponseEntries ) {
			switch ($notificationResponseEntries->localName) {
				case 'confirmation' :
					$this->confirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_NotificationResponse_Confirmation ( $notificationResponseEntries );
					break;
			}
		}
	}
	
	/**
	 *
	 * @return array
	 */
	public function getConfirmations() {
		return $this->confirmations;
	}
}
