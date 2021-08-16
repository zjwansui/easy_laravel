<?php

namespace Zjwansui\EasyLaravel\Model\Page;

/**
 * @OA\Schema (
 *     title="分页",
 * )
 * Class Pagination
 */
class Pagination
{

    /**
     * @OA\Property (
     *     description="总个数",
     *     format="int"
     * )
     */
    public int $total;
    /**
     * @OA\Property (
     *     description="当前页",
     *     format="int"
     * )
     */
    public int $page;
    /**
     * @OA\Property (
     *     description="每页个数",
     *     format="int"
     * )
     */
    public int $pageSize;

}