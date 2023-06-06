<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult',
		'hideTable' => 1,
		'label' => 'finished',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		// 'languageField' => 'sys_language_uid',
		// 'transOrigPointerField' => 'l10n_parent',
		// 'transOrigDiffSourceField' => 'l10n_diffsource',
		// 'delete' => 'deleted',
		'enablecolumns' => [
			// 'disabled' => 'hidden',
            // 'starttime' => 'starttime',
			// 'endtime' => 'endtime',
		],
		'searchFields' => 'finished,survey,question_result,token,tags',
		'iconfile' => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_surveyresult.gif'
	],
	'interface' => [
	    // 'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, finished, survey, question_result',
		'showRecordFieldList' => 'finished, survey, question_result, token, tags',
	],
	'types' => [
        // '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, finished, survey, question_result, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
        '1' => ['showitem' => 'finished, survey, question_result, token, tags, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
	],
	'columns' => [
	    /*
		'sys_language_uid' => [
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
			'exclude' => true,
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_rkwsurvey_domain_model_surveyresult',
				'foreign_table_where' => 'AND tx_rkwsurvey_domain_model_surveyresult.pid=###CURRENT_PID### AND tx_rkwsurvey_domain_model_surveyresult.sys_language_uid IN (-1,0)',
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
						'0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
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
		],
	    */
        'crdate' => [
            'exclude' => true,
            //'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.crdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
		'finished' => [
			'exclude' => true,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult.finished',
			'config' => [
				'type' => 'check',
				'default' => '0'
			]
		],
		'survey' => [
			'exclude' => true,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult.survey',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_rkwsurvey_domain_model_survey',
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
			'exclude' => true,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult.question_result',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_rkwsurvey_domain_model_questionresult',
				'foreign_field' => 'survey_result',
				'maxitems' => 9999,
				'minitems' => 1,
				'appearance' => [
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				],
			],
		],
        'token' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult.token',
            'config' => [
                'type' => 'input',
                'size' => 255,
                'eval' => 'trim, required'
            ],
        ],
        'tags' => [
            'exclude' => true,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_surveyresult.tags',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'readOnly' => true,
            ],
        ],
	],
];
