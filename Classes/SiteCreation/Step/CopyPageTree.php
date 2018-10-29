<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CopyPageTree extends AbstractStep implements SiteCreationInterface
{
    public function getTitle(): string
    {
        return 'Copy page tree';
    }

    public function isValid(): bool
    {
        return true;
    }

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        $sourcePageUid = $configuration->getSourceRootPageId();

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->copyTree = 99;
        $dataHandler->admin = true;
        $duplicateCmd = [
            'pages' => [
                $sourcePageUid => [
                    'copy' => 0
                ]
            ]
        ];

        $dataHandler->start([], $duplicateCmd);
        $dataHandler->process_cmdmap();

        $duplicateMappingArray = $dataHandler->copyMappingArray;
        $duplicateUid = $duplicateMappingArray['pages'][$sourcePageUid];

        $response->setTargetRootPageId($duplicateUid);
    }

}