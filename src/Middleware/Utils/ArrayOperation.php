<?php


namespace Zjwansui\EasyLaravel\Middleware\Utils;




use Zjwansui\EasyLaravel\Middleware\Exception\ArrayOutBoundsException;

class ArrayOperation
{
    private array $array;

    public function setArray(array $array): self
    {
        $this->array = $array;
        return $this;
    }

    public function newInstance(): self
    {
        return new static();
    }

    /**
     * @param mixed $key
     * @param null $defaultValue
     * @return mixed
     */
    public function get($key = null, $defaultValue = null)
    {
        if (!isset($this->array[$key]) && null === $defaultValue) {
            throw new ArrayOutBoundsException("key: " .$key. " not isset");
        }

        if (!isset($this->array[$key]) && null !== $defaultValue) {
            return $defaultValue;
        }

        return $this->array[$key];
    }

    public function filterNull(): self
    {
        $this->array = array_filter($this->array, static function ($v, $k) {
            return $k !== null && $k !== "" && $v !== null;
        }, ARRAY_FILTER_USE_BOTH);
        return $this;
    }

    public function convertInt(): self
    {
        $this->array = array_map(static function ($v) {
            return (int)$v;
        }, $this->array);
        return $this;
    }

    public function unique(): self
    {
        $this->array = array_unique($this->array);
        return $this;
    }

    public function addPrefixForEach(string $prefix): self
    {
        $this->array = array_map(static function ($item) use($prefix){
            return $prefix . $item;
        }, $this->array);
        return $this;
    }

    public function mergeRecursive(array $array): self
    {
        $this->array = array_merge_recursive($this->array, $array);
        return $this;
    }

    public function merge(array $array): self
    {
        $this->array = array_merge($this->array, $array);
        return $this;
    }

    public function keys(): self
    {
        $this->array = array_keys($this->array);
        return $this;
    }

    public function values (): self
    {
        $this->array = array_values($this->array);
        return $this;
    }

    public function kSort(): self
    {
        ksort($this->array);
        return $this;
    }

    public function getArray(): array
    {
        return $this->array;
    }

}
