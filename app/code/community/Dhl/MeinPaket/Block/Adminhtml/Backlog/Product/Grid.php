<?php
class Dhl_MeinPaket_Block_Adminhtml_Backlog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'meinpaket_backlog_product_grid' );
		$this->setDefaultSort ( 'backlog_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setUseAjax ( true );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		/* @var $model Dhl_MeinPaket_Model_Backlog_Product */
		$model = Mage::getModel ( 'meinpaket/backlog_product' );
		/* @var $collection Dhl_MeinPaket_Model_Mysql4_Backlog_Product_Collection */
		$collection = $model->getCollection ();
		$collection->getSelect ()->joinLeft ( array (
				'product_table' => $collection->getTable ( 'catalog/product' ) 
		), 'main_table.product_id=product_table.entity_id', array (
				'sku' => 'sku' 
		) );
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'backlog_id', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'ID' ),
				'type' => 'number',
				'index' => 'backlog_id' 
		) );
		
		$this->addColumn ( 'product_id', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Product ID' ),
				'type' => 'number',
				'index' => 'product_id' 
		) );

		$this->addColumn ( 'sku', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'SKU' ),
				'index' => 'sku' 
		) );
		
		$this->addColumn ( 'changes', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Changes' ),
				'index' => 'changes' 
		) );
		
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Created At' ),
				'type' => 'datetime',
				'index' => 'created_at' 
		) );
		
		$this->addExportType ( '*/*/exportExcel', Mage::helper ( 'meinpaket' )->__ ( 'Excel XML' ) );
		
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'backlog_id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'backlogIds' );
		$this->getMassactionBlock ()->addItem ( 'delete', array (
				'label' => Mage::helper ( 'meinpaket' )->__ ( 'Delete' ),
				'url' => $this->getUrl ( '*/*/massDelete', array () ),
				'confirm' => Mage::helper ( 'meinpaket' )->__ ( 'Are you sure?' ) 
		) );
		
		return $this;
	}
	public function getRowUrl($row) {
		return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
				'id' => $row->getProductId () 
		) );
	}
	public function getGridUrl() {
		return $this->getUrl ( '*/*/grid', array (
				'_current' => true 
		) );
	}
}