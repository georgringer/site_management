<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Base
{

    /** @var Configuration */
    protected $configuration;

    /**
     * Base constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    public function handle()
    {


    }

    protected function duplicatePage()
    {

        $duplicateTce = GeneralUtility::makeInstance(DataHandler::class);
        $sourcePageUid = 1;

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

        return $duplicateUid;
    }
}