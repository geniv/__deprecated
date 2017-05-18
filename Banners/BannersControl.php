<?php

/**
 * Class BannersControl
 *
 * tb_banner: "%tb_prefix%banner"
 *
 * --
 * -- Struktura tabulky `prefix_banner`
 * --
 *
 * CREATE TABLE IF NOT EXISTS `prefix_banner` (
 * `Id` int(11) NOT NULL,
 * `Ident` varchar(100) DEFAULT NULL COMMENT 'identifikator',
 * `ShowText` tinyint(1) DEFAULT '0' COMMENT 'zobrazit text'
 * ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='banery';
 *
 * --
 * -- Vypisuji data pro tabulku `prefix_banner`
 * --
 *
 * INSERT INTO `prefix_banner` (`Id`, `Ident`, `ShowText`) VALUES
 * (1, 'home', 1),
 * (2, 'side', 0);
 *
 * -- --------------------------------------------------------
 *
 * --
 * -- Struktura tabulky `prefix_banner_item`
 * --
 *
 * CREATE TABLE IF NOT EXISTS `prefix_banner_item` (
 * `Id` int(11) NOT NULL,
 * `IdLanguage` int(11) DEFAULT NULL COMMENT 'jazyk',
 * `IdBanners` int(11) DEFAULT NULL COMMENT 'baner',
 * `Image` varchar(400) DEFAULT NULL COMMENT 'obrazek',
 * `Title` varchar(255) DEFAULT NULL COMMENT 'titulek',
 * `Text` text COMMENT 'text',
 * `Url` varchar(255) DEFAULT NULL COMMENT 'url',
 * `Added` datetime DEFAULT NULL COMMENT 'pridano',
 * `Visible` tinyint(1) DEFAULT '0' COMMENT 'viditelnost',
 * `Order` int(11) DEFAULT '0' COMMENT 'poradi'
 * ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='banery - polozky';
 *
 * --
 * -- Vypisuji data pro tabulku `prefix_banner_item`
 * --
 *
 * INSERT INTO `prefix_banner_item` (`Id`, `IdLanguage`, `IdBanners`, `Image`, `Title`, `Text`, `Url`, `Added`, `Visible`, `Order`) VALUES
 * (1, 1, 1, 'slide_0.jpg', 'Title', 'text', '', '0000-00-00 00:00:00', 1, 0),
 * (2, 1, 1, 'slide_2.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (3, 1, 1, 'slide_1.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (4, 1, 2, 'slide_0.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (5, 1, 2, 'slide_1.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (6, 1, 2, 'slide_2.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (7, 1, 1, 'slide_0.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (8, 1, 1, 'slide_2.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (9, 1, 2, 'slide_1.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (10, 1, 2, 'slide_2.jpg', 'Title', 'text', NULL, NULL, 1, 0),
 * (11, 1, 1, '', '', '', '', '2016-10-10 23:22:00', 0, 1);
 *
 * --
 * -- Klíče pro tabulku `prefix_banner`
 * --
 * ALTER TABLE `prefix_banner`
 * ADD PRIMARY KEY (`Id`);
 *
 * --
 * -- Klíče pro tabulku `prefix_banner_item`
 * --
 * ALTER TABLE `prefix_banner_item`
 * ADD PRIMARY KEY (`Id`), ADD KEY `fk_banners_item_banners_idx` (`IdBanners`), ADD KEY `fk_banner_item_language_idx` (`IdLanguage`);
 *
 * --
 * -- AUTO_INCREMENT pro tabulku `prefix_banner`
 * --
 * ALTER TABLE `prefix_banner`
 * MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
 * --
 * -- AUTO_INCREMENT pro tabulku `prefix_banner_item`
 * --
 * ALTER TABLE `prefix_banner_item`
 * MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
 *
 * --
 * -- Omezení pro tabulku `prefix_banner_item`
 * --
 * ALTER TABLE `prefix_banner_item`
 * ADD CONSTRAINT `fk_banner_item_language` FOREIGN KEY (`IdLanguage`) REFERENCES `prefix_language` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
 * ADD CONSTRAINT `fk_banners_item_banners` FOREIGN KEY (`IdBanners`) REFERENCES `prefix_banner` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION;
 *
 *
 * ## Banners
 * neon:
 * ```neon
 * - \BannersControl(%tb_banner%)
 * ```
 *
 * basepresenter:
 * ```php
 * protected function createComponentBanners()
 * {
 * return $this->context->getByType(\BannersControl::class)
 * ->setPathTemplate(__DIR__ . '/templates/hp-banner.latte');
 * }
 * ```
 *
 * latte:
 * ```latte
 * {control banners 'home'}
 * ```
 *
 *
 * @author  geniv
 * @package NetteWeb
 */
class BannersControl extends Nette\Application\UI\Control// implements IInvalidateCache
{
    private $tableBanners, $tableBannersItems;
    private $database, $idLanguage, $translator, $cache;
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
        $this->idLanguage = $language->getId();
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
        $cursor = $this->cache->load($ident . $this->idLanguage);
        if ($cursor === null) {
            $cursor = $this->database->select('i.Id, i.IdBanners, i.Image, b.ShowText, i.Title, i.Text, i.Url')
                ->from($this->tableBanners)->as('b')
                ->join($this->tableBannersItems)->as('i')->on('i.IdBanners=b.Id')
                ->where('b.Ident=%s', $ident)
                ->where('i.Visible=%b', true)
                ->where('i.IdLanguage=%i', $this->idLanguage)
                ->orderBy('i.[Order]')->asc()
                ->fetchAll();

            // ulozeni cache
            $this->cache->save($ident . $this->idLanguage, $cursor, [
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
