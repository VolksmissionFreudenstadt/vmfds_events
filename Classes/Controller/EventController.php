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
	
		
	public function __construct() {
		parent::__construct();
		$this->koolEventRepository = new \VMFDS\VmfdsEvent\Domain\Repository\KoolEventRepository();
	}
		
	public function listAction() {
		$events = $this->koolEventRepository->findBySettings($this->settings);
		
		$this->view->assign('events', $events);
	}

}
	
?>