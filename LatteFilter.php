<?php


/**
 * Class LatteFilter
 */
class LatteFilter
{

    /**
     * Autoloader.
     *
     * @param $filter
     * @param $value
     * @return mixed
     */
    public static function common($filter, $value)
    {
        if (method_exists(__CLASS__, $filter)) {
            $args = func_get_args();
            array_shift($args);
            return call_user_func_array([__CLASS__, $filter], $args);
        }
    }


    /**
     * Insert text filter |addTag:'xyz'
     *
     * @param $string
     * @param $tag
     * @return mixed
     */
    public static function addTag($string, $tag)
    {
        $lastPoint = strrpos($string, '.');
        return ($tag ? substr_replace($string, sprintf('.%s.', $tag), $lastPoint, 1) : $string);
    }
}
