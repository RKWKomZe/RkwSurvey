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
 * ExportDefault
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class ExportDefault extends \RKW\RkwSurvey\Exports\Export
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


}
