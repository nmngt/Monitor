<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class MetaDescriptionLength extends ConfigurableCheckBase
{
    public $name = 'Tag <meta name="description"> length';

    protected $minlength = 70;

    protected $maxlength = 160;

    protected $configurableField = ['minlength', 'maxlength'];

    protected $errorMessage = 'The meta description on this page does not have the required length.';

    protected $successMessage = 'The META description has the required length';

    protected $returnValue = '';

    public function validate()
    {
        $metaDescriptionValue = $this->getDomElementFromBodyByXpath('/html/head/meta[@name="description"]/ @content');

        if (!is_array($metaDescriptionValue) || empty($metaDescriptionValue)) {
            $this->errorMessage = 'No meta description found';
            return false;
        }

        $length = mb_strlen($metaDescriptionValue[0]['content'], 'UTF-8');

        if ($length < $this->minlength) {
            $this->errorMessage = 'The meta description is too short.';
            return false;
        }

        if ($length > $this->maxlength) {
            $this->errorMessage = 'The meta description is too long.';
            return false;
        }

        $this->returnValue = $length;

        return true;
    }
}
