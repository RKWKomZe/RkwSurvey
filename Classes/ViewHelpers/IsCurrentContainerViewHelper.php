<?php

namespace RKW\RkwSurvey\ViewHelpers;
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

use RKW\RkwSurvey\Domain\Model\QuestionContainer;
use RKW\RkwSurvey\Domain\Model\SurveyResult;

/**
 * IsCurrentContainerViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IsCurrentContainerViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('surveyResult', SurveyResult::class, 'The survey result', true);
        $this->registerArgument('questionContainerToCheck', QuestionContainer::class, 'The current question container to check', true);
    }

    /**
     * detects if a questionContainer is the current one (calculated by already answered questions of previous containers)
     *
     * @return bool
     */
    public function render(): bool
    {
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $surveyResult = $this->arguments['surveyResult'];
        /** @var \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer */
        $questionContainerToCheck = $this->arguments['questionContainerToCheck'];

        $alreadyAnsweredQuestionsCount = $surveyResult->getQuestionResult()->count();

        /** @var QuestionContainer $questionContainer */
        $containerQuestionCount = 0;
        foreach ($surveyResult->getSurvey()->getQuestionContainer() as $questionContainer){

            // if the questionCounts are equal, we're at the point where the next containers come around
            if ($containerQuestionCount == $alreadyAnsweredQuestionsCount) {
                if ($questionContainer->getUid() == $questionContainerToCheck->getUid()) {
                    return true;
                }
            }
            $containerQuestionCount += $questionContainer->getQuestion()->count();
        }

        return false;
    }

}
