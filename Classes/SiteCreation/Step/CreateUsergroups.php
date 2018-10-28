<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;

class CreateUsergroups extends AbstractStep implements SiteCreationInterface
{
    public function getTitle(): string
    {
        return 'Create usergroups';
    }

    public function isValid(): bool
    {
        return true;
    }

    public function handle(Configuration $configuration): void
    {

    }

}