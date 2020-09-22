<?php

namespace App\Checks;

use Techie\Google\GoogleSearch;
use NGT\Monitor\Checks\ConfigurableCheckBase;

class GoogleRanking extends ConfigurableCheckBase
{
    public $name = 'Has Google Ranking';

    protected $keyword = "";
    protected $location = "";
    protected $language = "";
    protected $device = "";

    protected $configurableField = ['keyword', 'location', 'device', 'language'];

    protected $errorMessage = 'The url with given keyword could not be found on page 1 of the Google SERPs.';
    protected $successMessage = "Hit";
    protected $returnValue = '';

    public function validate()
    {
        die('NOT_YET_DONE');

        $google = new GoogleSearch();
        $google->dataDir = $monitor->storagePath(__DIR__.'/../../storage/data/'); // must end with a slash (/)!
        $locations = $google->searchLocations("Saarland");
        $google->setLocation($locations[0]);
        $google->setDevice('mobile');
        $google->setQuery("Werbeagentur");

        $results = $google->next();
        d(json_encode($results, JSON_PRETTY_PRINT), true);


        // $domain = "codecanyon.net";
        // $max_pages = 5;

        // // create the search object
        // $google = new GoogleSearch();

        // // set a hardcoded location (skip searching)
        // $google->setLocation(new Location(
//     1013469,
//     "Queen Creek",
//     "Queen Creek,Arizona,United States"
        // ));

        // // set the search query
        // $google->setQuery("php scripts");

        // $rank = 0;
        // $page = 1;
        // $i = 0;

        // for ($x = 1; $x <= $max_pages; $x++) {
//     $res = $google->next();

//     while ($result = $res->fetch_object()) {
//         $i++;
//         if (stripos($result->domain, $domain) !== false) {
//             $page = $x;
//             $rank = $i;
//             break;
//         }
//     }

//     if ($rank > 0) break;
        // }

        // if ($rank == 0) echo "{$domain} was not found in the first {$max_pages} pages.";
        // else echo "{$domain} was rank #{$rank} and was visible on page {$page}!";


        if (false === $from_source = $this->hasPropertyId($this->client->getHtml())) {
            $this->errorMessage = "No Google Analytics PropertyId found.";

            return false;
        } else {
            $this->successMessage = "Found Google Analytics PropertyId: {$from_source}";

            if ("SKIP_MATCHING_PROPERTY_IDS" == $this->propertyId) {
                return true;
            }

            if (false === $this->matchPropertyId($from_source, $this->propertyId)) {
                $this->errorMessage = "Google Analytics PropertyId does not match ({$from_source} != {$this->propertyId}).";

                return false;
            } else {
                $this->successMessage = "Google Analytics PropertyId matchs: {$from_source} == {$this->propertyId}";
            }
        }
        return true;
    }

    public function setValue(array $values)
    {
        if (isset($values['keyword'])) {
            $this->keyword = $values['keyword'];
        }

        if (isset($values['location'])) {
            $this->location = $values['location'];
        }

        if (isset($values['device'])) {
            $this->device = $values['device'];
        }

        if (isset($values['language'])) {
            $this->propertyId = $values['language'];
        }
    }
}
