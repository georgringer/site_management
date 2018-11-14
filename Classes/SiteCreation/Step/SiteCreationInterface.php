<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;

interface SiteCreationInterface
{

    public function getTitle(): string;

    public function isValid(): bool;


    public function handle(array $stepConfiguration = []): void;

}