<?php
namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class HttpRequestTime extends ConfigurableCheckBase
{
    public $name = "HTTP request time";

    protected $errorMessage = 'This resource took too long to download';

    public $max = 1000;

    protected $configurableField = ["max"];

    public function validate()
    {
        //
        // @TODO
        //
        // getTransferTime() doesnt exist.
        // $duration = $this->client->getTransferTime();

        if ($duration > $this->max) {
            $this->errorMessage = 'The resources took '.$duration.' ms to download. The max. allowed time is '.$this->max.'ms.';
            return false;
        }
        return true;
    }
}
