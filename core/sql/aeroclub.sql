-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 22 Juin 2015 à 11:39
-- Version du serveur :  5.6.16-log
-- Version de PHP :  5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

GRANT USAGE ON *.* TO 'aeroclub_guest'@'localhost' IDENTIFIED BY PASSWORD '*DAE3B2D2167FEE31AC927E8A245E34BB78D68A1E';

GRANT SELECT, INSERT ON `aeroclub`.* TO 'aeroclub_guest'@'localhost';

GRANT USAGE ON *.* TO 'aeroclub_admin'@'localhost' IDENTIFIED BY PASSWORD '*DAE3B2D2167FEE31AC927E8A245E34BB78D68A1E';

GRANT SELECT, INSERT ON `aeroclub`.* TO 'aeroclub_admin'@'localhost';
--
-- Base de données :  `aeroclub`
--

-- --------------------------------------------------------

--
-- Structure de la table `aeronefs`
--

CREATE TABLE IF NOT EXISTS `aeronefs` (
  `num_aeronef` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '0 : planneur | 1 : remorqueur',
  `modele` varchar(100) NOT NULL,
  `immat` varchar(30) NOT NULL,
  `statut` tinyint(1) NOT NULL,
  `tarif_associe` int(11) NOT NULL,
  `dispo_envo` tinyint(1) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`num_aeronef`),
  KEY `tarif_associe` (`tarif_associe`),
  KEY `num_aeronef` (`num_aeronef`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Structure de la table `compte_utilisateur`
--

CREATE TABLE IF NOT EXISTS `compte_utilisateur` (
  `id_util` int(11) NOT NULL AUTO_INCREMENT,
  `id_club` int(11) DEFAULT NULL,
  `id_Fffvv` varchar(15) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `pseudo` varchar(15) NOT NULL,
  `profession` varchar(30) CHARACTER SET latin1 NOT NULL,
  `sexe` set('Masculin','Feminin') NOT NULL,
  `date_naissance` date NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `cp` int(11) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `tel_fixe` varchar(12) NOT NULL,
  `tel_mobile` varchar(12) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `password` varchar(8) NOT NULL,
  `date_licence` date DEFAULT NULL,
  `num_licence` varchar(10) DEFAULT NULL,
  `visit_med` date DEFAULT NULL,
  `mouvement_forfait` int(11) DEFAULT NULL,
  `cmp_regle` int(11) NOT NULL,
  `p_instructeur` tinyint(1) NOT NULL,
  `p_remorqeure` tinyint(1) NOT NULL,
  `p_eleve` tinyint(1) NOT NULL,
  `id_statut` int(11) NOT NULL,
  `p_aneg` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_util`),
  UNIQUE KEY `pseudo` (`pseudo`),
  UNIQUE KEY `id_club` (`id_club`),
  KEY `id_statut` (`id_statut`),
  KEY `id_statut_2` (`id_statut`),
  KEY `id_statut_3` (`id_statut`),
  KEY `id_util` (`id_util`),
  KEY `id_util_2` (`id_util`),
  KEY `mouvement_forfait` (`mouvement_forfait`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `config_forfait`
--

CREATE TABLE IF NOT EXISTS `config_forfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarif_associe` int(11) NOT NULL,
  `heure_forfait` time NOT NULL,
  `tarif_planeur_jeune` float DEFAULT NULL,
  `tarif_planeur_adulte` float DEFAULT NULL,
  `supplement` float DEFAULT NULL,
  `planeur_concerne` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `config_forfait_ibfk_2` (`tarif_associe`),
  KEY `config_forfait_ibfk_1` (`planeur_concerne`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `mouvement`
--

CREATE TABLE IF NOT EXISTS `mouvement` (
  `id_mouv` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_mouv` datetime NOT NULL,
  `type_mouv` set('credit','debit') NOT NULL,
  `mode_paie` set('cheque','CB','virement','ancv','especes') NOT NULL,
  `montant` float NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_gestionnaire` int(11) DEFAULT NULL,
  `type_tarif` int(11) DEFAULT NULL,
  `n_vol` int(11) DEFAULT NULL,
  `comm` varchar(150) NOT NULL COMMENT 'Renseigner au besoin le numero de cheque',
  PRIMARY KEY (`id_mouv`),
  KEY `id_client_2` (`id_client`,`id_gestionnaire`),
  KEY `type_tarif` (`type_tarif`,`n_vol`),
  KEY `id_gestionnaire` (`id_gestionnaire`),
  KEY `n_vol` (`n_vol`),
  KEY `id_mouv` (`id_mouv`),
  KEY `id_client` (`id_client`),
  KEY `id_gestionnaire_2` (`id_gestionnaire`),
  KEY `type_tarif_2` (`type_tarif`),
  KEY `n_vol_2` (`n_vol`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='1' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `operation`
--

CREATE TABLE IF NOT EXISTS `operation` (
  `id_operation` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `id_gestionnaire` int(11) NOT NULL,
  `demande_valid` tinyint(1) NOT NULL COMMENT '0 : aucune demande | 1 : demande à valider',
  `table_modif` varchar(30) NOT NULL,
  `champ_modif` varchar(20) NOT NULL,
  `valeur` varchar(100) NOT NULL,
  `typ_op` set('Ajout','supression','modification') NOT NULL,
  `date_op` datetime NOT NULL,
  PRIMARY KEY (`id_operation`),
  KEY `id_utilisateur` (`id_utilisateur`,`id_gestionnaire`),
  KEY `id_operation` (`id_operation`),
  KEY `id_utilisateur_2` (`id_utilisateur`),
  KEY `id_gestionnaire` (`id_gestionnaire`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

CREATE TABLE IF NOT EXISTS `statut` (
  `num_statut` int(11) NOT NULL AUTO_INCREMENT,
  `nom_statut` tinytext NOT NULL,
  PRIMARY KEY (`num_statut`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Structure de la table `tarif`
--

CREATE TABLE IF NOT EXISTS `tarif` (
  `id_tarif` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) NOT NULL,
  `tarif_jeune` float DEFAULT NULL,
  `tarif_adulte` float DEFAULT NULL,
  `code_barre` int(11) NOT NULL,
  PRIMARY KEY (`id_tarif`),
  UNIQUE KEY `code_barre` (`code_barre`),
  KEY `id_tarif` (`id_tarif`),
  KEY `id_tarif_2` (`id_tarif`),
  KEY `code_barre_2` (`code_barre`),
  KEY `id_tarif_3` (`id_tarif`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

-- --------------------------------------------------------

--
-- Structure de la table `vol`
--

CREATE TABLE IF NOT EXISTS `vol` (
  `id_vol` int(11) NOT NULL AUTO_INCREMENT,
  `date_vol` date NOT NULL,
  `id_planeur` int(11) DEFAULT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `id_remorqueur` int(11) DEFAULT NULL,
  `duree_vol_remq` double NOT NULL,
  `type_vol` set('solo','1er_payeur','vi_30','vi_60','partage','vi_initiation','instruction') NOT NULL,
  `id_pilote` int(11) DEFAULT NULL,
  `id_passager` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_vol`),
  KEY `type_vol` (`type_vol`),
  KEY `id_pilot` (`id_pilote`,`id_passager`),
  KEY `id_passager` (`id_passager`),
  KEY `id_vol` (`id_vol`,`id_planeur`,`id_remorqueur`,`id_pilote`,`id_passager`),
  KEY `vol_ibfk_2` (`id_remorqueur`),
  KEY `vol_ibfk_1` (`id_planeur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `aeronefs`
--
ALTER TABLE `aeronefs`
  ADD CONSTRAINT `aeronefs_ibfk_1` FOREIGN KEY (`tarif_associe`) REFERENCES `tarif` (`id_tarif`);

--
-- Contraintes pour la table `compte_utilisateur`
--
ALTER TABLE `compte_utilisateur`
  ADD CONSTRAINT `cu_ibkf_1` FOREIGN KEY (`mouvement_forfait`) REFERENCES `mouvement` (`id_mouv`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `compte_utilisateur_ibfk_1` FOREIGN KEY (`id_statut`) REFERENCES `statut` (`num_statut`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `config_forfait`
--
ALTER TABLE `config_forfait`
  ADD CONSTRAINT `config_forfait_ibfk_1` FOREIGN KEY (`planeur_concerne`) REFERENCES `aeronefs` (`num_aeronef`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `config_forfait_ibfk_2` FOREIGN KEY (`tarif_associe`) REFERENCES `tarif` (`id_tarif`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `mouvement`
--
ALTER TABLE `mouvement`
  ADD CONSTRAINT `mouvement_ibfk_4` FOREIGN KEY (`n_vol`) REFERENCES `vol` (`id_vol`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `mouvement_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `compte_utilisateur` (`id_util`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `mouvement_ibfk_2` FOREIGN KEY (`id_gestionnaire`) REFERENCES `compte_utilisateur` (`id_util`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `mouvement_ibfk_3` FOREIGN KEY (`type_tarif`) REFERENCES `tarif` (`id_tarif`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `operation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `compte_utilisateur` (`id_util`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `operation_ibfk_2` FOREIGN KEY (`id_gestionnaire`) REFERENCES `compte_utilisateur` (`id_util`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vol`
--
ALTER TABLE `vol`
  ADD CONSTRAINT `vol_ibfk_1` FOREIGN KEY (`id_planeur`) REFERENCES `aeronefs` (`num_aeronef`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vol_ibfk_2` FOREIGN KEY (`id_remorqueur`) REFERENCES `aeronefs` (`num_aeronef`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vol_ibkf_3` FOREIGN KEY (`id_pilote`) REFERENCES `compte_utilisateur` (`id_club`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vol_ibkf_4` FOREIGN KEY (`id_passager`) REFERENCES `compte_utilisateur` (`id_club`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
