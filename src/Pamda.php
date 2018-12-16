<?php
/**
 * The implementation of Function Programming like Ramda for PHP
 *
 * @author Zeon <scplay@gmail.com>
 */

namespace Pamda;


use Pamda\Reflector\Reflector;
use Pamda\Util\ArrayUtil;
use ArrayAccess;
use Closure;

/**
 * Class Pamda
 *
 * @method static callable curryN(int $param_count = null, callable $fn = null)
 *
 * @method static callable|mixed prop(string $prop = null, array|object $assoc_data = null)
 * @method static callable|mixed path(array $path = null, array|object $assoc_data = null)
 * @method static callable|mixed pathOr($default = null, array $path = null, array|object $assoc_data = null)
 *
 * @method static callable|mixed memorize(callable $fn = null)
 *
 * @package App\Services\Utils
 */
class Pamda
{
    const __ = PamdaPlaceHolder::class;

    /**
     * @param callable $fn
     *
     * @return callable
     * @throws \ReflectionException
     */
    public static function curry(callable $fn): callable
    {
        if (($arity = Reflector::paramNumOfCallable($fn)) > 0) {
            return self::_curryN($arity, $fn);
        } else {
            return $fn;
        }
    }

    /**
     * @used-by curryN
     *
     * @param int $param_num
     * @param callable $fn
     * @param mixed ...$provides
     *
     * @return callable
     */
    private static function _curryN(int $param_num, callable $fn, ...$provides): callable
    {
        return function (...$fresh) use ($param_num, $fn, $provides) {
            $cached = ArrayUtil::limit($param_num, self::replaceMergeArgs($provides, $fresh));

            if (count($cached) === $param_num && self::noMorePlaceholder($cached)) {
                return $fn(...$cached);
            } else {
                return self::_curryN($param_num, $fn, ...$cached);
            }
        };
    }

    /**
     * @used-by pathOr
     *
     * @param mixed $default
     * @param array $path
     * @param array|object $assoc_data
     *
     * @return mixed
     */
    protected static function _pathOr($default, array $path, $assoc_data)
    {
        if (is_object($assoc_data)) {
            $next_assoc = $assoc_data->{array_shift($path)} ?? $default;
        } elseif (is_array($assoc_data) || $assoc_data instanceof ArrayAccess) {
            $next_assoc = $assoc_data[array_shift($path)] ?? $default;
        } else {
            return $default;
        }

        if (count($path) && $next_assoc) {
            return self::_pathOr($default, $path, $next_assoc);
        } else {
            return $next_assoc;
        }
    }

    /**
     * @used-by path
     *
     * @param array $path
     * @param array|object $assoc_data
     *
     * @return mixed|null
     */
    protected static function _path(array $path, $assoc_data)
    {
        return self::_pathOr(null, $path, $assoc_data);
    }

    /**
     * @used-by prop
     *
     * @param string $prop
     * @param array|object $assoc_data
     *
     * @return mixed|null
     */
    protected static function _prop(string $prop, $assoc_data)
    {
        return self::_path([$prop], $assoc_data);
    }

    /**
     * @used-by memorize
     * @param callable $fn
     *
     * @return Closure
     */
    public static function _memorize(callable $fn): Closure
    {
        return function () use ($fn) {
            static $cache = [];

            $serial = serialize(func_get_args());

            return $cache[$serial] ?? $cache[$serial] = $fn(...func_get_args());
        };
    }

    /**
     * @used-by pipe
     *
     * @param callable[] $fns
     *
     * @return callable|mixed
     */
    public static function pipe(callable ...$fns)
    {
        return function () use ($fns) {
            $args = func_get_args();
            $pipe_result = array_shift($args);

            foreach ($fns as $fn) {
                $pipe_result = $fn($pipe_result);
            }

            return (is_callable($pipe_result) && count($args))
                ? $pipe_result(...$args)
                : $pipe_result;
        };
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public static function __callStatic($method, $arguments)
    {
        $self = self::class;
        $real = "_$method";

        if (method_exists($self, $real)) {
            return self::curry([$self, $real])(...$arguments);
        } else {
            throw new \InvalidArgumentException("method [$real] not found in [$self]");
        }
    }

    /**
     * @param array $cached
     * @param array $fresh
     *
     * @return array
     */
    private static function replaceMergeArgs(array $cached, array $fresh): array
    {
        foreach ($cached as $idx => $cache) { // replace placeholders with real args
            if ($cache === self::__ && count($fresh)) {
                $cached[$idx] = array_shift($fresh);
            }
        }

        return array_merge($cached, $fresh);
    }


    /**
     * array_search must be strict !!!
     *
     * @param array $cached
     *
     * @return bool
     */
    private static function noMorePlaceholder(array $cached): bool
    {
        return array_search(self::__, $cached, $strict = true) === false;
    }
}