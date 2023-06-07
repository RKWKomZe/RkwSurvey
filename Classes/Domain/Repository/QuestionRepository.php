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

/**
 * Class QuestionRepository
 *
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class QuestionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * findOneByGroupedByAndSurvey
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @return \RKW\RkwSurvey\Domain\Model\Question|null
     */
    public function findOneByGroupedByAndSurvey(Survey $survey):? Question
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('survey', $survey),
                $query->equals('group_by', true)
            )
        );

        return $query->execute()->getFirst();
    }


}
