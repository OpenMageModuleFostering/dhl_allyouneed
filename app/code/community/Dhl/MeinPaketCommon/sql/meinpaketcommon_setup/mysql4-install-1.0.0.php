<?php
/**
 * Setup-Script fuer Dhl_MeinPaket
 *
 * Uninstall MeinPaket: 
 * delete from core_resource where code = 'meinpaket_setup';
 * delete from eav_attribute where attribute_code = "is_dhl_mein_paket_root";
 * delete from eav_attribute where attribute_code = "is_dhl_marketplace_root";
 * delete from eav_attribute where attribute_code = "dhl_marketplace_category_id";
 * delete from eav_attribute where attribute_code = "sync_with_dhl_mein_paket";
 * delete from eav_attribute where attribute_code = "max_stock_for_dhl_mein_paket";
 * delete from eav_attribute where attribute_code = "shipment_was_exported_for_dhl_mein_paket";
 * delete from eav_attribute where attribute_code = "dhl_mein_paket_order_id";
 * delete from eav_attribute_group where attribute_group_name = "Allyouneed";
 * alter table sales_flat_order drop column dhl_mein_paket_order_id;
 * alter table sales_flat_order_grid drop column dhl_mein_paket_order_id;
 * alter table sales_flat_shipment drop column shipment_was_exported_for_dhl_mein_paket;
 * 
 * drop table meinpaket_async
 * drop table meinpaket_log
 */

/* @var $installer Dhl_MeinPaketCommon_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

// get DB connectiondelete from core_resource where code = 'meinpaket_setup';
$db = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
$table_prefix = Mage::getConfig ()->getTablePrefix ();

// ############### create dhl_mein_paket_order_id attribute #################

// check wether dhl_mein_paket_order_id column exists for orders
$orderIdFieldExists = false;
$result = $db->query ( "EXPLAIN {$table_prefix}sales_flat_order" );

while ( $resultset = $result->fetch ( PDO::FETCH_ASSOC ) ) {
	if ($resultset ['Field'] == 'dhl_mein_paket_order_id')
		$orderIdFieldExists = true;
}

if (! $orderIdFieldExists) {
	$installer->getConnection ()->addColumn ( $installer->getTable ( 'sales_flat_order' ), 'dhl_mein_paket_order_id', 'varchar(255) NULL DEFAULT NULL AFTER `entity_id`' );
	
	$installer->addAttribute ( 'order', 'dhl_mein_paket_order_id', array (
			'type' => 'static' 
	) );
	// 'visible' => false
}

// check wether dhl_mein_paket_order_id column exists for orders grid
$orderIdFieldExists = false;
$result = $db->query ( "EXPLAIN {$table_prefix}sales_flat_order_grid" );

while ( $resultset = $result->fetch ( PDO::FETCH_ASSOC ) ) {
	if ($resultset ['Field'] == 'dhl_mein_paket_order_id')
		$orderIdFieldExists = true;
}

if (! $orderIdFieldExists) {
	$installer->getConnection ()->addColumn ( $installer->getTable ( 'sales_flat_order_grid' ), 'dhl_mein_paket_order_id', 'varchar(255) NULL DEFAULT NULL AFTER `entity_id`' );
}

// ############### create shipment_was_exported_for_dhl_mein_paket attribute #################

$orderIdFieldExists = false;
$result = $db->query ( "EXPLAIN {$table_prefix}sales_flat_shipment" );

while ( $resultset = $result->fetch ( PDO::FETCH_ASSOC ) ) {
	if ($resultset ['Field'] == 'shipment_was_exported_for_dhl_mein_paket')
		$orderIdFieldExists = true;
}

if (! $orderIdFieldExists) {
	$installer->getConnection ()->addColumn ( $installer->getTable ( 'sales_flat_shipment' ), 'shipment_was_exported_for_dhl_mein_paket', 'int(1) NULL DEFAULT NULL AFTER `entity_id`' );
}

$installer->run ( "
		DROP TABLE IF EXISTS {$this->getTable('meinpaketcommon/async')};
		CREATE TABLE {$this->getTable('meinpaketcommon/async')} (
		`async_id` int(11) unsigned NOT NULL auto_increment,
		`request_id` varchar(255),
		`status` varchar(255),
		`created_at` datetime default '0000-00-00 00:00:00',
		`updated_at` datetime default '0000-00-00 00:00:00',
		PRIMARY KEY(`async_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MeinPaket Async Requests';
" );

$installer->run ( "
		DROP TABLE IF EXISTS {$this->getTable('meinpaketcommon/log')};
		CREATE TABLE {$this->getTable('meinpaketcommon/log')} (
		`log_id` int(11) unsigned NOT NULL auto_increment,
		`request_id` varchar(255),
		`status` varchar(255),
		`send` text default '',
		`received` text default '',
		`error` text default '',
		`created_at` datetime default '0000-00-00 00:00:00',
		PRIMARY KEY(`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MeinPaket Log';
" );

$installer->endSetup ();
