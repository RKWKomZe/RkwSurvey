<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
	function($extKey)
	{

        //=================================================================
        // Register Plugins
        //=================================================================
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
			'RKW.RkwSurvey',
			'Survey',
			'RKW Survey: Umfrage'
		);

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


        //=================================================================
        // Add TypoScript
        //=================================================================
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		    $extKey,
            'Configuration/TypoScript',
            'RKW Survey'
        );

		//=================================================================
		// Add Flexform
		//=================================================================
		$extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
		$pluginName = strtolower('Survey');
		$pluginSignature = $extensionName . '_' . $pluginName;
		$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages';
		$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		    $pluginSignature,
            'FILE:EXT:' . $extKey . '/Configuration/FlexForms/Survey.xml'
        );
	},
	$_EXTKEY
);


