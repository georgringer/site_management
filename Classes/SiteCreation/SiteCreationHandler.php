<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\SiteCreationInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteCreationHandler
{

    /** @var Configuration */
    protected $configuration;

    /** @var PackageManager */
    protected $packageManager;

    /** @var DependencyOrderingService */
    protected $dependencyOrderingService;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $this->dependencyOrderingService = GeneralUtility::makeInstance(DependencyOrderingService::class);
    }


    public function handle(): Response
    {
        $response = GeneralUtility::makeInstance(Response::class);

        $allConfiguredSteps = $this->loadConfiguration();
        $allSteps = $this->sanitizeSteps($allConfiguredSteps);

        foreach ($allSteps as $name => $stepConfiguration) {
            /** @var SiteCreationInterface $step */
            $step = GeneralUtility::makeInstance($stepConfiguration['target']);
            if ($step->isValid()) {
                $options = $stepConfiguration['options'] ?? [];
                $step->handle($this->configuration, $response, $options);
            }
        }

        return $response;
    }

    /**
     * Loop over all packages and check for a Configuration/SiteCreationSteps.php file
     *
     * @return array
     */
    protected function loadConfiguration(): array
    {
        $packages = $this->packageManager->getActivePackages();
        $allSteps = [[]];
        foreach ($packages as $package) {
            $packageConfiguration = $package->getPackagePath() . 'Configuration/SiteCreationSteps.php';
            if (file_exists($packageConfiguration)) {
                $stepConfiguration = require $packageConfiguration;
                if (is_array($stepConfiguration)) {
                    $allSteps[] = $stepConfiguration;
                }
            }
        }
        return array_replace_recursive(...$allSteps);
    }

    /**
     * Order and sanizize steps
     *
     * @param array
     * @return array
     */
    protected function sanitizeSteps(array $allSteps): array
    {
        $steps = [];
        foreach ($allSteps as $stepsOfStack) {
            foreach ($stepsOfStack as $name => $step) {
                // Skip this step if disabled by configuration
                if (isset($step['disabled']) && $step['disabled'] === true) {
                    continue;
                }
                $steps[$name] = $step;
            }
        }

        $steps = $this->dependencyOrderingService->orderByDependencies($steps);
        return $steps;
    }
}