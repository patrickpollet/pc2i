-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 25 Février 2013 à 16:08
-- Version du serveur: 5.1.67
-- Version de PHP: 5.3.5-1ubuntu7.2ppa1~lucid1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `c2i1utf8`
--

-- --------------------------------------------------------

--
-- Structure de la table `c2ialinea`
--

CREATE TABLE IF NOT EXISTS `c2ialinea` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'nouvelle clé autonum rev 1.5.1',
  `referentielc2i` varchar(5) NOT NULL DEFAULT '',
  `alinea` tinyint(2) NOT NULL DEFAULT '0',
  `aptitude` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competence` (`referentielc2i`,`alinea`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='liste des compétences' AUTO_INCREMENT=1 ;



--
-- Structure de la table `c2icache_filters`
--

CREATE TABLE IF NOT EXISTS `c2icache_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filter` varchar(32) NOT NULL DEFAULT '',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  `md5key` varchar(32) NOT NULL DEFAULT '',
  `rawtext` text NOT NULL,
  `timemodified` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `filtermd5key` (`filter`,`md5key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='For keeping information about cached data' AUTO_INCREMENT=1 ;



--
-- Structure de la table `c2iconfig`
--

CREATE TABLE IF NOT EXISTS `c2iconfig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categorie` varchar(255) NOT NULL DEFAULT '',
  `cle` varchar(255) NOT NULL DEFAULT '',
  `valeur` text NOT NULL,
  `defaut` text NOT NULL,
  `description` text NOT NULL,
  `modifiable` tinyint(1) NOT NULL DEFAULT '0',
  `validation` varchar(255) NOT NULL DEFAULT 'required',
  `drapeau` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Variables de  configuration globales' AUTO_INCREMENT=210 ;

--
-- Contenu de la table `c2iconfig`
--

INSERT INTO `c2iconfig` (`id`, `categorie`, `cle`, `valeur`, `defaut`, `description`, `modifiable`, `validation`, `drapeau`) VALUES
(1, 'pfc2i', 'version', '2.0', '1.5\r\n', '', 0, 'required', 1),
(2, 'pfc2i', 'prefix', 'c2i', 'c2i', '', 1, 'required', 1),
(3, 'pfc2i', 'date_creation_config', '1244212885', '1244212885', 'ne pas modifier !!!', 0, 'required', 1),
(4, 'pfc2i', 'date_derniere_maj', '1244212885', '1244212885', 'ne pas modifier !!!', 0, 'required', 1),
(5, 'pfc2i', 'version_release', '20090415', '20090415', 'ne pas modifier !!!', 0, 'required', 1),
(6, 'pfc2i', 'chemin_ressources', '/var/www/c2i/V2/ressources', '/Votre dossier de ressources : chemin absolu', 'ce dossier doit être autorisé en écriture au compte apache', 1, 'required', 1),
(7, 'pfc2i', 'wwwroot', 'http://prope.insa-lyon.fr/c2i/V2', 'https://univ.domaine.fr/plate-forme', 'adresse internet plateforme', 1, 'required', 1),
(8, 'pfc2i', 'encodage', 'UTF-8', 'ISO-8859-1', 'ne pas modifier !!!', 1, 'required', 1),
(9, 'pfc2i', 'adresse_version', 'http://c2i.education.fr/version.txt', 'http://c2i.education.fr/version.txt', 'ne pas modifier !!!', 1, 'required', 1),
(10, 'pfc2i', 'langue', 'fr', 'fr', '', 0, 'required', 1),
(11, 'pfc2i', 'verifier_install', '0', '1', '', 0, 'required', 1),
(12, 'ldap', 'ldap_version', '2', '2', 'changer en 3 si nécessaire', 1, 'required', 1),
(13, 'pfc2i', 'csv_separateur', ';', ';', '', 1, 'required', 1),
(14, 'pfc2i', 'tsv_separateur', '', '', '', 0, 'required', 1),
(180, 'examen', 'convoque_mail_copie_auteur', '0', '0', 'lors des convocations spar email, envoyer aussi une copie à l''auteur', 1, 'required', 1),
(16, 'dev', 'debug_templates', '0', '0', '', 1, 'required', 1),
(17, 'dev', 'debug_in_html', '0', '0', 'ajouter des infos de debug dans la page html', 1, 'required', 1),
(18, 'dev', 'debug_level', '0', '0', 'niveau de debug php', 1, 'required', 1),
(19, 'dev', 'syslog_level', '0', '0', '', 1, 'required', 1),
(20, 'pfc2i', 'largeur_minipopups', '460', '460', '', 1, 'required', 1),
(21, 'pfc2i', 'largeur_popups', '900', '900', '', 1, 'required', 1),
(22, 'pfc2i', 'hauteur_minipopups', '320', '320', '', 1, 'required', 1),
(23, 'pfc2i', 'hauteur_popups', '600', '600', '', 1, 'required', 1),
(24, 'question', 'nombre_reponses_maxi', '5', '5', '', 1, 'required', 1),
(25, 'question', 'nombre_reponses_mini', '4', '4', '', 1, 'required', 1),
(26, 'question', 'max_documents_par_question', '5', '5', 'nombre de documents attachés aux questions', 1, 'required', 1),
(27, 'question', 'peut_dupliquer_question', '1', '1', 'autoriser duplication (non sur la nationale)', 1, 'required', 1),
(28, 'notion', 'peut_dupliquer_notion', '1', '1', 'autoriser duplication ', 1, 'required', 1),
(29, 'examen', 'peut_dupliquer_examen', '1', '1', 'autoriser duplication ', 1, 'required', 1),
(30, 'candidat', 'peut_dupliquer_inscrit', '1', '1', 'autoriser duplication ', 1, 'required', 1),
(31, 'profil', 'peut_dupliquer_profil', '0', '0', 'autoriser duplication ', 1, 'required', 1),
(32, 'utilisateur', 'peut_dupliquer_utilisateur', '0', '0', 'autoriser duplication ', 1, 'required', 1),
(33, 'etablissement', 'peut_dupliquer_etablissement', '0', '0', 'autoriser duplication ', 1, 'required', 1),
(34, 'parcours', 'peut_dupliquer_parcours', '1', '1', 'autoriser duplication ', 1, 'required', 1),
(35, 'compte', 'longueur_mini_login', '5', '5', '', 1, 'required', 1),
(36, 'compte', 'longueur_mini_password', '5', '5', '', 1, 'required', 1),
(37, 'compte', 'montrer_password_inscrits', '1', '1', '', 1, 'required', 1),
(38, 'notion', 'nombre_liens_maxi', '5', '5', '', 1, 'required', 1),
(39, 'notion', 'nombre_liens_mini', '1', '1', '', 1, 'required', 1),
(40, 'utilisateur', 'regle_nom_prenom', '1', '1', '1 pour nom prenom 2 pour prénom nom', 1, 'required', 1),
(41, 'utilisateur', 'regle_nom_en_majuscule', '0', '0', '1 pour oui, 0 pour non ', 1, 'required', 1),
(42, 'pfc2i', 'err_mysql_avec_requete', '1', '0', 'ajoute la requeste SQL a l''erreur fatale', 1, 'required', 1),
(43, 'pfc2i', 'log_erreur_fatale', '1', '1', 'trace dans c2itracking ou non', 1, 'required', 1),
(44, 'dev', 'dump_vars', '0', '0', 'ajoute variables globales à l''HTML', 1, 'required', 1),
(45, 'pfc2i', 'multip_parpage', '0', '0', 'option par page en multipagination (a debogger)', 0, 'required', 1),
(46, 'pfc2i', 'multip_haut', '1', '1', 'multipagination en haut', 1, 'required', 1),
(47, 'pfc2i', 'multip_bas', '1', '1', 'multipagination en bas', 1, 'required', 1),
(176, 'question', 'peut_filtrer_question_cert', '0', '0', 'filtrage des questions de certification', 1, 'required', 1),
(175, 'question', 'peut_filtrer_question_pos', '0', '0', 'filtrage des questions de positionnement', 1, 'required', 1),
(50, 'exp', 'prof_peut_passer_qcm', '0', '0', 'les ''profs '' peuvent ils passer des qcm (experimental)', 1, 'required', 1),
(51, 'exp', 'prof_peut_avoir_parcours', '0', '0', 'les ''profs'' peuvent ils passer des qcm (experimental)', 1, 'required', 1),
(52, 'notion', 'peut_modifier_notion_nationale', '0', '0', '', 1, 'required', 1),
(53, 'pfc2i', 'theme', 'v15', 'v15', 'chemin vers le thème', 1, 'required', 1),
(54, 'pfc2i', 'bodydir', 'ltr', 'ltr', 'clause dir du body', 1, 'required', 1),
(55, 'dev', 'tpl_pas_trad_auto', '0', '0', 'developeurs : test traduction automatique des templates ', 0, 'required', 1),
(56, 'dev', 'tpl_montrer_balises', '0', '0', 'developeurs :dans ce mode on voit, entre autres les chaines codées en dur (ca casse aussi les thèmes) ', 0, 'required', 1),
(58, 'pfc2i', 'utiliser_infobulle_js', '1', '1', 'utiliser la javascript infobulle (me pas modifier)', 0, 'required', 1),
(59, 'pfc2i', 'adresse_pl_nationale', 'https://c2i.education.fr/pfv3/', 'https://c2i.education.fr/pfv3/', '', 0, 'required', 1),
(60, 'pfc2i', 'adresse_wiki', 'http://c2i.education.fr/wikipfc2i-X/index.php/Accueil', 'http://c2i.education.fr/wikipfc2i-X/index.php/Accueil', '', 0, 'required', 1),
(61, 'pfc2i', 'adresse_forum', 'http://www.c2i.education.fr/forum-c2i-1/', 'http://www.c2i.education.fr/forum-c2i-1/', '', 0, 'required', 1),
(62, 'candidat', 'peut_creer_compte_inscrit', '0', '0', 'création manuel de compte étudiant sans passer par inscriptions', 1, 'required', 1),
(63, 'pfc2i', 'universite_serveur', '0', '0', 'code national de l''établissement (ne pas modifier)', 0, 'required', 1),
(65, 'examen', 'examen_seuil_validation', '50', '50', 'seuil de validation par défaut d''un examen', 1, 'required', 1),
(66, 'examen', 'examen_type_tirage_defaut', 'aleatoire', 'aleatoire', 'type de tirage par défaut : manuel ou aleatoire', 1, 'required', 1),
(67, 'examen', 'examen_ordre_questions_defaut', 'aleatoire', 'aleatoire', 'ordre des questions par défaut : fixe ou aleatoire', 1, 'required', 1),
(68, 'examen', 'examen_ordre_reponses_defaut', 'aleatoire', 'aleatoire', 'ordre des réponses par défaut : fixe ou aleatoire', 1, 'required', 1),
(69, 'pool', 'autoriser_pool', '1', '1', 'autoriser les pools d''examen', 1, 'required', 1),
(70, 'pool', 'pool_nb_questions_defaut', '180', '180', 'nombre de questions à placer dans un pool par défaut', 1, 'required', 1),
(71, 'pool', 'pool_nb_groupes_defaut', '5', '5', 'nombre de groupes à placer dans un pool par défaut', 1, 'required', 1),
(72, 'pfc2i', 'theme_js_calendar', 'system', 'system', 'utiliser ce théme pour js_calendar', 1, 'required', 1),
(73, 'pfc2i', 'montrer_progression_ajax', '1', '1', '', 1, 'required', 1),
(74, 'pfc2i', 'montrer_fiche_apres_modification', '1', '1', 'après une insertion, modification d''un item, montrer la fiche correspondante ou retourner à la liste', 1, 'required', 1),
(75, 'examen', 'examen_date_defaut', '1', '1', 'date de l''examen par défaut 0: aujourd''hui 1 demain ...', 1, 'required', 1),
(76, 'examen', 'examen_heure_debut_defaut', '9', '9', 'heure de début par défaut d''un examen', 1, 'required', 1),
(77, 'examen', 'examen_minute_debut_defaut', '15', '15', 'minutes de début par défaut d''un examen', 1, 'required', 1),
(78, 'examen', 'examen_duree_defaut', '1', '1', 'durée par défaut d''un examen', 1, 'required', 1),
(79, 'examen', 'nombre_decimales_score', '1', '1', 'TESTS : nombre de décimales pour AFFICHER le score (0,1, ou 2) ', 1, 'required', 1),
(80, 'pfc2i', 'cas', '0', '0', '', 1, 'required', 1),
(81, 'pfc2i', 'cas_force', '0', '0', '', 1, 'required', 1),
(82, 'ldap', 'synchro_infos_ldap_a_la_connexion', '1', '1', '', 1, 'required', 1),
(83, 'compte', 'longueur_mot_de_passe_aleatoire', '6', '6', '', 1, 'required validate-digits', 1),
(84, 'compte', 'compte_sans_nom_prenom_ok', '0', '0', '', 1, 'required validate-digits', 1),
(85, 'compte', 'compte_sans_mail_ok', '1', '1', '', 1, 'required validate-digits', 1),
(86, 'parcours', 'utiliser_notions_parcours', '1', '1', 'gérer les notions et les parcours', 1, 'required validate-digits', 1),
(87, 'examen', 'permettre_repasser_examen', '1', '1', 'autoriser les ''personnels'' à autoriser les repassages de QCM', 1, 'required validate-digits', 1),
(88, 'pfc2i', 'migration_15', '0', '0', 'Les résultats V 1.4 ont été migrés ou non (fait une seule fois) au 1er appel d''une fonction d''affichage de résultats', 1, 'required', 1),
(89, 'pfc2i', 'max_taille_fichiers_uploades', '10000000', '10000000', '(10Mo) doit etre en accord avec les reglages de php.ini et apache !', 1, 'required', 1),
(90, 'pfc2i', 'marqueur_apogee_par_de_note', 'DEF', 'DEF', '', 1, 'required', 1),
(91, 'pfc2i', 'marqueur_export_apogee', 'export_apogee_', 'export_apogee_', 'indicateur pour differentier les imports des exports', 1, 'required', 1),
(92, 'pfc2i', 'boutons_retour_fermer_haut', '1', '1', 'mettre des boutons de fermeture et de retour aussi en haut des popups', 1, 'required validate-digits', 1),
(93, 'pfc2i', 'telechargement_pf', '1', '1', '', 1, 'required validate-digits', 1),
(94, 'pfc2i', 'telechargement_bdd', '1', '1', '', 1, 'required validate-digits', 1),
(95, 'dev', 'debug_traduction', '0', '0', 'mettre entre crochets les chaines non traduites auto', 1, 'required', 1),
(96, 'parcours', 'parcours_auto_positionnement', '0', '0', 'créer automatiquement un parcours en fin de positionnement', 1, 'required', 1),
(97, 'pool', 'pool_marqueur', ' G%s', ' G%s', '', 1, 'required', 1),
(98, 'pfc2i', 'session_nom', 'c2iv15', 'c2iv20', '', 1, 'required', 1),
(99, 'pool', 'pool_en_positionnement', '1', '1', 'v 1.5 ok par défaut', 1, 'required', 1),
(100, 'pfc2i', 'admin_mail', '', '', 'si vide mettra noreplyaddress dans le champ répondre à des mails', 1, 'required', 1),
(101, 'examen', 'pas_plus_une_question_par_famille', '0', '0', 'respect strict ou non de cette régle en selection manuelle de questions ', 1, 'required', 1),
(102, 'pool', 'peut_dupliquer_pool_fils', '1', '1', 'normalement oui c''est ainsi que l''on augmente le nombre de groupes d''un pool après coup', 1, 'required', 1),
(103, 'pool', 'peut_supprimer_pool_fils', '1', '1', 'normalement oui c''est ainsi que l''on reduit le nombre de groupes d''un pool après coup', 1, 'required', 1),
(104, 'pfc2i', 'envoi_resultat', '0', '0', 'envoyer les résultats de positionnement par mail', 1, 'required', 1),
(105, 'pfc2i', 'examen_anonyme', '1', '1', 'autoriser ou non l''examen anonyme en positionnement', 1, 'required', 1),
(106, 'pfc2i', 'afficher_lien_mail_liste_qcm', '0', '0', 'mettre un lien mailto sur le nom de l''auteur d''un qcm dans la vue candidat', 1, 'required', 1),
(107, 'pfc2i', 'afficher_lien_mail_liste_questions', '0', '0', 'mettre un lien mailto sur le nom de l''auteur d''une question dans les listes', 1, 'required', 1),
(108, 'pfc2i', 'afficher_lien_mail_liste_examens', '0', '0', 'mettre un lien mailto sur le nom de l''auteur d''un examen dans les listes', 1, 'required', 1),
(109, 'pfc2i', 'multi_max_pages', '10', '10', 'multipagination nombre maxi de numéros de page à cliquer', 1, 'required', 1),
(110, 'pfc2i', 'multi_parpage', '1', '1', 'multipagination ajouter option afficher par page', 1, 'required', 1),
(111, 'pfc2i', 'generer_xml_qti', '0', '0', 'arret utilisation de ce format à partir v1.5', 0, 'required', 1),
(112, 'pfc2i', 'peut_proposer_nouvelle_famille', '0', '0', 'option retirée v 1.5 (trop de familles à valider ensuite)', 0, 'required', 1),
(113, 'utilisateur', 'utiliser_mkjoli', '1', '1', 'essayer de normaliser l''affichage des noms/prénoms (1ere lettre en majuscule, attention aux tirets)', 1, 'required', 1),
(114, 'pfc2i', 'cmd_zip', '/usr/bin/zip', '/usr/bin/zip', '', 1, 'required', 1),
(115, 'nationale', 'nombre_questions_a_envoyer_pos', '-1', '-1', '', 1, 'required', 1),
(116, 'nationale', 'nombre_questions_a_envoyer_cert', '-1', '-1', '', 1, 'required', 1),
(117, 'pfc2i', 'utiliser_inlinemod_js', '0', '0', 'drapeau interne pour javascript supplementaire', 0, 'required', 1),
(118, 'ldap', 'nbre_reponses_ldap', '100', '100', 'nombre de réponses en recherche LDAP', 1, 'required', 1),
(119, 'ldap', 'champs_synchro_ldap', 'login,nom,prenom,mail,numetudiant', 'login,nom,prenom,mail,numetudiant', 'champs BD synchronisables en LDAP', 1, 'required', 1),
(120, 'pfc2i', 'smtphosts', 'smtp.insa-lyon.fr', '', 'adresse hote SMTP ((si vide on passe par la fonction mail de php)', 1, 'saisie', 1),
(121, 'pfc2i', 'smtpuser', '', '', 'login éventuel sur l''hote SMTP', 1, 'required', 1),
(122, 'pfc2i', 'smtppass', '', '', 'mot de passe éventuel sur l''hote SMTP', 1, 'required', 1),
(123, 'pfc2i', 'noreplyaddress', 'nepasrepondre@education.gouv.fr', 'nepasrepondre@education.gouv.fr', '', 1, 'required', 1),
(124, 'pfc2i', 'smtp_debugging', '1', '0', '', 1, 'required', 1),
(125, 'pfc2i', 'date_mise_a_jour', '1244394738', '1244394738', '', 0, 'required', 1),
(126, 'parcours', 'creer_parcours_html', '1', '1', 'créer un parcours simple en HTML (non enregistré en BD) que le candidat peut sauver', 0, 'required', 1),
(127, 'pfc2i', 'masquer_infobulles', '0', '0', 'ne pas afficher les bulles d''aide infobulles', 0, 'required', 1),
(128, 'pfc2i', 'pas_de_motdepasse_oublie', '0', '0', 'ne pas proposer le lien j''ai oublié mon mot de passe', 1, 'required', 1),
(129, 'pfc2i', 'proxy_host', '', '', '', 1, 'required', 1),
(130, 'pfc2i', 'proxy_port', '', '', '', 1, 'required', 1),
(131, 'pfc2i', 'proxy_login', '', '', '', 1, 'required', 1),
(132, 'pfc2i', 'proxy_password', '', '', '', 1, 'required', 1),
(133, 'pfc2i', 'utiliser_curl', '0', '0', 'utiliser curl au lieu de fopen vers la nationale', 1, 'required', 1),
(134, 'pfc2i', 'pas_de_scores_negatifs', '1', '1', 'circulaire 2011', 1, 'required', 1),
(135, 'pfc2i', 'afficher_score_question', '0', '0', 'affiche le score obtneu dans les corrigés par question', 1, 'required', 1),
(136, 'pfc2i', 'recalculer_score_question', '0', '0', 'debug algo de calcul du score : afficher les détails', 1, 'required', 1),
(137, 'pfc2i', 'calcul_indice_discrimination', '1', '1', 'statistiques avancées sur les questions', 1, 'required', 1),
(138, 'pfc2i', 'remonter_validees_seulement', '1', '1', 'ne remonter à la nationale que les questions localement validées', 1, 'required', 1),
(139, 'webservice', 'ws_sessiontimeout', '1800', '1800', 'durée de timeout d''une session', 1, 'required', 1),
(140, 'webservice', 'ws_logoperations', '1', '1', 'une ligne dans le tracking a chaque accès', 1, 'required', 1),
(141, 'webservice', 'ws_logerrors', '1', '1', 'une ligne dans le tracking a chaque erreur', 1, 'required', 1),
(142, 'webservice', 'ws_logdetailedoperations', '1', '1', 'non utilisé', 1, 'required', 1),
(143, 'webservice', 'ws_enforceipcheck', '0', '0', 'controler les IP d''après la table c2iwebservices_clients_allow', 1, 'required', 1),
(144, 'webservice', 'ws_disable', '0', '0', 'refuse toute demande de connexion', 1, 'required', 1),
(145, 'examen', 'seulement_validee_en_positionnement', '0', '0', 'ne retenir que des questions validées en positionnement (toujours sur la nationale)', 1, 'required', 1),
(146, 'examen', 'autoriser_qcm_par_domaine_en_positionnement', '1', '1', '', 1, 'required', 1),
(147, 'examen', 'pas_de_timer', '0', '0', 'afficher ou non le temps restant dans un QCM', 1, 'required', 1),
(148, 'examen', 'debut_timer', '3600', '3600', 'montrer le chronométre si il reste moins de xx secondes', 1, 'required', 1),
(149, 'examen', 'onglet_admin_examen', '1', '0', 'ajouter un onglet admin aux examens (expérimental)', 1, 'required', 1),
(150, 'question', 'onglet_admin_question', '0', '0', 'ajouter un onglet admin aux questions (expérimental)', 1, 'required', 1),
(151, 'moodle', 'qtype_moodle', 'multichoice', 'multichoice', ' type de question à exporter en HTML Moodle (expérimental)', 1, 'required', 1),
(152, 'resultats', 'export_ods', '1', '1', ' export au format OpenOffice document ODS  (expérimental)', 1, 'required', 1),
(153, 'question', 'utiliser_editeur_html', '0', '0', ' éditeur HTMl en saisie de questions (expérimental)', 1, 'required', 1),
(154, 'pfc2i', 'pclzip_trace', '0', '0', 'créer un fichier de trace de pclzip dans /web/c2isrv.univ-rennes1.fr/https/plate-forme/ressources/tmp', 1, 'required', 1),
(157, 'pfc2i', 'W3C_strict', '1', '1', 'compatibilité W3C experimental', 1, 'required', 1),
(158, 'examen', 'numerotation_reponses', '1', '1', 'numérotation des réponses 1=1,2,3... 2=A,B,C...', 1, 'required', 1),
(159, 'examen', 'export_AMC', '1', '0', 'exportation au format auto multiple choice', 1, 'required', 1),
(160, 'question', 'force_synchro_questions_utilisees', '0', '0', 'ecraser questions même si utilisées dans des examens', 1, 'required', 1),
(161, 'pfc2i', 'c2i', 'c2i1', 'c2i1', '', 0, 'required', 1),
(162, 'ldap', 'chercher_groupes_ldap', '1', '1', 'proposer la liste des groupes LDAP trouvés', 1, 'required', 1),
(163, 'ldap', 'debug_ldap_groupes', '0', '0', 'passer en mode debug l''inscription des groupes LDAP trouvés', 1, 'required', 1),
(164, 'AMC', 'AMC_multicol', '0', '0', 'export AMC mettre les réponses sous plusieurs colonnes 0=non 2=deux colonnes...', 1, 'required', 1),
(165, 'AMC', 'AMC_extra_copies', '5', '5', 'export AMC nombre de copies supplémentaires en % ', 1, 'required', 1),
(166, 'AMC', 'AMC_inclure_images', '1', '1', 'export AMC émettre les images eventuellement associées aux questions 0=non 1=oui', 1, 'required', 1),
(167, 'AMC', 'AMC_taille_numetudiant', '8', '8', 'export AMC nombre de chiffres du numéro d''étudiant', 1, 'required', 1),
(168, 'AMC', 'AMC_nom_colonne_numetudiant', 'numetudiant', 'numetudiant', 'export AMC nom de la colonne a rechercher dans le CSV en retour ', 1, 'required', 1),
(169, 'examen', 'autoriser_export', '1', '1', 'autoriser exportation d''examen', 1, 'required', 1),
(170, 'examen', 'autoriser_import', '1', '1', 'autoriser import d''examen (non sur la nationale)', 1, 'required', 1),
(171, 'examen', 'afficher_temps_passage', '0', '0', 'afficher aussi la durée du passage du candidat', 1, 'required', 1),
(172, 'AMC', 'AMC_recto_verso', '0', '0', 'impression recto-verso des sujets', 1, 'required', 1),
(173, 'pfc2i', 'W3C_validateurs', '1', '1', 'afficher les liens vers les validateurs dans le pied de page', 1, 'required', 1),
(174, 'pfc2i', 'boutons_graphiques', '1', '1', 'boutons de type images ou non ', 1, 'required', 1),
(177, 'AMC', 'AMC_verif_score', '1', '1', 'vérification des calculs effectués par AMC', 1, 'required', 1),
(181, 'pfc2i', 'anonyme_controle_adresse_mail', '0', '0', '0 : adresse facultative  1 : adresse valide obligatoire\n                                                            2: adresse obligatoire ET connue de la plate-forme\n                                                            3 : adresse obligatoire ET connue de votre LDAP', 1, 'required', 1),
(182, 'examen', 'limite_temps_passage', '0', '0', 'Si vous spécifier une valeur, elle sera utilisée comme durée de passage par défaut d''un examen', 1, 'required', 1),
(183, 'examen', 'montrer_stats_par_famille_cert', '1', '1', 'Dans la fiche d''un examen de certification, montrer aussi les stats par famille utilisées', 1, 'required', 1),
(184, 'examen', 'montrer_stats_par_famille_pos', '1', '1', 'Dans la fiche d''un examen de positionnement, montrer aussi les stats par famille utilisées', 1, 'required', 1),
(185, 'question', 'activer_filtre_latex', '0', '0', '', 1, 'required', 1),
(186, 'tex', 'filter_tex_latexpreamble', 'usepackage[latin1]{inputenc}\nusepackage{amsmath}\nusepackage{amsfonts}\nRequirePackage{amsmath,amssymb,latexsym}\n', 'usepackage[latin1]{inputenc}\nusepackage{amsmath}\nusepackage{amsfonts}\nRequirePackage{amsmath,amssymb,latexsym}\n', 'Entete fichier Latex', 0, 'required', 1),
(187, 'tex', 'filter_tex_latexbackground', '#FFFFFF', '#FFFFFF', 'Couleur de fond transparent des images générées', 1, 'required', 1),
(188, 'tex', 'filter_tex_density', '120', '120', '', 1, 'required', 1),
(189, 'tex', 'filter_tex_pathlatex', '/usr/bin/latex', '/usr/bin/latex', '', 1, 'required', 1),
(190, 'tex', 'filter_tex_pathdvips', '/usr/bin/dvips', '/usr/bin/dvips', '', 1, 'required', 1),
(191, 'tex', 'filter_tex_pathconvert', '/usr/bin/convert', '/usr/bin/convert', '', 1, 'required', 1),
(192, 'tex', 'filter_tex_convertformat', 'png', 'png', '', 1, 'required', 1),
(193, 'tex', 'filter_tex_utiliser_flatten_pour_gif', '0', '0', '', 1, 'required', 1),
(194, 'examen', 'algo_tirage', '3', '3', 'algorithme de tirage des questions  : 1 = équilibre par domaine 2  =  équilibre par compétence 3 = équilibre par thème', 1, 'required', 1),
(195, 'examen', 'activer_tags_examen', '0', '0', '', 1, 'required', 1),
(196, 'question', 'activer_tags_question', '0', '0', '', 1, 'required', 1),
(197, 'candidat', 'activer_tags_candidat', '0', '0', '', 1, 'required', 1),
(198, 'utilisateur', 'activer_tags_utilisateur', '0', '0', '', 1, 'required', 1),
(199, 'famille', 'activer_tags_famille', '0', '0', '', 1, 'required', 1),
(200, 'notion', 'activer_tags_notion', '0', '0', '', 1, 'required', 1),
(201, 'parcours', 'activer_tags_parcours', '0', '0', '', 1, 'required', 1),
(202, 'examen', 'ne_pas_afficher_score_global', '0', '0', 'si 1 on n''affiche pas le score global au candidat en positionnement', 1, 'required', 1),
(203, 'examen', 'autoriser_qcm_par_domaine_en_certification', '1', '1', 'circulaire 14/07/2011', 1, 'required', 1),
(204, 'dev', 'debug_events', '0', '0', 'trace du traitement des évenements dans ressources/events.log', 1, 'required', 1),
(205, 'pfc2i', 'restrictions_ip', '1', '1', 'activer les restrictions ip pour les QCMs', 1, 'required', 1),
(206, 'question', 'adresse_feedback_questions', 'qcm-c2i1@education.gouv.fr', 'qcm-c2i1@education.gouv.fr', 'adresse des experts validateurs des questions', 0, 'required', 1),
(207, 'pfc2i', 'adresse_serveur_public_c2i', 'https://c2i.education.fr/c2iws/service.php', 'https://c2i.education.fr/c2iws/service.php', '', 0, 'required', 1),
(209, 'pfc2i', 'date_installation', '1359387486', '1359387486', '', 127, 'required', 1),
(208, 'examen', 'seulement_mes_examens', '0', '0', 'n''afficher à un enseignant que les examens qu''il a généré', 1, 'required', 1),
(210, 'question', 'seulement_validees_liste', '1', '1', 'n''afficher que les validées dans les listes, sauf aux experts validateurs', 1, 'required', 1);

-- --------------------------------------------------------

--
-- Structure de la table `c2idroits`
--

CREATE TABLE IF NOT EXISTS `c2idroits` (
  `login` varchar(100) NOT NULL DEFAULT '',
  `id_profil` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login`,`id_profil`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



--
-- Structure de la table `c2ietablissement`
--

CREATE TABLE IF NOT EXISTS `c2ietablissement` (
  `id_etab` int(11) NOT NULL AUTO_INCREMENT,
  `nom_etab` varchar(255) NOT NULL DEFAULT '',
  `pere` int(11) NOT NULL DEFAULT '1',
  `positionnement` tinyint(1) NOT NULL DEFAULT '0',
  `certification` tinyint(1) NOT NULL DEFAULT '0',
  `locale` tinyint(1) NOT NULL DEFAULT '0',
  `nationale` tinyint(1) NOT NULL DEFAULT '0',
  `nb_telechargements` int(11) NOT NULL DEFAULT '0',
  `param_nb_items` int(11) NOT NULL DEFAULT '50',
  `param_nb_aleatoire` int(11) NOT NULL DEFAULT '60',
  `param_nb_experts` int(11) NOT NULL DEFAULT '3',
  `param_nb_qac` int(11) NOT NULL DEFAULT '50',
  `param_ldap` varchar(255) DEFAULT NULL,
  `base_ldap` varchar(255) DEFAULT NULL,
  `rdn_ldap` varchar(255) DEFAULT NULL,
  `passe_ldap` varchar(255) DEFAULT NULL,
  `param_langue` varchar(20) NOT NULL DEFAULT 'fr',
  `nb_quest_recup` int(11) NOT NULL DEFAULT '50',
  `ldap_group_class` varchar(255) NOT NULL DEFAULT 'groupOfNames',
  `ldap_group_attribute` varchar(255) NOT NULL DEFAULT 'member',
  `ldap_id_attribute` varchar(255) NOT NULL DEFAULT 'supanncodeine',
  `url` varchar(255) NOT NULL DEFAULT '../../ressources/notions',
  `ldap_login_attribute` varchar(255) DEFAULT 'uid',
  `ldap_mail_attribute` varchar(255) DEFAULT 'mail',
  `ldap_nom_attribute` varchar(255) DEFAULT 'sn',
  `ldap_prenom_attribute` varchar(255) DEFAULT 'givenName',
  `ldap_version` varchar(10) DEFAULT '2',
  `ldap_user_type` varchar(255) DEFAULT 'rfc2307bis',
  PRIMARY KEY (`id_etab`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=0 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2ietablissement`
--

INSERT INTO `c2ietablissement` (`id_etab`, `nom_etab`, `pere`, `positionnement`, `certification`, `locale`, `nationale`, `nb_telechargements`, `param_nb_items`, `param_nb_aleatoire`, `param_nb_experts`, `param_nb_qac`, `param_ldap`, `base_ldap`, `rdn_ldap`, `passe_ldap`, `param_langue`, `nb_quest_recup`, `ldap_group_class`, `ldap_group_attribute`, `ldap_id_attribute`, `url`, `ldap_login_attribute`, `ldap_mail_attribute`, `ldap_nom_attribute`, `ldap_prenom_attribute`, `ldap_version`, `ldap_user_type`) VALUES
(1, 'Ministère de l''éducation nationale', -1, 0, 0, 0, 0, 35, 100, 45, 3, 45, 'ou=fr', '', '', '', 'fr', 50, '', '', '', '', 'uid', 'mail', 'sn', 'givenName', '2', 'rfc2307bis');

-- --------------------------------------------------------

--
-- Structure de la table `c2ievents`
--

CREATE TABLE IF NOT EXISTS `c2ievents` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `objet` varchar(50) NOT NULL COMMENT 'type d''objet concerné',
  `action` varchar(50) NOT NULL COMMENT 'type d''action',
  `script` varchar(255) NOT NULL COMMENT 'script ou est le gestionnaire',
  `fonction` varchar(50) NOT NULL COMMENT 'nom de la fonction',
  `cron` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'immediat ou deferé',
  `actif` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'activé ou non',
  `commentaire` varchar(255) NOT NULL DEFAULT '' COMMENT 'decsiption détaillée',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='gestionnaires d''evenements' AUTO_INCREMENT=51 ;

--
-- Contenu de la table `c2ievents`
--

INSERT INTO `c2ievents` (`id`, `objet`, `action`, `script`, `fonction`, `cron`, `actif`, `commentaire`) VALUES
(1, 'question', 'ajout', 'commun/lib_questions.php', 'evt_question_ajout', 0, 1, ''),
(2, 'question', 'modification', 'commun/lib_questions.php', 'evt_question_modification', 0, 1, ''),
(3, 'question', 'suppression', 'commun/lib_questions.php', 'evt_question_suppression', 0, 1, ''),
(4, 'question', 'validation', 'commun/lib_questions.php', 'evt_question_validation', 0, 1, ''),
(5, 'question', 'invalidation', 'commun/lib_questions.php', 'evt_question_invalidation', 0, 1, ''),
(6, 'question', 'envoi', 'commun/lib_questions.php', 'evt_question_envoi', 0, 1, 'reception de questions sur une nationale'),
(7, 'question', 'filtrage', 'commun/lib_questions.php', 'evt_question_filtrage', 0, 1, ''),
(8, 'question', 'defiltrage', 'commun/lib_questions.php', 'evt_question_defiltrage', 0, 1, ''),
(9, 'question', 'duplication', 'commun/lib_questions.php', 'evt_question_duplication', 0, 1, ''),
(10, 'examen', 'ajout', 'commun/lib_examens.php', 'evt_examen_ajout', 0, 1, ''),
(11, 'examen', 'modification', 'commun/lib_examens.php', 'evt_examen_modification', 0, 1, ''),
(12, 'examen', 'suppression', 'commun/lib_examens.php', 'evt_examen_suppression', 0, 1, ''),
(13, 'examen', 'envoi', 'commun/lib_examens.php', 'evt_examen_envoi', 0, 1, 'reception de stats examen sur une nationale'),
(14, 'utilisateur', 'ajout', 'commun/lib_acces.php', 'evt_utilisateur_ajout', 0, 1, ''),
(15, 'utilisateur', 'modification', 'commun/lib_acces.php', 'evt_utilisateur_modification', 0, 1, ''),
(16, 'utilisateur', 'suppression', 'commun/lib_acces.php', 'evt_utilisateur_suppression', 0, 1, ''),
(17, 'etudiant', 'ajout', 'commun/lib_acces.php', 'evt_etudiant_ajout', 0, 1, ''),
(18, 'etudiant', 'modification', 'commun/lib_acces.php', 'evt_etudiant_modification', 0, 1, ''),
(19, 'etudiant', 'suppression', 'commun/lib_acces.php', 'evt_etudiant_suppression', 0, 1, ''),
(20, 'notion', 'ajout', 'commun/lib_notions.php', 'evt_notion_ajout', 0, 1, ''),
(21, 'notion', 'modification', 'commun/lib_notions.php', 'evt_notion_modification', 0, 1, ''),
(22, 'notion', 'suppression', 'commun/lib_notions.php', 'evt_notion_suppression', 0, 1, ''),
(23, 'ressource', 'ajout', 'commun/lib_ressources.php', 'evt_ressource_ajout', 0, 1, ''),
(24, 'ressource', 'modification', 'commun/lib_ressources.php', 'evt_ressource_modification', 0, 1, ''),
(25, 'ressource', 'suppression', 'commun/lib_ressources.php', 'evt_ressource_suppression', 0, 1, ''),
(26, 'profil', 'ajout', 'commun/lib_acces.php', 'evt_profil_ajout', 0, 1, ''),
(27, 'profil', 'modification', 'commun/lib_acces.php', 'evt_profil_modification', 0, 1, ''),
(28, 'profil', 'suppression', 'commun/lib_acces.php', 'evt_profil_suppression', 0, 1, ''),
(29, 'famille', 'ajout', 'commun/lib_questions.php', 'evt_famille_ajout', 0, 1, ''),
(30, 'famille', 'modification', 'commun/lib_questions.php', 'evt_famille_modification', 0, 1, ''),
(31, 'famille', 'suppression', 'commun/lib_questions.php', 'evt_famille_suppression', 0, 1, ''),
(32, 'qcm', 'passage', 'commun/lib_examens.php', 'evt_qcm_passage', 0, 1, ''),
(33, 'etablissement', 'ajout', 'commun/lib_etablissements.php', 'evt_etablissement_ajout', 0, 1, ''),
(34, 'etablissement', 'suppression', 'commun/lib_etablissements.php', 'evt_etablissement_suppression', 0, 1, ''),
(35, 'etablissement', 'modification', 'commun/lib_etablissements.php', 'evt_etablissement_modification', 0, 1, ''),
(36, 'examen', 'verouillage', 'codes/locale/local_events.php', 'local_evt_examen_verouillage', 0, 1, 'exemple d''un evenement local'),
(37, 'examen', 'deverouillage', 'codes/locale/local_events.php', 'local_evt_examen_deverouillage', 0, 1, 'exemple d''un evenement local'),
(38, 'examen', 'verouillage', 'commun/lib_examens.php', 'evt_examen_verouillage', 0, 1, ''),
(39, 'examen', 'deverouillage', 'commun/lib_examens.php', 'evt_examen_deverouillage', 0, 1, ''),
(40, 'question', 'importation', 'commun/lib_questions.php', 'evt_question_importation', 0, 1, ''),
(41, 'question', 'feedback', 'commun/lib_questions.php', 'evt_question_feedback', 0, 1, ''),
(42, 'plage', 'ajout', 'commun/lib_auth.php', 'evt_plage_ajout', 0, 1, ''),
(43, 'plage', 'suppression', 'commun/lib_auth.php', 'evt_plage_suppression', 0, 1, ''),
(44, 'plage', 'modification', 'commun/lib_auth.php', 'evt_plage_modification', 0, 1, ''),
(45, 'utilisateur', 'connexion', 'commun/lib_acces.php', 'evt_utilisateur_connexion', 0, 1, ''),
(46, 'etudiant', 'connexion', 'commun/lib_acces.php', 'evt_etudiant_connexion', 0, 1, ''),
(47, 'utilisateur', 'deconnexion', 'commun/lib_acces.php', 'evt_utilisateur_deconnexion', 0, 1, ''),
(48, 'etudiant', 'deconnexion', 'commun/lib_acces.php', 'evt_etudiant_deconnexion', 0, 1, ''),
(49, 'examen', 'inscription', 'commun/lib_examens.php', 'evt_examen_inscription', 0, 1, ''),
(50, 'examen', 'desinscription', 'commun/lib_examens.php', 'evt_examen_desinscription', 0, 1, '');

-- --------------------------------------------------------

--
-- Structure de la table `c2iexamens`
--

CREATE TABLE IF NOT EXISTS `c2iexamens` (
  `id_examen` int(11) NOT NULL AUTO_INCREMENT,
  `id_etab` int(11) NOT NULL DEFAULT '-1',
  `nom_examen` varchar(255) NOT NULL DEFAULT '',
  `auteur` varchar(255) DEFAULT NULL,
  `positionnement` enum('OUI','NON') NOT NULL DEFAULT 'NON',
  `certification` enum('OUI','NON') NOT NULL DEFAULT 'NON',
  `referentielc2i` varchar(255) DEFAULT '-1',
  `langue` varchar(20) DEFAULT NULL,
  `auteur_mail` varchar(255) DEFAULT NULL,
  `type_tirage` enum('manuel','aleatoire','passage','pool') NOT NULL DEFAULT 'manuel',
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `ordre_q` enum('aleatoire','fixe') NOT NULL DEFAULT 'aleatoire',
  `ordre_r` enum('aleatoire','fixe') NOT NULL DEFAULT 'aleatoire',
  `correction` tinyint(1) NOT NULL DEFAULT '0',
  `est_pool` tinyint(4) NOT NULL DEFAULT '0',
  `nb_q_pool` int(11) NOT NULL DEFAULT '0',
  `pool_pere` int(11) NOT NULL DEFAULT '0',
  `pool_nb_groupes` int(11) NOT NULL DEFAULT '0',
  `resultat_mini` int(11) NOT NULL DEFAULT '50',
  `ts_datecreation` int(10) DEFAULT '0',
  `ts_datemodification` int(10) DEFAULT '0',
  `ts_datedebut` int(10) DEFAULT '0',
  `ts_datefin` int(10) DEFAULT '0',
  `template_resultat` longtext NOT NULL,
  `anonyme` tinyint(1) DEFAULT '0',
  `frequence` tinyint(1) NOT NULL DEFAULT '0',
  `envoi_resultat` tinyint(1) NOT NULL DEFAULT '0',
  `ts_dateenvoi` int(10) DEFAULT '0',
  `nbquestions` int(10) DEFAULT '0',
  `affiche_chrono` tinyint(1) NOT NULL DEFAULT '0',
  `ts_dureelimitepassage` int(10) DEFAULT '0',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  `verouille` int(1) DEFAULT '0' COMMENT 'examen verouillé par SGA',
  `subnet` text NOT NULL COMMENT 'restriction reseau ajoutée revision 986',
  PRIMARY KEY (`id_examen`,`id_etab`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



--
-- Structure de la table `c2iextelec`
--

CREATE TABLE IF NOT EXISTS `c2iextelec` (
  `num_telec` int(11) NOT NULL DEFAULT '0',
  `type` enum('c','p') NOT NULL DEFAULT 'c',
  `id_examen` int(11) NOT NULL DEFAULT '0',
  `id_etab` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`num_telec`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `c2iextelec`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2ifamilles`
--

CREATE TABLE IF NOT EXISTS `c2ifamilles` (
  `idf` int(11) NOT NULL AUTO_INCREMENT,
  `famille` varchar(255) NOT NULL,
  `referentielc2i` varchar(5) NOT NULL,
  `alinea` tinyint(2) NOT NULL,
  `ordref` int(11) NOT NULL DEFAULT '1',
  `mots_clesf` varchar(255) DEFAULT NULL,
  `mots_cles_manquants` varchar(255) DEFAULT NULL,
  `commentaires` text,
  `ts_datecreation` int(10) DEFAULT '0',
  `auteur` varchar(255) NOT NULL DEFAULT '',
  `auteur_mail` varchar(255) NOT NULL DEFAULT '',
  `ts_dateutilisation` int(10) DEFAULT '0',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  PRIMARY KEY (`idf`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Structure de la table `c2ifeedbackexamen`
--

CREATE TABLE IF NOT EXISTS `c2ifeedbackexamen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_examen` int(11) NOT NULL DEFAULT '0',
  `note_mini` int(3) NOT NULL DEFAULT '0',
  `note_maxi` int(3) NOT NULL DEFAULT '0',
  `feedback` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2ifeedbackexamen`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iinscrits`
--

CREATE TABLE IF NOT EXISTS `c2iinscrits` (
  `login` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(250) NOT NULL DEFAULT '',
  `nom` varchar(250) DEFAULT NULL,
  `prenom` varchar(250) DEFAULT NULL,
  `genre` varchar(10) DEFAULT NULL,
  `numetudiant` varchar(250) DEFAULT NULL,
  `etablissement` int(11) DEFAULT '-1',
  `email` varchar(255) DEFAULT NULL,
  `ts_connexion` int(10) DEFAULT '0',
  `ts_derniere_connexion` int(10) DEFAULT '0',
  `auth` varchar(15) NOT NULL DEFAULT 'manuel',
  `ts_datecreation` int(10) NOT NULL DEFAULT '0',
  `ts_datemodification` int(10) NOT NULL DEFAULT '0',
  `origine` varchar(25) NOT NULL DEFAULT '',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



--
-- Structure de la table `c2ildap`
--

CREATE TABLE IF NOT EXISTS `c2ildap` (
  `id_etab` int(11) NOT NULL DEFAULT '0',
  `champ_LDAP` varchar(100) NOT NULL DEFAULT '',
  `nom_champ` varchar(100) NOT NULL DEFAULT '',
  `valeur` varchar(200) NOT NULL DEFAULT '',
  `modifiable` enum('OUI','NON') NOT NULL DEFAULT 'OUI',
  `ordre` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_etab`,`champ_LDAP`,`nom_champ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `c2ildap`
--

INSERT INTO `c2ildap` (`id_etab`, `champ_LDAP`, `nom_champ`, `valeur`, `modifiable`, `ordre`) VALUES
(1, 'sn', 'nom', '', 'OUI', 0),
(1, 'givenName', 'prenom', '', 'OUI', 1),
(1, 'mail', 'email', '', 'OUI', 2),
(1, 'supanncodeine', 'numetudiant', '', 'OUI', 3);

-- --------------------------------------------------------

--
-- Structure de la table `c2iliens`
--

CREATE TABLE IF NOT EXISTS `c2iliens` (
  `id_lien` int(11) NOT NULL AUTO_INCREMENT,
  `id_notion` int(11) NOT NULL DEFAULT '0',
  `origine` varchar(255) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `actif` enum('oui','non') DEFAULT 'oui',
  PRIMARY KEY (`id_lien`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2iliens`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iparcours`
--

CREATE TABLE IF NOT EXISTS `c2iparcours` (
  `id_parcours` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('creation','examen','croisement creation/examen') NOT NULL DEFAULT 'création',
  `login` varchar(100) DEFAULT NULL,
  `ts_datecreation` int(10) DEFAULT '0',
  `ts_datemodification` int(10) DEFAULT '0',
  `examen` varchar(20) NOT NULL DEFAULT '',
  `titre` varchar(100) NOT NULL DEFAULT '',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  PRIMARY KEY (`id_parcours`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2iparcours`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iplagesip`
--

CREATE TABLE IF NOT EXISTS `c2iplagesip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(120) NOT NULL DEFAULT '',
  `adresses` varchar(120) NOT NULL,
  `id_etab` int(11) NOT NULL,
  `ts_datecreation` int(10) NOT NULL DEFAULT '0',
  `ts_datemodification` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='plages nommées d''adresses IP autorisées par établissement' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2iplagesip`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2ipreferences`
--

CREATE TABLE IF NOT EXISTS `c2ipreferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL DEFAULT '',
  `categorie` varchar(50) NOT NULL DEFAULT '',
  `cle` varchar(50) NOT NULL DEFAULT '',
  `valeur` text NOT NULL,
  `defaut` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`,`login`,`categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Variables de preferences' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2ipreferences`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iprofils`
--

CREATE TABLE IF NOT EXISTS `c2iprofils` (
  `id_profil` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) NOT NULL DEFAULT '',
  `q_ajouter` tinyint(1) NOT NULL DEFAULT '0',
  `q_modifier` tinyint(1) NOT NULL DEFAULT '0',
  `q_dupliquer` tinyint(1) NOT NULL DEFAULT '0',
  `q_lister` tinyint(1) NOT NULL DEFAULT '0',
  `q_supprimer` tinyint(1) NOT NULL DEFAULT '0',
  `ex_ajouter` tinyint(1) NOT NULL DEFAULT '0',
  `ex_modifier` tinyint(1) NOT NULL DEFAULT '0',
  `ex_dupliquer` tinyint(1) NOT NULL DEFAULT '0',
  `ex_lister` tinyint(1) NOT NULL DEFAULT '0',
  `ex_supprimer` tinyint(1) NOT NULL DEFAULT '0',
  `q_valider` tinyint(1) NOT NULL DEFAULT '0',
  `acces_tracking` tinyint(1) NOT NULL DEFAULT '0',
  `etudiant_ajouter` tinyint(1) NOT NULL DEFAULT '0',
  `etudiant_modifier` tinyint(1) NOT NULL DEFAULT '0',
  `etudiant_lister` tinyint(1) NOT NULL DEFAULT '0',
  `etudiant_supprimer` tinyint(1) NOT NULL DEFAULT '0',
  `resultats_afficher` tinyint(1) NOT NULL DEFAULT '0',
  `utilisateur_ajouter` tinyint(1) NOT NULL DEFAULT '0',
  `utilisateur_modifier` tinyint(1) NOT NULL DEFAULT '0',
  `utilisateur_lister` tinyint(1) NOT NULL DEFAULT '0',
  `utilisateur_supprimer` tinyint(1) NOT NULL DEFAULT '0',
  `plc_telecharger` tinyint(1) NOT NULL DEFAULT '0',
  `plp_telecharger` tinyint(1) NOT NULL DEFAULT '0',
  `banquedd_telecharger` tinyint(1) NOT NULL DEFAULT '0',
  `configurer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_profil`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `c2iprofils`
--

INSERT INTO `c2iprofils` (`id_profil`, `intitule`, `q_ajouter`, `q_modifier`, `q_dupliquer`, `q_lister`, `q_supprimer`, `ex_ajouter`, `ex_modifier`, `ex_dupliquer`, `ex_lister`, `ex_supprimer`, `q_valider`, `acces_tracking`, `etudiant_ajouter`, `etudiant_modifier`, `etudiant_lister`, `etudiant_supprimer`, `resultats_afficher`, `utilisateur_ajouter`, `utilisateur_modifier`, `utilisateur_lister`, `utilisateur_supprimer`, `plc_telecharger`, `plp_telecharger`, `banquedd_telecharger`, `configurer`) VALUES
(1, 'administrateurs', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 'Responsables nationaux du C2i', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
(3, 'Experts du groupe de suivi', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 0),
(4, 'Experts validateurs', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 'Auteur', 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(6, 'Saisie', 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 'Positionnement', 1, 0, 1, 1, 0, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 1, 0, 0),
(8, 'Correspondant C2i', 1, 0, 1, 1, 0, 1, 1, 1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 0),
(9, 'ComposanteUniversité', 1, 0, 1, 1, 0, 1, 1, 1, 1, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);


-- --------------------------------------------------------

--
-- Structure de la table `c2iqcm`
--

CREATE TABLE IF NOT EXISTS `c2iqcm` (
  `login` varchar(200) NOT NULL DEFAULT '',
  `id_examen` int(11) NOT NULL DEFAULT '0',
  `id_etab` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) DEFAULT NULL,
  `tags` text NOT NULL COMMENT 'ajouté revision 983',
  `date` int(10) DEFAULT '0',
  PRIMARY KEY (`login`,`id_examen`,`id_etab`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



--
-- Structure de la table `c2iquestions`
--

CREATE TABLE IF NOT EXISTS `c2iquestions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etab` int(11) NOT NULL DEFAULT '-1',
  `titre` text NOT NULL,
  `referentielc2i` varchar(255) NOT NULL DEFAULT '',
  `alinea` int(11) NOT NULL DEFAULT '0',
  `positionnement` enum('OUI','NON') NOT NULL DEFAULT 'NON',
  `certification` enum('OUI','NON') DEFAULT 'NON',
  `etat` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Version 2 état = -1, 0 ou 1',
  `langue` varchar(20) NOT NULL DEFAULT 'fr',
  `auteur` varchar(255) DEFAULT NULL,
  `auteur_mail` varchar(255) DEFAULT NULL,
  `famille_proposee` varchar(255) NOT NULL,
  `id_famille_proposee` int(11) NOT NULL DEFAULT '0',
  `id_famille_validee` int(11) NOT NULL DEFAULT '0',
  `ts_datecreation` int(10) DEFAULT '0',
  `ts_datemodification` int(10) DEFAULT '0',
  `ts_dateenvoi` int(10) DEFAULT '0',
  `ts_dateutilisation` int(10) DEFAULT '0',
  `generalfeedback` text NOT NULL,
  `correctfeedback` text NOT NULL,
  `incorrectfeedback` text NOT NULL,
  `partiallycorrectfeedback` text NOT NULL,
  `est_filtree` tinyint(1) NOT NULL DEFAULT '0',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  PRIMARY KEY (`id`,`id_etab`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Structure de la table `c2iquestionsdocuments`
--

CREATE TABLE IF NOT EXISTS `c2iquestionsdocuments` (
  `id` int(11) NOT NULL DEFAULT '0',
  `id_etab` int(11) NOT NULL DEFAULT '0',
  `id_doc` int(11) NOT NULL DEFAULT '0',
  `extension` varchar(5) NOT NULL DEFAULT '',
  `description` varchar(100) DEFAULT NULL COMMENT 'ajouté v 1.5',
  PRIMARY KEY (`id`,`id_etab`,`id_doc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='le nom du document est id_doc.extension';

--
-- Contenu de la table `c2iquestionsdocuments`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iquestionsexamen`
--

CREATE TABLE IF NOT EXISTS `c2iquestionsexamen` (
  `id_examen` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL DEFAULT '0',
  `id_examen_etab` int(11) NOT NULL DEFAULT '0',
  `id_etab` int(11) NOT NULL DEFAULT '0',
  `ts_dateselection` int(10) DEFAULT '0',
  PRIMARY KEY (`id`,`id_examen_etab`,`id_examen`,`id_etab`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Structure de la table `c2iquestionsvalidation`
--

CREATE TABLE IF NOT EXISTS `c2iquestionsvalidation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_etab` int(11) NOT NULL DEFAULT '0',
  `validation` enum('OUI','NON') NOT NULL DEFAULT 'NON',
  `remarques` longtext,
  `modifications` longtext,
  `login` varchar(100) NOT NULL DEFAULT '0',
  `ts_date` int(10) DEFAULT '0',
  PRIMARY KEY (`id`,`id_etab`,`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2iquestionsvalidation`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2ireferentiel`
--

CREATE TABLE IF NOT EXISTS `c2ireferentiel` (
  `referentielc2i` varchar(5) NOT NULL DEFAULT '',
  `domaine` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`referentielc2i`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='liste des domaines';


--
-- Structure de la table `c2ireponses`
--

CREATE TABLE IF NOT EXISTS `c2ireponses` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `id_etab` int(11) NOT NULL DEFAULT '-1',
  `reponse` text NOT NULL,
  `bonne` enum('OUI','NON') DEFAULT 'NON',
  `feedback` text NOT NULL,
  `commentaires` text NOT NULL,
  PRIMARY KEY (`num`,`id`,`id_etab`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
  `filtree` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `c2iressourcesparcours`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iresultats`
--

CREATE TABLE IF NOT EXISTS `c2iresultats` (
  `login` varchar(200) NOT NULL DEFAULT '',
  `question` varchar(50) NOT NULL DEFAULT '',
  `examen` varchar(50) NOT NULL DEFAULT '',
  `reponse` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) DEFAULT NULL,
  `ts_date` int(10) DEFAULT '0',
  `origine` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`login`,`question`,`examen`,`reponse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Structure de la table `c2iresultatscompetences`
--

CREATE TABLE IF NOT EXISTS `c2iresultatscompetences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `examen` varchar(20) NOT NULL,
  `competence` varchar(20) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `date` int(10) NOT NULL,
  `drapeau` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`examen`,`competence`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='mémorisation des scores par login examen competences (domain' AUTO_INCREMENT=1501 ;

--
-- Contenu de la table `c2iresultatscompetences`
--


--
-- Structure de la table `c2iresultatsdetailles`
--

CREATE TABLE IF NOT EXISTS `c2iresultatsdetailles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `examen` varchar(20) NOT NULL,
  `question` varchar(20) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `date` int(10) NOT NULL,
  `drapeau` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`examen`,`question`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='mémorisation des scores par login examen question ' AUTO_INCREMENT=1 ;


--
-- Structure de la table `c2iresultatsexamens`
--

CREATE TABLE IF NOT EXISTS `c2iresultatsexamens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `examen` varchar(10) NOT NULL,
  `date` int(10) NOT NULL,
  `ip_max` varchar(100) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `drapeau` tinyint(4) NOT NULL DEFAULT '0',
  `origine` varchar(30) NOT NULL DEFAULT '',
  `date_debut` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`examen`,`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='memorisation des scores globaux aux examens' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Structure de la table `c2iresultatsreferentiels`
--

CREATE TABLE IF NOT EXISTS `c2iresultatsreferentiels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `examen` varchar(20) NOT NULL,
  `referentielc2i` varchar(20) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `date` int(10) NOT NULL,
  `drapeau` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `login` (`login`,`examen`,`referentielc2i`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='mémorisation des scores par login examen domaines ' AUTO_INCREMENT=1 ;



--
-- Structure de la table `c2itracking`
--

CREATE TABLE IF NOT EXISTS `c2itracking` (
  `id_tracking` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL DEFAULT '',
  `login` varchar(100) NOT NULL DEFAULT '',
  `id_objet` varchar(255) NOT NULL DEFAULT '',
  `type_utilisateur` enum('E','P') NOT NULL DEFAULT 'P',
  `etat` enum('echec','succes') NOT NULL DEFAULT 'succes',
  `objet` varchar(255) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `date` int(10) DEFAULT NULL,
  `plateforme` varchar(20) NOT NULL DEFAULT '',
  `etablissement` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tracking`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Structure de la table `c2iutilisateurs`
--

CREATE TABLE IF NOT EXISTS `c2iutilisateurs` (
  `login` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(250) DEFAULT NULL,
  `nom` varchar(250) DEFAULT NULL,
  `prenom` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `est_superadmin` enum('O','N') DEFAULT 'N',
  `est_admin_univ` enum('O','N') NOT NULL DEFAULT 'N',
  `etablissement` int(11) NOT NULL DEFAULT '-1',
  `futur_mdp` varchar(255) DEFAULT NULL,
  `futur_verif` varchar(255) DEFAULT NULL,
  `limite_positionnement` tinyint(4) NOT NULL DEFAULT '0',
  `ts_connexion` int(10) DEFAULT '0',
  `ts_derniere_connexion` int(10) DEFAULT '0',
  `auth` varchar(15) NOT NULL DEFAULT 'manuel',
  `numetudiant` varchar(250) NOT NULL DEFAULT '',
  `ts_datecreation` int(10) NOT NULL DEFAULT '0',
  `ts_datemodification` int(10) NOT NULL DEFAULT '0',
  `origine` varchar(25) NOT NULL DEFAULT '',
  `tags` text NOT NULL COMMENT 'ajouté revision 982',
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Structure de la table `c2iwebservices_clients_allow`
--

CREATE TABLE IF NOT EXISTS `c2iwebservices_clients_allow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client` varchar(15) NOT NULL DEFAULT '0.0.0.0',
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Liste des IP autorisees' AUTO_INCREMENT=1 ;

--
-- Contenu de la table `c2iwebservices_clients_allow`
--


-- --------------------------------------------------------

--
-- Structure de la table `c2iwebservices_sessions`
--

CREATE TABLE IF NOT EXISTS `c2iwebservices_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sessionbegin` int(10) unsigned NOT NULL DEFAULT '0',
  `sessionend` int(10) unsigned NOT NULL DEFAULT '0',
  `sessionkey` varchar(32) NOT NULL DEFAULT '',
  `userid` varchar(64) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='donnees du Web service (sessions, logs...)' AUTO_INCREMENT=1 ;

