<?php

namespace RKW\RkwSurvey\Validation;
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

/**
 * Class QuestionResultValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionResultValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * validation
     * Is called directly in controller and not via PhpDocs. So it's looks not like always
     * Problem: The Validator itself works correct. But if he returns false at some question (which was not the first one),
     * extbase gets just another (previous) "questionResult" from the "surveyResult". Big problem!
     *
     * @var \RKW\RkwSurvey\Domain\Model\QuestionResult $newQuestionResult
     * @return boolean|string
     */
    public function isValid($newQuestionResult)
    {
        // don't validate, if field is not required
        if (!$newQuestionResult->getQuestion()->isRequired()) {
            return true;
            //===
        }

        $isValid = true;
        $message = '';

        if (!strlen($newQuestionResult->getAnswer())) {
            $message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'questionResultValidator.not_filled',
                'rkw_survey'
            );
            $isValid = false;
        }

        return $isValid ? $isValid : $message;
        //===
    }
}

