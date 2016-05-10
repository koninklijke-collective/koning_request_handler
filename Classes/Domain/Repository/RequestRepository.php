<?php
namespace Keizer\KoningRequestHandler\Domain\Repository;

use Keizer\KoningRequestHandler\Domain\Model\Request;

/**
 * Repository: Requests
 *
 * @package Keizer\KoningRequestHandler\Domain\Repository
 */
class RequestRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Get all locked requests
     *
     * @param string $uniqueId
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllLocked($uniqueId)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('running', $uniqueId));
        return $query->execute();
    }

    /**
     * Lock pending requests
     *
     * @param string $uniqueId
     * @return void
     */
    public function lockAll($uniqueId)
    {
        // @todo; deprecate this when DBAL is implemented
        $this->getDatabaseConnection()->exec_UPDATEquery(
            Request::TABLE,
            'running = ""',
            array('running' => $uniqueId)
        );
    }

    /**
     * Unlock records
     *
     * @param string $uniqueId
     * @return void
     */
    public function unlockAll($uniqueId)
    {
        // @todo; deprecate this when DBAL is implemented
        $this->getDatabaseConnection()->exec_UPDATEquery(
            Request::TABLE,
            'running = "' . $uniqueId . '"',
            array('running' => '')
        );
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function existsByIdentifier($identifier)
    {
        return (bool) $this->getDatabaseConnection()->exec_SELECTcountRows(
            'uid',
            Request::TABLE,
            'identifier = "' . $identifier . '"'
        );
    }

    /**
     * @param string $identifier
     * @return void
     */
    public function removeByIdentifier($identifier)
    {
        $query = $this->createQuery();
        $object = $query->matching($query->equals('identifier', $identifier))
            ->execute()->getFirst();

        if ($object !== null) {
            /** @var Request $object */
            if ($object->getPersistent() === false) {
                $this->remove($object);
            }
        }
    }

    /**
     * @todo; deprecate this when DBAL is implemented
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}