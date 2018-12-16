<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/7/27 9:42
 * @copyright PanxSoft Inc.
 */

namespace Tests;

use Pamda\Pamda;
use PHPUnit\Framework\TestCase;

class PamdaTest extends TestCase
{
    protected $prop;

    protected $method_call_count = 0;

    /**
     * @uses Pamda::curry()
     *
     * @throws \ReflectionException
     */
    public function test_p_curry()
    {
        $out = 1;
        $add = Pamda::curry(function ($a, $b) use ($out) {
            return $a + $b;
        });
        $this->assertEquals(1, $add(0)(1));

        $add3 = Pamda::curry(function ($a, $b, $c, $d, $e) use ($out) {
            return $a + $b + $c + $d + $e;
        })(1, 2);
        $this->assertEquals(15, $add3(3, 4)(5));

        $noop = Pamda::curry(function () {});
        $this->assertNull($noop());
    }

    /**
     * @uses Pamda::curry()
     *
     * @throws \ReflectionException
     */
    public function test_p_curry_can_use_class_method()
    {
        $selfAdd = Pamda::curry([$this, 'add']);

        $this->assertEquals(1, $selfAdd(0)(1));
    }

    public function add($a, $b)
    {
        return $a + $b;
    }

    /**
     * @uses Pamda::curryN()
     */
    public function test_p_curryN_can_use_class_method()
    {
        $add = Pamda::curryN(2, [$this, 'add']);

        $this->assertEquals(3, $add(1)(2));

        $add = Pamda::curryN(2, [$this, 'notPureAdd']);

        $this->prop = 2;
        $this->assertEquals(5, $add(1)(2));
    }

    /**
     * @uses Pamda::curryN()
     */
    public function test_p_curryN_also_curry()
    {
        $curry2 = Pamda::curryN(2);

        $sum2 = $curry2(function (...$args) {
            return array_sum($args);
        });

        $this->assertEquals(3, $sum2(1)(2));

        $sum2 = Pamda::curryN(2, function (...$args) {
            return array_sum($args);
        });
        $this->assertEquals(3, $sum2(1)(2));

        $addOne = Pamda::curryN(2, function (...$args) {
            return array_sum($args);
        })(1);

        $this->assertEquals(3, $addOne(2));
    }

    /**
     * 最好不要这样用，这样函数就不纯了
     *
     * @param $a
     * @param $b
     *
     * @return mixed
     */
    public function notPureAdd($a, $b)
    {
        return $this->prop + $a + $b;
    }

    /**
     * @uses Pamda::pathOr()
     */
    public function test_pathOr()
    {
        $real = false;

        $a = [
            'a' => [
                'b' => [
                    'c' => $real
                ]
            ]
        ];

        $default = ['other' => 'value'];

        $expect = Pamda::pathOr($default, ['a', 'b', 'c'], $a);
        $this->assertEquals($expect, $real);

        $expect = Pamda::pathOr($default, ['a', 'b', 'c'], null);
        $this->assertEquals($expect, $expect);

        $expect = Pamda::pathOr($default, ['b', 'd'], $a);
        $this->assertEquals($expect, $default);

        $expect = Pamda::pathOr($default, ['a', 'd'], $a);
        $this->assertEquals($expect, $default);

        $expect = Pamda::pathOr($default, [], $a);
        $this->assertEquals($expect, $default);

        $expect = Pamda::pathOr($default, ['a', 'b', 'd'], $a);
        $this->assertEquals($expect, $default);

        $pathOrDefault = Pamda::pathOr($default);
        $expect = $pathOrDefault(['a', 'b', 'd'], $a);
        $this->assertEquals($expect, $default);

        $path_a_b_d_OrDefault = Pamda::pathOr($default, ['a', 'b', 'd']);
        $expect = $path_a_b_d_OrDefault($a);
        $this->assertEquals($expect, $default);

        $real_b = 2;
        $b = [
            'a' => [
                (object) [
                    'b' => $real_b
                ]
            ]
        ];

        $path_a_0_d_OrDefault = Pamda::pathOr($default, ['a', 0, 'b']);
        $expect = $path_a_0_d_OrDefault($b);
        $this->assertEquals($real_b, $expect);

        $path_a_0_d_OrDefault = Pamda::pathOr($default, ['a', '1', 'b']);
        $expect = $path_a_0_d_OrDefault($b);
        $this->assertEquals($expect, $default);
    }

    /**
     *
     */
    public function test_p_curry_placeholder()
    {
        $expect = 2;
        $data = [
            'a' => [
                'b' => $expect
            ]
        ];

        $default = 1;
        $dataPathOr1 = Pamda::pathOr($default, Pamda::__, $data);
        $this->assertEquals($expect, $dataPathOr1(['a', 'b']));

        $dataPathOr1 = Pamda::pathOr($default, Pamda::__, $data);
        $this->assertEquals($default, $dataPathOr1(['a', 'c', 'd', 'e']));

        $path_a_b_Or = Pamda::pathOr(Pamda::__, ['a', 'b'], Pamda::__);
        $path_a_b_Or = $path_a_b_Or(Pamda::__, Pamda::__); // placeholder can use any times
        $path_a_b_Or = $path_a_b_Or(Pamda::__);
        $path_a_b_Or1 = $path_a_b_Or($default);
        $this->assertEquals($expect, $path_a_b_Or1($data));
    }

    public function test_p_memorize()
    {
        $call_count = 0;

        $func = Pamda::memorize(function ($a) use (&$call_count){
            $call_count++;
            return $a;
        });
        $func(10);
        $this->assertEquals(1, $call_count);
        $func(10);
        $this->assertEquals(1, $call_count);
        $func(20);
        $this->assertEquals(2, $call_count);

        $this->method_call_count  = 0;
        $method = Pamda::memorize([$this, 'memoMethod']);
        $method(10);
        $this->assertEquals(1, $this->method_call_count);
        $method(10);
        $this->assertEquals(1, $this->method_call_count);

        $method(20);
        $this->assertEquals(2, $this->method_call_count);
    }

    public function memoMethod($val)
    {
        $this->method_call_count += 1;

        return $val;
    }

    /**
     * @throws \ReflectionException
     */
    public function test_p_pipe()
    {
        $add = Pamda::curry(function ($a, $b, $c) {
            return $a + $b + $c;
        });
        $mutli = function ($a, $b) {
            return $a * $b;
        };
        $mutli3 = Pamda::curry($mutli)(3);

        $multi3Add = Pamda::pipe($mutli3, $add);
        $this->assertEquals(6, $multi3Add(1)(1)(2));
        $this->assertEquals(6, $multi3Add(1)(1, 2));
        $this->assertEquals(6, $multi3Add(1, 1, 2));

        $path_a_add3OrDefault11 = Pamda::pipe(
            Pamda::pathOr(11, ['a'])
        );
        $this->assertEquals(11, $path_a_add3OrDefault11(['ab']));
        $this->assertEquals(12, $path_a_add3OrDefault11(['a' => 12]));

        $path_a_add3OrDefault11_2 = Pamda::pipe(
            Pamda::pathOr(11, ['a']),
            Pamda::curry($add)(3, 0) // test multi curry
        );
        $this->assertEquals(15, $path_a_add3OrDefault11_2(['a' => 12]));
        $this->assertEquals(14, $path_a_add3OrDefault11_2([]));
    }
}