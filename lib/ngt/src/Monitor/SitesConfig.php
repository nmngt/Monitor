<?php

namespace NGT\Monitor;

use NGT\Monitor\Contracts\Constants;
use NGT\Monitor\Exceptions\SiteWithoutUrlException;
use NGT\Monitor\Exceptions\SiteWithoutChecksException;
use NGT\Monitor\Exceptions\SitesWithoutChecksException;

class SitesConfig extends \ArrayObject
{
    public function __construct(array $config)
    {
        $this->raw = $config;
        $this->parse($config);
    }

    /**
     * get this ArrayObject as a static object
     *
     * @param  array  $config [description]
     * @return object         [description]
     */
    public static function get(array $config)
    {
        return new static($config);
    }

    /**
     * parse config array and check for sites. if we have a site+checks
     * append the \NGT\Monitor\Site object to the ArrayObejct
     *
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    private function parse($config)
    {
        if (empty($config['sites']) && ! is_array($config['sites'])) {
            throw new SitesWithoutChecksException('Sites part in config is missing or invalid');
        }

        foreach ($config['sites'] as $url => $checks) {
            try {
                $this->validate($url, $checks);
            } catch (SiteWithoutChecksException $e) {

                // if we catch a SiteWithoutChecksException exception, try to
                // pull the default checks and run these..
                if ($e instanceof \NGT\Monitor\Exceptions\SiteWithoutChecksException) {
                    $checks = Constants::DEFAULT_CHECKS;
                    $this->validate($url, $checks);
                } else {
                    throw new SiteWithoutChecksException('No checks configured for website "{$url}"!');
                }
            }

            // append the site to the ArrayObject
            $this->append(new Site($url, $checks));
        }
    }

    /**
     * validate that we have a site and checks for that site
     *
     * @param  [type] $url [description]
     * @return [type]          [description]
     */
    private function validate($url = null, $checks = null)
    {
        if (empty($url)) {
            throw new SiteWithoutUrlException('URL for website is required');
        }

        if (empty($checks)) {
            throw new SiteWithoutChecksException('No checks configured for website "{$url}"');
        }
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getCheckDefinitions()
    {
        return $this->raw['checks'];
    }
}
