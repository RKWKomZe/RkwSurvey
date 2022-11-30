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

use RKW\RkwSurvey\Domain\Model\Survey;

/**
 * CheckSurveyDateViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CheckSurveyDateViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('survey', Survey::class, 'The survey to check', true);
    }

    /**
     * check if we want to show this survey
     *
     * @return null|string
     */
    public function render(): ?string
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->arguments['survey'];

        if ($survey->getStarttime()) {
            if ($survey->getStarttime() > time()) {
                return 'early';
            }
        }

        if ($survey->getEndtime()) {
            if ($survey->getEndtime() < time()) {
                return 'tardy';
            }
        }

        return null;
    }

}
