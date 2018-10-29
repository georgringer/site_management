<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use GeorgRinger\SiteManagement\Utility\VariableReplacer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateSysFileMounts extends AbstractStep implements SiteCreationInterface
{
    private const TABLE = 'sys_filemounts';

    /** @var DuplicateCommand */
    protected $duplicateService;

    /** @var ResourceFactory */
    protected $fileFactory;

    public function __construct()
    {
        $this->duplicateService = GeneralUtility::makeInstance(DuplicateCommand::class);
        $this->fileFactory = ResourceFactory::getInstance();
    }

    public function getTitle(): string
    {
        return 'Create sys_filemounts';
    }

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        $fileMounts = $this->getAllFileMountsOfSourceSite($configuration->getSourceRootPageId());

        if ($fileMounts) {
            $newFileMountIds = [];
            foreach ($fileMounts as $fileMount) {
                $newFileMountId = $this->duplicateFileMount($fileMount, $configuration);
                $newFileMountIds[] = $newFileMountId;
            }
            $response->setSysFileMounts($newFileMountIds);
        }
    }

    protected function duplicateFileMount(array $row, Configuration $configuration): int
    {
        $newFileMountId = 0;
        $identifier = $row['base'] . ':' . $row['path'];
        $sourceMount = $this->fileFactory->getFolderObjectFromCombinedIdentifier($identifier);
        if ($sourceMount) {
            $parentDirectory = $sourceMount->getParentFolder();

            $newFolderName = $configuration->getIdentifier();

            if (!$parentDirectory->hasFolder($newFolderName)) {
                $newFolder = $parentDirectory->createFolder($newFolderName);
            } else {
                $newFolder = $parentDirectory->getSubfolder($newFolderName);
            }

            $newFileMountId = $this->getFileMount($row['base'], $newFolder->getIdentifier(), $row['uid'], $configuration);
        }

        return $newFileMountId;
    }

    protected function getFileMount(int $storage, string $identifier, int $sourceId, Configuration $configuration)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::TABLE);
        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('base', $storage, \PDO::PARAM_INT),
                $queryBuilder->expr()->eq('path', $queryBuilder->createNamedParameter($identifier, \PDO::PARAM_STR))
            )
            ->execute()
            ->fetch();

        if (\is_array($row)) {
            return $row['uid'];
        } else {
            $targetUid = $this->duplicateService->duplicate(self::TABLE, $sourceId);

            $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(self::TABLE);

            $originalRow = BackendUtility::getRecord(self::TABLE, $sourceId);
            $currentPageTitle = trim(str_replace('(copy 1)', '', $originalRow['title']));

            $connection->update(
                self::TABLE,
                [
                    'title' => VariableReplacer::replace($currentPageTitle, $configuration),
                    'path' => $identifier,
                    'tx_site_management_site' => 0,
                ],
                [
                    'uid' => $targetUid
                ]
            );

            return $targetUid;
        }
    }

    protected function getAllFileMountsOfSourceSite(int $pageId)
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