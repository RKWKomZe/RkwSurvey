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
        // Add XClasses for extending existing classes
        // ATTENTION: deactivated due to faulty mapping in TYPO3 9.5
        //=================================================================
        /*
        // for TYPO3 12+
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Madj2k\FeRegister\Domain\Model\BackendUser::class] = [
            'className' => \RKW\RkwSurvey\Domain\Model\BackendUser::class
        ];

        // for TYPO3 9.5 - 11.5 only, not required for TYPO3 12
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \Madj2k\FeRegister\Domain\Model\BackendUser::class,
                \RKW\RkwSurvey\Domain\Model\BackendUser::class
            );
        */
        //=================================================================
        // Setting Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwSurvey']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
			// levels with higher severity (ERROR, CRITICAL, EMERGENCY)
			\TYPO3\CMS\Core\Log\LogLevel::WARNING => array(
				// add a FileWriter
				'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
					// configuration for the writer
					'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath()  . '/log/tx_survey.log'
				)
			),
		);

	},
	'rkw_survey'
);
