<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Statements\Traits\Columns;
use CommandString\Orm\Statements\Traits\Join;
use CommandString\Orm\Statements\Traits\LimitOffset;
use CommandString\Orm\Statements\Traits\OrderBy;
use CommandString\Orm\Statements\Traits\Where;

final class Select {
    use Statement;
    use Where;
    use Columns;
    use LimitOffset;
    use Join;
    use OrderBy;

    public function from(string $table): self
    {
        return $this->table($table);
    }

    protected function build(): string
    {
        if (isset($this->query)) {
            $this->parameters = [];
        }

        $query = "SELECT";

        $this->buildColumns($query);     

        $query .= " FROM {$this->table}";

        $this->buildJoin($query);
        $this->buildOn($query);
        
        $this->buildWheres($query);

        $this->buildOrderBy($query);

        $this->buildLimit($query);
        $this->buildOffset($query);

        $this->query = $query;

        return $query;
    }
}