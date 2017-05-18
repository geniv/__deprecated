<?php

/**
 * Class TextsControl
 * pro plneni textoveho obsahu webu (title + content)
 *
 * tb_text: "%tb_prefix%text"
 * tb_text_extended: "%tb_prefix%text"
 *
 * --
 * -- Struktura tabulky `prefix_text`
 * --
 *
 * CREATE TABLE IF NOT EXISTS `prefix_text` (
 * `Id` int(11) NOT NULL,
 * `IdLanguage` int(11) NOT NULL COMMENT 'jazyk',
 * `Ident` varchar(100) NOT NULL COMMENT 'identifikator',
 * `Title` varchar(255) DEFAULT NULL COMMENT 'titulek',
 * `Content` text COMMENT 'obsah'
 * ) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='texty';
 *
 * --
 * -- Vypisuji data pro tabulku `prefix_text`
 * --
 *
 * INSERT INTO `prefix_text` (`Id`, `IdLanguage`, `Ident`, `Title`, `Content`) VALUES
 * (1, 2, 'homepage', '## Title ## - homepage - 2', '<p>## Content ## - homepage - 2</p>\n'),
 * (2, 2, 'homepage1', '## Title ## - homepage1 - 2', '## Content ## - homepage1 - 2'),
 * (4, 1, 'homepage', '## Title ## - homepage - 1', '## Content ## - homepage - 1'),
 * (5, 1, 'homepage1', '## Title ## - homepage1 - 1', '## Content ## - homepage1 - 1'),
 * (7, 1, 'abc', 'TitleAbcCs', '## Content ## - abc - 1'),
 * (8, 2, 'abc', 'TitleAbcEn', '## Content ## - abc - 2'),
 * (9, 1, 'cda', '## Title ## - cda - 1', '## Content ## - cda - 1'),
 * (10, 2, 'cda', '## Title ## - cda - 2', '## Content ## - cda - 2'),
 * (11, 1, 'homepage2', '## Title ## - homepage2 - 1', '## Content ## - homepage2 - 1'),
 * (13, 1, 'ccc', '## Title ## - ccc - 1', '## Content ## - ccc - 1'),
 * (14, 1, 'homepage11', '## Title ## - homepage11 - 1', '## Content ## - homepage11 - 1'),
 * (15, 1, 'homepage111', '## Title ## - homepage111 - 1', '## Content ## - homepage111 - 1'),
 * (16, 1, 'homepage211', '## Title ## - homepage211 - 1', '## Content ## - homepage211 - 1'),
 * (17, 1, 'homepage1111', '## Title ## - homepage1111 - 1', '## Content ## - homepage1111 - 1'),
 * (18, 1, 'homepage111122', '## Title ## - homepage111122 - 1', '## Content ## - homepage111122 - 1'),
 * (19, 1, 'homepage111122444455', '## Title ## - homepage111122444455 - 1', '## Content ## - homepage111122444455 - 1'),
 * (20, 1, 'homepage11112244445578787', '## Title ## - homepage11112244445578787 - 1', '## Content ## - homepage11112244445578787 - 1'),
 * (21, 2, 'homepage2', '## Title ## - homepage2 - 2', '## Content ## - homepage2 - 2');
 *
 * --
 * -- Klíče pro tabulku `prefix_text`
 * --
 * ALTER TABLE `prefix_text`
 * ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `Language_Ident_UNIQUE` (`IdLanguage`,`Ident`), ADD KEY `fk_text_language_idx` (`IdLanguage`);
 *
 * --
 * -- AUTO_INCREMENT pro tabulku `prefix_text`
 * --
 * ALTER TABLE `prefix_text`
 * MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
 *
 * --
 * -- Omezení pro tabulku `prefix_text`
 * --
 * ALTER TABLE `prefix_text`
 * ADD CONSTRAINT `fk_text_language` FOREIGN KEY (`IdLanguage`) REFERENCES `prefix_language` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
 *
 *
 * ## Texts
 *
 * ### AbstractTextsControl
 *
 * ### TextsControl
 *
 * obsahuje jen metody: Title, Content
 *
 * neon:
 * ```neon
 * - \TextsControl(%tb_text%)
 * ```
 *
 * basepresenter:
 * ```php
 * protected function createComponentTexts()
 * {
 * return $this->context->getByType(\TextsControl::class);
 * }
 * ```
 *
 * latte:
 * ```latte
 * {control texts:title 'homepage'}
 * {control texts:content 'homepage'}
 * ```
 *
 * mozne problemy:
 * - pri vytvareni nove stranky: page/nazev nazev nesmi obsahovat mezery a nesmi zacinat cislem,
 * pokud jsou stranky ktere vyzaduji pocatek adresy cislem tak je potreba vymyslet lepsi nazev (ident), nazev je na pozadi pouzity jako ident resp action. Slug se generuje z title.
 *
 *
 * @author  geniv
 * @package NetteWeb
 */
class TextsControl extends AbstractTextsControl
{

    /**
     * TextsControl constructor.
     * @param $tableTexts
     * @param \Dibi\Connection $database
     * @param AbstractLanguageService $language
     * @param \Nette\Caching\IStorage $cacheStorage
     */
    public function __construct($tableTexts, \Dibi\Connection $database, \AbstractLanguageService $language, \Nette\Caching\IStorage $cacheStorage)
    {
        parent::__construct($tableTexts, $database, $language, $cacheStorage);

        // explicitni nadefinovani zakladnich sloupcu
        $this->setExtraColumns(['Title', 'Content']);
    }
}
