<?php

namespace CommandString\Orm;

use CommandString\Pdo\Driver;
use Exception;
use PDO;

abstract class Table {
    private Driver $driver;
    public readonly string $table;
    private string $primaryKey;
    private array $queryTemplates = [
        "SELECT" => "SELECT %s FROM %s", // columns : table name
        "INSERT" => "INSERT INTO %s (%s) VALUES (%s)", // table name : columns : ids
        "UPDATE" => "UPDATE %s", // table name
        "DELETE" => "DELETE FROM %s", // table name
        "PARAMETER" => " %s %s :%s", // column name : operator : id
        "JOIN" => " %s JOIN %s", // direction : tablename
        "ON" => " ON %s = %s", // columnName1 : columnName2,
        "IN" => " %s IN (%s)", // columnName : list
    ];
    private string $query;
    private array $parameters = [];
    private array $customQueries = [];

    public function __construct(Driver $driver) {
        $this->driver = $driver;
        $this->table = $this->getTable();
        $this->primaryKey = $this->getPrimaryKey();
    }

    /**
     * Generates ID used when bind parameters
     *
     * @return string
     */
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

    /**
     * Trashes the current query being built
     *
     * @return void
     */
    private function reset(): void
    {
        $this->parameters = [];
        unset($this->query);
    }

    /**
     * Start a select statement
     *
     * @param array $columns ["columnName", "alias" => "columnName"]
     * @return self
     * 
     * ```
     * Table::select(["username" => "name", "password"])->execute(PDO::FETCH_OBJ);
     * ```
     */
    public function select(array $columns = []): self
    {
        $this->reset();

        if (!empty($columns)) {
            $columnString = "";

            foreach ($columns as $key => $value) {
                $columnString .= (is_string($key)) ? "$key AS $value, " : "$value, ";
            }

            $this->query = sprintf($this->queryTemplates["SELECT"], substr($columnString, 0, -2), $this->table);
        } else {
            $this->query = sprintf($this->queryTemplates["SELECT"], "*", $this->table);
        }
        
        return $this;
    }

    /**
     * Add WHERE clause
     *
     * @param string $columnName
     * @param string $operator
     * @param mixed $value
     * @return self
     * 
     * ```
     * Table::select(["username", "password"])
     * ->where("admin_rank", Operators::GREATER_THAN, 1)
     * ->execute();
     * ```
     */
    public function where(string $columnName, string $comparingOperator = Operators::EQUAL_TO, mixed $value = null, string $combiningOperator = Operators::AND): self
    {
        $id = $this->generateId();

        $this->parameters[$id] = $value;
        
        $this->query .= (str_contains($this->query, "WHERE")) ? " $combiningOperator" : " WHERE";
        $this->query .= sprintf($this->queryTemplates["PARAMETER"], $columnName, $comparingOperator, $id);

        return $this;
    }

    /**
     * Add WHERE BETWEEN clause
     *
     * @param string $columnName
     * @param mixed $start
     * @param mixed $end
     * @param string $combiningOperator
     * @return self
     * 
     * ```
     * Table::select("username", "score")
     * ->whereBetween("score", 0, 500000)
     * ->execute()
     * ```
     */
    public function whereBetween(string $columnName, mixed $start, mixed $end, string $combiningOperator = Operators::AND): self
    {
        $startId = $this->generateId();
        $endId = $this->generateId();

        $this->parameters[$startId] = $start;
        $this->parameters[$endId] = $end;
        
        $this->query .= (str_contains($this->query, "WHERE")) ? " $combiningOperator" : " WHERE";
        $this->query .= sprintf($this->queryTemplates["PARAMETER"], $columnName, Operators::BETWEEN, "$startId AND :$endId");

        return $this;
    }

    /**
     * Add WHERE IN clause
     *
     * @param string $columnName
     * @param array $values
     * @param [type] $combiningOperator
     * @return self
     * 
     * ```
     * Table::select("username", "score")
     * ->whereIn("username", ["CommandString", "realdiegopoptart"])
     * ->execute();
     * ```
     */
    public function whereIn(string $columnName, array $values, string $combiningOperator = Operators::AND): self
    {
        $this->query .= (str_contains($this->query, "WHERE")) ? " $combiningOperator" : " WHERE";

        $list = "";

        foreach ($values as $value) {
            $id = $this->generateId();

            $list .= ":$id, ";
            $this->parameters[$id] = $value;
        }

        $this->query .= sprintf($this->queryTemplates["IN"], $columnName, substr($list, 0, -2));

        return $this;
    }

    /**
     * Add JOIN clause
     *
     * @param string $direction
     * @param string $tableName
     * @param string|null $onColumn1
     * @param string|null $onColumn2
     * @return self
     */
    public function join(string $direction, string $tableName, ?string $onColumn1 = null, ?string $onColumn2 = null): self
    {
        $this->query .= sprintf($this->queryTemplates["JOIN"], $direction, $tableName);

        return ($onColumn1 !== null && $onColumn2 !== null) ? $this->on($onColumn1, $onColumn2) : $this;
    }

    /**
     * Add ON clause to the query
     *
     * @param string $columnName1
     * @param string $columnName2
     * @return self
     */
    public function on(string $columnName1, string $columnName2): self
    {
        $this->query .= sprintf($this->queryTemplates["ON"], $columnName1, $columnName2);

        return $this;
    }

    /**
     * Create an insert query
     *
     * @param string $tableName
     * @param array $values ["columnName" => "value"]
     * @return self
     * 
     * ```
     * Table::insert("users", ["username" => "CommandString", "password" => "123456"]);
     * 
     * // the query would look like 
     * "INSERT INTO users (users.username, users.password) VALUES (:ughaASD2188dg, :nlkiugh867KOPh)";
     * // before having the values binded
     * ```
     */
    public function insert(array $values): self
    {
        $this->reset();

        $columns = "";
        $ids = "";

        foreach ($values as $column => $value) {
            $columns .= "$column, ";

            $id = $this->generateId();
            $this->parameters[$id] = $value;
            $ids .= ":$id, ";
        }

        $this->query = sprintf($this->queryTemplates["INSERT"], $this->table, substr($columns, 0, -2), substr($ids, 0, -2));

        return $this;
    }

    /**
     * UPDATE Query parameters
     *
     * @param array $values ["columnName" => "value"]
     * @return self
     * 
     * ```
     * Table::insert("users", ["username" => "CommandString", "password" => "123456"]);
     * 
     * // the query would look like 
     * "UPDATE users SET users.username = :ughaASD2188dg AND password = :nlkiugh867KOPh";
     * // before having the values binded
     * ```
     */
    public function update(array $values): self
    {
        $this->reset();

        $this->query = sprintf($this->queryTemplates["UPDATE"], $this->table);

        foreach ($values as $column => $value) {
            $id = $this->generateId();

            $this->parameters[$id] = $value;

            $this->query .= (!str_contains($this->query, "SET")) ? " SET" : ",";
            $this->query .= sprintf($this->queryTemplates["PARAMETER"], $column, Operators::EQUAL_TO, $id);
        }

        return $this;
    }

    /**
     * Delete statement
     *
     * @return self
     */
    public function delete(): self
    {
        $this->reset();

        $this->query = sprintf($this->queryTemplates["DELETE"], $this->table);

        return $this;
    }

    /**
     * Executes query and returns the result
     *
     * @param int $fetchMode
     * @return mixed
     */
    public function execute(int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
        $driver = $this->driver;

        echo "\n\n".$this->query."\n\n";

        $stmt = (!empty($this->parameters)) ? $driver->prepare($this->query) : $driver->query($this->query);
        
        foreach ($this->parameters as $id => $value) {
            $stmt->bindValue($id, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll($fetchMode);
    }

    public function makeCustomQuery(string $name, ?Callable $handler = null): self
    {
        if (!isset($this->query)) {
            throw new Exception("No query has been built");
        }

        if (isset($this->customQueries[$name])) {
            throw new Exception("A custom query has already been built with the given name.");
        }

        $this->customQueries[$name] = [
            "parameters" => $this->parameters,
            "query" => $this->query,
            "handler" => $handler
        ];

        return $this;
    }

    public function runCustomQuery(string $name, mixed ...$args) {
        $this->reset();

        if (!isset($this->customQueries[$name])) {
            throw new Exception("No query exists under the name given.");
        }

        $query = $this->customQueries[$name];

        $this->query = $query["query"];

        $i = 0;

        foreach (array_keys($query["parameters"]) as $id) {
            $query["parameters"][$id] = $args[$i++];
        }

        $this->parameters = $query["parameters"];

        $results = $this->execute();

        return ($query["handler"] !== null) ? call_user_func($query["handler"], $results) : $results;
    }

    abstract protected function getTable(): string;

    abstract protected function getPrimaryKey(): string;
}