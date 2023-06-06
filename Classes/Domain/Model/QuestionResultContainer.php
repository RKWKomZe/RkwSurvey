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
 * QuestionResultContainer
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionResultContainer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult|null
     */
    protected ?SurveyResult $surveyResult = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult>|null
     */
    protected ?ObjectStorage $questionResult = null;


    /**
     * skipped
     *
     * @var bool
     */
    protected bool $skipped = false;


    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
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
    protected function initStorageObjects():void
    {
        $this->questionResult = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }


    /**
     * Returns the surveyResult
     *
     * @return \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
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
    public function setSurveyResult(SurveyResult $surveyResult):void
    {
        $this->surveyResult = $surveyResult;
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
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult> $questionResult
     * @return void
     */
    public function setQuestionResult(ObjectStorage $questionResult): void
    {
        $this->questionResult = $questionResult;
    }


    /**
     * Returns the skipped
     *
     * @return bool
     */
    public function getSkipped(): bool
    {
        return $this->skipped;
    }


    /**
     * Sets the skipped
     *
     * @param bool $skipped
     * @return void
     */
    public function setSkipped(bool $skipped): void
    {
        $this->skipped = $skipped;
    }


    /**
     * Returns the skipped
     *
     * @return bool$skipped
     */
    public function isSkipped(): bool
    {
        return $this->skipped;
    }
}
