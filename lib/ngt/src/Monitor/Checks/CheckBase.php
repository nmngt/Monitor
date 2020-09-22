<?php
namespace NGT\Monitor\Checks;

use NGT\Monitor\Client;
use NGT\Monitor\Checks\Interfaces\CheckInterface;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

abstract class CheckBase implements CheckInterface
{
    public $name = '';
    protected $client = null;
    protected $errorMessage = 'Failed!';
    protected $successMessage = 'Success.';
    protected $returnValue = '';

    public function __construct(Client $client = null)
    {
        if (empty($this->name)) {
            $this->name = get_called_class();
        }

        if (isset($client)) {
            $this->setClient($client);
        }
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function getClassName()
    {
        return get_called_class();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    public function getReturnValue()
    {
        return $this->returnValue;
    }

    protected function getResponseBodyAsXml($responseBody)
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->strictErrorChecking = false;
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$responseBody);

        return simplexml_import_dom($doc);
    }

    protected function getDomElementFromBodyByXpath($xpath)
    {
        // get html from Client
        $html = $this->client->getHtml();

        if (empty($html)) {
            return '';
        }

        // convert to xml
        $xml = $this->getResponseBodyAsXml($html);

        return $xml->xpath($xpath);
    }

    protected function getResponseHeader(string $headerName = null, $returnFirstValue = false)
    {
        if(is_null($headerName)) {
            return $this->client->getHeaders();
        }
        else {
            $headers = $this->client->getHeaders();

            if(isset($headers[$headerName])) {
                return $returnFirstValue ? $headers[$headerName][0] : $headers[$headerName];
            }
            else
                throw new HeaderNotFoundException('The HTTP header "' . $headerName. '" is missing.');
        }
    }

    public function setValue($value, $valueName = false)
    {
        if($valueName) {
            $this->{$valueName} = $value;
        }
        else {
            $this->value = $value;
        }
    }
}
