<?php

namespace VMFDS\VmfdsEvents\Domain\Repository;

// override autoload:
require_once(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:vmfds_kool/Classes/Connectors/KoolConnector.php'));
require_once(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:vmfds_events/Classes/Domain/Repository/KoolEventGroupRepository.php'));

class KoolEventRepository {
	// kool connector
	protected $kool;
	protected $koolEventGroupRepository;
	
			
	public function __construct() {
		$this->kool = new \TYPO3\VmfdsKool\Connectors\koolConnector();
		$this->koolEventGroupRepository = new \VMFDS\VmfdsEvents\Domain\Repository\KoolEventGroupRepository();		
	}
	
	public function findByQuery($sql) {
		return $this->kool->query($sql);
	}

	public function findAll() {
		return $this->findByQuery('SELECT * FROM ko_event');
	}	
	
	public function findBySettings($settings, $args = array()) {
		$sql = 'SELECT * FROM ko_event';
		
		$startDate = strtotime(($settings['period']['start']) ? $settings['period']['start'] : 'last sunday');
		$endDate = strtotime(($settings['period']['end']) ? $settings['period']['end'] : 'next sunday');
		
		if ($settings['options']['checkPiVars']) {
			if ($args['startDate']) {
				$startDate = $args['startDate'];
			}
			if ($args['endDate']) $endDate = $args['endDate'];
		}
		
		
		// filter by calendars
		if ($settings['filters']['calendars'])
			$where .= ' AND (grp.calendar_id IN ('.$settings['filters']['calendars'].')) ';
		// filter by event groups
		if ($settings['filters']['groups'])
			$where .= ' AND (grp.id IN ('.$settings['filters']['groups'].')) ';
		// filter by category (my_vmfds_events_categories)
		if ($settings['filters']['category'])
			$where .= ' AND (FIND_IN_SET('.$settings['filters']['category'].', event.my_vmfds_events_categories)>0) ';
		
		// filter by teaser start date
		if ($settings['options']['teaser'])
			$where .= ' AND ((event.my_vmfds_events_teaser_start<=NOW()) OR (event.my_vmfds_events_teaser_start=\'\')) ';
		
		// skip current event (already displayed)
		if ($settings['options']['skipCurrent'] && ($args['id']))
			$where .= ' AND (NOT (event.id='.$args['id'].')) ';
		
		// further filters from where
		if (is_array($settings['filters']['raw'])) {
			foreach ($settings['filters']['raw'] as $filter) {
				$where .= ' AND ('.$filter.') ';
			}
		}
		
		
		$limitCount = $settings['options']['limit'];
		
		$sql = 'SELECT event.*,grp.calendar_id,grp.name'
		.' FROM ko_event event'
		.' LEFT JOIN ko_eventgruppen grp ON (event.eventgruppen_id = grp.id)'
		.' WHERE '
		.'(STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')>=\''.strftime('%Y-%m-%d %H:%M:%S', $startDate).'\')'
		.' AND (STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')<=\''.strftime('%Y-%m-%d %H:%M:%S', $endDate).'\')'
		.$where
		.' ORDER BY STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\') '
		.($limitCount ? ' LIMIT '.$limitCount.' ' : '')
		.';';
		
		$events = $this->findByQuery($sql);
		
		foreach ($events as $key => $event) {
			// fetch full group info
			$events[$key]['group'] = $this->koolEventGroupRepository->findByUid($event['eventgruppen_id']);
			
			// provide dates for grouping
			$tmp = explode('-', $event['startdatum']);
			$events[$key]['start_year'] = $tmp[0];
			$events[$key]['start_month'] = $tmp[1];
			$events[$key]['start_day'] = $tmp[2];
			// workaround for missing strftime
			$events[$key]['start_day_name'] = strftime('%A', strtotime($event['startdatum']));
			$events[$key]['start_month_name'] = strftime('%B', strtotime($event['startdatum']));
			$events[$key]['start_day_name_short'] = strftime('%a', strtotime($event['startdatum']));
			$events[$key]['start_month_name_short'] = strftime('%b', strtotime($event['startdatum']));
			$tmp = explode('-', $event['enddatum']);
			$events[$key]['end_year'] = $tmp[0];
			$events[$key]['end_month'] = $tmp[1];
			$events[$key]['end_day'] = $tmp[2];
			$events[$key]['end_day_name'] = strftime('%A', strtotime($event['enddatum']));
			$events[$key]['end_month_name'] = strftime('%B', strtotime($event['enddatum']));
			$events[$key]['end_day_name_short'] = strftime('%a', strtotime($event['enddatum']));
			$events[$key]['end_month_name_short'] = strftime('%b', strtotime($event['enddatum']));
				
		}
		
		return $events;
	}
	
	public function findByUid($uid) {
		$res = $this->findByQuery('SELECT * FROM ko_event WHERE id='.$uid);
		return $res[0];
	}
}
