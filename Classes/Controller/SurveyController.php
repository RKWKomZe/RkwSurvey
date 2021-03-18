<?php

namespace RKW\RkwSurvey\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use \RKW\RkwSurvey\Domain\Model\Survey;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \RKW\RkwSurvey\Domain\Model\SurveyResult;
use \RKW\RkwSurvey\Domain\Model\QuestionResult;
use \RKW\RkwSurvey\Utility\SurveyProgressUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;

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
     * Expose the pageRenderer
     *
     * @var $pageRenderer
     */
    protected $pageRenderer;

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
//        $this->view->assign('chart', $this->prepareChart($surveyResult));
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

        $this->pageRenderer = GeneralUtility::makeInstance( PageRenderer::class );

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

        $chart = $this->prepareChart($surveyResult);
        $donuts = $this->prepareDonuts($surveyResult);

        $this->pageRenderer->addJsFooterInlineCode( 'chartScript', $this->renderChart($chart, $surveyResult), true );
        $this->pageRenderer->addJsFooterInlineCode( 'donutScript', $this->renderDonuts($donuts, $surveyResult), true );

        $this->view->assign('surveyResult', $surveyResult);
        $this->view->assign('tokenInput', $tokenInput);

        $this->view->assign('donuts', $donuts);

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

    /**
     * @param SurveyResult $surveyResult
     * @return array
     */
    protected function prepareChart(SurveyResult $surveyResult): array
    {

        //  get only questions marked as benchmark
        $benchmarkQuestions = $surveyResult->getSurvey()->getBenchmarkQuestions();

        $benchmarkValues = [];
        $questionShortNames = [];
        foreach ($benchmarkQuestions as $question) {
            $benchmarkValues[] = $question->getBenchmarkValue();
            $questionShortNames[] = $question->getShortName();
        }

        $individualValues = [];
        //  filter to results matching a benchmark question
        foreach ($surveyResult->getBenchmarkQuestionResults() as $result) {
            //  cast answer to int, if question is of marked as benchmark
            $individualValues[] = (int)$result->getAnswer();
        }

        //  mit 0 auffüllen, wenn bisher weniger Antworten als Labels Fragen zur Verfügung stehen
        if (($fill = count($benchmarkValues) - count($individualValues)) > 0) {
            for ($i = 1; $i === $fill; $i++) {
                $individualValues[] = 0;
            }
        }

        $chart = [
            'labels' => $questionShortNames,
            'values' => [
                'benchmark'  => $benchmarkValues,
                'individual' => $individualValues
            ]
        ];

        return $chart;
    }

    /**
     * @param SurveyResult $surveyResult
     * @return array
     */
    protected function prepareDonuts(SurveyResult $surveyResult): array // @todo: Make this dynamic somehow
    {

        $donuts = [];

        $surveyQuestions = $surveyResult->getSurvey()->getQuestion();

        $myFirstQuestion = $surveyResult->getQuestionResult()->toArray()[0];    //  @todo: How to identify grouping by as it does not have to be always the first question?

        $allQuestionResultsByQuestion = $this->questionResultRepository->findByQuestionAndAnswer($myFirstQuestion->getQuestion(), $myFirstQuestion->getAnswer());

        $surveyResultUids = [];

        foreach ($allQuestionResultsByQuestion as $questionResult) {
            $surveyResultUids[] = $questionResult->getSurveyResult()->getUid();
        }

        foreach ($surveyQuestions as $question) {

            //  use question only if it is a scale
            if ($question->getType() !== 3) {
                continue;
            }

            $slug = $this->slugify($question->getQuestion());

            $donuts[$slug] = [
                'question' => $question->getQuestion(),
            ];

            //  group the values
            $evaluation = [
                'my-region' => [
                    'low' => [],
                    'neutral' => [],
                    'high' => [],
                ],
                'all-regions' => [
                    'low' => [],
                    'neutral' => [],
                    'high' => [],
                ]
            ];

            $questionResults = $this->questionResultRepository->findByQuestionAndSurveyResultUids($question, $surveyResultUids);
            $donuts = $this->collectData($questionResults, $evaluation, $myFirstQuestion, $donuts, $slug, $key = 'my_region');

            //  Ostdeutschland = all regions
            $questionResults = $this->questionResultRepository->findByQuestion($question);
            $donuts = $this->collectData($questionResults, $evaluation, $myFirstQuestion, $donuts, $slug, $key = 'all_regions');

            //  Deutschland = GEM
            $donuts[$slug]['data']['benchmark']['region'] = 'Bundesweit (GEM)';

            //  get benchmark from question = GEM
            //  @todo: Need all three values per question (low, neutral, high) from GEM - must be put to question
            $donuts[$slug]['data']['benchmark']['evaluation']['series'] = [rand(0, 100), rand(0, 100), rand(0, 100)];
            $donuts[$slug]['data']['benchmark']['evaluation']['labels'] = ['low', 'neutral', 'high'];

        }

        return $donuts;
    }

    //  @todo: Fix to convert real umlauts
    protected function slugify($input, $word_delimiter='_') {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, '-'));
        $slug = preg_replace("/[\/_|+ -]+/", $word_delimiter, $slug);
        return $slug;
    }

    /**
     * @param array        $chart
     * @param SurveyResult $surveyResult
     * @return string
     */
    protected function renderChart(array $chart, SurveyResult $surveyResult): string
    {

        return '
            
            var options = {
                chart: {
                    type: \'radar\'
                },
                series: [
                    {
                        name: "Ihr Wert",
                        data: ' . json_encode($chart['values']['individual']) . ',
                    },
                    {
                        name: "GEM",
                        data: ' . json_encode($chart['values']['benchmark']) . ',
                    },
                    {
                        data: [0, 0, 11, 11],
                    },
                    {
                        data: [11, 0, 0, 11],
                    },
                    {
                        data: [11, 11, 0, 0],
                    },
                    {
                        data: [0, 11, 11, 0],
                    }
                ],
                labels: ' . json_encode($chart['labels']) . '
            }
            
            var chart = new ApexCharts(document.querySelector(\'#chart_' . $surveyResult->getUid() . '\'), options);

            chart.render(); 
                
        ';

    }

    /**
     * @param array        $charts
     * @param SurveyResult $surveyResult
     * @return string
     */
    protected function renderDonuts(array $charts, SurveyResult $surveyResult): string
    {

        $script = '';

        foreach ($charts as $chartIdentifier => $comparisons) {

            foreach ($comparisons['data'] as $comparisonIdentifier => $comparison) {

                $identifier = $chartIdentifier . '_' . $comparisonIdentifier;

                $script .= '
                    
                    var options_' . $identifier . ' = {
                        chart: {
                            type: \'donut\'
                        },
                        series: ' . json_encode($comparison['evaluation']['series']) . ',
                        labels: ' . json_encode($comparison['evaluation']['labels']) . ',
                        plotOptions: {
                            pie: {
                                customScale: 0.5
                            }
                        },
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: false,
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true
                                    }
                                }
                            }
                        }
                    }
                    
                    var chart_' . $identifier . ' = new ApexCharts(document.querySelector(\'#' . $identifier . '\'), options_' . $identifier . ');

                    chart_' . $identifier . '.render(); 
                    
                ';

            }

        }

        return $script;

    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults
     * @param array               $evaluation
     * @param                     $myFirstQuestion
     * @param array               $donuts
     * @param string              $slug
     * @param key                 $key
     * @return array              $donuts
     */
    protected function collectData(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $questionResults, array $evaluation, $myFirstQuestion, array $donuts, string $slug, string $key): array
    {
        foreach ($questionResults as $result) {

            if ((int)$result->getAnswer() < 5) {
                $evaluation[$key]['low'][] = $result;
            }

            if ((int)$result->getAnswer() === 5) {
                $evaluation[$key]['neutral'][] = $result;
            }

            if ((int)$result->getAnswer() > 5) {
                $evaluation[$key]['high'][] = $result;
            }

        }

        //  show results for each question
        //  meine Region, Ostdeutschland, Deutschland

        //  my-region
        $myFirstQuestionAnswerOptions = GeneralUtility::trimExplode(PHP_EOL, $myFirstQuestion->getQuestion()->getAnswerOption(), true);
        $donuts[$slug]['data'][$key]['region'] = $myFirstQuestionAnswerOptions[((int)$myFirstQuestion->getAnswer() - 1)];
        $donuts[$slug]['data'][$key]['region'] = 'Ihre Region';

        $donuts[$slug]['data'][$key]['evaluation']['low'] = (isset($evaluation[$key]['low'])) ? count($evaluation[$key]['low']) : 0;
        $donuts[$slug]['data'][$key]['evaluation']['neutral'] = (isset($evaluation[$key]['neutral'])) ? count($evaluation[$key]['neutral']) : 0;
        $donuts[$slug]['data'][$key]['evaluation']['high'] = (isset($evaluation[$key]['high'])) ? count($evaluation[$key]['high']) : 0;

        //  @todo: improve this whole aggregation process
        $donuts[$slug]['data'][$key]['evaluation']['series'] = [$donuts[$slug]['data'][$key]['evaluation']['low'], $donuts[$slug]['data'][$key]['evaluation']['neutral'], $donuts[$slug]['data'][$key]['evaluation']['high']];
        $donuts[$slug]['data'][$key]['evaluation']['labels'] = ['low', 'neutral', 'high'];

        unset($donuts[$slug]['data'][$key]['evaluation']['low']);
        unset($donuts[$slug]['data'][$key]['evaluation']['neutral']);
        unset($donuts[$slug]['data'][$key]['evaluation']['high']);

        return $donuts;
    }

}
