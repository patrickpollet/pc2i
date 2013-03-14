-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Lundi 14 Août 2006 à 04:55
-- Version du serveur: 4.1.9
-- Version de PHP: 4.3.10
-- 
-- Base de données: `developpez`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `inlinemod`
-- 

CREATE TABLE `inlinemod` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) collate latin1_general_ci NOT NULL default '',
  `prenom` varchar(255) collate latin1_general_ci NOT NULL default '',
  `adresse` tinytext collate latin1_general_ci NOT NULL,
  `code_postal` varchar(5) collate latin1_general_ci NOT NULL default '',
  `ville` varchar(255) collate latin1_general_ci NOT NULL default '',
  `enfants` int(11) NOT NULL default '0',
  `email` varchar(255) collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- 
-- Contenu de la table `inlinemod`
-- 

INSERT INTO `inlinemod` VALUES (1, 'Martel', 'Myriam', '14 rue Rebeval', '75019', 'Paris', 2, 'lulumartl@email.fr');
INSERT INTO `inlinemod` VALUES (2, 'Dupond', 'Albert', '2 Boulevard Morland', '75004', 'Paris', 0, 'albert.dupond@email.fr');
