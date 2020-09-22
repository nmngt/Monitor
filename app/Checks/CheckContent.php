<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

/**
 * @deprecated
 */
class CheckContent extends ConfigurableCheckBase
{
    public $name = 'Find string(s) on website';

    protected $configurableField = ['stringsToSearchFor'];

    protected $stringsToSearchFor = [];

    public function validate()
    {
        foreach ($this->stringsToSearchFor as $k => $value) {
            if (strstr(
                $this->client->getHtml(),
                $this->stringsToSearchFor
            ) === false) {
                $this->errorMessage = "Not Found: {$value}";
                return false;
            } else {
                $this->successMessage = "Found: {$value}";
            }
        }
        return $return;
    }

    public function setValue($value)
    {
        $this->stringsToSearchFor[] = $value;
    }
}
