<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SendMail extends AbstractStep implements SiteCreationInterface
{
    public function getTitle(): string
    {
        return 'Send mail';
    }

    public function isValid(): bool
    {
        return true;
    }

    public function handle(Configuration $configuration): void
    {
        $mailMessage = GeneralUtility::makeInstance(MailMessage::class);
        $mailMessage
            ->addTo('georg.ringer@gmail.com')
            ->addFrom('noreply@fo.com', 'Site Management')
            ->setSubject('Site created')
            ->addPart('Site has been created', 'text/plain')
            ->send();
    }


}