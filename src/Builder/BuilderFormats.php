<?php

namespace CommandString\Orm\Builder;

enum BuilderFormats {
    case NAME;
    case PRIMARY_KEY;
    case TABLE_NAME;

    public static function format(self $format, string $toFormat): string
    {
        $formatted = "";

        $getMethod = Builder::TAB."public function %s(): string".PHP_EOL.
            Builder::TAB."{".PHP_EOL.
                Builder::TAB.Builder::TAB."return \"%s\";".PHP_EOL.
                Builder::TAB.
            "}".PHP_EOL
        ;
        
        $name = Builder::TAB."public const %s = \"%s\";";

        switch ($format) {
            case self::NAME:
                $formatted = strtoupper($toFormat);
                $formatted = str_replace(" ", "_", $formatted);
                $formatted = preg_replace("/[^a-zA-Z\d_]/", "", $formatted);
                $formatted = sprintf($name, $formatted, $toFormat).PHP_EOL;
            break;
            case self::PRIMARY_KEY:
                $formatted = sprintf($getMethod, "getPrimaryKey", $toFormat);
            break;
            case self::TABLE_NAME:
                $formatted = sprintf($getMethod, "getTable", $toFormat);
            break;
        }

        return $formatted;
    }
}