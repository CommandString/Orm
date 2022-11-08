<?php

namespace CommandString\Orm\Builder;

use CommandString\Pdo\Driver;
use Exception;
use PDO;

class Builder {
    private Driver $driver;
    public function __construct(Driver $driver) {
        $this->driver = clone $driver;
    }
    public const TAB = "    ";

    /**
     * Builds table model
     *
     * @param string $tableName Name of the table you want to base the model off of
     * @param string $outputDirectory What directory to output the file in without the trailing slash
     * @return void
     */
    public function buildTableClass(string $tableName, string $outputDirectory = ""): void
    {
        $driver = $this->driver;
        $result = $driver->query("DESCRIBE {$tableName}");

        $columns = "";

        while ($row = $result->fetch(PDO::FETCH_OBJ)) {
            $columns .= BuilderFormats::format(BuilderFormats::NAME, $row->Field);

            if ($row->Key === "PRI") {
                $primaryKey = BuilderFormats::format(BuilderFormats::PRIMARY_KEY, $row->Field);
            }
        }

        if (!isset($primaryKey)) {
            throw new Exception("Was unable to find the primary key inside table $tableName");
        }

        $table = BuilderFormats::format(BuilderFormats::TABLE_NAME, $tableName);

        $classTemplate = "<?php".PHP_EOL.
        "class %s extends \CommandString\Orm\Table {".PHP_EOL. // Table name
            "%s".PHP_EOL. // Column Constants
            "%s". // Methods
        "}";

        file_put_contents("$outputDirectory/{$tableName}.php", sprintf($classTemplate, $tableName, $columns, $primaryKey.$table));
    }

    public function buildDatabaseClass(string $outputDirectory = ""): void
    {
        $driver = $this->driver;
        
        $result = $driver->query("SHOW TABLES");
        $tableNames = [];

        while ($row = $result->fetch()) {
            $tableNames[] = $row[0];
            
            if (!isset($databaseName)) {
                $explode = explode("_", array_keys($row)[0]);
                $databaseName = end($explode);
            }

            $this->buildTableClass($row[0], $outputDirectory);
        }
        
        $requireStatementTemplate = "require_once __DIR__.\"/%s.php\";".PHP_EOL;
        $tableObjectTemplate = "(new %s(\$driver)), ";
        $tableConstantTemplate = self::TAB."public const %s = \"%s\";".PHP_EOL;
        $requireStatements = $tableObjects = $tableConstants = "";

        foreach ($tableNames as $tableName) {
            $requireStatements .= sprintf($requireStatementTemplate, $tableName);
            $tableObjects .= sprintf($tableObjectTemplate, $tableName);
            $tableConstants .= BuilderFormats::format(BuilderFormats::NAME, $tableName);
        }

        $tableObjects = substr($tableObjects, 0, -2);

        $classTemplate = "<?php".PHP_EOL.
        "use CommandString\Pdo\Driver;".PHP_EOL.PHP_EOL.
        "%s".PHP_EOL. // require statements OR namespace
        "class %s extends \CommandString\Orm\Database {".PHP_EOL. // Database name
            "%s".PHP_EOL. // Table constants
            self::TAB."public function __construct(Driver \$driver) {".PHP_EOL.
            self::TAB.self::TAB."parent::__construct(%s);".PHP_EOL. // Table objects
            self::TAB."}".PHP_EOL.
        "}";

        file_put_contents("$outputDirectory/{$databaseName}.php", sprintf($classTemplate, $requireStatements, $databaseName, $tableConstants, $tableObjects));
    }
}