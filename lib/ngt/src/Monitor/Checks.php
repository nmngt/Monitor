<?php
namespace NGT\Monitor;

use NGT\Monitor\Checks\Interfaces\CheckInterface;
use NGT\Monitor\Checks\Interfaces\ConfigurableCheckInterface;
use NGT\Monitor\Checks\Exceptions\CheckNotConfigurableException;

class Checks
{
    public $checks;

    public function __construct(array $siteChecks, array $checkDefinitions)
    {
        $this->checks = $this->mergeConfiguration($siteChecks, $checkDefinitions);
    }

    public function build() : array
    {
        $checks = [];
        foreach ($this->checks as $name => $values) {
            // get className from checkDefinitions
            $className = $values['class'];
            $class = new $className();

            // populate check classes with config items
            $this->addMethodCalls($values, $class);
            $this->addConfiguration($values, $class);

            $checks[$name] = $class;
        }
        return $checks;
    }

    /**
     * this gets the [calls] part as method calls from config file
     * and injects it in the object
     *
     * @param [type]         $config [description]
     * @param CheckInterface $class  [description]
     */
    private function addMethodCalls($config, CheckInterface $class)
    {
        if (isset($config['calls']) && is_array($config['calls'])) {
            foreach ($config['calls'] as $method => $params) {
                $params = is_array($params) ? $params : [$params];
                call_user_func_array([$class, $method], $params);
            }
        }
    }

    /**
     * this gets the [configuration] part as method calls from config
     * file, checks if the configured value is allowed, then injects
     * it in the object
     *
     * @param [type]         $config [description]
     * @param CheckInterface $class  [description]
     */
    private function addConfiguration($config, $class)
    {
        if (empty($config['configuration'])) {
            return;
        }

        if (! (isset($config['configuration']) && $class instanceof ConfigurableCheckInterface)) {
            throw new CheckNotConfigurableException($config['class'].' is not configurable.');
        }

        $this->addMethodCalls(['calls' => $config['configuration']], $class);
    }

    /**
     * merge the configuration from our config file with the default check
     * configuration. Settings from config file are prefered and are
     * not overwritten by default values
     *
     * @param  array   $siteChecks       [description]
     * @param  array   $checkDefinitions [description]
     * @param  boolean $overwrite        [description]
     * @return [type]                    [description]
     */
    private function mergeConfiguration(array $siteChecks, array $checkDefinitions, $overwrite = true) : array
    {
        $config = [];
        foreach ($siteChecks as $key => $values) {
            if (false === $values) {
                continue;
            }

            if (isset($checkDefinitions[$key])) {
                $values = is_array($values) ? $values : [$values];
                $config[$key] = array_merge_recursive($values, $checkDefinitions[$key]);
            } else {
                $config[$key] = $values;
            }
        }
        return $config;
    }

    public function setDefinitions(array $checkDefinitions)
    {
        $this->checkDefinitions = $checkDefinitions;
    }
}
