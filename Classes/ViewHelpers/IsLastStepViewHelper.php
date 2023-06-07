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

use Doctrine\Common\Util\Debug;
use RKW\RkwSurvey\Domain\Model\QuestionContainer;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * IsLastStepViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IsLastStepViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
    }

    /**
     * gets count of already answered questions and adds 1
     *
     * @return int
     */
    public function render(): int
    {
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $surveyResult = $this->arguments['surveyResult'];

        $questionResultCountTotal = $surveyResult->getQuestionResult()->count();

        if ($surveyResult->getSurvey()->getType() == 2) {

            $containerQuestionCountTotal = 0;
            $questionCountLastContainer = 0;
            /** @var QuestionContainer $questionContainer */
            foreach ($surveyResult->getSurvey()->getQuestionContainer() as $questionContainer){

                // simply override until the end to get the last one
                $questionCountLastContainer = $questionContainer->getQuestion()->count();
            }

            if ($surveyResult->getSurvey()->getQuestionCountTotal() == ($questionResultCountTotal + $questionCountLastContainer)) {
                return true;
            }


        } else {

            if ($surveyResult->getSurvey()->getQuestionCountTotal() == ($questionResultCountTotal + 1)) {
                return true;
            }
        }

        // general return false
        return false;
    }

}