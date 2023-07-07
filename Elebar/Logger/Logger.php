<?php
namespace Rollpix\Elebar\Logger;

class Logger extends \Monolog\Logger
{
    public function setName($name)
    {
        $this->name = $name;
    }
}
