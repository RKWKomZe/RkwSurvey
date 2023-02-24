<?php

namespace RKW\RkwSurvey\Domain\Repository;

use \RKW\RkwSurvey\Domain\Model\Survey;

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
 * Tokens
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwSurvey
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TokenRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * findOneBySurveyAndName
     *
     * @param \RKW\RkwSurvey\Domain\Model\Survey $survey
     * @param string $name
     * @return \RKW\RkwSurvey\Domain\Model\Token|null
     */
    public function findOneBySurveyAndName(Survey $survey, $name)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $query->matching(
            $query->logicalAnd(
                $query->equals('survey', $survey),
                $query->equals('name', $name)
            )
        );

        return $query->execute()->getFirst();
        //====
    }

}
