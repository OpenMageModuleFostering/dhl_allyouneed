<?php

/**
 * Partial which represents the 'notificationRequest' element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_NotificationRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	/**
	 *
	 * @var DOMNode
	 */
	protected $cancellations = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $consignments = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $trackingNumbers = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $returns = null;
	/**
	 *
	 * @var DOMNode
	 */
	protected $credits = null;
	
	/**
	 * Default Constructor.
	 */
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Create the root element for the document.
	 *
	 * @return DOMNode
	 */
	public function createDocumentElement() {
		$this->node = $this->getDocument ()->createElement ( 'notificationRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_ORDERS );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment        	
	 * @param Mage_Sales_Model_Order $order        	
	 * @param mixed $trackingNumber
	 *        	null or trackingNumber
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addConsignment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order = null, $trackingNumber = null) {
		$consignmentNode = $this->getDocument ()->createElement ( 'consignment' );
		$this->getConsignments ()->appendChild ( $consignmentNode );
		
		if ($order == null) {
			$order = $shipment->getOrder ();
		}
		
		$orderIdNode = $this->getDocument ()->createElement ( 'orderId', $order->getDhlMeinPaketOrderId () );
		$consignmentNode->appendChild ( $orderIdNode );
		
		$consignmentIdNode = $this->getDocument ()->createElement ( 'consignmentId', $shipment->getId () );
		$consignmentNode->appendChild ( $consignmentIdNode );
		
		if ($trackingNumber == null) {
			foreach ( $shipment->getAllTracks () as $track ) {
				// take the last one
				$trackingNumber = $track->getNumber ();
			}
		}
		
		if (strlen ( $trackingNumber )) {
			$trackingIdNode = $this->getDocument ()->createElement ( 'trackingId', $trackingNumber );
			$consignmentNode->appendChild ( $trackingIdNode );
		}
		
		foreach ( $shipment->getAllItems () as $item ) {
			$consignmentEntryNode = $this->getDocument ()->createElement ( 'consignmentEntry' );
			$consignmentNode->appendChild ( $consignmentEntryNode );
			
			$consignmentEntryNode->appendChild ( $this->getDocument ()->createElement ( 'common:productId', $item->getProductId () ) );
			$consignmentEntryNode->appendChild ( $this->getDocument ()->createElement ( 'quantity', ( int ) $item->getQty () ) );
		}
		
		$this->setHasData();
		
		return $this;
	}
	
	/**
	 *
	 * @param Mage_Sales_Model_Order_Shipment_Track $trackingNumber        	
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addTrackingNumber(Mage_Sales_Model_Order_Shipment_Track $trackingNumber) {
		$trackingNumberNode = $this->getDocument ()->createElement ( 'trackingNumber' );
		$this->getTrackingNumbers ()->appendChild ( $trackingNumberNode );
		
		$consignmentIdNode = $this->getDocument ()->createElement ( 'consignmentId', $trackingNumber->getShipment ()->getId () );
		$trackingNumberNode->appendChild ( $consignmentIdNode );
		
		$trackingIdNode = $this->getDocument ()->createElement ( 'trackingId', $trackingNumber->getNumber () );
		$trackingNumberNode->appendChild ( $trackingIdNode );
		
		$this->setHasData();
		
		return $this;
	}
	
	/**
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment        	
	 * @param Mage_Sales_Model_Order $order        	
	 * @param mixed $trackingNumber
	 *        	null or trackingNumber
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addCancellation(Mage_Sales_Model_Order $order) {
		$cancellationNode = $this->getDocument ()->createElement ( 'cancellation' );
		$this->getCancellations ()->appendChild ( $cancellationNode );
		
		$orderIdNode = $this->getDocument ()->createElement ( 'orderId', $order->getData ( 'dhl_mein_paket_order_id' ) );
		$cancellationNode->appendChild ( $orderIdNode );
		
		$consignmentIdNode = $this->getDocument ()->createElement ( 'consignmentId', $order->getId () . ' ' . microtime () );
		$cancellationNode->appendChild ( $consignmentIdNode );
		
		foreach ( $order->getAllItems () as $orderItem ) {
			$cancellationEntryNode = $this->getDocument ()->createElement ( 'cancellationEntry' );
			$cancellationNode->appendChild ( $cancellationEntryNode );
			
			$productIdNode = $this->getDocument ()->createElement ( 'common:productId', $orderItem->getProductId () );
			$cancellationEntryNode->appendChild ( $productIdNode );
			
			$quantityNode = $this->getDocument ()->createElement ( 'quantity', ( int ) $orderItem->getQtyOrdered () );
			$cancellationEntryNode->appendChild ( $quantityNode );
			
			$reasonNode = $this->getDocument ()->createElement ( 'reason', 'DealerRequest' );
			$cancellationEntryNode->appendChild ( $reasonNode );
		}
		
		$this->setHasData();
		
		return $this;
	}
	
	/**
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment        	
	 * @param Mage_Sales_Model_Order $order        	
	 * @param mixed $trackingNumber
	 *        	null or trackingNumber
	 * @return DOMNode|Ambigous <boolean, DOMElement>
	 */
	public function addCreditMemo(Mage_Sales_Model_Order_Creditmemo $creditMemo, Mage_Sales_Model_Order_Shipment $shipment) {
		$creditMemoNode = $this->getDocument ()->createElement ( 'creditMemo' );
		$this->getCredits ()->appendChild ( $creditMemoNode );
		
		$consignmentIdNode = $this->getDocument ()->createElement ( 'consignmentId', $shipment->getId () );
		$creditMemoNode->appendChild ( $consignmentIdNode );
		
		$orderIdNode = $this->getDocument ()->createElement ( 'orderId', $creditMemo->getOrder ()->getDhlMeinPaketOrderId () );
		$creditMemoNode->appendChild ( $orderIdNode );
		
		$creditIdNode = $this->getDocument ()->createElement ( 'creditId', $creditMemo->getId () );
		$creditMemoNode->appendChild ( $creditIdNode );
		
		$creditAmountNode = $this->getDocument ()->createElement ( 'creditAmount', $creditMemo->getGrandTotal () );
		$creditMemoNode->appendChild ( $creditAmountNode );
		
		$creditMemoReasonNode = $this->getDocument ()->createElement ( 'creditMemoReason', 'DEALER_GOODWILL' );
		$creditMemoNode->appendChild ( $creditMemoReasonNode );
		
		$this->setHasData();
		
		return $this;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getCancellations() {
		if ($this->cancellations == null) {
			$this->cancellations = $this->getDocument ()->createElement ( 'cancellations' );
			$this->getDocumentElement ()->appendChild ( $this->cancellations );
		}
		return $this->cancellations;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getConsignments() {
		if ($this->consignments == null) {
			$this->consignments = $this->getDocument ()->createElement ( 'consignments' );
			$this->getDocumentElement ()->appendChild ( $this->consignments );
		}
		return $this->consignments;
	}
	
	/**
	 *
	 * @return DOMElement
	 */
	protected function getReturns() {
		if ($this->returns == null) {
			$this->returns = $this->getDocument ()->createElement ( 'returns' );
			$this->getDocumentElement ()->appendChild ( $this->returns );
		}
		return $this->returns;
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
	protected function getCredits() {
		if ($this->credits == null) {
			$this->credits = $this->getDocument ()->createElement ( 'credits' );
			$this->getDocumentElement ()->appendChild ( $this->credits );
		}
		return $this->credits;
	}
}
