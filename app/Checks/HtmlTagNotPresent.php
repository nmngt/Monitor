<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class HtmlTagNotPresent extends ConfigurableCheckBase
{
	//
	// This Check should not be called directly because it
	// then returns false!
	//
	// This Checks purpose is to be extended by other checks, e.g.
	//
	// - Check if <body> tag is available
	// - check if <header> tag is available
	// - check if <h1> tag is available
	//
    public $name = '(Given) HTML-tag not present';

    protected $xpath = 'body';

    protected $configurableField = ['xpath'];

    public function validate()
    {
        $result = $this->getDomElementFromBodyByXpath($this->xpath);

        return empty($result);
    }
}
