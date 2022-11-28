<?php

namespace CommandString\Orm\Statements\Traits;

use InvalidArgumentException;

trait OrderBy {
    private string $columnToOrderBy;
    private string $directionToOrderBy;

    public function orderBy(string $column, string $direction): self
    {
        $direction = strtoupper($direction);

        if ($direction !== "ASC" && $direction !== "DESC") {
            throw new InvalidArgumentException("You must supply ASC or DESC for the direction argument!");
        }

        $this->directionToOrderBy = $direction;
        $this->columnToOrderBy = $column;

        return $this;
    }

    private function buildOrderBy(string &$query) {
        if (isset($this->columnToOrderBy) && isset($this->directionToOrderBy)) {
            $query .= " ORDER BY {$this->columnToOrderBy} {$this->directionToOrderBy}";
        }
    }
}