<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\SiteCreationInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteCreationHandler
{

    /** @var Configuration */
    protected $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    public function handle(): Response
    {
        $response = GeneralUtility::makeInstance(Response::class);

        $configuration = $this->configuration;

        foreach ($this->getSteps() as $class => $stepConfiguration) {
            /** @var SiteCreationInterface $step */
            $step = GeneralUtility::makeInstance($class);
            if ($step->isValid()) {
                $options = $stepConfiguration['options'] ?? [];
                $step->handle($configuration, $response, $options);
            }
        }

        return $response;
    }

    protected function getSteps(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['site_management']['steps'];
    }

}