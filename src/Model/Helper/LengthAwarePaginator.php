<?php

namespace Zjwansui\EasyLaravel\Model\Helper;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Zjwansui\EasyLaravel\Model\Page\Page;
use Zjwansui\EasyLaravel\Model\Page\Pagination;

class LengthAwarePaginator extends Paginator
{
    public function toArray(array $fields = null): array
    {
        $defaultFields = [
            'total', 'per_page', 'current_page', 'last_page',
            'next_page_url', 'prev_page_url', 'from', 'to', 'data'
        ];

        $fields = is_array($fields) ? array_intersect($fields, $defaultFields) : $defaultFields;

        $methods = [
            'total' => 'total',
            'per_page' => 'perPage',
            'current_page' => 'currentPage',
            'last_page' => 'lastPage',
            'next_page_url' => 'nextPageUrl',
            'prev_page_url' => 'previousPageUrl',
            'from' => 'firstItem',
            'to' => 'lastItem',
        ];

        $data = [];

        foreach ($fields as $field) {
            if ($field === 'data') {
                $data[$field] = $this->items->toArray();
            } else {
                $data[$field] = $this->{$methods[$field]}();
            }
        }

        return $data;
    }

    public function pageStyle(): array
    {
        $pagination = $this->toArray(['total', 'per_page', 'current_page']);

        $list = $this->items->toArray();
        return [
            'data' => $list,
            'pagination' => $pagination,
        ];
    }

    public function toPage(): Page
    {
        $page = new Page();
        $page->data = $this->items->toArray();
        $page->pagination = new Pagination();
        $page->pagination->pageSize = $this->perPage;
        $page->pagination->page = $this->currentPage;
        $page->pagination->total = $this->total;
        return $page;
    }
}
