<?php
namespace RKW\RkwSurvey\DisplayCond;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use RKW\RkwSurvey\Domain\Repository\SurveyRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 *  Question
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Question
{

    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected SurveyRepository $surveyRepository;


    /**
     * Question constructor
     * @return void
     */
    public function __construct()
    {

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

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
