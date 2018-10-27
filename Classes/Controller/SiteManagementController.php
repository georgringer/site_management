<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Controller;


use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Repository\SiteManagementRepository;
use GeorgRinger\SiteManagement\SiteCreation\SiteCreationHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3Fluid\Fluid\View\ViewInterface;

class SiteManagementController
{
    /**
     * @var ModuleTemplate
     */
    protected $moduleTemplate;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var SiteFinder
     */
    protected $siteFinder;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
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
        $siteManagementRepository = GeneralUtility::makeInstance(SiteManagementRepository::class);
        $demoSites = $siteManagementRepository->getDemoSiteRows();

        $this->view->assignMultiple([
            'demoSites' => $demoSites,
        ]);
    }

    protected function demoSiteSelectionAction(ServerRequestInterface $request): void
    {
        $selectedDemoSite = $request->getQueryParams()['site'];
        $siteManagementRepository = GeneralUtility::makeInstance(SiteManagementRepository::class);
        $demoSites = $siteManagementRepository->getDemoSiteRows();
        $demoSite = $demoSites[$selectedDemoSite];
        if ($demoSite) {
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            $this->view->assign('siteConfiguration', $siteFinder->getSiteByRootPageId($demoSite['uid']));
        }
        $this->view->assignMultiple([
            'demoSite' => $demoSite,
        ]);
    }

    protected function createAction(ServerRequestInterface $request)
    {
        $configuration = $this->createConfigurationFromRequest($request);

        $siteCreation = GeneralUtility::makeInstance(SiteCreationHandler::class, $configuration);
        $siteCreation->handle();
    }

    protected function createConfigurationFromRequest(ServerRequestInterface $request): Configuration
    {
        $configuration = GeneralUtility::makeInstance(Configuration::class);

        $vars = $request->getParsedBody();
        $configuration->setIdentifier($vars['identifier']);
        $configuration->setSourceRootPageId((int)$vars['base']);
        $configuration->setLanguages((array)$vars['languages']);

        return $configuration;
    }

    /**
     * Validation and processing of site identifier
     *
     * @param bool $isNew If true, we're dealing with a new record
     * @param string $identifier Given identifier to validate and process
     * @param int $rootPageId Page uid this identifier is bound to
     * @return mixed Verified / modified value
     */
    protected function validateAndProcessIdentifier(bool $isNew, string $identifier, int $rootPageId)
    {
        $languageService = $this->getLanguageService();
        // Normal "eval" processing of field first
        $identifier = $this->validateAndProcessValue('site', 'identifier', $identifier);
        if ($isNew) {
            // Verify no other site with this identifier exists. If so, find a new unique name as
            // identifier and show a flash message the identifier has been adapted
            try {
                $this->siteFinder->getSiteByIdentifier($identifier);
                // Force this identifier to be unique
                $originalIdentifier = $identifier;
                $identifier = $identifier . '-' . str_replace('.', '', uniqid((string)mt_rand(), true));
                $message = sprintf(
                    $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.identifierRenamed.message'),
                    $originalIdentifier,
                    $identifier
                );
                $messageTitle = $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.identifierRenamed.title');
                $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, $messageTitle, FlashMessage::WARNING, true);
                $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                $defaultFlashMessageQueue->enqueue($flashMessage);
            } catch (SiteNotFoundException $e) {
                // Do nothing, this new identifier is ok
            }
        } else {
            // If this is an existing config, the site for this identifier must have the same rootPageId, otherwise
            // a user tried to rename a site identifier to a different site that already exists. If so, we do not rename
            // the site and show a flash message
            try {
                $site = $this->siteFinder->getSiteByIdentifier($identifier);
                if ($site->getRootPageId() !== $rootPageId) {
                    // Find original value and keep this
                    $origSite = $this->siteFinder->getSiteByRootPageId($rootPageId);
                    $originalIdentifier = $identifier;
                    $identifier = $origSite->getIdentifier();
                    $message = sprintf(
                        $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.identifierExists.message'),
                        $originalIdentifier,
                        $identifier
                    );
                    $messageTitle = $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.identifierExists.title');
                    $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, $messageTitle, FlashMessage::WARNING, true);
                    $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                    $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                    $defaultFlashMessageQueue->enqueue($flashMessage);
                }
            } catch (SiteNotFoundException $e) {
                // User is renaming identifier which does not exist yet. That's ok
            }
        }
        return $identifier;
    }


    /**
     * Last sanitation method after all data has been gathered. Check integrity
     * of full record, manipulate if possible, or throw exception if unfixable broken.
     *
     * @param array $newSysSiteData Incoming data
     * @return array Updated data if needed
     * @throws \RuntimeException
     */
    protected function validateFullStructure(array $newSysSiteData): array
    {
        $languageService = $this->getLanguageService();
        // Verify there are not two error handlers with the same error code
        if (isset($newSysSiteData['errorHandling']) && is_array($newSysSiteData['errorHandling'])) {
            $uniqueCriteria = [];
            $validChildren = [];
            foreach ($newSysSiteData['errorHandling'] as $child) {
                if (!isset($child['errorCode'])) {
                    throw new \RuntimeException('No errorCode found', 1521788518);
                }
                if (!in_array((int)$child['errorCode'], $uniqueCriteria, true)) {
                    $uniqueCriteria[] = (int)$child['errorCode'];
                    $validChildren[] = $child;
                } else {
                    $message = sprintf(
                        $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.duplicateErrorCode.message'),
                        $child['errorCode']
                    );
                    $messageTitle = $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.duplicateErrorCode.title');
                    $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, $messageTitle, FlashMessage::WARNING, true);
                    $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                    $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                    $defaultFlashMessageQueue->enqueue($flashMessage);
                }
            }
            $newSysSiteData['errorHandling'] = $validChildren;
        }

        // Verify there is only one inline child per sys_language record configured.
        if (!isset($newSysSiteData['languages']) || !is_array($newSysSiteData['languages']) || count($newSysSiteData['languages']) < 1) {
            throw new \RuntimeException(
                'No default language definition found. The interface does not allow this. Aborting',
                1521789306
            );
        }
        $uniqueCriteria = [];
        $validChildren = [];
        foreach ($newSysSiteData['languages'] as $child) {
            if (!isset($child['languageId'])) {
                throw new \RuntimeException('languageId not found', 1521789455);
            }
            if (!in_array((int)$child['languageId'], $uniqueCriteria, true)) {
                $uniqueCriteria[] = (int)$child['languageId'];
                $validChildren[] = $child;
            } else {
                $message = sprintf(
                    $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.duplicateLanguageId.title'),
                    $child['languageId']
                );
                $messageTitle = $languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_siteconfiguration.xlf:validation.duplicateLanguageId.title');
                $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, $messageTitle, FlashMessage::WARNING, true);
                $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                $defaultFlashMessageQueue->enqueue($flashMessage);
            }
        }
        $newSysSiteData['languages'] = $validChildren;

        return $newSysSiteData;
    }

    /**
     * Delete an existing configuration
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function deleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $siteIdentifier = $request->getQueryParams()['site'] ?? '';
        if (empty($siteIdentifier)) {
            throw new \RuntimeException('Not site identifier given', 1521565182);
        }
        // Verify site does exist, method throws if not
        GeneralUtility::makeInstance(SiteConfiguration::class, Environment::getConfigPath() . '/sites')->delete($siteIdentifier);
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $overviewRoute = $uriBuilder->buildUriFromRoute('site_configuration', ['action' => 'overview']);
        return new RedirectResponse($overviewRoute);
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
     * Create document header buttons of "edit" action
     */
    protected function configureEditViewDocHeader(): void
    {
        $iconFactory = $this->moduleTemplate->getIconFactory();
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $lang = $this->getLanguageService();
        $closeButton = $buttonBar->makeLinkButton()
            ->setHref('#')
            ->setClasses('t3js-editform-close')
            ->setTitle($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.closeDoc'))
            ->setShowLabelText(true)
            ->setIcon($iconFactory->getIcon('actions-close', Icon::SIZE_SMALL));
        $saveButton = $buttonBar->makeInputButton()
            ->setTitle($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.saveDoc'))
            ->setName('_savedok')
            ->setValue('1')
            ->setShowLabelText(true)
            ->setForm('siteConfigurationController')
            ->setIcon($iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL));
        $buttonBar->addButton($closeButton);
        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
    }

    /**
     * Create document header buttons of "overview" action
     */
    protected function configureOverViewDocHeader(): void
    {
        $iconFactory = $this->moduleTemplate->getIconFactory();
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $getVars = ['id', 'route'];
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName('site_configuration')
                ->setGetVariables($getVars);
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    /**
     * Returns a list of pages that have 'is_siteroot' set
     *
     * @return array
     */
    protected function getAllSitePages(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeByType(HiddenRestriction::class);
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class, 0, false));
        $statement = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('sys_language_uid', 0),
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('pid', 0),
                        $queryBuilder->expr()->neq('doktype', PageRepository::DOKTYPE_SYSFOLDER)
                    ),
                    $queryBuilder->expr()->eq('is_siteroot', 1)
                )
            )
            ->orderBy('pid')
            ->addOrderBy('sorting')
            ->execute();

        $pages = [];
        while ($row = $statement->fetch()) {
            $row['rootline'] = BackendUtility::BEgetRootLine((int)$row['uid']);
            array_pop($row['rootline']);
            $row['rootline'] = array_reverse($row['rootline']);
            $i = 0;
            foreach ($row['rootline'] as &$record) {
                $record['margin'] = $i++ * 20;
            }
            $pages[(int)$row['uid']] = $row;
        }
        return $pages;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
