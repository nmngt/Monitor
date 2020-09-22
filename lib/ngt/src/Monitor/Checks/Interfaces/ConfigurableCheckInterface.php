<?php

namespace NGT\Monitor\Checks\Interfaces;

interface ConfigurableCheckInterface extends CheckInterface
{
    public function set($key, $value);
}
