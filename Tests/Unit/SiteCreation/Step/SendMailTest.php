<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\SendMail;
use Prophecy\Argument;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\TestingFramework\Core\BaseTestCase;

class SendMailTest extends BaseTestCase
{

    /**
     * @test
     */
    public function mailMessageIsInvoked()
    {
        $subject = $this->getAccessibleMock(SendMail::class, ['dummy'], [], '', false);

        $mailMessageProphecy = $this->prophesize(MailMessage::class);
        $mailMessageProphecy->addTo(Argument::cetera())->shouldBeCalled();
        $mailMessageProphecy->addFrom(Argument::cetera())->shouldBeCalled();
        $mailMessageProphecy->setSubject(Argument::cetera())->shouldBeCalled();
        $mailMessageProphecy->addPart(Argument::cetera())->shouldBeCalled();
        $mailMessageProphecy->send(Argument::cetera())->shouldBeCalled();

        $subject->_set('mailMessage', $mailMessageProphecy->reveal());

        $configuration = new Configuration();
        $response = new Response();
        $subject->handle($configuration, $response, []);
    }
}