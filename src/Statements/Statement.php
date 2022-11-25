<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use PDOStatement;

trait Statement {
    use NeedPdoDriver;
    private array $parameters = [];
    private string $query;

    public function getParameters(): array
    {
        return $this->parameters;
    }

    private function addParam(string $name, int|string $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    abstract public function build(): string;
    
    abstract public function execute(): PDOStatement;

    public function __toString(): string
    {
        return $this->build();
    }

    public static function new(Driver $driver): self
    {
        return new self($driver);
    }

    private function generateId(): string {
        $characters = array_merge(range("A", "Z"), range("a", "z"));
        $id = "";

        for ($i = 0; $i <= 16; $i++) {
            $id .= $characters[rand(0, count($characters)-1)];
        }

        if (in_array($id, array_keys($this->parameters))) { // in the crazy case that there is a collision
            return $this->generateId();
        }

        return $id;
    }
}