<?php

class Lib_Array
{
    /**
     * Переделанная функция array_merge_recursive
     * Оригинал превращает совпадающие ключи в массив,
     * здесь это исправленно.
     *
     * @static
     *
     * @param array [, array [, array]]
     *
     * @return array|mixed
     */
    public static function MergeRecursive()
    {
        $a = func_get_arg(0);
        $count = func_num_args();

        for ($i = 1; $i < $count; $i++) {
            $b = func_get_arg($i);
            $a = self::_mergeRecursiveChanged($a, $b);
        }

        return $a;
    }

    protected static function _mergeRecursiveChanged($a, $b)
    {
        if (is_array($a)) {
            if (is_array($b)) {
                foreach ($b as $k => $v) {
                    if (isset($a[$k])) {
                        $a[$k] = self::_mergeRecursiveChanged($a[$k], $b[$k]);
                    } else {
                        $a[$k] = $b[$k];
                    }
                }
            } else {
                $a = $b;
            }
        } else {
            $a = $b;
        }

        unset($b);

        return $a;
    }

    /**
     * Вычисление рекурсивной разницы массивов
     *
     * @static
     *
     * @param array [, array [, array]]
     *
     * @return array|mixed
     */
    public static function DiffRecursive()
    {
        $a = func_get_arg(0);
        $count = func_num_args();

        for ($i = 1; $i < $count; $i++) {
            $b = func_get_arg($i);
            $a = self::_diffRecursive($a, $b);
        }

        return $a;
    }

    protected static function _diffRecursive($a, $b)
    {
        if (is_array($a)) {
            if (is_array($b)) {
                foreach ($b as $k => $v) {
                    if (isset($a[$k])) {
                        $diff = self::_diffRecursive($a[$k], $b[$k]);

                        if (empty($diff)) {
                            unset($a[$k]);
                        } else {
                            $a[$k] = $diff;
                        }
                    } else {
                        $a[$k] = $b[$k];
                    }
                }
            } else {
                $a = $b;
            }
        } else {
            if ($a !== $b) {
                $a = $b;
            } else {
                return [];
            }
        }

        unset($b);

        return $a;
    }
}
