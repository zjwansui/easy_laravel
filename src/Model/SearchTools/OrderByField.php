<?php

namespace Zjwansui\EasyLaravel\Model\SearchTools;

class OrderByField
{

    private const DESC = 'desc';
    private const ASC = 'asc';

    public string $field;
    public string $sort;

    public function __construct($field, $sort)
    {
        $this->field = $field;
        $this->sort = $sort;
    }

    public static function setDescOrderFiled($field): OrderByField
    {
        return new self($field, self::DESC);
    }

    public static function setAscOrderFiled($field): OrderByField
    {
        return new self($field, self::ASC);

    }


}
