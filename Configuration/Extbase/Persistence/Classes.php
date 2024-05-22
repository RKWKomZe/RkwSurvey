<?php
declare(strict_types = 1);

return [
    \RKW\RkwSurvey\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
    # Optional dependency for rkw_events!
    \RKW\RkwEvents\Domain\Model\Survey::class => [
        'tableName' => 'tx_rkwsurvey_domain_model_survey',
    ],
];
