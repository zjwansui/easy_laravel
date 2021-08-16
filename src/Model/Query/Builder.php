<?php

namespace Zjwansui\EasyLaravel\Model\Query;

use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{

    public function select($columns = ['*'])
    {
        if (is_array($columns) && is_string(key($columns))) {

            $_columns = [];
            foreach ($columns as $alias => $fields) {
                foreach ($fields as $field) {
                    $_columns[] = "$alias.$field";
                }
            }
            $columns = $_columns;
        }

        return parent::select($columns);
    }

}
