<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Utility;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Utility\VariableReplacer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class VariableReplacerTest extends UnitTestCase
{

    /**
     * @test
     */
    public function configurationIsReplaced(): void
    {
        $configuration = new Configuration();
        $configuration->setIdentifier('master');
        $this->assertEquals('Template master', VariableReplacer::replace('Template {configuration.identifier}', $configuration));
    }

    /**
     * @test
     */
    public function configurationWithNoVariableKeepsSame(): void
    {
        $configuration = new Configuration();
        $configuration->setIdentifier('master');
        $configuration->setDomain('bla.com');
        $this->assertEquals('Template master with no variable', VariableReplacer::replace('Template master with no variable', $configuration));
    }

}