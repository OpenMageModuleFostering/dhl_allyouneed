<?php

/**
 * Abstract base class of all partials.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Xml
 * @version		$Id$
 */
abstract class Dhl_MeinPaketCommon_Model_Xml_AbstractXmlRequest extends Dhl_MeinPaketCommon_Model_Xml_AbstractXmlPartial {
	// const XMLNS = 'http://www.meinpaket.de/xsd/dietmar/1.0/products';
	const XMLNS_COMMON = 'http://www.meinpaket.de/xsd/dietmar/1.0/common';
	const XMLNS_DATA = 'http://www.meinpaket.de/xsd/dietmar/1.0/data';
	const XMLNS_PRODUCTS = 'http://www.meinpaket.de/xsd/dietmar/1.0/products';
	const XMLNS_ORDERS = 'http://www.meinpaket.de/xsd/dietmar/1.0/orders';
	const XMLNS_CHECKOUT = 'http://www.meinpaket.de/xsd/dietmar/1.0/checkout';
	const XMLNS_ASYNCHRONOUS = 'http://www.meinpaket.de/xsd/dietmar/1.0/asynchronous';
	
	/**
	 * The XML-version of the generated XML.
	 *
	 * @var string
	 */
	const XML_VERSION = '1.0';
	
	/**
	 * The charset which is set in the XML prologue of the generated XML.
	 *
	 * @var string
	 */
	const XML_CHARSET = 'UTF-8';
	
	/**
	 * Has data been added
	 *
	 * @var boolean
	 */
	protected $hasData = false;
	
	/**
	 * Default constructor.
	 *
	 * @param DOMDocument $document        	
	 */
	public function __construct(DOMDocument $document = null) {
		parent::__construct ( $document === null ? new DOMDocument ( self::XML_VERSION, self::XML_CHARSET ) : $document );
		$this->createDocumentElement ();
		$this->addHeader ();
	}
	public function addHeader() {
		$username = Mage::getStoreConfig ( 'meinpaket/credentials/username' );
		$passwordCrypted = Mage::getStoreConfig ( 'meinpaket/credentials/password' );
		$password = Mage::helper ( 'core' )->decrypt ( $passwordCrypted );
		
		if (! is_string ( $username ) || ! is_string ( $password ) || strlen ( $username ) <= 0 || strlen ( $password ) <= 0) {
			throw new Dhl_MeinPaketCommon_Model_Xml_XmlBuildException ( 'No authentication parameters set.' );
		}
		
		$headerNode = $this->getDocument ()->createElement ( 'common:header' );
		$usernameNode = $this->getDocument ()->createElement ( 'common:login' );
		$passwordNode = $this->getDocument ()->createElement ( 'common:password' );
		$languageNode = $this->getDocument ()->createElement ( 'common:language', 'de' );
		$multiplierIdNode = $this->getDocument ()->createElement ( 'common:multiplierId', 'MAGENTO' );
		
		$usernameNode->appendChild ( $this->getDocument ()->createTextNode ( $username ) );
		$passwordNode->appendChild ( $this->getDocument ()->createTextNode ( $password ) );
		
		$headerNode->appendChild ( $usernameNode );
		$headerNode->appendChild ( $passwordNode );
		$headerNode->appendChild ( $languageNode );
		$headerNode->appendChild ( $multiplierIdNode );
		
		$this->getDocumentElement ()->appendChild ( $headerNode );
	}
	
	/**
	 * Builds the DOM node.
	 *
	 * @return Dhl_MeinPaketCommon_Model_Xml_AbstractXmlPartial
	 */
	public function build() {
		// No need to be build
		return $this;
	}
	
	/**
	 * Is has data set?
	 *
	 * @return boolean
	 */
	public function isHasData() {
		return $this->hasData;
	}
	
	/**
	 * Set has data.
	 *
	 * @param boolean $value        	
	 */
	public function setHasData($value = true) {
		$this->hasData = $value;
	}
	
	/**
	 * Create the root element for the document.
	 *
	 * @return DOMNode
	 */
	abstract public function createDocumentElement();
}
