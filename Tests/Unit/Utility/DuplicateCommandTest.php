<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Utility;

use GeorgRinger\SiteManagement\Utility\DuplicateCommand;
use Prophecy\Argument;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DuplicateCommandTest extends UnitTestCase
{

    /**
     * @test
     */
    public function dataHandlerIsProperlyCalled(): void
    {

        $configuration = [
            'aTable' => [
                123 => [
                    'copy' => 4
                ]
            ]
        ];
        $dateHandlerProphecy = $this->prophesize(DataHandler::class);
        $dateHandlerProphecy->start([], $configuration)->shouldBeCalled();
        $dateHandlerProphecy->process_cmdmap(Argument::cetera())->shouldBeCalled();
        $dateHandlerProphecy->copyMappingArray = [
            'aTable' => [
                123 => 10
            ]];

        $subject = $this->getAccessibleMock(DuplicateCommand::class, ['dummy'], [], '', false);
        $subject->_set('dataHandler', $dateHandlerProphecy->reveal());


        $response = $subject->duplicate('aTable', 123, 4);
        $this->equalTo(10, $response);
    }
}