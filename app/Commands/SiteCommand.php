<?php
namespace App\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use NGT\Monitor\App;
use NGT\Monitor\Commands;

class SiteCommand extends Commands
{
    public function configure()
    {
        $this
            ->setName('check:site')
            ->setDescription('Run the default checks on the given url.')
            ->setHelp('This command checks some defaults on the given url.')
            ->addArgument('url', InputArgument::REQUIRED, 'The url to run checks on.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->mainApplication->setIO($io);

        $url = $input->getArgument('url');

        $run = $this->mainApplication->runSiteChecks($url);
    }

    public function setMainApplication(App $mainApplication)
    {
        $this->mainApplication = $mainApplication;
    }
}
