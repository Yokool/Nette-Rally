-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Čtv 26. říj 2023, 16:18
-- Verze serveru: 10.4.28-MariaDB
-- Verze PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `nette_rally`
--
CREATE DATABASE IF NOT EXISTS `nette_rally` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `nette_rally`;

-- --------------------------------------------------------

--
-- Struktura tabulky `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `team_member`
--

CREATE TABLE `team_member` (
  `id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `team_position_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `team_member__team`
--

CREATE TABLE `team_member__team` (
  `id` int(11) NOT NULL,
  `team_member_fk` int(11) NOT NULL,
  `team_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `team_position`
--

CREATE TABLE `team_position` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `min_allowed` int(11) NOT NULL,
  `max_allowed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `team_position`
--

INSERT INTO `team_position` (`id`, `name`, `min_allowed`, `max_allowed`) VALUES
(1, 'Závodník', 1, 3),
(2, 'Spolujezdec', 1, 3),
(3, 'Technik', 1, 2),
(4, 'Manažer', 1, 1),
(5, 'Fotograf', 0, 1);

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `team_member`
--
ALTER TABLE `team_member`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_position_fk` (`team_position_fk`);

--
-- Indexy pro tabulku `team_member__team`
--
ALTER TABLE `team_member__team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_member_fk` (`team_member_fk`),
  ADD KEY `team_fk` (`team_fk`);

--
-- Indexy pro tabulku `team_position`
--
ALTER TABLE `team_position`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pro tabulku `team_member`
--
ALTER TABLE `team_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pro tabulku `team_member__team`
--
ALTER TABLE `team_member__team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pro tabulku `team_position`
--
ALTER TABLE `team_position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `team_member`
--
ALTER TABLE `team_member`
  ADD CONSTRAINT `team_member_ibfk_2` FOREIGN KEY (`team_position_fk`) REFERENCES `team_position` (`id`);

--
-- Omezení pro tabulku `team_member__team`
--
ALTER TABLE `team_member__team`
  ADD CONSTRAINT `team_member__team_ibfk_1` FOREIGN KEY (`team_fk`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `team_member__team_ibfk_2` FOREIGN KEY (`team_member_fk`) REFERENCES `team_member` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
