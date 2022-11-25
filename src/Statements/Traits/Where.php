<?php

namespace CommandString\Orm\Statements\Traits;

use CommandString\Orm\Operators;
use Exception;

trait Where {
    private array $wheres = [];

    public function where(string $name, string $operator, mixed $value): self
    {
        if (!Operators::isValidOperator($operator)) {
            throw new Exception("$operator is an invalid operator, check \CommandString\Orm\Operators for a list of valid operators!");
        }

        $this->wheres[$name] = [
            "operator" => $operator,
            "value" => $value
        ];

        return $this;
    }

    private function buildWheres(string &$query)
    {
        if (!empty($this->wheres)) {
            $i = 0;
            foreach ($this->wheres as $name => $options) {
                $value = $options["value"];
                $operator = $options["operator"];

                if ($i) {
                    $query .= " AND";
                } else {
                    $i++;
                }

                $id = $this->generateId();

                $query .= " WHERE $name $operator :$id";

                $this->addParam($id, $value);
            }
        }
    }
}