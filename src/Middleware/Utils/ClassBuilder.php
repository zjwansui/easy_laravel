<?php


namespace Zjwansui\EasyLaravel\Middleware\Utils;


use Illuminate\Support\Facades\Log;
use ReflectionException;
use TypeError;
use Zjwansui\EasyLaravel\Middleware\Exception\ArrayOutBoundsException;
use Zjwansui\EasyLaravel\Middleware\Exception\ClassPropertyException;


class ClassBuilder
{
    private string $className;
    private ArrayOperation $arrayOperation;

    public function __construct (ArrayOperation $arrayOperation)
    {
        $this->arrayOperation = $arrayOperation;
    }

    public function newInstance(): self
    {
        return new static(new ArrayOperation());
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;
        return $this;
    }

    public function setClassData(array $classData): self
    {
        $this->arrayOperation->setArray($classData)->filterNull();
        return $this;
    }


    /**
     * @return object
     * @throws ReflectionException
     * @throws ClassPropertyException
     */
    public function build(): object
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $static = $reflectionClass->newInstanceWithoutConstructor();
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            //忽略没有声明类型参数
            if (!$property->hasType()) {
                continue;
            }
            $propertyType = $property->getType();

            //校验必传参数
            if (!$propertyType->allowsNull()) {
                try {
                    $this->arrayOperation->get($property->getName());
                } catch (ArrayOutBoundsException $e) {
                    throw new ClassPropertyException("property: ".$property->getName(). " is required, but not set");
                }
            }

            try {
                //包含子文档的数据
                if (!$propertyType->isBuiltin()) {
                    $childStatic = $this->newInstance()->setClassName($propertyType->getName())
                        ->setClassData($this->arrayOperation->get($property->getName()))
                        ->build();
                    $property->setValue($static, $childStatic);
                    continue;
                }

                //避免int->string进行隐式转换
                if ("int" === $propertyType->getName() && !is_int($this->arrayOperation->get($property->getName()))) {
                    throw new ClassPropertyException("property: " .$property->getName(). " type is error, please check it");
                }

                //避免string->int进行隐式转换
                if ("string" === $propertyType->getName() && !is_string($this->arrayOperation->get($property->getName()))) {
                    throw new ClassPropertyException("property: " .$property->getName(). " type is error, please check it");
                }

                $property->setValue($static, $this->arrayOperation->get($property->getName()));
            }catch (TypeError $error) {
                throw new ClassPropertyException("property: " .$property->getName(). " type is error, please check it");
            }catch (ArrayOutBoundsException $exception) {
                //可能存在非必传参数未设置，抛出此异常，不用处理，跳过
            }
        }

        return $static;
    }

    /**
     * @return object
     * @throws ReflectionException
     */
    public function buildIgnoreErrorType(): object
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $static = $reflectionClass->newInstanceWithoutConstructor();
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            //忽略没有声明类型参数
            if (!$property->hasType()) {
                continue;
            }
            $propertyType = $property->getType();


            //校验必传参数
            if (!$propertyType->allowsNull()) {
                try {
                    $this->arrayOperation->get($property->getName());
                } catch (\Exception $e) {
                    continue;
                }
            }


            try {
                //包含子文档的数据
                if (!$propertyType->isBuiltin()) {
                    $childData = $this->arrayOperation->get($property->getName());
                    $childStatic = $this->newInstance()->setClassName($propertyType->getName())
                        ->setClassData($childData)
                        ->buildIgnoreErrorType();

                    $property->setValue($static, $childStatic);
                    continue;
                }

                //避免int->string进行隐式转换
                if ("int" === $propertyType->getName() && !is_int($this->arrayOperation->get($property->getName()))) {
                    continue;
                }

                //避免string->int进行隐式转换
                if ("string" === $propertyType->getName() && !is_string($this->arrayOperation->get($property->getName()))) {
                    continue;
                }

                $property->setValue($static, $this->arrayOperation->get($property->getName()));
            }catch (TypeError $error) {
                continue;
            }catch (ArrayOutBoundsException $exception) {
                //可能存在非必传参数未设置，抛出此异常，不用处理，跳过
            }
        }

        return $static;
    }
}
