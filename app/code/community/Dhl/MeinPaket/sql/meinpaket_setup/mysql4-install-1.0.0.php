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
 * 
 * @author Andreas Demmer <andreas.demmer@aoemedia.de>
 */

/* @var $installer Dhl_MeinPaket_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

// Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'DHL Allyouneed extension was successfully installed!' );

$installer->installEntities ();
$installer->endSetup ();
