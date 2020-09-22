<?php

namespace App\Checks;

use Spatie\Dns\Dns;
use NGT\Support\Str;
use NGT\Monitor\Checks\ConfigurableCheckBase;

//
// https://github.com/spatie/dns
//
// The class can get these record types:
//   A, AAAA, CNAME, NS, SOA, MX, SRV, TXT,
//   DNSKEY, CAA, NAPTR.
//
class CheckDnsRecord extends ConfigurableCheckBase
{
    public $name = 'Check if specified DNS records exists';

    protected $record = 'A';

    protected $configurableField = ['record'];

    protected $errorMessage = 'The specified DNS record could not be found.';
    protected $successMessage = "DNS record found";
    protected $returnValue = '';

    /**
     * pattern matchs an record
     *
     * @var string
     */
    private $patterns = [
        'A' => "/\\d{1,3}.\\d{1,3}.\\d{1,3}.\\d{1,3}/m",
        'MX' => "/ (?:([a-zA-Z0-9-_]+)\\.)+/m",
    ];

    /**
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public function validate()
    {
        $dns = $this->client->getDns();
        $result = $this->getRecord($this->record, $this->patterns[$this->record], $dns);

        if (! $result) {
            $this->errorMessage = "The specified DNS {$this->record} record could not be found";
            return false;
        } else {
            $result = is_array($result) ? implode('|', $result) : $result;

            $this->successMessage = "DNS {$this->record} record found";
            $this->returnValue = Str::truncate($result, 40);// truncate $result if it has more than 40 chars

            return true;
        }
    }

    /**
     * [getRecord description]
     * @param  string          $record  [description]
     * @param  string          $pattern [description]
     * @param  \Spatie\Dns\Dns $dns     [description]
     * @return [type]                   [description]
     */
    private function getRecord(string $record, string $pattern, Dns $dns)
    {
        if ("" == $result = $dns->getRecords($record)) {
            return false;
        } else {
            if ($c = preg_match_all($pattern, $result, $matches)) {
                // trim whitespace from results before return
                return array_map('trim', $matches[0]);
            } else {
                return false;
            }
        }
    }

    /**
     * [setValue description]
     * @param [type] $value [description]
     */
    public function setValue($value, $valueName = false)
    {
        $this->record = $value;
    }
}
