<?php

namespace NGT;

class Registry
{
    private static $instance;
    private $store;

    protected function __construct()
    {
        $this->store = [];
    }

    public static function __init()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function add($key, $value)
    {
        $instance = self::__init();
        $instance->store[$key]= $value;
    }

    public static function load($key)
    {
        $instance = self::__init();
        return $instance->store[$key];
    }

    public static function storage()
    {
        $instance = self::__init();
        return $instance->store;
    }

    public static function stored($key)
    {
        $instance = self::__init();
        return isset($instance->store[$key]);
    }

    public static function remove($key)
    {
        $instance = self::__init();
        unset($instance->store[$key]);
    }

    public static function output()
    {
        $instance = self::__init();
        return get_object_vars($instance);
    }

    private function __sleep()
    {
        $this->store = serialize($this->store);
    }

    private function __wakeup()
    {
        $this->store = unserialize($this->store);
    }
}
