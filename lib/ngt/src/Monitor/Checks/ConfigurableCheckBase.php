<?php
namespace NGT\Monitor\Checks;

use NGT\Monitor\Checks\Interfaces\ConfigurableCheckInterface;
use NGT\Monitor\Checks\Exceptions\FieldNotConfigurableException;

abstract class ConfigurableCheckBase extends CheckBase implements ConfigurableCheckInterface
{
    protected $configurableField = [];

    public function set($key, $value)
    {
        if (! in_array($key, $this->configurableField)) {
            throw new FieldNotConfigurableException("$key is not configurable");
        }
        $this->$key = $value;
    }
}
