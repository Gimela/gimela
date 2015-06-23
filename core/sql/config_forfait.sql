-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 23 Juin 2015 à 11:00
-- Version du serveur :  5.6.16-log
-- Version de PHP :  5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `aeroclub`
--

-- --------------------------------------------------------

--
-- Structure de la table `config_forfait`
--

CREATE TABLE IF NOT EXISTS `config_forfait` (
  `id` int(11) NOT NULL,
  `tarif_associe` int(11) NOT NULL,
  `heure_forfait` time NOT NULL,
  `tarif_planeur_jeune` float DEFAULT NULL,
  `tarif_planeur_adulte` float DEFAULT NULL,
  `supplement` float DEFAULT NULL,
  `planeur_concerne` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `config_forfait`
--

INSERT INTO `config_forfait` (`id`, `tarif_associe`, `heure_forfait`, `tarif_planeur_jeune`, `tarif_planeur_adulte`, `supplement`, `planeur_concerne`) VALUES
(6, 41, '40:00:00', 14.75, 18.75, 0, 7),
(8, 41, '40:00:00', 14.75, 18.75, 0, 12),
(9, 41, '40:00:00', 14.75, 18.75, 0, 13),
(10, 41, '40:00:00', 14.75, 18.75, 0, 14),
(11, 41, '40:00:00', 14.75, 18.75, 4.75, 15),
(12, 41, '40:00:00', 14.75, 18.75, 4.75, 16),
(13, 41, '40:00:00', 14.75, 18.75, 4.75, 23),
(14, 41, '40:00:00', 14.75, 18.75, 6.81, 11),
(15, 41, '40:00:00', 14.75, 18.75, 6.81, 19),
(16, 41, '40:00:00', 14.75, 18.75, 6.81, 17),
(17, 41, '40:00:00', 14.75, 18.75, 6.81, 18),
(19, 41, '40:00:00', 14.75, 18.75, 6.81, 21),
(20, 41, '40:00:00', 14.75, 18.75, 6.81, 22),
(22, 42, '80:00:00', 12, 0, 12, 7),
(23, 42, '80:00:00', 11.88, 0, 9, 12),
(24, 42, '80:00:00', 10, 0, 0, 13),
(25, 42, '80:00:00', 11.88, 0, 0, 14),
(26, 42, '80:00:00', 11.88, 0, 4.75, 15),
(27, 42, '80:00:00', 10, 0, 4.75, 16),
(28, 42, '80:00:00', 10.88, 0, 4.75, 23),
(29, 42, '80:00:00', 11.88, 0, 5, 11),
(30, 42, '80:00:00', 11.88, 0, 6.81, 17),
(31, 42, '80:00:00', 11.88, 0, 6.81, 18),
(32, 42, '80:00:00', 11.88, 0, 6.81, 19),
(33, 42, '80:00:00', 11.88, 0, 6.81, 20),
(34, 42, '80:00:00', 11.88, 0, 6.81, 21),
(35, 42, '80:00:00', 11.88, 0, 6.81, 22),
(36, 39, '150:00:00', 10.07, 12.57, 0, 5),
(37, 39, '150:00:00', 10.07, 12.57, 0, 7),
(38, 39, '150:00:00', 10.07, 12.57, 0, 12),
(39, 39, '150:00:00', 10.07, 12.57, 0, 13),
(40, 39, '150:00:00', 10.07, 12.57, 0, 14),
(41, 39, '150:00:00', 10.07, 12.57, 4.75, 15),
(42, 39, '150:00:00', 10.07, 12.57, 4.75, 16),
(43, 39, '150:00:00', 10.07, 12.57, 4.75, 23),
(44, 39, '150:00:00', 10.07, 12.57, 6.81, 11),
(45, 39, '150:00:00', 10.07, 12.57, 6.81, 17),
(46, 39, '150:00:00', 10.07, 12.57, 6.81, 18),
(47, 39, '150:00:00', 10.07, 12.57, 6.81, 19),
(48, 39, '150:00:00', 10.07, 12.57, 6.81, 20),
(49, 39, '150:00:00', 10.07, 12.57, 6.81, 21),
(50, 39, '150:00:00', 10.07, 12.57, 6.81, 22),
(51, 40, '200:00:00', 9.06, 11.36, 0, 5),
(52, 40, '200:00:00', 9.06, 11.36, 0, 7),
(53, 40, '200:00:00', 9.06, 11.36, 0, 12),
(54, 40, '200:00:00', 9.06, 11.36, 0, 13),
(55, 40, '200:00:00', 9.06, 11.36, 0, 14),
(56, 40, '200:00:00', 9.06, 11.36, 4.75, 15),
(57, 40, '200:00:00', 9.06, 11.36, 4.75, 16),
(58, 40, '200:00:00', 9.06, 11.36, 4.75, 23),
(59, 40, '200:00:00', 9.06, 11.36, 6.81, 11),
(60, 40, '200:00:00', 9.06, 11.36, 6.81, 17),
(61, 40, '200:00:00', 9.06, 11.36, 6.81, 18),
(62, 40, '200:00:00', 9.06, 11.36, 6.81, 19),
(63, 40, '200:00:00', 9.06, 11.36, 6.81, 20),
(64, 40, '200:00:00', 9.06, 11.36, 6.81, 21),
(65, 40, '200:00:00', 9.06, 11.36, 6.81, 22),
(74, 41, '40:00:00', 21, 18, 40, 5),
(77, 37, '20:00:00', 0, 15, 5, 19);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `config_forfait`
--
ALTER TABLE `config_forfait`
  ADD PRIMARY KEY (`id`),
 
--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `config_forfait`
--
ALTER TABLE `config_forfait`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=78;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `config_forfait`
--
ALTER TABLE `config_forfait`
  ADD CONSTRAINT `config_forfait_ibfk_1` FOREIGN KEY (`planeur_concerne`) REFERENCES `aeronefs` (`num_aeronef`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `config_forfait_ibfk_2` FOREIGN KEY (`tarif_associe`) REFERENCES `tarif` (`id_tarif`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
