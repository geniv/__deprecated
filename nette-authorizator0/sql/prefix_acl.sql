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
-- Struktura tabulky `prefix_acl`
--

CREATE TABLE `prefix_acl` (
  `id` int(11) NOT NULL,
  `id_role` int(11) DEFAULT NULL COMMENT 'vazba na role',
  `id_resource` int(11) DEFAULT NULL COMMENT 'vazba na zdroj',
  `id_privilege` int(11) DEFAULT NULL COMMENT 'vazba na opravneni',
  `active` tinyint(1) DEFAULT '0' COMMENT 'aktivni',
  `position` int(11) DEFAULT NULL COMMENT 'poradi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='acl';

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_resource_privilege_UNIQUE` (`id_role`,`id_resource`,`id_privilege`),
  ADD KEY `fk_acl_acl_privilege_idx` (`id_privilege`),
  ADD KEY `fk_acl_acl_role_idx` (`id_role`),
  ADD KEY `fk_acl_acl_resource_idx` (`id_resource`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `prefix_acl`
--
ALTER TABLE `prefix_acl`
  ADD CONSTRAINT `fk_acl_acl_privilege` FOREIGN KEY (`id_privilege`) REFERENCES `prefix_acl_privilege` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_acl_acl_resource` FOREIGN KEY (`id_resource`) REFERENCES `prefix_acl_resource` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_acl_acl_role` FOREIGN KEY (`id_role`) REFERENCES `prefix_acl_role` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
