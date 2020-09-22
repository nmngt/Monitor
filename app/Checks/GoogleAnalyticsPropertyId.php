<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class GoogleAnalyticsPropertyId extends ConfigurableCheckBase
{
    public $name = 'Has & Match Google Analytics Property Id';

    protected $propertyId = "";

    protected $configurableField = ['propertyId'];

    protected $errorMessage = 'The Google Analytics Property Id could not be found on this page.';
    protected $successMessage = "Google Analytics PropertyId found";
    protected $returnValue = '';

    // old and working, but does not work at my regex checker
    //private $pattern = "/(UA-\\d{4,9}-\\d{1,3})/is";
    private $pattern = "/\\bUA-\\d{4,10}-\\d{1,4}\\b/is";

    public function validate()
    {
        if (false === $from_source = $this->hasPropertyId($this->client->getHtml())) {
            $this->errorMessage = "No Google Analytics PropertyId found.";

            return false;
        } else {
            $this->returnValue = $from_source;

            if ("SKIP_MATCHING_PROPERTY_IDS" == $this->propertyId) {
                return true;
            }

            if (false === $this->matchPropertyId($from_source, $this->propertyId)) {
                $this->errorMessage = "Google Analytics PropertyId does not match ({$from_source} != {$this->propertyId}).";

                return false;
            } else {
                $this->successMessage = "Google Analytics PropertyId matches: {$from_source} == {$this->propertyId}";
            }
        }
        return true;
    }

    private function hasPropertyId($page_source)
    {
        if ($c = preg_match_all($this->pattern, $page_source, $matches)) {
            return $matches[0][0];
        } else {
            return false;
        }
    }

    private function matchPropertyId($from_source, $from_config)
    {
        // dump([$from_source, $from_config]);die();
        return ($from_source == $from_config) ? true : false;
    }

    public function isPropertyId($str)
    {
        return (bool) preg_match($this->pattern, strval($str));
    }

    public function setValue($value, $valueName = false)
    {
        $this->propertyId = $value;
    }
}
