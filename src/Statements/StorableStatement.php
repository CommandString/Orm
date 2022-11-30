<?php

namespace CommandString\Orm\Statements;

use Closure;
use LogicException;

class StorableStatement {
    private Closure $before;
    private Closure $after;
    private Select|Update|Delete|Insert $statement;

    public function __construct(public readonly string $name) {}

    public function setStatement(Select|Update|Delete|Insert $statement) {
        $this->statement = $statement;

        return $this;
    }

    public function setBeforeHandler(Closure $before): self
    {
        $this->before = $before;

        return $this;
    }

    public function setAfterHandler(Closure $after): self
    {
        $this->after = $after;

        return $this;
    }

    public function execute(array $beforeArgs = [], array $afterArgs = []): mixed
    {
        $statement = (isset($this->before)) ? call_user_func($this->before, $this->statement, ...$beforeArgs) : $this->statement;

        if (
            !$statement instanceof Select &&
            !$statement instanceof Update &&
            !$statement instanceof Delete &&
            !$statement instanceof Insert
        ) {
            throw new LogicException("Your before handler must return an instance of Select, Update, Delete, or Insert! ".gettype($statement)." was returned");
        }

        $results = $statement->execute();

        return (isset($this->after)) ? call_user_func($this->after, $results, ...$afterArgs) : $results;
    }
}