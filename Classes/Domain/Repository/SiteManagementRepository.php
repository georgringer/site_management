<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Domain\Repository;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteManagementRepository
{

    public function demoSiteExists(int $pageId): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $row = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $pageId),
                $queryBuilder->expr()->eq('is_siteroot', 1),
                $queryBuilder->expr()->eq('tx_site_management_demo_tree', 1)
            )
            ->execute()
            ->fetch();

        return \is_array($row) && !empty($row);
    }

    public function getDemoSiteRows(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $statement = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('is_siteroot', 1),
                $queryBuilder->expr()->eq('tx_site_management_demo_tree', 1)
            )
            ->orderBy('pid')
            ->addOrderBy('sorting')
            ->execute();

        $pages = [];
        while ($row = $statement->fetch()) {
            $row['rootline'] = BackendUtility::BEgetRootLine((int)$row['uid']);
            array_pop($row['rootline']);
            $row['rootline'] = array_reverse($row['rootline']);

            $pages[(int)$row['uid']] = $row;
        }
        return $pages;
    }

    public function getUserGroupsOfDemoSite(int $pageId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $rows = $queryBuilder
            ->select('*')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->eq('tx_site_management_site', $pageId)
            )
            ->addOrderBy('sorting')
            ->execute()
            ->fetchAll();

        $rows = $this->setUidAsKey($rows);

        return $rows;
    }

    public function getUsersOfDemoSite(int $pageId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');
        $rows = $queryBuilder
            ->select('*')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('tx_site_management_site', $pageId)
            )
            ->addOrderBy('username')
            ->execute()
            ->fetchAll();

        $rows = $this->setUidAsKey($rows);

        return $rows;
    }

    protected function setUidAsKey(array $rows): array
    {
        $new = [];
        foreach ($rows as $row) {
            $new[$row['uid']] = $row;
        }
        return $new;
    }
}