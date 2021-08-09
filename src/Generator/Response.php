<?php

namespace Zjwansui\EasyLaravel\Generator;


use Zjwansui\EasyLaravel\HttpCode\StatusCode;

/**
 * @OA\Schema(
 *     title="返回参数",
 *     required={"code","message"}
 * )
 *
 * Class Response
 * @package App\Http\Controllers\Response
 */
class Response extends BaseResponse
{
    /**
     * @OA\Property (
     *     description="业务状态码",
     *     format="int",
     *     example=200
     * )
     *
     * @var int
     */
    public int $code;

    /**
     * @OA\Property (
     *     description="业务错误信息",
     *     format="string",
     *     example="OK"
     * )
     * @var string
     */
    public string $message;


    public function __construct(string $message = "OK", int $code = StatusCode::SUCCESS_OK, $args = null)
    {
        parent::__construct($args);
        $this->code = $code;
        $this->message = $message;
    }

    public function __toString()
    {
        try {
            $data = json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            $data = '';
        }
        if (!is_string($data)){
            $data = '';
        }
        return $data;
    }


}
