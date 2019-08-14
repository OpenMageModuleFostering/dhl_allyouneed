<?php
/**
 * PayPal Standard payment "form"
 */
class Dhl_Postpay_Block_Payment_Form_Standard extends Mage_Payment_Block_Form {
	public function __construct() {
		parent::__construct ();
		
		$image = Mage::getConfig ()->getBlockClassName ( 'core/template' );
		$image = new $image ();
		$image->setTemplate ( 'postpay/checkout/image.phtml' );
		
		$this->setTemplate ( 'postpay/payment/redirect.phtml' )->setRedirectMessage ( Mage::helper ( 'postpay' )->__ ( 'You will be redirected to the Postpay website when you place an order.' ) )->setMethodTitle ( '' )->setMethodLabelAfterHtml ( $image->toHtml () );
	}
}
