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

use RKW\RkwEvents\Domain\Repository\EventRepository;
use RKW\RkwShop\Domain\Repository\ProductRepository;
use RKW\RkwSurvey\Domain\Repository\QuestionResultRepository;
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

abstract class Export
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
