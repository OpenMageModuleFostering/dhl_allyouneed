<?php
/**
 * Setup-Script fuer Dhl_Postpay
 */

/* @var $installer Dhl_Postpay_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->run ( "
		DROP TABLE IF EXISTS {$this->getTable('postpay/cart')};
		CREATE TABLE {$this->getTable('postpay/cart')} (
		`cart_id` int(11) unsigned NOT NULL auto_increment,
		`order_id` int(11) unsigned,
		`notification_id` varchar(255),
		`state` varchar(255) DEFAULT 'PENDING',
		`created_at` timestamp default CURRENT_TIMESTAMP(),
		PRIMARY KEY(`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allyouneed Sync';
" );

$installer->endSetup ();
