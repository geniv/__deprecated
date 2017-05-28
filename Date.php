<?php

declare(strict_types=1);

namespace NAttreid\Utils;

use Datetime;
use DateTimeImmutable;
use InvalidArgumentException;
use Nette\SmartObject;

/**
 * Pomocna trida pro datum
 *
 * @author Attreid <attreid@gmail.com>
 */
class Date extends Lang
{
	use SmartObject;

	const
		DAY_SHORT = 'dayNamesShort',
		DAY = 'dayNames',
		MONTH_SHORT = 'monthNamesShort',
		MONTH = 'monthNames',
		DATETIME = 'datetime',
		DATE_WITH_TIME = 'dateWithTime',
		DATE = 'date';

	/** @var string[][] */
	private static $dayNamesShort = [
		'en' => [1 => 'mon', 2 => 'tue', 3 => 'wed', 4 => 'thu', 5 => 'fri', 6 => 'sat', 7 => 'sun'],
		'cs' => [1 => 'po', 2 => 'út', 3 => 'st', 4 => 'čt', 5 => 'pá', 6 => 'so', 7 => 'ne']
	];

	/** @var string[][] */
	private static $dayNames = [
		'en' => [1 => 'sunday', 2 => 'monday', 3 => 'tuesday', 4 => 'wednesday', 5 => 'thursday', 6 => 'friday', 7 => 'saturday'],
		'cs' => [1 => 'neděle', 2 => 'pondělí', 3 => 'úterý', 4 => 'středa', 5 => 'čtvrtek', 6 => 'pátek', 7 => 'sobota']
	];

	/** @var string[][] */
	private static $monthNamesShort = [
		'en' => [1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'],
		'cs' => [1 => 'led', 2 => 'úno', 3 => 'bře', 4 => 'dub', 5 => 'kvě', 6 => 'čer', 7 => 'črn', 8 => 'srp', 9 => 'zář', 10 => 'říj', 11 => 'lis', 12 => 'pro']
	];

	/** @var string[][] */
	private static $monthNames = [
		'en' => [1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july', 8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december'],
		'cs' => [1 => 'leden', 2 => 'únor', 3 => 'březen', 4 => 'duben', 5 => 'květen', 6 => 'červen', 7 => 'červenec', 8 => 'srpen', 9 => 'září', 10 => 'říjen', 11 => 'listopad', 12 => 'prosinec']
	];

	/** @var string[][] */
	private static $datetime = [
		'en' => 'n/j/Y G:i:s',
		'cs' => 'j.n.Y G:i:s'
	];

	/** @var string[][] */
	private static $dateWithTime = [
		'en' => 'n/j/Y G:i',
		'cs' => 'j.n.Y G:i'
	];

	/** @var string[][] */
	private static $date = [
		'en' => 'n/j/Y',
		'cs' => 'j.n.Y'
	];

	/**
	 * Formatovani
	 * @param string $type
	 * @return string
	 */
	public static function getFormat(string $type): string
	{
		$arr = self::$$type;
		return $arr[self::$locale];
	}

	/**
	 * Vrati pocatecni rok - aktualni rok. V pripade, ze se shoduji pouze aktualni
	 * @param int $beginYear pocatecni rok
	 * @return string napr: 2012 - 2014 nebo pouze 2014
	 */
	public static function getYearToActual(int $beginYear): string
	{
		$actualYear = strftime('%Y');
		if ($beginYear == $actualYear) {
			return $actualYear;
		} else {
			return $beginYear . ' - ' . $actualYear;
		}
	}

	/**
	 * Vrati aktualni cas na milivteriny
	 * @return string
	 */
	public static function getCurrentTimeStamp(): string
	{
		$t = microtime(true);
		$micro = sprintf('%06d', ($t - floor($t)) * 1000000);
		$d = new DateTime(date('Y-m-d H:i:s.' . $micro, (int) $t));
		return $d->format('Y_m_d_H_i_s_u');
	}

	/**
	 * Vrati nazev dne
	 * @param int|Datetime $day
	 * @return string
	 */
	public static function getDay($day): string
	{
		if ($day instanceof DateTime) {
			$day = (int) $day->format('N');
		}
		if (!is_int($day)) {
			throw new InvalidArgumentException;
		}
		return self::$dayNames[self::$locale][$day];
	}

	/**
	 * Vrati zkraceny nazev dne
	 * @param int|Datetime $day
	 * @return string
	 */
	public static function getShortDay($day): string
	{
		if ($day instanceof DateTime) {
			$day = (int) $day->format('N');
		}
		if (!is_int($day)) {
			throw new InvalidArgumentException;
		}
		return self::$dayNamesShort[self::$locale][$day];
	}

	/**
	 * Vrati nazev mesice
	 * @param int|Datetime $month
	 * @return string
	 */
	public static function getMonth($month): string
	{
		if ($month instanceof DateTime) {
			$month = (int) $month->format('j');
		}
		if (!is_int($month)) {
			throw new InvalidArgumentException;
		}
		return self::$monthNames[self::$locale][$month];
	}

	/**
	 * Vrati zkraceny nazev mesice
	 * @param int|Datetime $month
	 * @return string
	 */
	public static function getShortMonth($month): string
	{
		if ($month instanceof DateTime) {
			$month = (int) $month->format('j');
		}
		if (!is_int($month)) {
			throw new InvalidArgumentException;
		}
		return self::$monthNamesShort[self::$locale][$month];
	}

	/**
	 * Vrati nazvy dnu
	 * @return string[]
	 */
	public static function getDays(): array
	{
		return self::$dayNames[self::$locale];
	}

	/**
	 * Vrati zkracene nazvy dnu
	 * @return string[]
	 */
	public static function getShortDays(): array
	{
		return self::$dayNamesShort[self::$locale];
	}

	/**
	 * Vrati nazvy mesicu
	 * @return string[]
	 */
	public static function getMonths(): array
	{
		return self::$monthNames[self::$locale];
	}

	/**
	 * Vrati zkracene nazvy mesicu
	 * @return string[]
	 */
	public static function getShortMonths(): array
	{
		return self::$monthNamesShort[self::$locale];
	}

	/**
	 * Vrati lokalizovany format data
	 * @param DateTime|int $datetime
	 * @param array $formats
	 * @return string|null
	 */
	private static function formatDate($datetime, array $formats): ?string
	{
		if (empty($datetime)) {
			return null;
		} elseif ($datetime instanceof DateTime || $datetime instanceof DateTimeImmutable) {
			$date = $datetime;
		} else {
			$date = DateTime::createFromFormat('U', (string) $datetime);
		}
		return $date->format($formats[self::$locale]);
	}

	/**
	 * Lokalizovane datum s casem
	 * @param DateTime|int $datetime datum nebo timestamp
	 * @return string|null
	 */
	public static function getDateTime($datetime): ?string
	{
		return self::formatDate($datetime, self::$datetime);
	}

	/**
	 * Lokalizovane datum s casem bez sekund
	 * @param DateTime|int $datetime datum nebo timestamp
	 * @return string|null
	 */
	public static function getDateWithTime($datetime): ?string
	{
		return self::formatDate($datetime, self::$dateWithTime);
	}

	/**
	 * Lokalizovane datum
	 * @param DateTime|int $datetime datum nebo timestamp
	 * @return string|null
	 */
	public static function getDate($datetime): ?string
	{
		return self::formatDate($datetime, self::$date);
	}

	/**
	 * Vrati predchozi mesic
	 * @return Range
	 */
	public static function getPreviousMonth(): Range
	{
		return new Range(new DateTime('first day of last month'), new DateTime('last day of last month'));
	}

}
