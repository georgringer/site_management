<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateUsergroups extends AbstractStep implements SiteCreationInterface
{

    /** @var DuplicateCommand */
    protected $duplicateCommand;

    /** @var PasswordHashInterface */
    protected $passwordHashInstance;

    public function __construct()
    {
        $this->duplicateCommand = GeneralUtility::makeInstance(DuplicateCommand::class);
        $this->passwordHashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('BE');
    }

    public function getTitle(): string
    {
        return 'Create users';
    }

    public function isValid(): bool
    {
        return true;
    }

    public function handle(Configuration $configuration): void
    {
        $users = $configuration->getUsers();
        if (!empty($users)) {
            foreach ($users as $sourceRecordId => $userGroup) {
                foreach ($userGroup as $user) {

                    $this->duplicateSingleUser($sourceRecordId, $user);
                }
            }
        }
    }

    protected function duplicateSingleUser(int $templateRecordId, User $user)
    {
        $newPassword = $this->generatePassword();
        $user->setPassword($newPassword);
        $newId = $this->duplicateCommand->duplicate('be_users', $templateRecordId);
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_users');

        $connection->update(
            'be_users',
            [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'realName' => $user->getName(),
                'password' => $this->passwordHashInstance->getHashedPassword($newPassword),
                'tx_site_management_site' => 0
            ],
            [
                'uid' => $newId
            ]
        );
    }

    protected function generatePassword(int $length = 12)
    {
        $password = '';
        $possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

}