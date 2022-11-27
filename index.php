<?php

require_once __DIR__."/vendor/autoload.php";

use CommandString\Orm\Database\City;
use CommandString\Orm\Database\World;
use CommandString\Orm\Orm;
use CommandString\Pdo\Driver;

$driver = (new Driver())
	->withUsername("admin")
	->withPassword("password")
	->withDatabase("world")
	->withHost("127.0.0.1")
	->connect()
;

$orm = new Orm($driver);

$orm->build([
    "output-dir" => __DIR__."/src/Database",
    "namespace" => "CommandString\\Orm\\Database",
	"database" => "World"
]);

$database = (new World($driver));