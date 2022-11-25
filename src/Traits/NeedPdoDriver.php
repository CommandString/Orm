<?php

namespace CommandString\Orm\Traits;

use CommandString\Pdo\Driver;

trait NeedPdoDriver {
    public readonly Driver $driver;

    public function __construct(Driver $driver) {
        if (!isset($driver->driver)) {
            $driver->connect();
        }

        $this->driver = $driver;
    }
}