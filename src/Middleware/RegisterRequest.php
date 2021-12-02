<?php

namespace Zjwansui\EasyLaravel\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use \Zjwansui\EasyLaravel\Generator\Response as ApiResponse;
use Illuminate\Support\Facades\Route;
use ReflectionParameter;
use Zjwansui\EasyLaravel\HttpCode\StatusCode;
use Zjwansui\EasyLaravel\Middleware\Exception\ClassPropertyException;
use Zjwansui\EasyLaravel\Middleware\Utils\ArrayOperation;
use Zjwansui\EasyLaravel\Middleware\Utils\ClassBuilder;

class RegisterRequest
{
    private ArrayOperation $arrayOperation;
    private ClassBuilder $classBuilder;

    public function __construct(ArrayOperation $arrayOperation, ClassBuilder $classBuilder)
    {
        $this->arrayOperation = $arrayOperation;
        $this->classBuilder = $classBuilder;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function handle(Request $request, Closure $next)
    {
        $currentRouteAction = Route::currentRouteAction();
        [$className, $methodName] = explode('@', $currentRouteAction);
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $parameters = $reflectionMethod->getParameters();
//        $parameter = $this->arrayOperation->setArray($parameters)->get(0);
        $parameter = current($parameters);
        $reflectionRequestClass = $parameter->getClass();
        if (!$reflectionRequestClass) {
            throw new \ReflectionException('error');
        }
        $reflectionRequestClassName = $reflectionRequestClass->getName();
        try {// 构造requestClass
            $requestClass = $this->classBuilder
                ->setClassName($reflectionRequestClassName)
                ->setClassData($request->input())
                ->build();
            //特殊类型限定判断
            $requestClassInstance = $reflectionRequestClass->newInstanceWithoutConstructor();
            $reflectionClass = new \ReflectionClass($requestClassInstance);
            $ruleName = $reflectionClass->getName();
            if (method_exists($ruleName, 'rules')) {
                $rules = (new $ruleName())->rules();
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }

            if ($requestClass instanceof \Zjwansui\EasyLaravel\Generator\Request) {
                app()->instance($reflectionRequestClassName, $requestClass);
            }
        } catch (ClassPropertyException $error) {//参数类型错误
            $apiResponse = new ApiResponse($error->getMessage(), StatusCode::CLIENT_BAD_REQUEST);
            return Response::json($apiResponse, StatusCode::CLIENT_BAD_REQUEST);
        } catch (Exception $e) {
            $apiResponse = new ApiResponse("Service Internet Error", StatusCode::SERVER_INTERNAL_ERROR);
            return Response::json($apiResponse, StatusCode::SERVER_INTERNAL_ERROR);
        }

        return $next($request);
    }

    private function filterParam (&$param)
    {
        foreach ($param as $k => $v) {
            if (is_null($v) || $v === '') {
                unset($param[$k]);
            }
        }
    }
}
