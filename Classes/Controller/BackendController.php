<?php

namespace RKW\RkwSurvey\Controller;

use \RKW\RkwSurvey\Domain\Model\Survey;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
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
 * BackendController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Rkw Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
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
     * initialize
     */
    public function initializeAction()
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
    public function listAction()
    {
        $this->view->assign('surveyList', $this->surveyRepository->findAllSorted());
    }


    /**
     * action show
     * because extbase makes some trouble if some survey has a starttime in future, is disabled or something, just give the uid
     *
     * @param int $survey
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function showAction($survey, $starttime = null)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($survey));

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
     * @param int $survey
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function printAction($survey, $starttime = null)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($survey));

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
     * @param int $survey
     * @param string $starttime
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function csvAction($survey, $starttime = null)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($survey));
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

        // column names
        $surveyUidTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyUid', $this->extensionName);
        $surveyResultUidTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.surveyResultUid', $this->extensionName);
        $QuestionResultUidTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.questionUid', $this->extensionName);
        $questionTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.question', $this->extensionName);
        $answerOptionTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answerOption', $this->extensionName);
        $answerTranslation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.answer', $this->extensionName);

        // Fill the CSV file with content
        fputcsv($csv, array($surveyUidTranslation, $surveyResultUidTranslation, $QuestionResultUidTranslation, $questionTranslation, $answerOptionTranslation, $answerTranslation), $separator);

        /** @var \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult */
        foreach ($questionResultList as $questionResult) {

            try {

                if (!$questionResult->getSurveyResult()) {
                    continue;
                    //===
                }

                /** @var \RKW\RkwSurvey\Domain\Model\Question $question */
                $question = $questionResult->getQuestion();
                $surveyUid = $survey->getUid();
                $surveyResultUid = $questionResult->getSurveyResult()->getUid();
                $questionResultUid = $questionResult->getUid();

                $answerOption = '';
                if (!$question->getAnswerOption()) {
                    if ($question->getType() == 0 || $question->getType() == 4) {
                        $answerOption = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.freetext', $this->extensionName);
                    }
                    if ($question->getType() == 3) {
                        $answerOption = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_rkwsurvey_controller_backend_csv.scale', $this->extensionName, [
                            $question->getScaleFromPoints(),
                            $question->getScaleToPoints(),
                            $question->getScaleStep()
                        ]);
                    }
                } else {
                    $answerOption = $question->getAnswerOption();
                }

                fputcsv($csv, array($surveyUid, $surveyResultUid, $questionResultUid, $question->getQuestion(), $answerOption, $questionResult->getAnswer()), $separator);

            } catch (\Exception $e) {
                continue;
                //===
            }
        }

        fclose($csv);
        exit;
        //===
    }


    /**
     * action tokenList
     * show token list (if exists)
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return void
     */
    public function tokenListAction($survey)
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
    public function tokenCreateAction($survey, $number)
    {
        $tokenCountBefore = $survey->getToken()->count();
        do {

            $characters = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
            $newTokenName = substr(str_shuffle($characters), 0, 10);
            if (!$this->tokenRepository->findOneBySurveyAndName($survey, $newTokenName)) {

                /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
                $token = GeneralUtility::makeInstance('RKW\\RkwSurvey\\Domain\\Model\\Token');
                $token->setName($newTokenName);
                $token->setCruserId(intval($GLOBALS['BE_USER']->user['uid']));
                $survey->addToken($token);
            }

            // run until the number of existing token are equal to original token count + new number of tokens
        } while (($tokenCountBefore + $number) > $survey->getToken()->count());

        $this->surveyRepository->update($survey);
        $this->forward('tokenList', null, null, array('survey' => $survey));
        //===
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
    public function tokenRemoveAction($survey)
    {

        $tokenList = $this->tokenRepository->findBySurvey($survey);
        /** @var \RKW\RkwSurvey\Domain\Model\Token $token */
        foreach ($tokenList as $token) {
            $survey->removeToken($token);
        }

        $this->surveyRepository->update($survey);
        $this->forward('tokenList', null, null, array('survey' => $survey));
        //===
    }


    /**
     * action tokenCsv
     * because extbase makes some trouble if some survey has a starttime in future, ist disabled or something, just give the uid
     *
     * @param int $survey
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function tokenCsvAction($survey)
    {
        /** @var \RKW\RkwSurvey\Domain\Model\Survey $survey */
        $survey = $this->surveyRepository->findByIdentifierIgnoreEnableFields(intval($survey));

        // get all results of survey
        $surveyResultList = $this->surveyResultRepository->findBySurveyWithToken(intval($survey));

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
        //===
    }
}
