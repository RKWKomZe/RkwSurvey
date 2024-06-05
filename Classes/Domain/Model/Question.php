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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Question
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Question extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     */
    protected string $question = '';


    /**
     * @var string
     */
    protected string $shortName = '';


    /**
     * @var string
     */
    protected string $hint = '';


    /**
     * @var bool
     */
    protected bool $required = true;


    /**
     * @var bool
     */
    protected bool $groupBy = false;


    /**
     * @var int
     */
    protected int $type = 0;


    /**
     * @var string
     */
    protected string $textConsent = '';


    /**
     * @var string
     */
    protected string $textRejection = '';


    /**
     * @var int
     */
    protected int $scaleFromPoints = 0;


    /**
     * @var int
     */
    protected int $scaleToPoints = 0;


    /**
     * @var int
     */
    protected int $scaleStep = 0;


    /**
     * @var bool
     */
    protected bool $benchmark = false;


    /**
     * @var float
     */
    protected float $benchmarkValue = 0.0;


    /**
     * @var string
     */
    protected string $benchmarkWeighting = '';


    /**
     * @var string
     */
    protected string $answerOption = '';


    /**
     * @var bool
     */
    protected bool $doAction = false;


    /**
     * @var string
     */
    protected string $doActionIf = '0';


    /**
     * @var int
     */
    protected int $doActionJump = 1;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Survey|null
     */
    protected ?Survey $survey = null;

    /**
     * @var \RKW\RkwSurvey\Domain\Model\QuestionContainer|null
     */
    protected ?QuestionContainer $questionContainer = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Topic|null
     */
    protected ?Topic $topic = null;


    /**
     * Returns the question
     *
     * @return string $question
     */
    public function getQuestion(): string
    {
        return $this->question;
    }


    /**
     * Sets the question
     *
     * @param string $question
     * @return void
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }


    /**
     * Returns the shortName
     *
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }


    /**
     * Sets the shortName
     *
     * @param string $shortName
     * @return void
     */
    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }


    /**
     * Returns the hint
     *
     * @return string
     */
    public function getHint(): string
    {
        return $this->hint;
    }


    /**
     * Sets the hint
     *
     * @param string $hint
     * @return void
     */
    public function setHint(string $hint): void
    {
        $this->hint = $hint;
    }


    /**
     * Returns the required
     *
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Sets the required
     *
     * @param bool $required
     * @return void
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }


    /**
     * Returns the required
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }


    /**
     * Returns the groupBy
     *
     * @return boolean
     */
    public function getGroupBy(): bool
    {
        return $this->groupBy;
    }


    /**
     * Sets the groupBy
     *
     * @param bool $groupBy
     * @return void
     */
    public function setGroupBy(bool $groupBy): void
    {
        $this->groupBy = $groupBy;
    }


    /**
     * Returns the type
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }


    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }


    /**
     * Returns the textConsent
     *
     * @return string
     */
    public function getTextConsent(): string
    {
        return $this->textConsent;
    }


    /**
     * Sets the textConsent
     *
     * @param string $textConsent
     * @return void
     */
    public function setTextConsent(string $textConsent): void
    {
        $this->textConsent = $textConsent;
    }


    /**
     * Returns the textRejection
     *
     * @return string
     */
    public function getTextRejection(): string
    {
        return $this->textRejection;
    }


    /**
     * Sets the textRejection
     *
     * @param string $textRejection
     * @return void
     */
    public function setTextRejection(string $textRejection): void
    {
        $this->textRejection = $textRejection;
    }


    /**
     * Returns the scaleFromPoints
     *
     * @return int
     */
    public function getScaleFromPoints(): int
    {
        return $this->scaleFromPoints;
    }


    /**
     * Sets the scaleFromPoints
     *
     * @param int $scaleFromPoints
     * @return void
     */
    public function setScaleFromPoints(int $scaleFromPoints): void
    {
        $this->scaleFromPoints = $scaleFromPoints;
    }


    /**
     * Returns the scaleToPoints
     *
     * @return int
     */
    public function getScaleToPoints(): int
    {
        return $this->scaleToPoints;
    }


    /**
     * Sets the scaleToPoints
     *
     * @param int $scaleToPoints
     * @return void
     */
    public function setScaleToPoints(int $scaleToPoints): void
    {
        $this->scaleToPoints = $scaleToPoints;
    }


    /**
     * Returns the benchmark
     *
     * @return bool
     */
    public function getBenchmark(): bool
    {
        return $this->benchmark;
    }


    /**
     * Sets the benchmark
     *
     * @param bool $benchmark
     * @return void
     */
    public function setBenchmark(bool $benchmark): void
    {
        $this->benchmark = $benchmark;
    }


    /**
     * Returns the scaleStep
     *
     * @return int
     */
    public function getScaleStep(): int
    {
        return $this->scaleStep;
    }


    /**
     * Sets the scaleStep
     *
     * @param int $scaleStep
     * @return void
     */
    public function setScaleStep(int $scaleStep): void
    {
        $this->scaleStep = $scaleStep;
    }


    /**
     * Returns the benchmarkValue
     *
     * @return float
     */
    public function getBenchmarkValue(): float
    {
        return $this->benchmarkValue;
    }


    /**
     * Sets the benchmarkValue
     *
     * @param float $benchmarkValue
     * @return void
     */
    public function setBenchmarkValue(float $benchmarkValue): void
    {
        $this->benchmarkValue = $benchmarkValue;
    }


    /**
     * Returns the benchmarkWeighting
     *
     * @return string
     */
    public function getBenchmarkWeighting(): string
    {
        return $this->benchmarkWeighting;
    }


    /**
     * Sets the benchmarkWeighting
     *
     * @param string $benchmarkWeighting
     * @return void
     */
    public function setBenchmarkWeighting(string $benchmarkWeighting): void
    {
        $this->benchmarkWeighting = $benchmarkWeighting;
    }


    /**
     * Returns the answerOption
     *
     * @return string
     */
    public function getAnswerOption(): string
    {
        return $this->answerOption;
    }


    /**
     * Sets the answerOption
     *
     * @param string $answerOption
     * @return void
     */
    public function setAnswerOption(string $answerOption): void
    {
        $this->answerOption = $answerOption;
    }


    /**
     * Returns the doAction
     *
     * @return bool
     */
    public function getDoAction(): bool
    {
        return $this->doAction;
    }


    /**
     * Sets the doAction
     *
     * @param bool $doAction
     * @return void
     */
    public function setDoAction(bool $doAction): void
    {
        $this->doAction = $doAction;
    }


    /**
     * Returns the doAction
     *
     * @return bool
     */
    public function isDoAction(): bool
    {
        return $this->doAction;
    }


    /**
     * Returns the doActionIf
     *
     * @return array
     */
    public function getDoActionIf(): array
    {
        return array_map('intval', GeneralUtility::trimExplode(',', $this->doActionIf));
    }


    /**
     * Sets the doActionIf
     *
     * @param string $doActionIf
     * @return void
     */
    public function setDoActionIf(string $doActionIf): void
    {
        $this->doActionIf = $doActionIf;
    }


    /**
     * Returns the doActionJump
     *
     * @return int
     */
    public function getDoActionJump(): int
    {
        return $this->doActionJump;
    }


    /**
     * Sets the doActionJump
     *
     * @param int $doActionJump
     * @return void
     */
    public function setDoActionJump(int $doActionJump): void
    {
        $this->doActionJump = $doActionJump;
    }


    /**
     * Returns the survey
     *
     * @return \RKW\RkwSurvey\Domain\Model\Survey|null
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
     * Returns the questionContainer
     *
     * @return \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer
     */
    public function getQuestionContainer():? QuestionContainer
    {
        return $this->questionContainer;
    }


    /**
     * Sets the questionContainer
     *
     * @param \RKW\RkwSurvey\Domain\Model\QuestionContainer $questionContainer
     * @return void
     */
    public function setQuestionContainer(QuestionContainer $questionContainer): void
    {
        $this->questionContainer = $questionContainer;
    }


    /**
     * Returns the topic
     *
     * @return \RKW\RkwSurvey\Domain\Model\Topic $topic
     */
    public function getTopic():? Topic
    {
        return $this->topic;
    }


    /**
     * Sets the topic
     *
     * @param \RKW\RkwSurvey\Domain\Model\Topic $topic
     * @return void
     */
    public function setTopic(Topic $topic): void
    {
        $this->topic = $topic;
    }


    /**
     * Returns the range for scaled questions
     *
     * @return array
     */
    public function getScale(): array
    {
        if (! $range = range($this->scaleFromPoints, $this->scaleToPoints, $this->scaleStep)) {
            return [];
        }

        return $range;

    }
}
