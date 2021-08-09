<?php


namespace Zjwansui\EasyLaravel\Tools;


class DocParserFactory
{
    private static $p;

    private function __construct()
    {
    }

    public static function getInstance(): DocParser
    {
        if (is_null(self::$p)) {
            self::$p = new DocParser();
        }
        return self::$p;
    }

}
