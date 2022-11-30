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

use RKW\RkwSurvey\Domain\Model\Survey;

/**
 * Class CollectFreeTextAnswersViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CollectFreeTextAnswersViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('question', Question::class, 'The question which answers should be searched for free-text-answers', true);
        $this->registerArgument('questionResultList', 'array', 'Array with given answers for questions', true);
    }


    /**
     * @return array
     */
    public function render()
    {
        /** @var RKW\RkwSurvey\Domain\Model\Survey $question */
        $question = $this->arguments['question'];

        /** @var array $questionResultList */
        $questionResultList = $this->arguments['questionResultList'];

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
    }
}
