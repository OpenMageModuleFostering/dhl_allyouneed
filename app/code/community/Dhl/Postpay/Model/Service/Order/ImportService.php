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
		/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
		$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
		
		$cartCollection = Mage::getModel ( 'postpay/cart' )->getCollection ()->addFilter ( 'state', 'PENDING' );
		
		foreach ( $cartCollection as $cart ) {
			$queryRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest ();
			$queryRequest->addShoppingCartStatus ( $cart->getCartId () );
			
			if ($queryRequest->isHasData ()) {
				$connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
				try {
					$connection->beginTransaction ();
					
					// Make saves and other actions that affect the database
					$queryResult = $client->send ( $queryRequest );
					
					if ($queryResult != null && $queryResult instanceof Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse) {
						$this->processQueryResponse ( $cart, $queryResult );
					}
					
					$connection->commit ();
				} catch ( Exception $e ) {
					$connection->rollback ();
				}
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
		
		reset ( $statusResponses );
		$key = key ( $statusResponses );
		$status = strtoupper ( $statusResponses [$key] );
		
		$orderModel = null;
		/* @var $order Mage_Sales_Model_Order */
		if ($cart->getOrderId () != null) {
			$orderModel = Mage::getModel ( 'sales/order' )->load ( $cart->getOrderId () );
		}
		
		switch ($status) {
			case 'PENDING' :
				break;
			case 'CREATEDORDER' :
				$queryRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest ();
				$queryRequest->addOrderExternalId ( $cart->getCartId () );
				
				if ($queryRequest->isHasData ()) {
					/* @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp */
					$client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
					
					$queryResult = $client->send ( $queryRequest );
					
					if ($queryResult != null && $queryResult instanceof Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse) {
						foreach ( $queryResult->getOrders () as $order ) {
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
			case 'CANCELED' :
				if ($order != null) {
					$order->cancel ();
					$order->save ();
				}
				$cart->setState ( $status );
				$cart->save ();
				break;
		}
	}
}
