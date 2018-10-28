<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Domain\Model\Dto;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{

    /** @var Configuration */
    protected $subject;

    public function setUp(): void
    {
        $this->subject = new Configuration();
    }

    /**
     * @test
     */
    public function sourceRootPageIdCanBeTest(): void
    {
        $value = 123;
        $this->subject->setSourceRootPageId($value);
        $this->assertEquals($value, $this->subject->getSourceRootPageId());
    }

    /**
     * @test
     */
    public function identifierCanBeTest(): void
    {
        $value = 'customer1234';
        $this->subject->setIdentifier($value);
        $this->assertEquals($value, $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function languagesCanBeTest(): void
    {
        $value = [1, 4, 6];
        $this->subject->setLanguages($value);
        $this->assertEquals($value, $this->subject->getLanguages());
    }

    /**
     * @test
     */
    public function googleTagManagerCanBeTest(): void
    {
        $value = 'ABC-123-456';
        $this->subject->setGoogleTagManager($value);
        $this->assertEquals($value, $this->subject->getGoogleTagManager());
    }

    /**
     * @test
     */
    public function domainCanBeTest(): void
    {
        $value = 'maxmustermann.com';
        $this->subject->setDomain($value);
        $this->assertEquals($value, $this->subject->getDomain());
    }

    /**
     * @test
     */
    public function targetRootPageCanBeTest(): void
    {
        $value = 456;
        $this->subject->setTargetRootPageId($value);
        $this->assertEquals($value, $this->subject->getTargetRootPageId());
    }

    /**
     * @test
     */
    public function usersCanBeTest(): void
    {
        $user = new User('johnDoe');
        $value = [1 => [$user]];
        $this->subject->setUsers($value);
        $this->assertEquals($value, $this->subject->getUsers());
    }

}