<?php

namespace Zjwansui\EasyLaravel\Model\SearchTools;


class OrderByFields
{
    public array $orderByFields;

    public function __construct(OrderByField $byField)
    {
        $this->orderByFields[] = $byField;
    }

    public function setOrder(OrderByField $byField)
    {
        $this->orderByFields[] = $byField;
    }

}
