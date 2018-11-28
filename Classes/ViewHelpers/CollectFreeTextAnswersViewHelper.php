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
 * Class CollectFreeTextAnswersViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CollectFreeTextAnswersViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param array $questionResultList
     * @return array
     */
    public function render(\RKW\RkwSurvey\Domain\Model\Question $question, $questionResultList)
    {
        $collectedAnswers = array();
        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {
            if ($questionResult->getQuestion()->getUid() === $question->getUid()) {
                if ($questionResult->getAnswer()) {
                    $collectedAnswers[] = $questionResult->getAnswer();
                }
            }
        }

        return $collectedAnswers;
        //===
    }


}