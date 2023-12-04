<?php
namespace RKW\RkwSurvey\Controller;

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

use RKW\RkwSurvey\Domain\Model\Evaluator;
use RKW\RkwSurvey\Domain\Model\Survey;
use RKW\RkwSurvey\Domain\Model\SurveyResult;
use RKW\RkwSurvey\Domain\Model\QuestionResultContainer;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use RKW\RkwSurvey\Domain\Repository\TokenRepository;
use RKW\RkwSurvey\Service\RkwMailService;
use RKW\RkwSurvey\Utility\SurveyProgressUtility;
use RKW\RkwSurvey\Validation\ContactFormValidator;
use RKW\RkwSurvey\Validation\QuestionResultValidator;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * SurveyController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
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
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected SurveyRepository $surveyRepository;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected SurveyResultRepository $surveyResultRepository;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected QuestionResultRepository $questionResultRepository;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\TokenRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected TokenRepository $tokenRepository;


    /**
     * @var \RKW\RkwSurvey\Validation\QuestionResultValidator
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected QuestionResultValidator $questionResultValidator;


    /**
     * @var \RKW\RkwSurvey\Validation\ContactFormValidator
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ContactFormValidator $contactFormValidator;


    /**
     * @var \RKW\RkwSurvey\Service\RkwMailService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected RkwMailService $rkwMailService;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected PersistenceManager $persistenceManager;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer|null
     */
    protected ?PageRenderer $pageRenderer = null;


    /**
     * action welcome
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey|null $survey
     * @param string                                  $tokenInput
     * @param string                                  $tagsInput
     * @return void
     */
    public function welcomeAction(Survey $survey = null, string $tokenInput = '', string $tagsInput = ''): void
    {
        if (
            (GeneralUtility::_GP('token'))
            && (!$tokenInput)
        ) {
            $tokenInput = GeneralUtility::_GP('tx_rkwsurvey_survey')['token'];
        }

        if (
            (GeneralUtility::_GP('tx_rkwsurvey_survey')['tags'])
            && (!$tagsInput)
        ) {
            $tagsInput = GeneralUtility::_GP('tx_rkwsurvey_survey')['tags'];
        }

        $this->view->assignMultiple(
            array(
                'survey'     => $survey ?: $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($this->settings['selectedSurvey'])),
                'tokenInput' => $tokenInput,
                'tagsInput'  => $tagsInput
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
     * @param string $tagsInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function startAction(
        Survey $survey,
        string $extensionSuffix = '',
        string $tokenInput = '',
        string $tagsInput = ''
    ): void {

        // If access restricted, the initial assignment will be done here
        // Is also returning initial surveyResult-Object (existing or new)
        $surveyResult = $this->checkInitialAccessRestriction($survey, $tokenInput);

        // create new surveyResult (if not exists)
        if ($surveyResult->_isNew()) {
            $surveyResult->setSurvey($survey);
            $surveyResult->setTags($tagsInput);
            $this->surveyResultRepository->add($surveyResult);

            // persist now to have an uid to log
            $this->persistenceManager->persistAll();
        }

        // log
        $this->getLogger()->log(
            \TYPO3\CMS\Core\Log\LogLevel::INFO,
            sprintf(
                'New survey started (surveyUid=%s, surveyResultUid=%s, extensionSuffix=%s).',
                $surveyResult->getUid(),
                $surveyResult->getUid(),
                $extensionSuffix
            )
        );

        $this->forward(
            'progress',
            null,
            null,
            ['surveyResult' => $surveyResult, 'extensionSuffix' => $extensionSuffix, 'tokenInput' => $tokenInput]
        );
    }


    /**
     * initializeProgressAction
     * If question is multiple choice, we have to convert the array to a value-string
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeProgressAction(): void
    {

        if ($this->request->hasArgument('newQuestionResultContainer')) {
            $newQuestionResultContainer = $this->request->getArgument('newQuestionResultContainer');

            if (key_exists('questionResult', $newQuestionResultContainer)) {
                foreach ($newQuestionResultContainer['questionResult'] as $key => $newQuestionResult) {
                    if (is_array($newQuestionResult['answer'])) {
                        $newQuestionResultContainer['questionResult'][$key]['answer'] = implode(
                            ',',
                            array_keys(array_filter($newQuestionResult['answer']))
                        );
                    }
                }
                $this->request->setArgument('newQuestionResultContainer', $newQuestionResultContainer);
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
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResultContainer|null $newQuestionResultContainer
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function progressAction(
        SurveyResult $surveyResult,
        string $extensionSuffix = '',
        string $tokenInput = '',
        ?QuestionResultContainer $newQuestionResultContainer = null
    ): void {

        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        if ($newQuestionResultContainer instanceof QuestionResultContainer) {

            $formErrorDetected = false;
            $questionHasErrorArray = [];
            $questionResultToAddList = [];

            /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $newQuestionResult */
            foreach ($newQuestionResultContainer->getQuestionResult()->toArray() as $key => $newQuestionResult) {

                // Workaround: We have several problems if we're using @validate via PhpDocs.
                $validatorRequest = $this->questionResultValidator->isValid($newQuestionResult);

                if (is_string($validatorRequest)) {
                    // set error message to user
                    $this->view->assign('errorMessage', $validatorRequest);
                    $formErrorDetected = true;

                    // in fluid we identify the error-question through container iteration
                    $questionHasErrorArray[$key] = ' validation-error';

                } elseif ($validatorRequest === true) {

                    // continue with question result
                    if ($newQuestionResult) {

                        // for secure - check if question is already answered (prevents browser-hopping anomalies)
                        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $oldQuestionResult */
                        if ($oldQuestionResult = $this->questionResultRepository
                            ->findByQuestionAndSurveyResult($newQuestionResult->getQuestion(), $surveyResult)
                        ) {

                            // Remove the old one instead of returning an error
                            $surveyResult->removeQuestionResult($oldQuestionResult);
                            $this->questionResultRepository->remove($oldQuestionResult);
                        }

                        // add it only, if there comes no more validation error
                        $questionResultToAddList[] = $newQuestionResult;

                        if ($surveyResult->getSurvey()->getType() == 2) {

                            // @toDo: Add container jump
                            // @toDo: Makes a container jump sense? Actually the jump-function depends on a single question type

                        } else {
                            SurveyProgressUtility::handleJumpAction($surveyResult, $newQuestionResult);
                        }
                    }
                }
            }

            // only update the surveyResult if there is no error message. Other simply show the template again with error message
            if (!$formErrorDetected) {
                $this->surveyResultRepository->update($surveyResult);

                // if there comes to error around, not set the questionResults to the surveyResult
                foreach ($questionResultToAddList as $questionResultToAdd) {
                    $surveyResult->addQuestionResult($questionResultToAdd);
                }

            } else {
                // give back the given questionContainer to re-fill given formfields
                $this->view->assign('prevResultContainer', $newQuestionResultContainer);
                $this->view->assign('questionHasErrorArray', $questionHasErrorArray);
            }
        }

        // if all questions are answered, finalize it!
        if (count($surveyResult->getQuestionResult()) === $surveyResult->getSurvey()->getQuestionCountTotal()) {

            // final create and show endtext
            $this->forward(
                'create',
                null,
                null,
                [
                    'surveyResult' => $surveyResult,
                    'tokenInput' => $tokenInput
                ]
            );
        }

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('extensionSuffix', $extensionSuffix);
        $this->view->assign('tokenInput', $tokenInput);

        // workaround test ObjectStorage iteration issue
        $this->view->assign('surveyQuestionContainerArray', $surveyResult->getSurvey()->getQuestionContainer()->toArray());


    }


    /**
     * action result
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function newContactAction(SurveyResult $surveyResult, string $tokenInput): void
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
     * @TYPO3\CMS\Extbase\Annotation\Validate("RKW\RkwSurvey\Validation\ContactFormValidator", param="contactForm")
     * @TYPO3\CMS\Extbase\Annotation\Validate("Madj2k\FeRegister\Validation\Consent\PrivacyValidator", param="contactForm")
     * @throws \Exception
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     */
    public function createContactAction(SurveyResult $surveyResult, array $contactForm, string $tokenInput = ''): void
    {

        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        // @todo Actually we have no frontendUser for creating a useful privacy-entry
        // \Madj2k\FeRegister\DataProtection\ConsentHandler::add($this->request, $this->getFrontendUser(), $surveyResult, 'new survey contactForm');

        // send contactForm data to flexForm user
        $this->rkwMailService->sendContactForm($surveyResult->getSurvey()->getAdmin(), $surveyResult, $contactForm);

        $this->addFlashMessage(
            LocalizationUtility::translate('tx_rkwsurvey_controller_survey.contactSuccessful', $this->extensionName),
            '',
            AbstractMessage::OK
        );

        // final create and show final text
        $this->forward(
            'newContact',
            null,
            null,
            ['surveyResult' => $surveyResult, 'tokenInput' => $tokenInput]
        );
    }


    /**
     * action create
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \Exception
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     *      */
    public function createAction(SurveyResult $surveyResult, string $tokenInput = ''): void
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
            $this->getSignalSlotDispatcher()->dispatch(
                __CLASS__,
                self::SIGNAL_AFTER_CREATING_SURVEY,
                [$surveyResult]
            );

            $this->getLogger()->log(
                \TYPO3\CMS\Core\Log\LogLevel::INFO,
                sprintf(
                    'New survey completed (surveyUid=%s, surveyResultUid=%s).',
                    $surveyResult->getSurvey()->getUid(),
                    $surveyResult->getUid()
                )
            );
        }
        $this->redirect(
            'result',
            null,
            null,
            ['surveyResult' => $surveyResult, 'tokenInput' => $tokenInput]
        );
    }


    /**
     * action result
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function resultAction(SurveyResult $surveyResult, string $tokenInput): void
    {
        // check access restriction
        $this->checkAccessRestriction($surveyResult, $tokenInput);

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('tokenInput', $tokenInput);

        if ($surveyResult->getSurvey()->getType() === 1) {

            /**  @todo Das muss ausgelagert werden in die RkwGraphs!
             *  Allerdings muss auch ein Identifier übergeben werden, anhand dessen die RkwGraphs das Ergebnis render kann!
             *  instantiate with object manager -> see feecalculator
             */

            $evaluator = GeneralUtility::makeInstance(Evaluator::class, $surveyResult);
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            // Inject necessary js libs
            $this->pageRenderer->addJsFooterLibrary(
                'ApexCharts', /* name */
                'https://cdn.jsdelivr.net/npm/apexcharts',
                'text/javascript', /* type */
                false, /* compress*/
                true, /* force on top */
                '', /* allwrap */
                true /* exclude from concatenation */
            );

            $chart = $evaluator->prepareChart();
            $this->pageRenderer->addJsFooterInlineCode('chartScript', $evaluator->renderChart($chart), true);

            $donuts = $evaluator->prepareDonuts();
            $this->pageRenderer->addJsFooterInlineCode('donutScript', $evaluator->renderDonuts($donuts), true);

            if ($evaluator->containsGroupedByQuestion()) {
                $bars = $evaluator->prepareBars();
                $this->pageRenderer->addJsFooterInlineCode('barScript', $evaluator->renderBars($bars), true);
                $this->view->assign('bars', $bars);
            }

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
    protected function checkInitialAccessRestriction(Survey $survey, string $tokenInput): SurveyResult
    {
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $surveyResult = GeneralUtility::makeInstance(SurveyResult::class);
        if ($survey->isAccessRestricted()) {

            // check given token string
            $tokenName = trim(filter_var($tokenInput, FILTER_SANITIZE_STRING));
            $token = null;
            if (
                (!$tokenName)
                || (!$token = $this->tokenRepository->findOneBySurveyAndName($survey, $tokenName))
            ) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenNotValid', $this->extensionName),
                    '',
                    AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $survey));
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
                    LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenExistAndFinished', $this->extensionName),
                    '',
                    AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $survey));

            // exist & not finished: The $surveyResult is already set, let him run
            } else {

                // is new & token exists -> set token and go ahead!
                if ($surveyResult->_isNew()) {

                    if ($survey->getToken()->contains($token)) {
                        $surveyResult->setToken($token);

                    } else {

                        // something went wrong
                        $this->addFlashMessage(
                            LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenSomethingWentWrong', $this->extensionName),
                            '',
                            AbstractMessage::WARNING
                        );
                        $this->forward('welcome', null, null, array('survey' => $survey));
                    }
                }
            }
        }

        return $surveyResult;
    }


    /**
     * checkAccessRestriction
     *
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @param string $tokenInput
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    protected function checkAccessRestriction(SurveyResult $surveyResult, string $tokenInput): void
    {
        // avoid errors which occurs if a survey is hidden or deleted
        if (!$surveyResult->getSurvey() instanceof Survey) {
            $this->addFlashMessage(
                LocalizationUtility::translate('tx_rkwsurvey_controller_survey.surveyNoLongerActive', $this->extensionName),
                '',
                AbstractMessage::INFO
            );
            $this->forward('welcome');
        }

        if ($surveyResult->getSurvey()->isAccessRestricted()) {
            $token = trim(filter_var($tokenInput, FILTER_SANITIZE_STRING));

            // for secure:
            // a) check surveyResult-token (catch if getToken()->getName() does not exists. Avoid PHP-error)
            // b) check token itself
            if (
                !$surveyResult->getToken()
                || (
                    $token != $surveyResult->getToken()->getName()
                    || !$this->tokenRepository->findOneBySurveyAndName($surveyResult->getSurvey(), $token)
                )
            ) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('tx_rkwsurvey_controller_survey.tokenSomethingWentWrong', $this->extensionName),
                    '',
                    AbstractMessage::WARNING
                );
                $this->forward('welcome', null, null, array('survey' => $surveyResult->getSurvey()));
            }
        }
    }


    /**
     * Remove ErrorFlashMessage
     *
     * @return bool
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::getErrorFlashMessage()
     */
    protected function getErrorFlashMessage(): bool
    {
        return false;
    }


    /**
     * Returns SignalSlotDispatcher
     *
     * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected function getSignalSlotDispatcher(): Dispatcher
    {
        if (!$this->signalSlotDispatcher) {
            $this->signalSlotDispatcher = $this->objectManager->get(Dispatcher::class);
        }

        return $this->signalSlotDispatcher;
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {

        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}
