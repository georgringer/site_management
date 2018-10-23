<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {

        $additionalColumns = [
            'tx_site_management_site' => [
                'exclude' => true,
                'label' => 'Used in site',
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
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table, 'tx_site_management_site');


    },
    'be_groups'
);
