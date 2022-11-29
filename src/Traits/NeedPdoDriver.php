<?php

namespace CommandString\Orm\Traits;

use CommandString\Pdo\Driver;

trait NeedPdoDriver {
    public readonly Driver $driver;

    public function __construct(Driver $driver) {
        $this->driver = self::checkDriver($driver);
    }

    /**
     * Makes the PDO Driver is connected to the database, if not it will perform that action for the user
     *
     * @param Driver $driver
     * @return Driver
     */
    public static function checkDriver(Driver $driver): Driver
    {
        if (!isset($driver->driver)) {
            $driver->connect();
        }

        return $driver;
    }
}