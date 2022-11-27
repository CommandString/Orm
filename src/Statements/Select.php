<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Statements\Traits\Columns;
use CommandString\Orm\Statements\Traits\LimitOffset;
use CommandString\Orm\Statements\Traits\Where;
use PDOStatement;

final class Select {
    use Statement;
    use Where;
    use Columns;
    use LimitOffset;
    private string $table;

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    protected function build(): string
    {
        if (isset($this->query)) {
            return $this->query;
        }

        $query = "SELECT";

        $this->buildColumns($query);     

        if (isset($this->table)) {
            $query .= " FROM {$this->table}";
        } else {
            throw new \Exception("You must define the table you want to select from!");
        }
        
        $this->buildWheres($query); 

        $this->buildLimit($query);

        $this->buildOffset($query);

        $this->query = $query;

        return $query;
    }
}