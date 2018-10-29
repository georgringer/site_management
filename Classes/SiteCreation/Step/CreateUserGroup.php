<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateUserGroup extends AbstractStep implements SiteCreationInterface
{

    /** @var DuplicateCommand */
    protected $duplicateCommand;

    public function __construct()
    {
        $this->duplicateCommand = GeneralUtility::makeInstance(DuplicateCommand::class);
    }

    public function getTitle(): string
    {
        return 'Create usergroup';
    }

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {

    }


}