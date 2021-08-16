<?php


namespace Zjwansui\EasyLaravel\Model\Helper;


use Illuminate\Database\Eloquent\Collection as BaseCollection;

class Collection extends BaseCollection
{
    public function walk(callable $callback, $userData = null)
    {
        array_walk($this->items, $callback, $userData);
        return $this;
    }
}
