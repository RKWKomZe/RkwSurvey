<?php

namespace RKW\RkwSurvey\Domain\Repository;

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

use RKW\RkwSurvey\Domain\Model\Question;
use RKW\RkwSurvey\Domain\Model\QuestionResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


/**
 * QuestionResults
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionResultRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * findByQuestionAndSurveyResult
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
     * @return \RKW\RkwSurvey\Domain\Model\QuestionResult|null
     */
    public function findByQuestionAndSurveyResult(
        \RKW\RkwSurvey\Domain\Model\Question $question,
        \RKW\RkwSurvey\Domain\Model\SurveyResult $surveyResult
    ):? QuestionResult {

        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('question', $question),
                $query->equals('surveyResult', $surveyResult)
            )
        );

        return $query->execute()->getFirst();
    }

    /**
     * findByQuestionAndSurveyResultUids
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param array $surveyResultUids
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByQuestionAndSurveyResultUids(
        \RKW\RkwSurvey\Domain\Model\Question $question,
        array $surveyResultUids
    ):? QueryResultInterface {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('question', $question),
                $query->in('survey_result', $surveyResultUids)
            )
        );

        return $query->execute();
    }


    /**
     * findByQuestionUidAndSurveyResultUids
     *
     * @param int $questionUid
     * @param array $surveyResultUids
     * @return \RKW\RkwSurvey\Domain\Model\QuestionResult|null
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByQuestionUidAndSurveyResultUids(
        int $questionUid,
        array $surveyResultUids
    ):? QuestionResult {

        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('question', $questionUid),
                $query->in('survey_result', $surveyResultUids)
            )
        );

        return $query->execute();
    }


    /**
     * findBySurveyOrderByQuestionAndType
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $startDate
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findBySurveyOrderByQuestionAndType(
        \RKW\RkwSurvey\Domain\Model\Survey $survey,
        string $startDate = ''
    ): QueryResultInterface {

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = array();

        //$constraints[] = $query->equals('question.survey', $survey);
        $constraints[] = $query->equals('surveyResult.survey', $survey);
        $constraints[] = $query->logicalAnd(
            $query->logicalNot(
                $query->equals('surveyResult.uid', null)
            ),
            $query->greaterThan('surveyResult', 0)
        // $query->equals('surveyResult.finished', 1)
        );

        // use given time, or just the survey's starttime
        if ($startDate) {
            $constraints[] = $query->greaterThanOrEqual('surveyResult.crdate', strtotime($startDate));
        } else {
            $constraints[] = $query->greaterThanOrEqual('surveyResult.crdate', $survey->getStarttime());
        }
        // always make a cut at survey's endtime!
        if ($survey->getEndtime()) {
            $constraints[] = $query->lessThanOrEqual('surveyResult.crdate', $survey->getEndtime());
        }

        // NOW: construct final query!
        $query->matching($query->logicalAnd($constraints));

        $query->setOrderings(
            array(
                'question'      => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
                'question.type' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            )
        );

        return $query->execute();
    }


    /**
     * findByQuestion
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @return \RKW\RkwSurvey\Domain\Model\QuestionResult|null
     */
    public function findByQuestion(Question $question):? QuestionResult
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('question', $question)
        );

        return $query->execute();
    }


    /**
     * findByQuestionUid
     *
     * @param int $questionUid
     * @return \RKW\RkwSurvey\Domain\Model\QuestionResult|null
     */
    public function findByQuestionUid(int $questionUid):? QuestionResult
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('question', $questionUid)
        );

        return $query->execute();

    }


    /**
     * findByQuestionAndAnswer
     *
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param mixed $answer
     * @return \RKW\RkwSurvey\Domain\Model\QuestionResult|null
     */
    public function findByQuestionAndAnswer(Question $question, $answer):? QuestionResult
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('question', $question),
                $query->equals('answer', $answer)
            )
        );

        return $query->execute();
    }

}
