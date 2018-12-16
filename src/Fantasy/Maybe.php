<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/9 10:45

 */

namespace Pamda\Fantasy;


use Pamda\Fantasy\Contracts\Monad;


/**
 * Class Maybe - takes care of “null” or “undefined” values
 *
 * @package Pamda\Fantasy
 */
class Maybe implements Monad
{
    /**
     * @param $value
     *
     * @return Just
     */
    public static function of($value)
    {
        return self::Just($value);
    }

    /**
     * @param $value
     *
     * @return Just
     */
    public static function Just($value)
    {
        return new Just($value);
    }

    /**
     * @param $x
     *
     * @return Just|Nothing
     */
    public function __invoke($x)
    {
        return is_null($x) ? new Nothing : self::of($x);
    }

    public function map(callable $fn)
    {
        // implement by sub
    }

    public function ap(Maybe $m)
    {
        // implement by sub
    }

    public function chain(callable $fn)
    {
        // implement by sub
    }
}

function Maybe($x) {
    return (new Maybe)($x);
}