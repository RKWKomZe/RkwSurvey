<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
	function($extKey)
	{

        //=================================================================
        // Configure Plugin
        //=================================================================
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
			'RKW.RkwSurvey',
			'Survey',
			[
				'Survey' => 'welcome, start, progress, newContact, createContact, create, result'
			],
			// non-cacheable actions
			[
				'Survey' => 'welcome, start, progress, newContact, createContact, create, result'
			]
		);

        //=================================================================
        // Setting Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwSurvey']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
			// levels with higher severity (ERROR, CRITICAL, EMERGENCY)
			\TYPO3\CMS\Core\Log\LogLevel::INFO => array(
				// add a FileWriter
				'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
					// configuration for the writer
					'logFile' => 'typo3temp/logs/tx_survey.log'
				)
			),
		);
	},
	$_EXTKEY
);
