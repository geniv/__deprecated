<?php

namespace LocaleServices\Drivers;

use LocaleServices\LocaleService;
use Dibi\Connection;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Exception;


/**
 * Class Database
 *
 * databazova jazykova sluzba pro DYNAMICKE preklady z databaze, pro dynamicky rozsiritelne jazyky
 *
 * @author  geniv
 * @package LocaleServices\Drivers
 */
class Database extends LocaleService
{

    /**
     * Database constructor.
     *
     * @param array      $parameters
     * @param Connection $database
     * @param IStorage   $cacheStorage
     * @throws Exception
     */
    public function __construct(array $parameters, Connection $database, IStorage $cacheStorage)
    {
        $cache = new Cache($cacheStorage, 'cache' . __CLASS__);

        // pokud parametr table neexistuje
        if (!isset($parameters['table'])) {
            throw new Exception('Table name is not defined in configure! (table: xy)');
        }
        // nacteni jmena tabulky
        $tableLanguages = $parameters['table'];

        // ulozeni locales do cache
        $locales = $cache->load('locales');
        if ($locales === null) {
            // nacteni vsech jazyku do pole
            $locales = $database->select('id, code, name, plural, main')
                ->from($tableLanguages)
                ->where('active=%b', true)
                ->orderBy('id')->asc()
                ->fetchAssoc('code');

            $cache->save('locales', $locales);  // cachovani bez expirace
        }

        // ulozeni a nacteni defaultniho locale do cache
        $defaultLocale = $cache->load('defaultLocale');
        if ($defaultLocale === null) {
            // nacteni hlavniho jazyku, pokud neni definovano vezme prvi v rade
            $main = array_keys(array_filter($locales, function ($item) {
                return ($item->main == 1);
            }));
            $defaultLocale = isset($main[0]) ? $main[0] : array_keys($locales)[0];

            $cache->save('defaultLocale', $defaultLocale);  // cachovani bez expirace
        }

        // ulozeni a nacteni locale aliasu do cache
        $localeAlias = $cache->load('localeAlias');
        if ($localeAlias === null) {
            // nacitani aliasu
            $localeAlias = $database->select('a.alias, l.code')
                ->from($tableLanguages . '_alias')->as('a')
                ->join($tableLanguages)->as('l')->on('l.id=a.id_locale')
                ->fetchPairs('alias', 'code');

            $cache->save('localeAlias', $localeAlias);  // cachovani bez expirace
        }

        parent::__construct($defaultLocale, $locales, $localeAlias);
    }
}
