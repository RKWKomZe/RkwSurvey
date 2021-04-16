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
 * SurveyResult
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyResult extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var integer
     */
    protected $crdate;

    /**
     * finished
     *
     * @var boolean
     */
    protected $finished = false;

    /**
     * token
     *
     * @var \RKW\RkwSurvey\Domain\Model\Token
     */
    protected $token = null;

    /**
     * survey
     *
     * @var \RKW\RkwSurvey\Domain\Model\Survey
     */
    protected $survey = null;

    /**
     * questionResult
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult>
     */
    protected $questionResult = null;

    /**
     * __construct
     */
    public function __construct()
    {
        // Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->questionResult = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the crdate value
     *
     * @return integer
     * @api
     */
    public function getCrdate()
    {
        return $this->crdate;
        //===
    }

    /**
     * Returns the finished
     *
     * @return boolean $finished
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Sets the finished
     *
     * @param boolean $finished
     * @return void
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * Returns the finished
     *
     * @return boolean $finished
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * Returns the token
     *
     * @return \RKW\RkwSurvey\Domain\Model\Token $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $token
     * @return void
     */
    public function setToken(\RKW\RkwSurvey\Domain\Model\Token $token)
    {
        $this->token = $token;
    }

    /**
     * Returns the survey
     *
     * @return \RKW\RkwSurvey\Domain\Model\Survey $survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Sets the survey
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return void
     */
    public function setSurvey(\RKW\RkwSurvey\Domain\Model\Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * Adds a QuestionResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove
     * @return void
     */
    public function addQuestionResult(\RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove)
    {
        $this->questionResult->attach($questionResultToRemove);
    }

    /**
     * Removes a QuestionResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove The Question to be removed
     * @return void
     */
    public function removeQuestionResult(\RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove)
    {
        $this->questionResult->detach($questionResultToRemove);
    }

    /**
     * Returns the questionResult
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult> $questionResult
     */
    public function getQuestionResult()
    {
        return $this->questionResult;
    }

    /**
     * Sets the questionResult
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $questionResult
     * @return void
     */
    public function setQuestionResult(\RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult)
    {
        $this->questionResult = $questionResult;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getBenchmarkQuestionResults()
    {
        $benchmarkQuestionResults = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        foreach ($this->getQuestionResult() as $questionResult) {
            if ($questionResult->getQuestion()->getBenchmark()) {
                $benchmarkQuestionResults->attach($questionResult);
            }
        }

        return $benchmarkQuestionResults;
    }

}
