<?php

namespace DatabaseRouter;

use DateTime;
use Dibi\Connection;
use Dibi\Result;
use Dibi\Row;
use LocaleServices\LocaleService;
use Nette\Application\UI\Presenter;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\SmartObject;
use Nette\Utils\Strings;


/**
 * Class Model
 *
 * @author  geniv
 * @package DatabaseRouter
 */
class Model
{
    use SmartObject;

    protected $tableRoute, $tableRouteAlias,
        $database, $cache, $locale;
    /** @var \Router */
    protected $databaseRouter;


    /**
     * DatabaseRouterModel constructor.
     *
     * @param               $tableRoute
     * @param Connection    $database
     * @param LocaleService $localeService
     * @param IStorage      $cacheStorage
     * @internal param LocaleService $ocale
     */
    public function __construct($tableRoute, Connection $database, LocaleService $localeService, IStorage $cacheStorage)
    {
        $this->tableRoute = $tableRoute;
        $this->tableRouteAlias = $tableRoute . '_alias';

        $this->database = $database;
        $this->locale = $localeService;
        $this->cache = new Cache($cacheStorage, 'cache' . __CLASS__);
    }


    /**
     * vnitrni vyhledani routy
     *
     * @param $presenter
     * @param $action
     * @return mixed
     */
    protected function getIdRoute($presenter, $action)
    {
        $cacheKey = 'getIdRoute' . $presenter . $action;
        $cursor = $this->cache->load($cacheKey);
        if ($cursor === null) {
            $cursor = $this->database->select('id')
                ->from($this->tableRoute)
                ->where('presenter=%s', $presenter)
                ->where('action=%s', $action)
                ->fetchSingle();

            // ulozeni cache
            $this->cache->save($cacheKey, $cursor, [
                Cache::TAGS => ['getIdRoute/' . $presenter . '/' . $action],
            ]);
        }
        return $cursor;
    }


    /**
     * vnitrni vkladan routy
     *
     * @param $presenter
     * @param $action
     * @return bool|Result|int
     */
    protected function insertRoute($presenter, $action)
    {
        if ($presenter && $action) {
            $values = [
                'presenter' => $presenter,
                'action'    => $action,
            ];

            // promazavani cache pro id routeru
            $this->cache->clean([
                Cache::TAGS => ['getIdRoute/' . $presenter . '/' . $action],
            ]);

            // vkladani do tabulky route (presenter + akce)
            return $this->database->insert($this->tableRoute, $values)
                ->execute(Dibi::IDENTIFIER);
        }
        return false;
    }


    /**
     * vnitrni vkladani route aliasu
     *
     * @param $idRoute
     * @param $locale
     * @param $alias
     * @param $idItem
     * @return Result|int
     */
    protected function insertRouteAlias($idRoute, $locale, $alias, $idItem)
    {
        $values = [
            'id_route'  => $idRoute,
            'id_locale' => $this->locale->getIdByCode($locale),
            'alias'     => $alias,     // musi se ukladat jen alias bez jazyka a strankovani
            'id_item'   => $idItem,
            'added'     => new DateTime,   // datum urcuje poradi routy (nejnovejsi je aktualni)
        ];
        return $this->database->insert($this->tableRouteAlias, $values)
            ->execute(Dibi::IDENTIFIER);
    }


    /**
     * vnitrni vyhledavani alusu podle aliasu
     *
     * @param      $locale
     * @param      $alias
     * @param null $parameters
     * @return mixed
     */
    protected function getIdRouteAlias($locale, $alias, $parameters = null)
    {
        $idLocale = $this->locale->getIdByCode($locale);

        // bere id aliasu
        $cursor = $this->database->select('r.id')
            ->from($this->tableRoute)->as('r')
            ->join($this->tableRouteAlias)->as('a')->on('r.id=a.id_route')
            ->where('alias=%s', $alias)
            ->where('id_locale=%i', $idLocale);

        if (isset($parameters['action'])) {
            $cursor->where('action=%s', $parameters['action']);
        }

        if (isset($parameters['id'])) {
            $cursor->where('id_item=%i', $parameters['id']);
        }
        return $cursor->fetchSingle();
    }


    /**
     * nacteni presenteru a akce podle aliasu
     * - match
     *
     * @param $locale
     * @param $alias
     * @return Row|FALSE|mixed
     */
    public function getByAlias($locale, $alias)
    {
        $idLocale = $this->locale->getIdByCode($locale);

        $cacheKey = 'match-' . $locale . $alias;
        $cursor = $this->cache->load($cacheKey);
        if ($cursor === null) {
            $cursor = $this->database->select('r.id, presenter, action, id_locale, alias, id_item')
                ->from($this->tableRoute)->as('r')
                ->join($this->tableRouteAlias)->as('a')->on('r.id=a.id_route')
                ->where('alias=%s', $alias)
                ->where('id_locale=%i', $idLocale)
                ->fetch();

            // ulozeni cache
            $this->cache->save($cacheKey, $cursor, [
                Cache::TAGS => [$locale . '/' . $alias],
            ]);
        }
        return $cursor;
    }
//FIXME upravit vkladani na insert on update !!


    /**
     * interni nacteni jazykoveho kodu z parametru nebo language service
     *
     * @param $parameters
     * @return int
     */
    protected function getLangCode($parameters)
    {
        // nacitani jazyka z parametru (+ preklad do ID) a nebo z interniho nastaveni
        if (isset($parameters['lang'])) {
            return $parameters['lang'];
        } else {
            return $this->locale->getCode();
        }
    }


    /**
     * nacteni aliasu podle presenteru a parametru
     * - constructUrl
     *
     * @param $presenter
     * @param $parameters
     * @return mixed
     */
    public function getAliasByParams($presenter, $parameters)
    {
        $locale = $this->getLangCode($parameters);    // akceptuje idLanguage parametr
        $idLocale = $this->locale->getIdByCode($locale);

        $action = (isset($parameters['action']) ? $parameters['action'] : null);
        $id = (isset($parameters['id']) ? $parameters['id'] : null);

        $cacheKey = 'constructUrl-' . $locale . $presenter . $action . $id;
        $cursor = $this->cache->load($cacheKey);
        if ($cursor === null) {
            $cursor = $this->database->select('r.id, alias, id_item')
                ->from($this->tableRoute)->as('r')
                ->join($this->tableRouteAlias)->as('a')->on('r.id=a.id_route')
                ->where('presenter=%s', $presenter)
                ->where('id_locale=%i', $idLocale)
                ->orderBy('added')->desc();// bere vzdy novejsi verzi

            // hledani podle action
            if ($action) {
                $cursor->where('action=%s', $action);
            }

            // pridavne hledani podle id
            if ($id) {
                $cursor->where('id_item=%i', $id);
            }

            $cursor = $cursor->fetch();

            // ulozeni cache
            $this->cache->save($cacheKey, $cursor, [
                Cache::TAGS => [$locale . ':' . $presenter . ':' . $action . ':' . $id],
            ]);
        }
        return $cursor;
    }


    /**
     * prenost instance databazoveho routeru
     *
     * @param Model $databaseRouter
     */
    public function setDatabaseRouter(Model $databaseRouter)
    {
        $this->databaseRouter = $databaseRouter;
    }


    /**
     * manualni vlozeni routy
     *
     * @param       $presenter
     * @param       $action
     * @param       $alias
     * @param array $parameters
     * @return $this
     */
    public function createRoute($presenter, $action, $alias, $parameters = [])
    {
        $this->insertAlias(new InternalRouterPresenter($presenter, $action, $parameters), $alias);
        return $this;
    }


    /**
     * vkladani aliasu do databaze prostrednictvim komponenty: AliasCreator
     *
     * @param Presenter                       $context
     * @param                                 $alias
     * @return \Dibi\Result|int|mixed|null
     */
    public function insertAlias(Presenter $context, $alias)
    {
        try {
            if ($this->databaseRouter) {
                $params = $context->getParameters();
                // nacitani promennych pro dalsi pouziti
                $lang = $this->getLangCode($params);   // akceptuje lang parametr

                $presenter = $context->getName();
                $action = $context->getParameter('action');
                $parameters = array_filter($params);   // vezme vsechny parametry, odfiltruje null
                $newAlias = Strings::webalize($alias, '/');  // webalizace aliasu, ignorace /

                $idRoute = $this->getIdRoute($presenter, $action);  // kontrola duplicity na routeru
                if (!$idRoute) {
                    // pokud se uspesne vlozi, nacte znovu IdRoute pro dalsi dotaz
                    $idRoute = $this->insertRoute($presenter, $action);
                }

                $idItem = (isset($parameters['id']) ? $parameters['id'] : null);

                // vkladani do tabulky route_alias (idRoute + jazyk + alias)
                $idAlias = $this->getIdRouteAlias($lang, $newAlias, $parameters); // kontrola duplicity na nazev a jazyk aliasu


                // vyfiltrovani parametru
//                $parameters = $this->databaseRouter->filterParameters($parameters);
//                $params = ($parameters ? serialize($parameters) : null);

                // promazavani cache pro alias
                $this->cache->clean([
                    Cache::TAGS => [$lang . '/' . $newAlias],
                ]);

                // promazavani cache pro presentery
                $this->cache->clean([
                    Cache::TAGS => [$lang . ':' . $presenter . ':' . $action . ':' . $idItem],
                ]);

                $ret = 0;
                //neexistuje/existuje jeden/existuje vice
                if (strpos($alias, '##') === false && !$idAlias) {   // alias nesmi obsahovat na zacatku ## a nesmi existovat v databazi
                    $ret = $this->insertRouteAlias($idRoute, $lang, $newAlias, $idItem);
                }

                // vytvoreni upozorneni
                if (strpos($alias, '##') === 0) {
                    echo('<strong>neuplny preklad aliasu, zkontrolujte prosim preklady!</strong>');
                }
                return $ret;
            }
            return null;
        } catch (\Dibi\Exception $e) {
            return -$e->getCode();
        }
    }
}


/**
 * Class InternalRouterPresenter
 *
 * interni trida pro vkladani rout
 */
class InternalRouterPresenter extends Presenter
{
    private $name;
    private $action;


    /**
     * InternalRouterPresenter constructor.
     *
     * @param       $name
     * @param       $action
     * @param array $parameters
     */
    public function __construct($name, $action, $parameters = [])
    {
        $this->name = $name;    // vlozeni jmena
        $this->action = $action;    // vlozeni akce
        $this->params = $parameters;    // vlozeni parametru
        $this->params['action'] = $action;  // vlozeni akce do parametru
    }


    /**
     * nacteni jmena
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * nacteni akce
     *
     * @param bool $fullyQualified
     * @return mixed
     */
    public function getAction($fullyQualified = false)
    {
        return $fullyQualified ? ':' . $this->getName() . ':' . $this->action : $this->action;
    }
}
