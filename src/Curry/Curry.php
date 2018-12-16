<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/7 16:52
 */

namespace Pamda\Curry;



use ReflectionClass;
use ReflectionFunction;

/**
 * @deprecated - PHP 可使用闭包自引用，不需要使用类完成柯里化
 *
 * @package App\Services\Utils
 */
class Curry
{
    /**
     * @var array
     */
    protected $provided_args = [];

    /**
     * @var int
     */
    protected $num_of_param = 0;

    /**
     * @var callable
     */
    protected $fn;

    /**
     * Curry constructor.
     *
     * @param callable $fn
     * @param array $args
     *
     * @throws \ReflectionException
     */
    public function __construct(callable $fn, ...$args)
    {
        $this->fn = $fn;

        $this->reflectSetNumOfParam();

        $this->mergeProvides($args);
    }

    /**
     * @param mixed ...$args
     *
     * @return Curry|mixed
     */
    public function __invoke(...$args)
    {
        $this->mergeProvides($args);

        return $this->shouldInvoke() ? $this->callFn() : $this;
    }

    /**
     * @throws \ReflectionException
     */
    protected function reflectSetNumOfParam()
    {
        $this->num_of_param = (new ReflectionFunction($this->fn))->getNumberOfParameters();
    }

    /**
     * @return bool
     */
    private function shouldInvoke()
    {
        return count($this->provided_args) === $this->num_of_param;
    }

    /**
     * @return mixed
     */
    private function callFn()
    {
        return ($this->fn)(...$this->provided_args);
    }

    /**
     * @param $args
     */
    private function mergeProvides($args): void
    {
        $this->provided_args = array_slice(
            array_merge($this->provided_args, $args), 0, $this->num_of_param
        );
    }

    /**
     * @deprecated - 演示反射用
     *
     * @return Curry|object
     *
     * @throws \ReflectionException
     */
    public static function curry()
    {
        $selfReflector = new ReflectionClass(self::class);

        return $selfReflector->newInstanceArgs(func_get_args());
    }
}
