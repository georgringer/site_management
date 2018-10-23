<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {

        $GLOBALS['SiteConfiguration'][$table]['columns']['googleTagManager'] = [
            'label' => 'Google Tag Manager',
            'config' => [
                'type' => 'input',
                'eval' => '',
            ],
        ];
        // And add it to showitem
        $GLOBALS['SiteConfiguration'][$table]['types']['0']['showitem'] .= ',--div--;SiteMgmt,googleTagManager';
    },
    'site'
);
