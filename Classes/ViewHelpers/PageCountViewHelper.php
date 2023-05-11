<?php

namespace RKW\RkwSurvey\ViewHelpers;
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

use Doctrine\Common\Util\Debug;
use RKW\RkwSurvey\Domain\Model\QuestionContainer;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * PageCountViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageCountViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * gets count of already answered questions and adds 1
     *
     * @param SurveyResult $surveyResult
     * @param int $start
     * @return integer
     */
    public function render(SurveyResult $surveyResult, $start = 1)
    {

        return $surveyResult->getQuestionResult()->count() + $start;
    }

}