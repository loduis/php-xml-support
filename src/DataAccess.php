<?php

namespace XML\Support;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use function Php\{
    str_camel,
    str_snake,
    array_pull,
    array_has
};
use Php\Arrayable;
use Traversable;

abstract class DataAccess implements ArrayAccess, IteratorAggregate, Arrayable, Countable
{
    use AttributeAccess;
    use PropertyAccessor;

    protected array $data = [];

    public function __construct($data = [])
    {
        foreach ($this->prepareData($data) as $key => $value) {
            $this->$key = $value;
        }
    }

    protected function prepareData($data)
    {
        $values = [];
        if ($this->prepareFillableTypes()) {
            foreach ($this->fillable as $key => $type) { // not usign array_flip for keep order
                if (array_has($data, $key)) {
                    $values[$key] = array_pull($data, $key);
                } elseif (($snake = str_snake($key)) != $key) {
                    $values[$key] = array_pull($data, $snake);
                }
            }
        }
        $values += $data;

        return $values;
    }


    /**
     * Check if a piece of data is bound to the view.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        $key = str_camel($key);

        return array_key_exists($key, $this->data);

    }

    /**
     * Get a piece of data from the view.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $key = str_camel($key);

        return $this->propertyGetterAccessor($key);
    }

    /**
     * Set a piece of data on the view.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $key = str_camel($key);

        $this->propertySetterAccessor($key, $value);
    }

    /**
     * Remove a piece of bound data from the view.
     *
     * @param  string  $key
     */
    public function __unset($key)
    {
        $key = str_camel($key);

        unset($this->data[$key]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }

    public function __debugInfo(): array
    {
        return [
            '@attributes' => $this->attributes(),
            '@data' => $this->toArray()
        ];
    }

    public function count(): int
    {
        return count($this->data);
    }
}
