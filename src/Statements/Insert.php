<?php


namespace CommandString\Orm\Statements;

final class Insert {
    use Statement;

    private string $tableName = "";
    private array $values = [];

    public function values(array $values): self
    {
        foreach ($values as $column => $value) {
            $this->values[$column] = $value;
        }

        return $this;
    }

    public function into(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function buildValues(string &$query) {
        $query .= "(";
        $values = ") VALUES (";
        foreach ($this->values as $column => $value) {
            $id = $this->generateId();
            $values .= ":$id, ";

            $this->addParam($id, $value);

            $query .= "$column, ";
        }

        $query = substr($query, 0, -2).substr($values, 0, -2).")";
    }

    protected function build(): string
    {
        $this->parameters = [];

        $query = "INSERT INTO {$this->tableName} ";

        $this->buildValues($query);

        return $query;
    }
}