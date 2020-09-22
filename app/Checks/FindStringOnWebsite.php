<?php
namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class FindStringOnWebsite extends ConfigurableCheckBase
{
    public $name = 'Find string on website';

    protected $configurableField = ['stringToSearchFor'];

    protected $stringToSearchFor = '';

    protected $errorMessage = 'The given string could NOT be found';
    protected $successMessage = 'The given string is found';
    protected $returnValue = '';

    public function validate()
    {
        if(empty($this->stringToSearchFor)) {
            throw new InsufficientConfigurationException('"stringToSearchFor" not set for ' . $this->getName());
        }

    	$this->returnValue = $this->stringToSearchFor;

        if(strstr($this->client->getHtml(), $this->stringToSearchFor) !== false)
        {
        	$this->successMessage = 'The string "'.$this->stringToSearchFor.'" is found';
        	return true;
        }
        else {
        	$this->errorMessage = 'The string "'.$this->stringToSearchFor.'" is NOT found';
        	return false;
        }
    }

    public function setValue($value, $valueName = false)
    {
        $this->stringToSearchFor = $value;
    }
}
