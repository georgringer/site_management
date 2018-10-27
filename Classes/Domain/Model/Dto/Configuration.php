<?php

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

    /** @var int */
    protected $targetRootPageId = 0;

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
     * @return int
     */
    public function getTargetRootPageId(): int
    {
        return $this->targetRootPageId;
    }

    /**
     * @param int $targetRootPageId
     */
    public function setTargetRootPageId(int $targetRootPageId): void
    {
        $this->targetRootPageId = $targetRootPageId;
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

}