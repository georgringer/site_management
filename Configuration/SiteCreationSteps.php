<?php

return [
    'site_management' => [
        'typo3/ext-site_management/copy-pagetree' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\CopyPageTree::class,
        ],
        'typo3/ext-site_management/create-site-configuration' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\CreateSiteConfiguration::class,
            'after' => [
                'typo3/ext-site_management/copy-pagetree',
            ]
        ],
        'typo3/ext-site_management/reset-root-page' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\ResetRootPage::class,
            'after' => [
                'typo3/ext-site_management/create-site-configuration',
            ]
        ],
        'typo3/ext-site_management/create-sysfilemounts' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\CreateSysFileMounts::class,
            'after' => [
                'typo3/ext-site_management/reset-root-page',
            ]
        ],
        'typo3/ext-site_management/create-usergroups' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\CreateUserGroup::class,
            'after' => [
                'typo3/ext-site_management/create-sysfilemounts',
            ]
        ],
        'typo3/ext-site_management/create-users' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\CreateUsers::class,
            'after' => [
                'typo3/ext-site_management/create-usergroups',
            ]
        ],
        'typo3/ext-site_management/send-mail' => [
            'target' => \GeorgRinger\SiteManagement\SiteCreation\Step\SendMail::class,
            'after' => [
                'typo3/ext-site_management/create-users',
            ],
            'options' => [
                'to' => 'owner@youremail.com',
                'fromName' => 'Website',
                'fromEmail' => 'noreply@website.com',
                'subject' => 'New site has been created',
                'plainContent' => 'EXT:site_management/Resources/Private/Templates/Email/SendMail.txt',
            ]
        ],
    ]
];