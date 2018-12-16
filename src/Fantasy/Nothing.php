<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/9 10:45

 */

namespace Pamda\Fantasy;


class Nothing extends Maybe
{
    /**
     * @param callable $fn
     *
     * @return self
     */
    public function map(callable $fn)
    {
        return $this;
    }

    /**
     * @param Maybe $m
     *
     * @return self
     */
    public function ap(Maybe $m)
    {
        return $this;
    }

    /**
     * Nothing 的 chain 并不返回值，这样和 Just 的行为也不一致呀
     *
     * @param callable $fn
     *
     * @return self
     */
    public function chain(callable $fn)
    {
        return $this;
    }
}