<?php


namespace Zjwansui\EasyLaravel\Middleware\Exception;


use Throwable;
use Zjwansui\EasyLaravel\HttpCode\StatusCode;

class ClassPropertyException extends \Exception
{
    public function __construct ($message = "class property is error",
        $code = StatusCode::CLIENT_BAD_REQUEST, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
