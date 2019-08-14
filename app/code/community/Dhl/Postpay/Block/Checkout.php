<?php
/**
 * Paypal expess checkout shortcut link
 */
class Dhl_Postpay_Block_Checkout extends Mage_Core_Block_Template {
	/**
	 * Position of "OR" label against shortcut
	 */
	const POSITION_BEFORE = 'before';
	const POSITION_AFTER = 'after';
	
	/**
	 * Whether the block should be eventually rendered
	 *
	 * @var bool
	 */
	protected $_shouldRender;
	
	public function __construct() {
		parent::__construct();
		$this->_shouldRender = Mage::helper('postpay/data')->isActive();
	}
	
	/**
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _beforeToHtml() {
		$result = parent::_beforeToHtml ();
		$quote = Mage::helper ( 'meinpaketcommon/data' )->getQuoteFiltered ();
		
		// validate minimum quote amount and validate quote for zero grandtotal
		if (null == $quote || ! $quote->validateMinimumAmount ()) {
			$this->_shouldRender = false;
			return $result;
		}
		
		return $result;
	}
	public function getCheckoutUrl() {
		return Mage::getUrl ( 'postpay/checkout' );
	}
	public function getImageUrl() {
		return $this->getSkinUrl ( 'images/postpay/button.png' );
	}
	
	/**
	 * Render the block if needed
	 *
	 * @return string
	 */
	protected function _toHtml() {
		if (! $this->_shouldRender) {
			return '';
		}
		return parent::_toHtml ();
	}
	
	/**
	 * Check is "OR" label position before checkout
	 *
	 * @return bool
	 */
	public function isOrPositionBefore() {
		return $this->getShowOrPosition () && $this->getShowOrPosition () == self::POSITION_BEFORE;
	}
	
	/**
	 * Check is "OR" label position after checkout
	 *
	 * @return bool
	 */
	public function isOrPositionAfter() {
		return $this->getShowOrPosition () && $this->getShowOrPosition () == self::POSITION_AFTER;
	}
}
