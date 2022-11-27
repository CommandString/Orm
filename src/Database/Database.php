<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use ReflectionClass;
use stdClass;

/**
 * @property-read $tables
 */
abstract class Database {
    public readonly Driver $driver;
    public readonly stdClass $tables;
    public readonly string $name;

    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->tables = new stdClass;
        $this->name = $this->getName();
        $this->buildTables();
    }    

    private function getName(): string
    {   
        $parts = explode("\\", get_called_class());
        return $parts[count($parts)-1];
    }

    private function buildTables() {
        $tables = (new ReflectionClass(get_called_class()))->getConstants();

		foreach ($tables as $tableName) {
			$className = "\\".str_replace("$this->name", "", get_called_class()).ucfirst($tableName);
			$this->tables->$tableName = new $className($this->driver);
		}
    }

    public function getTable(string $table): ?Table
    {
        return $this->tables->$table ?? null;
    }
}