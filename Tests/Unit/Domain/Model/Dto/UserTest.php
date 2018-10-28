<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Domain\Model\Dto;

use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class UserTest extends UnitTestCase
{

    /**
     * @test
     */
    public function userCanBeSetTest(): void
    {
        $string = 'johndoe|John Doe|john@example.com';
        $user = new User($string);

        $this->assertEquals('johndoe', $user->getUsername());
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
    }

    /**
     * @test
     */
    public function emptyTemplateThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1540751157);

        new User(' ');
    }

    /**
     * @test
     */
    public function invalidEmailThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1540751158);

        $string = 'johndoe|John Doe|johnexample.com';
        new User($string);
    }

}