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

use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class FetchYearListViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FetchYearListViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{


    /**
     * Initialize arguments.
     *
     * @return void
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('surveyList', QueryResult::class, 'The list of surveys', true);
    }


    /**
     * returns an associated array with "year => year"
     *
     * @return array
     */
    public function render(): array
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $surveyList */
        $surveyList = $this->arguments['surveyList'];

        $yearList = array();
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        foreach ($surveyList as $survey) {
            $yearList[date('Y', $survey->getStarttime())] = date('Y', $survey->getStarttime());
        }
        ksort($yearList);

        return $yearList;
    }
}
