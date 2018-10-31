<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\Domain\Model\Dto;

class Configuration
{

    /** @var int */
    protected $sourceRootPageId = 0;

    /** @var string */
    protected $identifier = '';

    /** @var array */
    protected $languages = [];

    /** @var string */
    protected $googleTagManager = '';

    /** @var string domain */
    protected $domain = '';

    /** @var User[] */
    protected $users = [];

    /** @var array */
    protected $additionalInformation = [];

    /**
     * @return int
     */
    public function getSourceRootPageId(): int
    {
        return $this->sourceRootPageId;
    }

    /**
     * @param int $sourceRootPageId
     */
    public function setSourceRootPageId(int $sourceRootPageId): void
    {
        $this->sourceRootPageId = $sourceRootPageId;
    }


    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param array $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getGoogleTagManager(): string
    {
        return $this->googleTagManager;
    }

    /**
     * @param string $googleTagManager
     */
    public function setGoogleTagManager(string $googleTagManager): void
    {
        $this->googleTagManager = $googleTagManager;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function getAdditionalInformation(): array
    {
        return $this->additionalInformation;
    }

    /**
     * @param array $additionalInformation
     */
    public function setAdditionalInformation(array $additionalInformation): void
    {
        $this->additionalInformation = $additionalInformation;
    }

}