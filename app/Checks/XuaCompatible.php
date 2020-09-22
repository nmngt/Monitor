<?php
namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

class XuaCompatible extends ConfigurableCheckBase
{
    public $name = "X-UA-Compatible";
    protected $expectedValue = 'IE=edge';
    protected $configurableField = ['expectedValue'];

    protected $errorMessage = 'Neither a "X-UA-Compatible" HTTP header nor a corresponding meta tag was found';
    protected $successMessage = 'Found a "X-UA-Compatible" HTTP header or a corresponding meta tag';
    protected $returnValue = '';

    public function validate()
    {
        try {
            $xuaValue = $this->getResponseHeader('X-UA-Compatible');
        }
        catch(HeaderNotFoundException $e) {

            $xuaValueRaw = $this->getDomElementFromBodyByXpath('//meta[@http-equiv="X-UA-Compatible"]/@content');

            if(empty($xuaValueRaw) || ! is_array($xuaValueRaw) || sizeof($xuaValueRaw) > 1) {
                return false;
            }

            if(! $this->checkPrecedingSiblings()) {
                $this->errorMessage = 'The only elements allowed to precede the X-UA-Compatible meta tag are meta-tags and the title-tag';
                return false;
            }

            $xuaValue = array_pop($xuaValueRaw);
        }

        if(is_array($xuaValue)) {
            $xuaValue = $xuaValue[0];
        }

        if($xuaValue != $this->expectedValue) {
            $this->errorMessage = 'The expected X-UA-Compatible directive\'s value was "' . $this->expectedValue . '", but found "' . $xuaValue . '"';
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function checkPrecedingSiblings()
    {
        $allowedTags = ['meta', 'title'];

        $precedingSiblings = $this->getDomElementFromBodyByXpath('//meta[@http-equiv="X-UA-Compatible"]/preceding-sibling::*');

        foreach($precedingSiblings as $precedingSibling)
        {
            if(! in_array($precedingSibling->getName(), $allowedTags)) {
                return false;
            }
        }
        return true;
    }
}
