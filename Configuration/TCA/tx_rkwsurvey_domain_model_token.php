<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_token',
		'hideTable' => 1,
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'searchFields' => 'name,used,survey,cruser_id',
		'iconfile' => 'EXT:rkw_survey/Resources/Public/Icons/tx_rkwsurvey_domain_model_token.gif'
	],
	'types' => [
		'1' => ['showitem' => 'name, used, survey'],
    ],
	'columns' => [
        'name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_token.name',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'trim',
                'readOnly' => true
            ],
        ],
        'used' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_token.used',
            'config' => [
                'type' => 'check',
                'default' => '0',
                'readOnly' => true
            ],
        ],
        'cruser_id' => [
            'exclude' => false,
            'label' => 'LLL:EXT:rkw_survey/Resources/Private/Language/locallang_db.xlf:tx_rkwsurvey_domain_model_token.cruser_id',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'trim, int',
                'readOnly' => true
            ],
        ],
        'survey' => [
			'config' => [
				'type' => 'passthrough',
			],
		],
	],
];
