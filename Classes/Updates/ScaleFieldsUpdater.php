<?php
declare(strict_types=1);

namespace RKW\RkwSurvey\Updates;

/**
 * This file is part of the "RkwPdf2content" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Migrates "tx_rkwsurvey_domain_model_question.scale*" fields
 */
class ScaleFieldsUpdater extends AbstractUpdate
{

    const TABLE = 'tx_rkwsurvey_domain_model_question';

    /**
     * @var string
     */
    protected $title = 'Updates "' . self::TABLE . '" scale fields to use steps';

    /**
     * Checks whether updates are required.
     *
     * @param string $description The description for the update
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
            //===
        }
        return true;
        //===
    }

    /**
     * Performs the required update.
     *
     * @param array $dbQueries Queries done in this update
     * @param string $customMessage Custom message to be displayed after the update process finished
     * @return bool Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessage)
    {
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::TABLE);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder->select('uid')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'type',
                    $queryBuilder->createNamedParameter(3, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'scale_step',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->execute();

        while ($record = $statement->fetch()) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->update(self::TABLE)
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                    )
                )
                ->set('scale_from_points', 1)
                ->set('scale_step', 1);
            $databaseQueries[] = $queryBuilder->getSQL();
            $queryBuilder->execute();
        }

        $this->markWizardAsDone();
        return true;
        //===
    }
}