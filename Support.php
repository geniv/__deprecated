<?php

/**
 * Class Support
 *
 * @author  geniv, DavidBeran
 * @package NetteWeb
 */
class Support
{
    use Nette\SmartObject;


    private static function month($date)
    {
        $months = [1 => "ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince"];
        return $months[$date];
    }


    /**
     * vypisovani rozsahu datumu
     * @param DateTime $from
     * @param DateTime $to
     * @return string
     */
    public static function dateRange(\DateTime $from, \DateTime $to)
    {
        // month from
        $d1 = $from->format('j');
        $m1 = $from->format('n');
        $y1 = $from->format('Y');

        // month to
        $d2 = $to->format('j');
        $m2 = $to->format('n');
        $y2 = $to->format('Y');

        $month = self::month($from->format('n'));
        $month2 = self::month($to->format('n'));

        // years are the same
        if ($y1 == $y2) {
            // months are the same
            if ($m1 == $m2) {
                // one-day event
                if ($d1 == $d2) {
                    return "$d1. $month $y1";
                } // event lasts for more days
                else {
                    return "$d1.-$d2. $month $y1";
                }
            } // months range
            else {
                return "$d1. $month – $d2. $month2 $y1";
            }
        } // years range
        else {
            return "$d1. $month $y1 – $d2. $month2 $y2";
        }
    }

//FIXME prepsat pro podporu PHP7
    /**
     * odebrani camel case
     * @param $word
     * @return mixed
     */
    public static function decamelize($word)
    {
        return @preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strtolower(strlen("\\1") ? "\\1_\\2" : "\\2")',
            $word
        );
    }


    /**
     * vytvoreni camel case
     * @param $word
     * @param string $separator
     * @param bool $first
     * @return mixed
     */
    public static function camelize($word, $separator = '-', $first = true)
    {
        $arg = implode('|', array_filter([($first ? '^' : null), $separator]));
        return @preg_replace('/(' . $arg . ')([a-z])/e', 'strtoupper("\\2")', $word);
    }
}
