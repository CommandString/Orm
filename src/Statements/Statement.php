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

    /**
     * Retrieve Parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Store parameter
     *
     * @param string $name
     * @param integer|string $value
     * @return self
     */
    private function addParam(string $name, int|string $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Build SQL Query
     *
     * @return string
     */
    abstract protected function build(): string;

    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * Shorthand creation
     *
     * @param Driver $driver
     * @return self
     */
    public static function new(Driver $driver): self
    {
        return new self($driver);
    }

    /**
     * Generate a parameter id
     *
     * @return string
     */
    private function generateId(): string {
        $id = \CommandString\Utils\GeneratorUtils::uuid();

        if (in_array($id, array_keys($this->parameters))) { // in the crazy case that there is a collision
            return $this->generateId();
        }

        return $id;
    }
    
    /**
     * Execute query
     *
     * @return PDOStatement
     */
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

    /**
     * Set the table the SQL query will be performed upon
     *
     * @param string $table
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }
}