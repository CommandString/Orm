<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Statements\Traits\Where;
use CommandString\Orm\Statements\Traits\Set;

class Update {
    use Statement;
    use Where;
    use Set;

    public function build(): string
    {
        $query = "UPDATE {$this->table}";
        
        $this->buildSets($query);

        $this->buildWheres($query);

        return $query;
    }
}