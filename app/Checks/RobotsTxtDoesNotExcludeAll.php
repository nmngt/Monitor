<?php
namespace App\Checks;

use NGT\Monitor\Checks\CheckBase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class RobotsTxtDoesNotExcludeAll extends CheckBase
{
    public $name = 'robots.txt does not exclude all';

	private $pattern = "~User-Agent:\s*\*\s+Disallow:\s*/\s*$~";

    protected $configurableField = ['pattern'];

    protected $errorMessage = 'The robots.txt file does EXCLUDE all crawler.';
    protected $successMessage = 'The robots.txt file does not exclude all crawler.';
    protected $returnValue = '';

    public function validate()
    {
        $url = $this->client->getUrl();
        $this->returnValue = $this->requestRobotsTxt($url);

        return preg_match($this->pattern, $this->returnValue) === 0;
    }

    protected function requestRobotsTxt(string $url, $path = "/robots.txt", $method = "GET", $protocol = "https")
    {
        try {
            $client = new GuzzleClient([
                'base_uri' => $protocol.'://'.$url,
                'timeout'  => 2.0,
            ]);

            $response = $client->request($method, $path);
            return $response->getBody()->getContents();

        } catch (RequestException $e) {
            $this->errorMessage = 'Could not request robots.txt from '.$url;
			return false;

        } catch (GuzzleClientException $e) {
            $this->errorMessage = 'Could not get robots.txt from '.$url;
			return false;
        }
    }
}
