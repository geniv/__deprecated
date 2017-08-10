-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Ned 06. srp 2017, 11:43
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
-- Struktura tabulky `prefix_acl_privilege`
--

CREATE TABLE `prefix_acl_privilege` (
  `id` int(11) NOT NULL,
  `privilege` varchar(255) DEFAULT NULL COMMENT 'opravneni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl opravneni';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl_privilege`
--
ALTER TABLE `prefix_acl_privilege`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `privilege_UNIQUE` (`privilege`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl_privilege`
--
ALTER TABLE `prefix_acl_privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
