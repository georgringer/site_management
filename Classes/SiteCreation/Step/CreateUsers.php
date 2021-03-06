<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateUsers extends AbstractStep
{

    /** @var DuplicateCommand */
    protected $duplicateCommand;

    /** @var PasswordHashInterface */
    protected $passwordHashInstance;

    /**
     * CreateUsers constructor.
     * @throws \TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException
     */
    public function __construct()
    {
        $this->duplicateCommand = GeneralUtility::makeInstance(DuplicateCommand::class);
        $this->passwordHashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('BE');
    }

    public function getTitle(): string
    {
        return 'Create users';
    }

    public function handle(array $stepConfiguration = []): void
    {
        $users = $this->configuration->getUsers();
        if (!empty($users)) {
            foreach ($users as $sourceRecordId => $userGroup) {
                foreach ($userGroup as $user) {

                    $this->duplicateSingleUser($sourceRecordId, $user);
                }
            }
            $this->response->setUsers($users);
        }
    }

    protected function duplicateSingleUser(int $sourceRecordId, User $user)
    {
        $newPassword = $this->generatePassword();
        $user->setPassword($newPassword);

        $newId = $this->duplicateCommand->duplicate('be_users', $sourceRecordId);

        $update = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'realName' => $user->getName(),
            'password' => $this->passwordHashInstance->getHashedPassword($newPassword),
            'tx_site_management_site' => $this->response->getTargetRootPageId(),
            'tx_site_management_based_on' => $sourceRecordId
        ];
        $this->updateRow('be_users', $update, ['uid' => $newId]);
    }

    protected function generatePassword(int $length = 12)
    {
        $password = '';
        $possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $i = 0;
        while ($i < $length) {
            $char = $possible[random_int(0, \strlen($possible) - 1)];
            if (false === strpos($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

}