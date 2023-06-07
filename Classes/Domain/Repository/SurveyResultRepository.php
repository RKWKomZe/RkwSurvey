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
use RKW\RkwSurvey\Domain\Model\Survey;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * SurveyResults
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyResultRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * findBySurveyAndFinished
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $startDate
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findBySurvey(Survey $survey, string $startDate = '1970-01-01'): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = array();

        $constraints[] = $query->equals('survey', $survey);

        // use given time, or just the survey's starttime
        if ($startDate) {
            $constraints[] = $query->greaterThan('crdate', strtotime($startDate));
        } else {
            $constraints[] = $query->greaterThanOrEqual('crdate', $survey->getStarttime());
        }

        // always make a cut at survey's endtime!
        if ($survey->getEndtime()) {
            $constraints[] = $query->lessThanOrEqual('crdate', $survey->getEndtime());
        }

        // NOW: construct final query!
        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }


    /**
     * findBySurveyAndFinished
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param int $finished
     * @param string $startDate
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findBySurveyAndFinished(
        Survey $survey,
        int $finished = 1,
        string $startDate = '1970-01-01 00:00'
    ): QueryResultInterface{

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = array();

        $constraints[] = $query->equals('survey', $survey);
        $constraints[] = $query->equals('finished', $finished);

        // use given time, or just the survey's starttime
        if ($startDate) {
            $constraints[] = $query->greaterThanOrEqual('crdate', strtotime($startDate));
        } else {
            $constraints[] = $query->greaterThanOrEqual('crdate', $survey->getStarttime());
        }
        // always make a cut at survey's endtime!
        if ($survey->getEndtime()) {
            $constraints[] = $query->lessThanOrEqual('crdate', $survey->getEndtime());
        }

        // NOW: construct final query!
        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }


    /**
     * findBySurveyAndQuestionAndAnswerAndFinished
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param \RKW\RkwSurvey\Domain\Model\Question $question
     * @param mixed $answer
     * @param int $finished
     * @param string $startDate
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findBySurveyAndQuestionAndAnswerAndFinished(
        Survey $survey,
        Question $question,
        $answer,
        int $finished = 1,
        string $startDate = '1970-01-01'
    ): QueryResultInterface {

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = array();

        $constraints[] = $query->equals('survey', $survey);
        $constraints[] = $query->equals('finished', $finished);
        $constraints[] = $query->equals('questionResult.question', $question);
        $constraints[] = $query->equals('questionResult.answer', $answer);

        // use given time, or just the survey's starttime
        if ($startDate) {
            $constraints[] = $query->greaterThanOrEqual('crdate', strtotime($startDate));
        } else {
            $constraints[] = $query->greaterThanOrEqual('crdate', $survey->getStarttime());
        }
        // always make a cut at survey's endtime!
        if ($survey->getEndtime()) {
            $constraints[] = $query->lessThanOrEqual('crdate', $survey->getEndtime());
        }

        // NOW: construct final query!
        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }


    /**
     * findBySurveyWithToken
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey|int $survey
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findBySurveyWithToken($survey): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->logicalAnd(
                $query->equals('survey', $survey),
                $query->logicalNot(
                    $query->equals('token', 0)
                )
            )
        );

        return $query->execute();
    }


    /**
     * findBySurveyAndFinishedWithToken
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param int $finished
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     */
    public function findBySurveyAndFinishedWithToken(Survey $survey, int $finished = 1): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->logicalAnd(
                $query->equals('survey', $survey),
                $query->equals('finished', $finished),
                $query->logicalNot(
                    $query->equals('token', 0)
                )
            )
        );

        return $query->execute();
    }
}
