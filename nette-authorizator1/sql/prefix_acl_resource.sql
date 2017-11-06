-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost:3306
-- Vytvořeno: Pon 06. lis 2017, 12:32
-- Verze serveru: 10.1.26-MariaDB-0+deb9u1
-- Verze PHP: 7.0.19-1

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
-- Struktura tabulky `prefix_acl_resource`
--

CREATE TABLE `prefix_acl_resource` (
  `id` int(11) NOT NULL,
  `resource` varchar(100) DEFAULT NULL COMMENT 'zdroj',
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl zdroje';

--
-- Vypisuji data pro tabulku `prefix_acl_resource`
--

INSERT INTO `prefix_acl_resource` (`id`, `resource`, `name`) VALUES
(1, 'article', 'članky'),
(2, 'comment', 'komentáře');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl_resource`
--
ALTER TABLE `prefix_acl_resource`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `resource_UNIQUE` (`resource`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl_resource`
--
ALTER TABLE `prefix_acl_resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
