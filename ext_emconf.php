<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Site Management',
    'description' => '',
    'category' => 'Site',
    'author' => 'Georg Ringer',
    'author_email' => 'mail@ringer.it',
    'state' => 'alpha',
    'version' => '0.1.0',
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '9.5.0-9.5.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [

                ],
        ],
    'autoload' =>
        [
            'psr-4' =>
                [
                    'GeorgRinger\\SiteManagement\\' => 'Classes',
                ],
        ],
    'autoload-dev' =>
        [
            'psr-4' =>
                [
                    'GeorgRinger\\SiteManagement\\Tests\\' => 'Tests',
                ],
        ],
];
