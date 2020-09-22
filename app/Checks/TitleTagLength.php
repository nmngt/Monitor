<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class TitleTagLength extends ConfigurableCheckBase
{
    public $name = 'Title tag length';

    protected $minlength = 10;

    protected $maxlength = 80;

    protected $configurableField = ['minlength', 'maxlength'];

    protected $errorMessage = 'The title tag on this page does not have the required length.';
    protected $successMessage = 'The title tag has the required length';
    protected $returnValue = '';

    public function validate()
    {
        $xml = $this->getResponseBodyAsXml($this->client->getHtml());

        $titleTagValue = $xml->head->title;

        if (empty($titleTagValue)) {
            $this->errorMessage = 'The title tag was not found.';
            return false;
        }

        $length = mb_strlen($titleTagValue, 'UTF-8');

        if ($length < $this->minlength) {
            $this->errorMessage = 'The title tag is too short.';
            return false;
        }

        if ($length > $this->maxlength) {
            $this->errorMessage = 'The title tag is too long.';
            return false;
        }

        // store result
        $this->returnValue = $length;

        return true;
    }
}
