<?php

/**
 * Observer for all events the DHL MeinPaket extension has to catch.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Observer {
	/**
	 * Holds the id of the order whose save_after event is actually triggered.
	 *
	 * @var integer
	 */
	protected static $currentlySavedOrderId = - 1;
	
	/**
	 * Tells if the save_after has been triggered twice or more on
	 * the same order during script lifetime.
	 *
	 * @var boolean
	 */
	protected static $isSameSavedOrder = false;
	
	/**
	 * Sets the id of the order whose save_after event has been triggered.
	 *
	 * @param integer $orderId        	
	 * @return void
	 */
	protected static function setCurrentOrderId($orderId) {
		if (self::$currentlySavedOrderId === $orderId) {
			self::$isSameSavedOrder = true;
		} else {
			self::$isSameSavedOrder = false;
		}
		self::$currentlySavedOrderId = $orderId;
	}
	
	/**
	 *
	 * @param unknown $observer        	
	 */
	public function gridAddAttributes(Varien_Event_Observer $observer) {
		$block = $observer->getBlock ();
		if (! isset ( $block )) {
			return $this;
		}
		
		if ($block->getType () == 'adminhtml/sales_order_grid') {
			/* @var $block Mage_Adminhtml_Block_Sales_Order_Grid */
			
			$block->addColumnAfter ( 'dhl_mein_paket_order_id', array (
					'header' => __ ( 'Allyouneed Order No.' ),
					'index' => 'dhl_mein_paket_order_id' 
			), 'real_order_id' );
		}
		return $this;
	}
	
	/**
	 * Tells if the save_after event of the current order has been triggered
	 * at least once during the lifecycle of the current script.
	 *
	 * @return boolean
	 */
	protected static function saveEventHasBeenTriggeredOnSameOrderBefore() {
		return self::$isSameSavedOrder;
	}
	
	/**
	 * Is triggered when an order is saved.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	public function salesOrderSaveAfter(Varien_Event_Observer $observer) {
		$order = $observer->getData ( 'order' );
		
		self::setCurrentOrderId ( $order->getId () );
		
		$this->_updateMeinPaketOrderId ( $order );
		$this->_handleOrderItemsChange ( $order );
		$this->_cancelMeinPaketOrder ( $order );
		
		return $this;
	}
	
	/**
	 * Sets the dhl_mein_paket_order_id for the given order.
	 * This is necessary if an order is edit and so created a new one.
	 * The new one will not have it's dhl_mein_paket_order_id attribute
	 * automatically.
	 *
	 * @param Varien_Object $order        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	protected function _updateMeinPaketOrderId($order) {
		// return if the order doesn't have a parent order
		// or if it already has a Allyouneed order id...
		if (! $order->hasData ( 'relation_parent_id' ) || $order->hasData ( 'dhl_mein_paket_order_id' )) {
			return;
		}
		
		/* @var $parentOrder Mage_Sales_Model_Order */
		$parentOrder = Mage::getModel ( 'sales/order' )->load ( $order->getData ( 'relation_parent_id' ) );
		
		if (! $parentOrder->hasData ( 'relation_parent_id' ) || $parentOrder->hasData ( 'dhl_mein_paket_order_id' )) {
			$order->setData ( 'dhl_mein_paket_order_id', $parentOrder->getDhlMeinPaketOrderId () );
			$parentOrder->setData ( 'dhl_mein_paket_order_id', '' )->getResource ()->saveAttribute ( $parentOrder, 'dhl_mein_paket_order_id' );
		}
		
		return $this;
	}
	
	/**
	 * Cancels single order items.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	protected function _handleOrderItemsChange(Mage_Sales_Model_Order $order) {
		if (self::saveEventHasBeenTriggeredOnSameOrderBefore ()) {
			return;
		}
		
		if ($order->getData ( 'state' ) === Mage_Sales_Model_Order::STATE_CANCELED) {
			return $this;
		}
		
		if (! $order->hasData ( 'relation_parent_id' )) {
			return $this;
		}
		
		/* @var $orderHelper Dhl_MeinPaketCommon_Helper_Order */
		$orderHelper = Mage::helper ( 'meinpaketcommon/order' );
		
		/* @var $parentOrder Mage_Sales_Model_Order */
		$parentOrder = Mage::getModel ( 'sales/order' )->load ( $order->getData ( 'relation_parent_id' ) );
		
		if ($orderHelper->isMeinPaketOrder ( $order )) {
			
			$orderItemCollection = $order->getItemsCollection ();
			$parentItemCollection = $parentOrder->getItemsCollection ();
			$items = array ();
			$parentItems = array ();
			$reduceList = array ();
			$submitChanges = false;
			
			foreach ( $orderItemCollection as $item ) {
				$options = $item->getProductOptions ();
				if (isset ( $options ['info_buyRequest'] ) && isset ( $options ['info_buyRequest'] ['dhl_mein_paket_item_id'] )) {
					$items [$options ['info_buyRequest'] ['dhl_mein_paket_item_id']] = array (
							'qty' => ( integer ) $item->getQtyOrdered (),
							'productId' => $item->getProductId () 
					);
				}
			}
			foreach ( $parentItemCollection as $item ) {
				$options = $item->getProductOptions ();
				if ($item->getQtyCanceled () != $item->getQtyOrdered ()) {
					$submitChanges = true;
				}
				if (isset ( $options ['info_buyRequest'] ) && isset ( $options ['info_buyRequest'] ['dhl_mein_paket_item_id'] )) {
					$parentItems [$options ['info_buyRequest'] ['dhl_mein_paket_item_id']] = array (
							'qty' => ( integer ) $item->getQtyOrdered (),
							'productId' => $item->getProductId () 
					);
				}
			}
			
			if ($submitChanges === false) {
				return $this;
			}
			
			// find items with lowered quantity
			if (sizeof ( $items ) < sizeof ( $parentItems )) {
				foreach ( $parentItems as $dhlItemId => $item ) {
					if (! array_key_exists ( $dhlItemId, $items )) {
						$reduceList [$dhlItemId] = array (
								'qty' => $item ['qty'],
								'productId' => $item ['productId'] 
						);
					} elseif ($items [$dhlItemId] ['qty'] < $item ['qty']) {
						$reduceList [$dhlItemId] = array (
								'qty' => $item ['qty'] - $items [$dhlItemId] ['qty'],
								'productId' => $item ['productId'] 
						);
					}
				}
			} else {
				foreach ( $items as $dhlItemId => $item ) {
					if (array_key_exists ( $dhlItemId, $parentItems ) && $item ['qty'] < $parentItems [$dhlItemId] ['qty']) {
						$reduceList [$dhlItemId] = array (
								'qty' => $parentItems [$dhlItemId] ['qty'] - $item ['qty'],
								'productId' => $item ['productId'] 
						);
					}
				}
			}
			
			if (sizeof ( $reduceList ) > 0) {
				// make request
				$cancellationService = new Dhl_MeinPaketCommon_Model_Service_Order_CancellationService ();
				$result = null;
				$helper = Mage::helper ( 'meinpaketcommon/data' );
				$errMsg = '';
				
				try {
					$result = $cancellationService->cancelOrderItems ( $order, $reduceList );
				} catch ( Dhl_MeinPaketCommon_Model_Xml_XmlBuildException $e ) {
					Mage::logException ( $e );
					$errMsg .= $helper->__ ( 'Request could not be built.' );
				} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
					Mage::logException ( $e );
					$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned HTPP code %s.' ), $e->getHttpReturnCode () );
				} catch ( Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException $e ) {
					Mage::logException ( $e );
					$errMsg .= $helper->__ ( 'Connection to Allyouneed server timed out.' );
				} catch ( Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException $e ) {
					Mage::logException ( $e );
					$errMsg .= $helper->__ ( 'Invalid response from Allyouneed server.' );
				} catch ( Exception $e ) {
					Mage::logException ( $e );
					$errMsg .= $helper->__ ( 'Unknown error.' );
				}
				if (is_object ( $result ) && $result->hasError ()) {
					$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned error code %s.' ), $result->getError () );
				}
				
				if (strlen ( $errMsg ) > 0) {
					Mage::getSingleton ( 'adminhtml/session' )->addError ( $helper->__ ( 'Error on tranfering cancelled items to Allyouneed.' ) . ' (' . $errMsg . ')' );
					throw new Exception ( 'Failed transfering cancelled items to Allyouneed server.' );
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Cancels the given order in Allyouneed if the given order is a Allyouneed order
	 * and its state is Mage_Sales_Model_Order::STATE_CANCELED.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	protected function _cancelMeinPaketOrder(Mage_Sales_Model_Order $order) {
		$meinPaketOrderId = $order->getData ( 'dhl_mein_paket_order_id' );
		
		if (strlen ( $meinPaketOrderId ) > 1 && $order->getData ( 'state' ) === Mage_Sales_Model_Order::STATE_CANCELED) {
			
			$col = Mage::getModel ( 'sales/order' )->getCollection ()->addAttributeToFilter ( 'relation_parent_id', $order->getId () )->load ();
			
			if ($col->count () > 0) {
				return $this;
			}
			
			// TODO: Class missing
			
			/* @var $orderCancellationService Dhl_MeinPaketCommon_Model_Service_Order_CancellationService */
			$orderCancellationService = new Dhl_MeinPaketCommon_Model_Service_Order_CancellationService ();
			
			$result = null;
			$helper = Mage::helper ( 'meinpaketcommon/data' );
			$errMsg = '';
			
			try {
				$result = $orderCancellationService->cancelOrder ( $order );
			} catch ( Dhl_MeinPaketCommon_Model_Xml_XmlBuildException $e ) {
				Mage::logException ( $e );
				$errMsg .= $helper->__ ( 'Request could not be built.' );
			} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
				Mage::logException ( $e );
				$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned HTPP code %s.' ), $e->getHttpReturnCode () );
			} catch ( Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException $e ) {
				Mage::logException ( $e );
				$errMsg .= $helper->__ ( 'Connection to Allyouneed server timed out.' );
			} catch ( Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException $e ) {
				Mage::logException ( $e );
				$errMsg .= $helper->__ ( 'Invalid response from Allyouneed server.' );
			} catch ( Exception $e ) {
				Mage::logException ( $e );
				$errMsg .= $helper->__ ( 'Unknown error.' );
			}
			if (is_object ( $result ) && $result->hasError ()) {
				$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned error code %s.' ), $result->getError () );
			}
			
			if (strlen ( $errMsg ) > 0) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $helper->__ ( 'Error on tranfering cancelled items to Allyouneed.' ) . ' (' . $errMsg . ')' );
				throw new Exception ( 'Failed transfering cancelled items to Allyouneed server.' );
			}
		}
		
		return $this;
	}
	
	/**
	 * Is triggered when a shipment is saved.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer) {
		/* @var $order Dhl_MeinPaketCommon_Helper_Shipment */
		$shipmentHelper = Mage::helper ( 'meinpaketcommon/shipment' );
		
		/* @var $shipment Mage_Sales_Model_Order_Shipment */
		$shipment = $observer->getData ( 'shipment' );
		
		self::setCurrentOrderId ( $shipment->getOrder ()->getId () );
		
		if ($shipmentHelper->isExportedToDhlMeinPaket ( $shipment )) {
			return $this;
		}
		
		if (! $shipmentHelper->isMeinPaketShipment ( $shipment )) {
			return $this;
		}
		
		// send shipment request to MeinPaket.
		$this->_exportShipmentToMeinPaket ( $shipment );
		
		return $this;
	}
	
	/**
	 * Is called after a shipment save is committed.
	 * Sets the shipment's "shipment_was_exported_for_dhl_mein_paket" attribute
	 * value to "1" if it isn't set yet.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	public function salesOrderShipmentSaveCommitAfter(Varien_Event_Observer $observer) {
		/* @var $order Dhl_MeinPaketCommon_Helper_Shipment */
		$shipmentHelper = Mage::helper ( 'meinpaketcommon/shipment' );
		
		/* @var $shipment Mage_Sales_Model_Order_Shipment */
		$shipment = $observer->getShipment ();
		
		self::setCurrentOrderId ( $shipment->getOrder ()->getId () );
		
		if ($shipmentHelper->isExportedToDhlMeinPaket ( $shipment )) {
			return $this;
		}
		
		if (! $shipmentHelper->isMeinPaketShipment ( $shipment )) {
			return $this;
		}
		
		if ($shipmentHelper->isMeinPaketShipment ( $shipment ) && ! $shipment->getData ( 'shipment_was_exported_for_dhl_mein_paket' )) {
			$shipment->setData ( 'shipment_was_exported_for_dhl_mein_paket', 1 )->getResource ()->saveAttribute ( $shipment, 'shipment_was_exported_for_dhl_mein_paket' );
		}
	}
	
	/**
	 * Exports an saved shipment to Allyouneed.
	 *
	 * @param Mage_Sales_Model_Order_Shipment $shipment        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	protected function _exportShipmentToMeinPaket(Mage_Sales_Model_Order_Shipment $shipment) {
		$shipmentExportService = new Dhl_MeinPaketCommon_Model_Service_Order_ShipmentExportService ();
		
		$result = null;
		$errMsg = '';
		$helper = Mage::helper ( 'meinpaketcommon/data' );
		
		try {
			$result = $shipmentExportService->exportShipment ( $shipment );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_XmlBuildException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Request could not be built.' );
		} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
			Mage::logException ( $e );
			$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned HTPP code %s.' ), $e->getHttpReturnCode () );
		} catch ( Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Connection to Allyouneed server timed out.' );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Invalid response from Allyouneed server.' );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Unknown error.' );
		}
		
		if ($result == null || $result->hasErrors ()) {
			$errMsg .= $helper->__ ( 'Shipment has not been accepted by Allyouneed.' );
		}
		
		if (strlen ( $errMsg ) > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $helper->__ ( 'Error on transfering shipment to Allyouneed.' ) . ' (' . $errMsg . ')' );
			throw new Exception ( 'Failed exporting shipment to Allyouneed server.' );
		}
		
		return $this;
	}
	
	/**
	 * Is called when a creditmemo is saved.
	 * If the creditmemo is refunded, a notification will be send to Allyouneed.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	public function salesOrderCreditmemoSaveAfter(Varien_Event_Observer $observer) {
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getData ( 'creditmemo' );
		
		self::setCurrentOrderId ( $creditmemo->getOrder ()->getId () );
		
		// check if creditmemo is refunded
		if ($creditmemo->getState () != Mage_Sales_Model_Order_Creditmemo::STATE_REFUNDED) {
			return $this;
		}
		
		// check if the order is a Allyouneed order
		if (! Mage::helper ( 'meinpaketcommon/order' )->isMeinPaketOrder ( $creditmemo->getOrder () )) {
			return $this;
		}
		
		/* @var $result Dhl_MeinPaketCommon_Model_Sevice_RefundExport_Result */
		$result = null;
		
		/* @var $exportService Dhl_MeinPaketCommon_Model_Service_Order_RefundExportService */
		$exportService = new Dhl_MeinPaketCommon_Model_Service_Order_RefundExportService ();
		
		$helper = Mage::helper ( 'meinpaketcommon/data' );
		$errMsg = '';
		
		try {
			$result = $exportService->exportRefund ( $creditmemo );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_XmlBuildException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Request could not be built.' );
		} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
			Mage::logException ( $e );
			$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned HTPP code %s.' ), $e->getHttpReturnCode () );
		} catch ( Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Connection to Allyouneed server timed out.' );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Invalid response from Allyouneed server.' );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Unknown error.' );
		}
		
		if ($result == null) {
			$errMsg .= $helper->__ ( 'Refund has not been accepted by Allyouneed.' );
		}
		
		if (strlen ( $errMsg ) > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $helper->__ ( 'Error on transfering refund to Allyouneed.' ) . ' (' . $errMsg . ')' );
			throw new Exception ( 'Failed exporting refund to Allyouneed server.' );
		}
		
		return $this;
	}
	
	/**
	 * Is called when a shipment track is saved.
	 * The tracking code will be sent to Allyouneed if it's the first track
	 * for the shipment.
	 *
	 * @param Varien_Event_Observer $observer        	
	 * @return Dhl_MeinPaketCommon_Model_Observer
	 */
	public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer) {
		/* @var $track Mage_Sales_Model_Order_Shipment_Track */
		$track = $observer->getData ( 'track' );
		
		if ($track == null) {
			return this;
		}
		
		self::setCurrentOrderId ( $track->getOrderId () );
		
		if (self::$isSameSavedOrder) {
			return $this;
		}
		
		/* @var $shipment Mage_Sales_Model_Order_Shipment */
		$shipment = $track->getShipment ();
		
		// check if order is a Allyouneed order...
		if (! Mage::helper ( 'meinpaketcommon/shipment' )->isMeinPaketShipment ( $shipment )) {
			return $this;
		}
		
		$helper = Mage::helper ( 'meinpaketcommon/data' );
		$result = null;
		$errMsg = '';
		
		$service = new Dhl_MeinPaketCommon_Model_Service_Order_ShipmentExportService ();
		
		try {
			$result = $service->exportTrackingNumber ( $track );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_XmlBuildException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Request could not be built.' );
		} catch ( Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException $e ) {
			Mage::logException ( $e );
			$errMsg .= sprintf ( $helper->__ ( 'Allyouneed server returned HTPP code %s.' ), $e->getHttpReturnCode () );
		} catch ( Dhl_MeinPaketCommon_Model_Client_HttpTimeoutException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Connection to Allyouneed server timed out.' );
		} catch ( Dhl_MeinPaketCommon_Model_Xml_InvalidXmlException $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Invalid response from Allyouneed server.' );
		} catch ( Exception $e ) {
			Mage::logException ( $e );
			$errMsg .= $helper->__ ( 'Unknown error.' );
		}
		
		if ($result !== null && ! $result->hasBeenAccepted ()) {
			$errMsg .= $helper->__ ( 'Tracking code has not been accepted by Allyouneed.' );
			if ($result->hasError ()) {
				$errMsg .= ' (' . sprintf ( $helper->__ ( 'Allyouneed returned error code %s.' ), $result->getError () ) . ')';
			}
		}
		
		if (strlen ( $errMsg ) > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( $helper->__ ( 'Error on transfering tracking code to Allyouneed.' ) . ' (' . $errMsg . ')' );
			throw new Exception ( 'Failed exporting tracking code to Allyouneed server.' );
		}
		
		return $this;
	}
}
