<?php
declare(strict_types=1);

namespace GeorgRinger\SiteManagement\SiteCreation\Step;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\View\TemplateView;

class SendMail extends AbstractStep
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

    public function handle(array $stepConfiguration = []): void
    {
        $view = new TemplateView();
        $view->getTemplatePaths()->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($stepConfiguration['plainContent']));
        $view->assignMultiple([
            'configuration' => $this->configuration,
            'response' => $this->response,
            'stepConfiguration' => $stepConfiguration
        ]);

        $this->mailMessage->addTo($stepConfiguration['to']);
        $this->mailMessage->addFrom($stepConfiguration['fromEmail'], $stepConfiguration['fromName']);
        $this->mailMessage->setSubject($stepConfiguration['subject']);
        $this->mailMessage->addPart($view->render(), 'text/plain');
        $this->mailMessage->send();
    }

}