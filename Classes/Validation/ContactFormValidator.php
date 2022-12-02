<?php

namespace RKW\RkwSurvey\Validation;

use Madj2k\CoreExtended\Utility\GeneralUtility as Common;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
 * Class ContactFormValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ContactFormValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * validation
     * Is called directly in controller and not via PhpDocs. So it's looks not like always
     *
     * @var array $contactForm
     * @return boolean|string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($contactForm)
    {
        $isValid = true;
        $settings = $this->getSettings();

        foreach ($contactForm as $field => $value) {

            if ($field == 'privacy') {
                continue;
            }

            // do only check, if field is a not required field or not shown in FE
            if (
                (
                    ($settings['contact']['required'][$field])
                    && ($settings['contact']['show'][$field])
                )
                || ($field == 'email')
            ) {

                if (
                    (
                        ($field == 'gender')
                        && ($value == 99)
                    )
                    || (
                        ($field != 'gender')
                        && (empty($value))
                    )
                ) {
                    $this->result->forProperty($field)->addError(
                        new \TYPO3\CMS\Extbase\Error\Error(
                            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                                'contactFormValidator.not_filled',
                                'rkw_survey'
                            ), 1541170766
                        )
                    );
                    $isValid = false;
                }
            }
        }

        // Check privacy
        if (!$contactForm['privacy']) {
            $this->result->addError(
                new \TYPO3\CMS\Extbase\Error\Error(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'contactFormValidator.privacy',
                        'rkw_survey'
                    ), 1541170767
                )
            );
            $isValid = false;
        }

        return $isValid;
        //===
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings($which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
    {

        return Common::getTypoScriptConfiguration('Rkwsurvey', $which);
        //===
    }
}

