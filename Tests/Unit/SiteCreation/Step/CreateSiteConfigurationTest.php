<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;


use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\CreateSiteConfiguration;
use TYPO3\TestingFramework\Core\BaseTestCase;

class CreateSiteConfigurationTest extends BaseTestCase
{


    /**
     * @test
     */
    public function titleIsReturned(): void
    {
        $subject = $this->getAccessibleMock(CreateSiteConfiguration::class, ['dummy'], [], '', false);

        $this->assertNotEmpty($subject->getTitle());
    }

    /**
     * @test
     */
    public function propertiesAreProperlyMerged(): void
    {
        $subject = $this->getAccessibleMock(CreateSiteConfiguration::class, ['dummy'], [], '', false);

        $sourceConfiguration = [
            'fo' => 123,
            'rootPageId' => 1,
            'base' => 'default.vm',
            'languages' => [
                1 => ['languageId' => 0],
                2 => ['languageId' => 1],
                3 => ['languageId' => 2]
            ]
        ];
        $response = new Response();
        $response->setTargetRootPageId(2);
        $configuration = new Configuration();
        $configuration->setLanguages([0, 2]);
        $configuration->setDomain('new.vm');

        $subject->setup($configuration, $response);

        $targetConfiguration = [
            'fo' => 123,
            'rootPageId' => 2,
            'base' => 'new.vm',
            'languages' => [
                1 => ['languageId' => 0],
                3 => ['languageId' => 2]
            ]
        ];

        $newConfiguration = $subject->_call('mergeConfigurationIntoSiteConfiguration', $sourceConfiguration);
        $this->assertEquals($targetConfiguration, $newConfiguration);
    }
}