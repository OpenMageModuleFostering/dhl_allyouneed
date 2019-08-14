<?php

/* @var $installer Dhl_MeinPaket_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->run ( "
DROP TABLE IF EXISTS {$this->getTable('meinpaket/category')};
CREATE TABLE {$this->getTable('meinpaket/category')} (
		`category_id` int(10) unsigned not null auto_increment,
		`name` varchar(255) not null,
		`code` varchar(255) not null,
		`parent` varchar(255) not null,
		`leaf` tinyint(1) unsigned not null ,
		PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allyouneed Categories';
" );

$installer->run ( "
		DROP TABLE IF EXISTS {$this->getTable('meinpaket/backlog_product')};
		CREATE TABLE {$this->getTable('meinpaket/backlog_product')} (
		`backlog_id` int(11) unsigned NOT NULL auto_increment,
		`product_id` int(10) unsigned NOT NULL,
		`changes` text default '',
		`created_at` datetime default '0000-00-00 00:00:00',
		PRIMARY KEY(`backlog_id`),
		CONSTRAINT `FK_MEINPAKET_PRODUCT_BACKLOG_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES {$installer->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allyouneed Backlog';
" );

$installer->run ( "
		DROP TABLE IF EXISTS {$this->getTable('meinpaket/bestprice')};
		CREATE TABLE {$this->getTable('meinpaket/bestprice')} (
		`bestprice_id` int(11) unsigned NOT NULL auto_increment,
		`product_id` int(10) unsigned NOT NULL,
		`price` decimal(12,4),
		`price_currency` varchar(255),
		`delivery_cost` decimal(12,4),
		`delivery_cost_currency` varchar(255),
		`delivery_time` int(10),
		`active_offers` int(10),
		`ownership` varchar(255),
		`owning_dealer_code` varchar(255),
		`created_at` datetime default '0000-00-00 00:00:00',
		PRIMARY KEY(`bestprice_id`),
		CONSTRAINT `FK_MEINPAKET_BESTPRICE_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES {$installer->getTable('catalog_product_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allyouneed BestPrices';
" );

$installer->getConnection ()->addColumn ( $installer->getTable ( 'catalog/eav_attribute' ), 'meinpaket_attribute', "VARCHAR( 255 ) DEFAULT 'None' COMMENT 'Allyouneed Attribute'" );

$installer->endSetup ();
