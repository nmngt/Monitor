<?php

namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class OgPropertyPresent extends ConfigurableCheckBase
{
    public $name = 'OG property present';

    protected $requiredProperties = ['title', 'description', 'url', 'image'];

    protected $configurableField = ['requiredProperties'];

    protected $errorMessage = 'The required open graph property is not present.';
    protected $successMessage = 'The open graph property are all found';
    protected $returnValue = '';

    public function validate()
    {
        $xml = $this->getResponseBodyAsXml($this->client->getBody());
        return $this->checkRequiredProperties($xml);
    }

    /**
     * @param \SimpleXMLElement $body
     *
     * @return bool
     */
    private function checkRequiredProperties($body)
    {
        $this->returnValue = [];
        foreach ($this->requiredProperties as $propertyName) {
            $this->returnValue[$propertyName] = true;

            $propertyItemValue = $body->xpath('/html/head/meta[@property="og:'.$propertyName.'"]/ @content');

            if (! is_array($propertyItemValue) || empty($propertyItemValue)) {
                $this->returnValue[$propertyName] = false;
                $this->errorMessage = 'The open graph property "'.$propertyName.'" was not found on the site.';

                return false;
            }

            $length = mb_strlen($propertyItemValue[0]['content'], 'UTF-8');

            if ($length == 0) {
                $this->returnValue[$propertyName] = false;
                $this->errorMessage = 'The open graph property "'.$propertyName.'" was found but is empty.';

                return false;
            }
        }

        return true;
    }
}
