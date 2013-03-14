-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mer 27 Juin 2007 à 09:51
-- Version du serveur: 5.0.27
-- Version de PHP: 5.2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de données: `c2i`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `c2iwebservices_clients_allow`
-- 

CREATE TABLE `c2iwebservices_clients_allow` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `client` varchar(15) NOT NULL default '0.0.0.0',
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Liste des IP autorisees' AUTO_INCREMENT=2 ;

-- 
-- Contenu de la table `c2iwebservices_clients_allow`
-- 

INSERT INTO `c2iwebservices_clients_allow` (`id`, `client`, `description`) VALUES 
(1, '127.0.0.1', 'machine locale');


-- --------------------------------------------------------

-- 
-- Structure de la table `c2iwebservices_sessions`
-- 

CREATE TABLE `c2iwebservices_sessions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sessionbegin` int(10) unsigned NOT NULL default '0',
  `sessionend` int(10) unsigned NOT NULL default '0',
  `sessionkey` varchar(32) NOT NULL default '',
  `userid` varchar(64) default NULL,
  `verified` tinyint(1) NOT NULL default '0',
  `ip` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='donnees du Web service (sessions, logs...)' AUTO_INCREMENT=1 ;


