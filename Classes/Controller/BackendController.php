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

use RKW\RkwSurvey\Domain\Model\Survey;
use RKW\RkwSurvey\Domain\Model\Token;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyRepository;
use RKW\RkwSurvey\Domain\Repository\SurveyResultRepository;
use RKW\RkwSurvey\Domain\Repository\TokenRepository;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * BackendController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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
     * @param int $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function showAction(int $surveyUid, int $starttime = 0)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);

        $this->view->assign('starttime', $starttime);
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
     * @param int $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function printAction(int $surveyUid, int $starttime = 0): void
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
     * @param int $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function csvAction(int $surveyUid, int $starttime = 0): void
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields($surveyUid);
        $questionResultList = $this->questionResultRepository->findBySurveyOrderByQuestionAndType($survey, $starttime);

        // create a name for the file
        $surveyName = preg_replace("/[^a-zA-Z0-9]/", "", $survey->getName());
        $surveyName = str_replace(' ', '', $surveyName);
        $surveyName = strtolower(trim($surveyName)) . '.csv';

        $csv = fopen('php://output', 'w');
        $separator = ';';

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$surveyName");
        header("Pragma: no-cache");

        $columArray = [];
        // column names
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyUid', $this->extensionName);
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyResultUid', $this->extensionName);
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyResultTags', $this->extensionName);
        if ($survey->getType() == 2) {
            $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.questionContainerUid', $this->extensionName);
        }
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.questionUid', $this->extensionName);
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.question', $this->extensionName);
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answerOption', $this->extensionName);
        $columArray[] = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answer', $this->extensionName);

        // Fill the CSV file with content
        fputcsv($csv, $columArray, $separator);

        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {

            try {

                if (!$questionResult->getSurveyResult()) {
                    continue;
                    //===
                }

                /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
                $question = $questionResult->getQuestion();

                $answerOption = '';
                if (!$question->getAnswerOption()) {
                    if ($question->getType() == 0 || $question->getType() == 4) {
                        $answerOption = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.freetext', $this->extensionName);
                    }
                    if ($question->getType() == 3) {
                        $answerOption = LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.scale', $this->extensionName,
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

                $dataArray = [];
                $dataArray[] = $survey->getUid();
                $dataArray[] = $questionResult->getSurveyResult()->getUid();
                $dataArray[] = $questionResult->getSurveyResult()->getTags();
                if ($survey->getType() == 2) {
                    $dataArray[] = $questionResult->getQuestion()->getQuestionContainer()->getUid();
                }
                $dataArray[] = $questionResult->getUid();
                $dataArray[] = $question->getQuestion();
                $dataArray[] = $answerOption;
                $dataArray[] = $questionResult->getAnswer();

                fputcsv($csv, $dataArray, $separator);

            } catch (\Exception $e) {
                continue;
            }
        }

        fclose($csv);
        exit;
    }


    /**
     * action tokenList
     * show token list (if exists)
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return void
     */
    public function tokenListAction(Survey $survey): void
    {

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
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param int $number
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function tokenCreateAction(Survey $survey, int $number): void
    {
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
        $this->forward('tokenList', null, null, ['survey' => $survey]);
    }


    /**
     * action tokenRemove
     * remove token list (if exists)
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function tokenRemoveAction(Survey $survey): void
    {

        $tokenList = $this->tokenRepository->findBySurvey($survey);
        /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
        foreach ($tokenList as $token) {
            $survey->removeToken($token);
        }

        $this->surveyRepository->update($survey);
        $this->forward('tokenList', null, null, array('survey' => $survey));
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
}
