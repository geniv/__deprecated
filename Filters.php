<?php

namespace Lqd\Tools;

/**
 * Template filters.
 * @author     David Grudl
 * @internal
 */
class Filters
{
	static public $datetimeFormat = '%c';
   
	static public $currencyDigits = 2;
	
	static public $locale = 'cs_CZ';
	
	static public $currency = 'CZK';

	/**
	 * Escapes string for use inside XML 1.0 template.
	 * @param  string UTF-8 encoding
	 * @return string
	 */
	public static function datetime($time, $format = NULL)
	{
		if ($time == NULL) { // intentionally ==
			return NULL;
		}

		if (!isset($format)) {
			$format = self::$datetimeFormat;
		}

		if ($time instanceof \DateInterval) {
			return $time->format($format);

		} elseif (is_numeric($time)) {
			$time = new \DateTime('@' . $time);
			$time->setTimeZone(new \DateTimeZone(date_default_timezone_get()));

		} elseif (!$time instanceof \DateTime && !$time instanceof \DateTimeInterface) {
			$time = new \DateTime($time);
		}
		return strpos($format, '%') === FALSE
			? $time->format($format) // formats using date()
			: strftime($format, $time->format('U')); // formats according to locales
	}

	public static function price($s, $digits = null)
	{
		$formatter = new \NumberFormatter(self::$locale,  \NumberFormatter::CURRENCY);
		
		$digits = (self::$currencyDigits !== null) ? $digits : null;
		
		if ($digits !== null)
			$formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, self::$currencyDigits);
		
		return $formatter->formatCurrency($s, self::$currency);    
	}
	
	public static function number($s, $digits = 2, $style = \NumberFormatter::DEFAULT_STYLE)
	{
		$formatter = new \NumberFormatter(self::$locale, $style );
		$formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $formatter->format($s);   
	}
	
	public static function percent($s, $digits = 2)
	{
		$formatter = new \NumberFormatter(self::$locale, \NumberFormatter::PERCENT );
		$formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
		return $formatter->format($s);   
	}

	public static function inflection($s, $val)
	{
		if (self::$locale == 'cs_CZ')
		{
			$inflection = new \Inflection();
			$inflected = $inflection->inflect($s);
	
		return $inflected[$val];
		} else {return $s;}	
	}
	
	public static function defaultval($s, $empty = "-")
	{   
		return ($s) ? $s : $empty;
	}
	
	public static function map($s, array $tomap)
	{    
		return @$tomap[$s];
	}
	
	public static function mailto($s)
	{
		return !$s ? null : '<a href="mailto:'.$s.'">'.$s.'</a>';
	}
	
	public static function register($template, array $options = [])
	{							 
		foreach($options as $k => $v) {
			static::$$k = $v;
		}
	
		$template->addFilter('datetime', ['\Lqd\Base\Filters','datetime']);
		$template->addFilter('price', ['\Lqd\Base\Filters','price']);
		$template->addFilter('defaultval', ['\Lqd\Base\Filters','defaultval']);
		$template->addFilter('mailto', ['\Lqd\Base\Filters','mailto']);
		$template->addFilter('inflection', ['\Lqd\Base\Filters','inflection']);
		$template->addFilter('map', ['\Lqd\Base\Filters','map']);
	}
	 

}
