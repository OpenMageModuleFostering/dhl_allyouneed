<?php

/**
 * Block for the preview list of the products which will be exported with the current selection.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Block_Adminhtml_ProductExport
 * @version		$Id$
 * @author		Daniel Pötzinger <daniel.poetzinger@aoemedia.de>
 */
class Dhl_MeinPaket_Block_Adminhtml_ProductExport_List extends Mage_Adminhtml_Block_Template {
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Assigns a product model collection to the template.
	 *
	 * @param
	 *        	$collection
	 * @return void
	 */
	public function assignProductCollection($collection) {
		$this->assign ( 'productCollection', $collection );
	}
}
