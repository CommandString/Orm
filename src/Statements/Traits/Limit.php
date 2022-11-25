<?php

namespace CommandString\Orm\Statements\Traits;

trait Limit {
    private int $limit = 0;

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    private function buildLimit(string &$query) {
        if ($this->limit > 0) {
            $query .= " LIMIT {$this->limit}";
        }
    }
}