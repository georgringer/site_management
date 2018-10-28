<?php

namespace GeorgRinger\SiteManagement\Utility;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DuplicateCommand
{

    public function duplicate(string $table, int $sourcePageId, int $targetPid = 0): int
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->copyTree = 99;
        $dataHandler->admin = true;
        $duplicateCmd = [
            $table => [
                $sourcePageId => [
                    'copy' => $targetPid
                ]
            ]
        ];

        $dataHandler->start([], $duplicateCmd);
        $dataHandler->process_cmdmap();

        $duplicateMappingArray = $dataHandler->copyMappingArray;
        $duplicateUid = $duplicateMappingArray[$table][$sourcePageId];

        return $duplicateUid;
    }

}