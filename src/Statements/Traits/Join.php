<?php

namespace CommandString\Orm\Statements\Traits;

use Exception;

trait Join {    
    private string $column1;
    private string $column2;
    private string $direction;
    private string $tableToJoin;

    private function join(string $direction, string $tableToJoin): self
    {
        $direction = strtoupper($direction);

        if (!in_array($direction, ["LEFT", "RIGHT"])) {
            throw new Exception("Direction must be LEFT or RIGHT!");
        }

        $this->direction = $direction;
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

    public function buildJoin(string &$query) {
        if (isset($this->direction)) {
            $query .= " LEFT JOIN {$this->tableToJoin}";
        }
    }

    public function on(string $column1, string $column2): self
    {
        $this->column1 = $column1;
        $this->column2 = $column2;

        return $this;
    }

    public function buildOn(string &$query) {
        $query .= " ON {$this->column1} = {$this->column2}";
    }
}