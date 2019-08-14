<?php

/* @var $installer Dhl_MeinPaketCommon_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->addAttribute ( 'order', 'meinpaket_id', array (
		'type' => 'int',
		'label' => 'Order Allyouneed Id',
		'required' => false,
		'is_visible' => false,
		'visible' => false
) );

$installer->addAttribute ( 'customer', 'meinpaket_buyer_id', array (
		'type' => 'int',
		'label' => 'Allyouneed Buyer Id',
		'input' => 'text',
		'required' => false,
		'sort_order' => 200,
		'visible' => false
) );

$installer->addAttribute ( 'customer', 'meinpaket_buyer_name', array (
		'type' => 'varchar',
		'label' => 'Allyouneed Buyer Name',
		'input' => 'text',
		'required' => false,
		'sort_order' => 201,
		'visible' => false
) );

$installer->endSetup ();
