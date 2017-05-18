<?php

namespace App\Model;

/**
 * Class NewsColumns, pro sloupcovy pristup (spatny)
 *
 * @author  geniv
 * @package NetteWeb
 *
 * @deprecated
 */
class NewsColumns
{
    use \Nette\SmartObject;

    private $tableNews, $tableNewsGallery;
    /** @var \Dibi\Connection */
    private $database;
    private $lang, $mainLang;


    /**
     * News constructor.
     * @param $tableNews
     * @param \Dibi\Connection $database
     * @param \AbstractLanguageService $language
     */
    public function __construct($tableNews, \Dibi\Connection $database, \AbstractLanguageService $language)
    {
        $this->tableNews = $tableNews;
        $this->tableNewsGallery = $tableNews . '_gallery';  // galerie novinek

        $this->database = $database;
        $this->lang = $language->getCode(true);
        $this->mainLang = $language->getMainLang(true);
    }


    /**
     * interni vyber novinek
     * @return \Dibi\Fluent
     */
    private function getNews()
    {
        return $this->database->select('Id, Title' . $this->mainLang . ' Slug, Title' . $this->lang . ' Title, Perex' . $this->lang . ' Perex, Description' . $this->lang . ' Description, Image, Added, Visible, VisibleOnHomepage')
            ->from($this->tableNews)
            ->where('Visible=%b', true);
    }


    /**
     * nacitani vsech novinek
     * @return \Dibi\Fluent
     */
    public function getListNews()
    {
        return $this->getNews()
            ->orderBy('Added')->desc();
    }


    /**
     * nacitani galerie pro detail novinky
     * @param $idNews
     * @return \Dibi\Fluent
     */
    public function getListNewsGallery($idNews)
    {
        return $this->database->select('Id, IdNews, Image, Title' . $this->lang . ' Title, Added, Visible, [Order]')
            ->from($this->tableNewsGallery)
            ->where('IdNews=%i', $idNews)
            ->where('Visible=%b', true)
            ->orderBy('Added')->asc();
    }


    /**
     * nacitani vsech novinek pro homepage
     * @return \Dibi\Fluent
     */
    public function getListHomepageNews()
    {
        return $this->getNews()
            ->where('VisibleOnHomepage=%b', true)
            ->orderBy('Added')->desc();
    }


    /**
     * nacitani detailu novinky
     * @param $idNews
     * @return \Dibi\Row|FALSE
     */
    public function getDetail($idNews)
    {
        return $this->getNews()
            ->where('Id=%i', $idNews)
            ->fetch();
    }
}
