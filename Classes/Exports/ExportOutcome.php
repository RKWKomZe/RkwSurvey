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
use RKW\RkwSurvey\Domain\Model\Survey;
use SplTempFileObject;

/**
 * ExportOutcome
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class ExportOutcome extends \RKW\RkwSurvey\Exports\Export
{

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

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';');

        $csv = $this->buildCsvArray($survey, $csv, $questionResultList);

        $surveyName = SlugUtility::slugify($survey->getName()) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . $surveyName . '"');

        $csv->output($surveyName);

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


}
