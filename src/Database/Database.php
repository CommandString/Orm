<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use ReflectionClass;
use stdClass;

abstract class Database {
    public readonly Driver $driver;
    private stdClass $tables;
    public readonly string $name;

    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->tables = new stdClass;
        $this->name = $this->getName();
        $this->buildTables();
    }    

    /**
     * Get name of database
     *
     * @return string
     */
    private function getName(): string
    {   
        $parts = explode("\\", get_called_class());
        return $parts[count($parts)-1];
    }

    /**
     * Instantiate all table classes belonging to this database
     *
     * @return void
     */
    private function buildTables() {
        $reflection = (new ReflectionClass(get_called_class()));
        $tables = $reflection->getConstants();

		foreach ($tables as $tableName) {
            $className = $reflection->getNamespaceName()."\\".ucfirst($tableName);

			$this->tables->$tableName = new $className($this->driver);
		}
    }

    /**
     * Get table
     *
     * @param string $table
     * @return Table|null
     */
    public function getTable(string $table): ?Table
    {
        return $this->tables->$table ?? null;
    }
}