<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// register the plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Events',
	'Events'
);

// add static TypoScript configuration
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Events');

// Model: Event

// add language file for the model 
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_vmfdsevents_domain_model_sermon', 'EXT:vmfds_sermons/Resources/Private/Language/locallang_csh_tx_vmfdsevents_domain_model_event.xlf');
// no TCA section here, since we're using an external table




// add flexform
$pluginSignature = 'vmfdsevents_events';


$TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature,
		'FILE:EXT:' . $_EXTKEY .
		'/Configuration/FlexForms/flexform_events.xml');

?>