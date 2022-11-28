<?php

namespace CommandString\Orm\Statements\Traits;

trait Values {
    private array $values = [];

    public function value(string $column, int|string $value): self
    {
        $this->values[$column] = $value;

        return $this;
    }

    public function values(array $values): self
    {
        foreach ($values as $column => $value) {
            $this->value($column, $value);
        }

        return $this;
    }

    public function buildValues(string &$query) {
        $query .= "(";
        $values = ") VALUES (";
        foreach ($this->values as $column => $value) {
            $id = $this->generateId();
            $values .= ":$id, ";

            $this->addParam($id, $value);

            $query .= "$column, ";
        }

        $query = substr($query, 0, -2).substr($values, 0, -2).")";
    }
}