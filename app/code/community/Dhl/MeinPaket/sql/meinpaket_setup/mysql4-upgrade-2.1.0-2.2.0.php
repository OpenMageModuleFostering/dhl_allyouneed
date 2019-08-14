<?php

/* @var $installer Dhl_MeinPaket_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->addAttribute ( 'catalog_product', 'meinpaket_id', array (
		'type' => 'int',
		'label' => 'Product DHL Allyouneed Id',
		'required' => false,
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => false,
		'group' => 'Allyouneed' 
) );

$installer->addAttribute ( 'catalog_product', 'sync_with_dhl_mein_paket', array (
		'type' => 'int',
		'label' => 'Sync with Allyouneed',
		'frontend' => 'meinpaket/entity_attribute_frontend_labelTranslation',
		'input' => 'select',
		'source' => 'meinpaket/entity_attribute_source_productSyncMode',
		'required' => false,
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'group' => 'Allyouneed' 
) );

$installer->addAttribute ( 'catalog_product', 'max_stock_for_dhl_mein_paket', array (
		'type' => 'int',
		'label' => 'Maximum stock qty. for Allyouneed',
		'frontend' => 'meinpaket/entity_attribute_frontend_labelTranslation',
		'input' => 'text',
		'required' => false,
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'group' => 'Allyouneed' 
) );

$installer->addAttribute ( 'catalog_product', 'meinpaket_category', array (
		'type' => 'text',
		'label' => 'Allyouneed Category',
		'input' => 'select',
		'source' => 'meinpaket/entity_attribute_source_meinPaketCategory',
		'required' => false,
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'group' => 'Allyouneed' 
) );

$installer->endSetup ();
