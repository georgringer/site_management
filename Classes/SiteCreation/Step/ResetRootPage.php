<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Utility\VariableReplacer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResetRootPage extends AbstractStep implements SiteCreationInterface
{
    public function getTitle(): string
    {
        return 'Reset root page';
    }

    public function handle(array $stepConfiguration = []): void
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages');

        $originalRow = BackendUtility::getRecord('pages', $this->response->getTargetRootPageId());
        $currentPageTitle = trim(str_replace('(copy 1)', '', $originalRow['title']));

        $connection->update(
            'pages',
            [
                'title' => VariableReplacer::replace($currentPageTitle, $this->configuration),
                'rowDescription' => '',
                'tx_site_management_demo_tree' => 0,
                'hidden' => 1,
            ],
            [
                'uid' => $this->response->getTargetRootPageId()
            ]
        );
    }


}