<?php

namespace CommandString\Orm\Statements\Traits;

use CommandString\Orm\Operators;
use Exception;
use InvalidArgumentException;

trait Where {
    private array $wheres = [];

    public function addWhere(string $name, string $operator, mixed $value, bool $not = false, bool $or = false): self
    {
        if (!Operators::isValidOperator($operator)) {
            throw new Exception("$operator is an invalid operator, check \CommandString\Orm\Operators for a list of valid operators!");
        }

        if ($not && $or) {
            throw new InvalidArgumentException("Not and or cannot both be true!");
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

        $this->wheres[] = [
            "name" => $name,
            "operator" => $operator,
            "value" => $value,
            "not" => $not,
            "or" => $or
        ];

        return $this;
    }

    public function whereOr(string $name, string $operator, mixed $value): self
    {
        return $this->addWhere($name, $operator, $value, false, true);
    }

    public function whereNot(string $name, string $operator, mixed $value): self
    {
        return $this->addWhere($name, $operator, $value, true, false);
    }

    public function where(string $name, string $operator, mixed $value): self
    {
        return $this->addWhere($name, $operator, $value, false, false);
    }

    private function buildWheres(string &$query)
    {
        $i = 0;

        foreach ($this->wheres as $options) {
            $name = $options["name"];
            $value = $options["value"];
            $operator = $options["operator"];
            $not = $options["not"];
            $or = $options["or"];

            if ($i) {
                $query .= ($or) ? " OR" : " AND";
            } else {
                $query .= " WHERE";
                $i++;
            }

            $id = $this->generateId();

            if ($operator !== Operators::IN && $operator !== Operators::BETWEEN) {
                $query .= ($not) ? " NOT $name $operator :$id" : " $name $operator :$id";
                
                $this->addParam($id, $value);
            } else {
                $query .= " $name $operator $value";
            }
        }
    }
}
