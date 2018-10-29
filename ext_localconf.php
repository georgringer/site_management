<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['site_management']['steps'] = [
    \GeorgRinger\SiteManagement\SiteCreation\Step\CopyPageTree::class => [
        'options' => []
    ],
    \GeorgRinger\SiteManagement\SiteCreation\Step\CreateSiteConfiguration::class => [
        'options' => []
    ],
    \GeorgRinger\SiteManagement\SiteCreation\Step\ResetRootPage::class => [
        'options' => []
    ],
    \GeorgRinger\SiteManagement\SiteCreation\Step\CreateUsergroups::class => [
        'options' => []
    ],
    \GeorgRinger\SiteManagement\SiteCreation\Step\SendMail::class => [
        'options' => []
    ],
];
