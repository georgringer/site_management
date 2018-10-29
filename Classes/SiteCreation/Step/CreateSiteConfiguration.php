<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateSiteConfiguration extends AbstractStep implements SiteCreationInterface
{

    /** @var SiteFinder */
    protected $siteFinder;

    /** @var SiteConfiguration */
    protected $siteConfigurationManager;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->siteConfigurationManager = GeneralUtility::makeInstance(SiteConfiguration::class, Environment::getConfigPath() . '/sites');
    }

    public function getTitle(): string
    {
        return 'Create Site configuration';
    }

    /**
     * @param Configuration $configuration
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        return;
        $currentSite = $this->siteFinder->getSiteByRootPageId($configuration->getSourceRootPageId());
        $currentSiteConfiguration = $currentSite->getConfiguration();

        $targetConfiguration = $this->mergeConfigurationIntoSiteConfiguration($currentSiteConfiguration, $response, $configuration);

        // Persist the configuration
        $this->siteConfigurationManager->write($configuration->getIdentifier(), $targetConfiguration);
        $this->clearCaches();
    }

    protected function mergeConfigurationIntoSiteConfiguration(array $sourceConfiguration, Response $response, Configuration $configuration)
    {
        $sourceConfiguration['rootPageId'] = $response->getTargetRootPageId();
        $sourceConfiguration['base'] = $configuration->getDomain();

        foreach ($sourceConfiguration['languages'] as $key => $language) {
            if (!\in_array($language['languageId'], $configuration->getLanguages())) {
                unset($sourceConfiguration['languages'][$key]);
            }
        }

        return $sourceConfiguration;
    }

    /**
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    protected function clearCaches()
    {
        $this->getCache()->remove('pseudo-sites');
        $this->getCache()->remove('legacy-domains');
    }

    /**
     * Shorthand method to flush the related caches
     * @return FrontendInterface
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    protected function getCache(): FrontendInterface
    {
        return GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_core');
    }

}