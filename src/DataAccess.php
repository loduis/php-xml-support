<?php

namespace XML\Support;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Illuminate\Support\{
    Str,
    Arr
};
use Illuminate\Contracts\Support\Arrayable;
use Traversable;

abstract class DataAccess implements ArrayAccess, IteratorAggregate, Arrayable, Countable
{
    use AttributeAccess;
    use PropertyAccessor;

    protected $data = [];

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
                if (Arr::has($data, $key)) {
                    $values[$key] = Arr::pull($data, $key);
                } elseif (($snake = Str::snake($key)) != $key) {
                    $values[$key] = Arr::pull($data, $snake);
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
        $key = Str::camel($key);

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
        $key = Str::camel($key);

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
        $key = Str::camel($key);

        $this->propertySetterAccessor($key, $value);
    }

    /**
     * Remove a piece of bound data from the view.
     *
     * @param  string  $key
     */
    public function __unset($key)
    {
        $key = Str::camel($key);

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
