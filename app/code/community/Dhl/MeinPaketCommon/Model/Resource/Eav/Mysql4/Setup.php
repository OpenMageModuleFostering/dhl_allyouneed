<?php
class Dhl_MeinPaketCommon_Model_Resource_Eav_Mysql4_Setup extends Mage_Catalog_Model_Resource_Setup {
	/**
	 *
	 * @return array
	 */
	public function getDefaultEntities() {
		return array (
				'order' => array (
						'entity_model' => 'sales/order',
						'table' => 'sales/order',
						'increment_model' => 'eav/entity_increment_numeric',
						'increment_per_store' => true,
						'backend_prefix' => 'sales_entity/order_attribute_backend',
						'attributes' => array (
								'meinpaket_id' => array (
										'type' => 'int',
										'label' => 'Order Allyouneed Id',
										'required' => false,
										'is_visible' => false,
										'visible' => false 
								) 
						) 
				),
				'customer' => array (
						'entity_model' => 'customer/customer',
						'attribute_model' => 'customer/attribute',
						'table' => 'customer/entity',
						'increment_model' => 'eav/entity_increment_numeric',
						'additional_attribute_table' => 'customer/eav_attribute',
						'entity_attribute_collection' => 'customer/attribute_collection',
						'attributes' => array (
								'meinpaket_buyer_id' => array (
										'type' => 'int',
										'label' => 'MeinPaket Buyer Id',
										'input' => 'text',
										'required' => false,
										'sort_order' => 200,
										'visible' => false 
								),
								'meinpaket_buyer_name' => array (
										'type' => 'varchar',
										'label' => 'Allyouneed Buyer Name',
										'input' => 'text',
										'required' => false,
										'sort_order' => 201,
										'visible' => false 
								) 
						) 
				) 
		);
	}
}
