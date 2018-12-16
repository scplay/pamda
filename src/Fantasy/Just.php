<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/9 10:45

 */

namespace Pamda\Fantasy;


class Just extends Maybe
{
    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param callable $fn
     *
     * @return Just
     */
    public function map(callable $fn)
    {
        return new self($fn($this->value));
    }

    /**
     * @param Maybe $m
     *
     * @return Just
     */
    public function ap(Maybe $m)
    {
        return $m->map($this->value);
    }


    /**
     * @param callable $fn
     *
     * @return Just
     */
    public function chain(callable $fn)
    {
        return $fn($this->value);
    }
}