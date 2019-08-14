<?php

/**
 * Partial which represents the 'submitCartRequest' element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_SubmitCartRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	const EU_MIN_STANDARD_TAX = 15;
	const POSTPAY_TAX_FREE = 'Free';
	const POSTPAY_TAX_REDUCED = 'Reduced';
	const POSTPAY_TAX_STANDARD = 'Standard';
	
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
		$this->node = $this->getDocument ()->createElement ( 'submitCartRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_CHECKOUT );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 * Add cart.
	 *
	 * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $order        	
	 * @param Dhl_Paypal_Model_Cart $cart        	
	 * @return string
	 */
	public function addCart($order, $cart) {
		if ($order == null) {
			return;
		}
		
		$shoppingCartNode = $this->getDocument ()->createElement ( 'shoppingCart' );
		$cartIdnode = $this->getDocument ()->createElement ( 'cartId', ( string ) $cart->getId () );
		$shoppingCartNode->appendChild ( $cartIdnode );
		
		foreach ( $order->getAllVisibleItems () as $item ) {
			/* @var $item Mage_Sales_Model_Order_Item */
			$this->addCartItem ( $shoppingCartNode, $item );
		}
		
		if ($order instanceof Mage_Sales_Model_Order) {
			$this->addCustomerData ( $shoppingCartNode, $order );
		}
		
		if ($order->hasShippingInclTax ()) {
			// If shipping availabe -> no quote
			$shippingCostNode = $this->getDocument ()->createElement ( "shippingCost", $order->getShippingInclTax () );
			$shoppingCartNode->appendChild ( $shippingCostNode );
		}
		
		$params = array (
				'_forced_secure' => true 
		);
		
		if ($order instanceof Mage_Sales_Model_Order) {
			$params ['order'] = $order->getIncrementId ();
		}
		
		$redirectURLSuccessNode = $this->getDocument ()->createElement ( "redirectURLSuccess", Mage::getUrl ( 'postpay/response/success', $params ) );
		$shoppingCartNode->appendChild ( $redirectURLSuccessNode );
		
		$redirectURLErrorNode = $this->getDocument ()->createElement ( "redirectURLError", Mage::getUrl ( 'postpay/response/error', $params ) );
		$shoppingCartNode->appendChild ( $redirectURLErrorNode );
		
		$redirectURLBackNode = $this->getDocument ()->createElement ( "redirectURLBack", Mage::getUrl ( 'postpay/response/back', $params ) );
		$shoppingCartNode->appendChild ( $redirectURLBackNode );
		
		$notificationIdNode = $this->getDocument ()->createElement ( "notificationId", $cart->getNotificationId () );
		$shoppingCartNode->appendChild ( $notificationIdNode );
		
		$this->getDocumentElement ()->appendChild ( $shoppingCartNode );
		
		return $cart->getNotificationId ();
	}
	
	/**
	 * Add cart item.
	 *
	 * @param DOMElement $shoppingCartNode        	
	 * @param Mage_Sales_Model_Order_Item|Mage_Sales_Model_Quote_Item $item        	
	 */
	protected function addCartItem(DOMElement $shoppingCartNode, $item) {
		if (! Mage::helper ( 'meinpaketcommon/data' )->checkItem ( $item )) {
			return;
		}
		
		$shoppingCartItemNode = $this->getDocument ()->createElement ( 'shoppingCartItem' );
		$productIdNode = $this->getDocument ()->createElement ( 'productId', $item->getProductId () );
		$shoppingCartItemNode->appendChild ( $productIdNode );
		
		$shoppingCartItemNode->appendChild ( $this->getCDATANode ( 'name', $item->getName () ) );
		$basePriceNode = $this->getDocument ()->createElement ( 'basePrice', $item->getBasePriceInclTax () );
		$shoppingCartItemNode->appendChild ( $basePriceNode );
		$taxNode = $this->getDocument ()->createElement ( 'tax' );
		if ($item->getTaxPercent () <= 0) {
			$taxNode->nodeValue = self::POSTPAY_TAX_FREE;
		} else if ($item->getTaxPercent () >= self::EU_MIN_STANDARD_TAX) {
			$taxNode->nodeValue = self::POSTPAY_TAX_STANDARD;
		} else {
			$taxNode->nodeValue = self::POSTPAY_TAX_REDUCED;
		}
		$shoppingCartItemNode->appendChild ( $taxNode );
		$quantityNode = $this->getDocument ()->createElement ( 'quantity', $item->getQty () ? $item->getQty () : $item->getQtyOrdered () );
		$shoppingCartItemNode->appendChild ( $quantityNode );
		
		$shoppingCartNode->appendChild ( $shoppingCartItemNode );
		$this->setHasData ( true );
	}
	
	/**
	 * Add customer address
	 *
	 * @param DOMElement $shoppingCartNode        	
	 * @param Mage_Sales_Model_Order $order        	
	 */
	protected function addCustomerData(DOMElement $shoppingCartNode, Mage_Sales_Model_Order $order) {
		$customerDataNode = $this->getDocument ()->createElement ( 'customerData' );
		
		$emailNode = $this->getCDATANode ( 'email', $order->getCustomerEmail () );
		$customerDataNode->appendChild ( $emailNode );
		
		if ($order->getShippingAddressId () != $order->getBillingAddressId ()) {
			$deliveryAddressNode = $this->getDocument ()->createElement ( 'deliveryAddress' );
			$this->addCustomerAddressFields ( $deliveryAddressNode, $order->getShippingAddress () );
			$customerDataNode->appendChild ( $deliveryAddressNode );
		}
		
		$billingAddressNode = $this->getDocument ()->createElement ( 'billingAddress' );
		$this->addCustomerAddressFields ( $billingAddressNode, $order->getBillingAddress () );
		$customerDataNode->appendChild ( $billingAddressNode );
		
		$shoppingCartNode->appendChild ( $customerDataNode );
	}
	
	/**
	 * Add customer address fields.
	 *
	 * @param DOMElement $addressNode        	
	 * @param Mage_Sales_Model_Order_Address $address        	
	 */
	protected function addCustomerAddressFields(DOMElement $addressNode, Mage_Sales_Model_Order_Address $address) {
		$streetHouseNumber = Mage::helper ( 'meinpaketcommon/address' )->parseStreetHouseNumber ( $address->getStreet1 () );
		
		// The salutation if provided, i.e .Herr/Frau or Mr./Mrs
		switch ($address->getPrefix ()) {
			case 'Frau' :
				$salutation = 'Frau';
				break;
			case 'Mrs' :
				$salutation = 'Mrs';
				break;
			case 'Mr' :
			case 'Mr.' :
				$salutation = 'Mr.';
				break;
			default :
				$salutation = 'Herr';
				break;
		}
		
		$addressNode->appendChild ( $this->getCDATANode ( 'salutation', $salutation ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'firstName', $address->getFirstname () ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'lastName', $address->getLastname () ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'street', $streetHouseNumber ["street"] ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'houseNumber', $streetHouseNumber ["houseNumber"] ) );
		if (strlen ( $address->getStreet2 () )) {
			$addressNode->appendChild ( $this->getCDATANode ( 'addressAddition', $address->getStreet2 () ) );
			if (strlen ( $address->getStreet3 () )) {
				$addressNode->appendChild ( $this->getCDATANode ( 'addressAddition', $address->getStreet3 () ) );
				if (strlen ( $address->getStreet4 () )) {
					$addressNode->appendChild ( $this->getCDATANode ( 'addressAddition', $address->getStreet4 () ) );
				}
			}
		}
		$addressNode->appendChild ( $this->getCDATANode ( 'zipCode', $address->getPostcode () ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'city', $address->getCity () ) );
		$addressNode->appendChild ( $this->getCDATANode ( 'country', $address->getCountry () ) );
	}
}
