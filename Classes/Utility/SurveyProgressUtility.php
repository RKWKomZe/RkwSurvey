<?php
namespace RKW\RkwSurvey\Utility;

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

use RKW\RkwSurvey\Domain\Model\SurveyResult;
use RKW\RkwSurvey\Domain\Model\QuestionResult;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SurveyProgress
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyProgressUtility implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * handleJumpAction
     * in every question can be defined to jump X questions on result Y
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $newQuestionResult
     * @return void
     */
    public static function handleJumpAction(SurveyResult $surveyResult, QuestionResult $newQuestionResult): void
    {
        // optional, if set: Skip next question(s), if a condition take effect
        if (
            $newQuestionResult->getQuestion()->isDoAction()
            && in_array(intval($newQuestionResult->getAnswer()), $newQuestionResult->getQuestion()->getDoActionIf())
        ) {
            // add (a) questions to jump and (b) already answered questions
            $newAnswerCount = $newQuestionResult->getQuestion()->getDoActionJump() + $surveyResult->getQuestionResult()->count();

            // iterate questions
            $i = 0;
            /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
            foreach ($surveyResult->getSurvey()->getQuestion() as $question) {
                // iterate until questions which are not answered yet
                if ($i < $surveyResult->getQuestionResult()->count()) {
                    $i++;
                    continue;
                }
                $i++;

                if ($newAnswerCount > $surveyResult->getQuestionResult()->count()) {
                    /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
                    $questionResult = GeneralUtility::makeInstance(QuestionResult::class);
                    $questionResult->setSkipped(true);
                    $questionResult->setQuestion($question);
                    $questionResult->setSurveyResult($surveyResult);
                    $surveyResult->addQuestionResult($questionResult);
                }

                // on the one hand: Simply quit if work is done
                // on the other hand: Fetch it, if someone want to skip XXXXX questions (for safety - condition should never reached)
                if (
                    $newAnswerCount == $surveyResult->getQuestionResult()->count()
                    || $surveyResult->getQuestionResult()->count() == $surveyResult->getSurvey()->getQuestion()->count()
                ) {
                    break;
                }
            }
        }
    }
}
