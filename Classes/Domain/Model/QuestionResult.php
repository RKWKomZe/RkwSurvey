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
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionResult extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     */
    protected string $answer = '';


    /**
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult|null
     */
    protected ?SurveyResult $surveyResult = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Question|null
     */
    protected ?Question $question = null;


    /**
     * @var bool
     */
    protected bool $skipped = false;


    /**
     * Returns the answer
     *
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }


    /**
     * Sets the answer
     *
     * @param string $answer
     * @return void
     */
    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }


    /**
     * Returns the surveyResult
     *
     * @return \RKW\RkwSurvey\Domain\Model\SurveyResult|null
     */
    public function getSurveyResult():? SurveyResult
    {
        return $this->surveyResult;
    }


    /**
     * Sets the surveyResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @return void
     */
    public function setSurveyResult(SurveyResult $surveyResult): void
    {
        $this->surveyResult = $surveyResult;
    }


    /**
     * Returns the question
     *
     * @return \RKW\RkwSurvey\Domain\Model\Question|null
     */
    public function getQuestion():? Question
    {
        return $this->question;
    }


    /**
     * Sets the question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return void
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }


    /**
     * Returns the skipped
     *
     * @return bool $skipped
     */
    public function getSkipped(): bool
    {
        return $this->skipped;
    }


    /**
     * Sets the skipped
     *
     * @param boolean $skipped
     * @return void
     */
    public function setSkipped(bool $skipped): void
    {
        $this->skipped = $skipped;
    }


    /**
     * Returns the skipped
     *
     * @return bool
     */
    public function isSkipped(): bool
    {
        return $this->skipped;
    }
}
