<?php


/**
 * Class FilterLatte
 *
 * @author geniv
 */
class FilterLatte
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
     * Insert text filter |addTag:'xyz'.
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


    /**
     * Show email.
     *
     * @param $string
     * @return null|string
     */
    public static function mailto($string)
    {
        return ($string ? '<a href="mailto:' . str_replace('@', '%40', $string) . '">' . str_replace('@', '&#064;', $string) . '</a>' : null);
    }
}
