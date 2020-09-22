<?php

namespace NGT\Monitor;

class Site
{
    public $url;
    public $checks;
    public $status;
    private $response = false;

    public function __construct($url, array $checks)
    {
        $this->url = $url;
        $this->checks = $checks;
        $this->status = new Status($url);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function getChecks()
    {
        return $this->checks;
    }

    public function setChecks(array $checks)
    {
        $this->checks = $checks;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}
