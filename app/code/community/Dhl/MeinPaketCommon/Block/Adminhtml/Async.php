<?php
class Dhl_MeinPaketCommon_Block_Adminhtml_Async extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * Constructor for Cron Adminhtml Block
	 */
	public function __construct() {
		$this->_blockGroup = 'meinpaketcommon';
		$this->_controller = 'adminhtml_async';
		$this->_headerText = Mage::helper ( 'meinpaketcommon' )->__ ( 'Async' );
		parent::__construct ();
	}
	/**
	 * Prepare layout
	 *
	 * @return Dhl_MeinPaket_Block_Adminhtml_Backlog_Product_Grid_Container
	 */
	protected function _prepareLayout() {
		$this->removeButton ( 'add' );
		
		$this->_addButton ( 'run_async', array (
				'label' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Synchronize Async Jobs' ),
				'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaketcommon_async/run', array (
						'cronjob' => Dhl_MeinPaketCommon_Model_Cron::SYNC_ASYNC 
				) ) . '\')',
				'class' => 'add' 
		) );
		
		return parent::_prepareLayout ();
	}
}