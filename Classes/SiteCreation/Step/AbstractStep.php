<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;


use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;

abstract class AbstractStep
{

    /** @var Configuration */
    protected $configuration;

    /**
     * AbstractStep constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


}