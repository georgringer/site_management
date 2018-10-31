<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\CopyPageTree;
use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use Prophecy\Argument;
use TYPO3\TestingFramework\Core\BaseTestCase;

class CopyPageTreeTest extends BaseTestCase
{

    /**
     * @test
     */
    public function dataHandlerIsInvoked()
    {
        $subject = $this->getAccessibleMock(CopyPageTree::class, ['duplicate'], [], '', false);

        $dateHandlerProphecy = $this->prophesize(DuplicateCommand::class);
        $dateHandlerProphecy->duplicate(Argument::cetera())->shouldBeCalled()->willReturn(1337);

        $subject->_set('duplicateCommandService', $dateHandlerProphecy->reveal());

        $configuration = new Configuration();
        $response = new Response();
        $subject->handle($configuration, $response, []);

        $this->assertEquals(1337, $response->getTargetRootPageId());
    }
}