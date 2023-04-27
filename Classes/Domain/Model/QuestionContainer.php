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
 * Class Topic
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionContainer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     * @validate NotEmpty
     */
    protected $name;

    /**
     * hideNameFe
     *
     * @var boolean
     */
    protected $hideNameFe = 1;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * question
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question>
     * @cascade remove
     */
    protected $question;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the hideNameFe
     *
     * @return boolean $hideNameFe
     */
    public function getHideNameFe()
    {
        return $this->hideNameFe;
    }

    /**
     * Sets the hideNameFe
     *
     * @param boolean $hideNameFe
     * @return void
     */
    public function setHideNameFe($hideNameFe)
    {
        $this->hideNameFe = $hideNameFe;
    }

    /**
     * Returns the hideNameFe
     *
     * @return boolean $hideNameFe
     */
    public function isHideNameFe()
    {
        return $this->hideNameFe;
    }

    /**
     * Returns the shortName
     *
     * @return string $shortName
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Sets the shortName
     *
     * @param string $shortName
     * @return void
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

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
        $this->question = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return void
     */
    public function addQuestion(Question $question): void
    {
        $this->question->attach($question);
    }

    /**
     * Removes a Question
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $questionToRemove The Question to be removed
     * @return void
     */
    public function removeQuestion(Question $questionToRemove): void
    {
        $this->question->detach($questionToRemove);
    }

    /**
     * Returns the questions
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question> $question
     */
    public function getQuestion(): ObjectStorage
    {
        return $this->question;
    }

    /**
     * Sets the questions
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwSurvey\Domain\Model\Question> $question
     * @return void
     */
    public function setQuestions(ObjectStorage $question): void
    {
        $this->question = $question;
    }
}
