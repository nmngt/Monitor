<?php

namespace App\Checks;

use Spatie\SslCertificate\SslCertificate;
use NGT\Monitor\Checks\ConfigurableCheckBase;

class SslEndOfLife extends ConfigurableCheckBase
{
    public $name = 'Check SSL certificate EndOfLife';

    protected $expireWarning = 30; //days

    protected $configurableField = ['expireWarning'];

    protected $errorMessage = 'The SSL certificate is not valid';
    protected $returnValue = "";

    public function validate()
    {
        $certificate = $this->client->getSSLCert();

        if ($certificate->isValid()) {
            $this->returnValue = $diffInDays = $certificate->expirationDate()->diffInDays();

            if ($this->expireWarning >= $diffInDays) {
                $this->successMessage = "(ATTENTION!) The SSL certificate expires in <= ".$diffInDays." days";
                return true;
            }

            $this->successMessage = "The SSL certificate expires in ".$diffInDays." days";
            return true;
        } else {
            $this->errorMessage = "The SSL certificate is not valid";
            return false;
        }
    }

    public function setValue($value, $valueName = false)
    {
        $this->expireWarning = $value;
    }
}
