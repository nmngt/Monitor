<?php
namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use NGT\Monitor\App;
use NGT\Monitor\Commands;
use NGT\Monitor\Contracts\Constants;

class ConfigCommand extends Commands
{
    protected function configure(): void
    {
        $this
            ->setName('check:config')
            ->setDescription('Run a full check.')
            ->addOption(
                'report',
                'r',
                InputOption::VALUE_NONE,
                'Add --report to send an email report after processing.'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->mainApplication->setIO($io);

        $sendReport = false !== $input->getOption('report') ? true : false;

        $run = $this->mainApplication->runConfigChecks($sendReport);
    }

    public function setMainApplication(App $mainApplication)
    {
        $this->mainApplication = $mainApplication;
    }
}
