<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\SendMail;
use Prophecy\Argument;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\BaseTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

class SendMailTest extends BaseTestCase
{

    /**
     * @test
     */
    public function mailMessageIsInvoked()
    {
        $subject = $this->getAccessibleMock(SendMail::class, ['dummy'], [], '', false);

        $stepConfiguration = [
            'to' => 'owner@youremail.com',
            'fromName' => 'Website',
            'fromEmail' => 'noreply@website.com',
            'subject' => 'New site has been created',
            'plainContent' => 'EXT:site_management/Tests/Fixtures/Templates/Email/SendMail.txt',
        ];

        $configuration = new Configuration();
        $configuration->setIdentifier('newSite');
        $configuration->setDomain('newdomain.local');
        $response = new Response();

        $view = new TemplateView();
        $view->getTemplatePaths()->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($stepConfiguration['plainContent']));
        $view->assignMultiple([
            'configuration' => $configuration,
            'response' => $response,
            'stepConfiguration' => $stepConfiguration
        ]);
        $expectedContent = 'Identifier:newSite';

        $mailMessageProphecy = $this->prophesize(MailMessage::class);
        $mailMessageProphecy->addTo($stepConfiguration['to'])->shouldBeCalled();
        $mailMessageProphecy->addFrom($stepConfiguration['fromEmail'], $stepConfiguration['fromName'])->shouldBeCalled();
        $mailMessageProphecy->setSubject($stepConfiguration['subject'])->shouldBeCalled();
        $mailMessageProphecy->addPart($expectedContent, 'text/plain')->shouldBeCalled();
        $mailMessageProphecy->send(Argument::cetera())->shouldBeCalled();

        $subject->_set('mailMessage', $mailMessageProphecy->reveal());

        $subject->handle($configuration, $response, $stepConfiguration);
    }
}