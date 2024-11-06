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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


/**
 * SurveyResult
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyResult extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var bool
     */
    protected bool $finished = false;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Token|null
     */
    protected ?Token $token = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Survey|null
     */
    protected ?Survey $survey = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult>|null
     */
    protected ?ObjectStorage $questionResult = null;


    /**
     * @var string
     */
    protected string $tags = '';


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
     * Returns the finished
     *
     * @return bool
     */
    public function getFinished(): bool
    {
        return $this->finished;
    }


    /**
     * Sets the finished
     *
     * @param boolean $finished
     * @return void
     */
    public function setFinished(bool $finished): void
    {
        $this->finished = $finished;
    }


    /**
     * Returns the finished
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }


    /**
     * Returns the token
     *
     * @return \RKW\RkwSurvey\Domain\Model\Token
     */
    public function getToken():? Token
    {
        return $this->token;
    }


    /**
     * Sets the token
     *
     * @param \RKW\RkwSurvey\Domain\Model\Token $token
     * @return void
     */
    public function setToken(Token $token): void
    {
        $this->token = $token;
    }


    /**
     * Returns the survey
     *
     * @return \RKW\RkwSurvey\Domain\Model\Survey
     */
    public function getSurvey():? Survey
    {
        return $this->survey;
    }


    /**
     * Sets the survey
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return void
     */
    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }


    /**
     * Adds a QuestionResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove
     * @return void
     */
    public function addQuestionResult(QuestionResult $questionResultToRemove): void
    {
        $this->questionResult->attach($questionResultToRemove);
    }


    /**
     * Removes a QuestionResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResultToRemove The Question to be removed
     * @return void
     */
    public function removeQuestionResult(QuestionResult $questionResultToRemove)
    {
        $this->questionResult->detach($questionResultToRemove);
    }


    /**
     * Returns the questionResult
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult> $questionResult
     */
    public function getQuestionResult(): ObjectStorage
    {
        return $this->questionResult;
    }


    /**
     * Sets the questionResult
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $questionResult
     * @return void
     */
    public function setQuestionResult(ObjectStorage $questionResult): void
    {
        $this->questionResult = $questionResult;
    }


    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getBenchmarkQuestionResults(): ObjectStorage
    {
        $benchmarkQuestionResults = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        foreach ($this->getQuestionResult() as $questionResult) {
            if ($questionResult->getQuestion()->getBenchmark()) {
                $benchmarkQuestionResults->attach($questionResult);
            }
        }

        return $benchmarkQuestionResults;
    }

    /**
     * Returns the tags
     *
     * @return string
     */
    public function getTags(): string
    {
        return $this->tags;
    }


    /**
     * Sets the tags
     *
     * @param string $tags
     * @return void
     */
    public function setTags(string $tags): void
    {
        $this->tags = $tags;
    }

}
