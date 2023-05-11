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
use RKW\RkwAjax\Utilities\GeneralUtility;
use RKW\RkwSurvey\Domain\Model\QuestionContainer;
use RKW\RkwSurvey\Domain\Model\QuestionResult;
use RKW\RkwSurvey\Domain\Model\QuestionResultContainer;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * GetFormFieldValueViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class GetFormFieldValueViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('questionResultContainer', QuestionResultContainer::class, 'The complete question result container', true);
        $this->registerArgument('containerIter', 'int', 'The current container iteration to identify the searched container', true);
        $this->registerArgument('questionIter', 'int', 'Has to be set vor multiple choice questions to extract the searched value', false, 0);
    }

    /**
     * extract the form value from the previous questionContainerResultArray
     *
     * @return string
     */
    public function render(): string
    {
        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResultContainer $questionResultContainer */
        $questionResultContainer = $this->arguments['questionResultContainer'];
        $containerIter = $this->arguments['containerIter'];
        $questionIter = $this->arguments['questionIter'];

        if (!$questionResultContainer instanceof QuestionResultContainer) {
            return '';
        }

        $questionResultList = $questionResultContainer->getQuestionResult()->toArray();

        if (!key_exists($containerIter, $questionResultList)) {
            return '';
        }

        /** @var QuestionResult $questionResult */
        $questionResult = $questionResultList[$containerIter];

        if (!$questionIter) {
            return $questionResult->getAnswer();
        } else {
            // multi field fields (checkboxes, radio)
            $selectedAnswers = GeneralUtility::trimExplode(',', $questionResult->getAnswer());

            // @toDo: this return value is boolean, not string (typecast). Maybe adjust the typecast in PHP 8
            return in_array($questionIter, $selectedAnswers);
        }
    }

}