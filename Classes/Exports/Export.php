<?php

namespace RKW\RkwSurvey\Exports;

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
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
use SplTempFileObject;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Export
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class Export
{

    /**
     * @var \RKW\RkwSurvey\Domain\Repository\QuestionResultRepository
     */
    protected ?QuestionResultRepository $questionResultRepository = null;


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
     * @param QuestionResultRepository $questionResultRepository
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param EventRepository   $eventRepository
     */
    public function __construct(
        QuestionResultRepository $questionResultRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        EventRepository $eventRepository
    ) {
        $this->questionResultRepository = $questionResultRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->eventRepository = $eventRepository;
    }


    /**
     * @param Survey $survey
     * @param string $starttime
     * @return void
     * @throws \League\Csv\InvalidArgument
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function download(\RKW\RkwSurvey\Domain\Model\Survey $survey, string $starttime = ''): void
    {
        $questionResultList = $this->questionResultRepository->findBySurveyOrderByQuestionAndType($survey, $starttime);

        $this->determineSurveyPurpose($questionResultList[0]);

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';');

        if ($this->surveyPurpose === 'outcome') {
            $csv = $this->buildOutcomeCsvArray($survey, $csv, $questionResultList);
        } else {
            $csv = $this->buildDefaultCsvArray($survey, $csv, $questionResultList);
        }

        $surveyName = SlugUtility::slugify($survey->getName()) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $surveyName . '"');

        $csv->output($surveyName);

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


    /**
     * @param Survey                                              $survey
     * @param \League\Csv\Writer                                  $csv
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
     * @return \League\Csv\Writer
     * @throws \League\Csv\CannotInsertRecord
     */
    protected function buildDefaultCsvArray(
        Survey $survey,
        Writer $csv,
        \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
    ): Writer
    {

        $columnHeaders = [
            'tx_rkwsurvey_controller_backend_csv.surveyUid',
            'tx_rkwsurvey_controller_backend_csv.surveyResultUid',
            'tx_rkwsurvey_controller_backend_csv.questionUid',
            'tx_rkwsurvey_controller_backend_csv.question',
            'tx_rkwsurvey_controller_backend_csv.answerOption',
            'tx_rkwsurvey_controller_backend_csv.answer'
        ];

        $columnArray = $this->buildColumnArray($columnHeaders);

        $csv->insertOne($columnArray);

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
     * @param Survey                                              $survey
     * @param \League\Csv\Writer                                  $csv
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
     * @return \League\Csv\Writer
     * @throws \League\Csv\CannotInsertRecord
     */
    protected function buildOutcomeCsvArray(
        Survey $survey,
        Writer $csv,
        \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $questionResultList
    ): Writer
    {

        $columnHeaders = [
            'tx_rkwsurvey_controller_backend_csv.surveyUid',
            'tx_rkwsurvey_controller_backend_csv.surveyResultUid',
            'tx_rkwsurvey_controller_backend_csv.surveyResultTags',
            'tx_rkwsurvey_controller_backend_csv.output.targetGroup',
            'tx_rkwsurvey_controller_backend_csv.output.type',
            'tx_rkwsurvey_controller_backend_csv.output.title',
            'tx_rkwsurvey_controller_backend_csv.questionPositionInContainerUid',
            'tx_rkwsurvey_controller_backend_csv.questionUid',
            'tx_rkwsurvey_controller_backend_csv.question',
            'tx_rkwsurvey_controller_backend_csv.answerOption',
            'tx_rkwsurvey_controller_backend_csv.answer'
        ];

        $columnArray = $this->buildColumnArray($columnHeaders);

        $csv->insertOne($columnArray);

        $questionContainerUids = array_map(static function ($question) {
            return $question->getUid();
        }, $survey->getQuestionContainer()->toArray());

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
                $dataArray = $this->resolveSurveyResultTags($questionResult, $dataArray);
                $dataArray = $this->getQuestionPosition($questionResult, $questionContainerUids, $dataArray);
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
    protected function resolveSurveyResultTags(
        \RKW\RkwSurvey\Domain\Model\QuestionResult $questionResult,
        array $dataArray
    ): array
    {
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
     * @param array $columnHeaders
     * @return array
     */
    protected function buildColumnArray(array $columnHeaders): array
    {
        $columnArray = [];
        foreach ($columnHeaders as $columnHeader) {
            $columnArray[] = LocalizationUtility::translate($columnHeader, 'rkw_survey');
        }

        return $columnArray;
    }
}
