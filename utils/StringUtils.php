<?php

namespace CommandString\Utils;
use InvalidArgumentException;
class StringUtils {
    public static function getCharactersBetween(string $startCharacter, string $endCharacter, string $string, bool $case_sensitive = true) {
        $start = ($case_sensitive) ? strpos($string, $startCharacter) : stripos($string, $startCharacter);
        $end = ($case_sensitive) ? strrpos($string, $endCharacter) : strripos($string, $endCharacter);
        
        if ($start === false || $end === false) {
            throw new InvalidArgumentException("Unable to find the start and/or the end position!");
        }

        return substr($string, $start, ($end-$start)+1);
    }
}