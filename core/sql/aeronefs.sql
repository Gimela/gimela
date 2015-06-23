-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 23 Juin 2015 à 11:05
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
-- Structure de la table `aeronefs`
--

CREATE TABLE IF NOT EXISTS `aeronefs` (
  `num_aeronef` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0 : planneur | 1 : remorqueur',
  `modele` varchar(100) NOT NULL,
  `immat` varchar(30) NOT NULL,
  `statut` tinyint(1) NOT NULL,
  `tarif_associe` int(11) NOT NULL,
  `dispo_envo` tinyint(1) NOT NULL,
  `commentaire` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `aeronefs`
--

INSERT INTO `aeronefs` (`num_aeronef`, `type`, `modele`, `immat`, `statut`, `tarif_associe`, `dispo_envo`, `commentaire`) VALUES
(2, 1, 'RM FLAH', 'F-BRKL', 1, 101, 1, 'Remorqueur'),
(5, 0, 'ASK 21', 'F-AS12', 1, 7, 0, ''),
(6, 0, 'ASKW 22', 'F-ASW2', 1, 8, 0, ''),
(7, 0, 'DISCUS CS', 'F-DISCS', 1, 28, 0, ''),
(11, 0, 'DUO DISCUS', 'F-DUO', 1, 30, 0, ''),
(12, 0, 'LS4', 'F-LS4', 1, 46, 0, ''),
(13, 0, 'JANUS', 'F-JANUS', 0, 44, 0, ''),
(14, 0, 'Pegase', 'F-PEG', 1, 64, 0, ''),
(15, 0, 'LS8', 'F-LS8', 1, 53, 0, ''),
(16, 0, 'LS6', 'F-LS6', 1, 52, 0, ''),
(17, 0, 'DUO DISCUS HR BIPLACE CONFIRME', 'F-DUO', 1, 31, 0, ''),
(18, 0, 'DUO DISCUS HR ECOLE', 'F-DUO', 0, 32, 0, ''),
(19, 0, 'DUO DISCUS HR PERF', 'F-DUO', 0, 33, 0, ''),
(20, 0, 'DUO DISCUS SM BI CONF', 'F-DUO', 1, 34, 0, ''),
(21, 0, 'DUO DISCUS SM ECOLE', 'F-DUO', 1, 35, 0, ''),
(22, 0, 'DUO DISCUS SM PERF', 'F-DUO', 1, 36, 0, ''),
(23, 0, 'VENTUS', 'F-VENTUS', 1, 103, 0, ''),
(24, 0, 'DUO TESTEUR', 'F-CHAM', 1, 30, 0, '');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `aeronefs`
--
ALTER TABLE `aeronefs`
  ADD PRIMARY KEY (`num_aeronef`),

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `aeronefs`
--
ALTER TABLE `aeronefs`
  MODIFY `num_aeronef` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `aeronefs`
--
ALTER TABLE `aeronefs`
  ADD CONSTRAINT `aeronefs_ibfk_1` FOREIGN KEY (`tarif_associe`) REFERENCES `tarif` (`id_tarif`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
