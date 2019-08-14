<?php

/**
 * Service class which imports orders from Allyouneed.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Order
 * @version		$Id$
 */
class Dhl_Postpay_Model_Service_Order_ImportService extends Dhl_MeinPaketCommon_Model_Service_Order_ImportService {
	
	/**
	 *
	 * @var string
	 */
	const POSTPAY_IMPORT_PAYMENT_METHOD = 'postpay_express';
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Imports Order from meinPaket
	 * //TODO make a simple result object containing: countnew / countexisting / warnings (e.g.
	 * if price missmatch)
	 *
	 * @param integer $start        	
	 * @param integer $stop        	
	 * @return void
	 */
	public function importOrders($start = null, $stop = null) {
		$cartCollection = Mage::getModel ( 'postpay/cart' )->getCollection ()->addFilter ( 'state', Dhl_Postpay_Model_Cart::STATE_PENDING );
		
		$queryRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest ();
		foreach ( $cartCollection as $cart ) {
			$queryRequest->addShoppingCartStatus ( $cart->getCartId () );
		}
		
		if ($queryRequest->isHasData ()) {
			$connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
			try {
				$connection->beginTransaction ();
				
				// Make saves and other actions that affect the database
				$queryResult = $this->client->send ( $queryRequest );
				
				if ($queryResult != null && $queryResult instanceof Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse) {
					$this->processQueryResponse ( $cart, $queryResult );
				}
				
				$connection->commit ();
			} catch ( Exception $e ) {
				$connection->rollback ();
			}
		}
		
		return parent::importOrders ( $start, $stop );
	}
	
	/**
	 * process status infos.
	 *
	 * @param Dhl_Postpay_Model_Cart $cart        	
	 * @param Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse $queryResult        	
	 */
	protected function processQueryResponse(Dhl_Postpay_Model_Cart $cart, Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse $queryResult) {
		$statusResponses = $queryResult->getShoppingCartStatusResponses ();
		
		foreach ( $statusResponses as $key => $status ) {
			$status = strtoupper ( $status );
			
			if ($status == Dhl_Postpay_Model_Cart::STATE_PENDING ) {
				continue;
			}
			
			$orderModel = null;
			/* @var $orderModel Mage_Sales_Model_Order */
			if ($cart->getOrderId () != null) {
				$orderModel = Mage::getModel ( 'sales/order' )->load ( $cart->getOrderId () );
			}
			
			switch ($status) {
				case Dhl_Postpay_Model_Cart::STATE_CREATEDORDER :
					$createdOrderRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest ();
					$createdOrderRequest->addOrderExternalId ( $cart->getCartId () );
					
					if ($createdOrderRequest->isHasData ()) {
						$createdOrderResult = $this->client->send ( $createdOrderRequest );
						
						if ($createdOrderResult != null && $createdOrderResult instanceof Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse) {
							foreach ( $createdOrderResult->getOrders () as $order ) {
								/* @var $order Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order */
								if ($orderModel != null && $orderModel->getId ()) {
									$this->_orderCount ['imported'] ++;
									$orderModel->setData ( 'dhl_mein_paket_order_id', $order->getOrderId () );
									$this->createInvoice ( $orderModel );
									
									$cart->setState ( $status );
									$cart->save ();
								} else {
									$successCode = $this->_importOrder ( $order, self::POSTPAY_IMPORT_PAYMENT_METHOD );
									
									switch ($successCode) {
										case self::IMPORTED_ORDER_STATUS :
											$this->_orderCount ['imported'] ++;
											
											$cart->setState ( $status );
											$cart->save ();
											
											break;
										case self::DUPLICATE_ORDER_STATUS :
											$this->_orderCount ['duplicates'] ++;
											
											$cart->setState ( $status );
											$cart->save ();
											
											break;
										case self::OUT_OF_STOCK_ORDER_STATUS :
											$this->_orderCount ['outOfStock'] ++;
											break;
										case self::INVALID_PRODUCT_STATUS :
											$this->_orderCount ['invalid'] ++;
											break;
										case self::DISABLED_ORDER_STATUS :
											$this->_orderCount ['disabled'] ++;
											break;
									}
								}
							}
						}
					}
					break;
				case Dhl_Postpay_Model_Cart::STATE_CANCELED :
					if ($orderModel != null) {
						$orderModel->cancel ();
						$orderModel->save ();
					}
					$cart->setState ( $status );
					$cart->save ();
					break;
			}
		}
	}
}
