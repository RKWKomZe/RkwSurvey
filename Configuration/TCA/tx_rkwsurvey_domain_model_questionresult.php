<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresult',
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
		'searchFields' => 'answer,survey_result,question,skipped',
		'iconfile' => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_questionresult.gif'
	],
	'types' => [
	    // 		'1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, answer, survey_result, question, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
		'1' => ['showitem' => 'answer, survey_result, question, skipped, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
	],
	'columns' => [
		/*'sys_language_uid' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => [
					[
						'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
						-1,
						'flags-multiple'
					]
				],
				'default' => 0,
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_rkwsurvey_domain_model_questionresult',
				'foreign_table_where' => 'AND tx_rkwsurvey_domain_model_questionresult.pid=###CURRENT_PID### AND tx_rkwsurvey_domain_model_questionresult.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
		'hidden' => [
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
				'items' => [
					'1' => [
						'0' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:labels.enabled'
					]
				],
			],
		],
		'starttime' => [
			'exclude' => true,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'eval' => 'datetime',
				'default' => 0,
			]
		],
		'endtime' => [
			'exclude' => true,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'eval' => 'datetime',
				'default' => 0,
				'range' => [
					'upper' => mktime(0, 0, 0, 1, 1, 2038)
				]
			],
		],*/
		'answer' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresult.answer',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			]
		],
		'survey_result' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresult.survey_result',
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
		'question' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questionresult.question',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_rkwsurvey_domain_model_question',
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
        'skipped' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.skipped',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
	],
];
