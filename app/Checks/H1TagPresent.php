<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class H1TagPresent extends ConfigurableCheckBase
{
    public $name = 'Tag <H1> present';

    protected $allowMultipleTags = true;

    protected $successMessage = "This page contain an H1 tag";
    protected $returnValue = '';

    public function validate()
    {
        $xml    = $this->getResponseBodyAsXml($this->client->getHtml());
        $amount = count($xml->xpath('//h1'));

        if (!$this->allowMultipleTags && $amount > 1) {
            $this->errorMessage = 'More than one H1 tag exists on this page.';
            return false;
        }

        switch ($amount) {
            case 0:
                $this->errorMessage = 'This page does not contain an H1 tag.';
                return false;
                break;
            case 1:
                $this->returnValue = 1;
                return true;
                break;
            case ($amount>1):
                $this->returnValue = $amount;
                $this->successMessage = "This page contain multiple H1 tags";
                return true;
                break;
        }
    }

    public function allowMultipleTags($allowMultipleTags)
    {
        $this->allowMultipleTags = $allowMultipleTags;
    }
}
