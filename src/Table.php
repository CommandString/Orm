<?php

namespace CommandString\Orm;

use CommandString\Orm\Statements\Select;
use CommandString\Orm\Statements\Statement;
use CommandString\Orm\Traits\NeedPdoDriver;
use PDOStatement;

abstract class Table {
    use NeedPdoDriver;

    public string $name;

    public function select(): Select
    {
        return (new Select($this->driver))->from($this->name);
    }

    public function executeStatement(Statement $statement): PDOStatement
    {
        return $statement->execute();
    }
}