<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

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

    public function handle(): void
    {
        $duplicateTce = GeneralUtility::makeInstance(DataHandler::class);
        $duplicateTce->copyTree = 99;
        $sourcePageUid = $this->configuration->getSourceRootPageId();

        $duplicateCmd = [
            'pages' => [
                $sourcePageUid => [
                    'copy' => 0
                ]
            ]
        ];

        $duplicateTce->start([], $duplicateCmd);
        $duplicateTce->process_cmdmap();

        $duplicateMappingArray = $duplicateTce->copyMappingArray;
        $duplicateUid = $duplicateMappingArray['pages'][$sourcePageUid];

        $this->configuration->setTargetRootPageId($duplicateUid);
    }


}