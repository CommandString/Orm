<?php

namespace CommandString\Orm\Statements\Traits;

trait LimitOffset {
    private int $limit = 0;
    private int $offset = -1;

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

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function buildOffset(&$query) {
        if ($this->offset > -1) {
            $query .= " OFFSET {$this->offset}";
        }
    }
}