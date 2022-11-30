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

/**
 * Surveys
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SurveyRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * findAllSorted
     *
     * @param integer $year
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllSorted($year = null)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(array('starttime', 'endtime'));

        if ($year) {
            $query->matching(
                $query->logicalAnd(
                    $query->greaterThanOrEqual('starttime', strtotime($year)),
                    $query->lessThanOrEqual('starttime', strtotime($year))
                )
            );
        }

        $query->setOrderings(
            array(
                'starttime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            )
        );

        return $query->execute();
        //====
    }


    /**
     * findByIdentifierIgnoreEnableFields
     *
     * @param int $surveyUid
     * @return \RKW\RkwSurvey\Domain\Model\Survey|null
     */
    public function findByIdentifierIgnoreEnableFields($surveyUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored(array('starttime', 'endtime'));

        $query->matching(
            $query->equals('uid', intval($surveyUid))
        );

        return $query->execute()->getFirst();
        //====
    }

}
