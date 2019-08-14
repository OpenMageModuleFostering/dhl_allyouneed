<?php

/**
 * Block for the menu list of the MeinPaket menu items.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Block_Adminhtml
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_Leftmenu extends Mage_Adminhtml_Block_Template {
	/**
	 *
	 * @var array
	 */
	protected $menuItems = array ();
	
	/**
	 *
	 * @var string
	 */
	protected $activeItem;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Adds an item to the end of the menu.
	 *
	 * @param string $action        	
	 * @param string $name        	
	 * @return void
	 */
	public function addMenuItem($action, $name) {
		$this->menuItems [$action] = $name;
	}
	
	/**
	 *
	 * @return string $activeItem
	 */
	public function getActiveItem() {
		return $this->activeItem;
	}
	
	/**
	 *
	 * @param string $activeItem        	
	 * @return Dhl_MeinPaket_Block_Adminhtml_Leftmenu
	 */
	public function setActiveItem($activeItem) {
		$this->activeItem = $activeItem;
		return $this;
	}
}
