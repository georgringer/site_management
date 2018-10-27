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
    public function configurationIsReplaced()
    {
        $configuration = new Configuration();
        $configuration->setIdentifier('master');
        $this->assertEquals('Template master', VariableReplacer::replace('Template {configuration.identifier}', $configuration));
    }

}