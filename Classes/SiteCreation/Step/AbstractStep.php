<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;

abstract class AbstractStep implements SiteCreationInterface
{
//
    /** @var Configuration */
    protected $configuration;

    /** @var Response */
    protected $response;

    public function setup(Configuration $configuration, Response $response)
    {
        $this->configuration = $configuration;
        $this->response = $response;
    }


    public function isValid(): bool
    {
        return true;
    }

}