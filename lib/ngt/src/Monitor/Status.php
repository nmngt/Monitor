<?php declare(strict_types=1);

namespace NGT\Monitor;

class Status
{
    public $url;
    protected $info = [];
    protected $error = [];
    protected $success = [];
    protected $warning = [];

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    public function get() : array
    {
        return array_merge(
            $this->info,
            $this->error,
            $this->success,
            $this->warning
        );
    }

    public function add(array $arr, string $context = 'success') : Status
    {
        $this->{$context}[] = $arr;

        return $this;
    }

    public function stats() : array
    {
        return [
            'error' => sizeof($this->error)>0 ? sizeof($this->error) : 0,
            'success' => sizeof($this->success)>0 ? sizeof($this->success) : 0,
            'info' => sizeof($this->info)>0 ? sizeof($this->info) : 0,
            'warning' => sizeof($this->warning)>0 ? sizeof($this->warning) : 0
        ];
    }

    public function hasInfo() : ?bool
    {
        return sizeof($this->info)>0 ? sizeof($this->info) : false;
    }

    public function getInfo() : array
    {
        return $this->info;
    }

    public function hasError()
    {
        return sizeof($this->error)>0 ? sizeof($this->error) : false;
    }

    public function getErrors() : array
    {
        return $this->error;
    }

    public function hasSuccess()
    {
        return sizeof($this->success)>0 ? sizeof($this->success) : false;
    }

    public function getSuccess() : array
    {
        return $this->success;
    }

    public function hasWarning()
    {
        return sizeof($this->warning)>0 ? sizeof($this->warning) : false;
    }

    public function getWarning() : array
    {
        return $this->warning;
    }
}
