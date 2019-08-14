<?php

/**
 * Result class which keeps information 
 *
 * @category	Mage
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Service
 * @version		$Id$
 */
abstract class Dhl_MeinPaketCommon_Model_Service_Result_Abstract {
	
	/**
	 *
	 * @var array
	 */
	protected $commonErrors = array ();
	
	/**
	 * Adds a common error which is not product related.
	 *
	 * @param string $code        	
	 * @param string $message        	
	 */
	public function addCommonError($code, $message) {
		// check if error already exists
		if (sizeof ( $this->commonErrors ) > 0) {
			foreach ( $this->commonErrors as $error ) {
				if ($error ['code'] === $code && $error ['message'] === $message) {
					return;
				}
			}
		}
		
		// add error
		$this->commonErrors [] = array (
				'code' => $code,
				'message' => $message 
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
}