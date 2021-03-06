<?php

namespace Zjwansui\EasyLaravel\Model\Helper;

use Illuminate\Pagination\Paginator;

/**
 * @method Builder where(string | \Closure | array $column, string $operator = null, $value = null, $boolean = 'and')
 * @method Builder orWhere(string | \Closure | array $column, string $operator = null, $value = null, $boolean = 'and')
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{

    /**
     * @inheritdoc
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    public function rawSql(): string
    {
        $sql = $this->toSql();
        $bindings = $this->getBindings();
        $sqlArray = explode('?', $sql);
        $rawSql = array_shift($sqlArray);
        foreach ($bindings as $value) {
            if (!$sqlArray) break;
            $rawSql .= (is_int($value) ? $value : "'$value'") . array_shift($sqlArray);
        }
        return $rawSql;
    }

    public function __toString()
    {
        return $this->rawSql();
    }
}
