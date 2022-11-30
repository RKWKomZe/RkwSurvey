<?php

namespace RKW\RkwSurvey\Helper;

use \RKW\RkwSurvey\Domain\Model\SurveyResult;
use \RKW\RkwSurvey\Domain\Model\QuestionResult;

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
 * SurveyProgress
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyProgress extends \RKW\RkwSurvey\Utility\SurveyProgressUtility implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * constructor
     */
    public function __construct()
    {
        trigger_error(__CLASS__ . ' is deprecated and will be removed soon. Please use RKW\RkwSurvey\Utility\SurveyProgressUtility instead.', E_USER_DEPRECATED);
    }
}
