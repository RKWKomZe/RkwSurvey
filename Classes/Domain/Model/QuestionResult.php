<?php

namespace RKW\RkwSurvey\Domain\Model;

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
 * QuestionResult
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionResult extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * answer
     *
     * @var string
     */
    protected $answer = '';

    /**
     * surveyResult
     *
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult
     */
    protected $surveyResult = null;

    /**
     * question
     *
     * @var \RKW\RkwSurvey\Domain\Model\Question
     */
    protected $question = null;

    /**
     * skipped
     *
     * @var boolean
     */
    protected $skipped = 0;


    /**
     * Returns the answer
     *
     * @return string $answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Sets the answer
     *
     * @param string $answer
     * @return void
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * Returns the surveyResult
     *
     * @return \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     */
    public function getSurveyResult()
    {
        return $this->surveyResult;
    }

    /**
     * Sets the surveyResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @return void
     */
    public function setSurveyResult(\RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult)
    {
        $this->surveyResult = $surveyResult;
    }

    /**
     * Returns the question
     *
     * @return \RKW\RkwSurvey\Domain\Model\Question $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return void
     */
    public function setQuestion(\RKW\RkwSurvey\Domain\Model\Question $question)
    {
        $this->question = $question;
    }

    /**
     * Returns the skipped
     *
     * @return boolean $skipped
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /**
     * Sets the skipped
     *
     * @param boolean $skipped
     * @return void
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;
    }

    /**
     * Returns the skipped
     *
     * @return boolean $skipped
     */
    public function isSkipped()
    {
        return $this->skipped;
    }
}
