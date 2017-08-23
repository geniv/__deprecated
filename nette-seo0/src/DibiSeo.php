<?php

namespace Seo;

use Dibi\Connection;
use Locale\Locale;
use Nette\Application\Application;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\SmartObject;


/**
 * Class DibiSeo
 *
 * @author  geniv
 * @package Seo
 */
class DibiSeo
{
    use SmartObject;

    // define constant table names
    const
        TABLE_NAME = 'seo';

    /** @var Cache */
    private $cache;
    /** @var Connection database connection from DI */
    private $connection;
    /** @var Locale */
    private $locale;
    /** @var string table names */
    private $tableSeo;


    /**
     * DibiSeo constructor.
     *
     * @param array      $parameters
     * @param Connection $connection
     * @param Locale     $locale
     * @param IStorage   $storage
     */
    public function __construct(array $parameters, Connection $connection, Locale $locale, IStorage $storage)
    {
        $this->connection = $connection;
        $this->locale = $locale;
        $this->cache = new Cache($storage, 'cache-Seo-DibiSeo');
        // define table names
        $this->tableSeo = $parameters['tablePrefix'] . self::TABLE_NAME;
    }


    /**
     * Get seo item.
     *
     * @param Application $application
     * @param             $array
     * @return mixed
     */
    public function getItem(Application $application, $array)
    {
        $presenter = $application->getPresenter();

        $idLocale = $this->locale->getIdByCode($presenter->getParameter('locale'));
        $presenterName = $presenter->getName();
        $presenterAction = $presenter->action;
        $idItem = $presenter->getParameter('id');

        $key = array_keys($array);

        $cacheKey = 'getItem-' . $idLocale . $presenterName . $presenterAction . $idItem . $key[0];
        $value = $this->cache->load($cacheKey);
        if ($value === null) {
            $values = [
                'id_locale' => $idLocale,
                'presenter' => $presenterName,
                'action'    => $presenterAction,
                'id_item'   => $idItem,
            ];

            $item = $this->connection->select(array_merge(['id'], $key))
                ->from($this->tableSeo)
                ->where($values)
                ->fetch();

            if (!isset($item['id'])) {
                //insert
                $this->connection->insert($this->tableSeo, $values + $array + ['added%sql' => 'NOW()'])->execute();
            } else {
                // update
                if ($array[$key[0]] != $item[$key[0]]) {
                    // update only different value
                    $this->connection->update($this->tableSeo, $array)->where(['id' => $item['id']])->execute();
                }
            }

            $value = $item[$key[0]];

            $this->cache->save($cacheKey, $value, [
                Cache::TAGS => ['seo-cache'],
            ]);
        }
        return $value;
    }
}
