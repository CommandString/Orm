<?php

namespace CommandString\Orm\Statements\Traits;

trait Where {
    private array $wheres = [];

    public function where(string $name, mixed $value): self
    {
        $this->wheres[$name] = $value;

        return $this;
    }

    private function buildWheres(string &$query)
    {
        if (!empty($this->wheres)) {
            $i = 0;
            foreach ($this->wheres as $name => $value) {
                if ($i) {
                    $query .= " AND";
                } else {
                    $i++;
                }

                $id = $this->generateId();

                $query .= " WHERE $name = :$id";

                $this->addParam($id, $value);
            }
        }
    }
}