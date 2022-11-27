# commandstring/orm #

A low level PDO orm

# Requirements: #
    - PHP 8.1<=
    - commandstring/pdo
    - PDO extension enabled
    - Mysql database connection
    - PHP OOP Knowledge
    - MySQL knowledge

# Todo: #
    - More testing
    - More code refactoring before release
    - Packages built here will be split off into their own package and then redded as a dependency

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
$database = (new Books_over_coffee($driver))->initializeDatabase();
```

## Use intellisense to build SQL queries ##
```php
$query = $database->tables->accounts
    ->select()
    ->columns(Accounts::ID, accounts::EMAIL)
    ->limit(2)
->execute();

var_dump($query->fetchAll(PDO::FETCH_OBJ));
```

# CommandString/Utils #
Basic utility functions for PHP

## ArrayUtils::toStdClass(array &$array) ##
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