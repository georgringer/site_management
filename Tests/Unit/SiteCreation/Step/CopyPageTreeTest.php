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
     * @var bool Reset singletons created by subject
     */
    protected $resetSingletonInstances = true;

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
        $configuration->setSourceRootPageId(123);
        $response = new Response();
        $subject->setup($configuration, $response);
        $subject->handle([]);

        $this->assertEquals(1337, $response->getTargetRootPageId());
    }

    /**
     * @test
     */
    public function exceptionIsThrownForMissingSourceUid()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1541019057);

        $subject = $this->getAccessibleMock(CopyPageTree::class, ['duplicate'], [], '', false);
        $configuration = new Configuration();
        $response = new Response();
        $subject->setup($configuration, $response);
        $subject->handle();
    }

    /**
     * @test
     */
    public function titleIsReturned(): void
    {
        $subject = $this->getAccessibleMock(CopyPageTree::class, ['duplicate'], [], '', false);

        $this->assertNotEmpty($subject->getTitle());
    }

}