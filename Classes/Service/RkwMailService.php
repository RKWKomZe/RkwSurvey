<?php
namespace RKW\RkwSurvey\Service;

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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwMailer\Service\MailService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use RKW\RkwSurvey\Domain\Model\SurveyResult;

/**
 * RkwMailService
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RkwMailService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * newSurveyAdmin
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $backendUserList
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @return void
     * @throws \Exception
     * @throws \RKW\RkwMailer\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function newSurveyAdmin(ObjectStorage $backendUserList, SurveyResult $surveyResult): void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        if ($settings['view']['templateRootPaths']) {

            /** @var \RKW\RkwMailer\Service\MailService $mailService */
            $mailService = GeneralUtility::makeInstance(MailService::class);

            /** @var \RKW\RkwSurvey\Domain\Model\BackendUser $backendUser */
            foreach ($backendUserList->toArray() as $backendUser) {
                $mailService->setTo($backendUser, array(
                    'marker'  => array(
                        'surveyResult' => $surveyResult,
                        'backendUser'  => $backendUser,
                    ),
                    'subject' => \RKW\RkwMailer\Utility\FrontendLocalizationUtility::translate(
                        'rkwMailService.newSurveyAdmin.subject',
                        'rkw_survey',
                        null,
                        $backendUser->getLang()
                    ),
                ));
            }

            $mailService->getQueueMail()->setSubject(
                \RKW\RkwMailer\Utility\FrontendLocalizationUtility::translate(
                    'rkwMailService.newSurveyAdmin.subject',
                    'rkw_survey',
                    null,
                    'de'
                )
            );
            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->setPlaintextTemplate('Email/NewSurveyAdmin');
            $mailService->getQueueMail()->setHtmlTemplate('Email/NewSurveyAdmin');
            $mailService->send();
        }
    }


    /**
     * sendContactForm
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $backendUserList
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param array $contactForm
     * @return void
     * @throws \Exception
     * @throws \RKW\RkwMailer\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function sendContactForm(ObjectStorage $backendUserList, SurveyResult $surveyResult, array $contactForm): void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if ($settings['view']['templateRootPaths']) {

            /** @var \RKW\RkwMailer\Service\MailService $mailService */
            $mailService = GeneralUtility::makeInstance(MailService::class);

            /** @var \RKW\RkwSurvey\Domain\Model\BackendUser $backendUser */
            foreach ($backendUserList as $backendUser) {

                $mailService->setTo($backendUser, array(
                    'marker'  => array(
                        'surveyResult' => $surveyResult,
                        'contactForm'  => $contactForm,
                        'backendUser'  => $backendUser,
                    ),
                    'subject' => \RKW\RkwMailer\Utility\FrontendLocalizationUtility::translate(
                        'rkwMailService.contactAdmin.subject',
                        'rkw_survey',
                        null,
                        $backendUser->getLang()
                    ),
                ));
            }

            if ($contactForm['email']) {
                $mailService->getQueueMail()->setReplyToAddress($contactForm['email']);
            }

            $mailService->getQueueMail()->setSubject(
                \RKW\RkwMailer\Utility\FrontendLocalizationUtility::translate(
                    'rkwMailService.contactAdmin.subject',
                    'rkw_survey',
                    null,
                    'de'
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->setPlaintextTemplate('Email/ContactAdmin');
            $mailService->getQueueMail()->setHtmlTemplate('Email/ContactAdmin');
            $mailService->send();
        }
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwsurvey', $which);
    }
}
