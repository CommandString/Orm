<?php

namespace CommandString\Orm;

use CommandString\Pdo\Driver;
use PDO;

abstract class Table {
    private Driver $driver;
    protected string $table;
    protected string $primaryKey;

    public function __construct(Driver $driver) {
        $this->driver = $driver;
        $this->table = $this->getTable();
        $this->primaryKey = $this->getPrimaryKey();
    }

    public function fetchAllRows(int $fetchMode = PDO::FETCH_ASSOC, string ...$columns): mixed
    {
        $queryTemplate = "SELECT %s FROM {$this->table}";

        if (!empty($columns)) {
            $columnString = "";

            foreach ($columns as $column) {
                $columnString .= "$column, ";
            }

            $query = sprintf($queryTemplate, substr($columnString, 0, -2));
        } else {
            $query = sprintf($queryTemplate, "*");
        }

        return $this->driver->query($query)->fetchAll($fetchMode);
    }

    abstract protected function getTable(): string;

    abstract protected function getPrimaryKey(): string;
}