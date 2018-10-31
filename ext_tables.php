<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    'site',
    'management',
    'bottom',
    '',
    [
        'routeTarget' => \GeorgRinger\SiteManagement\Controller\SiteManagementController::class . '::handleRequest',
        'access' => 'admin',
        'name' => 'site_management',
        'icon' => 'EXT:site_management/Resources/Public/Icons/module-site-management.svg',
        'labels' => 'LLL:EXT:site_management/Resources/Private/Language/locallang_sitemanagement_module.xlf'
    ]
);