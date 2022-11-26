<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Traits\NeedPdoDriver;
use ReflectionClass;
use stdClass;

/**
 * @property-read $tables
 */
abstract class Database {
    use NeedPdoDriver;
    protected stdClass $tables;

    public function __get($name): mixed
    {
        $readonlyProperties = ["tables"];

        if (in_array($name, $readonlyProperties)) {
            return $this->{$name};
        }

        return null;
    }

    public function addTable(Table $table) {
        if (!isset($this->tables)) {
            $this->tables = new stdClass();
        }

        $this->tables->$table->name = $table;
    }

    public function getTable(string $table): ?Table
    {
        return $this->tables->$table ?? null;
    }

	public function initializeDatabase():self 
    {
        $tables = (new ReflectionClass(__CLASS__))->getConstants();

		foreach($tables as $table) {
			$this->addTable($table);
		}

        return $this;
	}
}