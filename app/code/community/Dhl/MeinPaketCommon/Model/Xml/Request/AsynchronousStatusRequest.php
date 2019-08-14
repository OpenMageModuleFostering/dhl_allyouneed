<?php

/**
 * Partial which represents the 'downloadRequest' element.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml_Partial
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Xml_Request_AsynchronousStatusRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest {
	/**
	 * RequesdId for this request
	 *
	 * @var unknown
	 */
	protected $requestId;
	
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
		$this->node = $this->getDocument ()->createElement ( 'asynchronousStatusRequest' );
		$this->node->setAttribute ( 'xmlns', self::XMLNS_ASYNCHRONOUS );
		$this->node->setAttribute ( 'xmlns:common', self::XMLNS_COMMON );
		$this->node->setAttribute ( 'version', '1.0' );
		$this->getDocument ()->appendChild ( $this->node );
	}
	
	/**
	 * Creates the request XML for a category structure download request.
	 */
	public function addRequestStatus($requestId, $onlyStatus = 'false') {
		if (strlen ( $requestId ) > 0) {
			$this->requestId = $requestId;
			$getRequestStatusNode = $this->getDocument ()->createElement ( 'getRequestStatus' );
			$this->getDocumentElement ()->appendChild ( $getRequestStatusNode );
			
			$requestIdNode = $this->getDocument ()->createElement ( 'requestId', $requestId );
			$getRequestStatusNode->appendChild ( $requestIdNode );
			
			$onlyStatusNode = $this->getDocument ()->createElement ( 'onlyStatus', $onlyStatus );
			$getRequestStatusNode->appendChild ( $onlyStatusNode );
			
			$this->setHasData ( true );
		}
		return $this;
	}
	public function getRequestId() {
		return $this->requestId;
	}
}
