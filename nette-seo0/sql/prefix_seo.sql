-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Čtv 17. srp 2017, 12:07
-- Verze serveru: 10.0.31-MariaDB-0ubuntu0.16.04.2
-- Verze PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `netteweb`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `prefix_seo`
--

CREATE TABLE `prefix_seo` (
  `id` int(11) NOT NULL,
  `id_locale` int(11) DEFAULT NULL COMMENT 'vazba na jazyk',
  `presenter` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL COMMENT 'id polozky',
  `title` varchar(255) DEFAULT NULL COMMENT 'titulek',
  `description` varchar(255) DEFAULT NULL COMMENT 'popisek',
  `added` datetime DEFAULT NULL COMMENT 'pridano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='seo';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_seo`
--
ALTER TABLE `prefix_seo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_locale_presenter_action_id_item_UNIQUE` (`id_locale`,`presenter`,`action`,`id_item`),
  ADD KEY `fk_router_has_locale_locale_idx` (`id_locale`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_seo`
--
ALTER TABLE `prefix_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `prefix_seo`
--
ALTER TABLE `prefix_seo`
  ADD CONSTRAINT `fk_router_has_locale_locale` FOREIGN KEY (`id_locale`) REFERENCES `prefix_locale` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
