<?php
/**
 *
 *
 * @author Zeon <scplay@gmail.com>
 * @date 2018/11/7 17:07

 */

namespace Pamda\Util;


class ArrayUtil
{
    /**
     * @param int $size
     * @param array $arr
     *
     * @return array
     */
    public static function limit(int $size, array $arr): array
    {
        return array_slice($arr, 0, $size);
    }

}