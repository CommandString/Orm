<?php

namespace CommandString\Orm;

use CommandString\Orm\Traits\NeedPdoDriver;

class Orm {
    use NeedPdoDriver;

    public function build(array $options = []) {
        $builder = new Builder($this->driver);

        foreach ($options as $name => $value) {
            $builder->setOption($name, $value);
        }

        if ($builder->getOption("database") !== null) {
            $builder->database($builder->getOption("database"));
        } else if ($builder->getOption("tables") !== null) {
            $builder->tables($builder->getOption("tables"));
        }
    }
}