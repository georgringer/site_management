<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use GeorgRinger\SiteManagement\Utility\VariableReplacer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateUserGroup extends AbstractStep implements SiteCreationInterface
{

    protected const TABLE = 'be_groups';

    /** @var DuplicateCommand */
    protected $duplicateService;

    public function __construct()
    {
        $this->duplicateService = GeneralUtility::makeInstance(DuplicateCommand::class);
    }

    public function getTitle(): string
    {
        return 'Create usergroup';
    }

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        $userGroups = $this->getAllUsergroupsOfSourceSite($configuration->getSourceRootPageId());
        if ($userGroups) {
            $newIds = [];
            foreach ($userGroups as $usergroup) {
                $newId = $this->duplicateUsergroup($usergroup, $configuration, $response);
                $newIds[] = $newId;
            }
            $response->setUsergroups($newIds);
        }
    }

    protected function duplicateUsergroup(array $usergroup, Configuration $configuration, Response $response)
    {
        $targetUid = $this->duplicateService->duplicate(self::TABLE, $usergroup['uid']);

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(self::TABLE);

        $title = trim(str_replace('(copy 1)', '', $usergroup['title']));

        $connection->update(
            self::TABLE,
            [
                'title' => VariableReplacer::replace($title, $configuration),
                'db_mountpoints' => $response->getTargetRootPageId(),
                'file_mountpoints' => implode(',', $response->getSysFileMounts()),
                'tx_site_management_site' => 0,
                'tx_site_management_based_on' => $usergroup['uid']
            ],
            [
                'uid' => $targetUid
            ]
        );

        return $targetUid;
    }


    protected function getAllUsergroupsOfSourceSite(int $pageId)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);
        $rows = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('tx_site_management_site', $pageId)
            )
            ->execute()
            ->fetchAll();

        return $rows;
    }


}