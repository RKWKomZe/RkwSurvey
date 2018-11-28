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
 * Question
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Question extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * question
     *
     * @var string
     */
    protected $question = '';

    /**
     * hint
     *
     * @var string
     */
    protected $hint = '';

    /**
     * required
     *
     * @var boolean
     */
    protected $required = 1;

    /**
     * type
     *
     * @var integer
     */
    protected $type = 0;

    /**
     * textConsent
     *
     * @var string
     */
    protected $textConsent = '';

    /**
     * textRejection
     *
     * @var string
     */
    protected $textRejection = '';

    /**
     * scaleFromPoints
     *
     * @var integer
     */
    protected $scaleFromPoints = 0;

    /**
     * scaleToPoints
     *
     * @var integer
     */
    protected $scaleToPoints = 0;

    /**
     * answerOption
     *
     * @var string
     */
    protected $answerOption = '';

    /**
     * doAction
     *
     * @var boolean
     */
    protected $doAction = 0;

    /**
     * doActionIf
     *
     * @var integer
     */
    protected $doActionIf = 0;

    /**
     * doActionJump
     *
     * @var integer
     */
    protected $doActionJump = 1;

    /**
     * survey
     *
     * @var \RKW\RkwSurvey\Domain\Model\Survey
     */
    protected $survey = null;

    /**
     * Returns the question
     *
     * @return string $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param string $question
     * @return void
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Returns the hint
     *
     * @return string $hint
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Sets the hint
     *
     * @param string $hint
     * @return void
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }

    /**
     * Returns the required
     *
     * @return boolean $required
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Sets the required
     *
     * @param boolean $required
     * @return void
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * Returns the required
     *
     * @return boolean $required
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Returns the type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the textConsent
     *
     * @return string $textConsent
     */
    public function getTextConsent()
    {
        return $this->textConsent;
    }

    /**
     * Sets the textConsent
     *
     * @param string $textConsent
     * @return void
     */
    public function setTextConsent($textConsent)
    {
        $this->textConsent = $textConsent;
    }

    /**
     * Returns the textRejection
     *
     * @return string $textRejection
     */
    public function getTextRejection()
    {
        return $this->textRejection;
    }

    /**
     * Sets the textRejection
     *
     * @param string $textRejection
     * @return void
     */
    public function setTextRejection($textRejection)
    {
        $this->textRejection = $textRejection;
    }

    /**
     * Returns the scaleFromPoints
     *
     * @return int $scaleFromPoints
     */
    public function getScaleFromPoints()
    {
        return $this->scaleFromPoints;
    }

    /**
     * Sets the scaleFromPoints
     *
     * @param int $scaleFromPoints
     * @return void
     */
    public function setScaleFromPoints($scaleFromPoints)
    {
        $this->scaleFromPoints = $scaleFromPoints;
    }

    /**
     * Returns the scaleToPoints
     *
     * @return int $scaleToPoints
     */
    public function getScaleToPoints()
    {
        return $this->scaleToPoints;
    }

    /**
     * Sets the scaleToPoints
     *
     * @param int $scaleToPoints
     * @return void
     */
    public function setScaleToPoints($scaleToPoints)
    {
        $this->scaleToPoints = $scaleToPoints;
    }

    /**
     * Returns the answerOption
     *
     * @return int $answerOption
     */
    public function getAnswerOption()
    {
        return $this->answerOption;
    }

    /**
     * Sets the answerOption
     *
     * @param int $answerOption
     * @return void
     */
    public function setAnswerOption($answerOption)
    {
        $this->answerOption = $answerOption;
    }

    /**
     * Returns the doAction
     *
     * @return boolean $doAction
     */
    public function getDoAction()
    {
        return $this->doAction;
    }

    /**
     * Sets the doAction
     *
     * @param boolean $doAction
     * @return void
     */
    public function setDoAction($doAction)
    {
        $this->doAction = $doAction;
    }

    /**
     * Returns the doAction
     *
     * @return boolean $doAction
     */
    public function isDoAction()
    {
        return $this->doAction;
    }

    /**
     * Returns the doActionIf
     *
     * @return int $doActionIf
     */
    public function getDoActionIf()
    {
        return $this->doActionIf;
    }

    /**
     * Sets the doActionIf
     *
     * @param int $doActionIf
     * @return void
     */
    public function setDoActionIf($doActionIf)
    {
        $this->doActionIf = $doActionIf;
    }

    /**
     * Returns the doActionJump
     *
     * @return int $doActionJump
     */
    public function getDoActionJump()
    {
        return $this->doActionJump;
    }

    /**
     * Sets the doActionJump
     *
     * @param int $doActionJump
     * @return void
     */
    public function setDoActionJump($doActionJump)
    {
        $this->doActionJump = $doActionJump;
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
}
