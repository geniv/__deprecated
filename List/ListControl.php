<?php

/**
 * Class ListControl
 * obecny vypis bez prokliku, pro radkovy pristup (spravny)
 *
 * @author  geniv
 * @package NetteWeb
 */
class ListControl extends Nette\Application\UI\Control
{
    private $tableList, $database, $idLang, $translator;
    private $pathTemplate, $columns;
    private $lang = 'IdLang';   // implicitni jazyk
    private $order = 'Id';      // implicitni razeni
    private $orderDirection = 'asc';    // implicitni smer razeni
    private $limit = null, $offset = null;
    private $cache, $caching = false;   // zapinani cache


    /**
     * ListControl constructor.
     * @param \Nette\ComponentModel\IContainer $tableList
     * @param \Dibi\Connection $database
     * @param BaseLanguageService $language
     * @param AbstractTranslator $translator
     * @param \Nette\Caching\IStorage $cacheStorage
     */
    public function __construct($tableList, \Dibi\Connection $database, \BaseLanguageService $language, \AbstractTranslator $translator, \Nette\Caching\IStorage $cacheStorage)
    {
        parent::__construct();
        $this->tableList = $tableList;
        $this->database = $database;
        $this->idLang = $language->getId();
        $this->translator = $translator;
        $this->cache = new \Nette\Caching\Cache($cacheStorage, 'cache' . __CLASS__);

        // nastaveni implcitni cesty
        $this->pathTemplate = __DIR__ . '/default.latte';
    }


    /**
     * ovladani cache, defaultne vypnuto
     * @param $state
     * @return $this
     */
    public function setCaching($state)
    {
        $this->caching = $state;
        return $this;
    }


    /**
     * nastavovani cesty pro template
     * @param $path
     * @return $this
     */
    public function setPathTemplate($path)
    {
        $this->pathTemplate = $path;
        return $this;
    }


    /**
     * nastaveni sloupcu pro db
     * @param $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }


    /**
     * nastaveni jazykoveho sloupce
     * @param $lang
     * @return $this
     */
    public function setLanguage($lang)
    {
        $this->lang = $lang;
        return $this;
    }


    /**
     * nastaveni radiciho sloupce
     * @param $order
     * @param string $direction
     * @return $this
     */
    public function setOrder($order, $direction = 'asc')
    {
        $this->order = $order;
        $this->orderDirection = $direction;
        return $this;
    }


    /**
     * nastavani limitu pro vypis
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }


    /**
     * nastaveni offsetu pro vypis
     * @param $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }


    /**
     * pripraveni nacitani z databaze
     * @return DibiFluent
     */
    private function getListItems()
    {
        // ovladani cachovani
        if ($this->caching) {
            $list = $this->cache->load('list' . $this->tableList . $this->idLang);
            if ($list !== null) {
                return $list;
            }
        }

        //obecny vypis, setry a getry na univerzalni natypovani
        $c = $this->database->select($this->columns)
            ->from($this->tableList);
        // nastaveni jazyka
        if ($this->lang) {
            $c->where($this->lang . '=%i', $this->idLang);
        }
        // nastaveni razeni
        if ($this->order) {
            $direction = $this->orderDirection;
            $c->orderBy($this->order)->$direction();
        }
        // nastaveni limitu
        if ($this->limit) {
            $c->limit($this->limit);
        }
        // nastaveni offsetu
        if ($this->offset) {
            $c->offset($this->offset);
        }

        // ovladani cachovani
        if ($this->caching) {
            if ($list === null) {
                $list = $c->fetchAll();
                // ulozeni cache
                $this->cache->save('list' . $this->tableList . $this->idLang, $list, [
                    Nette\Caching\Cache::EXPIRE => '30 minutes',
                    Nette\Caching\Cache::TAGS => ['getListItems'],
                ]);
                return $list;
            }
        }
        return $c;
    }


    /**
     * hook pro invalidaci cache
     */
//    public function hookInvalidateCache()
//    {
//        // vynuceni precachovani
//        $this->cache->clean([
//            Nette\Caching\Cache::TAGS => ['getListItems'],
//        ]);
//    }


    /**
     * defaultni vykreslovani
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->setTranslator($this->translator);
        $template->setFile($this->pathTemplate);
        $template->list = $this->getListItems();
        $template->render();
    }
}
