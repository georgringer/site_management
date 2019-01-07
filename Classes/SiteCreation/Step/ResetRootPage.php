<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Utility\VariableReplacer;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class ResetRootPage extends AbstractStep
{
    public function getTitle(): string
    {
        return 'Reset root page';
    }

    public function handle(array $stepConfiguration = []): void
    {
        $originalRow = $this->getOriginalRow();
        $currentPageTitle = trim(str_replace('(copy 1)', '', $originalRow['title']));

        $update = [
            'title' => VariableReplacer::replace($currentPageTitle, $this->configuration),
            'rowDescription' => '',
            'tx_site_management_demo_tree' => 0,
            'hidden' => 1,
        ];

        $this->updateRow('pages', $update, [
            'uid' => $this->response->getTargetRootPageId()
        ]);
    }

    /**
     * @return array
     */
    protected function getOriginalRow(): array
    {
        return BackendUtility::getRecord('pages', $this->response->getTargetRootPageId());
    }

}