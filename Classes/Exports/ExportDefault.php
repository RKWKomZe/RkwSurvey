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
use RKW\RkwSurvey\Domain\Model\Survey;

/**
 * ExportDefault
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class ExportDefault extends \RKW\RkwSurvey\Exports\AbstractExport
{

    /**
     * @return array
     */
    protected function headings(): array
    {

        $headings = [
            'tx_rkwsurvey_controller_backend_csv.surveyUid',
            'tx_rkwsurvey_controller_backend_csv.surveyResultUid',
            'tx_rkwsurvey_controller_backend_csv.questionUid',
            'tx_rkwsurvey_controller_backend_csv.question',
            'tx_rkwsurvey_controller_backend_csv.answerOption',
            'tx_rkwsurvey_controller_backend_csv.answer'
        ];

        if ($this->hasQuestionContainers) {
            $headings = $this->insertAfter(
                $headings,
                'tx_rkwsurvey_controller_backend_csv.questionPositionInContainerUid',
                'tx_rkwsurvey_controller_backend_csv.questionUid',
            );
        }

        return $headings;

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

        $columnArray = $this->buildColumnArray();

        $csv->insertOne($columnArray);

        if ($this->hasQuestionContainers) {
            $questionContainerUids = $this->getQuestionContainerUids($survey);
        }

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
                if ($this->hasQuestionContainers) {
                    $dataArray[] = $this->getQuestionPosition($questionResult, $questionContainerUids);
                }
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
