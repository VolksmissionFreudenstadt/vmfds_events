<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'VMFDS.' . $_EXTKEY,
	'Events',
	array(
		'Event' => 'list,show,register',		
	),
	// non-cacheable actions
	array(
	)
);

?>