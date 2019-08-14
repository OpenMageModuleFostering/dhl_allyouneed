<?php
/**
 * Block for the product matching.
 *
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Dhl_MeinPaket_Block_Adminhtml_Catalog_Product_Matching
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_Catalog_Product_Matching_Tab extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	
	/**
	 * Set the template for the block
	 */
	public function _construct() {
		parent::_construct ();
		$this->setTemplate ( 'meinpaket/catalog/product/matching/tab.phtml' );
	}
	
	/**
	 * Retrieve the label used for the tab relating to this block
	 *
	 * @return string
	 */
	public function getTabLabel() {
		return $this->__ ( 'Allyouneed Matching' );
	}
	
	/**
	 * Retrieve the title used by this tab
	 *
	 * @return string
	 */
	public function getTabTitle() {
		return $this->__ ( 'Assign Allyouneed Category' );
	}
	
	/**
	 * Determines whether to display the tab
	 * Add logic here to decide whether you want the tab to display
	 *
	 * @return bool
	 */
	public function canShowTab() {
		return true;
	}
	
	/**
	 * Stops the tab being hidden
	 *
	 * @return bool
	 */
	public function isHidden() {
		return false;
	}
}	
