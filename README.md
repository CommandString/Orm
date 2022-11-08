# TEST CODE #
```php
<?php

require __DIR__."/vendor/autoload.php";

use CommandString\Orm\Builder\Builder;
use CommandString\Pdo\Driver;

(new Driver(true)) // create a PDO driver
    ->withUsername("admin")
    ->withPassword("password")
    ->withDatabase("world")
    ->connect()
;

$builder = new Builder(Driver::get()); // create a builder

$builder->buildDatabaseClass("./world"); // generates database class along with tables

require_once "./world/world.php"; // require database class generated

$worldDatabase = new world(Driver::get()); // create instance of database driver
$cityTable = $worldDatabase->getTable(world::CITY); // get table from database driver
```