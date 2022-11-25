<?php

require_once __DIR__."/vendor/autoload.php";

use CommandString\Orm\Database\City;
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
    "output" => __DIR__."/src/Database",
    "namespace" => "CommandString\\Orm\\Database"
]);

$city = (new City($driver));

$query = $city->select()->columns([City::ID => "city_id"], City::NAME)->limit(5)->where(City::ID, 5);

echo "$query\n\n";

$results = $query->execute()->fetchAll(PDO::FETCH_OBJ);

var_dump($results);