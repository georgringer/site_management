<?php
declare(strict_types = 1);
namespace GeorgRinger\SiteManagement\Command;


use GeorgRinger\SiteManagement\Domain\Model\Dto\Configuration;
use GeorgRinger\SiteManagement\SiteCreation\SiteCreationHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;

/**
 * Command for activating an existing extension via CLI.
 */
class SiteCreationCommand extends Command
{
    /**
     * Defines the allowed options for this command
     */
    protected function configure()
    {
        $this
            ->setDescription('Create site')
            ->setHelp('...');
    }

    /**
     * Installs an extension
     *
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

//        if (!$this->isValid($request)) {
//            die('invalid');
//        }

//        $this->initialize();
        $configuration = GeneralUtility::makeInstance(Configuration::class);
        $configuration->setDomain('a.vm');
        $configuration->setIdentifier('toha-vm');
        $configuration->setLanguages(['0', '2']);
        $configuration->setSourceRootPageId(1);

        $siteCreation = GeneralUtility::makeInstance(SiteCreationHandler::class, $configuration);
        $response = $siteCreation->handle();

print_R($response);
//        return $response;

        $io->success('Site created');
    }
}
