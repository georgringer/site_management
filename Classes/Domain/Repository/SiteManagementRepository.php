<?php

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

        return is_array($row) && !empty($row);
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
            $i = 0;
            foreach ($row['rootline'] as &$record) {
                $record['margin'] = $i++ * 20;
            }
            $pages[(int)$row['uid']] = $row;
        }
        return $pages;
    }
}