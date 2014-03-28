<?php

namespace VMFDS\VmfdsEvents\Controller;

// override autoload:
require_once(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:vmfds_events/Classes/Domain/Repository/KoolEventRepository.php'));

class EventController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	protected $koolEventRepository;
	
	
	/**
	* frontendUserRepository
	*
	* @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	* @inject
	*/
	protected $frontendUserRepository;

	/**
	* frontendUserGroupRepository
	*
	* @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
	* @inject
	*/
	protected $frontendUserGroupRepository;
	
		
	private function tsRemoveDots($a) {
		foreach ($a as $key => $val) {
			if (substr($key, -1) == '.') {
				$key = substr($key, 0, -1);
				$a[$key] = $a[$key.'.'];
				unset ($a[$key.'.']);
				if (is_array($a[$key])) $a[$key] = $this->tsRemoveDots($a[$key]);
			}
		}
		return $a;
	}
	
	public function __construct() {
		parent::__construct();
		$this->koolEventRepository = new \VMFDS\VmfdsEvents\Domain\Repository\KoolEventRepository();
	}
	
	public function importFlexFormSettings() {
		$additionalTS = $this->settings['myTS'];
		if ($additionalTS) {
			unset($this->settings['myTS']);
			if ($additionalTS) {
				$tsParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_TSparser');
				$tsParser->setup = array('settings.' => $this->settings);
				$tsParser->parse(trim($additionalTS));
				$tmp = $tsParser->setup;
			}
			$tmp = $this->tsRemoveDots($tmp);
			$this->settings = $tmp['settings'];
		}
	}
		
	public function listAction() {
		$this->importFlexFormSettings();
		$events = $this->koolEventRepository->findBySettings($this->settings, $this->request->getArguments());
		$this->view->assign('events', $events);
	}
	
	public function showAction() {
		$this->importFlexFormSettings();
		$req = $this->request->getArguments();
		$this->view->assign('event', $this->koolEventRepository->findByUid($req['id']));
	}

}
	
?>