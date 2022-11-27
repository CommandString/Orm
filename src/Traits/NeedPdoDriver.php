<?php

namespace CommandString\Orm\Traits;

use CommandString\Pdo\Driver;

trait NeedPdoDriver {
    public readonly Driver $driver;

    public function __construct(Driver $driver) {
        $this->driver = self::checkDriver($driver);
    }

    public static function checkDriver(Driver $driver): Driver
    {
        if (!isset($driver->driver)) {
            $driver->connect();
        }

        return $driver;
    }
}