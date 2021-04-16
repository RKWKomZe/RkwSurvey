<?php

namespace RKW\RkwSurvey\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use RKW\RkwSurvey\Domain\Model\Survey;
use RKW\RkwSurvey\Domain\Model\Evaluator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use RKW\RkwSurvey\Domain\Model\QuestionResult;
use RKW\RkwSurvey\Utility\SurveyProgressUtility;

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
 * SurveyController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_CREATING_SURVEY = 'afterCreatingSurvey';

    /**
     * surveyRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyRepository
     * @inject
     */
    protected $surveyRepository;

    /**
     * surveyResultRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     * @inject
     */
    protected $surveyResultRepository;

    /**
     * questionResultRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @inject
     */
    protected $questionResultRepository;

    /**
     * tokenRepository
     *
     * @var \RKW\RkwSurvey\Domain\Repository\TokenRepository
     * @inject
     */
    protected $tokenRepository;

    /**
     * questionResultValidator
     *
     * @var \RKW\RkwSurvey\Validation\QuestionResultValidator
     * @inject
     */
    protected $questionResultValidator;

    /**
     * contactFormValidator
     *
     * @var \RKW\RkwSurvey\Validation\ContactFormValidator
     * @inject
     */
    protected $contactFormValidator;

    /**
     * rkwMailService
     *
     * @var \RKW\RkwSurvey\Service\RkwMailService
     * @inject
     */
    protected $rkwMailService;

    /**
     * persistenceManager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * Expose the pageRenderer
     *
     * @var $pageRenderer
     */
    protected $pageRenderer;

    /**
     * action welcome
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function welcomeAction(Survey $survey = null, $tokenInput = null)
    {
        if (
            (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('token'))
            && (!$tokenInput)
        ) {
            $tokenInput = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('token');
        }

        $this->view->assignMultiple(
            array(
                'survey'     => $survey ? $survey : $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($this->settings['selectedSurvey'])),
                'tokenInput' => $tokenInput,
            )
        );
    }


    /**
     * action start
     * ! Use this as entry point for signalSlot usage, if you want to start immediately without welcome !
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $extensionSuffix
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function startAction(Survey $survey, $extensionSuffix = null, $tokenInput = null)
    {
        // If access restricted, the initial assignment will be done here
        // Is also returning initial surveyResult-Object (existing or new)
        $surveyResult = $this->checkInitialAccessRestriction($survey, $tokenInput);

        // create new surveyResult (if not exists)
        if ($surveyResult->_isNew()) {
            $surveyResult->setSurvey($survey);
            $this->surveyResultRepository->add($surveyResult);
            // persist now to have a uid to log
            $this->persistenceManager->persistAll();
        }

        // log
        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('New survey started (surveyUid=%s, surveyResultUid=%s, extensionSuffix=%s).', $surveyResult->getUid(), $surveyResult->getUid(), $extensionSuffix));
        $this->forward('progress', null, null, array('surveyResult' => $surveyResult, 'extensionSuffix' => $extensionSuffix, 'tokenInput' => $tokenInput));
        //===
    }


    /**
     * initializeProgressAction
     * If question is multiple choice, we have to convert the array to a value-string
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeProgressAction()
    {
        if ($this->request->hasArgument('newQuestionResult')) {
            $newQuestionResult = $this->request->getArgument('newQuestionResult');

            if (is_array($newQuestionResult['answer'])) {
                $newQuestionResult['answer'] = implode(',', array_keys(array_filter($newQuestionResult['answer'])));
                $this->request->setArgument('newQuestionResult', $newQuestionResult);
            }
        }
    }


    /**
     * action progress
     * This action calls itself until all questions are answered
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $extensionSuffix
     * @param string $tokenInput
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $newQuestionResult
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function progressAction(SurveyResult $surveyResult, $extensionSuffix = null, $tokenInput = null, QuestionResult $newQuestionResult = null)
    {
        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        // Workaround: We have several problems if we're using @validate via PhpDocs.
        $validatorRequest = false;
        if ($newQuestionResult) {
            $validatorRequest = $this->questionResultValidator->isValid($newQuestionResult);
        }

        if (is_string($validatorRequest)) {
            // set error message to user
            $this->view->assign('errorMessage', $validatorRequest);

        } elseif ($validatorRequest === true) {

            // continue with question result
            if ($newQuestionResult) {
                // for secure - check if question is already answered (prevents browser-hopping anomalies)
                /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $oldQuestionResult */
                if ($oldQuestionResult = $this->questionResultRepository->findByQuestionAndSurveyResult($newQuestionResult->getQuestion(), $surveyResult)) {

                    //@toDo: Why not remove the old one instead of returning an error
                    $surveyResult->removeQuestionResult($oldQuestionResult);
                    $this->questionResultRepository->remove($oldQuestionResult);

                    /*
                     $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.alreadyAnswered', $this->extensionName),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                    );
                    $this->redirect('welcome', null, null, array('survey' => $surveyResult->getSurvey()));
                    //===
                    */
                }

                $surveyResult->addQuestionResult($newQuestionResult);
                SurveyProgressUtility::handleJumpAction($surveyResult, $newQuestionResult);
                $this->surveyResultRepository->update($surveyResult);
            }
        }

        // if all questions are answered, finalize it!
        if (count($surveyResult->getQuestionResult()) === count($surveyResult->getSurvey()->getQuestion())) {

            // final create and show endtext
            $this->forward('create', null, null, array('surveyResult' => $surveyResult, 'tokenInput' => $tokenInput));
            //===
        }

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('extensionSuffix', $extensionSuffix);
        $this->view->assign('tokenInput', $tokenInput);
    }


    /**
     * action result
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function newContactAction(SurveyResult $surveyResult, $tokenInput)
    {
        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('tokenInput', $tokenInput);
    }

    /**
     * action createContact
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param array $contactForm
     * @param string $tokenInput
     * @validate $contactForm \RKW\RkwSurvey\Validation\ContactFormValidator
     * @return void
     * @throws \Exception
     * @throws \RKW\RkwMailer\Service\MailException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function createContactAction(SurveyResult $surveyResult, $contactForm, $tokenInput = null)
    {

        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        // @toDo: Actually we have no frontendUser for creating a useful privacy-entry
        // \RKW\RkwRegistration\Tools\Privacy::addPrivacyData($this->request, $this->getFrontendUser(), $surveyResult, 'new survey contactForm');

        // send contactForm data to flexForm user
        $this->rkwMailService->sendContactForm($surveyResult->getSurvey()->getAdmin(), $surveyResult, $contactForm);

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.contactSuccessful', $this->extensionName),
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );

        // final create and show final text
        $this->forward('newContact', null, null, array('surveyResult' => $surveyResult, 'tokenInput' => $tokenInput));
        //===

    }


    /**
     * action create
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \Exception
     * @throws \RKW\RkwMailer\Service\MailException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     *      */
    public function createAction(SurveyResult $surveyResult, $tokenInput = null)
    {
        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        // check if result has not already been saved (browser reload)
        if (!$surveyResult->getFinished()) {

            $surveyResult->setFinished(true);
            $this->surveyResultRepository->add($surveyResult);

            // send mail(s) so admin(s)
            $this->rkwMailService->newSurveyAdmin($surveyResult->getSurvey()->getAdmin(), $surveyResult);

            // Signal for e.g. E-Mails
            $this->getSignalSlotDispatcher()->dispatch(__CLASS__, self::SIGNAL_AFTER_CREATING_SURVEY, array($surveyResult));
            $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('New survey completed (surveyUid=%s, surveyResultUid=%s).', $surveyResult->getSurvey()->getUid(), $surveyResult->getUid()));
        }
        $this->redirect('result', null, null, array('surveyResult' => $surveyResult, 'tokenInput' => $tokenInput));
        //===
    }


    /**
     * action result
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function resultAction(SurveyResult $surveyResult, $tokenInput)
    {
        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('tokenInput', $tokenInput);

        if ($surveyResult->getSurvey()->getType() === 1) {

            //  @todo: Das muss ausgelagert werden in die RkwGraphs! Allerdings muss auch ein Identifier übergeben werden, anhand dessen die RkwGraphs das Ergebnis render kann!
            //  instantiate with object manager -> see feecalculator
            $evaluator = GeneralUtility::makeInstance(Evaluator::class);
            $evaluator->setSurveyResult($surveyResult);

            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            // Inject necessary js libs
            $this->pageRenderer->addJsFooterLibrary(
                'ApexCharts', /* name */
                'https://cdn.jsdelivr.net/npm/apexcharts',
                'text/javascript', /* type */
                false, /* compress*/
                true, /* force on top */
                '', /* allwrap */
                true /* exlude from concatenation */
            );

            $chart = $evaluator->prepareChart();
            $this->pageRenderer->addJsFooterInlineCode('chartScript', $evaluator->renderChart($chart), true);

            $donuts = $evaluator->prepareDonuts();
            $this->pageRenderer->addJsFooterInlineCode('donutScript', $evaluator->renderDonuts($donuts), true);

            $bars = $evaluator->prepareBars();
            $this->pageRenderer->addJsFooterInlineCode('barScript', $evaluator->renderBars($bars), true);

            $this->view->assign('bars', $bars);
            $this->view->assign('donuts', $donuts);

        }

    }


    /**
     * checkInitialAccessRestriction
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $tokenInput
     * @return \RKW\RkwSurvey\Domain\Model\SurveyResult
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    protected function checkInitialAccessRestriction($survey, $tokenInput)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $surveyResult = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSurvey\\Domain\\Model\\SurveyResult');
        if ($survey->isAccessRestricted()) {

            // check given token string
            $tokenName = trim(filter_var($tokenInput, FILTER_SANITIZE_STRING));
            $token = null;
            if (
                (!$tokenName)
                || (!$token = $this->tokenRepository->findOneBySurveyAndName($survey, $tokenName))
            ) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenNotValid', $this->extensionName),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $survey));
                //===
            }

            /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
            $surveyResultFromDb = $this->surveyResultRepository->findByToken($token)->getFirst();

            // overwrite $surveyResult, if we have an existing one
            if ($surveyResultFromDb) {
                $surveyResult = $surveyResultFromDb;
            }

            // check various scenarios
            if ($surveyResult->isFinished()) {
                // 1. exist & finished!
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenExistAndFinished', $this->extensionName),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $survey));
                //===

            // exist & not finished: The $surveyResult is already set, let him run
            } else {

                // is new & token exists -> set token and go ahead!
                if ($surveyResult->_isNew()) {

                    if ($survey->getToken()->contains($token)) {

                        $surveyResult->setToken($token);

                    } else {

                        // something went wrong
                        $this->addFlashMessage(
                            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenSomethingWentWrong', $this->extensionName),
                            '',
                            \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                        );
                        $this->forward('welcome', null, null, array('survey' => $survey));
                        //===
                    }
                }
            }
        }

        return $surveyResult;
        //===
    }


    /**
     * checkAccessRestriction
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    protected function checkAccessRestriction($surveyResult, $tokenInput)
    {
        if ($surveyResult->getSurvey()->isAccessRestricted()) {
            $token = trim(filter_var($tokenInput, FILTER_SANITIZE_STRING));

            // for secure: check surveyResult-token (catch if getToken()->getName() does not exists. Avoid PHP-error)
            if (!$surveyResult->getToken()) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenSomethingWentWrong', $this->extensionName),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $surveyResult->getSurvey()));
                //===
            }

            // check token itself
            if (
                $token != $surveyResult->getToken()->getName()
                || !$this->tokenRepository->findOneBySurveyAndName($surveyResult->getSurvey(), $token)
            ) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenSomethingWentWrong', $this->extensionName),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $surveyResult->getSurvey()));
                //===
            }
        }
    }

    /**
     * Remove ErrorFlashMessage
     *
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::getErrorFlashMessage()
     */
    protected function getErrorFlashMessage()
    {
        return false;
        //===
    }

    /**
     * Returns SignalSlotDispatcher
     *
     * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected function getSignalSlotDispatcher()
    {

        if (!$this->signalSlotDispatcher) {
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $this->signalSlotDispatcher = $objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        }

        return $this->signalSlotDispatcher;
        //===
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        }

        return $this->logger;
        //===
    }

}
