<?php

namespace Zjwansui\EasyLaravel\Generator;


/**
 * @OA\Schema(
 *     title="请求参数",
 *     required={}
 * )
 *
 * Class Request
 * @package App\Http\Controllers\Request
 */
class Request
{
    /**
     * @param $class
     * @return static
     */
    public static function cast($class): Request
    {
        return $class;
    }

    /**
     * @throws \JsonException
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), true, 512, JSON_THROW_ON_ERROR);
    }
}
