<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
	function($extKey)
	{

		if (TYPO3_MODE === 'BE') {

			\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'RKW.RkwSurvey',
				'web', // Make module a submodule of 'web'
				'evaluation', // Submodule key
				'', // Position
				[
					'Backend' => 'list, show, print, csv, tokenList, tokenCreate, tokenRemove, tokenCsv',
				],
				[
					'access' => 'user,group',
					'icon'   => 'EXT:' . $extKey . '/ext_icon.gif',
					'labels' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_backend.xlf',
				]
			);
		}

        //=================================================================
        // Add tables
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_rkwsurvey_domain_model_survey'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_rkwsurvey_domain_model_question'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_rkwsurvey_domain_model_surveyresult'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_rkwsurvey_domain_model_questionresult'
        );


	},
	'rkw_survey'
);


