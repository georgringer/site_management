<?php

namespace GeorgRinger\SiteManagement\Domain\Model\Dto;

class Response
{

    /** @var int */
    protected $targetRootPageId = 0;

    /** @var array */
    protected $sysFileMounts = [];

    /** @var array */
    protected $users = [];

    /** @var array */
    protected $usergroups = [];

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
    public function getSysFileMounts(): array
    {
        return $this->sysFileMounts;
    }

    /**
     * @param array $sysFileMounts
     */
    public function setSysFileMounts(array $sysFileMounts): void
    {
        $this->sysFileMounts = $sysFileMounts;
    }

    /**
     * @return array
     */
    public function getUsergroups(): array
    {
        return $this->usergroups;
    }

    /**
     * @param array $usergroups
     */
    public function setUsergroups(array $usergroups): void
    {
        $this->usergroups = $usergroups;
    }


}