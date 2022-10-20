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
class CollectScaleAnswersViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('question', Question::class, 'The question which answers should be searched for scaled answers', true);
        $this->registerArgument('questionResultList', 'array', 'Array with given answers for questions', true);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        /** @var RKW\RkwSurvey\Domain\Model\Survey $question */
        $question = $this->arguments['question'];

        /** @var array $questionResultList */
        $questionResultList = $this->arguments['questionResultList'];

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
