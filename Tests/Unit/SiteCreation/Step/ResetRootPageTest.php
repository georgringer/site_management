<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\ResetRootPage;
use TYPO3\TestingFramework\Core\BaseTestCase;

class ResetRootPageTest extends BaseTestCase
{

    /**
     * @test
     */
    public function userIsDuplicatedTest(): void
    {
        $subject = $this->getAccessibleMock(ResetRootPage::class, ['updateRow', 'getOriginalRow'], [], '', false);

        $response = new Response();
        $response->setTargetRootPageId(1337);
        $configuration = new Configuration();
        $configuration->setIdentifier('fobar');

        $subject->setup($configuration, $response);

        $update = [
            'title' => 'site fobar',
            'rowDescription' => '',
            'tx_site_management_demo_tree' => 0,
            'hidden' => 1,
        ];
        $originalRow = [
            'title' => 'site {configuration.identifier}'
        ];
        $subject->expects($this->once())->method('getOriginalRow')->willReturn($originalRow);
        $subject->expects($this->once())->method('updateRow')->with('pages', $update, ['uid' => 1337]);

        $subject->_call('handle');
    }

    /**
     * @test
     */
    public function titleIsReturned(): void
    {
        $subject = $this->getAccessibleMock(ResetRootPage::class, ['dummy'], [], '', false);

        $this->assertNotEmpty($subject->getTitle());
    }

}