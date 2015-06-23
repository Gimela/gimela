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
-- Structure de la table `tarif`
--

CREATE TABLE IF NOT EXISTS `tarif` (
  `id_tarif` int(11) NOT NULL,
  `description` varchar(200) NOT NULL,
  `tarif_jeune` float DEFAULT NULL,
  `tarif_adulte` float DEFAULT NULL,
  `code_barre` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `tarif`
--

INSERT INTO `tarif` (`id_tarif`, `description`, `tarif_jeune`, `tarif_adulte`, `code_barre`) VALUES
(1, 'Achat d''un badge', 49, 100, 136),
(2, 'Annulation frais club', 0, 0, 88),
(3, 'ASH-26', 10, 35, 59),
(7, 'ASK 21', 14.75, 18.75, 32),
(8, 'ASW 22', 0.0001, 0, 77),
(9, 'Avance sur vol', 0, 0, 42),
(10, 'Bourse au BPP FFVV', 0, 0, 113),
(11, 'Bourse au lacher FFVV', 0, 0, 114),
(12, 'Bourse au mérite FFVV', 0, 0, 116),
(13, 'Bourse aux 1000 km FFVV', 0, 0, 115),
(14, 'Bourse CDVP', 100, 100, 117),
(15, 'Bourse CFVP', 0, 0, 118),
(16, 'Bourse FFVV au lacher', 0, 0, 100),
(17, 'Bourse FFVV BIA', 0, 0, 98),
(18, 'Bourse FFVV BPP', 0, 0, 99),
(19, 'Camping  tente', 15, 15, 80),
(20, 'Camping caravane /mois', 40, 40, 55),
(21, 'Carnet de vol', 18, 18, 15),
(22, 'Carte fédérale VI', 8.5, 8.5, 53),
(23, 'Concours', 0, 0, 96),
(24, 'Contribution exceptionnelle du club', 0, 0, 135),
(25, 'Cotisation annuelle', 85, 85, 8),
(26, 'Dépannage air Local', 0.0001, 31.5, 43),
(27, 'Dépannage air remorqueur CVVFR', 0, 0, 102),
(28, 'Discus CS', 14.75, 22.7218, 54),
(29, 'Discus Sabinne', 0.0001, 0, 86),
(30, 'Duo_Discus', 6.81, 6.81, 101),
(31, 'Duo_Discus HR en Biplace confirmé', 0.0001, 39.22, 70),
(32, 'Duo_Discus HR en Ecole', 0.0001, 25, 71),
(33, 'Duo_Discus HR en perfectionnement', 0.0001, 30.67, 72),
(34, 'Duo_Discus SM en Biplace confirmé', 0.0001, 39.22, 48),
(35, 'Duo_Discus SM en Ecole', 0.0001, 25, 57),
(36, 'Duo_Discus SM en perfectionnement', 0.0001, 30.67, 58),
(37, 'Formule 20H réservé exclusiv. Au titulaire de la licence DUO', 0, 369, 106),
(38, 'Formule journalière 20 E/Jour', 20, 20, 110),
(39, 'Formule Vélivole 150 heures', 1511, 1886, 64),
(40, 'Formule Vélivole 200 heures', 1811, 2272, 63),
(41, 'Formule Vélivole 40 heures', 590, 750, 11),
(42, 'Formule Vélivole 80 heures ', 950, 1200, 62),
(43, 'Gratification exceptionnelle 8JF', 0, 0, 134),
(44, 'Janus', 14.75, 18.75, 33),
(45, 'Licence fédérale et assurance', 87.8, 167.4, 9),
(46, 'LS4', 14.75, 18.75, 36),
(52, 'LS6', 0.0001, 30.67, 68),
(53, 'LS8', 14.75, 18.75, 39),
(55, 'Manuel élève pilote', 45, 45, 16),
(56, 'Nimbus_3', 0.0001, 0, 69),
(57, 'Opération diverses', 0, 0, 66),
(58, 'Option assur annuelle pour continuer sa formation et voler à l''heure toute l''année', 87, 167.4, 111),
(59, 'Participation aux frais de fonctionnement du club', 103, 185, 52),
(60, 'Participation aux travaux d''hivers 6 jours de travail', 120, 216, 10),
(61, 'Participation frais infrastructure planeur de passage\nextérieur/jour', 5, 5, 107),
(62, 'Pauwny', 309, 309, 60),
(64, 'Pégase', 14.75, 18.75, 40),
(65, 'Piwi', 0, 0, 94),
(66, 'Planeur_Extérieur', 0.0001, 0, 41),
(67, 'Propriétaire de planeur de passage / jour', 10, 10, 81),
(68, 'Propriétaire de planeur hors hangar y compris 1/2 taxe fainéant', 528, 528, 56),
(69, 'Rallye 180Ch', 309, 309, 30),
(70, 'Remb. partiel ou total camping', 0, 0, 129),
(71, 'Remb. partiel ou total cotisation', 0, 0, 122),
(72, 'Remb. Partiel ou total forfait 150H', 0, 0, 125),
(73, 'Remb. Partiel ou total forfait 200H', 0, 0, 126),
(74, 'Remb. Partiel ou total forfait 40H', 0, 0, 123),
(75, 'Remb. Partiel ou total forfait 80H', 0, 0, 124),
(76, 'Remb. partiel ou total licence', 0, 0, 121),
(77, 'Remb. partiel ou total SIPP', 0, 0, 128),
(78, 'Remb. partiel ou total stage BIA', 0, 0, 127),
(79, 'Remb. vol impute par erreur', 0, 0, 130),
(80, 'Remboursement travaux d''hivers 6 jours de travail', 216, 216, 74),
(81, 'Remorqueur_ Extérieur', 0, 0, 31),
(82, 'Report année antérieure', 0, 0, 61),
(83, 'Stage découverte 3 jours assur compris 3 vols 2h', 195, 0, 12),
(84, 'Stage découverte 6 jours assur* compris 6 vols 4h à 5h', 395, 0, 51),
(85, 'Stage SIPP 6 vols 4h à 5h avec Licence assurance et remorqués', 435, 535, 104),
(86, 'Standard', 0, 0, 24),
(87, 'Stationnement dans le hangar par M² et Jour\n', 0.75, 0.75, 109),
(88, 'Upgrade 150H a 200H', 0, 0, 133),
(89, 'Upgrade 40H a 80H', 0, 0, 131),
(90, 'Upgrade 80H a 150H', 0, 0, 132),
(91, 'Utilisation d''une remorque plan. / jour \nmission CVVFR (hors dépannage vache) le km', 5, 5, 108),
(92, 'Utilisation remorque du club 5 euros / jours', 5, 5, 92),
(93, 'Ventus_2', 0.0001, 30.67, 65),
(94, 'VI_CVVFR', 78, 99, 25),
(95, 'VI_perso', 0, 9, 83),
(96, 'Vol découverte BIA', 75, 0, 103),
(97, 'Vol formation pilote remorqueur KL', 0, 0, 119),
(98, 'Vol formation pilote remorqueur UP', 0, 0, 120),
(99, 'Vol_de_contrôle', 0, 0, 26),
(100, 'Vol_Extérieur', 0, 0, 23),
(101, 'Remorquage (pour 1/100 d''heure)', 3.09, 3.09, 150),
(103, 'Ventus', 4.75, 4.75, 153);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `tarif`
--
ALTER TABLE `tarif`
  ADD PRIMARY KEY (`id_tarif`),
  ADD UNIQUE KEY `code_barre` (`code_barre`),

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `tarif`
--
ALTER TABLE `tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=104;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
