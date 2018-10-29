<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Controller;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use GeorgRinger\SiteManagement\Domain\Repository\SiteManagementRepository;
use GeorgRinger\SiteManagement\Exception\SiteConfigurationException;
use GeorgRinger\SiteManagement\SiteCreation\SiteCreationHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\ViewInterface;

class SiteManagementController
{
    /** @var ModuleTemplate */
    protected $moduleTemplate;

    /** @var ViewInterface */
    protected $view;

    /** @var SiteFinder */
    protected $siteFinder;

    /** @var SiteManagementRepository */
    protected $siteManagementRepository;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->siteManagementRepository = GeneralUtility::makeInstance(SiteManagementRepository::class);
    }

    /**
     * Main entry method: Dispatch to other actions - those method names that end with "Action".
     *
     * @param ServerRequestInterface $request the current request
     * @return ResponseInterface the response with the content
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        $action = $request->getQueryParams()['action'] ?? $request->getParsedBody()['action'] ?? 'overview';
        $this->initializeView($action);
        $result = call_user_func_array([$this, $action . 'Action'], [$request]);
        if ($result instanceof ResponseInterface) {
            return $result;
        }
        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * List pages that have 'is_siteroot' flag set - those that have the globe icon in page tree.
     * Link to Add / Edit / Delete for each.
     */
    protected function overviewAction(ServerRequestInterface $request): void
    {
        $demoSites = $this->siteManagementRepository->getDemoSiteRows();

        $this->view->assignMultiple([
            'demoSites' => $demoSites,
        ]);
    }

    protected function demoSiteSelectionAction(ServerRequestInterface $request): void
    {
        $selectedDemoSite = $request->getQueryParams()['site'];
        $demoSites = $this->siteManagementRepository->getDemoSiteRows();
        $demoSite = $demoSites[$selectedDemoSite];
        if ($demoSite) {
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            $this->view->assignMultiple([
                'siteConfiguration' => $siteFinder->getSiteByRootPageId($demoSite['uid']),
                'users' => $this->siteManagementRepository->getUsersOfDemoSite($demoSite['uid'])
            ]);
        }
        $this->view->assignMultiple([
            'demoSite' => $demoSite,
        ]);
    }

    protected function createAction(ServerRequestInterface $request)
    {
        try {
            $configuration = $this->createConfigurationFromRequest($request);
            $this->validateConfiguration($configuration);
        } catch (SiteConfigurationException $e) {
            $siteId = $request->getParsedBody()['base'];
            $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $e->getMessage(), '', FlashMessage::ERROR, true);

            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $defaultFlashMessageQueue->enqueue($flashMessage);

            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $redirectUrl = (string)$uriBuilder->buildUriFromRoute('site_management', [
                'action' => 'demoSiteSelection',
                'site' => $siteId
            ]);
            return new RedirectResponse($redirectUrl);
        }

        $siteCreation = GeneralUtility::makeInstance(SiteCreationHandler::class, $configuration);
        $response = $siteCreation->handle();

        $this->view->assignMultiple([
            'response' => $response
        ]);
    }

    /**
     * @param Configuration $configuration
     * @throws SiteConfigurationException
     */
    protected function validateConfiguration(Configuration $configuration)
    {

        // site identifier not used
        try {
            $siteExists = $this->siteFinder->getSiteByIdentifier($configuration->getIdentifier());
            throw new SiteConfigurationException(sprintf('Site identifier "%s" does already exist!', $configuration->getIdentifier()), 1540665355);
        } catch (SiteNotFoundException $e) {
            // site not found is good in that case
        }

        // source page is valid
        if (!$this->siteManagementRepository->demoSiteExists($configuration->getSourceRootPageId())) {
            throw new SiteConfigurationException(sprintf('Demo site with page id "%s" does not exist!', $configuration->getSourceRootPageId()), 1540665355);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return Configuration
     * @throws SiteConfigurationException
     */
    protected function createConfigurationFromRequest(ServerRequestInterface $request): Configuration
    {
        $configuration = GeneralUtility::makeInstance(Configuration::class);

        $vars = $request->getParsedBody();
        $configuration->setIdentifier($vars['identifier']);
        $configuration->setSourceRootPageId((int)$vars['base']);
        $configuration->setLanguages((array)$vars['languages']);
        $configuration->setDomain($vars['domain']);

        $users = $vars['users'];
        if (!empty($users)) {
            try {
                $userCollection = [];
                foreach ($users as $userTemplateId => $userData) {
                    $userSplit = GeneralUtility::trimExplode(LF, $userData, true);
                    foreach ($userSplit as $line) {
                        $newUser = GeneralUtility::makeInstance(User::class, $line);
                        $userCollection[$userTemplateId][] = $newUser;
                    }
                }
                $configuration->setUsers($userCollection);
            } catch (\RuntimeException $exception) {
                throw new SiteConfigurationException(sprintf('Error with user "%s": %s', $line, $exception->getMessage()), 1540665356);
            }
        }

        return $configuration;
    }

    /**
     * Sets up the Fluid View.
     *
     * @param string $templateName
     */
    protected function initializeView(string $templateName): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:site_management/Resources/Private/Templates/']);
        $this->view->setPartialRootPaths(['EXT:site_management/Resources/Private/Partials']);
        $this->view->setLayoutRootPaths(['EXT:site_management/Resources/Private/Layouts']);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
