<?php
namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;

class HttpHeaderStatusCode extends ConfigurableCheckBase
{
    public $name = "Http header: Status code";

    protected $errorMessage = 'The resources HTTP status code was unexpected.';

    protected $value = 200;

    protected $configurableField = ['value'];

    protected $successMessage = 'The resources HTTP status code is 200';

    public function validate()
    {
        $resultCode = $this->client->getStatusCode();

        if ($resultCode != $this->value) {
            $this->returnValue = $resultCode;
            $this->errorMessage = 'The resources HTTP status code is "'.$resultCode.'" not "'.$this->value.'"';
            return false;
        }
        $this->returnValue = $resultCode;

        return true;
    }

    /**
     * @param int $value
     */
    public function setValue($value, $valueName = false)
    {
        $this->value = $value;
    }
}
