-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Sob 24. čen 2017, 23:10
-- Verze serveru: 10.0.29-MariaDB-0ubuntu0.16.04.1
-- Verze PHP: 7.0.18-0ubuntu0.16.04.1

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
-- Struktura tabulky `prefix_identity`
--

CREATE TABLE `prefix_identity` (
  `id` int(11) NOT NULL,
  `login` varchar(50) DEFAULT NULL COMMENT 'login',
  `hash` varchar(100) DEFAULT NULL COMMENT 'otisk hesla',
  `role` varchar(20) DEFAULT NULL COMMENT 'role',
  `username` varchar(100) DEFAULT NULL COMMENT 'jmeno uzivatele',
  `email` varchar(100) DEFAULT NULL COMMENT 'email',
  `active` tinyint(1) DEFAULT '0' COMMENT 'aktivni',
  `added` datetime DEFAULT NULL COMMENT 'pridano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='tabulka uzivatelu pro prihlaseni do administrace webu';

--
-- Vypisuji data pro tabulku `prefix_identity`
--

-- data

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_identity`
--
ALTER TABLE `prefix_identity`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_identity`
--
ALTER TABLE `prefix_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
