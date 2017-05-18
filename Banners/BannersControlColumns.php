<?php

/**
 * Class BannersControlColumns, pro sloupcovy pristup (spatny)
 *
 * @author  geniv
 * @package NetteWeb
 *
 * @deprecated
 */
class BannersControlColumns extends Nette\Application\UI\Control// implements IInvalidateCache
{
    private $tableBanners, $tableBannersItems;
    private $database, $lang, $translator, $cache;
    private $pathTemplate;


    /**
     * BannersControl constructor.
     * @param \Nette\ComponentModel\IContainer $tableBanners
     * @param \Dibi\Connection $database
     * @param AbstractLanguageService $language
     * @param AbstractTranslator $translator
     * @param \Nette\Caching\IStorage $cacheStorage
     */
    public function __construct($tableBanners, \Dibi\Connection $database, \AbstractLanguageService $language, \AbstractTranslator $translator, \Nette\Caching\IStorage $cacheStorage)
    {
        parent::__construct();
        $this->tableBanners = $tableBanners;
        $this->tableBannersItems = $tableBanners . '_item';
        $this->database = $database;
        $this->lang = $language->getCode(true);
        $this->translator = $translator;
        $this->cache = new \Nette\Caching\Cache($cacheStorage, 'cache' . __CLASS__);

        // nastaveni implcitni cesty
        $this->pathTemplate = __DIR__ . '/Banners.latte';
    }


    /**
     * interni nacitani polozek baneru, vcetne cachovani
     * @param $ident
     * @return mixed
     */
    private function getListBanners($ident)
    {
        $cursor = $this->cache->load($ident . $this->lang);
        if ($cursor === null) {
            $cursor = $this->database->select('i.Id, i.IdBanners, i.Image, b.ShowText, i.Title' . $this->lang . ' Title, i.Text' . $this->lang . ' Text, i.Url' . $this->lang . ' Url')
                ->from($this->tableBanners)->as('b')
                ->join($this->tableBannersItems)->as('i')->on('i.IdBanners=b.Id')
                ->where('b.Ident=%s', $ident)
                ->where('i.Visible=%b', true)
                ->orderBy('i.[Order]')->asc()
                ->fetchAll();

            // ulozeni cache
            $this->cache->save($ident . $this->lang, $cursor, [
                Nette\Caching\Cache::EXPIRE => '30 minutes',
                Nette\Caching\Cache::TAGS => ['getListBanners'],
            ]);
        }
        return $cursor;
    }


    /**
     * hook pro invalidaci cache
     */
//    public function hookInvalidateCache()
//    {
//        // vynuceni precachovani
//        $this->cache->clean([
//            Nette\Caching\Cache::TAGS => ['getListBanners'],
//        ]);
//    }


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
     * defaultni vykreslovani
     * @param $ident
     */
    public function render($ident)
    {
        $template = $this->getTemplate();

        $template->addFilter(null, 'LatteFilter::common');
        $template->setTranslator($this->translator);
        $template->setFile($this->pathTemplate);

        $template->banners = $this->getListBanners($ident);

        $template->render();
    }
}
