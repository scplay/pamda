<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/7 17:06
 */

namespace Pamda\Reflector;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

class Reflector
{
    /**
     * @param callable $fn
     *
     * @return int
     * @throws \ReflectionException
     */
    public static function paramNumOfCallable(callable $fn): int
    {
        return is_array($fn)
            ? self::paramNumOfMethod(...$fn)
            : self::paramNumOfClosure($fn);
    }


    /**
     * @param string|object $class
     * @param string $name
     *
     * @return int
     * @throws \ReflectionException
     */
    private static function paramNumOfMethod($class, string $name): int
    {
        $params = (new ReflectionMethod($class, $name))->getParameters();

        return self::paramNumExcludeVariadic($params);
    }

    /**
     * @param callable $fn
     *
     * @return int
     * @throws \ReflectionException
     */
    private static function paramNumOfClosure(callable $fn): int
    {
        $params = (new ReflectionFunction($fn))->getParameters();

        return self::paramNumExcludeVariadic($params);
    }

    /**
     * @param array|ReflectionParameter[] $reflectParams
     *
     * @return int
     */
    private static function paramNumExcludeVariadic(array $reflectParams): int
    {
        $param_num = count($reflectParams);

        foreach ($reflectParams as $idx => $reflectParam) {
            if ($reflectParam->isVariadic()) {
                $param_num -= 1;
            }
        }

        return max(0, $param_num);
    }
}