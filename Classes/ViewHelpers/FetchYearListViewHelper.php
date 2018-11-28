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

/**
 * Class FetchYearListViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FetchYearListViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * returns a associated array with "year => year"
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $surveyList
     * @return array
     */
    public function render(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $surveyList)
    {
        $yearList = array();
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        foreach ($surveyList as $survey) {
            $yearList[date('Y', $survey->getStarttime())] = date('Y', $survey->getStarttime());
        }
        ksort($yearList);

        return $yearList;
        //===
    }
}