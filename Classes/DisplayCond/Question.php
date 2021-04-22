<?php

namespace RKW\RkwSurvey\DisplayCond;

use RKW\RkwSurvey\Domain\Repository\SurveyRepository;

/**
 * This file is part of the "RkwSurvey" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */


/**
 * Checks display conditions on TCA
 */
class Question
{

    /**
     * surveyRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyRepository
     * @inject
     */
    protected $surveyRepository;

    public function __construct() {

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        if (!$this->surveyRepository) {
            $this->surveyRepository = $objectManager->get(SurveyRepository::class);
        }
    }

    public function useTopic(array $array) {

        $survey = $this->surveyRepository->findByUid($array['record']['survey']);

        return $survey->getTopics()->count() > 0;

    }

}