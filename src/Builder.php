<?php

namespace CommandString\Orm;

use CommandString\Orm\Traits\NeedPdoDriver;
use Nette\PhpGenerator\PhpFile;
use PDO;

class Builder {
    private array $options = [];
    
    use NeedPdoDriver;

    public function table(string $table) {
        $this->checkReqOptions();

        $file = new PhpFile();
        $loweredName = preg_replace("/[-]/", "_", strtolower($table));
        $properName = ucfirst($loweredName);
        
        $namespace = $file->addNamespace($this->getOption("namespace"));

        $class = $namespace->addClass($properName);
        $class->setExtends("\CommandString\Orm\Database\Table");

        $this->driver->query("DESCRIBE {$table}")->execute();

        while ($row = $this->driver->fetch(PDO::FETCH_OBJ)) {
            $class->addConstant(preg_replace("/[-]/", "_", strtoupper($row->Field)), strtolower("{$table}.{$row->Field}"));
        }

        $file->addNamespace($namespace);

        file_put_contents($this->getOption("output-dir")."/$properName.php", (string)$file);
    }

    public function tables(string|array ...$tables): array
    {
        $this->driver->query("SHOW TABLES")->execute();

        $tablesBuilt = [];

        foreach ($this->driver->fetchAll() as $row) {
            if (empty($tables) || in_array($row[0], $tables)) {
                $this->table($row[0]);
                $tablesBuilt[] = $row[0];
            }
        }

        return $tablesBuilt;
    }

    public function database(string $database, array $tables = [])
    {
        $this->checkReqOptions();

        $file = new PhpFile();
        $loweredName = strtolower($database);
        $properName = ucfirst($loweredName);

        $tables = $this->tables();
        
        $file = new PhpFile();
        $namespace = $file->addNamespace($this->getOption("namespace"));
        $class = $namespace->addClass($properName);
        $class->setExtends("\CommandString\Orm\Database\Database");

        foreach ($tables as $table) {
            $class->addConstant(strtoupper($table), strtolower($table));
        }

        $file->addNamespace($namespace);

        file_put_contents($this->getOption("output-dir")."/$properName.php", (string)$file);
    }

    private function checkReqOptions(): void
    {
        $requiredOptions = [
            "output-dir",
            "namespace"
        ];

        foreach ($requiredOptions as $option) {
            if (!in_array($option, array_keys($this->options))) {
                throw new \Exception("You must set $option before using this method!");
            }
        }
    }
    
    public function setOption(string $option, mixed $value): self
    {
        $this->options[$option] = $value;
        
        return $this;
    }
    
    public function getOption(string $option): mixed
    {
        return $this->options[$option] ?? null;
    }
}