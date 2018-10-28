<?php

namespace GeorgRinger\SiteManagement\Tests\Unit\Domain\Repository;

use GeorgRinger\SiteManagement\Domain\Repository\SiteManagementRepository;
use TYPO3\TestingFramework\Core\BaseTestCase;

class SiteManagementRepositoryTest extends BaseTestCase
{

    /**
     * @test
     */
    public function arrayKeyIsSet()
    {
        $subject = $this->getAccessibleMock(
            SiteManagementRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $in = [
            ['uid' => 123, 'title' => 'first'],
            ['uid' => 456, 'title' => '2nd'],
        ];
        $expected = [
            123 => ['uid' => 123, 'title' => 'first'],
            456 => ['uid' => 456, 'title' => '2nd'],
        ];
        $this->assertEquals($expected, $subject->_call('setUidAsKey', $in));
    }

}