<?php

namespace CommandString\Orm\Statements;

use CommandString\Orm\Traits\NeedPdoDriver;
use CommandString\Pdo\Driver;
use PDOStatement;

trait Statement {
    use NeedPdoDriver;
    private array $parameters = [];
    private string $query;
    private string $table;

    public function getParameters(): array
    {
        return $this->parameters;
    }

    private function addParam(string $name, int|string $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    abstract protected function build(): string;

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
    
    public function execute(): PDOStatement
    {
        $this->driver->prepare($this);
        
        if (!empty($this->parameters)) {
            foreach ($this->parameters as $id => $value) {
                $this->driver->bindValue($id, $value);
            }
        }

        $this->driver->execute();

        return $this->driver->statement;
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }
}