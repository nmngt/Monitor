<?php
namespace NGT\Monitor\Checks\Interfaces;

use NGT\Monitor\Client;

interface CheckInterface
{
    public function setClient(Client $client);

    public function validate();

    public function getName();

    public function getErrorMessage();
}
