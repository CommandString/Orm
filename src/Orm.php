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

        $builder->table("city");
    }
}