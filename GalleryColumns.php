<?php

namespace App\Model;

/**
 * Class GalleryColumns, pro sloupcovy pristup (spatny)
 *
 * @author  geniv
 * @package NetteWeb
 *
 * @deprecated
 */
class GalleryColumns
{
    use \Nette\SmartObject;

    private $tableGallery, $tableGalleryItem;
    /** @var \Dibi\Connection */
    private $database;
    private $lang, $mainLang;


    /**
     * Gallery constructor.
     * @param $tableGallery
     * @param \Dibi\Connection $database
     * @param \AbstractLanguageService $language
     */
    public function __construct($tableGallery, \Dibi\Connection $database, \AbstractLanguageService $language)
    {
        $this->tableGallery = $tableGallery;
        $this->tableGalleryItem = $tableGallery . '_item';  // polozky

        $this->database = $database;
        $this->lang = $language->getCode(true);
        $this->mainLang = $language->getMainLang(true);
    }


    /**
     * interni vyber galerie
     * @return \Dibi\Fluent
     */
    private function getGallery()
    {
        return $this->database->select('Id, Name' . $this->mainLang . ' Slug, Name' . $this->lang . ' Name, Description' . $this->lang . ' Description, Added, Visible, VisibleOnHomepage, [Order]')
            ->from($this->tableGallery)
            ->where('Visible=%b', true);
    }


    /**
     * nacitani vsech gallerii
     * @return mixed
     */
    public function getListGallery()
    {
        return $this->getGallery()
            ->orderBy('Added')->asc();
    }


    /**
     * nacitani polozek galerie s prvnim obrazkem
     * @return mixed
     */
    public function getListGalleryFirstImage()
    {
        return $this->database->select('g.Id, Name' . $this->lang . ' Name, Description' . $this->lang . ' Description, g.Added, g.Visible, VisibleOnHomepage, Image, g.[Order]')
            ->from($this->tableGallery)->as('g')
            ->leftJoin($this->tableGalleryItem)->as('i')->on('g.Id=i.IdGallery')
            ->where('g.Visible=%b', true)
            ->groupBy('IdGallery')
            ->orderBy('g.[Order]')->asc()
            ->orderBy('i.[Order]')->asc();
    }


    /**
     * nacitani polozek galerie pro detail
     * @param $idGallery
     * @return mixed
     */
    public function getListGalleryItems($idGallery)
    {
        return $this->database->select('Id, IdGallery, Image, Title' . $this->lang . ' Title, Added, Visible, [Order]')
            ->from($this->tableGalleryItem)
            ->where('IdGallery=%i', $idGallery)
            ->where('Visible=%b', true)
            ->orderBy('[Order]')->asc();
    }


    /**
     * nacitani vsech galerii pro homepage
     * @return mixed
     */
    public function getListHomepageGallery()
    {
        return $this->getGallery()
            ->where('VisibleOnHomepage=%b', true)
            ->orderBy('Added')->asc();
    }


    /**
     * nacitani detailu galerie
     * @param $idGallery
     * @return \Dibi\Row|FALSE
     */
    public function getDetail($idGallery)
    {
        return $this->getGallery()
            ->where('Id=%i', $idGallery)
            ->fetch();
    }
}
