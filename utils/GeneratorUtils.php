<?php

namespace CommandString\Utils;

class GeneratorUtils {
    public static function uuid(int $length = 16, array $characters = []): string
    {
        $characters = empty($characters) ? array_merge(range("A", "Z"), range("a", "z"), range(0, 9)) : $characters;
        $id = "";

        for ($i = 0; $i <= $length; $i++) {
            $id .= $characters[rand(0, count($characters)-1)];
        }

        return $id;
    }
}