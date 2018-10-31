<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
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

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        $newId = $this->duplicateCommandService->duplicate('pages', $configuration->getSourceRootPageId());
//        $newId = 65;
        $response->setTargetRootPageId($newId);
    }

}