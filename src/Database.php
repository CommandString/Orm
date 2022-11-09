<?php

namespace CommandString\Orm;

abstract class Database {
    private array $tables;

    public function __construct(Table ...$tables) {
        $this->tables = $tables;
    }

    public function __get($name) {
        if ($name === $this->tables) {
            return $this->tables;
        }

        if ($this->getTable($name) !== null) {
            return $this->getTable($name);
        }

        return null;
    }

    public function getTable(string $tableName): Table|null
    {
        foreach ($this->tables as $table) {
            if ($table->getTable() === $tableName) {
                return $table;
            }
        }

        return null;
    }
}