<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use GeorgRinger\SiteManagement\SiteCreation\Step\CreateUsers;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use TYPO3\CMS\Core\Crypto\PasswordHashing\Argon2iPasswordHash;
use TYPO3\TestingFramework\Core\BaseTestCase;

class CreateUsersTest extends BaseTestCase
{

    /**
     * @test
     */
    public function passwordIsUnique(): void
    {
        $attempts = 100;
        $subject = $this->getAccessibleMock(CreateUsers::class, ['dummy'], [], '', false);

        $passwordList = [];
        $passwordUnique = true;
        for ($i = 1; $i <= $attempts; $i++) {
            $password = $subject->_call('generatePassword');
            if (isset($passwordList[$password])) {
                $passwordUnique = false;
                continue;
            }
            $passwordList[$password] = 1;
        }
        $this->assertTrue($passwordUnique, sprintf('Password not unique for %s attempts', $attempts));
    }

    /**
     * @test
     */
    public function userIsDuplicatedTest(): void
    {
        $duplicateCommandProphecy = $this->prophesize(DuplicateCommand::class);
        $duplicateCommandProphecy->duplicate(\Prophecy\Argument::cetera())->willReturn(1337);

        $passwordHashInstanceProphecy = $this->prophesize(Argon2iPasswordHash::class);
        $passwordHashInstanceProphecy->getHashedPassword(\Prophecy\Argument::cetera())->willReturn('hashedPassword');

        $subject = $this->getAccessibleMock(CreateUsers::class, ['updateRow'], [], '', false);
        $subject->_set('duplicateCommand', $duplicateCommandProphecy->reveal());
        $subject->_set('passwordHashInstance', $passwordHashInstanceProphecy->reveal());

        $response = new Response();
        $response->setTargetRootPageId(123);
        $configuration = new Configuration();
        $configuration->setSourceRootPageId(12);
        $subject->setup($configuration, $response);

        $user = new User('maxmuster|max mustermann|max@muster.tld');

        $update = [
            'username' => 'maxmuster',
            'email' => 'max@muster.tld',
            'realName' => 'max mustermann',
            'password' => 'hashedPassword',
            'tx_site_management_site' => 123,
            'tx_site_management_based_on' => 12
        ];

        $subject->expects($this->once())->method('updateRow')->with('be_users', $update, ['uid' => 1337]);

        $subject->_call('duplicateSingleUser', 12, $user);
    }

    /**
     * @test
     */
    public function titleIsReturned(): void
    {
        $subject = $this->getAccessibleMock(CreateUsers::class, ['dummy'], [], '', false);

        $this->assertNotEmpty($subject->getTitle());
    }

}