<?php

namespace CommandString\Orm;

use CommandString\Orm\Traits\NeedPdoDriver;
use LogicException;
use Nette\PhpGenerator\PhpFile;
use PDO;

class Builder {
    private array $options = [];
    
    use NeedPdoDriver;
    public const OUTPUT_DIR = "output_dir";
    public const NAMESPACE = "namespace";
    public const DATABASE = "database";

    /**
     * Build table
     *
     * @param string $table
     * @return void
     */
    private function table(string $table): bool
    {
        $this->checkReqOptions();

        $file = new PhpFile();
        $loweredName = preg_replace("/[-]/", "_", strtolower($table));
        $properName = ucfirst($loweredName);
        
        $namespace = $file->addNamespace($this->getOption(self::NAMESPACE));

        $class = $namespace->addClass($properName);
        $class->setExtends("\CommandString\Orm\Database\Table");

        $this->driver->query("DESCRIBE {$table}")->execute();

        while ($row = $this->driver->fetch(PDO::FETCH_OBJ)) {
            $class->addConstant(preg_replace("/[-]/", "_", strtoupper($row->Field)), strtolower("{$table}.{$row->Field}"));
        }

        $file->addNamespace($namespace);

        return (file_put_contents($this->getOption(self::OUTPUT_DIR)."/$properName.php", (string)$file) !== false);
    }

    /**
     * Build tables
     *
     * @param string|array ...$tables
     * @return array returns list of tables that were successfully built
     */
    private function tables(string|array ...$tables): array
    {
        $this->driver->query("SHOW TABLES")->execute();

        $tablesBuilt = [];

        foreach ($this->driver->fetchAll() as $row) {
            if (empty($tables) || in_array(strtolower($row[0]), $tables)) {
                if ($this->table($row[0])) {
                    $tablesBuilt[] = $row[0];
                }
            }
        }

        return $tablesBuilt;
    }

    /**
     * Build database
     *
     * @return void
     */
    public function build()
    {
        $this->checkReqOptions();

        $database = $this->getOption(self::DATABASE);

        $file = new PhpFile();
        $loweredName = strtolower($database);
        $properName = ucfirst($loweredName);

        $tables = $this->tables();
        
        $file = new PhpFile();
        $namespace = $file->addNamespace($this->getOption(self::NAMESPACE));
        $class = $namespace->addClass($properName);
        $class->setExtends("\CommandString\Orm\Database");

        foreach ($tables as $table) {
            $class->addConstant(strtoupper($table), strtolower($table));
        }

        $file->addNamespace($namespace);

        file_put_contents($this->getOption(self::OUTPUT_DIR)."/$properName.php", (string)$file);
    }

    /**
     * @throws LogicException
     * @return void
     */
    private function checkReqOptions(): void
    {
        $requiredOptions = [
            self::OUTPUT_DIR,
            self::NAMESPACE,
            self::DATABASE
        ];

        foreach ($requiredOptions as $option) {
            if (!in_array($option, array_keys($this->options))) {
                throw new LogicException("You must set $option before using this method!");
            }
        }
    }
    
    /**
     * Configure an option
     *
     * @param string $option name of the option
     * @param mixed $value value you want to the option to be
     * @return self
     */
    public function setOption(string $option, mixed $value): self
    {
        $this->options[$option] = $value;
        
        return $this;
    }
    
    /**
     * Get an option
     *
     * @param string $option name of option
     * @return mixed value of the option
     */
    public function getOption(string $option): mixed
    {
        return $this->options[$option] ?? null;
    }
}
