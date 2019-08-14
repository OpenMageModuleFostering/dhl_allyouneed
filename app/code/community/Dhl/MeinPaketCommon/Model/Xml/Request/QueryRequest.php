<?php

/**
 * Partial which represents the 'queryRequest' element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_QueryRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	
	/**
	 *
	 * @var DOMElement
	 */
	protected $orders;
	
	/**
	 *
	 * @var DOMElement
	 */
	protected $order_externalIds;
	
	/**
	 *
	 * @var DOMElement
	 */
	protected $shoppingCartStatus;
	
	/**
	 *
	 * @var DOMElement
	 */
	protected $shoppingCartStatus_externalIds;
	
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
		$this->node = $this->getDocument ()->createElement ( 'queryRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_ORDERS );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 * Add order request.
	 *
	 * @param string $from        	
	 * @param string $to        	
	 * @param string $status        	
	 */
	public function addOrders($from = null, $to = null, $status = null) {
		$this->_initializeOrder ();
		$this->setHasData ( true );
		
		if ($from != null) {
			$dateFromNode = $this->getDocument ()->createElement ( "dateFrom", $this->getFormatedDate ( $from ) );
			$this->orders->appendChild ( $dateFromNode );
		} else {
			// One week ago
			/* @var $date Zend_Date */
			$date = Zend_Date::now ();
			$date->subWeek ( 1 );
			$date->setHour ( 0 );
			$date->setMinute ( 0 );
			$date->setMilliSecond ( 0 );
			$dateFromNode = $this->getDocument ()->createElement ( "dateFrom", $date->toString ( Zend_Date::ISO_8601 ) );
			$this->orders->appendChild ( $dateFromNode );
		}
		
		if ($to != null) {
			$dateToNode = $this->getDocument ()->createElement ( "dateTo", $this->getFormatedDate ( $to ) );
			$this->orders->appendChild ( $dateToNode );
		}
		
		if ($status != null) {
			$statusNode = $this->getDocument ()->createElement ( "orderStatus", $status );
			$this->orders->appendChild ( $statusNode );
		}
	}
	
	/**
	 * Shopping Cart Status.
	 *
	 * @param
	 *        	string cartId
	 */
	public function addShoppingCartStatus($cartId) {
		$this->setHasData ( true );
		if ($this->shoppingCartStatus == null) {
			$this->shoppingCartStatus = $this->getDocument ()->createElement ( 'shoppingCartStatus' );
			$this->getDocumentElement ()->appendChild ( $this->shoppingCartStatus );
		}
		
		if ($this->shoppingCartStatus_externalIds == null) {
			$this->shoppingCartStatus_externalIds = $this->getDocument ()->createElement ( 'externalIds' );
			$this->shoppingCartStatus->appendChild ( $this->shoppingCartStatus_externalIds );
		}
		
		$externalId = $this->getDocument ()->createElement ( "externalId", $cartId );
		$this->shoppingCartStatus_externalIds->appendChild ( $externalId );
	}
	
	/**
	 * Shopping Cart Status.
	 *
	 * @param
	 *        	string cartId
	 */
	public function addOrderExternalId($cartId) {
		$this->setHasData ( true );
		$this->_initializeOrder ();
		
		if ($this->order_externalIds == null) {
			$this->order_externalIds = $this->getDocument ()->createElement ( 'externalIds' );
			$this->orders->appendChild ( $this->order_externalIds );
		}
		
		$externalId = $this->getDocument ()->createElement ( "externalId", $cartId );
		$this->order_externalIds->appendChild ( $externalId );
	}
	protected function _initializeOrder() {
		if ($this->orders == null) {
			$this->orders = $this->getDocument ()->createElement ( 'orders' );
			$this->getDocumentElement ()->appendChild ( $this->orders );
			$this->orders->setAttribute ( 'getEmail', 'true' );
			$this->orders->setAttribute ( 'additionalInfos', true );
		}
	}
}
