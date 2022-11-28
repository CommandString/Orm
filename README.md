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
- Add [DELETE](https://www.w3schools.com/mysql/mysql_delete.asp) statement
- Add [NOT and OR](https://www.w3schools.com/mysql/mysql_and_or.asp)
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
	->withDatabase("books_over_coffee")
	->withHost("127.0.0.1")
	->connect()
;
```

## Create ORM object ##

```php
$orm = new Orm($driver);

$orm->build([
    "output" => __DIR__."/src/Database", // change this
    "namespace" => "CommandString\\Orm\\Database", // change this
	"database" => "Books_over_coffee"
]);
```

## Initialize Database

```php
$database = (new Books_over_coffee($driver));
```

## Build MySQL query programmatically and execute ##
```php
$query = $database->tables->accounts
    ->select()
    ->columns(Accounts::ID, accounts::EMAIL)
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
    ->where("column", "value")
;
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

# CommandString/Utils #
Basic utility functions for PHP

## ArrayUtils::toStdClass() ##
```php
$users = [
    "value" => [
        "users" => [
            [
                "username" => "user",
                "password" => "********",
                "email" => "user@example.com"
            ]
        ]
    ],
    "token" => "********************************"
];

$users = ArrayUtils::toStdClass($users);

var_dump($users);
/* output
object(stdClass)#2 (2) {
    ["value"]=>
    object(stdClass)#4 (1) {
        ["users"]=>
        object(stdClass)#5 (1) {
        ["0"]=>
        object(stdClass)#6 (3) {
            ["username"]=>
            string(4) "user"
            ["password"]=>
            string(8) "********"
            ["email"]=>
            string(16) "user@example.com"
        }
        }
    }
    ["token"]=>
    string(32) "********************************"
}
*/
```
