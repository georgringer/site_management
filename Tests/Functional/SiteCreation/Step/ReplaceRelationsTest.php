<?php

namespace GeorgRinger\SiteManagement\Tests\Functionals\SiteCreation\Step;

/**
 * This file is part of the "tt_address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\Domain\Model\Dto\Response;
use GeorgRinger\SiteManagement\Domain\Model\Dto\User;
use GeorgRinger\SiteManagement\SiteCreation\Step\ReplaceRelations;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ReplaceRelationsTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = ['typo3conf/ext/site_management'];

    public function setUp()
    {
        parent::setUp();

        $this->importDataSet(__DIR__ . '/../../Fixtures/replaceRelations.xml');
    }

    /**
     * @test
     */
    public function doneWizardReturnsFalse()
    {
        $configuration = new Configuration();
        $configuration->setIdentifier('newIdentifier');
        $configuration->setLanguages([0, 1]);
        $configuration->setSourceRootPageId(1);

        $response = new Response();
        $response->setTargetRootPageId(191);

        $users = [
            3 => new User('maxMustermann|max mustermann|fo@bar.com')
        ];
        $response->setUsers($users);

        $subject = new ReplaceRelations();
        $subject->setup($configuration, $response);
        $subject->handle();
    }

}
