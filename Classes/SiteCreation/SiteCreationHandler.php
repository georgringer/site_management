<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\SiteCreation\Step\CopyPageTree;
use GeorgRinger\SiteManagement\SiteCreation\Step\SiteCreationInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteCreationHandler
{

    /** @var Configuration */
    protected $configuration;

    protected static $handlers = [
        CopyPageTree::class
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

        foreach (self::$handlers as $class) {
            /** @var SiteCreationInterface $step */
            $step = GeneralUtility::makeInstance($class, $this->configuration);
            if ($step->isValid()) {
                $step->handle();
            }
        }
        print_r($this->configuration);
        die('xxx');
    }


}