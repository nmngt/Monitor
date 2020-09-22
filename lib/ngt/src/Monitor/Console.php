<?php

namespace NGT\Monitor;

use NGT\Monitor\Contracts\Constants;
use Symfony\Component\Console\Application;

class Console extends Application
{
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct(Constants::PROPAGANDA, Constants::VERSION);
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Norman</comment>';
    }
}
