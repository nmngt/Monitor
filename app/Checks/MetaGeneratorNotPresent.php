<?php

namespace App\Checks;

class MetaGeneratorNotPresent extends HtmlTagNotPresent
{
    public $name = 'meta name="generator" not present';

    protected $errorMessage = 'The meta name="generator" tag must not be present!';

    protected $successMessage = 'The meta name="generator" tag must is not present';

    protected $xpath = '/html/head/meta[@name="generator"]/ @content';
}
