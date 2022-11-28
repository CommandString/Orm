<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Statements\Traits\Columns;
use CommandString\Orm\Statements\Traits\Join;
use CommandString\Orm\Statements\Traits\LimitOffset;
use CommandString\Orm\Statements\Traits\Where;

final class Select {
    use Statement;
    use Where;
    use Columns;
    use LimitOffset;
    use Join;
    private string $table;

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
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

        $this->buildLimit($query);
        $this->buildOffset($query);

        $this->query = $query;

        return $query;
    }
}