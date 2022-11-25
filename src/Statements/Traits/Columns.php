<?php

namespace CommandString\Orm\Statements\Traits;

trait Columns {
    private array $columns = [];

    public function columns(string|array ...$columns): self
    {
        foreach ($columns as $column) {
            if (!in_array($column, $this->columns)) {
                $this->columns[] = $column;
            }
        }

        return $this;
    }

    private function buildColumns(string &$query) {
        if (!empty($this->columns)) {
            foreach ($this->columns as $column) {
                if (is_array($column)) {
                    $columnName = array_keys($column)[0];
                    $column = $columnName." AS ".$column[$columnName];
                }

                $query .= " $column,";
            }
            
            $query = substr($query, 0, -1);
        } else {
            $query .= " *";
        }
    }
}