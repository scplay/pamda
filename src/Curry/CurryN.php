<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/7 16:52
 */

namespace Pamda\Curry;

/**
 * @deprecated - PHP 可使用闭包自引用，不需要使用类完成柯里化
 *
 * Class CurryN
 * @package Pamda\Curry
 */
class CurryN extends Curry
{
    /**
     * Curry constructor.
     *
     * @param int $num_of_param
     * @param callable $fn
     *
     * @throws \ReflectionException
     */
    public function __construct(int $num_of_param, callable $fn)
    {
        parent::__construct($fn);

        $this->setNumOfParam($num_of_param);
    }

    /**
     * @see setNumOfParam
     */
    protected function reflectSetNumOfParam()
    {
        return; // override parent because reflectFunction can not apply to object method
    }

    /**
     * @param int $num
     */
    protected function setNumOfParam(int $num)
    {
        $this->num_of_param = $num;
    }
}