<?php

namespace CommandString\Orm\Statements\Traits;

use Exception;

trait Join {    
    private string $column1;
    private string $column2;
    private string $type;
    private string $tableToJoin;

    private function join(string $type, string $tableToJoin): self
    {
        $direction = strtoupper($type);

        if (!in_array($direction, ["LEFT", "RIGHT", "FULL", "SELF"])) {
            throw new Exception("Direction must be LEFT, RIGHT, FULL, SELF!");
        }

        $this->type = $type;
        $this->tableToJoin = $tableToJoin;

        return $this;
    }

    public function leftJoin(string $tableToJoin): self
    {
        return $this->join("LEFT", $tableToJoin);
    }

    public function rightJoin(string $tableToJoin): self
    {
        return $this->join("RIGHT", $tableToJoin);
    }

    public function fullJoin(string $tableToJoin): self
    {
        return $this->join("FULL", $tableToJoin);
    }

    public function selfJoin(string $tableToJoin): self
    {
        return $this->join("SELF", $tableToJoin);
    }

    private function buildJoin(string &$query) {
        if (isset($this->type)) {
            $query .= " {$this->type} JOIN {$this->tableToJoin}";
        }
    }

    public function on(string $column1, string $column2): self
    {
        $this->column1 = $column1;
        $this->column2 = $column2;

        return $this;
    }

    private function buildOn(string &$query) {
        if (!isset($this->column1) || !isset($this->column2)) {
            return;
        }

        $query .= " ON {$this->column1} = {$this->column2}";
    }
}