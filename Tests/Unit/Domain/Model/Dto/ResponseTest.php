<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Domain\Model\Dto;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ResponseTest extends UnitTestCase
{

    /** @var Response */
    protected $subject;

    public function setUp(): void
    {
        $this->subject = new Response();
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
        $user = new User('janeDoe');
        $value = [1 => [$user]];
        $this->subject->setUsers($value);
        $this->assertEquals($value, $this->subject->getUsers());
    }


    /**
     * @test
     */
    public function sysFileMountsCanBeTest(): void
    {
        $value = [12, 35];
        $this->subject->setSysFileMounts($value);
        $this->assertEquals($value, $this->subject->getSysFileMounts());
    }

    /**
     * @test
     */
    public function usergroupsCanBeTest(): void
    {
        $value = [45, 67];
        $this->subject->setUsergroups($value);
        $this->assertEquals($value, $this->subject->getUsergroups());
    }

}