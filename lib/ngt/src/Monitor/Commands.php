<?php
namespace NGT\Monitor;

use NGT\Monitor\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Commands extends Command
{
    protected $logger = null;
    private $mainApplication;

    public function __construct()
    {
        parent::__construct();
    }

    public function setMainApplication(App $mainApplication)
    {
        $this->mainApplication = $mainApplication;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}
