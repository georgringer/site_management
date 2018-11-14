<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractStep implements SiteCreationInterface
{
//
    /** @var Configuration */
    protected $configuration;

    /** @var Response */
    protected $response;

    public function setup(Configuration $configuration, Response $response): void
    {
        $this->configuration = $configuration;
        $this->response = $response;
    }


    public function isValid(): bool
    {
        return true;
    }

    protected function updateRow(string $tableName, array $row, array $identifier): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName);
        $connection->update(
            $tableName,
            $row, $identifier
        );
    }

}