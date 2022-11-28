<?php

namespace CommandString\Orm\Statements\Traits;

use CommandString\Orm\Operators;
use Exception;
use InvalidArgumentException;

trait Where {
    private array $wheres = [];

    public function where(string $name, string $operator, mixed $value): self
    {
        if (!Operators::isValidOperator($operator)) {
            throw new Exception("$operator is an invalid operator, check \CommandString\Orm\Operators for a list of valid operators!");
        }

        if (($operator === Operators::IN || $operator === Operators::BETWEEN) && !is_array($value)) {
            throw new InvalidArgumentException("An array must be supplied for the value argument when using the IN or BETWEEN operator!");
        }

        if ($operator === Operators::IN) {
            $values = $value;
            $string = "(";

            foreach ($values as $value) {
                $string .= "$value, ";
            }
            
            $value = substr($string, 0, -2).")";
        } else if ($operator === Operators::BETWEEN) {
            $values = $value;

            $value = "{$value[0]} AND {$value[1]}";
        }

        $this->wheres[$name] = [
            "operator" => $operator,
            "value" => $value
        ];

        return $this;
    }

    private function buildWheres(string &$query)
    {
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

            if ($operator !== Operators::IN && $operator !== Operators::BETWEEN) {
                $query .= " WHERE $name $operator :$id";
                $this->addParam($id, $value);
            } else {
                $query .= " WHERE $name $operator $value";
            }
        }
    }
}