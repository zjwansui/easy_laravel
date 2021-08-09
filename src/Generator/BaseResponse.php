<?php


namespace Zjwansui\EasyLaravel\Generator;


use ReflectionClass;
use Zjwansui\EasyLaravel\Tools\DocParserFactory;

/**
 * Class BaseResponse
 * @package App\Http\Controllers\Response
 */
class BaseResponse
{

    public function __construct($args = null)
    {
        $ref = new ReflectionClass($this);
        $doc = $ref->getDocComment();
        $attrs = DocParserFactory::getInstance()->getAttrs($doc);
        if (!is_null($args)) {
            foreach ($args as $k => $arg) {
                if (in_array($k, $attrs, true)) {
                    $this->{$k} = $arg;
                }
            }
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $name;
    }

    public function __isset($name)
    {

    }

}
