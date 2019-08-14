<?php
class Dhl_MeinPaketCommon_Helper_Cron extends Mage_Core_Helper_Abstract {
	/**
	 * Schedule jobs for next cron run.
	 *
	 * @param array $cronjobs
	 *        	to schedule
	 * @param string $addMessage
	 *        	add messages to session
	 */
	public function scheduleJobs(array $cronjobs, $addMessage = true) {
		$session = Mage::getSingleton ( 'adminhtml/session' );
		/* @var $session Mage_Adminhtml_Model_Session */
		
		$time = time ();
		$timeString = strftime ( '%Y-%m-%d %H:%M:%S', $time );
		foreach ( $cronjobs as $code ) {
			$schedule = Mage::getModel ( 'cron/schedule' );
			$schedule->setJobCode ( $code );
			$schedule->setStatus ( Mage_Cron_Model_Schedule::STATUS_PENDING );
			$schedule->setCreatedAt ( $timeString );
			$schedule->setScheduledAt ( $timeString );
			$schedule->save ();
			if ($addMessage) {
				$session->addSuccess ( $this->__ ( 'Scheduled "%s" at %s', $code, $timeString ) );
			}
		}
	}
	/**
	 * Run jobs now.
	 *
	 * @param array $cronjobs
	 *        	to run
	 * @param string $addMessage
	 *        	add messages to session
	 */
	public function runJobs(array $cronjobs, $addMessage = true) {
		/* @var $session Mage_Adminhtml_Model_Session */
		$session = Mage::getSingleton ( 'adminhtml/session' );
		
		foreach ( $cronjobs as $code ) {
			try {
				$res = null;
				
				switch ($code) {
					case Dhl_MeinPaketCommon_Model_Cron::SYNC_ORDERS :
						$res = Mage::getSingleton ( 'meinpaketcommon/service_order_importService' )->importOrders ();
						break;
					case Dhl_MeinPaketCommon_Model_Cron::SYNC_ASYNC :
						$res = Mage::getSingleton ( 'meinpaketcommon/service_async' )->process ();
						break;
					default :
						Mage::dispatchEvent ( ’dhl_meinpaket_run_cronjob’, array (
								$code 
						) );
						break;
				}
				
				$message = is_string ( $res ) ? $res : Zend_Debug::dump ( $res, null, false );
				
				if ($addMessage && strlen ( $message ) > 0) {
					$session->addSuccess ( $this->__ ( 'Ran "%s":<pre>%s</pre>', $code, $message ) );
				}
			} catch ( Exception $e ) {
				if ($addMessage) {
					$session->addError ( $this->__ ( 'Error while running "%s":<pre>%s</pre>', $code, $e->getMessage () ) );
				}
				Mage::logException ( $e );
			}
		}
	}
}
