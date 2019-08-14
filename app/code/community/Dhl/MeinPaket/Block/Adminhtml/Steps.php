<?php

/**
 * Block for the steps view used in all MeinPaket modules.
 * 
 * @category	Dhl
 * @package		Dhl_MeinPaket
 * @subpackage	Block_Adminhtml
 * @version		$Id$
 */
class Dhl_MeinPaket_Block_Adminhtml_Steps extends Mage_Adminhtml_Block_Template {
	/**
	 *
	 * @var array
	 */
	protected $steps;
	
	/**
	 *
	 * @var string
	 */
	protected $activeStep;
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->steps = array ();
		parent::__construct ();
	}
	
	/**
	 * Adds a step.
	 *
	 * @param string $name        	
	 * @param string $text        	
	 * @return Dhl_MeinPaket_Block_Adminhtml_Steps
	 */
	public function addStep($name, $text) {
		$this->steps [$name] = $text;
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getActiveStep() {
		return $this->activeStep;
	}
	
	/**
	 *
	 * @param
	 *        	string
	 * @return Dhl_MeinPaket_Block_Adminhtml_Steps
	 */
	public function setActiveStep($activeStep) {
		$this->activeStep = $activeStep;
		return $this;
	}
}
