<?php
class Dhl_MeinPaket_Block_Adminhtml_BestPrice extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * Constructor for Cron Adminhtml Block
	 */
	public function __construct() {
		$this->_blockGroup = 'meinpaket';
		$this->_controller = 'adminhtml_bestPrice';
		$this->_headerText = Mage::helper ( 'meinpaket' )->__ ( 'Best Price' );
		parent::__construct ();
	}
	/**
	 * Prepare layout
	 *
	 * @return Dhl_MeinPaket_Block_Adminhtml_Backlog_Product_Grid_Container
	 */
	protected function _prepareLayout() {
		$this->removeButton ( 'add' );
				
		return parent::_prepareLayout ();
	}
}