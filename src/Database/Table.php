<?php

namespace CommandString\Orm\Database;

use CommandString\Orm\Statements\Select;
use CommandString\Orm\Statements\Statement;
use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use PDOStatement;

abstract class Table {
    public readonly Driver $driver;
    public readonly string $name;
    
    public function __construct(Driver $driver) {
        $this->driver = NeedPdoDriver::checkDriver($driver);
        $this->name = $this->getName();
    }

    private function getName(): string
    {   
        $parts = explode("\\", get_called_class());
        return $parts[count($parts)-1];
    }

    public function select(): Select
    {
        return (new Select($this->driver))->from($this->name);
    }

    public function executeStatement(Statement $statement): PDOStatement
    {
        return $statement->execute();
    }
}