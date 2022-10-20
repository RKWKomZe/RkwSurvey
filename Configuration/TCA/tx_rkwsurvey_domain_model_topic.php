<?php
return [
    'ctrl'      => [
        'title'                    => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_topic',
        'label'                    => 'name',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled' => 'hidden',
        ],
        'searchFields'             => 'name,short_name',
        'iconfile'                 => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_topic.gif',
        'hideTable'                => true,
    ],
    'interface' => [
        // 'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, short_name, weight, result_a, result_b, result_c, questions',
        'showRecordFieldList' => 'hidden, name, short_name',
    ],
    'types'     => [
        // '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, short_name, weight, result_a, result_b, result_c, questions'],
        '1' => ['showitem' => 'hidden, name, short_name'],
    ],
    'columns'   => [
        /*
        'sys_language_uid' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'special'    => 'languages',
                'items'      => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default'    => 0,
            ],
        ],
        'l10n_parent'      => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => true,
            'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_rkwsurvey_domain_model_topic',
                'foreign_table_where' => 'AND tx_rkwsurvey_domain_model_topic.pid=###CURRENT_PID### AND tx_rkwsurvey_domain_model_topic.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource'  => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        */
        'hidden'           => [
            'exclude' => false,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type'  => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],
        'starttime'        => [
            'exclude'   => false,
            //'l10n_mode' => 'mergeIfNotBlank',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'    => [
                'type'    => 'input',
                'renderType' => 'inputDateTime',
                'size'    => 13,
                'eval'    => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime'          => [
            'exclude'   => false,
            //'l10n_mode' => 'mergeIfNotBlank',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'type'    => 'input',
                'renderType' => 'inputDateTime',
                'size'    => 13,
                'eval'    => 'datetime',
                'default' => 0,
                'range'   => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'name'             => [
            'exclude' => false,
            'label'   => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_topic.name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'short_name'            => [
            'exclude' => false,
            'label'   => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_topic.short_name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'questions'        => [
            'exclude' => false,
            'label'   => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_topic.questions',
            'config'  => [
                'type'          => 'inline',
                'foreign_table' => 'tx_rkwsurvey_domain_model_question',
                'foreign_field' => 'topic'
            ],
        ],
        'survey'          => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],

];
