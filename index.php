<?php

require_once __DIR__."/vendor/autoload.php";

use CommandString\Orm\Builder;
use CommandString\Orm\Database\Test\Students;
use CommandString\Orm\Database\Test\Testing;
use CommandString\Orm\Operators;
use CommandString\Pdo\Driver;
use CommandString\Utils\GeneratorUtils;

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