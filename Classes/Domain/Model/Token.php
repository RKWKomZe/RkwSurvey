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
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * used
     *
     * @var boolean
     */
    protected $used = 0;

    /**
     * cruserId
     *
     * @var int
     */
    protected $cruserId = 0;

    /**
     * survey
     *
     * @var \RKW\RkwSurvey\Domain\Model\Survey
     */
    protected $survey = null;

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
     * Returns the used
     *
     * @return string $used
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Sets the used
     *
     * @param string $used
     * @return void
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * Returns the cruserId
     *
     * @return integer $cruserId
     */
    public function getCruserId()
    {
        return $this->cruserId;
    }

    /**
     * Sets the cruserId
     *
     * @param integer $cruserId
     * @return void
     */
    public function setCruserId($cruserId)
    {
        $this->cruserId = $cruserId;
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
