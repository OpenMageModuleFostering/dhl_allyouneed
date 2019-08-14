<?php

/* @var $installer Dhl_MeinPaket_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->addAttribute ( 'catalog_product', 'meinpaket_category', array (
		'type' => 'text',
		'label' => 'Allyouneed Category',
		'input' => 'select',
		'source' => 'meinpaket/entity_attribute_source_meinPaketCategory',
		'input_renderer' => 'meinpaket/adminhtml_catalog_product_renderer_category',
		'required' => false,
		'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'visible' => true,
		'group' => 'Allyouneed' 
) );

$installer->endSetup ();
