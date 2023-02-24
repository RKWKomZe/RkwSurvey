<?php
namespace RKW\RkwSurvey\ViewHelpers;
/*
 *
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
 * PageCountViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageCountViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('questionResultList', 'array', 'Array with given answers for questions', true);
        $this->registerArgument('start', 'int', 'Page number to start from', false, 1);
    }


    /**
     * Gets count of already answered questions and adds 1
     *
     * @return int
     */
    public function render(): int
    {
        /** @var array $questionResultList */
        $questionResultList = $this->arguments['questionResultList'];

        /** @var int $start */
        $start = $this->arguments['start'];

        return count($questionResultList) + $start;
    }

}
