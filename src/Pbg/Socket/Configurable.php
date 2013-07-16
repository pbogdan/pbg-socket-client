<?php

namespace Pbg\Socket;

trait Configurable
{
    protected $options = array();

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function __call($method, $args)
    {
        $prefix = substr($method, 0, 3);
        $suffix = substr($method, 3);
        $optionName = strtolower($suffix[0]) . substr($suffix, 1);

        switch ($prefix) {
            case "set": {
                $optionValue = $args[0];
                $this->options[$optionName] = $optionValue;
                return $this;
                break;
            }
            case "get": {
                if (isset($this->options[$optionName])) {
                    return $this->options[$optionName];
                } else {
                    throw new \Exception(
                        sprintf("Uknknow option: %s",
                                $optionName
                               )
                    );
                }
                break;
            }
            default: {
              parent:__call($method, $args);
            }
        }
    }
}
