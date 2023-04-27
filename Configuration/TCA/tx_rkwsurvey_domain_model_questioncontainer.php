<?php
return [
    'ctrl'      => [
        'title'                    => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer',
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
        'iconfile'                 => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_questioncontainer.gif',
        'hideTable'                => true,
    ],
    'interface' => [
        // 'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, short_name, weight, result_a, result_b, result_c, question',
        'showRecordFieldList' => 'hidden, name, hide_name_fe, description, question',
    ],
    'types'     => [
        // '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, short_name, weight, result_a, result_b, result_c, question'],
        '1' => ['showitem' => 'hidden, --palette--;LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer.palettes.name;name_pal,, description, question'],
    ],
    'palettes' => [
        'name_pal' => [
            'showitem' => 'name, hide_name_fe, last_name',
            'canNotCollapse' => 0
        ],
    ],
    'columns'   => [
        /*
        'sys_language_uid' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'special'    => 'languages',
                'items'      => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
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
            'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_rkwsurvey_domain_model_questioncontainer',
                'foreign_table_where' => 'AND tx_rkwsurvey_domain_model_questioncontainer.pid=###CURRENT_PID### AND tx_rkwsurvey_domain_model_questioncontainer.sys_language_uid IN (-1,0)',
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
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
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
            'label'     => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
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
            'label'     => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
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
            'label'   => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer.name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'hide_name_fe' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer.hide_name_fe',
            'config' => [
                'type' => 'check',
                'default' => 1,
            ],
        ],
        'description' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim, required',
                'enableRichtext' => true,
            ],
        ],
        'question' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_questioncontainer.question',
            'config'  => [
                'type' => 'inline',
                'foreign_table' => 'tx_rkwsurvey_domain_model_question',
                'foreign_field' => 'question_container',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'minitems' => 1,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
        'survey'          => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],

];
