<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question',
		'hideTable' => 1,
		'label' => 'question',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		/*'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		*/
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		],
		'searchFields' => 'required,question,hint,short_name,type,text_consent,text_rejection,scale_to_points,answer_option,benchmark,benchmark_value,survey,do_action,do_action_if,do_action_jump',
		'iconfile' => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_question.gif'
	],
	'interface' => [
		// 'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, required, question, type, short_name, hint, text_consent, text_rejection, scale_from_points, scale_to_points, benchmark, answer_option, survey',
        'showRecordFieldList' => 'hidden, required, question, type, short_name, hint, text_consent, text_rejection, scale_from_points, scale_to_points, answer_option, benchmark, benchmark_value, do_action, do_action_if, do_action_jump, survey',

    ],
	'types' => [
        // '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, required, question, hint, short_name, type, text_consent, text_rejection, scale_from_points, scale_to_points, benchmark, answer_option, survey, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
		'1' => ['showitem' => 'hidden, required, question, hint, short_name, type, text_consent, text_rejection, scale_from_points, scale_to_points, answer_option, benchmark, benchmark_value, do_action, do_action_if, do_action_jump, survey, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
	'columns' => [
	    /*
		'sys_language_uid' => [
			'exclude' => false,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => [
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
						-1,
						'flags-multiple'
					]
				],
				'default' => 0,
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => false,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_rkwsurvey_domain_model_question',
				'foreign_table_where' => 'AND tx_rkwsurvey_domain_model_question.pid=###CURRENT_PID### AND tx_rkwsurvey_domain_model_question.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
	    */
		'hidden' => [
			'exclude' => false,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
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
			'exclude' => false,
			//'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
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
		'endtime' => [
			'exclude' => false,
			//'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
                'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'default' => 0,
				'range' => [
					'upper' => mktime(0, 0, 0, 1, 1, 2038)
				],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
			],
		],
		'required' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.required',
			'config' => [
				'type' => 'check',
				'default' => 1,
			],
		],
		'question' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.question',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, required'
			],
		],
        'short_name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.short_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required'
            ],
        ],
		'hint' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.hint',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			],
		],
		'type' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.type',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.type.free_text', 0],
					['LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.type.multiple', 1],
					['LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.type.single', 2],
					['LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.type.scale', 3],
				],
				'default' => 0
			],
            'onChange' => 'reload'
		],
        'benchmark' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.benchmark',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
            'displayCond' => 'FIELD:type:=:3',
            'onChange' => 'reload'
        ],
        'benchmark_value' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.benchmark.value',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim'
            ],
            'displayCond' => 'FIELD:benchmark:=:1',
        ],
		'answer_option' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.answer_option',
			'config' => [
				'type' => 'text',
				'cols' => 80,
				'rows' => 15,
			],
			'displayCond' => [
                'OR' => [
                    'FIELD:type:=:1',
                    'FIELD:type:=:2',
                ]
			],
		],
        'do_action' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.do_action',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
            'displayCond' => [
                'OR' => [
                    'FIELD:type:=:1',
                    'FIELD:type:=:2',
                    'FIELD:type:=:3',
                ]
            ],
            'onChange' => 'reload'
        ],
        'do_action_if' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.do_action_if',
            'config' => [
                'type' => 'input',
                'size' => 2,
                'eval' => 'trim, int'
            ],
            'displayCond' => 'FIELD:do_action:=:1',
        ],
        'do_action_jump' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.do_action_jump',
            'config' => [
                'type' => 'input',
                'size' => 2,
                'eval' => 'trim, int',
                'default' => 1,
            ],
            'displayCond' => 'FIELD:do_action:=:1',
        ],
		'text_consent' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.text_consent',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, required'
			],
			'displayCond' => 'FIELD:type:=:3',
		],
		'text_rejection' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.text_rejection',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim, required'
			],
			'displayCond' => 'FIELD:type:=:3',
		],
		'scale_to_points' => [
			'exclude' => false,
			'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_question.scale_to_points',
			'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
				'eval' => 'trim, int, required',
                'default' => 2,
                'items' => [
                    [2,2],
                    [3,3],
                    [4,4],
                    [5,5],
                    [6,6],
                    [7,7],
                    [8,8],
                    [9,9],
                    [10,10],
                ],
			],
			'displayCond' => 'FIELD:type:=:3',
		],
		'survey' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
	],
];
