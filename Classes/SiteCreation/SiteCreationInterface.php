<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation;

interface SiteCreationInterface
{

    public function getTitle(): string;


    public function getResponse(): string;

}