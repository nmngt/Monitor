<?php
namespace App\Checks;

use NGT\Monitor\Checks\CheckBase;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

class HttpHeaderResourceIsGzipped extends CheckBase
{
    public $name = 'HTTP header resource is flagged "gzipped"';

    protected $errorMessage = 'The "Content-Encoding" HTTP header was NOT found';
    protected $successMessage = 'The "Content-Encoding" HTTP header was found';
    protected $returnValue = '';

    public function validate()
    {
        try {
            $encoding = $this->getResponseHeader();

            if(isset($encoding['Accept-Encoding']))
            {
                // d($encoding['Accept-Encoding']);
                $this->returnValue = $encoding['Accept-Encoding'];

                switch ($encoding['Accept-Encoding'])
                {
                    case 'gzip, deflate':
                    case 'gzip':
                        return true;
                        break;
                    default:
                        return false;
                        break;
                }
            }
        } catch(HeaderNotFoundException $e) {
            $this->errorMessage = 'The "Content-Encoding" HTTP header was not found';
        }
        return false;
    }
}
