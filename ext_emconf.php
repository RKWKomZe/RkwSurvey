<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "rkw_survey"
 *
 * Auto generated by Extension Builder 2017-12-04
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'RKW Survey',
	'description' => '',
	'category' => 'plugin',
	'author' => 'Maximilian Fäßler, Steffen Kroggel',
	'author_email' => 'maximilian@faesslerweb.de, developer@steffenkroggel.de',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '8.7.69',
	'constraints' => [
		'depends' => [
            'typo3' => '7.6.0-8.7.99',
			'rkw_mailer' => '8.7.0-9.5.99',
            'rkw_basics' => '8.7.82-8.7.99',
            'rkw_registration' => '8.7.0-8.7.99',
            'rte_ckeditor' => '8.7.31-8.7.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
