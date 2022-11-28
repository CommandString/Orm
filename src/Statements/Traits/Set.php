<?php

namespace CommandString\Orm\Statements\Traits;

trait Set {
    private array $sets = [];

    public function set(string $column, int|string $value): self
    {
        $this->sets[$column] = $value;

        return $this;
    }

    public function sets(array $sets) {
        foreach ($sets as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    private function buildSets(string &$query) {
        $query .= " SET ";
        
        foreach ($this->sets as $column => $value) {
            $id = $this->generateId();

            $query .= "$column = :$id, ";

            $this->addParam($id, $value);
        }

        $query = substr($query, 0, -2);
    }
}