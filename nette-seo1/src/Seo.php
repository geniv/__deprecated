<?php

namespace Seo;

use dibi;
use Dibi\Connection;
use Locale\Locale;
use Nette\Application\Application;
use Nette\Application\UI\Control;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;


/**
 * Class Seo
 *
 * @author  geniv
 * @package Seo
 */
class Seo extends Control
{
    // define constant table names
    const
        TABLE_NAME = 'seo',
        TABLE_NAME_IDENT = 'seo_ident';

    /** @var Cache */
    private $cache;
    /** @var Connection database connection from DI */
    private $connection;
    /** @var Locale */
    private $locale;
    /** @var Application */
    private $application;
    /** @var string table names */
    private $tableSeo, $tableSeoIdent;


    public function __construct(array $parameters, Connection $connection, Locale $locale, IStorage $storage, Application $application)
    {
        $this->connection = $connection;
        $this->locale = $locale;
        $this->cache = new Cache($storage, 'cache-Seo-Seo');
        $this->application = $application;
        // define table names
        $this->tableSeo = $parameters['tablePrefix'] . self::TABLE_NAME;
        $this->tableSeoIdent = $parameters['tablePrefix'] . self::TABLE_NAME_IDENT;
    }


    /**
     * Internal insert and get id seo by presenter and action.
     *
     * @param $presenter
     * @param $action
     * @return mixed
     */
    private function getIdent($presenter, $action)
    {
        $cacheKey = 'getIdSeo-' . $presenter . '-' . $action;
        $id = $this->cache->load($cacheKey);
        if ($id === null) {
            $id = $this->connection->select('id')
                ->from($this->tableSeoIdent)
                ->where(['presenter' => $presenter, 'action' => $action])
                ->fetchSingle();

            if (!$id) {
                $id = $this->connection->insert($this->tableSeoIdent, [
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


    private function getIdentByIdent($ident)
    {
        $cacheKey = 'getIdentByIdent-' . $ident;
        $id = $this->cache->load($cacheKey);
        if ($id === null) {
            $id = $this->connection->select('id')
                ->from($this->tableSeoIdent)
                ->where(['ident' => $ident])
                ->fetchSingle();

            if (!$id) {
                $id = $this->connection->insert($this->tableSeoIdent, [
                    'ident' => $ident,
                ])->execute(Dibi::IDENTIFIER);
            }

            $this->cache->save($cacheKey, $id, [
                Cache::TAGS => ['seo-cache'],
            ]);
        }
        return $id;
    }


//    public function addSeo($method, $string)
//    {
//
//        $presenter = $this->application->getPresenter();
//
//        $idLocale = $this->locale->getIdByCode($presenter->getParameter('locale'));
//        $presenterName = $presenter->getName();
//        $presenterAction = $presenter->action;
//        $idItem = $presenter->getParameter('id');
//
//
//        $values = [
//            'id_locale' => $idLocale,
//            'id_seo'    => $this->getIdSeo($presenterName, $presenterAction),
//            'id_item'   => $idItem,
//        ];
//
//
//        $item = $this->connection->select('id, seo')
//            ->from($this->tableName[$method])
//            ->where($values)
////            ->where('id_locale IS NULL OR id_locale=%i', $idLocale)
//            ->fetch();
//
//        dump($item, $string);
//
//        // update
////        if ($item && !$item->seo && $string) {
//        if (!$item) {
////            $this->connection->update($this->tableName[$method], ['seo' => $string])->where(['id' => $item['id']])->execute();
////            $this->connection->insert($this->tableName[$method], $values + ['seo' => $string, 'added%sql' => 'NOW()'])->execute();
//        }
//    }


    public function __call($name, $args)
    {
        if (!in_array($name, ['onAnchor'])) {   // nesmi zachytavat definovane metody
            $presenter = $this->application->getPresenter();

            $idLocale = $this->locale->getIdByCode($presenter->getParameter('locale'));
            $presenterName = $presenter->getName();
            $presenterAction = $presenter->action;
            $idItem = $presenter->getParameter('id');

            $methodName = strtolower(substr($name, 6)); // load method name
            $ident = (isset($args[0]) ? $args[0] : null);
            $return = (isset($args[1]) ? $args[1] : false); // echo / return

            // get $idIdent from ident mode or presenter-action mode
            $idIdent = ($ident ? $this->getIdentByIdent($ident) : $this->getIdent($presenterName, $presenterAction));

            // ignore $idItem in case $ident mode
            if ($idIdent && $idItem) {
                $idItem = null;
            }

            $values = [
                's.id'        => $idIdent,
                'tab.id_item' => $idItem,
            ];

            $cursor = $this->connection->select('s.id, tab.id tid, lo_tab.id lotid, ' .
                'IFNULL(lo_tab.title, tab.title) title, ' .
                'IFNULL(lo_tab.description, tab.description) description')
                ->from($this->tableSeoIdent)->as('s')
                ->leftJoin($this->tableSeo)->as('tab')->on('tab.id_ident=s.id')->and('tab.id_locale IS NULL')
                ->leftJoin($this->tableSeo)->as('lo_tab')->on('lo_tab.id_ident=s.id')->and('lo_tab.id_locale=%i', $idLocale)
                ->where($values);

//            $cursor->test();
            $item = $cursor->fetch();

            // insert null locale item
            if (!$item->tid) {  //&& !$item->tid
                $this->connection->insert($this->tableSeo, [
                    'id_locale' => null,
                    'id_ident'  => $idIdent,
                    'id_item'   => $idItem,
                ])->execute();
            }

            // catch is* method
            switch ($name) {
                case 'isTitle':
                    return $item['title'];
                    break;

                case 'isDescription':
                    return $item['description'];
                    break;
            }

            // return value
            if ($item) {
                if ($return) {
                    return $item[$methodName];
                } else {
                    echo $item[$methodName];
                }
            }

//            //TODO taby bude vkladat jen defaultni jazyk a v metode vkladajici obsah z title bude vkladano
//            $idSeo = $this->getIdSeo($presenterName, $presenterAction);
//            $values = [
//                's.id'           => $idSeo,
//                'tab.id_item'    => $idItem,
//                'lo_tab.id_item' => $idItem,
//            ];
//
//            $method = strtolower(substr($name, 6)); // nacteni jmena
////            if (!isset($args[0])) {
////                throw new Exception('Nebyl zadany parametr identu.');
////            }
//
//            $return = (isset($args[1]) ? $args[1] : false); // echo / return
//
//
//            $cur = $this->connection->select('s.id, IFNULL(lo_tab.seo, tab.seo) seo')
//                ->from($this->tableSeo)->as('s')
//                ->leftJoin($this->tableName[$method])->as('tab')->on('tab.id_seo=s.id')->and('tab.id_locale IS NULL')
//                ->leftJoin($this->tableName[$method])->as('lo_tab')->on('lo_tab.id_seo=s.id')->and('lo_tab.id_locale=%i', $idLocale)
//                ->where($values);
//
////            $cur->test();
//            $item = $cur->fetch();
//
//            dump($item);
//
//            // insert null values for null
//            if (!$item) {
//                $this->connection->insert($this->tableName[$method], [
//                    'id_locale' => null,
//                    'id_seo'    => $idSeo,
//                    'id_item'   => $idItem,
//                    'added%sql' => 'NOW()',
//                ])->execute();
//            }
//
//            if ($item) {
//                if ($return) {
//                    return $item['seo'];
//                } else {
//                    echo $item['seo'];
//                }
//            }

//            // nacteni enable
//            if (substr($name, 0, 8) == 'isEnable') {
//                $method = strtolower(substr($name, 8));
//                if (isset($this->values[$method][$ident])) {
//                    $block = $this->values[$method][$ident];
//                    return $block->enable;
//                }
//            }

//            // vytvareni
//            if ($this->autoCreate && (!isset($this->values[$method]) || !isset($this->values[$method][$ident]))) {
//                $this->addData($method, $ident);    // vlozeni
//                $this->loadData();                  // znovunacteni
//            }

//            // nacitani
//            if (isset($this->values[$method])) {
//                $block = $this->values[$method];
//                if (isset($block[$ident])) {
//                    if ($return) {
//                        return ($block[$ident]->enable ? $block[$ident]->content : null);
//                    }
//                    echo($block[$ident]->enable ? $block[$ident]->content : null);
//
//                } else {
//                    throw new Exception('Nebyl nalezeny ident ' . $ident . '.');
//                }
//            } else {
//                throw new Exception('Nebyl nalezeny platny blok. Blok ' . $method . ' neexistuje.');
//            }
        }
    }














//    /**
//     * Get seo item.
//     *
//     * @param Application $application
//     * @param             $tableName
//     * @param             $string
//     * @return mixed
//     */
//    public function getItem(Application $application, $tableName, $string)
//    {
//        $presenter = $application->getPresenter();
//
//
//        $idLocale = $this->locale->getIdByCode($presenter->getParameter('locale'));
//        $presenterName = $presenter->getName();
//        $presenterAction = $presenter->action;
//        $idItem = $presenter->getParameter('id');
//
////        $cacheKey = 'getItem-' . $idLocale . $presenterName . $presenterAction . $idItem . $key[0];
////        $value = $this->cache->load($cacheKey);
////        if ($value === null) {
//        $values = [
//            'id_locale' => ($string ? $idLocale : null),
//            'id_seo'    => $this->getIdSeo($presenterName, $presenterAction),
//            'id_item'   => $idItem,
//        ];
//
//        $table = null;
//        switch ($tableName) {
//            case self::TABLE_NAME_TITLE:
//                $table = $this->tableSeoTitle;
//                break;
//
//            case self::TABLE_NAME_DESCRIPTION:
//                $table = $this->tableSeoDescription;
//                break;
//        }
//
////        $this->connection->insert($table, $values + ['added%sql' => 'NOW()']);//->onDuplicateKeyUpdate('%a', $values)->execute();
//
//
//        //TODO defaultni pridani pouzije indexy automaticky a text vyplni defaultni hodnotou
//        //TODO pri redefinici napr v novinkach a galerii pouzije text z bloku title a description, indexy opet pouzuje stejny!
//        //TODO v pripade zajmu udelat i do ladenky panel ktery bude zobrazovat aktualni pouziti seo title/description
//        //TODO index pouzivat dle nacteni hodnot z routeru: presenter/action/id_item
//
//        $item = $this->connection->select('id, seo')
//            ->from($table)
//            ->where($values)
//            ->fetch();
//
//        if (!$item) {
//            $this->connection->insert($table, $values + ['seo' => ($string ?: null), 'added%sql' => 'NOW()'])->execute();
//        }
//
//        if ($item) {
//            return $item['seo'];
//        }
//
////            if (!isset($item['id'])) {
////                //insert
////                $this->connection->insert($this->tableSeo, $values + $array + ['added%sql' => 'NOW()'])->execute();
////            } else {
////                // update
////                if (isset($array[$key[0]]) && !$item[$key[0]]) {
////                    // update only different value
////                    $this->connection->update($this->tableSeo, $array)->where(['id' => $item['id']])->execute();
////                }
////            }
//
////            $value = $item[$key[0]];
//
////            $this->cache->save($cacheKey, $value, [
////                Cache::TAGS => ['seo-cache'],
////            ]);
////    }
//
////        $value = null;
////        return $value;
//    }

}
