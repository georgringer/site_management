<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {

        $additionalColumns = [
            'tx_site_management_demo_tree' => [
                'exclude' => true,
                'label' => 'LLL:EXT:site_management/Resources/Private/Language/locallang.xlf:pages.tx_site_management_demo_tree',
                'config' => [
                    'type' => 'check',
                ],
            ],
            'tx_site_management_feature' => [
                'exclude' => true,
                'label' => 'LLL:EXT:site_management/Resources/Private/Language/locallang.xlf:pages.tx_site_management_feature',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [
                            '',
                            ''
                        ],
                        [
                            'Feature 1',
                            'feature_1'
                        ],
                        [
                            'Feature 2',
                            'feature_2'
                        ],
                    ],
                ],
            ]
        ];
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table, 'tx_site_management_demo_tree,tx_site_management_feature');


    },
    'pages'
);
