<?php
namespace RKW\RkwSurvey\Validation;

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
 * Class PrivacyValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PrivacyValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * validation
     * Is called directly in controller and not via PhpDocs. So it's looks not like always
     *
     * @var integer $privacy
     * @return boolean
     */
    public function isValid($privacy): bool
    {
        $isValid = true;
        if (!$privacy) {
            $this->result->forProperty('privacy')->addError(
                new \TYPO3\CMS\Extbase\Error\Error(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'contactFormValidator.not_filled',
                        'rkw_survey'
                    ), 1541174426
                )
            );
            $isValid = false;
        }

        return $isValid;
    }
}

