<?php

namespace CommandString\Orm;

use CommandString\Orm\Traits\NeedPdoDriver;
use Composer\Autoload\ClassLoader;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use PDO;

class Builder {
    private array $options = [];
    
    use NeedPdoDriver;

    public function Table(string $table) {
        $this->checkReqOptions();

        $file = new PhpFile();
        $loweredName = strtolower($table);
        $properName = ucfirst($loweredName);
        
        if ($this->getOption("namespace") !== null) {
            $namespace = $file->addNamespace($this->getOption("namespace"));
        }

        $class = (isset($namespace)) ? $namespace->addClass($properName) : $file->addClass($properName);
        $class->setExtends("\CommandString\Orm\Table");
        $class->addProperty("name", $loweredName)->setType("string")->setVisibility("public");

        $this->driver->query("DESCRIBE {$table}")->execute();

        while ($row = $this->driver->fetch(PDO::FETCH_OBJ)) {
            $class->addConstant(strtoupper($row->Field), strtolower("{$table}.{$row->Field}"));
        }

        if (isset($namespace)) {
            $file->addNamespace($namespace);
        }

        file_put_contents($this->getOption("output")."/$properName.php", (string)$file);
    }

    private function checkReqOptions(): void
    {
        $requiredOptions = [
            "output"
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