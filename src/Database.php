<?php

namespace CommandString\Orm;

abstract class Database {
    private array $tables;

    public function __construct(Table ...$tables) {
        $this->tables = $tables;
    }

    public function getTable(string $tableName) {
        foreach ($this->tables as $table) {
            if ($table->getTable() === $tableName) {
                return $table;
            }
        }
    }
}