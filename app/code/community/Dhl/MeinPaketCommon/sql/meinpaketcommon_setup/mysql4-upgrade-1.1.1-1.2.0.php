<?php

/* @var $installer Dhl_MeinPaketCommon_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup ();

$installer->run ( "
		ALTER TABLE {$this->getTable('meinpaketcommon/log')} ADD `url` varchar(255) AFTER `request_id`;
" );

$installer->endSetup ();
