<?php

namespace Seo;

use dibi;
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
        TABLE_NAME = 'seo',
        TABLE_NAME_TITLE = 'seo_title',
        TABLE_NAME_DESCRIPTION = 'seo_description';

    /** @var Cache */
    private $cache;
    /** @var Connection database connection from DI */
    private $connection;
    /** @var Locale */
    private $locale;
    /** @var string table names */
    private $tableSeo, $tableSeoTitle, $tableSeoDescription;


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
        $this->tableSeoTitle = $parameters['tablePrefix'] . self::TABLE_NAME_TITLE;
        $this->tableSeoDescription = $parameters['tablePrefix'] . self::TABLE_NAME_DESCRIPTION;
    }


    /**
     * Internal insert and get id seo by presenter and action.
     *
     * @param $presenter
     * @param $action
     * @return mixed
     */
    private function getIdSeo($presenter, $action)
    {
        $cacheKey = 'getIdSeo-' . $presenter . '-' . $action;
        $id = $this->cache->load($cacheKey);
        if ($id === null) {
            $id = $this->connection->select('id')
                ->from($this->tableSeo)
                ->where('presenter=%s', $presenter)
                ->where('action=%s', $action)
                ->fetchSingle();

            if (!$id) {
                $id = $this->connection->insert($this->tableSeo, [
                    'presenter' => $presenter,
                    'action'    => $action,
                ])->execute(Dibi::IDENTIFIER);
            }

            $this->cache->save($cacheKey, $id, [
                Cache::TAGS => ['seo-cache'],
            ]);
        }
        return $id;
    }


    /**
     * Get seo item.
     *
     * @param Application $application
     * @param             $tableName
     * @param             $string
     * @return mixed
     */
    public function getItem(Application $application, $tableName, $string)
    {
        $presenter = $application->getPresenter();


        $idLocale = $this->locale->getIdByCode($presenter->getParameter('locale'));
        $presenterName = $presenter->getName();
        $presenterAction = $presenter->action;
        $idItem = $presenter->getParameter('id');

//        $cacheKey = 'getItem-' . $idLocale . $presenterName . $presenterAction . $idItem . $key[0];
//        $value = $this->cache->load($cacheKey);
//        if ($value === null) {
        $values = [
            'id_locale' => ($string ? $idLocale : null),
            'id_seo'    => $this->getIdSeo($presenterName, $presenterAction),
            'id_item'   => $idItem,
        ];

        $table = null;
        switch ($tableName) {
            case self::TABLE_NAME_TITLE:
                $table = $this->tableSeoTitle;
                break;

            case self::TABLE_NAME_DESCRIPTION:
                $table = $this->tableSeoDescription;
                break;
        }

//        $this->connection->insert($table, $values + ['added%sql' => 'NOW()']);//->onDuplicateKeyUpdate('%a', $values)->execute();


        //TODO defaultni pridani pouzije indexy automaticky a text vyplni defaultni hodnotou
        //TODO pri redefinici napr v novinkach a galerii pouzije text z bloku title a description, indexy opet pouzuje stejny!
        //TODO v pripade zajmu udelat i do ladenky panel ktery bude zobrazovat aktualni pouziti seo title/description
        //TODO index pouzivat dle nacteni hodnot z routeru: presenter/action/id_item

        $item = $this->connection->select('id, seo')
            ->from($table)
            ->where($values)
            ->fetch();

        if (!$item) {
            $this->connection->insert($table, $values + ['seo' => ($string ?: null), 'added%sql' => 'NOW()'])->execute();
        }

        if ($item) {
            return $item['seo'];
        }

//            if (!isset($item['id'])) {
//                //insert
//                $this->connection->insert($this->tableSeo, $values + $array + ['added%sql' => 'NOW()'])->execute();
//            } else {
//                // update
//                if (isset($array[$key[0]]) && !$item[$key[0]]) {
//                    // update only different value
//                    $this->connection->update($this->tableSeo, $array)->where(['id' => $item['id']])->execute();
//                }
//            }

//            $value = $item[$key[0]];

//            $this->cache->save($cacheKey, $value, [
//                Cache::TAGS => ['seo-cache'],
//            ]);
//    }

//        $value = null;
//        return $value;
    }
}
