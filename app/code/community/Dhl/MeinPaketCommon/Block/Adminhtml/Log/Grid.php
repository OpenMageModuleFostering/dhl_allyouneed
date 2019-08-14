<?php
class Dhl_MeinPaketCommon_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'meinpaketcommon_log_grid' );
		$this->setDefaultSort ( 'log_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setUseAjax ( true );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'meinpaketcommon/log' )->getCollection ();
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'log_id', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'ID' ),
				'type' => 'number',
				'index' => 'log_id' 
		) );
		
		$this->addColumn ( 'request_id', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Request ID' ),
				'index' => 'request_id' 
		) );
		
		$this->addColumn ( 'url', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'URL' ),
				'index' => 'url' 
		) );
		
		$this->addColumn ( 'send', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Send' ),
				'index' => 'send' 
		) );
		
		$this->addColumn ( 'received', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Received' ),
				'index' => 'received' 
		) );
		
		$this->addColumn ( 'error', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Error' ),
				'index' => 'error' 
		) );
		
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Created At' ),
				'type' => 'datetime',
				'index' => 'created_at' 
		) );
		
		$this->addExportType ( '*/*/exportExcel', Mage::helper ( 'meinpaketcommon' )->__ ( 'Excel XML' ) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'log_id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'logIds' );
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( 'adminhtml/meinpaketcommon_log/massDelete', array () ),
				'confirm' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Are you sure?' ) 
		) );
		
		return $this;
	}
	public function getGridUrl() {
		return $this->getUrl ( 'adminhtml/meinpaketcommon_log/grid', array (
				'_current' => true 
		) );
	}
}