-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 22 Juin 2011 à 14:35
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `test2`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `idClient` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `civilite` varchar(255) DEFAULT NULL,
  `nom_entreprise` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `code_pays_facturation` varchar(5) DEFAULT NULL,
  `code_postal_facturation` varchar(255) DEFAULT NULL,
  `code_insee_facturation` varchar(255) DEFAULT NULL,
  `region_facturation` varchar(255) DEFAULT NULL,
  `municipalite_facturation` varchar(255) DEFAULT NULL,
  `ligne_adresse_facturation` varchar(255) DEFAULT NULL,
  `nom_rue_facturation` varchar(255) DEFAULT NULL,
  `numero_batiment_facturation` varchar(255) DEFAULT NULL,
  `unite_facturation` varchar(255) DEFAULT NULL,
  `boite_postale_facturation` varchar(255) DEFAULT NULL,
  `destinataire_facturation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idClient`),
  UNIQUE KEY `idClient` (`idClient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `client`
--


-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE IF NOT EXISTS `commande` (
  `idCommande` int(11) NOT NULL AUTO_INCREMENT,
  `codeCommande` varchar(255) DEFAULT NULL,
  `date_commande` int(11) DEFAULT NULL,
  `date_livraison` int(11) DEFAULT NULL,
  `etat_commande` varchar(255) DEFAULT NULL,
  `code_pays_livraison` varchar(5) DEFAULT NULL,
  `code_postal_livraison` varchar(255) DEFAULT NULL,
  `code_insee_livraison` varchar(255) DEFAULT NULL,
  `region_livraison` varchar(255) DEFAULT NULL,
  `municipalite_livraison` varchar(255) DEFAULT NULL,
  `ligne_adresse_livraison` varchar(255) DEFAULT NULL,
  `nom_rue_livraison` varchar(255) DEFAULT NULL,
  `numero_batiment_livraison` varchar(255) DEFAULT NULL,
  `unite_livraison` varchar(255) DEFAULT NULL,
  `boite_postale_livraison` varchar(255) DEFAULT NULL,
  `destinataire_livraison` varchar(255) DEFAULT NULL,
  `idClient` int(11) NOT NULL,
  `carteFidelite` varchar(255) NOT NULL,
  `commentaireClient` text NOT NULL,
  `modeLivraison` varchar(255) NOT NULL,
  PRIMARY KEY (`idCommande`),
  KEY `fk_Commande_Client1` (`idClient`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3001 ;

--
-- Contenu de la table `commande`
--


-- --------------------------------------------------------

--
-- Structure de la table `ean`
--

CREATE TABLE IF NOT EXISTS `ean` (
  `idEan` int(11) NOT NULL AUTO_INCREMENT,
  `ean` varchar(13) NOT NULL,
  `idProduit` int(11) NOT NULL,
  PRIMARY KEY (`idEan`),
  UNIQUE KEY `ean` (`ean`),
  KEY `fk_Ean_Produit` (`idProduit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15699 ;

--
-- Contenu de la table `ean`
--


-- --------------------------------------------------------

--
-- Structure de la table `est_geolocalise_dans`
--

CREATE TABLE IF NOT EXISTS `est_geolocalise_dans` (
  `idGeolocalisation` int(11) NOT NULL AUTO_INCREMENT,
  `idProduit` int(11) NOT NULL,
  `idEtagere` int(11) NOT NULL,
  PRIMARY KEY (`idGeolocalisation`),
  KEY `fk_Est_geolocalise_dans_Etagere1` (`idEtagere`),
  KEY `fk_Est_geolocalise_dans_Produits1` (`idProduit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11219 ;

--
-- Contenu de la table `est_geolocalise_dans`
--


-- --------------------------------------------------------

--
-- Structure de la table `etage`
--

CREATE TABLE IF NOT EXISTS `etage` (
  `idEtage` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) DEFAULT NULL,
  `hauteur` int(11) DEFAULT NULL,
  `largeur` int(11) DEFAULT NULL,
  `pt_depart_top` int(5) NOT NULL DEFAULT '0',
  `pt_depart_left` int(5) NOT NULL DEFAULT '0',
  `pt_arrive_top` int(5) NOT NULL DEFAULT '0',
  `pt_arrive_left` int(5) NOT NULL DEFAULT '0',
  `priorite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idEtage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `etage`
--

INSERT INTO `etage` (`idEtage`, `libelle`, `hauteur`, `largeur`, `pt_depart_top`, `pt_depart_left`, `pt_arrive_top`, `pt_arrive_left`, `priorite`) VALUES
(4, 'Rdc', 1236, 1635, 372, 378, 390, 378, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `etagere`
--

CREATE TABLE IF NOT EXISTS `etagere` (
  `idEtagere` int(11) NOT NULL AUTO_INCREMENT,
  `idSegment` int(11) NOT NULL,
  `priorite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idEtagere`),
  KEY `fk_Etagere_Segment1` (`idSegment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2954 ;

--
-- Contenu de la table `etagere`
--


-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande`
--

CREATE TABLE IF NOT EXISTS `ligne_commande` (
  `idLigne` int(11) NOT NULL AUTO_INCREMENT,
  `idProduit` int(11) NOT NULL,
  `idCommande` int(11) NOT NULL,
  `idPreparation` int(11) DEFAULT NULL,
  `quantite_commandee` int(11) NOT NULL,
  `est_dans_un_lot` int(1) DEFAULT NULL,
  `idLot` int(11) DEFAULT NULL,
  `libelle_lot` varchar(255) DEFAULT NULL,
  `code_ean_lot` int(13) DEFAULT NULL,
  `prix_unitaire_ttc` double DEFAULT NULL,
  PRIMARY KEY (`idLigne`),
  KEY `fk_Ligne_commande_Produit1` (`idProduit`),
  KEY `fk_Ligne_commande_Commande1` (`idCommande`),
  KEY `fk_Ligne_commande_Preparation1` (`idPreparation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=129120 ;

--
-- Contenu de la table `ligne_commande`
--


-- --------------------------------------------------------

--
-- Structure de la table `obstacle`
--

CREATE TABLE IF NOT EXISTS `obstacle` (
  `idobstacle` int(11) NOT NULL AUTO_INCREMENT,
  `position_top` int(11) DEFAULT NULL,
  `position_left` int(11) DEFAULT NULL,
  `hauteur` int(11) DEFAULT NULL,
  `largeur` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `idetage` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  PRIMARY KEY (`idobstacle`),
  KEY `fk_Obstacle_Etage1` (`idetage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

--
-- Contenu de la table `obstacle`
--


-- --------------------------------------------------------

--
-- Structure de la table `planning`
--

CREATE TABLE IF NOT EXISTS `planning` (
  `idplanning` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `idutilisateur` int(11) DEFAULT NULL,
  `duree` int(11) NOT NULL,
  PRIMARY KEY (`idplanning`),
  KEY `idutilisateur` (`idutilisateur`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `planning`
--


-- --------------------------------------------------------

--
-- Structure de la table `prelevement_realise`
--

CREATE TABLE IF NOT EXISTS `prelevement_realise` (
  `idPrelevement_realise` int(11) NOT NULL AUTO_INCREMENT,
  `temps` int(11) DEFAULT NULL,
  `distance` int(11) NOT NULL,
  `quantite_prelevee` int(11) NOT NULL,
  `ean_lu` varchar(13) NOT NULL,
  `prix_unitaire_ttc_lu` double DEFAULT NULL,
  `idLigne` int(11) NOT NULL,
  `idPrelevement_precedent` int(11) DEFAULT NULL,
  `modePreparation` varchar(255) NOT NULL,
  PRIMARY KEY (`idPrelevement_realise`),
  KEY `fk_Prelevement_realise_Ligne_commande1` (`idLigne`),
  KEY `fk_Prelevement_realise_Prelevement_realise1` (`idPrelevement_precedent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `prelevement_realise`
--


-- --------------------------------------------------------

--
-- Structure de la table `preparation`
--

CREATE TABLE IF NOT EXISTS `preparation` (
  `idPreparation` int(11) NOT NULL AUTO_INCREMENT,
  `duree` int(11) NOT NULL,
  `date_preparation` int(11) NOT NULL,
  `etat` int(1) DEFAULT '1',
  `prioritaire` int(1) NOT NULL DEFAULT '0',
  `idUtilisateur` int(11) NOT NULL,
  `modePreparation` varchar(255) NOT NULL,
  `typePreparation` varchar(255) NOT NULL,
  PRIMARY KEY (`idPreparation`),
  KEY `fk_Preparation_Utilisateur1` (`idUtilisateur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Contenu de la table `preparation`
--


-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE IF NOT EXISTS `produit` (
  `idProduit` int(11) NOT NULL AUTO_INCREMENT,
  `codeProduit` varchar(255) NOT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `largeur` int(11) NOT NULL,
  `hauteur` int(11) NOT NULL,
  `profondeur` int(11) NOT NULL,
  `unite_mesure` varchar(10) NOT NULL,
  `qte_par_unite_de_mesure` varchar(255) NOT NULL,
  `poids_brut` int(11) NOT NULL,
  `poids_net` int(11) NOT NULL,
  `est_poids_variable` int(1) NOT NULL,
  `priorite` int(11) DEFAULT '2',
  `tempsMoyenAccess` int(11) NOT NULL DEFAULT '30',
  PRIMARY KEY (`idProduit`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12925 ;

--
-- Contenu de la table `produit`
--


-- --------------------------------------------------------

--
-- Structure de la table `rayon`
--

CREATE TABLE IF NOT EXISTS `rayon` (
  `idRayon` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) DEFAULT NULL,
  `position_top` int(11) DEFAULT '-1',
  `position_left` int(11) DEFAULT '-1',
  `sens` int(11) NOT NULL DEFAULT '0',
  `idZone` int(11) NOT NULL,
  `hauteur` int(10) NOT NULL,
  `largeur` int(10) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'classique',
  `priorite` int(11) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idRayon`),
  KEY `fk_Rayon_Zone1` (`idZone`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1343 ;

--
-- Contenu de la table `rayon`
--


-- --------------------------------------------------------

--
-- Structure de la table `segment`
--

CREATE TABLE IF NOT EXISTS `segment` (
  `idsegment` int(11) NOT NULL AUTO_INCREMENT,
  `idrayon` int(11) NOT NULL,
  `priorite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idsegment`),
  KEY `fk_Segment_Rayon1` (`idrayon`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1939 ;

--
-- Contenu de la table `segment`
--


-- --------------------------------------------------------

--
-- Structure de la table `temps_preparation`
--

CREATE TABLE IF NOT EXISTS `temps_preparation` (
  `idtemps_preparation` int(11) NOT NULL AUTO_INCREMENT,
  `duree` int(11) NOT NULL DEFAULT '0',
  `idcommande` int(11) DEFAULT NULL,
  `idzone` int(11) DEFAULT NULL,
  PRIMARY KEY (`idtemps_preparation`),
  KEY `idcommande` (`idcommande`),
  KEY `idzone` (`idzone`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21001 ;

--
-- Contenu de la table `temps_preparation`
--


-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `user_level` varchar(100) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `derniere_connexion` int(11) DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUtilisateur`, `login`, `password`, `nom`, `prenom`, `user_level`, `template`, `photo`, `derniere_connexion`) VALUES
(1, 'administrateur', '5ebe2294ecd0e0f08eab7690d2a6ee69', 'administrateur', 'test', '3', 'navigateur', '', 1308753238);

-- --------------------------------------------------------

--
-- Structure de la table `zone`
--

CREATE TABLE IF NOT EXISTS `zone` (
  `idZone` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) DEFAULT NULL,
  `couleur` varchar(6) NOT NULL DEFAULT '000000',
  `idEtage` int(11) NOT NULL,
  `priorite` int(11) DEFAULT NULL,
  PRIMARY KEY (`idZone`),
  KEY `fk_Zone_Etage1` (`idEtage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Contenu de la table `zone`
--

INSERT INTO `zone` (`idZone`, `libelle`, `couleur`, `idEtage`, `priorite`) VALUES
(27, 'magasin', '000000', 4, NULL);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `fk_Commande_Client1` FOREIGN KEY (`idClient`) REFERENCES `client` (`idClient`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ean`
--
ALTER TABLE `ean`
  ADD CONSTRAINT `fk_Ean_Produit` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`idProduit`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `est_geolocalise_dans`
--
ALTER TABLE `est_geolocalise_dans`
  ADD CONSTRAINT `fk_Est_geolocalise_dans_Etagere1` FOREIGN KEY (`idEtagere`) REFERENCES `etagere` (`idEtagere`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Est_geolocalise_dans_Produits1` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`idProduit`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `etagere`
--
ALTER TABLE `etagere`
  ADD CONSTRAINT `fk_Etagere_Segment1` FOREIGN KEY (`idSegment`) REFERENCES `segment` (`idsegment`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
  ADD CONSTRAINT `fk_Ligne_commande_Commande1` FOREIGN KEY (`idCommande`) REFERENCES `commande` (`idCommande`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ligne_commande_Preparation1` FOREIGN KEY (`idPreparation`) REFERENCES `preparation` (`idPreparation`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Ligne_commande_Produit1` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`idProduit`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `obstacle`
--
ALTER TABLE `obstacle`
  ADD CONSTRAINT `fk_Obstacle_Etage1` FOREIGN KEY (`idetage`) REFERENCES `etage` (`idEtage`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `prelevement_realise`
--
ALTER TABLE `prelevement_realise`
  ADD CONSTRAINT `fk_Prelevement_realise_Ligne_commande1` FOREIGN KEY (`idLigne`) REFERENCES `ligne_commande` (`idLigne`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Prelevement_realise_Prelevement_realise1` FOREIGN KEY (`idPrelevement_precedent`) REFERENCES `prelevement_realise` (`idPrelevement_realise`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `preparation`
--
ALTER TABLE `preparation`
  ADD CONSTRAINT `fk_Preparation_Utilisateur1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `rayon`
--
ALTER TABLE `rayon`
  ADD CONSTRAINT `fk_Rayon_Zone1` FOREIGN KEY (`idZone`) REFERENCES `zone` (`idZone`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `segment`
--
ALTER TABLE `segment`
  ADD CONSTRAINT `fk_Segment_Rayon1` FOREIGN KEY (`idrayon`) REFERENCES `rayon` (`idRayon`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `temps_preparation`
--
ALTER TABLE `temps_preparation`
  ADD CONSTRAINT `temps_preparation_ibfk_1` FOREIGN KEY (`idcommande`) REFERENCES `commande` (`idCommande`),
  ADD CONSTRAINT `temps_preparation_ibfk_2` FOREIGN KEY (`idzone`) REFERENCES `zone` (`idZone`);

--
-- Contraintes pour la table `zone`
--
ALTER TABLE `zone`
  ADD CONSTRAINT `fk_Zone_Etage1` FOREIGN KEY (`idEtage`) REFERENCES `etage` (`idEtage`) ON DELETE NO ACTION ON UPDATE NO ACTION;
