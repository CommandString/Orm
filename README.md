# commandstring/orm #

A low level PDO orm

# Requirements: #
- PHP 8.1<=
- commandstring/pdo
- PDO extension enabled
- MySQL database connection
- PHP OOP Knowledge
- MySQL knowledge

# Todo: #
- Tests and possible refactoring
- Move commandstring/utils to its own repository
- Add [UNION](https://www.w3schools.com/mysql/mysql_union.asp)
- Add [GROUP BY](https://www.w3schools.com/mysql/mysql_groupby.asp)
- Add [HAVING](https://www.w3schools.com/mysql/mysql_having.asp)
- Add [EXISTS](https://www.w3schools.com/mysql/mysql_exists.asp)

# How to use #

## Create PDO Driver object ##

```php
$driver = (new \CommandString\Pdo\Driver())
	->withUsername("admin")
	->withPassword("password")
	->withDatabase("database")
	->withHost("127.0.0.1")
	->connect()
;
```

## Build your database ##

```php
(new Builder($driver))
    ->setOption(Builder::NAMESPACE, "CommandString\\Database") // change
    ->setOption(Builder::OUTPUT_DIR, __DIR__."/database") // change
    ->database("database")
;
```

## Initialize Database

```php
$database = (new Database($driver));
```

## Build MySQL query programmatically and execute ##
```php
$query = $database->getTable(Database::TABLENAME)
    ->select()
    ->columns(TableName::COLUMNNAME, TableName::COLUMNNAME)
    ->limit(2)
->execute();

var_dump($query->fetchAll(PDO::FETCH_OBJ));
```

---

# Don't want to generate classes for your database?

You can create instances of the statement type and execute those manually

```php
/**
 * @var \CommandString\Orm\Statements\Select
 */
$selectQuery = new Select($driver)->columns("username")->from("users")->where("id", "=", 5);

echo $selectQuery; // output: SELECT username FROM users WHERE id = :random-id-here

/**
 * @var \PDOStatement
 */
$results = $selectQuery->execute();
```

# Building Statements

## Select

```php

(new Select($driver))
    ->from("table")
    ->columns(["column" => "column_alias_name"], "column2")
    ->orderBy("column", "ASC")
    ->limit(20)
    ->offset(30)
;
```

## Insert

```php
(new Insert($driver))
    ->into("table")
    ->value("column", "value")
    ->values(["column2" => "value", "column3" => "value"])
;
```

## Update

```php
(new Update($driver))
    ->table("table")
    ->set("column", "newValue")
    ->where("column", "=", "value")
;
```

## Delete

```php
(new Delete($driver))
    ->from("table")
    ->where("column", "=", "value")
```
# Additional Notes

## Proper where usage
```php
// ...
->where("column", "=", "value")
->where("column", "IN", [1, 5, "hi"])
->where("column", "IN", [(new Select($driver))->from("table")->columns("column")])
->where("column", "BETWEEN", [0, 5])
```