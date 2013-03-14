-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 31 Janvier 2012 à 14:23
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
-- Structure de la table `c2iressources`
--

CREATE TABLE IF NOT EXISTS `c2iressources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `c2i` int(11) NOT NULL COMMENT 'numéro des c2i depuis la table referentiel',
  `titre` text NOT NULL,
  `domaine` varchar(30) NOT NULL COMMENT 'D1 D2...  A1... B1...',
  `competence` varchar(30) NOT NULL COMMENT '1 2 3 4 .....',
  `ordre` int(11) NOT NULL COMMENT '1 2 3 4 .....',
  `tags` text NOT NULL COMMENT 'Suite de mots clés pour selection',
  `fichier` text NOT NULL COMMENT 'chemin relatif par rapport à xxxxxx',
  `version` varchar(8) NOT NULL COMMENT 'format de date aaaammjj',
  `id_etab` int(10) NOT NULL DEFAULT '1',
  `ts_datecreation` int(11) NOT NULL,
  `ts_datemodification` int(11) NOT NULL,
  `modifiable` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=139 ;

--
-- Contenu de la table `c2iressources`
--

INSERT INTO `c2iressources` (`id`, `c2i`, `titre`, `domaine`, `competence`, `ordre`, `tags`, `fichier`, `version`, `id_etab`, `ts_datecreation`, `ts_datemodification`, `modifiable`) VALUES
(1, 1, 'L''identité numérique', 'D2', '1', 1, 'Ressources Silini-Denos', 'D2.1-1-IdentiteNumerique.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(2, 1, 'La compétence en questions', 'D2', '1', 6, 'Ressources Silini-Denos', 'D2.1-6-CompetencesEnQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(3, 1, 'La e-réputation', 'D2', '1', 5, 'Ressources Silini-Denos', 'D2.1-5-e-Reputation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(4, 1, 'Les traces numériques', 'D2', '1', 4, 'Ressources Silini-Denos', 'D2.1-4-TracesNumeriques.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(5, 1, 'L''authentification', 'D2', '1', 2, 'Ressources Silini-Denos', 'D2.1-2-Authentification.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(6, 1, 'Le paramétrage du profil', 'D2', '1', 3, 'Ressources Silini-Denos', 'D2.1-3-ParametrageProfil.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(7, 1, 'La compétence en situations', 'D2', '1', 7, 'Ressources Silini-Denos', 'D2.1-7-CompetencesEnSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(8, 1, 'Les atteintes à la vie privée', 'D2', '2', 1, 'Ressources Silini-Denos', 'D2.2-1-AtteintesViePrivee.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(9, 1, 'Le traitement automatique de l''information', 'D2', '2', 2, 'Ressources Silini-Denos', 'D2.2-2-TraitementAutomatiqueInformation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(10, 1, 'La CNIL', 'D2', '2', 3, 'Ressources Silini-Denos', 'D2.2-3-CNIL.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(11, 1, 'La collecte d''informations', 'D2', '2', 4, 'Ressources Silini-Denos', 'D2.2-4-CollecteInformations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(12, 1, 'La publication sur le web', 'D2', '2', 5, 'Ressources Silini-Denos', 'D2.2-5-PublicationWeb.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(13, 1, 'La correspondance privée', 'D2', '2', 6, 'Ressources Silini-Denos', 'D2.2-6-CorrespondancePrivee.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(14, 1, 'La compétence en questions', 'D2', '2', 7, 'Ressources Silini-Denos', 'D2.2-7-CompetencesEnQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(15, 1, 'La compétence en situations', 'D2', '2', 8, 'Ressources Silini-Denos', 'D2.2-8-CompetencesEnSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(16, 1, 'La protection des ?uvres', 'D2', '3', 1, 'Ressources Silini-Denos', 'D2.3-1-ProtectionOeuvre.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(17, 1, 'Les licences des ressources', 'D2', '3', 2, 'Ressources Silini-Denos', 'D2.3-2-LicencesRessources.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(18, 1, 'Le téléchargement de musique et de films', 'D2', '3', 3, 'Ressources Silini-Denos', 'D2.3-3-TelechargementMusiqueFilms.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(19, 1, 'L''exploitation des ressources du web', 'D2', '3', 4, 'Ressources Silini-Denos', 'D2.3-4-ExploitationRessourcesWeb.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(20, 1, 'Les licences des logiciels', 'D2', '3', 5, 'Ressources Silini-Denos', 'D2.3-5-LicencesLogiciels.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(21, 1, 'La compétence en questions', 'D2', '3', 6, 'Ressources Silini-Denos', 'D2.3-6-CompetencesEnQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(22, 1, 'La compétence en situations', 'D2', '3', 7, 'Ressources Silini-Denos', 'D2.3-7-CompetencesEnSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(23, 1, 'Le bon usage du numérique', 'D2', '4', 1, 'Ressources Silini-Denos', 'D2.4-1-BonUsageNumerique.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(24, 1, 'Les chartes', 'D2', '4', 2, 'Ressources Silini-Denos', 'D2.4-2-Chartes.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(25, 1, 'La Netiquette', 'D2', '4', 3, 'Ressources Silini-Denos', 'D2.4-3-Netiquette.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(26, 1, 'L''accessibilité', 'D2', '4', 4, 'Ressources Silini-Denos', 'D2.4-4-Accessibilite.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(27, 1, 'La compétence en questions', 'D2', '4', 5, 'Ressources Silini-Denos', 'D2.4-5-CompetencesEnQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(28, 1, 'La compétence en situations', 'D2', '4', 6, 'Ressources Silini-Denos', 'D2.4-6-CompetencesEnSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(29, 1, 'L''environnement de travail', 'D1', '1', 1, 'Ressources Silini-Denos', 'D1.1-1-EnvironnementTravail.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(30, 1, 'Poste de travail', 'D1', '1', 2, 'Ressources Silini-Denos', 'D1.1-2-PosteTravail.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(31, 1, 'La configuration du poste de travail', 'D1', '1', 3, 'Ressources Silini-Denos', 'D1.1-3-ConfigPosteTravail.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(32, 1, 'Le réseau', 'D1', '1', 4, 'Ressources Silini-Denos', 'D1.1-4-Reseau.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(33, 1, 'La connexion en réseau', 'D1', '1', 5, 'Ressources Silini-Denos', 'D1.1-5-ConnexionReseau.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(34, 1, 'L''installation des applications', 'D1', '1', 6, 'Ressources Silini-Denos', 'D1.1-6-InstallAppli.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(35, 1, 'Le choix des applications', 'D1', '1', 7, 'Ressources Silini-Denos', 'D1.1-7-ChoixAppli.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(36, 1, 'Les environnements numériques', 'D1', '1', 8, 'Ressources Silini-Denos', 'D1.1-8-EnvironnementNumerique.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(37, 1, 'Les espaces de stockage', 'D1', '1', 9, 'Ressources Silini-Denos', 'D1.1-9-EspaceStockage.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(38, 1, 'L''organisation des fichiers', 'D1', '1', 10, 'Ressources Silini-Denos', 'D1.1-10-OrganisationFichiers.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(39, 1, 'Les compétences en questions', 'D1', '1', 11, 'Ressources Silini-Denos', 'D1.1-11-CompetencesQuestion.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(40, 1, 'Les compétences en situations', 'D1', '1', 12, 'Ressources Silini-Denos', 'D1.1-12-CompetencesSituation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(41, 1, 'Les risques', 'D1', '2', 1, 'Ressources Silini-Denos', 'D1.2.1-Risques.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(42, 1, 'La protection des données', 'D1', '2', 2, 'Ressources Silini-Denos', 'D1.2.2-ProtectionDonnees.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(43, 1, 'La maîtrise des traces', 'D1', '2', 3, 'Ressources Silini-Denos', 'D1.2.3-MaitriseTraces.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(44, 1, 'Les logiciels malveillants', 'D1', '2', 4, 'Ressources Silini-Denos', 'D1.2.4-LogicielsMalveillants.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(45, 1, 'La démarche de protection', 'D1', '2', 5, 'Ressources Silini-Denos', 'D1.2.5-DemarcheProtection.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(46, 1, 'La réparation', 'D1', '2', 6, 'Ressources Silini-Denos', 'D1.2.6-Reparation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(47, 1, 'Les compétences en questions', 'D1', '2', 7, 'Ressources Silini-Denos', 'D1.2.7-CompetencesQuestion.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(48, 1, 'Les compétences en situations', 'D1', '2', 8, 'Ressources Silini-Denos', 'D1.2.8-CompetencesSituation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(49, 1, 'Les formats de fichiers', 'D1', '3', 1, 'Ressources Silini-Denos', 'D1.3.1-FormatsFichiers.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(50, 1, 'L''interoperabilité', 'D1', '3', 2, 'Ressources Silini-Denos', 'D1.3.2-Interoperabilite.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(51, 1, 'Les formats de documents', 'D1', '3', 3, 'Ressources Silini-Denos', 'D1.3.3-FormatsDocuments.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(52, 1, 'Les formats des images', 'D1', '3', 4, 'Ressources Silini-Denos', 'D1.3.4-FormatsImages.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(53, 1, 'Les autres formats', 'D1', '3', 5, 'Ressources Silini-Denos', 'D1.3.5-AutresFormats.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(54, 1, 'Les competences en questions', 'D1', '3', 6, 'Ressources Silini-Denos', 'D1.3.6-CompetencesQuestion.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(55, 1, 'Les compétences en situations', 'D1', '3', 7, 'Ressources Silini-Denos', 'D1.3.7-CompetencesSituation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(56, 1, 'L''enregistrement et la sauvegarde', 'D1', '4', 1, 'Ressources Silini-Denos', 'D1.4.1-EnregistrementSauvegarde.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(57, 1, 'La gestion des versions', 'D1', '4', 2, 'Ressources Silini-Denos', 'D1.4.2-GestionVersions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(58, 1, 'L''archivage', 'D1', '4', 3, 'Ressources Silini-Denos', 'D1.4.3-Archivage.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(59, 1, 'Les unites de mesure', 'D1', '4', 4, 'Ressources Silini-Denos', 'D1.4.4-UnitesMesure.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(60, 1, 'Les supports de stockage', 'D1', '4', 5, 'Ressources Silini-Denos', 'D1.4.5-SupportStockage.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(61, 1, 'Les compétences en questions', 'D1', '4', 6, 'Ressources Silini-Denos', 'D1.4.6-CompetencesQuestion.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(62, 1, 'Les compétences en situations', 'D1', '4', 7, 'Ressources Silini-Denos', 'D1.4.7-CompetencesSituation.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(63, 1, 'La communication numérique', 'D5', '1', 1, 'Ressources Silini-Denos', 'D5.1-1-CommunicationNumerique.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(64, 1, 'Les outils de communication', 'D5', '1', 2, 'Ressources Silini-Denos', 'D5.1-2-OutilsComm.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(65, 1, 'Les contacts', 'D5', '1', 3, 'Ressources Silini-Denos', 'D5.1-3-Contacts.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(66, 1, 'L''automatisation des tâches répétitives', 'D5', '1', 4, 'Ressources Silini-Denos', 'D5.1-4-AutomTachesRepetetives.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(67, 1, 'La configuration de la messagerie', 'D5', '1', 5, 'Ressources Silini-Denos', 'D5.1-5-ConfigMessagerie.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(68, 1, 'Les dysfonctionnements', 'D5', '1', 6, 'Ressources Silini-Denos', 'D5.1-6-Dysfonctionnements.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(69, 1, 'La compétence en questions', 'D5', '1', 7, 'Ressources Silini-Denos', 'D5.1-7-CompetencesQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(70, 1, 'La compétence en situations', 'D5', '1', 8, 'Ressources Silini-Denos', 'D5.1-8-CompetencesSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(71, 1, 'L''activité de groupe', 'D5', '2', 1, 'Ressources Silini-Denos', 'D5.2-1-ActiiviteGroupe.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(72, 1, 'Les plateformes de travail', 'D5', '2', 2, 'Ressources Silini-Denos', 'D5.2-2-PlateformeTravail.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(73, 1, 'Les outils de communication de groupe', 'D5', '2', 3, 'Ressources Silini-Denos', 'D5.2-3-OutilsCommGroupe.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(74, 1, 'Les outils de collaboration du groupe', 'D5', '2', 4, 'Ressources Silini-Denos', 'D5.2-4-OutilsCollaborationGroupe.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(75, 1, 'Les activités collaboratives sur le web', 'D5', '2', 5, 'Ressources Silini-Denos', 'D5.2-5-ActivitesCollaborativesWeb.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(76, 1, 'Les réseaux sociaux', 'D5', '2', 6, 'Ressources Silini-Denos', 'D5.2-6-ReseauxSociaux.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(77, 1, 'La collaboration informelle', 'D5', '2', 7, 'Ressources Silini-Denos', 'D5.2-7-CollaborationInformelle.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(78, 1, 'Le choix d''un outil collaboratif', 'D5', '2', 8, 'Ressources Silini-Denos', 'D5.2-8-ChoixOutilCollaboratif.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(79, 1, 'La compétence en questions', 'D5', '2', 9, 'Ressources Silini-Denos', 'D5.2-9-CompetencesQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(80, 1, 'La compétence en situations', 'D5', '2', 10, 'Ressources Silini-Denos', 'D5.2-10-CompetencesSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(81, 1, 'La production collabotrative', 'D5', '3', 1, 'Ressources Silini-Denos', 'D5.3-1-ProductionCollaborative.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(82, 1, 'L''édition en ligne', 'D5', '3', 2, 'Ressources Silini-Denos', 'D5.3-2-EditionEnLigne.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(83, 1, 'L''édition hors ligne', 'D5', '3', 3, 'Ressources Silini-Denos', 'D5.3-3-EditionHorsLigne.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(84, 1, 'La gestion des versions', 'D5', '3', 4, 'Ressources Silini-Denos', 'D5.3-4-GestionVersions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(85, 1, 'Les conflits d''accès', 'D5', '3', 5, 'Ressources Silini-Denos', 'D5.3-5-ConflitsAcces.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(86, 1, 'La compétence en questions', 'D5', '3', 6, 'Ressources Silini-Denos', 'D5.3-6-CompetencesQuestions.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(87, 1, 'La compétence en situations', 'D5', '3', 7, 'Ressources Silini-Denos', 'D5.3-7-CompetencesSituations.pdf', '20110809', 1, 1328011623, 1328014814, 0),
(88, 1, 'Le document numérique', 'D3', '1', 1, 'Ressources Silini-Denos', 'D3.1-1-DocumentNumerique.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(89, 1, 'La structure hiérarchique', 'D3', '1', 2, 'Ressources Silini-Denos', 'D3.1-2-StructureHierarchique.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(90, 1, 'La structure hypertexte', 'D3', '1', 3, 'Ressources Silini-Denos', 'D3.1-3-StructureHypertexte.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(91, 1, 'Les éléments constitutifs', 'D3', '1', 4, 'Ressources Silini-Denos', 'D3.1-4-ElementsConstitutifs.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(92, 1, 'L''automatisation de la mise en forme', 'D3', '1', 5, 'Ressources Silini-Denos', 'D3.1-5-AutomatisationMiseEnForme.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(93, 1, 'La compétence en situations', 'D3', '1', 6, 'Ressources Silini-Denos', 'D3.1-6-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(94, 1, 'Les champs', 'D3', '2', 1, 'Ressources Silini-Denos', 'D3.2-1-Champs.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(95, 1, 'Les éléments associés aux champs', 'D3', '2', 2, 'Ressources Silini-Denos', 'D3.2-2-ElementsAssociesChamps.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(96, 1, 'Les tables', 'D3', '2', 3, 'Ressources Silini-Denos', 'D3.2-3-Tables.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(97, 1, 'La compétence en situations', 'D3', '2', 4, 'Ressources Silini-Denos', 'D3.2-4-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(98, 1, 'Le document composite', 'D3', '3', 1, 'Ressources Silini-Denos', 'D3.3-1-DocumentComposite.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(99, 1, 'Les images', 'D3', '3', 2, 'Ressources Silini-Denos', 'D3.3-2-Images.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(100, 1, 'Les objets OLE', 'D3', '3', 3, 'Ressources Silini-Denos', 'D3.3-3-ObjetsOLE.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(101, 1, 'Les schémas', 'D3', '3', 4, 'Ressources Silini-Denos', 'D3.3-4-Schemas.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(102, 1, 'L''ancrage', 'D3', '3', 5, 'Ressources Silini-Denos', 'D3.3-5-Ancrage.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(103, 1, 'La compétence en situations', 'D3', '3', 6, 'Ressources Silini-Denos', 'D3.3-6-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(104, 1, 'La feuille de calcul', 'D3', '4', 1, 'Ressources Silini-Denos', 'D3.4-1-FeuilleCalcul.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(105, 1, 'La cellule', 'D3', '4', 2, 'Ressources Silini-Denos', 'D3.4-2-Cellule.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(106, 1, 'Les formules', 'D3', '4', 3, 'Ressources Silini-Denos', 'D3.4-3-Formules.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(107, 1, 'La recopie incrémentée', 'D3', '4', 4, 'Ressources Silini-Denos', 'D3.4-4-RecopieIncrementee.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(108, 1, 'Les tables de données', 'D3', '4', 5, 'Ressources Silini-Denos', 'D3.4-5-TablesDonnees.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(109, 1, 'Les graphiques', 'D3', '4', 6, 'Ressources Silini-Denos', 'D3.4-6-Graphiques.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(110, 1, 'La compétence en situations', 'D3', '4', 7, 'Ressources Silini-Denos', 'D3.4-7-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(111, 1, 'La diffusion', 'D3', '5', 1, 'Ressources Silini-Denos', 'D3.5-1-Diffusion.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(112, 1, 'Les supports de diffusion', 'D3', '5', 2, 'Ressources Silini-Denos', 'D3.5-2-SupportsDiffusion.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(113, 1, 'L''ergonomie', 'D3', '5', 3, 'Ressources Silini-Denos', 'D3.5-3-Ergonomie.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(114, 1, 'Les informations utiles', 'D3', '5', 4, 'Ressources Silini-Denos', 'D3.5-4-InformationsUtiles.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(115, 1, 'L''impression', 'D3', '5', 5, 'Ressources Silini-Denos', 'D3.5-5-Impression.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(116, 1, 'La compétence en situations', 'D3', '5', 6, 'Ressources Silini-Denos', 'D3.5-6-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(117, 1, 'La recherche d''information', 'D4', '1', 1, 'Ressources Silini-Denos', 'D4.1-1-RechercheInformation.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(118, 1, 'Les sources d''information', 'D4', '1', 2, 'Ressources Silini-Denos', 'D4.1-2-SourcesInformation.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(119, 1, 'Les catalogues de bibliothèque', 'D4', '1', 3, 'Ressources Silini-Denos', 'D4.1-3-CataloguesBibliotheque.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(120, 1, 'Les portails documentaires', 'D4', '1', 4, 'Ressources Silini-Denos', 'D4.1-4-PortailsDocumentaires.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(121, 1, 'Les annuaires de recherche', 'D4', '1', 5, 'Ressources Silini-Denos', 'D4.1-5-AnnuairesRecherche.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(122, 1, 'Les moteurs de recherche', 'D4', '1', 6, 'Ressources Silini-Denos', 'D4.1-6-MoteursRecherche.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(123, 1, 'Les requêtes', 'D4', '1', 7, 'Ressources Silini-Denos', 'D4.1-7-Requetes.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(124, 1, 'La compétence en situations', 'D4', '1', 8, 'Ressources Silini-Denos', 'D4.1-8-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(125, 1, 'Les critères d''évaluation', 'D4', '2', 1, 'Ressources Silini-Denos', 'D4.2-1-CriteresEvaluation.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(126, 1, 'L''évaluation de la fiabilite', 'D4', '2', 2, 'Ressources Silini-Denos', 'D4.2-2-EvaluationFiabilite.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(127, 1, 'Les règles de publication', 'D4', '2', 3, 'Ressources Silini-Denos', 'D4.2-3-ReglesPublication.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(128, 1, 'La compétence en situations', 'D4', '2', 4, 'Ressources Silini-Denos', 'D4.2-4-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(129, 1, 'La disponibilité d''une ressource', 'D4', '3', 1, 'Ressources Silini-Denos', 'D4.3-1-DisponibiliteRssource.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(130, 1, 'La récupération', 'D4', '3', 2, 'Ressources Silini-Denos', 'D4.3-2-Recuperation.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(131, 1, 'La référence à une ressource en ligne', 'D4', '3', 3, 'Ressources Silini-Denos', 'D4.3-3-ReferenceRessourceEnLigne.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(132, 1, 'La compétence en situations', 'D4', '3', 4, 'Ressources Silini-Denos', 'D4.3-4-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(133, 1, 'La veille informationnelle', 'D4', '4', 1, 'Ressources Silini-Denos', 'D4.4-1-VeilleInformationnelle.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(134, 1, 'L''agrégateur de flux', 'D4', '4', 2, 'Ressources Silini-Denos', 'D4.4-2-AgregateurFlux.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(135, 1, 'La lettre d''information', 'D4', '4', 3, 'Ressources Silini-Denos', 'D4.4-3-LettreInformation.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(136, 1, 'Le microblogage', 'D4', '4', 4, 'Ressources Silini-Denos', 'D4.4-4-Microblogage.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(137, 1, 'Le nomadisme', 'D4', '4', 5, 'Ressources Silini-Denos', 'D4.4-5-Nomadisme.pdf', '20110809', 1, 1328014148, 1328014814, 0),
(138, 1, 'La compétence en situations', 'D4', '4', 6, 'Ressources Silini-Denos', 'D4.4-6-CompetenceSituations.pdf', '20110809', 1, 1328014148, 1328014814, 0);
