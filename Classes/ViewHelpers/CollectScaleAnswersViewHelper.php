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

/**
 * Class CollectScaleAnswersViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CollectScaleAnswersViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param array $questionResultList
     * @return array
     */
    public function render(\RKW\RkwSurvey\Domain\Model\Question $question, $questionResultList)
    {
        $collectedAnswers = array();

        // prefill key (they're maybe not selected)
        for ($i = 1; $i <= $question->getScaleToPoints(); $i++) {
            $collectedAnswers[$i] = 0;
        }

        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {
            if ($questionResult->getQuestion()->getUid() === $question->getUid()) {
                // handle empty values (if question is not mandatory)
                if (!$questionResult->getAnswer()) {
                    $collectedAnswers[0] = $collectedAnswers[0] + 1;
                } else {
                    $collectedAnswers[$questionResult->getAnswer()] = $collectedAnswers[$questionResult->getAnswer()] + 1;
                }
            }
        }

        return $collectedAnswers;
        //===
    }


}