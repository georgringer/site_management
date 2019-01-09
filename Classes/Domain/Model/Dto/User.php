<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class User
{

    /** @var string */
    protected $username = '';

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $email = '';

    /** @var string */
    protected $password = '';

    /**
     * User constructor.
     *
     * @param string $input username|name|email
     */
    public function __construct(string $input)
    {
        $input = trim($input);

        if (empty($input)) {
            throw new \RuntimeException('Constructor is empty', 1540751157);
        }
        $split = GeneralUtility::trimExplode('|', $input, true);
        $this->username = $split[0];
        if (isset($split[1])) {
            $this->name = $split[1];
        }
        if (isset($split[2])) {
            if (!GeneralUtility::validEmail($split[2])) {
                throw new \RuntimeException(sprintf('Email address "%s" is invalid', $split[2]), 1540751158);
            }
            $this->email = $split[2];
        }
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

}