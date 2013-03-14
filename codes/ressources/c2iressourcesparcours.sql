-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 31 Janvier 2012 à 14:19
-- Version du serveur: 5.1.41
-- Version de PHP: 5.3.2-1ubuntu4.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `pfc2i_tmp`
--

-- --------------------------------------------------------

--
-- Structure de la table `c2iressourcesparcours`
--

CREATE TABLE IF NOT EXISTS `c2iressourcesparcours` (
  `id_parcours` int(11) NOT NULL DEFAULT '0',
  `id_ressource` int(11) NOT NULL DEFAULT '0',
  `ts_datecreation` int(10) DEFAULT '0',
  `ts_datevalidation` int(10) DEFAULT '0',
  `ts_datemodification` int(10) DEFAULT '0',
  PRIMARY KEY (`id_parcours`,`id_ressource`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `c2iressourcesparcours`
--