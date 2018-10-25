<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {
        $additionalColumns = [
            'tx_site_management_site' => [
                'label' => 'Used in site',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', '']
                    ],
                    'foreign_table' => 'pages',
                    'foreign_table_where' => 'tx_site_management_demo_tree=1',
                ],
            ]
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes($table, 'tx_site_management_site');
    },
    'be_groups'
);
