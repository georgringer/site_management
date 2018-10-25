<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\SiteCreation\Step\CopyPageTree;
use GeorgRinger\SiteManagement\SiteCreation\Step\CreateSiteConfiguration;
use GeorgRinger\SiteManagement\SiteCreation\Step\SendMail;
use GeorgRinger\SiteManagement\SiteCreation\Step\SiteCreationInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteCreationHandler
{

    /** @var Configuration */
    protected $configuration;

    protected static $handlers = [
        CopyPageTree::class,
        CreateSiteConfiguration::class,
        SendMail::class
    ];

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
        $configuration = $this->configuration;

        foreach (self::$handlers as $class) {
            /** @var SiteCreationInterface $step */
            $step = GeneralUtility::makeInstance($class);
            if ($step->isValid()) {
                $step->handle($configuration);
            }
        }
        print_r($configuration);
        die('xxx');
    }


}