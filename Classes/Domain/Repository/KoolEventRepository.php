<?php

namespace VMFDS\VmfdsEvent\Domain\Repository;

// override autoload:
require_once(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:vmfds_kool/Classes/Connectors/KoolConnector.php'));

class KoolUserRepository {
	// kool connector
	protected $kool; 
	
			
	public function __construct() {
		$this->kool = new \TYPO3\VmfdsKool\Connectors\koolConnector;		
	}
	
	public function findByQuery($sql) {
		return $this->kool->query($sql);
	}

	public function findAll() {
		return $this->findByQuery('SELECT * FROM ko_events');
	}	
	
	public function findBySettings($settings) {
		$sql = 'SELECT * FROM ko_events';
		
		return $this->findByQuery($sql);
	}
}
