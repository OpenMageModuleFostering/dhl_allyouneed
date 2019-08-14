<?php

/**
 * Exception which is thrown when a HTTP request returned an error.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Model_Client
 * @version		$Id$
 */
class Dhl_MeinPaketCommon_Model_Client_BadHttpReturnCodeException extends Dhl_MeinPaketCommon_Model_Client_HttpException {
	/**
	 *
	 * @var integer
	 */
	protected $httpReturnCode;
	
	/**
	 * Constructor.
	 *
	 * @param integer $httpReturnCode        	
	 * @param string $message        	
	 * @return void
	 */
	public function __construct($httpReturnCode, $message = '') {
		parent::__construct ( $message );
		
		$this->httpReturnCode = $httpReturnCode;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getHttpReturnCode() {
		return $this->httpReturnCode;
	}
}
