<?php

namespace AliasRouter;

use Dibi;
use Dibi\Connection;
use Exception;
use Locale\Locale;
use Nette\Application\UI\Presenter;
use Nette\Caching\IStorage;
use Nette\SmartObject;
use Nette\Utils\Strings;


/**
 * Class Model
 *
 * @author  geniv
 * @package AliasRouter
 */
class Model
{
    use SmartObject;

    /** @var string tables name */
    private $tableRouter, $tableRouterAlias;
    /** @var Connection database from DI */
    private $connection;
    /** @var LocaleService locale service */
    private $localeService;


    /**
     * Model constructor.
     *
     * @param            $parameters
     * @param Connection $connection
     * @param Locale     $locale
     * @param IStorage   $storage
     * @throws Exception
     */
    public function __construct($parameters, Connection $connection, Locale $locale, IStorage $storage)
    {
        // pokud parametr table neexistuje
        if (!isset($parameters['table'])) {
            throw new Exception('Table name is not defined in configure! (table: xy)');
        }

        // nacteni jmena tabulky
        $tableRouter = $parameters['table'];

        $this->tableRouter = $tableRouter;
        $this->tableRouterAlias = $tableRouter . '_alias';
        $this->connection = $connection;

        $this->localeService = $locale;
    }


    public function getParametersByAlias($locale, $alias)
    {
        $result = $this->connection->select('r.id, r.presenter, r.action, a.id_item')
            ->from($this->tableRouter)->as('r')
            ->join($this->tableRouterAlias)->as('a')->on('a.id_router=r.id')
            ->where('a.id_locale=%i', $this->localeService->getIdByCode($locale))
            ->where('a.alias=%s', $alias)
            ->fetch();
        return $result;
    }


    public function getAliasByParameters($presenter, $parameters)
    {
        $result = $this->connection->select('r.id, a.alias, a.id_item')
            ->from($this->tableRouter)->as('r')
            ->join($this->tableRouterAlias)->as('a')->on('a.id_router=r.id')
            ->where('r.presenter=%s', $presenter)
            ->where('a.id_locale=%i', $this->localeService->getIdByCode($parameters['locale']))
            ->orderBy('a.added')->desc();

        // add action condition
        if (isset($parameters['action'])) {
            $result->where('r.action=%s', $parameters['action']);
        }

        // add id condition
        if (isset($parameters['id'])) {
            $result->where('a.id_item=%i', $parameters['id']);
        }
        return $result->fetch();
    }


    public function getCodeLocale()
    {
        return (!$this->localeService->isDefaultLocale() ? $this->localeService->getCode() : '');
    }


    private function getIdRouter($presenter, $action)
    {
        $id = $this->connection->select('id')
            ->from($this->tableRouter)
            ->where('presenter=%s', $presenter)
            ->where('action=%s', $action)
            ->fetchSingle();

        if (!$id) {
            $id = $this->connection->insert($this->tableRouter, [
                'presenter' => $presenter,
                'action'    => $action,
            ])->execute(Dibi::IDENTIFIER);
        }
        return $id;
    }


    private function getIdRouterAlias($idRouter, $idLocale, $idItem, $alias)
    {
        $cursor = $this->connection->select('id')
            ->from($this->tableRouterAlias)
            ->where('id_router=%i', $idRouter)
            ->where('id_locale=%i', $idLocale)
            ->where('alias=%s', $alias);
        if ($idItem) {
            $cursor->where('id_item=%i', $idItem);
        }

        $id = $cursor->fetchSingle();
        if (!$id) {
            $id = $this->connection->insert($this->tableRouterAlias, [
                'id_router' => $idRouter,
                'id_locale' => $idLocale,
                'id_item'   => $idItem,
                'alias'     => $alias,
                'added%sql' => 'NOW()',
            ])->execute(Dibi::IDENTIFIER);
        }
        return $id;
    }


    public function insertAlias(Presenter $presenter, $alias)
    {
        $idRouter = $this->getIdRouter($presenter->getName(), $presenter->action);

        $safeAlias = Strings::webalize($alias);

        $result = $this->getIdRouterAlias($idRouter, $this->localeService->getIdByCode($presenter->getParameter('locale')), $presenter->getParameter('id'), $safeAlias);

        return $result;
    }


    /**
     * Create router match.
     *
     * @param       $presenter
     * @param       $action
     * @param       $alias
     * @param array $parameters
     */
    public function createRouter($presenter, $action, $alias, $parameters = [])
    {
        $this->insertAlias(new InternalRouterPresetner($presenter, $action, $parameters), $alias);
    }


    public function getRouterAlias(Presenter $presenter, $idLocale, $idItem = null)
    {
        $result = $this->connection->select('a.id, a.alias, a.id_item')
            ->from($this->tableRouter)->as('r')
            ->join($this->tableRouterAlias)->as('a')->on('a.id_router=r.id')
            ->where('r.presenter=%s', $presenter->getName())
            ->where('a.id_locale=%i', $idLocale)
            ->orderBy('a.added')->desc();

        // add action condition
        if ($presenter->action) {
            $result->where('r.action=%s', $presenter->action);
        }

        // add id condition
        if ($idItem) {
            $result->where('a.id_item=%i', $idItem);
        }

        return $result;
    }
}
