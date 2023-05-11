<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresultcontainer',
		'hideTable' => 1,
		'label' => 'answer_multiple',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		// 'languageField' => 'sys_language_uid',
		// 'transOrigPointerField' => 'l10n_parent',
		// 'transOrigDiffSourceField' => 'l10n_diffsource',
		// 'delete' => 'deleted',
		'enablecolumns' => [
			//'disabled' => 'hidden',
			//'starttime' => 'starttime',
			//'endtime' => 'endtime',
		],
		'searchFields' => 'answer,survey_result,question_result,skipped',
		'iconfile' => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_questionresultcontainer.gif'
	],
	'interface' => [
		// 'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, answer, survey_result, question',
        'showRecordFieldList' => 'survey_result, question_result, skipped',

    ],
	'types' => [
	    // 		'1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, answer, survey_result, question, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
		'1' => ['showitem' => 'survey_result, question_result, skipped, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
	],
	'columns' => [

		'survey_result' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresultcontainer.survey_result',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_rkwsurvey_domain_model_surveyresult',
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => [
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				],
			],
		],
		'question_result' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresultcontainer.question',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_rkwsurvey_domain_model_questionresult',
				'minitems' => 0,
				'maxitems' => 9999,
				'appearance' => [
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				],
			],
		],
        'skipped' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresultcontainer.skipped',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
	],
];
