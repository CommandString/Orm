<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Statements\Delete;
use CommandString\Orm\Statements\Insert;
use CommandString\Orm\Statements\Select;
use CommandString\Orm\Statements\StorableStatement;
use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use ReflectionClass;

abstract class Table {
    public readonly Driver $driver;
    public readonly string $name;
    private array $storedStatements;
    
    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->name = $this->getName();
    }

    /**
     * Get name of the table
     *
     * @return string
     */
    private function getName(): string
    {
        $reflection = new ReflectionClass(get_called_class());

        return strtolower($reflection->getShortName());
    }

    /**
     * Shorthand select
     *
     * @return Select
     */
    public function select(): Select
    {
        return (new Select($this->driver))->from($this->name);
    }

    /**
     * Shorthand insert
     *
     * @return Insert
     */
    public function insert(): Insert
    {
        return (new Insert($this->driver))->into($this->name);
    }

    /**
     * Shorthand delete
     *
     * @return Delete
     */
    public function delete(): Delete
    {
        return (new Delete($this->driver))->from($this->name);
    }

    /**
     * Store statement
     *
     * @param StorableStatement $statement
     * @return self
     */
    public function storeStatement(StorableStatement $statement): self
    {
        $this->storedStatements[$statement->name] = $statement;

        return $this;
    }

    /**
     * Get stored statement
     *
     * @param string $name
     * @return StorableStatement|null
     */
    public function getStoredStatement(string $name): ?StorableStatement
    {
        return $this->storedStatements[$name] ?? null;
    }
}