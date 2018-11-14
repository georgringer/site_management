<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ReplaceRelations extends AbstractStep
{

    static private $stepConfiguration = [
        'be_users' => [
            'db_mountpoints',
            'usergroup',
            'file_mountpoints'
        ],
        'be_groups' => [
            'subgroup',
            'db_mountpoints'
        ]
    ];

    public function getTitle(): string
    {
        return 'Replace relations';
    }

    public function handle(array $stepConfiguration = []): void
    {
        foreach (self::$stepConfiguration as $tableName => $fields) {
            $rows = $this->getRows($tableName, $this->response->getTargetRootPageId());

            foreach ($rows as $row) {
                $this->handleSingleRow($fields, $tableName, $row);
            }
        }
    }

    protected function handleSingleRow(array $fieldList, string $tableName, array $row)
    {
        $originalRow = $row;
        foreach ($fieldList as $field) {
            switch ($field) {
                case 'db_mountpoints':
                    $row[$field] = $this->replaceDbMountPoint($row[$field]);
                    break;
                case 'file_mountpoints':
                    $row[$field] = $this->getUpdatedIdList('sys_filemounts', $row[$field], $this->response->getTargetRootPageId());
                    break;
                case 'usergroup':
                case 'subgroup':
                    $row[$field] = $this->getUpdatedIdList('be_groups', $row[$field], $this->response->getTargetRootPageId());
                    break;

            }
        }
        if ($originalRow !== $row) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
            $connection->update(
                $tableName,
                $row,
                ['uid' => $originalRow['uid']]
            );
        }
    }

    protected function getUpdatedIdList(string $tableName, string $currentValue, int $targetRootPageId)
    {

    }

    protected function replaceDbMountPoint(string $currentValue): string
    {
        return $this->replaceValueInList($currentValue, $this->configuration->getSourceRootPageId(), $this->response->getTargetRootPageId());
    }

    protected function replaceValueInList(string $list, int $search, int $replace): string
    {
        if (empty($list)) {
            return $list;
        }
        $list = GeneralUtility::intExplode(',', $list, true);
        foreach ($list as $key => $value) {
            if ($value === $search) {
                $list[$key] = $replace;
            }
        }
        return implode(',', $list);
    }

    protected function getRows(string $tableName, int $targetId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()
            ->removeByType(HiddenRestriction::class);
        $rows = $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->in(
                    'tx_site_management_site',
                    $queryBuilder->createNamedParameter($targetId, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchAll();

        if (!$rows) {
            return [];
        }

        return $rows;
    }


}