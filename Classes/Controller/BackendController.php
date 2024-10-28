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

use League\Csv\Writer;
use Madj2k\DrSeo\Utility\SlugUtility;
use RKW\RkwEvents\Domain\Repository\EventRepository;
use RKW\RkwShop\Domain\Repository\ProductRepository;
use RKW\RkwSurvey\Domain\Model\Survey;
use RKW\RkwSurvey\Domain\Model\Token;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use RKW\RkwSurvey\Domain\Repository\TokenRepository;
use SplTempFileObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * BackendController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyRepository
     */
    protected ?SurveyRepository $surveyRepository = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\SurveyResultRepository
     */
    protected ?SurveyResultRepository $surveyResultRepository = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     */
    protected ?QuestionResultRepository $questionResultRepository = null;


    /**
     * @var \RKW\RkwSurvey\Domain\Repository\TokenRepository
     */
    protected ?TokenRepository $tokenRepository = null;


    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
     */
    protected ?CategoryRepository $categoryRepository = null;


    /**
     * @var \RKW\RkwShop\Domain\Repository\ProductRepository
     */
    protected ?ProductRepository $productRepository = null;


    /**
     * @var \RKW\RkwEvents\Domain\Repository\EventRepository
     */
    protected ?EventRepository $eventRepository = null;


    /**
     * @var string
     */
    protected string $surveyPurpose = 'default';


    /**
     * @param SurveyRepository $surveyRepository
     * @param SurveyResultRepository $surveyResultRepository
     * @param QuestionResultRepository $questionResultRepository
     * @param TokenRepository $tokenRepository
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param EventRepository $eventRepository
     */
    public function __construct(
        SurveyRepository $surveyRepository,
        SurveyResultRepository $surveyResultRepository,
        QuestionResultRepository $questionResultRepository,
        TokenRepository $tokenRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        EventRepository $eventRepository
    ) {
        $this->surveyRepository = $surveyRepository;
        $this->surveyResultRepository = $surveyResultRepository;
        $this->questionResultRepository = $questionResultRepository;
        $this->tokenRepository = $tokenRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * initialize
     */
    public function initializeAction(): void
    {
        // By MF: There is no better way to implement a datepicker in a backend module
        // http://typo3.sascha-ende.de/docs/development/extensions-general/use-datepicker-in-own-backend-module/
        $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        // Hint: Following line expects following path: EXT:my_extension/Resources/Public/JavaScript/MyCustom.js
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/RkwSurvey/txrkwsurvey_backend');
    }


    /**
     * action list
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function listAction(): void
    {
        $this->view->assign('surveyList', $this->surveyRepository->findAllSorted());
    }


    /**
     * action show
     * because extbase makes some trouble if some survey has a starttime in future, is disabled or something, just give the uid
     *
     * @param int $surveyUid
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function showAction(int $surveyUid, string $starttime = '')
    {

        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        $this->view->assign('starttime', ($starttime ? strtotime($starttime) : $survey->getStarttime()));
        $this->view->assign('survey', $survey);

        // To get always a complete year filter
        $this->view->assign('surveyListTotal', $this->surveyRepository->findAllSorted());
        $this->view->assign('surveyList', $this->surveyRepository->findAllSorted());
        $this->view->assign('surveyResultList', $this->surveyResultRepository->findBySurvey($survey, $starttime));
        $this->view->assign('surveyResultListFinished', $this->surveyResultRepository->findBySurveyAndFinished($survey, 1, $starttime));
        $this->view->assign('surveyResultListUnfinished', $this->surveyResultRepository->findBySurveyAndFinished($survey, 0, $starttime));
        $this->view->assign('questionResultList', $this->questionResultRepository->findBySurveyOrderByQuestionAndType($survey, $starttime));

    }


    /**
     * action print
     * because extbase makes some trouble if some survey has a starttime in future, ist disabled or something, just give the uid
     *
     * @param int $surveyUid
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function printAction(int $surveyUid, string $starttime = ''): void
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        $this->view->assign('survey', $survey);
        $this->view->assign('surveyResultList', $this->surveyResultRepository->findBySurvey($survey, $starttime));
        $this->view->assign('surveyResultListFinished', $this->surveyResultRepository->findBySurveyAndFinished($survey, 1, $starttime));
        $this->view->assign('surveyResultListUnfinished', $this->surveyResultRepository->findBySurveyAndFinished($survey, 0, $starttime));
        $this->view->assign('questionResultList', $this->questionResultRepository->findBySurveyOrderByQuestionAndType($survey, $starttime));
    }


    /**
     * action csv
     * because extbase makes some trouble if some survey has a starttime in future, ist disabled or something, just give the uid
     *
     * @param int $surveyUid
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function csvAction(int $surveyUid, string $starttime = ''): void
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);
        $questionResultList = $this->questionResultRepository->findBySurveyOrderByQuestionAndType($survey, $starttime);

        $this->determineSurveyPurpose($questionResultList[0]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';');

        $csv = $this->buildCsvArray($survey, $csv, $questionResultList);

        $surveyName = SlugUtility::slugify($survey->getName()) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $surveyName . '"');

        $csv->output($surveyName);
        die;

    }


    /**
     * action tokenList
     * show token list (if exists)
     *
     * @param int $surveyUid
     * @return void
     */
    public function tokenListAction(int $surveyUid): void
    {
        /** @var Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        // get all results of survey
        $surveyResultList = $this->surveyResultRepository->findBySurveyWithToken($survey);


        // build list of unused tokens
        $usedTokens = [];
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        foreach ($surveyResultList as $surveyResult) {
            $usedTokens[] = $surveyResult->getToken();
        }
        $unusedTokens = array_diff($survey->getToken()->toArray(), $usedTokens);

        $this->view->assign('survey', $survey);
        $this->view->assign('surveyResultList', $surveyResultList);
        $this->view->assign('unusedTokens', $unusedTokens);
    }


    /**
     * action tokenCreate
     * creates a given number of token for given survey
     * (create completely new OR add if already exists)
     *
     * @param int $surveyUid
     * @param int $number
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function tokenCreateAction(int $surveyUid, int $number): void
    {
        /** @var Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        $tokenCountBefore = $survey->getToken()->count();
        do {

            $characters = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
            $newTokenName = substr(str_shuffle($characters), 0, 10);
            if (!$this->tokenRepository->findOneBySurveyAndName($survey, $newTokenName)) {

                /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
                $token = GeneralUtility::makeInstance(Token::class);
                $token->setName($newTokenName);
                $token->setCruserId(intval($GLOBALS['BE_USER']->user['uid']));
                $survey->addToken($token);
            }

            // run until the number of existing token are equal to original token count + new number of tokens
        } while (($tokenCountBefore + $number) > $survey->getToken()->count());

        $this->surveyRepository->update($survey);
        $this->forward('tokenList', null, null, ['surveyUid' => $survey->getUid()]);
    }


    /**
     * action tokenRemove
     * remove token list (if exists)
     *
     * @param int $surveyUid
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function tokenRemoveAction(int $surveyUid): void
    {
        /** @var Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        $tokenList = $this->tokenRepository->findBySurvey($survey);
        /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
        foreach ($tokenList as $token) {
            $survey->removeToken($token);
        }

        $this->surveyRepository->update($survey);
        $this->forward('tokenList', null, null, array('surveyUid' => $survey->getUid()));
    }


    /**
     * action tokenCsv
     * because extbase makes some trouble if some survey has a starttime in future, is disabled or something, just give the uid
     *
     * @param int $surveyUid
     * @return void
     */
    public function tokenCsvAction(int $surveyUid)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        // get all results of survey
        $surveyResultList = $this->surveyResultRepository->findBySurveyWithToken($surveyUid);

        // build list of unused tokens
        /** @var \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult */
        $usedTokens = [];
        foreach ($surveyResultList as $surveyResult) {
            $usedTokens[] = $surveyResult->getToken();
        }
        $unusedTokens = array_diff($survey->getToken()->toArray(), $usedTokens);

        // create a name for the file
        $surveyName = preg_replace("/[^a-zA-Z0-9_]/", '', date("Ymd") . '_token_' . $survey->getName());
        $surveyName = str_replace(' ', '', $surveyName);
        $surveyName = strtolower(trim($surveyName)) . '.csv';

        $csv = fopen('php://output', 'w');

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$surveyName");
        header("Pragma: no-cache");

        // Fill the CSV file with content
        fputcsv($csv, array('Token'));

        /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
        foreach ($unusedTokens as $token) {
            try {
                fputcsv($csv, array(trim($token->getName())));
            } catch (\Exception $e) {
                continue;
                //===
            }
        }

        fclose($csv);
        exit;
    }


    /**
     * @param Survey                                              $survey
     * @param \League\Csv\Writer                                  $csv
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
     * @return \League\Csv\Writer
     * @throws \League\Csv\CannotInsertRecord
     */
    protected function buildCsvArray(
        Survey $survey,
        Writer $csv,
        \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
    ): Writer
    {

        if ($this->surveyPurpose === 'outcome') {
            $questionContainerUids = array_map(static function ($question) {
                return $question->getUid();
            }, $survey->getQuestionContainer()->toArray());
        }

        $columArray = [];
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyUid', 'rkw_survey');
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyResultUid', 'rkw_survey');
        if ($this->surveyPurpose === 'outcome') {
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyResultTags', 'rkw_survey');
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.output.targetGroup', 'rkw_survey');
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.output.type', 'rkw_survey');
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.output.title', 'rkw_survey');
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.questionPositionInContainerUid', 'rkw_survey');
        }
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.questionUid', 'rkw_survey');
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.question', 'rkw_survey');
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answerOption', 'rkw_survey');
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answer', 'rkw_survey');

        $csv->insertOne($columArray);

        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {

            try {

                if (!$questionResult->getSurveyResult()) {
                    continue;
                }

                /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
                $question = $questionResult->getQuestion();

                $dataArray = [];
                $dataArray[] = $survey->getUid();
                $dataArray[] = $questionResult->getSurveyResult()->getUid();
                if ($this->surveyPurpose === 'outcome') {
                    $dataArray = $this->resolveSurveyResultTags($questionResult, $dataArray);
                    $dataArray = $this->getQuestionPosition($questionResult, $questionContainerUids, $dataArray);
                }
                $dataArray[] = $question->getUid();
                $dataArray[] = $question->getQuestion();
                $dataArray[] = $this->getAnswerOption($question);
                $dataArray[] = $questionResult->getAnswer();

                $csv->insertOne($dataArray);

            } catch (\Exception $e) {
                continue;
            }
        }

        return $csv;
    }

    /**
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return string
     */
    protected function getAnswerOption(\RKW\RkwSurvey\Domain\Model\Question $question): string
    {
        $answerOption = '';

        if (!$question->getAnswerOption()) {
            if ($question->getType() === 0 || $question->getType() === 4) {
                $answerOption = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.freetext', 'rkw_survey');
            }
            if ($question->getType() === 3) {
                $answerOption = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.scale', 'rkw_survey',
                    [
                        $question->getScaleFromPoints(),
                        $question->getScaleToPoints(),
                        $question->getScaleStep()
                    ]
                );
            }
        } else {
            $answerOption = $question->getAnswerOption();
        }

        return $answerOption;
    }

    /**
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult
     * @param array                                      $dataArray
     * @return array
     */
    protected function resolveSurveyResultTags(\RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult, array $dataArray): array
    {
//  @todo: evtl. separate Export-Klassen je nach Output bzw. Surveyart (siehe Laravel?)
        $dataArray[] = $questionResult->getSurveyResult()->getTags();
        $surveyResultTags = explode(',', $questionResult->getSurveyResult()->getTags());

        /** @var \TYPO3\CMS\Extbase\Domain\Model\Category $category */
        $category = $this->categoryRepository->findByUid($surveyResultTags[0]);
        $dataArray[] = $category->getTitle();

        $dataArray[] = $surveyResultTags[1];

        if ($surveyResultTags[1] === 'Product') {
            /** @var \RKW\RkwShop\Domain\Model\Product $product */
            $product = $this->productRepository->findByUid($surveyResultTags[2]);
            $dataArray[] = $product->getTitle();
        } else {
            /** @var \RKW\RkwEvents\Domain\Model\Event $event */
            $event = $this->eventRepository->findByUid($surveyResultTags[2]);
            $dataArray[] = $event->getTitle();
        }

        return $dataArray;
    }

    /**
     * @param \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult
     * @param array                                      $questionContainerUids
     * @param array                                      $dataArray
     * @return array
     */
    protected function getQuestionPosition(\RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult, array $questionContainerUids, array $dataArray): array
    {
        $indexQuestionContainer = array_search($questionResult->getQuestion()->getQuestionContainer()->getUid(), $questionContainerUids, true);
        $indexQuestionContainerPos = ($indexQuestionContainer !== false) ? $indexQuestionContainer + 1 : '';

        $questionUids = array_map(function ($question) {
            return $question->getUid();
        }, $questionResult->getQuestion()->getQuestionContainer()->getQuestion()->toArray());
        $indexQuestion = array_search($questionResult->getQuestion()->getUid(), $questionUids, true);
        $indexQuestionPos = ($indexQuestion !== false) ? $indexQuestion + 1 : '';

        $dataArray[] = $indexQuestionPos . '.' . $indexQuestionContainerPos;

        return $dataArray;
    }

    /**
     * @param $questionResultList
     * @return void
     */
    protected function determineSurveyPurpose($questionResultList): void
    {
        if ($questionResultList->getSurveyResult()->getTags() !== '') {
            $this->surveyPurpose = 'outcome';
        }
    }
}
