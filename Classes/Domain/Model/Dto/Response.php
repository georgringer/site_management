<?php

namespace GeorgRinger\SiteManagement\Domain\Model\Dto;

class Response
{

    /** @var int */
    protected $targetRootPageId = 0;

    /** @var User[] */
    protected $users = [];

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



}