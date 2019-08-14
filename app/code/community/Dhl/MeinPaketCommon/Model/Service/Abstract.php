<?php
class Dhl_MeinPaketCommon_Model_Service_Abstract {
	/**
	 * Process ID.
	 */
	const DEFAULT_PROCESS_ID = 'meinpaket';
	
	/**
	 * Used process id.
	 *
	 * @var unknown
	 */
	protected $processID;
	
	/**
	 *
	 * @var Mage_Core_Model_App_Emulation
	 */
	protected $appEmulation;
	
	/**
	 * Initial environment when starting emulation.
	 * Used to unset.
	 *
	 * @var array
	 */
	protected $initialEnvironmentInfo;
	
	/**
	 * Lock API.
	 *
	 * @var Mage_Index_Model_Process $indexProcess
	 */
	protected $process;
	
	/**
	 *
	 * @var unknown
	 */
	protected $storeId;
	
	/**
	 *
	 * @var unknown
	 */
	protected $lockFile;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isLocked = false;
	function __construct($processID = null) {
		$this->processID = $processID;
		$this->storeId = Mage::helper ( 'meinpaket/data' )->getMeinPaketStoreId ();
		$this->appEmulation = Mage::getSingleton ( 'core/app_emulation' );
	}
	
	/**
	 * Lock process.
	 *
	 * @throws ErrorException
	 */
	function lock($storeId = null) {
		if (flock ( $this->getLockFile (), LOCK_EX | LOCK_NB )) {
			$this->isLocked = true;
		} else {
			throw new ErrorException ( 'MeinPaket already running' );
		}
		// Start environment emulation of the specified store
		$this->initialEnvironmentInfo = $this->appEmulation->startEnvironmentEmulation ( $storeId == null ? $this->storeId : $storeId );
	}
	
	/**
	 * Unlock process.
	 */
	function unlock() {
		if ($this->isLocked) {
			// Stop environment emulation and restore original store
			$this->appEmulation->stopEnvironmentEmulation ( $this->initialEnvironmentInfo );
			flock ( $this->getLockFile (), LOCK_UN );
			$this->isLocked = false;
		}
	}
	
	/**
	 * Get lock file resource
	 *
	 * @return resource
	 */
	protected function getLockFile() {
		if ($this->processID == null) {
			$id = self::DEFAULT_PROCESS_ID;
		} else {
			$id = $this->processID;
		}
		
		if ($this->lockFile === null) {
			$varDir = Mage::getConfig ()->getVarDir ( 'locks' );
			$file = $varDir . DS . 'sync_process_' . $id . '.lock';
			if (is_file ( $file )) {
				$this->lockFile = fopen ( $file, 'w' );
			} else {
				$this->lockFile = fopen ( $file, 'x' );
			}
			fwrite ( $this->lockFile, date ( 'r' ) );
		}
		return $this->lockFile;
	}
}
