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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExplodeViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ExplodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $string
     * @param string $delimiter
     * @return array
     */
    public function render($string, $delimiter = '|')
    {

        $options = [];

        $lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(PHP_EOL, $string, true);
        if (count($lines) > 1) {
            $options = $lines;
        } else {
            $options = GeneralUtility::trimExplode($delimiter, $string, true);
        }

        // additional: Filter empty entries
        return array_filter($options);
        //===
    }
}