<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\SiteCreation\Step;

use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\SiteCreation\Step\ReplaceRelations;
use TYPO3\TestingFramework\Core\BaseTestCase;

class ReplaceRelationsTest extends BaseTestCase
{


    /**
     * @test
     */
    public function titleIsReturned(): void
    {
        $subject = $this->getAccessibleMock(ReplaceRelations::class, ['dummy'], [], '', false);

        $this->assertNotEmpty($subject->getTitle());
    }

    /**
     * @test
     */
    public function dbMountIsReplaced()
    {
        $subject = $this->getAccessibleMock(ReplaceRelations::class, ['dummy'], [], '', false);

        $configuration = new Configuration();
        $configuration->setSourceRootPageId(983);
        $response = new Response();
        $response->setTargetRootPageId('456');
        $subject->setup($configuration, $response);
        $this->assertEquals('123,456,789', $subject->_call('replaceDbMountPoint', '123,983,789'));
    }

    /**
     * @param string $list
     * @param int $idToSearch
     * @param int $idToReplace
     * @param string $expected
     * @test
     * @dataProvider valueInListIsReplacedProvider
     */
    public function valueInListIsReplaced($list, int $idToSearch, int $idToReplace, $expected): void
    {
        $subject = $this->getAccessibleMock(ReplaceRelations::class, ['dummy'], [], '', false);
        $this->assertEquals($expected, $subject->_call('replaceValueInList', $list, $idToSearch, $idToReplace));
    }

    public function valueInListIsReplacedProvider(): array
    {
        return [
            'empty list' => ['', 123, 456, ''],
            'list with value on start' => ['123,456,7,8', 123, 12, '12,456,7,8'],
            'list with value in middle' => ['0,123,456,7,8', 7, 10, '0,123,456,10,8'],
            'list with value in end' => ['0,123,456,7,8', 8, 10, '0,123,456,7,10'],
        ];
    }

}