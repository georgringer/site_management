<?php

namespace GeorgRinger\SiteManagement\Utility;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DuplicateCommand
{
    /** @var DataHandler */
    protected $dataHandler;

    public function __construct()
    {
        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $this->dataHandler->copyTree = 99;
        $this->dataHandler->admin = true;
    }

    public function duplicate(string $table, int $sourcePageId, int $targetPid = 0): int
    {
        $duplicateCmd = [
            $table => [
                $sourcePageId => [
                    'copy' => $targetPid
                ]
            ]
        ];

        $this->dataHandler->start([], $duplicateCmd);
        $this->dataHandler->process_cmdmap();

        $duplicateMappingArray = $this->dataHandler->copyMappingArray;
        $duplicateUid = $duplicateMappingArray[$table][$sourcePageId];

        return $duplicateUid;
    }

}