<?php
class Dhl_MeinPaket_Block_Adminhtml_Backlog_Product extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * Constructor for Cron Adminhtml Block
	 */
	public function __construct() {
		$this->_blockGroup = 'meinpaket';
		$this->_controller = 'adminhtml_backlog_product';
		$this->_headerText = Mage::helper ( 'meinpaket' )->__ ( 'Backlog' );
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
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Synchronize Async Jobs' ),
				'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/run', array (
						'cronjob' => Dhl_MeinPaketCommon_Model_Cron::SYNC_ASYNC 
				) ) . '\')',
				'class' => 'add' 
		) );
		
		$this->_addButton ( 'run_catalog', array (
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Synchronize Productlist' ),
				'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/run', array (
						'cronjob' => Dhl_MeinPaket_Model_Cron::SYNC_CATALOG 
				) ) . '\')',
				'class' => 'add' 
		) );
		
		$this->_addButton ( 'run_orders', array (
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Import Orders' ),
				'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/run', array (
						'cronjob' => Dhl_MeinPaketCommon_Model_Cron::SYNC_ORDERS 
				) ) . '\')',
				'class' => 'add' 
		) );
		
		$this->_addButton ( 'schedule_jobs', array (
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Schedule Jobs' ),
				'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/schedule', array (
						'cronjob' => 'all' 
				) ) . '\')',
				'class' => 'add' 
		) );
		
		// $this->_addButton ( 'schedule_catalog', array (
		// 'label' => Mage::helper ( 'meinpaket' )->__ ( 'Schedule Catalog' ),
		// 'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/schedule', array (
		// 'cronjob' => Dhl_MeinPaket_Model_Cron::SYNC_CATALOG
		// ) ) . '\')',
		// 'class' => 'add'
		// ) );
		
		// $this->_addButton ( 'schedule_orders', array (
		// 'label' => Mage::helper ( 'meinpaket' )->__ ( 'Schedule Orders' ),
		// 'onclick' => 'setLocation(\'' . $this->getUrl ( 'adminhtml/meinpaket_backlog_product/schedule', array (
		// 'cronjob' => Dhl_MeinPaketCommon_Model_Cron::SYNC_ORDERS
		// ) ) . '\')',
		// 'class' => 'add'
		// ) );
		
		return parent::_prepareLayout ();
	}
}