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
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $surveyRepository;

    /**
     * Question constructor
     * @return void
     */
    public function __construct()
    {

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        if (!$this->surveyRepository) {
            $this->surveyRepository = $objectManager->get(SurveyRepository::class);
        }
    }

    /**
     * @param array $array
     * @return bool
     */
    public function useTopic(array $array): bool
    {

        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifier($array['record']['survey']);

        if (
            ($survey)
            && ($survey->getType() == 1)
            && ($survey->getTopics())
            && ($survey->getTopics()->count())

        ){
            return true;
        }

        return false;

    }

    /**
     * @param array $array
     * @return bool
     */
    public function isGroupable(array $array): bool
    {

        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifier($array['record']['survey']);

        if (
            ($survey)
            && ($survey->getType() == 1)
            && ($array['record']['type'][0] == 2)
        ){
            return true;
        }

        return false;

    }

}
