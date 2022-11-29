<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Statements\Delete;
use CommandString\Orm\Statements\Insert;
use CommandString\Orm\Statements\Select;
use CommandString\Orm\Statements\StorableStatement;
use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;

abstract class Table {
    public readonly Driver $driver;
    public readonly string $name;
    private array $storedStatements;
    
    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->name = $this->getName();
    }

    private function getName(): string
    {   
        $parts = explode("\\", get_called_class());
        return strtolower($parts[count($parts)-1]);
    }

    public function select(): Select
    {
        return (new Select($this->driver))->from($this->name);
    }

    public function insert(): Insert
    {
        return (new Insert($this->driver))->into($this->name);
    }

    public function delete(): Delete
    {
        return (new Delete($this->driver))->from($this->name);
    }

    public function storeStatement(StorableStatement $statement): self
    {
        $this->storedStatements[$statement->name] = $statement;

        return $this;
    }

    public function getStoredStatement(string $name): ?StorableStatement
    {
        return $this->storedStatements[$name] ?? null;
    }
}