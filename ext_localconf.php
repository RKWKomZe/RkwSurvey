<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
	function($extKey)
	{

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

		// set logger
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

		// wizards
        /*
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
			'mod {
				wizards.newContentElement.wizardItems.plugins {
					elements {
						survey {
							icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($extKey) . 'Resources/Public/Icons/user_plugin_survey.svg
							title = LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkw_survey_domain_model_survey
							description = LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkw_survey_domain_model_survey.description
							tt_content_defValues {
								CType = list
								list_type = rkwsurvey_survey
							}
						}
					}
					show = *
				}
		   }'
		);
        */
	},
	$_EXTKEY
);
