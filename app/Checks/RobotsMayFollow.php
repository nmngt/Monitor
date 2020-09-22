<?php
namespace App\Checks;

use Spatie\Robots\Robots;
use NGT\Monitor\Checks\ConfigurableCheckBase;

class RobotsMayFollow extends ConfigurableCheckBase
{
    public $name = 'May Robots follow this site?';

    protected $configurableField = ['url'];

    protected $url;

    protected $returnValue = 1;

    public function validate()
    {
        $robots = Robots::create();

        $url = !empty($this->url) ? $this->url : $this->client->getUrl();

        if (false === $robots->mayFollowOn("https://".$url)) {
            $this->returnValue = 0;
            $this->errorMessage = "Robots may not follow this site: https://{$url}";
            return false;
        } else {
            $this->successMessage = "Robots may follow this site: https://{$url}";
            return true;
        }
    }
}
