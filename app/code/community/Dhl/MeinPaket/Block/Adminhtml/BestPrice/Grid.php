<?php
class Dhl_MeinPaket_Block_Adminhtml_BestPrice_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'meinpaket_bestprice_grid' );
		$this->setDefaultSort ( 'bestprice_id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setUseAjax ( true );
		$this->setSaveParametersInSession ( true );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'meinpaket/bestPrice' )->getCollection ();
		
		$entityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$prodNameAttrId = Mage::getModel ( 'eav/entity_attribute' )->loadByCode ( $entityTypeId, 'name' )->getAttributeId ();
		$collection->getSelect ()->joinLeft ( array (
				'prod_name' => 'catalog_product_entity' 
		), 'prod_name.entity_id = main_table.product_id', array (
				'sku' 
		) )->joinLeft ( array (
				'cpev_name' => 'catalog_product_entity_varchar' 
		), 'cpev_name.entity_id=prod_name.entity_id AND cpev_name.attribute_id=' . $prodNameAttrId . '', array (
				'name' => 'cpev_name.value' 
		) );
		
		$this->setCollection ( $collection );
		
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'bestprice_id', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'ID' ),
				'type' => 'number',
				'index' => 'bestprice_id' 
		) );
		
		$this->addColumn ( 'product_id', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Product ID' ),
				'type' => 'number',
				'index' => 'product_id' 
		) );
		
		$this->addColumn ( 'sku', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Sku' ),
				'index' => 'sku' 
		) );
		
		$this->addColumn ( 'name', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Name' ),
				'index' => 'name' 
		) );
		
		$this->addColumn ( 'price', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Price' ),
				'type' => 'number',
				'index' => 'price' 
		) );
		
		$this->addColumn ( 'price_currency', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Price Currency' ),
				'index' => 'price_currency' 
		) );
		
		$this->addColumn ( 'delivery_cost', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Delivery Cost' ),
				'type' => 'number',
				'index' => 'delivery_cost' 
		) );
		
		$this->addColumn ( 'delivery_cost_currency', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Delivery Cost Currency' ),
				'index' => 'delivery_cost_currency' 
		) );
		
		$this->addColumn ( 'delivery_time', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Delivery Time' ),
				'type' => 'number',
				'index' => 'delivery_time' 
		) );
		
		$this->addColumn ( 'active_offers', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Active Offers' ),
				'type' => 'number',
				'index' => 'active_offers' 
		) );
		
		$this->addColumn ( 'ownership', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Ownership' ),
				'index' => 'ownership' 
		) );
		
		$this->addColumn ( 'owning_dealer_code', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Owning Dealer Code' ),
				'index' => 'owning_dealer_code' 
		) );
		
		$this->addColumn ( 'created_at', array (
				'header' => Mage::helper ( 'meinpaket' )->__ ( 'Created At' ),
				'type' => 'datetime',
				'index' => 'created_at' 
		) );
		
		$this->addExportType ( '*/*/exportExcel', Mage::helper ( 'meinpaket' )->__ ( 'Excel XML' ) );
		$this->addExportType ( '*/*/exportCsv', Mage::helper ( 'meinpaket' )->__ ( 'CSV' ) );
		
		return parent::_prepareColumns ();
	}
	public function getRowUrl($row) {
		return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
				'id' => $row->getProductId () 
		) );
	}
	public function getGridUrl() {
		return $this->getUrl ( 'adminhtml/meinpaket_bestPrice/grid', array (
				'_current' => true 
		) );
	}
}