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

use RKW\RkwSurvey\Domain\Model\Question;

/**
 * Class CountMultipleChoiceAnswersViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CountMultipleChoiceAnswersViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('question', Question::class, 'The question which answers should be searched for multiple-choice answers', true);
        $this->registerArgument('questionResultList', 'array', 'Array with given answers for questions', true);
        $this->registerArgument('answerToCount', 'string', 'The answer to count.', true);

    }

    /**
     * Render
     *
     * @return int
     */
    public function render(): int
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
        $question = $this->arguments['question'];

        /** @var array $questionResultList */
        $questionResultList = $this->arguments['questionResultList'];

        /** @var string answerToCount */
        $answerToCount = $this->arguments['answerToCount'];

        $countTotal = 0;
        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {
            $selectedAnswers = explode(',', $questionResult->getAnswer());
            if ($questionResult->getQuestion()->getUid() === $question->getUid()) {
                if (in_array($answerToCount, $selectedAnswers)) {
                    $countTotal++;
                }
            }
        }

        return $countTotal;
        //===
    }


}
