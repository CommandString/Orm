<?php

namespace CommandString\Utils;

use stdClass;

class ArrayUtils {
    public static function toStdClass(array &$array)
    {
        $stdClass = new stdClass();

        $toStdClass = function (array &$array, $toStdClass) use ($stdClass) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    foreach (array_keys($value) as $valueKey) {
                        if (!is_int($valueKey)) {
                            $value = $toStdClass($value);
                        }
                    }
                }

                $stdClass->$key = $value;
            }

            $array = $stdClass;
        };

        $toStdClass($array, $toStdClass);
    }
}