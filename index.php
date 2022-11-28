<?php

require_once __DIR__."/vendor/autoload.php";

use CommandString\Orm\Builder;
use CommandString\Orm\Database\Test\Classes;
use CommandString\Orm\Database\Test\Teachers;
use CommandString\Orm\Database\Test\Testing;
use CommandString\Orm\Operators;
use CommandString\Pdo\Driver;

$driver = (new Driver())
	->withUsername("admin")
	->withPassword("password")
	->withDatabase("testing")
	->withHost("127.0.0.1")
	->connect()
;

$builder = new Builder($driver);

$builder
	->setOption(Builder::NAMESPACE, "CommandString\\Orm\\Database\\Test")
	->setOption(Builder::OUTPUT_DIR, __DIR__."/src/Database/Test")
	->database("testing")
;

$database = new Testing($driver);

$results = $database->tables->classes
	->select()
	->columns([Teachers::F_NAME => "first_name"], [Teachers::L_NAME => "last_name"], Classes::NAME)
	->leftJoin(Testing::TEACHERS)
	->on(Teachers::ID, Classes::TEACHER_ID)
	->where(Classes::ID, Operators::EQUAL_TO, 1)
;

echo $results;

var_dump($results->execute()->fetch(PDO::FETCH_OBJ));