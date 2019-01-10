<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {
        $additionalColumns = [
            'tx_site_management_site' => [
                'label' => 'LLL:EXT:site_management/Resources/Private/Language/locallang.xlf:tx_site_management_site',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', '']
                    ],
                    'foreign_table' => 'pages',
                    'foreign_table_where' => 'is_siteroot=1',
                ],
            ],
            'tx_site_management_based_on' => [
                'label' => 'LLL:EXT:site_management/Resources/Private/Language/locallang.xlf:tx_site_management_based_on',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => $table,
                    'foreign_table' => $table,
                    'size' => 1,
                    'readOnly' => true,
                ],
            ]
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table, 'tx_site_management_site,tx_site_management_based_on');
    },
    'be_users'
);
