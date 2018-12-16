<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/9 10:49
 */

namespace Pamda\Fantasy\Contracts;


interface Monad
{
    /**
     * @param $value
     *
     * @return mixed
     */
    public static function of($value);
}