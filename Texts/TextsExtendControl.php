<?php

/**
 * Class TextsExtendControl
 * pro libovolne plneni obsahu webu s libovolnymi sloupcemi
 *
 *
 * --
 * -- Struktura tabulky `prefix_text_extended`
 * --
 *
 * CREATE TABLE IF NOT EXISTS `prefix_text_extended` (
 * `Id` int(11) NOT NULL,
 * `IdLanguage` int(11) DEFAULT NULL COMMENT 'jazyk',
 * `Ident` varchar(100) DEFAULT NULL COMMENT 'identifikator',
 * `Title` varchar(255) DEFAULT NULL COMMENT 'titulek',
 * `Title2` varchar(45) DEFAULT NULL COMMENT 'titulek2',
 * `Title3` varchar(45) DEFAULT NULL COMMENT 'titulek3',
 * `Content` text COMMENT 'obsah',
 * `Content2` text COMMENT 'obsah2'
 * ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='rozsirene texty';
 *
 * --
 * -- Vypisuji data pro tabulku `prefix_text_extended`
 * --
 *
 * INSERT INTO `prefix_text_extended` (`Id`, `IdLanguage`, `Ident`, `Title`, `Title2`, `Title3`, `Content`, `Content2`) VALUES
 * (1, 2, 'homepage1', '## Title ## - homepage1 - 2', '## Title2 ## - homepage1 - 2', '## Title3 ## - homepage1 - 2', '## Content ## - homepage1 - 2', '## Content2 ## - homepage1 - 2'),
 * (2, 2, 'homepage2', '## Title ## - homepage2 - 2', '## Title2 ## - homepage2 - 2', '## Title3 ## - homepage2 - 2', '<p>## Content ## - homepage2 - 2</p>\n', '<p>## Content2 ## - homepage2 - 2</p>\n'),
 * (3, 1, 'homepage1', '## Title ## - homepage1 - 1', '## Title2 ## - homepage1 - 1', '## Title3 ## - homepage1 - 1', '## Content ## - homepage1 - 1', '## Content2 ## - homepage1 - 1'),
 * (4, 1, 'homepage2', '## Title ## - homepage2 - 1', '## Title2 ## - homepage2 - 1', '## Title3 ## - homepage2 - 1', '<p>## Content ## - homepage2 - 1</p>\n', '<p>## Content2 ## - homepage2 - 1</p>\n');
 *
 * --
 * -- Klíče pro tabulku `prefix_text_extended`
 * --
 * ALTER TABLE `prefix_text_extended`
 * ADD PRIMARY KEY (`Id`), ADD KEY `fk_text_extended_language_idx` (`IdLanguage`);
 *
 * --
 * -- AUTO_INCREMENT pro tabulku `prefix_text_extended`
 * --
 * ALTER TABLE `prefix_text_extended`
 * MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
 *
 * --
 * -- Omezení pro tabulku `prefix_text_extended`
 * --
 * ALTER TABLE `prefix_text_extended`
 * ADD CONSTRAINT `fk_text_extended_language` FOREIGN KEY (`IdLanguage`) REFERENCES `prefix_language` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
 *
 *
 * protected function createComponentTextsExtended1()
 * {
 *      return $this->context->getService('extend1')
 *          ->setExtraColumns(['Title', 'Title2', 'Title3', 'Content', 'Content2']);
 *          ->setAutoCreate(true);
 * }
 *
 *
 * ### TextsExtend
 *
 * v zakladu neobsahu zadne metody, musi se nastavit pomoci: setExtraColumns(array)
 *
 * ```sql
 * CREATE TABLE `prefix_text_extension` (
 * `Id` int(11) NOT NULL AUTO_INCREMENT,
 * `Language` varchar(10) NOT NULL COMMENT 'jazyk',
 * `Ident` varchar(100) NOT NULL COMMENT 'identifikator',
 * `Title` varchar(255) DEFAULT NULL COMMENT 'titulek',
 * `Title2` varchar(45) DEFAULT NULL,
 * `Title3` varchar(45) DEFAULT NULL,
 * `Content` text COMMENT 'obsah',
 * `Content2` text,
 * PRIMARY KEY (`Id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='rozsiritelne texty';
 * ```
 *
 * neon:
 * ```neon
 * - \TextsExtendControl(%tb_prefix%text_extension)
 * ```
 *
 * basepresenter:
 * ```php
 * protected function createComponentTextsExtend()
 * {
 * return $this->context->getByType(\TextsExtendControl::class)
 * ->setExtraColumns(['Title', 'Title2', 'Title3', 'Content', 'Content2']);
 * }
 * ```
 *
 * latte:
 * ```latte
 * {control textsExtend:title 'homepage2'}
 * {control textsExtend:title2 'homepage2'}
 * {control textsExtend:title3 'homepage2'}
 * {control textsExtend:content 'homepage2'}
 * {control textsExtend:content2 'homepage2'}
 * ```
 *
 *
 * @author  geniv
 * @package NetteWeb
 */
class TextsExtendControl extends AbstractTextsControl
{
}
