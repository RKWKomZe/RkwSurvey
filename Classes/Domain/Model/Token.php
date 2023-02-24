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
 * Token
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Token extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected string $name = '';


    /**
     * @var bool
     */
    protected bool $used = false;


    /**
     * @var int
     */
    protected int $cruserId = 0;


    /**
     * @var \RKW\RkwSurvey\Domain\Model\Survey
     */
    protected ?Survey $survey = null;


    /**
     * Returns the name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }


    /**
     * Returns the used
     *
     * @return bool
     */
    public function getUsed(): bool
    {
        return $this->used;
    }


    /**
     * Sets the used
     *
     * @param bool $used
     * @return void
     */
    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }


    /**
     * Returns the cruserId
     *
     * @return int
     */
    public function getCruserId(): int
    {
        return $this->cruserId;
    }


    /**
     * Sets the cruserId
     *
     * @param int $cruserId
     * @return void
     */
    public function setCruserId(int $cruserId): void
    {
        $this->cruserId = $cruserId;
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
}
