<?php

require_once __DIR__."/vendor/autoload.php";

use CommandString\Orm\Database\Accounts;
use CommandString\Orm\Database\Books_over_coffee;
use CommandString\Orm\Orm;
use CommandString\Pdo\Driver;

$driver = (new Driver())
	->withUsername("admin")
	->withPassword("password")
	->withDatabase("books_over_coffee")
	->withHost("127.0.0.1")
	->connect()
;

$orm = new Orm($driver);

$orm->build([
    "output" => __DIR__."/src/Database",
    "namespace" => "CommandString\\Orm\\Database",
	"database" => "Books_over_coffee"
]);

$database = (new Books_over_coffee($driver))->initializeDatabase();

$query = $database->tables->accounts->select()->columns(Accounts::ID, accounts::EMAIL)->limit(2)->execute();

var_dump($query->fetchAll(PDO::FETCH_OBJ));