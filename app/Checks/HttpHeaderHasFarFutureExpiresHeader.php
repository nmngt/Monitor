<?php
namespace App\Checks;

use NGT\Monitor\Checks\ConfigurableCheckBase;
use NGT\Monitor\Checks\Exceptions\HeaderNotFoundException;

class HttpHeaderHasFarFutureExpiresHeader extends ConfigurableCheckBase {

    public $name = 'Far future "Expires" header';

    /**
     * Defaults to 7 days (7*24*60*60)
     *
     * @var integer
     */
    protected $threshold = 604800;
    protected $configurableField = ['threshold'];

    protected $errorMessage = 'The HTTP "Expires" header is NOT set to a far enough value';
    protected $successMessage = 'The HTTP "Expires" header is set to a far enough value';

    public function validate()
    {
        try {
            $this->returnValue = $this->getResponseHeader('Expires');

            return strtotime($this->returnValue[0]) >= time() + $this->threshold;
        }
        catch(HeaderNotFoundException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
        return false;
    }
}
