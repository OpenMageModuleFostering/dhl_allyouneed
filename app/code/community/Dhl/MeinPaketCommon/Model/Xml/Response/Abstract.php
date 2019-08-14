<?php

/**
 * Result class which keeps information 
 *
 * @category	Mage
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service
 * @version		$Id$
 */
abstract class Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	
	/**
	 *
	 * @var array
	 */
	protected $commonErrors = array ();
	
	/**
	 * domElement this class represents.
	 *
	 * @var DOMElement
	 */
	protected $domElement;
	
	/**
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		$this->domElement = $domElement;
		foreach ( $domElement->childNodes as $child ) {
			if ($child->localName == 'error') {
				$code = null;
				$message = null;
				$object = null;
				foreach ( $child->childNodes as $errorChild ) {
					switch ($errorChild->localName) {
						case 'categoryDeletion' :
						case 'category' :
							$object = $errorChild->getAttribute ( 'code' );
							break;
						case 'productDeletion' :
						case 'productDescription' :
						case 'productOffer' :
							$object = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_ProductId ( $errorChild );
							break;
						case 'internationalPrice' :
						case 'internationalPriceDeletion' :
							$productId = null;
							$location = null;
							
							foreach ( $errorChild->childNodes as $internationalPriceError ) {
								switch ($internationalPriceError->localName) {
									case 'productId' :
										$productId = $internationalPriceError->nodeValue;
										break;
									case 'country' :
										$location = $internationalPriceError->nodeValue;
										break;
									case 'deliveryZone' :
										$location = $internationalPriceError->getAttribute ( 'name' );
										break;
								}
							}
							
							$object = $productId . ' ' . $location;
							break;
						case 'variantGroup' :
							$object = $errorChild->getAttribute ( 'code' );
							break;
						case 'error-code' :
							$code = $errorChild->nodeValue;
							break;
						case 'error-message' :
							$message = $errorChild->nodeValue;
							break;
					}
				}
				$this->addCommonError ( $code, $message, $object );
			}
		}
	}
	
	/**
	 * Adds a common error which is not product related.
	 *
	 * @param string $code        	
	 * @param string $message        	
	 * @param string $object        	
	 */
	public function addCommonError($code, $message, $object = null) {
		// check if error already exists
		if (sizeof ( $this->commonErrors ) > 0) {
			foreach ( $this->commonErrors as $error ) {
				if ($error ['code'] === $code && $error ['message'] === $message && $error ['object'] == $object) {
					return;
				}
			}
		}
		
		// add error
		$this->commonErrors [] = array (
				'code' => $code,
				'message' => $message,
				'object' => $object 
		);
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function hasErrors() {
		return (sizeof ( $this->commonErrors ) > 0);
	}
	
	/**
	 * Returns an array of common (not product related) errors.
	 * The elements of result array have the following structure:
	 * [
	 * 'code' => '...',
	 * 'message' => '...'
	 * ]
	 *
	 * @return array
	 */
	public function getCommonErrors() {
		return $this->commonErrors;
	}
	
	/**
	 * Return error messages as string.
	 *
	 * @return string
	 */
	public function getErrorString() {
		$result = "";
		foreach ( $this->commonErrors as $message ) {
			$result = $message ['object'] . " " . $message ['message'] . " " . $message ['code'] . "\n";
		}
		return $result;
	}
	
	/**
	 * Process response
	 */
	public function process() {
	}
}