<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SendMail extends AbstractStep implements SiteCreationInterface
{
    /** @var MailMessage */
    protected $mailMessage;

    public function __construct()
    {
        $this->mailMessage = GeneralUtility::makeInstance(MailMessage::class);
    }

    public function getTitle(): string
    {
        return 'Send mail';
    }

    public function handle(Configuration $configuration, Response $response, array $stepConfiguration = []): void
    {
        $this->mailMessage->addTo('georg.ringer@gmail.com');
        $this->mailMessage->addFrom('noreply@fo.com', 'Site Management');
        $this->mailMessage->setSubject('Site created');
        $this->mailMessage->addPart('Site has been created', 'text/plain');
        $this->mailMessage->send();
    }

}