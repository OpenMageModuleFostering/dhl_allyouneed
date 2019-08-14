<?php

/**
 * Service class which imports orders from MeinPaket.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Order
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Service_Order_ImportService {
	/**
	 * counts of imported and duplicate orders
	 *
	 * @var $_orders array
	 */
	var $_orderCount;
	
	/**
	 * DHL IDs from orders which are out of stock
	 *
	 * @var $_outOfStockOrders array
	 */
	var $_outOfStockOrders;
	
	/**
	 * Dhl order ids which include disabled products.
	 *
	 * @var array
	 */
	protected $_disabledProductOrders;
	
	/**
	 *
	 * @var string
	 */
	const IMPORT_SHIPPING_METHOD = 'allyouneed_standard';
	
	/**
	 *
	 * @var string
	 */
	const IMPORT_PAYMENT_METHOD = 'allyouneed';
	
	/**
	 *
	 * @var duplicate orders, returncode
	 */
	const DUPLICATE_ORDER_STATUS = 2;
	
	/**
	 *
	 * @var imported orders, returncode
	 */
	const IMPORTED_ORDER_STATUS = 1;
	
	/**
	 *
	 * @var out-of-stock orders, returncode
	 */
	const OUT_OF_STOCK_ORDER_STATUS = 3;
	
	/**
	 *
	 * @var invalid products in orders, returncode
	 */
	const INVALID_PRODUCT_STATUS = 4;
	
	/**
	 * Status for a disabled product.
	 *
	 * @var integer
	 */
	const DISABLED_ORDER_STATUS = 4;
	const USE_GUEST_ACCOUNT_CONFIG = 'meinpaket/order/use_guest_account';
	const CUSTOMER_GROUP_CONFIG = 'meinpaket/customer/default_group';
	const SHIPPING_METHOD_CONFIG = 'meinpaket/shipment/default_shipment_method';
	
	/**
	 *
	 * @var boolean
	 */
	protected $serviceResponseWasMalformed = false;
	
	/**
	 * Event prefix for this class
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'meinpaketcommon_service_order_importService';
	
	/**
	 *
	 * @var Dhl_MeinPaketCommon_Helper_Data
	 */
	protected $_dataHelper;
	
	/**
	 *
	 * @var $client Dhl_MeinPaketCommon_Model_Client_XmlOverHttp
	 */
	protected $client;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_orderCount = array (
				'imported' => 0,
				'duplicates' => 0,
				'outOfStock' => 0,
				'disabled' => 0,
				'invalid' => 0 
		);
		
		$_outOfStockOrders = array ();
		$this->_disabledProductOrders = array ();
		$this->_dataHelper = Mage::helper ( 'meinpaketcommon/data' );
		$this->client = Mage::getModel ( 'meinpaketcommon/client_xmlOverHttp' );
	}
	
	/**
	 * Tells if the MeinPaket response for the impoer orders request was
	 * malformed and could not be parsed.
	 *
	 * @return boolean
	 */
	public function wasServiceResponseMalformed() {
		return $this->serviceResponseWasMalformed;
	}
	
	/**
	 * returns count of imported and duplicate orders
	 *
	 * @return array
	 */
	public function getOrderCount() {
		return $this->_orderCount;
	}
	
	/**
	 * returns DHL IDs of orders which are out of stock
	 *
	 * @return array
	 */
	public function getOutOfStockOrders() {
		return $this->_outOfStockOrders;
	}
	
	/**
	 * Returns the order ids which could not be imported, because they contained
	 * disabled products.
	 *
	 * @return array
	 */
	public function getDisabledProductOrders() {
		return $this->_disabledProductOrders;
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
		$queryRequest = new Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest ();
		// $queryRequest->addOrders ( $start, $stop );
		$queryRequest->addOrders ( $start, $stop, 'Open' );
		
		/* @var $queryResult Dhl_MeinPaketCommon_Model_Xml_Response_QueryResponse */
		$queryResult = $this->client->send ( $queryRequest );
		
		if ($queryResult == null) {
			return;
		}
		
		foreach ( $queryResult->getOrders () as $order ) {
			/* @var $order Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order */
			$successCode = $this->_importOrder ( $order );
			switch ($successCode) {
				case self::IMPORTED_ORDER_STATUS :
					$this->_orderCount ['imported'] ++;
					break;
				case self::DUPLICATE_ORDER_STATUS :
					$this->_orderCount ['duplicates'] ++;
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
		
		return $this->_orderCount;
	}
	
	/**
	 * imports orders from XML, returns success code
	 *
	 * @param SimpleXMLElement $order        	
	 * @return int
	 */
	protected function _importOrder(Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order $order, $paymentMethod = self::IMPORT_PAYMENT_METHOD) {
		$storeId = $this->_dataHelper->getMeinPaketStoreId ();
		$store = $this->_dataHelper->getMeinPaketStore ();
		/* @var $taxCalculation Mage_Tax_Model_Calculation */
		$taxCalculation = Mage::getModel ( 'tax/calculation' );
		
		/* @var $taxHelper Mage_Tax_Helper_Data */
		// $taxHelper = Mage::helper ( 'tax' );
		
		/* @var $taxConfig Mage_Tax_Model_Config */
		$taxConfig = Mage::getSingleton ( 'tax/config' );
		
		$priceIncludesTax = $taxConfig->priceIncludesTax ( $store );
		$shippingIncludesTax = $taxConfig->shippingPriceIncludesTax ( $store );
		
		/* @var $orderObj Mage_Sales_Model_Order */
		$orderObj = Mage::getModel ( 'sales/order' )->load ( $order->getOrderId (), 'dhl_mein_paket_order_id' );
		
		// do not import order already existing orders
		if ($orderObj->getId ()) {
			return self::DUPLICATE_ORDER_STATUS;
		}
		
		$customer = $this->getOrCreateCustomer ( $order );
		
		if (! Mage::getStoreConfig ( self::USE_GUEST_ACCOUNT_CONFIG )) {
			$customer->save ();
		}
		
		// Set email for guest order
		/* @var $quoteObj Mage_Sales_Model_Quote */
		$quoteObj = Mage::getModel ( 'sales/quote' );
		$quoteObj->setStoreId ( $storeId );
		$quoteObj->setCustomerNote ( __ ( 'Imported from DHL Allyouneed' ) . ' (' . __ ( 'Delivery Method' ) . ':' . (( string ) $order->getDeliveryMethod ()) . ')' );
		$quoteObj->setCustomerFirstname ( $customer->getFirstname () );
		$quoteObj->setCustomerLastname ( $customer->getLastname () );
		$quoteObj->setCustomerIsGuest ( Mage::getStoreConfig ( self::USE_GUEST_ACCOUNT_CONFIG ) );
		$quoteObj->setCustomerEmail ( $customer->getEmail () );
		$quoteObj->setCustomer ( $customer );
		
		$hasNoConfigurables = true;
		$quoteItems = array ();
		
		$billingAddress = $quoteObj->getBillingAddress ();
		$billingAddress->addData ( $this->_getAddressData ( $order->getBillingAddress () ) );
		$shippingAddress = $quoteObj->getShippingAddress ();
		if ($order->getDeliveryAddress () != null) {
			$shippingAddress->addData ( $this->_getAddressData ( $order->getDeliveryAddress () ) );
		} else {
			$shippingAddress->addData ( $this->_getAddressData ( $order->getBillingAddress () ) );
		}
		
		$taxRequest = $taxCalculation->getRateRequest ( $shippingAddress, $billingAddress, $customer, $store );
		
		foreach ( $order->getEntries () as $orderEntry ) {
			/* var $orderEntry Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order_Entry */
			$productId = ( string ) $orderEntry->getProductId ();
			
			if ($hasNoConfigurables) {
				try {
					/* @var $productObj Mage_Catalog_Model_Product */
					$productObj = Mage::getModel ( 'catalog/product' )->setStoreId ( $storeId )->load ( $productId );
					
					if ($productObj->isConfigurable ()) {
						$hasNoConfigurables = false;
						break;
					}
					
					// check wether product is in stock
					$stockItem = $productObj->getStockItem ();
					
					if ($productObj->getStatus () == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
						$this->_disabledProductOrders [] = $order->getOrderId ();
						return self::DISABLED_ORDER_STATUS;
					} elseif (( bool ) $stockItem->getData ( 'is_in_stock' )) {
						
						if (floatval ( $productObj->getPrice () ) != floatval ( $orderEntry->getBasePrice () )) {
							$message = sprintf ( __ ( '%s was ordered with a different price (%s instead of current price %s). Was the price changed after exporting to Allyouneed?' ), $productObj->getName (), $orderEntry->getBasePrice (), $productObj->getPrice () );
							Mage::getSingleton ( 'adminhtml/session' )->addNotice ( $message );
						}
						
						$item = $quoteObj->addProduct ( $productObj, new Varien_Object ( $request = array (
								'qty' => ( string ) $orderEntry->getQuantity (),
								'dhl_mein_paket_item_id' => ( string ) $orderEntry->getMeinPaketId () 
						) ) );
						/* @var $item Mage_Sales_Model_Quote_Item|string */
						
						if (is_object ( $item )) {
							$taxRequest->setProductClassId ( $item->getProduct ()->getTaxClassId () );
							$percent = $taxCalculation->getRate ( $taxRequest );
							if ($percent <= 0) {
								$percent = 19;
							}
							
							if ($priceIncludesTax) {
								$customPrice = $orderEntry->getBasePrice ();
							} else {
								$customPrice = $this->_dataHelper->priceWithoutTax ( $orderEntry->getBasePrice (), $percent );
							}
							
							/* @var $item Mage_Sales_Model_Quote_Item */
							$item->setStoreId ( $storeId );
							$item->setCustomPrice ( $customPrice );
							$item->setOriginalCustomPrice ( $customPrice );
							$item->getProduct ()->setIsSuperMode ( true );
						} else {
							Mage::log ( $item );
							Mage::getSingleton ( 'adminhtml/session' )->addWarning ( $item );
							return self::INVALID_PRODUCT_STATUS;
						}
					} else {
						$this->_outOfStockOrders [] = $order->getOrderId ();
						return self::OUT_OF_STOCK_ORDER_STATUS;
					}
				} catch ( Exception $e ) {
					Mage::logException ( $e );
					throw new Exception ( 'Could not add product ' . $productId . ' - ' . $e->getMessage () );
				}
			}
		}
		
		if (! $hasNoConfigurables) {
			$quoteObj->delete ();
			return 0;
		}
		
		if (sizeof ( $quoteItems ) > 0) {
			foreach ( $quoteItems as $quoteItem ) {
			}
		}
		
		if ($shippingIncludesTax) {
			$deliveryCosts = $orderEntry->getBasePrice ();
		} else {
			$deliveryCosts = $this->_dataHelper->priceWithoutTax ( $order->getTotalDeliveryCosts (), "19" );
		}
		
		$rate = $this->calculateRate ( $order );
		
		$shippingAddress->setCollectShippingRates ( false );
		$shippingAddress->addShippingRate ( $rate );
		$shippingAddress->setShippingMethod ( $rate->getCode () );
		
		$shippingAddress->setBaseShippingAmount ( $deliveryCosts );
		$shippingAddress->setShippingAmount ( $deliveryCosts );
		$shippingAddress->setPaymentMethod ( $paymentMethod );
		
		$quoteObj->getPayment ()->importData ( array (
				'method' => $paymentMethod 
		) );
		
		// Dhl_MeinPaketCommon_Model_Carrier_Meinpaket::unlock ();
		// Dhl_MeinPaketCommon_Model_Carrier_Meinpaket::setDeliveryCosts ( $order->getTotalDeliveryCosts () );
		
		// Required for Firegento_MageSetup
		/* @var $checkoutSession Mage_Checkout_Model_Session */
		$checkoutSession = Mage::getSingleton ( 'checkout/session' );
		$checkoutSession->replaceQuote ( $quoteObj );
		
		$quoteObj->setTotalsCollectedFlag ( false )->collectTotals ();
		$quoteObj->setGrandTotal ( $order->getTotalPrice () );
		$quoteObj->save ();
		
		// Required for Firegento_MageSetup
		$checkoutSession->clear ();
		
		/* @var $quoteObj Mage_Sales_Model_Service_Quote */
		$serviceQuote = Mage::getModel ( 'sales/service_quote', $quoteObj );
		$serviceQuote->submitAll ();
		
		/* @var $orderModel Mage_Sales_Model_Order */
		$orderModel = $serviceQuote->getOrder ();
		/**
		 * Triggert Aufruf von authorize() auf dem payment model (ggf auch capture() )
		 */
		
		$contactData = $order->getContactData ();
		
		$orderModel->place ();
		$orderModel->setCreatedAt ( $order->getOrderDate () );
		$orderModel->setUpdatedAt ( $order->getLastModificationDate () );
		$orderModel->setState ( Mage_Sales_Model_Order::STATE_PROCESSING, true );
		$orderModel->setData ( 'customer_email', strlen ( $contactData->getEmail () ) ? $contactData->getEmail () : Mage::getStoreConfig ( 'meinpaket/customer/default_email' ) );
		$orderModel->setData ( 'ext_customer_id', $contactData->getCustomerId () );
		$orderModel->setData ( 'dhl_mein_paket_order_id', $order->getOrderId () );
		// Zend_Debug::dump($orderModel->debug());die;
		$orderModel->save ();
		
		// Dhl_MeinPaketCommon_Model_Carrier_Meinpaket::unlock ();
		
		// dispatch event
		Mage::dispatchEvent ( 'dhl_meinpaket_orderimport', array (
				'orderId' => $orderModel->getId () 
		) );
		
		$this->createInvoice ( $orderModel );
		
		return self::IMPORTED_ORDER_STATUS;
	}
	
	/**
	 * Extracts address data from the given element.
	 *
	 * @param SimpleXMLElement $addressElement        	
	 * @return array
	 */
	protected function _getAddressData(Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address $address) {
		$addressArray = array (
				'salutation' => ( string ) $address->getSalutation (),
				'firstname' => ( string ) $address->getFirstName (),
				'lastname' => ( string ) $address->getLastName (),
				
				// 'street' => ( string ) $address->getStreet (),
				'postcode' => ( string ) $address->getZipCode (),
				'city' => ( string ) $address->getCity (),
				'region_id' => '0', // TODO: make this optional - and add in documentation
				'country_id' => strtoupper ( $address->getCountry () ),
				'telephone' => '0000' 
		);
		
		if (strlen ( $address->getCustomerId () )) {
			$addressArray ['street'] = ( string ) $address->getCustomerId () . "\n" . ( string ) $address->getStreet () . " " . ( string ) $address->getHouseNumber () . "\n" . ( string ) $address->getAddressAddition ();
		} else {
			$addressArray ['street'] = ( string ) $address->getStreet () . " " . ( string ) $address->getHouseNumber () . "\n" . ( string ) $address->getAddressAddition ();
		}
		
		if (strlen ( $address->getCompany () )) {
			$addressArray ['company'] = ( string ) $address->getCompany ();
		}
		
		return $addressArray;
	}
	
	/**
	 * Converts the given ISO date string (i.e.
	 * "2010-10-27T15:59:59.012+02:00")
	 * into the mysql datetime format (i.e. "2010-10-27 15:59:59").
	 *
	 * @param string $isoDateString
	 *        	@wins Code of the year contest
	 * @return string
	 * @deprecated
	 *
	 */
	protected function _getFormattedDateString($isoDateString) {
		$date = new Zend_Date ( $isoDateString, Zend_Date::ISO_8601 );
		$mysqlDateString = $date->toString ( 'YYYY-MM-dd HH:mm:ss' );
		return $mysqlDateString;
	}
	
	/**
	 * Get or create customer.
	 *
	 * @param array $order
	 *        	to check
	 * @return Mage_Customer_Model_Customer
	 */
	protected function getOrCreateCustomer(Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order $order) {
		/* @var $customer Mage_Customer_Model_Customer */
		$customer = Mage::getModel ( 'customer/customer' )->getCollection ()->addAttributeToSelect ( 'meinpaket_buyer_id' )->addAttributeToFilter ( 'meinpaket_buyer_id', $order->getContactData ()->getCustomerId () )->load ()->getFirstItem ();
		
		if ($customer->getId () != null) {
			return $customer;
		}
		
		// Set store and website for loadByEmail.
		$customer->setStore ( $this->_dataHelper->getMeinPaketStore () );
		// Could not find customer by meinpaket_buyer_id. As there can only be one customer for a given
		// email try to load one.
		$customer->loadByEmail ( $order->getContactData ()->getEmail () );
		
		if ($customer->getId () != null) {
			return $customer;
		}
		
		// New customer
		// Set store and website again after loadByEmail reset it.
		$customer->setStore ( $this->_dataHelper->getMeinPaketStore () );
		$customer->setFirstname ( $order->getBillingAddress ()->getFirstName () );
		$customer->setLastname ( $order->getBillingAddress ()->getLastName () );
		$customer->setEmail ( $order->getContactData ()->getEmail () );
		
		$groupId = Mage::getStoreConfig ( self::CUSTOMER_GROUP_CONFIG );
		$customer->setData ( 'group_id', $groupId );
		
		$customer->setData ( 'meinpaket_buyer_id', $order->getContactData ()->getCustomerId () );
		
		if (! $customer->getId ()) {
			// recurring customer
			$customer->setPasswordHash ( $customer->generatePassword () );
		}
		
		return $customer;
	}
	
	/**
	 * Create invoice for order.
	 *
	 * @param Mage_Sales_Model_Order $order        	
	 */
	public function createInvoice(Mage_Sales_Model_Order $order) {
		/* @var $invoiceModel Sales_Model_Order_Invoice */
		$invoice = $order->prepareInvoice ();
		
		$invoice->setRequestedCaptureCase ( Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE );
		$invoice->register ();
		$invoice->setIsInProcess ( true );
		
		$transactionSave = Mage::getModel ( 'core/resource_transaction' )->addObject ( $invoice )->addObject ( $invoice->getOrder () )->save ();
	}
	
	/**
	 * Calculate carrier rate.
	 *
	 * @param Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order $order        	
	 * @return Mage_Sales_Model_Quote_Address_Rate
	 */
	protected function calculateRate(Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Order $order) {
		$method = Mage::getStoreConfig ( self::SHIPPING_METHOD_CONFIG );
		$parts = explode ( "_", $method, 2 );
		
		$result = Mage::getModel ( 'sales/quote_address_rate' );
		/* @var $result Mage_Sales_Model_Quote_Address_Rate */
		
		$result->setCarrierTitle ( 'Allyouneed' )->setCode ( $method )->setCarrier ( $parts [0] )->setMethod ( $parts [1] )->setCost ( $order->getTotalDeliveryCosts () )->setPrice ( $order->getTotalDeliveryCosts () );
		
		return $result;
	}
}
