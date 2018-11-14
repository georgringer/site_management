<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CopyPageTree extends AbstractStep implements SiteCreationInterface
{

    /** @var DuplicateCommand */
    protected $duplicateCommandService;

    public function __construct()
    {
        $this->duplicateCommandService = GeneralUtility::makeInstance(DuplicateCommand::class);
    }

    public function getTitle(): string
    {
        return 'Copy page tree';
    }

    public function handle(array $stepConfiguration = []): void
    {
        $sourcePageId = $this->configuration->getSourceRootPageId();
        if ($sourcePageId === 0) {
            throw new \RuntimeException('No source root page has been defined!', 1541019057);
        }
        $newId = $this->duplicateCommandService->duplicate('pages', $sourcePageId);
        $this->response->setTargetRootPageId($newId);
    }

}