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
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ExplodeViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('string', 'string', 'The string to explode', true);
        $this->registerArgument('delimiter', 'string', 'The delimiter', false, '|');
    }


    /**
     * Render
     *
     * @return array
     */
    public function render(): array
    {
        /** @var string $string */
        $string = $this->arguments['string'];

        /** @var string $delimiter */
        $delimiter = $this->arguments['delimiter'];

        $items = GeneralUtility::trimExplode(PHP_EOL, $string, true);

        if (count($items) === 1) {
            $items = GeneralUtility::trimExplode($delimiter, $string, true);
        }

        return array_filter($items);
    }
}
