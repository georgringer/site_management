<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Domain\Model\Dto;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{

    /** @var Configuration */
    protected $subject;


    public function setUp()
    {
        $this->subject = new Configuration();
    }

    /**
     * @test
     */
    public function sourceRootPageIdCanBeTest()
    {
        $value = 123;
        $this->subject->setSourceRootPageId($value);
        $this->assertEquals($value, $this->subject->getSourceRootPageId());
    }
}