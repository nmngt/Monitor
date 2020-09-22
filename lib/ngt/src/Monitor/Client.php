<?php

namespace NGT\Monitor;

use Spatie\Dns\Dns;
use GuzzleHttp\Client as GuzzleClient;
use Spatie\SslCertificate\SslCertificate;

use InvalidArgumentException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use NGT\Monitor\Exceptions\ClientResponseException;
use NGT\Monitor\Exceptions\ClientSslResponseException;
use NGT\Monitor\Exceptions\ClientDnsResponseException;

class Client
{
    protected $url;
    protected $httpHeaders;
    protected $httpResponse;
    private $sslResponse;
    private $dnsResponse;
    private $htmlResponse = null;

    public function __construct(string $url)
    {
        $this->url = $url;

        $this->requestHttp($this->url);
        $this->requestSslCert($this->url);
        $this->requestDns($this->url);

        return $this;
    }

    protected function requestHttp(string $url, $path = "/", $method = "GET", $protocol = "https")
    {
        try {
            // get website
            $client = new GuzzleClient([
                'base_uri' => $protocol.'://'.$url,
                'timeout'  => 2.0,
                'User-Agent' => 'ngt-moni/1.0',
            ]);

            // get response
            $this->httpResponse = $client->request($method, $path);

            // extract headers
            $this->httpHeaders = $this->httpResponse->getHeaders();

            // extract html and base64encode it
            $this->htmlResponse = base64_encode($this->httpResponse->getBody()->getContents());
        } catch (RequestException $e) {

            // if ($e->hasResponse()) {
            //     $response = $e->getResponse();
            //     var_dump($response->getStatusCode()); // HTTP status code
            //     var_dump($response->getReasonPhrase()); // Message
            //     var_dump((string) $response->getBody()); // Body
            //     var_dump($response->getHeaders()); // Headers array
            //     var_dump($response->hasHeader('Content-Type')); // Is the header presented?
            //     var_dump($response->getHeader('Content-Type')[0]); // Concrete header value
            // }
            // d([$client, $e], true);

            throw new ClientResponseException("Error while requesting HTTP connection from {$url}.");
            // echo Psr7\str($e->getRequest());
        } catch (GuzzleClientException $e) {
            throw new ClientResponseException("Could not connect to {$url}{$path} via {$method}.");
            // echo Psr7\str($e->getResponse());
        }

        //
        // @TODO: We need transfer stats... How long does a request take?
        //

        // use GuzzleHttp\TransferStats;
        // $client = new GuzzleHttp\Client();
        // $client->request('GET', 'http://httpbin.org/stream/1024', [
        //     'on_stats' => function (TransferStats $stats) {
        //         echo $stats->getEffectiveUri() . "\n";
        //         echo $stats->getTransferTime() . "\n";
        //         var_dump($stats->getHandlerStats());
        //         // check if a response was received before using the
        //         // response object.
        //         if ($stats->hasResponse()) {
        //             echo $stats->getResponse()->getStatusCode();
        //         } else {
        //             var_dump($stats->getHandlerErrorData());
        //         }
        //     }
        // ]);
    }

    protected function requestSslCert(string $domain, $serialized = false)
    {
        try {
            $this->sslResponse   = $serialized
                ? serialize(SslCertificate::createForHostName($domain))
                : SslCertificate::createForHostName($domain);
        } catch (\Exception $e) {
            throw new ClientSslResponseException("Could not get a SSL certificate from {$domain}.");
        }
    }

    protected function requestDns(string $domain, $serialized = false)
    {
        try {
            $dns = new Dns($domain);
            $this->dnsResponse =$serialized ? serialize($dns) : $dns;
        } catch (\Exception $e) {
            throw new ClientDnsResponseException("Could not get DNS for {$domain}");
        }
    }

    public function response()
    {
        return $this->httpResponse;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getHeaders()
    {
        return $this->response()->getHeaders();
    }

    public function getStatusCode()
    {
        return $this->response()->getStatusCode();
    }

    public function getBody()
    {
        return $this->response()->getBody();
    }

    public function getContents()
    {
        return $this->response()->getBody()->getContents();
    }

    public function getHtml()
    {
        return base64_decode($this->htmlResponse);
    }

    public function getSslCert($serialized = false)
    {
        return $serialized ? unserialize($this->sslResponse) : $this->sslResponse;
    }

    public function getDns($serialized = false)
    {
        return $serialized ? unserialize($this->dnsResponse) : $this->dnsResponse;
    }
}
