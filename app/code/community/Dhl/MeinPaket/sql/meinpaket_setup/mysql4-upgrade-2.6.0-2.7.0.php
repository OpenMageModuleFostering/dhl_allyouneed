<?php

/* @var $installer Dhl_MeinPaket_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->getConnection ()->addColumn ( $installer->getTable ( 'meinpaket/backlog_product' ), 'request_description_upload', array (
		'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
		'nullable' => false,
		'default' => false,
		'comment' => 'Force upload of product description' 
) );

$installer->endSetup ();
