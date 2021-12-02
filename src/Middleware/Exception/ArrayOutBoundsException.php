<?php


namespace Zjwansui\EasyLaravel\Middleware\Exception;


use Zjwansui\EasyLaravel\HttpCode\StatusCode;

class ArrayOutBoundsException extends \Exception
{
    public function __construct($message = "Undefined offset",
        $code = StatusCode::CLIENT_BAD_REQUEST, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
