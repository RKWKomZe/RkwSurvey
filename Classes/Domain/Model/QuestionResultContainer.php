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
     * surveyResult
     *
     * @var \RKW\RkwSurvey\Domain\Model\SurveyResult
     */
    protected $surveyResult = null;

    /**
     * question
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult>
     */
    protected $questionResult = null;

    /**
     * skipped
     *
     * @var boolean
     */
    protected $skipped = 0;

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
    protected function initStorageObjects()
    {
        $this->questionResult = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\QuestionResult> $questionResult
     * @return void
     */
    public function setQuestionResult(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $questionResult)
    {
        $this->questionResult = $questionResult;
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
