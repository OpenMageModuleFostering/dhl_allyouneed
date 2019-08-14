<?php
class Dhl_MeinPaketCommon_Block_Adminhtml_Async_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'meinpaketcommon_async_grid' );
		$this->setDefaultSort ( 'async_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setUseAjax ( true );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'meinpaketcommon/async' )->getCollection ();
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'async_id', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'ID' ),
				'type' => 'number',
				'index' => 'async_id' 
		) );
		
		$this->addColumn ( 'request_id', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Request ID' ),
				'index' => 'request_id' 
		) );
		
		$this->addColumn ( 'status', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Status' ),
				'index' => 'status' 
		) );
		
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Created At' ),
				'type' => 'datetime',
				'index' => 'created_at' 
		) );
		
		$this->addColumn ( 'updated_at', array (
				'header' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Updated At' ),
				'type' => 'datetime',
				'index' => 'updated_at' 
		) );
		
		$this->addExportType ( '*/*/exportExcel', Mage::helper ( 'meinpaketcommon' )->__ ( 'Excel XML' ) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'async_id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'asyncIds' );
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( 'adminhtml/meinpaketcommon_async/massDelete', array () ),
				'confirm' => Mage::helper ( 'meinpaketcommon' )->__ ( 'Are you sure?' ) 
		) );
		
		return $this;
	}
	public function getGridUrl() {
		return $this->getUrl ( 'adminhtml/meinpaketcommon_async/grid', array (
				'_current' => true 
		) );
	}
}