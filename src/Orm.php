<?php

namespace CommandString\Orm;

use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;

class Orm {
    public readonly Driver $driver;
    public readonly Builder $builder;

    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->builder = new Builder($this->driver);
    }
}