<?php
/**
 * @author Patrick Pollet
 * @version $Id: fr_utf8.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 *  fichier de gestion de la langue française
 *  zone de traduction automatique
 * les mots affichés sont dans la colonne de droite
 *  pour traduire ce fichier en d'autres langues
 * attention de respecter le nombre et l'ordre des %s dans certaines chaines
 * pour peremttre l'insertion de valeurs spécifiques par le code.
 * Dans les chaines nommées js_xxx (-texte javascript), veuillez à bien ajouter un '\'
 * devant les eventuelles apostrophes.
**/

$textes_langues = array (
// la partie à adapter selon la plateforme est située dans le fichier plateforme.php
// elle y est copiée par l'installateur à partir de la varibale CFG->c2i

//partie commune toutes plateformes
    "SDTICE"=>"Ministère",
    "c2i" => "Certificat Informatique et Internet",
    "logo_c2i" => "Logo C2i",
    "texte_choix_pf" => "Veuillez choisir la plate-forme : ",

    "domaine_A"=>"Aptitudes générales et transversales",
    "domaine_B"=>"Savoir-faire pratiques",
    "domaine_D"=>"Savoir-faire pratiques",


	"credits" => "Crédits",
	"pre_requis" => "Prérequis techniques",
	"contrat_usage" => "Contrat d'usage des QM et examens",
	"methodologie" => "méthodologie de rédaction des questions",
	"ministere" => "ministère",
    "obligatoire"=>"obligatoire",
    "optionnel"=>"optionnel",




	//plate forme
	"plc" => "plateforme de certification",
	"plp" => "plateforme de positionnement",
	"pla" => "plateforme de positionnement anonyme",
    "plpc" => "plateforme complète",
    "plmaj"=>"mise à jour de votre plateforme",

	"pl" => "plateforme locale",
	"pn" => "plateforme nationale",
	"certification" => "certification",
	"positionnement" => "positionnement",
	"anonyme" => "anonyme",
	"p_locale" => "locale",
	"p_nationale" => "nationale",
	"bdd_qcm" => "banque de données",
	"se_loguer_national" => "identification sur la plateforme nationale",
	"identifiant_national" => "votre identifiant sur la plateforme nationale :",
	"mot_de_passe_national" => "votre mot de passe sur la plateforme nationale :",
    "info_telecharger"=>"Cliquez sur le bouton pour récupérer l' archive ZIP de votre %s",
    "acces_cas"=>"Accès par connexion sécurisée",
    "info_acces_cas"=>"Si votre établissement utilise le protocole d'authentification unique CAS/SSO
                vous devriez utiliser ce bouton pour bénéficier d'une connexion sécurisée
               et peut-etre éviter de resaisir votre login/mot de passe.
               ",

	"ldap"=>"LDAP",
	"connexion"=>"connexion",
    "deconnexion"=>"déconnexion",

	//mails

	"mdp_oublie" => "j'ai oublié mon mot de passe",
	"mot_de_passe_oublie" => "génération d'un nouveau mot de passe",
	"mdp_oublie_ligne1" => "
		Ce service n'est pas valable pour les étudiants <br/>
		saisir votre identifiant ou votre adresse mél :",

	"mdp_oublie_ligne2" => "un courriel vous sera envoyé pour conna&icirc;tre la marche &agrave; suivre.",

	"mdp_active" => "Votre nouveau mot de passe est activé.",
	"activation_mot_de_passe" => "activation du nouveau mot de passe",
	"err_mdp_active" => "Un problème est survenu, veuillez contacter votre administrateur pour obtenir un nouveau mot de passe.",

	"ne_pas_repondre" => "nepasrepondre@education.gouv.fr",
	"compte_cree" => "un compte a été créé  pour vous",
	"err_msg_mail_envoye" => "erreur en envoyant un courriel à ",
	"msg_mail_envoye" => "un courriel a été envoyé à ",


	"info_mail_mdp_oublie" => "Un courriel vous expliquant la marche à suivre a été envoyé à l'adresse %s <br/>" .
	" En cas de problème contactez l'administrateur du site.",
	"sujet_mail_mdp_oublie" => "votre nouveau mot de passe",

	"err_pas_de_mail" => "aucune adresse mèl n'a été définie. Veuillez contacter l'administrateur du site pour obtenir un nouveau mot de passe.",

    //rev 978 parametrage messages d'erreur au login
    "err_pa"=>"utilisateur ou mot de passe incorrect",
    "err_mail_invalide"=>"adresse éléctronique mal formée",
    "err_mail_inconnu_pf"=>"adresse éléctronique inconnue de cette plate-forme",
    "err_mail_inconnu_ldap"=>"adresse éléctronique inconnue de votre ENT",




	// taches de maintenance
	"taches_maintenance" => "taches de maintenance",

	"purger_candidats" => "effacer les candidats de mon établissement/composante non inscrits à un examen",
	"purger_ex_nat" => "effacer les examens générés automatiquement par la plateforme nationale",
	"purger_non_ldap" => "supprimer les comptes qui ne sont plus dans l'annuaire'",
	"synchro_ldap" => "resynchroniser les informations extraites du LDAP (nom,prénom,mail,N°etudiant)",

	"reparer_xmls" => "réparer ou récréer les fichiers XML utilisés lors des échanges entre plateformes",

	"info_nb_examens_nationaux_purges" => "%s examens nationaux purgés",
	"info_nb_non_inscrits_purges" => "%s comptes de non inscrits supprimés",
	"info_nb_comptes_non_ldap_purges" => "%s comptes plus dans l'annuaire LDAP purgés",
	"info_nb_comptes_ldap_synchronises" => "%s comptes synchronisés avec l'annuaire LDAP",

	//import de questions
	"err_ref_ou_comp_inconnu" => "La question %s utilise une compétence ou un alinéa inconnu : %s.%s",
	"err_question_existe" => "La question %s existe déjà avec le même libellé",

	"info_qst_importe_ok" => "la question %s '%s' a été importée avec l'id %s.%s'",
	"info_rep_importe_ok" => "la réponse %s à la question %s.%s a été importée avec l'id %s",
    "info_doc_importe_ok" => "le document %s à la question %s.%s a été importé avec l'id %s",

	"info_trad_xml" => "conversion en xml de la question %s",

	// mots francais courants
	"ok"=>'ok',
	"annuler"=>'annuler',
	"succes" => "succès",
	"echec" => "echec",
	"echecs" => "echecs",

	"actif" => "actif",
	"maintenant" => "maintenant",
	"jamais" => "jamais",
    "reveler"=>"révéler",

	"oui" => "oui",
	"non" => "non",
	"et" => "et",
	"ou" => "ou",
	"origine" => "origine",
	"retour" => "retour",
	"ip" => "ip",
	"login" => "login",
	"general" => "général",
	"fiche" => "fiche",
	"lien" => "lien",
	"parcours" => "parcours",
	"notions" => "notions",
	"notion" => "notion ",
	"statistiques" => "statistiques",
	"score" => "score",
	"scores" => "scores",
	"maximum" => "maximum",
	"max" => "max.",
	"tous" => "tous / toutes",
	"ID" => "ID",
	"elements" => "elements",
	"tracking" => "tracking",
	"criteres" => "critères",
	"termine" => "terminé",
	"en_cours" => "en cours",
	"a_venir" => "à venir",
	"plateforme" => "plateforme",
    "famille"=>"thème",
    "familles"=>"thèmes",
    "etablissement"=>"établissement",
    "etablissements"=>"établissements",
    "composante"=>"composante",
    "composantes"=>"composantes",



	"personnel" => "personnel",
	"etudiant" => "candidat",
	"personnels" => "personnels",
	"etudiants" => "candidats",
    "passage" => "passage",
	"passages" => "passages",
	"inscrits" => "inscrits",
	"non_passes" => "non passés",
    "passage_suspect"=>"passage suspect",
	"documents" => "documents",
	"document" => "document",
	"validees" => "validées",
	"qcm" => "qcm",
	"qcms" => "qcms",
	"corrige" => "correction de l'examen",

	"candidat" => "candidat",
	"utilisateur" => "utilisateur",
	"resultats" => "résultats",
	"competence" => "compétence",
	"competences" => "compétences",

	"legende" => "légende",
	"aide" => "aide",
	"telechargement" => "Téléchargement",

	"parametres" => "Paramètres",
	"referentiel" => "domaine",
	"referentiels" => "domaines",
	"alinea" => "compétence",
	"alineas" => "compétences",
	"pool" => "pool",
	"pools" => "pools",
	"lecture_optique" => "lecture optique",
    "groupe"=>"groupe",
    "groupes"=>"groupes",
	//actions divers'
	"quitter" => "Se deconnecter de la plate-forme",
	"fermer" => "fermer cette fen&ecirc;tre",
	"retour" => "retour",
    "vide"=>"vide",
    "pas_encore_de_valeur"=>"pas encore de valeur",
    "valeur_defaut"=>"défaut = %s",

	//  en vrac encore

	"selection_bilan" => "Veuillez sélectionner les 3 critères ci-dessus",

	"tab_famille" => "thème",
	"famille_validee" => "thème validé",
	"famille_proposee" => "thème proposé",

	"heure_validation" => "heure de validation",

	"titre_liste_parcours" => " Liste des notions",
	"note_mini" => "seuil minimal pour validation du domaine",

	"url_titre" => "chemin des ressources",
	"url_actu" => "chemin actuel :  ",

	"modifier_notion" => "modifier la notion",
	"modifier_parcours" => "modifier le parcours",
	"dupliquer_notion" => "duplication de la  notion",
	"dupliquer_parcours" => "duplication du parcours",
	"fiche_notion" => "consulter la fiche de la notion",
	"modifier_ressource" => "modifier la ressource",
	"dupliquer_ressource" => "dupliquer la ressource",
	"fiche_ressource" => "consulter la fiche de la ressource",
	"not_asso" => "notion associée",
	"tableau_type" => "type",
	"tableau_consult_parcours" => "consulter le parcours",
	"tableau_consult_notions" => "consulter la notion",

	"texte_mail" => "texte du courriel",
	"envoyer_mail" => "envoyer un courriel",

	"commentaire_existe_pool" => "Une fois le pool créé il faut créer groupes. Pour cela (après avoir sélectionné les questions du pool), cliquer sur le lien 'générer les groupes'. Il ne sera plus possible de modifier le tirage des questions.L'inscription des étudiants se fait via le pool. Ils seront répartis dans les groupes  au moment du passage de l'examen en fonction de leur heure de connexion. Pour cela il faut modifier les horaires de passage dans les groupes générés (en évitant les chevauchements)",
	"pool_manque_question" => "Le nombre de questions sélectionnées dans ce pool est nul ou n'est pas suffisant pour créer les examens",
	"generer_groupes" => "générer les examens associés au pool",
	"commentaire_pool" => "permet de gérer un examen en plusieurs groupes, selon leur heure de passage. Des examens seront générés à partir de ce pool (un examen par groupe, auquel il vous faudra modifier les dates / heures de passage.",
	"commentaire_pool2" => "Pour générer les groupes il vous faudra aller dans la fiche de ce pool et cliquer sur générer les examens, disponible après sélection définitive des questions dans ce pool), et les étudiants doivent être inscrits à ce pool; ils seront automatiquement répartis en fonction de leur heure de connexion",

    "form_nb_groupes_pool" => "nombre d'examens ",
    "form_nb_questions_pool" => "nombre de questions total",
    "form_nb_questions_groupe_pool" => "nombre de questions par membre ",
    "nb_questions_par_enfant"=>"nombre de questions par membre : %s",
    "form_est_un_pool" => "est un pool",
    "form_liste_groupes" => "liste des QCM du pool",
    "form_est_un_groupe" => "membre d'un pool de QCM",
    "form_pool_pere" => "généré à partir de ",
	"form_restriction_ip"=>"Liste d'adresses IP autorisées",
	"info_restriction_ip"=>"choisissez une ou plusieurs plages (ctrl/maj click). Ne rien selectionner équivaut à ne mettre aucune restriction. ",
	"info_pas_de_plages_ip_declarees"=>"votre administrateur n'a pas déclaré de plages Ip pour votre établissement ; cette fonctionnalité n'est donc pas activée",

	"conf_fin" => "fin du questionnaire",
	"afficher_toutes" => "Afficher tout",
	"tirage_q_passage" => "aléatoire au moment du passage",
	"voir examens masques" => "voir examens masqués",
	"nom_patronymique" => "nom patronymique",
	"epouse" => "épouse",
	"ne_le" => "Né(e) le",
	"ne_rien_inscrire" => "ne rien inscrire en dehors des zones prévues",
	"signature_etudiant" => "signature de l'étudiant",

	"voir_comme_candidat" => "voir l'examen comme un candidat",
	"est" => "est",
	"nestpas" => "n'est pas",
	"methodologie de redaction de question" => "Méthodologie pour la production de QCM",
	"erreur_qcm_valide" => "erreur, vous avez déjà passé cet examen",
	//"texte_choix_multiple" => "Il peut y avoir une ou plusieurs bonnes réponses. Les réponses correctes sont comptées positivement, les réponses fausses sont comptées négativement. Ne rien cocher vaut 0.",
	
	'touts_ips'=>"n'importe quelle adresse IP",
'erreur_ip_invalide'=>"votre adresse IP %s n'est pas dans les adresses autorisées pour passer cet examen",

"texte_choix_multiple" =>' Il peut y avoir une ou plusieurs bonnes réponses. Pour chaque
question, les réponses correctes sont comptées positivement, les 
réponses fausses sont comptées négativement ; si le bilan de la 
question est négatif il est ramené à 0. Ne rien cocher vaut 0.',
	
	"conf_validation" => "êtes vous sûr de vouloir valider maintenant ? vous ne pourrez plus revenir sur le questionnaire après",
	"questions_par_ref" => "nombre de questions par domaine C2i",
    "questions_par_comp" => "nombre de questions par compétence C2i",
    "questions_par_famille" => "nombre de questions par thème C2i",
	"export_ref" => "Exporter le référentiel",
	"imprimer" => "imprimer",

	"login_non_disponible" => "le login n'est pas disponible, ni un login approchant. Veuillez recréer une fiche avec un autre login",
	"liste_questions" => "liste des questions",
	"ajouter" => "ajouter",
	"enlever" => "enlever",
	"modifier" => "modifier",
	"supprimer" => "supprimer",
	"dupliquer" => "dupliquer",
	"lister" => "lister",
	"valider" => "valider",
	"numetud" => "numéro étudiant",
	"telecharger" => "télecharger",
	"autres_droits" => "autres droits",

	"nombre_davis_sur" => "%s avis sur %s",

    "double_click_toedit"=>"Double click pour éditer",
    "temps_restant"=>"Il vous reste : <br/>",

	//parcours

	"modifier_parcours" => "modification du parcours",
	"dupliquer_parcours" => "duplication du parcours",
	"consulter_parcours" => "consultation du parcours",

	//espion rev 978 traduction automatique de l'action et de l'objet (nécessaire passage UTF8)

    "ajout"=>"ajout",
    "modification"=>"modification",
    "configuration"=>"configuration",
    "exportation"=>"exportation",
    "importation"=>"importation",
    "telechargement" => "téléchargement",
    "activation_mot_de_passe"=>"activation du mot de passe",
    "envoi_mail"=>"envoi mail",
    "envoi_phpmailer"=>"envoi mail PHP mailer",

    "envoi_mail_refuse"=>"envoi du passe par mail refusé",
    "perte_mot_de_passe"=>"mot de passe oublié",
    "inscription"=>"inscription",
    "deinscription"=>"désinscription",
    "duplication"=>"duplication",
    "validation"=>"validation",
    "invalidation"=>"invalidation",
    "filtrage"=>"filtrage",
    "archivage"=>"archivage",
    "tirage"=>"tirage",
    "relancer"=>"relancer",
    "ajout_famille"=>"ajout d'un thème",
    "err_ajout_famille"=>"erreur en ajoutant le thème",
    "maj_famille"=>"mise à jour du thème",

    "suppression_profil"=>"suppression du profil",
    "generation_membre_pool"=>"génération membre pool",

    "acces_sans_authentification"=>"accés sans authentification",
    "droits_introuvables"=>"droits introuvables",



    "purge_candidats_non_inscrits"=>"purge candidats non inscrits",
    "modification_template"=>"modification template",
    "reinitialisation_template"=>"réinitialisation template",
    "simulation_passage"=>"simulation passage",
    "annulation_simulation_passage"=>"annulation simulation passage",
    "purge_resultats"=>"purge résultats",
    "purge_inscrits"=>"purge inscrits à l'examen",
    "purge_inscrits_non_passes"=>"purge inscrits non passés l'examen",


    "envoi"=>"envoi",
    "acces_REST"=>"accès REST",


	"config" => "configurer",
	"track" => "tracking",

	// résultats
	"result" => "afficher les résultats",
	"resultats_de" => "réponses de ",
	"pas_de_resultats" => "Pas de résultats pour ce candidat",
	"resultats_passage_anonyme" => "Résultats de votre test",
	"resultats_passage" => "Résultats de votre test",
	"resultat_positionnement" => "Résultats de votre test de positionnement", //sujet du mail
	"fin_passage" => "Fin de votre test",
	"info_notes_manquantes" => "cet étudiant semble avoir passé le test mais aucune note n'a été trouvée<br/>" .
	"il est probable qu'il a fermé la fenêtre du QCM sans passer par le bouton 'terminer' <br/> " .
	"et qu'ensuite la date de fin de l'examen l'ait empeché d'y revenir <br/>" .
	"<b>ceci est normal si vous l'avez autorisé à repasser son examen</b> (il retrouvera ainsi ses réponses)<br/>" .
	"Appuyez sur le bouton 'renoter' pour recalculer ses scores : ",

	"info_repasser_examen" => "vous pouvez autoriser cet étudiant à repasser son examen (si la date n'est pas passée).<br/>" .
	"en cas d'appui involontaire sur ce bouton, vous pourrez toujours le 'renoter' puisque l'historique de ses " .
	"réponses ne sera pas détruit",
	"info_examen_passe_le" => "examen terminé le %s depuis %s",
    "info_duree_passage" => " (%s) ",

	//pensez à laisser le %s pour glisser le score global de l'étudiant
	"votre_score" => "votre score global est <b>%s</b>",
	//template_resultat
	"description_domaine" => "Description du domaine",
	"score_obtenu" => "Score obtenu",
	"score_global" => "Score global",
	"score_global_perso" => "Votre score global est de",
	"domaine" => "Domaine",
	"score" => "Score",
	"conditions" => "Conditions",
	"constantes" => "Constantes",
	"balise_dispo" => "Balise disponible",
	"recapitulatif" => "Récapitulatif",
	"vocab" => "Vocabulaire à utiliser : ",
	"vocab_methode" => "Faire du Copier / Déplacer du vocabulaire dans le formulaire de saisie",
	"vocab_methode2" => "Cliquer sur le champs pour obtenir le vocabulaire a copier",
	"simule_score" => "Simuler un score de",
	"resultat_examen" => "Résultat de l'examen",

	// nouveaux

	"nouvelle_question" => "nouvelle question",
	"nouvel_etablissement" => "nouvel établissement ou composante",
	"nouvelle_composante" => "nouvelle composante",
	"nouveau_profil" => "nouveau profil",
	"nouveau_candidat" => "nouveau candidat",
	"nouvelle_notion" => "nouvelle notion",

	"nouveau_personnel" => "nouvel utilisateur",

	// multipagination
	"afficher_la_page" => "aller page",
	"afficher_par_page" => "par page",
	"precedent" => "précédent",
	"suivant" => "suivant",
	//

	"item_profils" => "PROFILS",
	"item_etablissements" => "ETABLISSEMENTS",

	"erreur_duplication" => "une erreur s'est produite durant la duplication",
	"modifier_question" => "modifier question",
	"validation_question" => "validation de la question",
	"fiche_question" => "consulter la fiche de la question",
	"dupliquer_question" => "question dupliquée",
	"commenter_question"=>'commenter la question',
	"dupliquer_examen" => "examen dupliqué",
	"questions_sel" => "questions sélectionnées",
	"nouvel_examen" => "nouvel examen",
	"modifier_examen" => "modifier l'examen",
	"fiche_examen" => "consulter la fiche de l'examen",
	"modifier_etablissement" => "modifier l'établissement",
    "modifier_composante" => "modifier la composante",
	"fiche_etablissement" => "consulter la fiche de l'établissement",
    "fiche_composante" => "consulter la fiche de la composante",
	"modifier_profil" => "modifier le profil",
	"fiche_profil" => "consulter le profil",

	"modifier_personnel" => "modifier l'utilisateur",
	"fiche_personnel" => "consulter la fiche de l'utilisateur",
	"liste_personnels" => "liste du personnel de l'établissement",

	"fiche_inscriptions" => "consulter la liste des inscrits de l'examen",
	"modifier_etudiant" => "modifier le candidat",
	"fiche_etudiant" => "consulter la fiche du candidat",

	"liste_etudiants" => "liste des candidats de l'établissement",
	"selection_manuel" => "tirage manuel : sélection des questions",
	"selection_aleatoire" => "tirage aléatoire : sélection des questions",
	"selection_passage" => "tirage aléatoire au moment du passage",
	"modifier_tirage" => "modifier le tirage des questions",

	"ref_c2i" => "domaine C2i",
	"options_selection" => "options de sélection des questions",

	"date_passage" => "paramètres de passage de l'examen",
	"mdp_requis" => "mot de passe d'accès à l'examen",
	"mdp_errone" => "mot de passe erroné",
	"difficulte" => "difficulté",
	"aucun" => "aucun",
	"autre" => "autre",
/*
	"v_easy" => "très facile",
	"easy" => "facile",
	"medium" => "moyen",
	"difficult" => "difficile",
	"v_difficult" => "très difficile",
	"pre_requis" => "pré requis",
	"os" => "système d'exploitation",

	"suite_bureautique" => "suite bureautique",
	"ou_autre_logiciel" => "ou autre logiciel",
	"duree_vie" => "Date de fin de validité (jj/mm/aaaa)",

	"caracts" => "caracteristiques",
*/
"selectionner" => "sélectionner",
	"jour" => "jour",
	"mois" => "mois",
	"annee" => "année",
	"minute" => "minute",
	"seconde" => "seconde",
	"heure" => "heure",
	"annee" => "annee",
	"jours" => "jours",
	"annees" => "années",
	"minutes" => "minutes",
	"secondes" => "secondes",
	"heures" => "heures",

	"oui" => "oui",
	"non" => "non",
	"libelle" => "libellé",
	"manuel" => "manuel",
	"fixe" => "fixe",
	"langue" => "langue",
	"universite" => "université",
	"options" => "options",
	"contexte" => "contexte",
	"date" => "date",
	"experts" => "expert(s)",
	"question" => "question",
	"examen" => "examen",
	"acces" => "accès",
	"outil" => "outil",
	"config" => "configuration",
	"retour" => "retour",

	"aleatoire" => "aléatoire", //TRES IMPORTANT c'est la valeur mise en dur en BD TODO FIX !!!'

	"reponses" => "réponses",
	"reponse" => "réponse",

	"cochez_cases" => "Cochez les cases correspondant aux bonnes réponses",
	"cochez_cases_sup" => "Cochez les cases des documents à supprimer",
	"modif_config" => "modifier la configuration",
	"visualiser" => "visualiser",
	"questions" => "questions",
	"exporter" => "exporter",
	"examens" => "examens",
	"acces_s" => "accès",
    "profil" => "profil",
	"profils" => "profils",
	"examens" => "examens",
	"examens_attribues" => "liste des examens passés / à passer",
	"examens_disponibles" => "liste des examens disponibles",
	"attribues" => "liste des profils attribués",
	"disponibles" => "liste des profils disponibles",
	"outils_s" => "outils",
	"objet" => "objet",
	"action" => "action",
	"typeu" => "type d'utilisateur",
	"menu_perso" => "Infos persos",
	"menu_aide" => "Aide",
	"menu_deconnexion" => "Déconnexion",
	"convocations" => "convocations",

	"nom" => "Nom",
	"prenom" => "Prénom",
	"login" => "Nom d'utilisateur",
	"password" => "Mot de Passe",
	"confirmer_password" => "confirmez le mot de Passe",
	"adresse" => "Adresse",
	"num_tel" => "Téléphone",
	"email" => "Email",
	"fonction" => "Fonction",
	"texte_mdp" => "Les deux mots de passes doivent être identiques",
	"texte_login" => "Le login ne peut être nul",
	"texte_log" => "le login que vous avez saisi est déjà utilisé par une autre personne, il n'a pas été modifié et nous vous conseillons de le changer",
	"texte_longueur_mdp" => "le mot de passe doit faire au minimum 5 caractères",
	"texte_resultat_recherche" => "Résultat de la recherche",
	"valide" => "Validé",
	"inconnue" => "indéterminé",

	"nb_telec" => "Nombre de téléchargement de la plateforme",

	//page config
	"purger_tracking" => "purger le tracking",
	"purger_tracking_datant" => "purger le tracking jusqu'au  : dd/mm/yyyy (inclus)",
    "purger_tracking_jusquau" => "purger le tracking jusqu'au  (inclus): ",
	"telechargement_cp" => "Télécharger la plateforme locale complète version ",
	"telechargement_c" => "Télécharger la plateforme locale de certification version ",
	"telechargement_p" => "Télécharger la plateforme locale de positionnement version ",
	"telechargement_maj" => "Télécharger la mise à jour de la plateforme (ne modifie pas les données) version ",
	"telechargement_bdd" => "Synchroniser ma plateforme avec la plateforme nationale ",
	"remontee_questions"=>"Soumettre mes questions à la plate-forme nationale",
	"remontee_examens"=>"Remonter mes statistiques d'examens vers la plate-forme nationale",
    "export_xml_moodle"=>"Exporter les questions de positionnement au format XML Moodle 1.9",
    "export_xml_moodle_20"=>"Exporter les questions de positionnement au format XML Moodle 2.0",
    "export_xml_moodle_21"=>"Exporter les questions de positionnement au format XML Moodle 2.1",
    "export_referentiel_xml_moodle"=>"Exporter le réferentiel au format XML Moodle",
    "export_referentiel_objectifs_moodle"=>"Exporter le réferentiel au format objectifs Moodle",
    "export_referentiel_xml"=>"Exporter le réferentiel au format XML EmaEval",

	"configuration_avancee" => "configuration avancée",
    "info_php" => "informations PHP",
	'restrictions_ips'=>"Définir des plages d'adresses IP pour restreindre l'accès aux examens",

	"type d'import" => "Type d'import",
	"fichier au format d'import défini" => "Fichier au format d'import défini",
	"importer_questions" => "Importer des questions",
	"import" => "Import",
	"domaines_a_revoir" => "Domaines &agrave; revoir ",
	"domaines_referentiel" => "Domaine du référentiel",
	"questionnaire_concerne" => "Plate-forme concernée",
	"type_fichier_questionnaire_concerne" => "Type de fichier",

	"positionnement" => "Positionnement",
	"certification" => "Certification",
	"information_selection_qst" => "Attention : ne pas choisir deux questions appartenant au même thème",
	"fichier_qcmdirect" => "QCM direct",
	"fichier_xml" => "XML",
    "fichier_xml_moodle" => "XML Moodle",


	"alert_exam_anonyme" => "Vous avez choisi de désanonymer cet examen, pensez en anonymer un autre si vous voulez continuer à proposer cette possibilité",
	"fermer_fiche_pos" => "fermer ma fiche de positionnement",
	"fermer_fiche_pos_anonyme" => "fermer ce test anonyme",

	"conf_fermeture_fiche_pos" => "Avez-vous bien noté vos scores et les recommandations ? Vous ne pourrez plus le faire si vous fermez cette fiche.",
	"copie_de" => "copie de",

	//ldap

	"ldap_synchro" => "Champs à utiliser en recherche LDAP",
	"ldap_nom_champ_bd" => "Nom du champ",
	"ldap_nom_attribut" => "Attribut ldap",
	"err_champ_sans_valeur" => "Un champ n'a pas de valeur",
	"recherche_ldap" => "Recherche LDAP",
	"err_connexion_ldap" => "la connexion au(x) serveur(s) LDAP a échoué. Faites vérifier par l'administrateur du site, les paramètres LDAP dans la partie Configuration de la plate-forme ",
    "consulter_annuaire" =>"Consulter l'annuaire",
	// validation
	"validateur_inconnu" => "expert inconnu (compte supprimé ?)",

	//liste questions
	"bilan_questions" => "Bilan des questions",
	"import_questions" => "Importer des questions",
	"export_questions" => "Exporter des questions",


	//liste examens
	"voir_examens_masques" => "voir examens masqués",

	//fiche examen
	"exam_anonyme" => "Examen anonyme",

	"nouvelles_inscriptions" => "nouvelles inscriptions",
	"liste_etudiants_inscrits" => "inscrits à ",
	"liste_etudiants_absents" => "absents à ",
	"liste_inscrits" => "afficher la liste des inscrits",
	"liste_inscrits_np" => "afficher la liste des inscrits ne l'ayant pas passé",
    "liste_emargement_odt"=>"liste d'émargement au format OpenOffice",
	"nombre_questions" => "nombre de questions",
	"nombre_inscrits" => "nombre d'inscrits",
	"nombre_passages" => "nombre de passages",
	"comme_candidat" => "voir comme candidat",
	"recuperation_lecture_optique" => "récupération des résultats de lecture optique",
	"fichier xml de resultat" => "sélection du fichier contenant les résultats",
	"version_imprimable" => "version imprimable (OpenOffice deux colonnes)",
	"version_corrigee" => "corrigé de l'examen",
	"version_corrigee_imprimable" => "corrigé de l'examen imprimable (OpenOffice deux colonnes)",
	"passer_examen" => "passer cet examen (sans mémorisation de vos résultats)",
    "pas_modifier_tirage_passage" =>"Les questions de cet examen sont tirés aléatoirement lors du passage",
    "pas_modifier_tirage_droits" => "Pas les droits de modifier les questions de cet examen",


	"pas_modifier_tirage" => "La modification du jeu de questions est impossible lorsque l'examen est en cours ou terminé",
	"pas_modifier_tirage_pool" => "La modification du jeu de questions d'un pool est impossible lorsque des groupes ont été définis",
	"pas_modifier_tirage_pool_groupe" => "La modification du jeu de questions d'un examen appartenant à un pool est impossible",
	"generation_qcm_direct" => "Génération de l'examen au format QCM direct",
	"generation_qcm_direct_avec_referentiel" => "Génération de l'examen au format QCM direct avec le référentiel",
	"generation_AMC" => "Génération de l'examen au format Auto Multiple Choice",
    "generation_AMC_V2" => "Génération de l'examen au format Auto Multiple Choice (grille de réponses separée)",
	"reponses_par_etudiant" => "résultats par candidat",
	"export_bd_mysql" => "Transfert direct vers une base MySQL externe",
	"resultats_par_domaine" => "résultats par rapport au domaine",
	"resultats_apogee" => "Exporter les résultats au format Apogée",
	"resultats_complets" => "résultats complets",
	"resultats_synthetiques" => "résultats synthétiques",
	"resultats_export_xml" => "Exporter les résultats au format XML",
	"template_resultats" => "template resultats",
	"libl_template_resultats" => "Personnaliser l'affichage du résultat du positionnement",
	"reinit_template_resultats" => "Réinitialiser le template resultats",
	"reinit_template_resultats_question" => "Attention, si vous réinitialisez ce modèle, c'est le modèle de base qui sera utilisé.",
	"reinit_template_resultats_complet" => "Le template d'affichage du résultat est réinitialisé, c'est le modèle de base est désormais utilisé.",

	"fiche_reponses_qcm_doc" => "fiche réponse QCM au format DOC",
	"fiche_reponses_qcm_pdf" => "fiche réponse QCM au format PDF",
	"fiche_reponses_qcm_odt" => "fiche réponse QCM au format ODT",

	"fiche_reponses" => "fiche réponse QCM",
    "liste_emargement"=>"liste d'émargement",
	"apercus" => "apercus",
	"inscriptions" => "inscriptions",
	// rev 948
	"administration" => "administration",
	"supprimer_inscrits"=>"désinscrire tous les inscrits",
	"supprimer_inscrits_np"=>"désinscrire les inscrits ne l'ayant pas passé",
	"purger_resultats"=>"purger les résultats",
	"simuler_passage"=>"simuler passage des inscrits" ,
	"annuler_simuler_passage"=>"annuler une simulation de passage des inscrits" ,
    // rev 1020
    "import_examen"=>"importer un examen",
    "exporter_examen"=>"exporter cet examen",
    "archiver_examen"=>"archiver cet examen",
        "verouiller_examen"=>"verouiller cet examen",
     "deverouiller_examen"=>"déverouiller cet examen",

    "format_import_examen"=>"fichier d'examen exporté par une autre plate-forme",
    "examen_importe_comme"=>"examen %s importé sous le numéro %s ",
    "importation_question"=>"ajout de la question %s",
    "import_de"=>"import de",
    "question_inconnue_localement"=>"la question %s n'existe pas sur votre locale",


	"info_supprimer_inscrits"=>"vous allez désinscrire %s candidats à cet examen et supprimer leurs éventuels résultats",
	"info_supprimer_inscrits_np"=>"vous allez désinscrire %s inscrits (sur %s) n'ayant pas passé cet examen",
	"info_purger_resultats"=>"vous allez purger les résultats de %s inscrits (sur %s) à cet examen",
	"info_simuler_passage"=>"vous allez simuler le passage de %s inscrits (sur %s) à cet examen" ,
	"info_annuler_simuler_passage"=>"vous allez annuler la simulation du passage de %s inscrits (sur %s) à cet examen" ,


    // rev 944
    "info_referentiels_traites"=>"choisissez un ou plusieurs domaines (ctrl/maj click). ne rien selectionner équivaut à tous les domaines. ",
    "form_referentiels_traites"=>"domaines traités",
    "referentiels_traites"=>"(domaines traités : %s)",
    "touts_referentiels_traites"=>"(tous les domaines sont traités)",
    "touts_referentiels"=>"tous les domaines",

    "form_nombre_questions"=>"nombre de questions",
    "info_nombre_questions"=>"assurez-vous que ce nombre est un multiple du nombre de domaines à traiter (sinon un arrondi inférieur sera fait)",

  // rev 982
    "form_tags"=>'tags associés',
    "tags"=>"tags",


	//export apogee
	"info_export_apogee" => "selectionnez ici le fichier que vous avez monté sur ce serveur lors de l'inscription de vos
		candidats issus d'Apogée.<br/>
		Ce fichier sera complété avec les scores des candidats dans le format requis par votre administrateur Apogée pour le reimporter.
		<br/> si l'opération s'est bien deroulée, utiliser le lien 'Récuperer...' pour le récupérer",
	"info_scores_traites" => "%s scores exportés",
	"info_export_de" => "traitement de %s",
	"telecharger_fichier_apogee" => "récuperer le fichier d'export Apogée",

	//inscriptions
	"inscription_manuelle" => "inscriptions/desinscriptions manuelles",
	"inscriptions_massives_csv" => "inscriptions massives à partir d'une liste ou d'un fichier (csv ou Apogée)",

	"inscriptions_groupe_ldap" => "inscriptions à partir d'une liste de login, d'un fichier CSV  ou par groupe(s) LDAP ",
	"recherches_ldap" => "inscriptions par recherche dans l'annuaire LDAP",
	//"inscriptions_apogee"=>"inscriptions à partir d'un fichier Apogée",

	"inscrire" => "inscrire",
	"desinscrire" => "désinscrire",

	"inscrire_tous" => "tous",
	"desinscrire_tous" => "tous",
	"info_inscriptions_manuelle" => "pour inscrire des candidats existants, selectionnez les dans la liste de droite " .
	"et appuyez sur le bouton 'inscrire'. Pour désincrire, répetez l'opération avec la liste de gauche et le bouton 'désinscrire.".
    " <b>Attention : </b> les inscriptions/créations de comptes ne seront effectives que si vous finalisez via le bouton 'enregistrer'",

	"comptes_manuels" => "comptes créés manuellement",

	"info_inscriptions_oui" => "vous pouvez encore inscrire de candidats puisque l'examen est à venir ou en cours",

	"info_inscriptions_non" => "vous ne pouvez plus inscrire de candidats puisque l'examen est terminé",

	"info_inscriptions_ldap" => "tapez dans la zone recherche vos critères LDAP , puis selectionnez dans la liste 'dynamique' ceux que vous voulez inscrire pour les faire passer dans la liste de gauche" .
	".<b>Attention : </b> les inscriptions/créations de comptes ne seront effectives que si vous finalisez via le bouton 'enregistrer'",

	"info_inscriptions_ldap_oui" => "vous avez renseigné les paramètres LDAP dans 'configuration', espérons qu'ils fonctionnent ...",

	"info_inscriptions_ldap_non" => "vous n'avez pas renseigné les paramètres LDAP dans 'configuration', vous ne pouvez donc pas utiliser ces options",

	"info_inscriptions_groupe_ldap" => "vous pouvez inscrire ici des candidats connus dans votre annuaire LDAP/CAS ; un compte  sera automatiquement créé si nécessaire. <br/>" .
	" Ils devront utiliser leur mot de passe usuel pour se connecter. " .
	"Pour en desinscrire certains ensuite, passez par l'option  'inscriptions/desinscriptions manuelles'.",

	"info_inscriptions_csv" => "vous pouvez inscrire ici des candidats en spécifiant <u>au minimum " .
	"leur numéro d'étudiant</u>. Si un compte correspondant existe déja sur la plateforme, ils seront directement inscrits à cet examen ; " .
	"dans le cas contraire, nous avons besoin d'un nom, d'un prénom et éventuellement d'une adresse électronique " .
	"pour leur créer un compte dont le login sera de la forme %s_numéroetudiant et un mot de passe sera généré aléatoirement." .
	"<br/>Pour en desinscrire certains ensuite, passez par l'option  'inscriptions/desinscriptions manuelles'.".
    "<br/><u> Si ces comptes sont réferencés dans votre annuaire LDAP, vous ne devez pas utiliser cette option mais passer par".
    " les inscriptions massives LDAP </u>",

	"info_nb_inscrits" => "candidats inscrits",
	"info_nb_candidats" => "candidats potentiels",
	"info_nb_trouves" => "candidats trouvés",
	"info_nb_convoques" => "candidats convoqués",
	"info_nb_non_convoques" => "candidats non convoqués",
	"info_convocations_mail" => "les candidats de la liste de gauche seront convoqués avec le message ci-dessous que vous pouvez éditer.
		Veillez toutefois à ne pas supprimer les zones <b> [nom] [prenom] [login], [password] [examen] [date_debut] et [date_fin] </b>
		qui seront ensuite automatiquement personnalisées",

	"vos_criteres" => "au moins un de ceux ci commence par...",
	"sujet_standard_convocation" => "convocation à l'examen de [type_p]",
	"message_standard_convocation" => "Bonjour [prenom] [nom],\r\n" .
	"Vous  êtes convoqué à l'examen de [type_p] qui aura lieu le [date_debut] de [heure_debut] à [heure_fin].\r\n" .
	"Votre identifiant est : [login] et votre mot de passe : [password]\r\n" .
	"\r\n" .
	"Cordialement\r\n",

	"votre_passe_usuel_ent" => " votre mot de passe usuel sur l'ENT de votre établissement",

	"rapport_de_convocation_par_mail" => "rapport de convocation par mail",
	"texte_rapport" => "Bonjour [prenom] [nom],\r\n" .
	"Vous  venez de convoquer des candidats par mail pour  l'examen de [type_p] qui aura lieu le [date_debut] de [heure_debut] à [heure_fin].\r\n" .

	"\r\n" .
	"l'opération a réussi pour :\r\n" .
	"[oks]" .
	"\r\n" .
	"et a échoué pour :" .
	"[kos]" .
	"\r\n" .
	"\r\n" .
	"Cordialement\r\n",
    "mail_copie_envoye"=>'mail de copie envoyé à %s',
    "err_mail_copie_envoye"=>'erreur mail de copie pour %s',



	"info_criteres_recherche" => "la recherche s'effectue par défaut sur les valeurs commencant par ; si vous voulez les valeurs contenant ajoutez un symbole '%' en début de chaîne.",
	"criteres_ldap" => "critères de recherche",
	"comptes_ldap" => "comptes existants dans votre annuaire LDAP",
	"message_a_envoyer" => "message à envoyer",

	"err_compte_ldap_inconnu" => "le compte %s n'existe pas dans l'annuaire LDAP",
	"err_compte_inconnu" => "le compte %s n'existe pas ",
    "err_compte_pas_inscrit" => "%s n'est pas inscrit à l'examen %s.%s",
	"err_groupe_ldap_inconnu_ou_vide" => "le groupe %s n'existe pas dans l'annuaire LDAP ou est vide",
	"err_creation_compte" => "erreur en créant le compte %s",
    "info_candidat_cree" => "création du compte manuel %s ",
    "info_candidat_ldap_cree" => "création du compte LDAP %s ",
	"info_candidat_inscrit" => "%s a été inscrit à l'examen %s.%s",
	"info_candidat_deja_inscrit" => "%s est déja inscrit à l'examen %s.%s",
    "info_candidat_pas_inscrit" => "%s n'est pas inscrit à l'examen %s.%s",
    "info_candidat_inconnu" => "aucun candidat avec ce numéro %s n'a été trouvé",
    "info_score_importe" => "score %s de %s importé pour l'examen %s.%s",
    "info_candidat_deja_importe"=>"le candidat %s a déja des réponses pour l'examen %s.%s",
    "info_resultats_importes"=>"%s résultats importés",
    "err_numetudiant_pas_ldap" => "Ce numéro d'étudiant %s n'existe pas dans l'annuaire LDAP ",
    // rev 1016
    "err_pas_pool"=>"cette opération n'est pas permise sur un pool d'examen",
    "err_pas_membre_pool"=>"cet examen %s n'est pas membre d'un pool d'examen",
    "err_membre_pool"=>"cet examen %s est membre d'un pool d'examen",
    "err_pas_de_fichier"=>"aucune fichier n'a été déposé",

    //rev 1021
    "err_amc_question_non_trouvee"=>"Erreur d'importation AMC : la question %s n'est pas dans cet examen.%s ",
	"err_amc_oubli_export_cases_cochees"=>"Erreur d'exportation AMC : vous devez exporter en CSV avec séparateur TAB et cases cochées",

	"info_creation_compte" => "le compte %s a été ajouté",
	"info_comptes_traites" => "%s comptes ont été ajoutés/inscrits",
	"err_lecture_fichier" => "fichier introuvable ou non lisible %s",
	"err_ecriture_fichier" => "problème d'écriture du fichier %s",
	"err_rien_a_faire" => "aucune donnée n'a été trouvée",
	"err_action_invalide" => "action invalide %s",

	"err_fichier_apogee_sans_donnee" => "le fichier Apogée %s ne contient aucune donnée lisible",
	"err_fichier_sans_donnee" => "le fichier  %s ne contient aucune donnée exploitable (mauvais format ?)",
	"err_upload_fichier" => "erreur en récupération du fichier %s. Trop gros, type incorrect ,format incorrect, espace disque ...",
	"info_inscription_liste" => "inscriptions à partir d'une liste au format %s",
	"info_inscription_fichier" => "inscriptions à partir du fichier %s au format %s",
    "info_inscription_groupe" => "inscription à partir du groupe LDAP %s",

	"info_noter_nom_fichier_apogee" => "veuillez noter le nom de ce fichier %s qui vous sera redemandé lors de l'exportation des résultats vers Apogée",

	"info_creation_parcours"=>"Au vu de vos scores à chaque réferentiel, nous pouvons vous proposer un parcours de formation".
    " constitué d'une liste de ressources qui vous pourriez consulter en autoformation avant de repasser un nouveau test.<br/>".
    "vous retrouverez ce parcours à chaque de vos connexions à la plateforme.<br/>".
    "Cliquez sur le bouton ci-dessous pour recevoir votre parcours.",

    "info_consultation_parcours"=>"votre parcours %s vient d'être créé. Vous pouvez le consulter de suite en cliquant sur le bouton ci-dessous<br/>".
    "Vous retrouverez ce parcours à chaque de vos connexions à la plateforme sous la rubrique 'Parcours'.",


    "err_compte_incomplet" => "pas assez d'information pour créer un compte pour %s",
    "err_ligne_import_incomplete"=>"ligne incorrecte : ",
    "err_nb_reponses_qcmdirect"=>"la réponse %s à la question %s n\'a pas été prise en compte dans QCMDirect ....",

	"form_liste" => "à partir de cette liste",
	"form_fichier" => "à partir de ce fichier",
	"form_liste_ldap" => "à partir de cette liste (un login par ligne)",
    "form_liste_ldap_csv" => "à partir de ce fichier (un compte par ligne)",
	"form_groupes_ldap" => "à partir de ce(s) groupes LDAP ",
     "form_groupes_ldap_liste" => "à partir de ce groupe LDAP ",
	"form_sujet_message" => "sujet du message",
	"form_texte_message" => "texte du message",

    //import optiques
    "import_lecteurs_optiques"=>"Importation de scores depuis un lecteur optique",
    "info_import_lecteurs_optiques"=>"",
    "format_optique"=>"type de lecteur",

    "info_purger_candidats_non_inscrits"=>"cette opération supprimera de votre plateforme<b> %s </b>candidats de votre établissement <b>%s </b>qui ne sont plus inscrits à un examen. <br/>".
            "Veuillez appuyer sur le bouton 'confirmer' pour réaliser cette opération ou simplement fermer cette fenêtre pour l'annuler.",

	//format import csv

	"format_inpm" => "numéroetudiant;nom;prénom;email",
	"format_inmp" => "numéroetudiant;nom;email;prénom",
	"format_ipnm" => "numéroetudiant;prénom;nom;email",
	"format_ipmn" => "numéroetudiant;prénom;email;nom",
	"format_imnp" => "numéroétudiant;email;nom;prénom",
	"format_impn" => "numéroétudiant;email;prénom;nom",
	"format_apogee" => "format Apogée (numéro etudiant nom prénom date_naissance) avec tabulations",
    "format_n"=>"numéroetudiant",
    "format_l"=>"login",
    "format_m"=>"email",


	"form_format_fic" => "ordre des colonnes du fichier",
    "form_format_fic_ldap" => "<span class='commentaire1'>la première colonne du fichier contient un </span>",
	"form_format" => "ordre des colonnes",

	"convocations_mail" => "convocations par courriel",

	// types de passage de qcm
	"normal" => "",
	"previsualisation" => "prévisualisation",
	"corrige_de" => "corrigé",
	"correction_de" => "correction",
	"test_de" => "test",

	//fiche profil
	"membres" => "utilisateurs ayant ce profil",
	"conf_actif" => "(nécessite que configuration soit actif)",
	"legende_profils" => "Légende des profils",

	// messages d'informations
	"msg_pour_sauver" => "Pour sauvegarder cette page utilisez le raccourci de votre navigateur (en général <b>Ctrl + S</b> ou <b>commande + S</b>)<br />",
    "msg_fin_qcm_cert"=>"Fin de votre test de certification. Vos résultats ont été correctement enregistrés",
    "msg_info_parcours_html"=>"Voici une liste de liens que vous pourriez consulter pour améliorer votre score dans certains des domaines requis<br/>".
     "Enregistrez cette page au format HTML (ctrl S), par exemple sur votre clé USB puis reconsultez cette liste à loisir. <br/>".
     "Vous pouvez aussi choisir de vous créer un parcours de formation, qui sera enregistré sur la plateforme et que vous pourrez suivre et annoter à chacune de vos visites"       ,
	"msg_parcours_cree" => "Le parcours a été ajouté dans votre liste",
	"msg_anonyme_info_mail" => "L'adresse électronique est facultative, si vous la précisez vous recevrez le résultat de votre positionnement par mél en plus de son affichage.",
    "msg_anonyme_info_mail_requis"=>"Une adresse éléctronique valide est requise.",
    "msg_anonyme_info_mail_connue_pf"=>"Une adresse éléctronique valide est requise. De plus elle doit correspondre à un compte connu sur cette plate-forme.",
    "msg_anonyme_info_mail_connue_ldap"=>"Une adresse éléctronique valide est requise. De plus elle doit correspondre à un compte connu sur votre ENT.",



	"msg_pas_de_notions" => "aucune notion trouvée",
	"msg_pas_de_ressources" => "aucune ressource trouvée",
    "msg_pas_de_familles" => "aucun thème trouvé",
    "msg_pas_de_referentiels" => "aucun domaine trouvé",
    "msg_pas_de_alineas" => "aucune compétence trouvée",


	"msg_pas_de_questions" => "aucune question trouvée",
	"msg_pas_de_examen" => "aucun examen trouvé",
	"msg_pas_de_personnel" => "aucun personnel trouvé",
	"msg_pas_de_inscrits" => "aucun candidat trouvé",
	"msg_pas_de_parcours" => "aucun parcours trouvé",
	"msg_pas_de_qcm" => "aucun examen trouvé",
	"msg_pas_de_profils" => "aucun profil trouvé",
	"msg_pas_de_record" => "aucun enregistrement ne correspond à vos critères",
    "msg_pas_de_mots_cle_famille"=> "pas de mots clés pour ce thème",
    "msg_pas_de_commentaires_famille"=> "pas de commentaires pour ce thème",



	"msg_pas_davis" => "pas encore d'avis d'experts",
	"msg_info_tirage_passage" => "cet examen est du type 'tirage aléatoire lors du passage', donc si vous rechargez cette page, vous obtiendrez un jeu différent de questions",
	"msg_info_pas_enregistrer_reponses" => "En mode test d'examen, vos réponses ne sont pas enregistrées au fur et à mesure.<br/>" .
	"donc si vous rechargez cette page, vous ne retrouverez pas vos réponses précédentes",

	"msg_info_configuration_avancee" => "A partir de la version 1.5 la plate-forme est fortement configurable à l'aide d'une table c2iconfig. Pour l'instant vous pouvez consulter ici les options disponibles et éventuellement expérimenter certaines d'entre elles en intervenant directement dans cette table avec un outil tel que phpMyAdmin. " .
	"Evitez toutefois de modifier celles qui sont marquées ici comme 'non modifiables'. En cas d'anomalie, vous devriez remettre la valeur de la colonne 'défaut'.",

    "msg_info_configuration_avancee2" => "A partir de la version 1.5 la plate-forme est fortement configurable à l'aide d'une table c2iconfig.".
    "Evitez toutefois de modifier celles qui sont marquées ici comme 'non modifiables'. En cas d'anomalie, vous devriez remettre la valeur par défaut en cliquant ".
    "sur le lien défaut situé à coté de chaque zone de saisie",


	"msg_operation_reussie" => "l'opération s'est déroulée avec succès",
	"msg_operation_echouee" => "l'opération a echoué",
	"msg_operation_partie_reussie" => "l'opération a partiellement réussi",

    "msg_aussi_inscrit"=>"%s examens de %s",


	//infobulles
	"astuce" => "astuce",
	"information" => "information",
	"aide" => "aide",

	"msg_separes_points_virgules" => "éventuellement plusieurs séparés par des points-virgules",

	"msg_tri_colonnes" => "cliquez sur les entêtes pour changer l'ordre de tri",

	"msg_parametrage_composante" => "par défaut une composante prend toutes les valeurs de son établissement parent.<br/>",

	"msg_examen_anonyme" => "il ne peut y a voir qu'un seul examen anonyme sur une plateforme de positionnement. <br/>." .
	"Si vous 'anonymiser' cet examen, un éventuel ancien examen anonyme sera automatiquement 'désanonymisé'.<br/>" .
	"Pour que l'examen anonyme soit proposé, il faut aussi que ses dates de passage soient correctement renseignées.",

	"msg_password_ldap" => "mot de passe LDAP non modifiable",

	//javascripts
	// IL FAUT mettre des slahes au apostrophes. ils ne seront pas retirés par les templates si le nom
	// de la balise commence par js_xxxx
    // rev 1016 avec firefox 3.5.7 encore pb avec les apos internes donc je les vire !!!
	"js_reparation_xml_termine" => "l opération s est bien passée. Controlez le contenu du dossier des ressources : ",
	"js_parcours_supprimer_0" => "attention ! vous êtes sur le point de supprimer le parcours numéro :",
	"js_parcours_supprimer_1" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_notion_supprimer_0" => "attention ! vous êtes sur le point de supprimer la notion :",
	"js_notion_supprimer_1" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_notion_dupliquer_0" => " Voulez-vous dupliquer la notion :",
	"js_notion_dupliquer_1" => " ? Cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_ressource_supprimer_0" => "attention ! vous êtes sur le point de supprimer la ressource numéro :",
	"js_ressource_supprimer_1" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_parcours_dupliquer_0" => "Voulez-vous dupliquer le parcours :",
	"js_etudiant_supprimer_0" => "attention ! vous êtes sur le point de supprimer le candidat :",
	"js_etudiant_supprimer_1" => ". Cette action est définitive mais ne sera possible que si le candidat n a pas encore passé d examen. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_personnel_supprimer_0" => "attention ! vous êtes sur le point de supprimer l utilisateur :",
	"js_personnel_supprimer_1" => ". Cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_profil_supprimer_0" => "attention ! vous êtes sur le point de supprimer le profil :",
	"js_profil_supprimer_1" => "Cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_etablissement_supprimer_0" => "attention ! vous êtes sur le point de supprimer l établissement :",
	"js_etablissement_supprimer_1" => ".  Cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_composante_supprimer_0" => "attention ! vous êtes sur le point de supprimer la composante :",
	"js_composante_supprimer_1" => ".  Cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_question_supprimer_0" => "attention ! vous êtes sur le point de supprimer la question numéro :",
	"js_question_supprimer_1" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_question_invalider_0" => "attention ! vous êtes sur le point d'invalider la question numéro :",
	"js_question_invalider_1" => " Cette question n'apparaitra plus dans les nouveaux examens ni dans le téléchargement de la banque de données. cette action est réversible en repassant par le processus de validation. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_examen_supprimer_0" => "attention ! vous êtes sur le point de supprimer l examen numero :",
	"js_examen_fils_supprimer_0" => "attention ! cet examen %s est membre d un pool. Vous allez donc réduire le nombre d examens du pool %s",
    "js_desinscrire_0" => "Confirmez-vous la désinscription de %s de cet examen ? ",
    "js_radier_0" => "Confirmez-vous la radiation de %s de ce profil ? ",

     "js_examen_supprimer_1" => " cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",
	"js_pool_supprimer_0" => "attention ! vous êtes sur le point de supprimer le pool d examens numero :",
	"js_pool_supprimer_1" => ". Ceci supprimera automatiquement les %s membres de ce pool ",
	
	
	"js_ips_supprimer_0" => "attention ! vous êtes sur le point de supprimer la plage numéro :",
	"js_ips_supprimer_1" => "cette action est définitive. Cliquez sur annuler si vous avez cliqué par erreur",

	//info validation
	"js_valeur_numerique_attendue" => "valeur numérique attendue",
	"js_valeur_alpha_attendue" => "cette information ne doit contenir que des lettres",
	"js_valeur_alphanumerique_attendue" => "valeur alphanumérique attendue",
	"js_valeur_non_vide_attendue" => "cette information ne peut être vide",
	"js_valeur_courriel_incorrecte" => "cette adresse de courriel est incorrecte",
	"js_valeur_date_incorrecte" => "cette date est incorrecte",
	"js_valeur_url_incorrecte" => "cet URL est incorrect",
	"js_valeur_chemin_incorrecte" => "ce chemin est incorrect",
	"js_valeur_select_incorrecte" => "vous devez choisir un élément de cette liste",
	"js_valeur_cb_incorrecte" => "vous devez cocher une de ces options",

	"js_login_manquant" => "le login est manquant ou contient des caractères interdits",
	"js_login_court" => "le login est trop court, il doit faire au moins %s caractères",
	"js_nom_manquant" => "vous devez donner un nom",
	"js_prenom_manquant" => "vous devez donner un prénom",
	"js_numetudiant_manquant" => "vous devez donner un numéro d étudiant",
	"js_mail_incorrect" => "adresse de couriel invalide",
	"js_libelle_manquant" => "le libelle est manquant ou contient des caractères interdits",
	"js_reponse_manquante" => "vous devez fournir entre %s et %s réponses possibles",
	"js_referentiel_manquant" => "vous devez fournir le domaine",
	"js_alinea_manquant" => "vous devez fournir la compétence",
	"js_aucun_profil" => "vous n'avez affecté aucun profil à cet utilisateur",

	"js_egalite_mdp" => "le mot de passe et sa confirmation doivent être identiques",
	"js_mdp_vide" => "le mot de passe ne peut être d une taille inférieure à %s caractères",
	"js_mdp_conf_vide" => "la confirmation du mot de passe ne peut être d'une taille inférieure à %s caractères    ",
    "js_mdp_manquant"=>'le mot de passe ne peut être vide',
	"js_mdp_etud_vide" => "le mot de passe ne peut être vide",
	"js_date_debut_manquante" => "vous avez oublié la date de début de l'examen",
	"js_date_fin_manquante" => "vous avez oublié la date de fin de l'examen",

	"js_sujet_manquant" => "vous avez oublié le sujet",
	"js_corps_manquant" => "vous avez oublié le corps du message",
	"js_valeur_manquante" => "valeur manquante",

	//titres standard des colonnes des listes
	"t_login" => "login",
	"t_nom" => "nom",
	"t_prenom" => "prénom",
	"t_nom_prenom" => "nom prénom",
	"t_prenom_nom" => "prénom nom",

	"t_numetud" => "numéro d'étudiant",
	"t_score" => "score",
	"t_titre" => "titre",
	"t_auteur" => "auteur",
	"t_auth" => "type compte",
	"t_mdp" => "mot de passe",
	"t_mail" => "courriel",
	"t_examen" => "examen(s)",
	"t_referentiel" => "dom.",
    "t_domaine" => "référentiel",
    "t_ordref" => "ordre F.",

	"t_alinea" => "comp.",
	"t_competence"=>"compétence",
    "t_ancien_domaine"=>"Comp. V1",
	"t_famille_ordre" => "thème",
	"t_reponses" => "réponses",
	"t_modif" => "modif.",
	"t_supp" => "suppr.",
	"t_date" => "date",
    "t_duree" => "durée",
	"t_id" => "identité",
	"t_dateh" => "date/horaire",
	"t_valider" => "valid.",
	"t_invalider" => "invalider",
	"t_selectionner" => "sélectionner",
	"t_sel" => "sél.",
	"t_relancer" => "remplacer",
	"t_consult" => "consult.",
    "t_passer" => "passer",
    "t_envoyer"=>"soumettre",
    "t_filtrer"=>"filtrer",


	"t_nbinscrits" => "inscrits",
	"t_passage_qcm" => "passage de qcm",
	"t_dupl" => "dupl.",
    "t_famille"=>"thème",
    "t_etat"=>"état",
    "t_commentaires"=>"commentaires",
    "t_motscles"=>"mots-clés",

    "t_numero"=>"numéro",
    "t_signature"=>"signature",

    "t_actions"=>"actions",
	"t_lien"=>'lien',
    "t_URL"=>'URL',

	//stats
	"t_utilisation" => "utilisée dans ",
	"t_nb" => "nb.",
	"t_mini" => "mini",
	"t_maxi" => "maxi",
	"t_moyenne" => "moy.",
	"t_nbq" => "nb. questions",
	"t_idisc"=>"I.D.",
	"t_cdisc"=>"C.D.",
	"t_ec"=>"E.C.",


	//tracking

	"t_objet" => "objet",
	"t_id_objet" => "id objet concerné",
	"t_etab" => "Etablissement",
	"t_action" => "action",
	"t_type" => "type",
	"t_ip" => "adr. IP",
	"compte_supprime" => "compte supprimé",
	"etablissement_supprime" => "établissement supprimé",
	//cong. avancée
	"t_id" => "id",
	"t_categorie" => "catégorie",
	"t_cle" => "clé",
	"t_valeur" => "valeur",
	"t_defaut" => "défaut",
	"t_modifiable" => "modifiable",
	"t_description" => "description",

	//items de menu niveau 2
	"menu_legende" => "légende",
	"menu_criteres" => "critères",
	"menu_afficher_tout" => "tout afficher",
	"menu_nouvelle_notion" => "nouvelle notion",
	"menu_nouvelle_question" => "nouvelle question",
	"menu_nouvel_examen" => "nouvel examen",
	"menu_nouvel_etablissement" => "nouvel établissement",
	"menu_nouvelle_composante" => "nouvelle composante",
	"menu_nouveau_candidat" => "nouveau candidat",
	"menu_nouveau_personnel" => "nouvel utilisateur",
	"menu_imprimer" => "imprimer",
	"menu_csv" => "sortie CSV",
	"menu_ods" => "sortie OpenOffice",
	"menu_import" => "Importer",
	"menu_export" => "Exporter",
	"menu_nouveau_profil" => "Nouveau profil",
	"menu_vide" => "&nbsp;", //un element de menu sans texte

	//textes alternatifs
	// les version " longues sont pour les templates V2 (icones_action_liste)"
    "alt_blanc"=>'',
	"alt_nouveau" => "nouveau",

	"alt_accueil" => "accueil",
	"alt_validation" => "validation",
	"alt_valide" => "validée",
	"alt_non_valide" => "invalidée",
	"alt_attente" => "en attente",
	"alt_non_examinee" => "non examinée",
	"alt_toutes" => "toutes",

	"alt_valider" => "valider",
	"alt_filtrer" => "filtrer (ne pas utiliser dans un examen)",
	"alt_commenter_email" => "envoyer un commentaire aux experts",
	"alt_defiltrer" => "défiltrer (utiliser dans un examen)",
	"alt_criteres" => "Critères de selection",
	"alt_afficher_tout" => "tout afficher",
	"alt_legende" => "Aide sur les actions possibles",
	"alt_import" => "importer des questions",
	"alt_bilan" => "bilan des questions",
	"alt_notion" => "notion",
	"alt_alinea" => "compétence",
	"alt_choix_date_debut"=>"choix de la date de début",
	"alt_choix_date_fin"=>"choix de la date de fin",


	"alt_consult" => "consulter",
	"alt_consulter" => "consulter",
    "alt_passerqcm" => "passer ce QCM",
    "alt_desinscrire" => "Désinscrire ce candidat",

	"alt_liste" => "liste des inscrits",
	"alt_dupl" => "dupliquer / ajout par recopie",
	"alt_dupliquer" => "dupliquer / ajout par recopie",
	"alt_ajouter" => "ajouter",
	"alt_retirer" => "retirer",
	"alt_continuer" => "continuer",
	"alt_changer_question" => "changer cette question",

	"alt_modif" => "modifier",
	"alt_modifier" => "modifier",
	"alt_profil"=>"profil",
        "alt_export" => "exporter",
	"alt_supp" => "supprimer",
	"alt_supprimer" => "supprimer",
	"alt_iv" => "invalider",
	"alt_invalider" => "invalider",
	"alt_config" => "configuration",
	"alt_tri" => "trier selon ce critère",
	"alt_gerer_notions" => "gérer les notions",
	"alt_gerer_ressources" => "gérer les ressources",
	"alt_gerer_parcours" => "gérer les parcours",
	"alt_gerer_questions" => "gérer les questions",
	"alt_gerer_examens" => "gérer les examens",
	"alt_gerer_acces" => "gérer les accès",
	"alt_gerer_config" => "gérer la configuration",
	"alt_gerer_outils" => "gérer les outils",
	"alt_gerer_qcms" => "passer des qcms",
	"alt_gerer_parcours" => "gérer vos parcours de formation",
	"alt_imprimer" => "imprimer",
	"alt_csv" => "Tableur CSV",
	"alt_ods" => "Tableur OpenOffice",

	// labels dans les "formes"

	"form_pool" => "pool d'examens",
	"form_tirage" => "tirage des questions",
	"form_modifications" => "modifications",
	"form_remarques" => "remarques",
	"form_avis" => "avis",
	"form_mots_cles" => "mot(s) clé(s)",
	"form_sep" => "séparés par un point virgule",
	"form_auteurs" => "auteur(s)",
	"form_expert" => "expert(s)",
	"form_nom_coll" => "Nom (ou nom collectif)",
	"form_adresse_e" => "Adresse électronique",
	"form_ordre_q" => "ordre d'affichage des questions",
	"form_ordre_r" => "ordre d'affichage des réponses",
	"form_correction" => "correction visible après passage",
	"form_envoi_resultat"=>"envoyer les résultats par mél",
    "form_affiche_chrono"=>"afficher le temps restant",
	"form_passage_aleatoire" => "généré au moment du passage",
	"form_manuel" => "manuel",
	"form_aleatoire" => "aléatoire",
	"form_tirage_pool" => "membre d'un pool d'examen",
	"form_fixe" => "fixe",
	"form_mot_passe_examen" => "mot de passe éventuel d'accès à l'examen",

	"form_date_de_creation" => "date de création",
	"form_date_avis" => "date de cet avis",
	"form_date_de_modification" => "date de dernière modification",
	"form_examen_anonyme" => "examen anonyme",
	"form_parametres_passage" => "Paramètres de passage de l'examen",

    "form_ancien_domaine" => "Ancien domaine ",


	"form_nb_telec" => "nombre de téléchargements de la plate-forme",
	"form_nb_items" => "nombre d'items affichés par page",
	"form_nb_aleatoire" => "nombre de questions par examen aléatoire",
	"form_nb_experts" => "nombre d'experts nécessaires pour valider une question",
	"form_langue" => "langue",
	"form_nbqar" => "nombre de questions validées téléchargeables (-1 pour toutes)",
	"form_nbcandidats" => "nombre de candidats",
	"form_nbpersonnels" => "nombre de personnels",
	"form_nbexams" => "nombre d'examens",
	"form_nbquestions" => "nombre de questions locales",
	"form_composantes" => "composantes",
    "form_parents" => "parents",

	"form_type_p" => "type de plateforme",
	"form_nat_loc" => "plateforme",
	"form_liens" => "liens associés",
	"form_lien" => "lien associé",
	"form_version" => "version",
	"form_modifiable" => "modifiable",
	"form_filtree" => "filtree",
	"form_ordre"=>"ordre",
	"form_avis_experts" => "avis des experts",
	"form_avis" => "avis",
	"form_votre_avis" => "votre avis",
	"form_choix_famille" => "Choix du thème",
	// ""=>"famille proposée par l'auteur",

	"form_remarques" => "remarques",
	"form_modifications" => "modifications proposées",
	"form_utilisation" => "utilisée dans ces %s examens",
	"form_questions_auteur" => "auteur des questions",
	"form_examens_auteur" => "auteur des examens",

	"form_utilisee_parcours" => "utilisée dans %s parcours",
	"form_stats" => "statistiques",

	//avec jscalendar
	"form_debut_examen" => "date de début de l'examen",
	"form_fin_examen" => "date de fin de l'examen ",
    "form_duree_limite"=>"Limiter la durée de passage à ",
    "info_duree_limite"=>"Si vous spécifiez une valeur (en minutes) ici, les candidats auront ce temps pour passer l'examen ; ceci forcera l'affichage du chronométre",



	"form_auth" => "méthode d'authentification",
    "form_origine" => "origine",
	"form_nom" => "nom",
	"form_prenom" => "prénom",
	"form_mail" => "courriel",
	"form_mdp" => "mot de passe",
	"form_cmdp" => "confirmation du mot de passe",
	"form_id" => "numéro d'identification",
	"form_numetud" => "numéro d'étudiant",
	"form_attribues" => "examens attribués",
	"form_admin" => "",
	"form_pos" => "",
	"form_libelle" => "libellé",
	"form_login" => "nom d'utilisateur",
	"form_derniere_connexion" => "dernière connexion",
	"form_etablissement" => "etablissement",
	"form_reponse" => "réponse",
	"form_copy_self" => "m'envoyer un courriel récapitulatif'",

	"form_admin" => "est administrateur de l'établissement",
	"form_pos" => "Acces limité au positionnement",

	"form_ref_c2i" => "Domaine C2i",
	"form_alinea" => "compétence",
    "form_aptitude"=>"compétence",
	"form_famille_validee" => "thème validé",
	"form_famille_proposee" => "thème proposé",
	"form_date_de_creation" => "date de création",
	"form_date_de_utilisation" => "dernière utilisation",
	"form_date_de_envoi" => "communiquée à la nationale le",
	"form_date_de_envoi_stats" => "stats remontées à la nationale le",
	"form_validation" => "validation",
    "form_typep" => "plateforme",
    "form_description" => "description",
    "form_fichier_local" => "fichier local",

    "form_famille"=>"thème",
    "form_commentaires"=>"commentaires",
    "form_motscles"=>"mots clés",
    "form_ordref"=>"ordre thème",

	"form_de"=>'de',
    "form_envoi_anonyme"=>'envoi anonyme (sans mes coordonnées)',
    "form_copie_commentaire_self"=>"m'envoyer une copie du courriel envoyé ",
	"info_commenter_email"=>"vous pouvez ici envoyer un commentaire sur cette question ; ce commentaire sera envoyé
	à une liste de diffusion nationale (%s) et permettra d'améliorer la pertinence de notre base de questions. ",
	'sujet_standard_commentaire_question'=>'commentaire sur la question %s',



	// boutons d'actions divers'
	"bouton_terminer" => "Terminer",
	"bouton_imprimer" => "imprimer",
	"bouton_apercu" => "apercu...",
	"bouton_tester" => "tester...",

	"bouton_annuler" => "Annuler",

	"bouton_ajouter" => "ajouter",
	"bouton_enlever" => "enlever",
	"bouton_ajouter_tout" => "ajouter tout",
	"bouton_enlever_tout" => "enlever tout",

	"bouton_defaut" => "Valeur par défaut",
	"bouton_modifier" => "Modifier",
	"bouton_fermer" => "Fermer",
	"bouton_enregistrer" => "Enregistrer",
	"bouton_retour_liste" => "retour à la liste",
	"bouton_retour_fiche" => "retour à la fiche",
	"bouton_continuer" => "continuer",
	"bouton_reset" => "tout effacer",
	"bouton_retour_pere" => "retour à l'établissement supérieur",
	"bouton_ok" => "ok",
	"bouton_details" => "détails...",
	"bouton_renoter" => "renoter",
	"bouton_repasser" => "l'autoriser à repasser l'examen",
	"bouton_inscrire" => "inscrire",
	"bouton_desinscrire" => "désinscrire",
	"bouton_envoyer" => "envoyer",
	"bouton_confirmer" => "Confirmer cette action",
	"bouton_generer_groupes" => "générer les examens associés au pool",
    "bouton_tout_cocher" => "tout cocher",
    "bouton_tout_decocher" => "tout décocher",
    "bouton_importer" => "importer",
    "bouton_telecharger" => "télécharger",
    "bouton_creer_parcours" => "créer un parcours de formation",
     "bouton_consulter_parcours" => "consulter mon parcours de formation",




	// boite de dialogue configuration
	// les noms sont de la forme config_nomduchampdanslatablec2ietablissement !important
	// aussi dans l'écran, de configuration
	"config_param_nb_items" => "Nombre d'items affichés par page",
	"config_param_nb_aleatoire" => "Nombre de questions par examen aléatoire (si différent d'un multiple de %s un arrondi inférieur sera fait)",
	"config_param_nb_experts" => "Nombre d'experts nécessaires pour valider une question de certification",
	"config_param_nb_qac" => "Nombre de questions à communiquer aux universités",
	"config_param_ldap" => "Coordonnées de l'annuaire LDAP de l'université (adresse IP ou adresse)",
	"config_base_ldap" => "base LDAP de l'université (sous la forme dc=...,dc=...)",
	"config_rdn_ldap" => "bind_rdn LDAP de l'université si nécessaire",
	"config_passe_ldap" => "bind_password LDAP de l'université si nécessaire",
	"config_ldap_version" => "version LDAP (si différente de 2)",
    "config_ldap_user_type" => "type d'annuaire  (si différent de Open LDAP)",
	"config_param_langue" => "langue",
	"config_ldap_group_class" => "classe LDAP ou sont stockés les groupes (si différent de groupOfNames)",
	"config_ldap_group_attribute" => "attribut LDAP des membres des groupes (si différent de member)",
	"config_ldap_id_attribute" => "attribut LDAP ou est stocké le numéro d'étudiant (si différent de supanncodeine)",
	"config_url" => "chemin des ressources concernant les notions",
    "config_chemin_ressources" => "chemin des ressources",
	"config_ldap_login_attribute" => "attribut LDAP ou est stocké le login (si différent de uid)",
	"config_ldap_mail_attribute" => "attribut LDAP ou est stocké le mail (si différent de mail)",
	"config_ldap_nom_attribute" => "attribut LDAP ou est stocké le nom (si différent de sn)",
	"config_ldap_prenom_attribute" => "attribut LDAP ou est stocké le prénom (si différent de givenName)",
	"config_ldap_champs_recherche" => "Champs à utiliser en recherche ldap",
	"0expert" => "pour valider dès la création",

	// liens de la zone footer
	// ces liens sont toujours renseignés par le mécanisme de traduction automatique
	"credits" => "crédits",
	"licence" => "licence",
	"prerequis" => "Prérequis techniques",
	"copyright" => "Tous droits réservés",
	"forum" => "forum",
	"wiki" => "wiki",
	//
	// zone des erreurs
	"err_tirage_pas_membre_pool" => "La modification du jeu de questions d'un examen appartenant à un pool est impossible",

	"err_login_mdp" => 'paramètres d\'accès non reconnus',
	"err_login_mdp_ldap" => 'login ou mot de passe LDAP erronés',
	"err_pas_de_compte_pf"=>"Vous avez été authentifié sur votre ENT, mais un compte avec votre login n'existe pas sur cette plate-forme.<br/>".
	          "Si vous estimez qu'il s'agit d'un oubli, veuillez contacter votre responsable C2i.",

	"err_login_mdp_vides" => "le login et mot de passe ne peuvent être vides",
	"err_lecture_config" => "erreur de lecture de la table c2iconfig. Cette table ne peut pas avoir de prefix autre que c2i, car c'est elle qui contient l'information sur ce prefixe !",

	"err_page_aide_non_trouve" => "la page d'aide %s n'a pas été trouvée.<br/> " . "Merci de signaler l'erreur sur <a href='http://www.c2i.education.fr/forum-c2i-1/'  target='_blank'  >le forum C2i</a>",
	"erreur_fatale" => "Erreur fatale",
	"err_mysql_serveur" => "Connexion au serveur impossible",
	"err_mysql_bd" => "Connexion a la base de données impossible",
	"err_mysql" => "Erreur dans l'exécution de la requête",
	"err_param_requis" => "Paramètre requis manquant",
	"err_param_suspect" => "erreur chaîne contenant un caractère non autorisé ",
	"err_compte_inconnu" => "ce compte n'existe pas",
	"err_profil_inconnu" => "ce profil n'existe pas",
	"err_examen_inconnu" => "cet examen  n'existe pas",
	"err_etablissement_inconnu" => "cet établissement n'existe pas",
	"err_question_inconnu" => "cette question n'existe pas",
	"err_question_non_nationale"=> "vous ne pouvez commenter par mèl qu'une question nationale",
	"err_adresse_experts_non_definie"=>"l'adresse mèl ou envoyer les commentaires (CFG->adresse_feedkack_questions) n'est pas définie sur votre plate-forme.",

    "err_pas_de_notions_parcours_ici" => "cette plateforme ne gére pas les notions et les parcours. Desolé...",
    "err_notion_inconnu" => "cette notion n'existe pas",
	"err_parcours" => "ce parcours n'existe pas",

	"err_referentiel_inconnu" => "ce domaine n'existe pas",
	"err_alinea_inconnu" => "cette compétence n'existe pas",
	"err_parcours_vide" => "comme vous n'avez coché aucune notion, aucun parcours n'a été créé",
	"err_parcours_inconnu" => "ce parcours %s n'existe pas",

    "err_pas_examen_anonyme" => "Pas d'examen anonyme autorisé ou en cours",
	"err_examen_mdp" => "erreur de mot de passe pour cet examen",
	"err_examen_deja_passe" => "vous avez déja passé cet examen",
	"err_examen_non_dispo" => "cet examen n'est pas disponible",
	"err_acces" => "accès non autorisé",
	"err_session_expire" => "votre session a expiré. Veuillez-vous reconnecter",
	"err_droits" => "incapable de determiner les droits de ",
	"err_duplication" => "erreur pendant la duplication",
	"err_param_suspect" => "erreur chaîne contenant un caractère non autorisé",
	"err_droits" => "vous n'avez pas le droit d'effectuer cette action",
	"err_adresse_ip_differe" => "erreur : adresse ip différente de celle stockée en session",
	"err_sql_injection" => "erreur : valeur illégale pour ce paramètre :",
	"err_sql_injection_num" => "erreur : injection sql : ce paramètre doit être numérique",
	"err_sql_injection_alphanum" => "erreur : injection sql : ce paramètre doit être alphanumérique",
	"err_rep_install_present" => "vous devez supprimer les fichiers du répertoire d'installation avant de pouvoir vous connecter",
	"err_droits_ressources" => "le dossier des ressources n'est pas correct ou n'est pas accessible en écriture",
	"err_modif_question_validee" => "Vous ne pouvez modifier cette question car elle a été validée par %s expert(s).",
	"err_acces_certification" => "Vous n'avez pas accès à la certification",
	"err_deja_logue_en" => "vous êtes déja connecté en ",
	"err_deja_connecte_autre_login" => "vous êtes déja connecté dans une autre fenêtre comme ",
	"err_conn_ldap" => "erreur de connexion au serveur ldap ",
	"err_auth_ldap" => "erreur d'authentification ldap ",
    "err_numetudiant_pas_ldap"=>"ce numéro d'étudiant %s n'existe pas dans votre LDAP",
    "err_login_pas_ldap"=>"ce login %s n'existe pas dans votre LDAP",


	"err_auth_ldap_anonyme" => "erreur de connexion (anonyme) au serveur ldap ",
	"err_dossier_ressources_inconnu" => "le dossier nécessaire pour stocker les ressources n'existe pas %s ",
	"err_dossier_ressources_droits" => "le dossier nécessaire pour stocker les ressources n'est pas accessible en écriture %s",
	"err_dossier_ressources_sature" => "le dossier nécessaire pour stocker les ressources est saturé (problème d'espace disque ?)",
	"err_fichier_langues_inconnu" => "fichier de langue non trouvé ou non lisible ",

	"err_config_parametre_inconnu" => "erreur de configuration : ce paramètre %s n'existe pas ",
	"err_question_sans_reponse" => "cette question n'a pas de réponses ",
	"err_pas_de_referentiel" => "la table des domaines est vide !",
	"err_pas_de_alineas" => "ce domaine n'a pas de compétence associée ?",
	"err_config_item" => " ce  paramètre %s n'est pas modifiable par cette méthode (BUG v 1.5 !) ",

	"err_deux_questions_meme_famille" => "vous avez selectionné deux questions du même thème",
	"err_tirage_pas_manuel" => "cet examen n'est pas en tirage 'manuel'",
	"err_tirage_pas_manuel_aleatoire" => "cet examen n'est pas en tirage 'manuel' ni aléatoire",

    "err_type_de_fichier_interdit"=>"pour des raisons de sécurité vous ne pouvez pas téléverser sur la plateforme le fichier %s ",

    "err_qcm_direct_bad_format"=>" ligne mal formatée %s",
    
	"err_restrictions_ip"=>"Vous devez activer les restrictions IP dans la configuration avancée",
	// strings utilisés par lib_date a la moodle

	'locale' => 'fr_FR.UTF8', // For France. If you live in Switzerland, use fr_CH.UTF-8
	'localewin' => 'French_France.1252',

	'secondstotime86400' => '1 jour',
	'secondstotime172800' => '2 jours',
	'secondstotime259200' => '3 jours',
	'secondstotime345600' => '4 jours',
	'secondstotime432000' => '5 jours',
	'secondstotime518400' => '6 jours',
	'secondstotime604800' => '1 semaine',
	'serverlocaltime' => 'Heure locale du serveur',
	'statstimeperiod' => 'Période&nbsp,:',
	'strftimedate' => '%d %B %Y',
	'strftimedateshort' => '%d/%m/%Y',
	'strftimedatetime' => '%d %B %Y, %H:%M',
	'strftimedatetimeday' => '%A %d %B %Y, %H:%M',
	'strftimedatetimeshort' => '%d/%m/%Y %H:%M',
	'strftimedaydate' => '%A %d %B %Y',
	'strftimedaydatetime' => '%A %d %B %Y, %H:%M',
	'strftimedayshort' => '%A %d %B',
	'strftimedaytime' => '%a, %H:%M',
	'strftimemonthyear' => '%B %Y',
	'strftimerecent' => '%d %b, %H:%M',
	'strftimerecentfull' => '%a %d %b %Y, %H:%M',
	'strftimetime' => '%H:%M',

	//format entrée sortie vers jscalendar
	"jscalendar_if" => "%A %d/%m/%Y %H:%M",

	"date_heure_jours" => "%s du %s au %s",
	"date_heure_jour" => "%s le %s de %s à %s",

	"scanner_cette_image"=>"",
	"msg_scanner_cette_image"=>"scanner cette image sur votre smartphone pour obtenir l'adresse URL de cette plate-forme",
"msg_doc_code_qr"=>"en savoir plus sur le code bare 2D QR",


	// on profite de la traduction auto des balises pour virer quelques erreurs CSS et HTML
	// en mode TEMPLATES_DEBUG
	"style_quitter" => "",
	"style_retour" => "",
	"selected" => "",
	"checked" => "",

	"AMC_entete_grille_reponses"=>' Feuille de réponses',
	"AMC_info_numetudiant"=>' codez votre numéro d étudiant ci-contre en noircissant entiérement les cases, et écrivez votre nom et prénom ci-dessous.',
	"AMC_info_nom_prenom"=>'Nom et prénom : ',

	"info_scores_identiques"=>"scores importés pour %s identiques entre %s et %s",
	"info_scores_non_identiques"=>"scores importés pour %s NON identiques entre %s et %s",


    "err_pas_envoi_mdp_admin_national"=>"Ce compte administrateur d'un établissement ne peut pas recevoir son mot de passe par courriel, veuillez contacter l'administrateur national.",

    "err_pas_envoi_mdp_admin_local"=>"Ce compte administrateur de la plate-forme ne peut pas recevoir son mot de passe par courriel, veuillez contacter votre administrateur réseau",


"ressource"=>"ressource",
"ressources"=>"ressources",
"fiche_ressource"=>"fiche ressource",
"nouvelle_ressource"=>"nouvelle ressource",


'gestion_ips'=>"gestion des plages d'adresses IP",
'nouvelle_plage'=>"nouvelle plage",
'form_adresses'=>'adresses',
't_adresses'=>'adresses',
'plages'=>'plages',
't_utilisees_examens'=>"nombre d'examens",
"fiche_plage"=>"fiche plage",


'commentaires_reponses'=>'Entrez un éventuel commentaire pour cette réponse',


);

//web service
//web service
$textes_langues['ws_accessdisabled']="accès par web service désactivé";
$textes_langues['ws_accessrestricted']="accès non autorisé pour cet ip %s";
$textes_langues['ws_invalidclient']="client ou session incorrecte";
$textes_langues['ws_illegaloperation']="pas les droits de faire cela";

$textes_langues['ws_invaliduser']="utilisateur ou mot de passe incorrect";
$textes_langues['ws_norights']="permission refusée";


$textes_langues['ws_candidatinconnu']="candidat %s=%s introuvable";
$textes_langues['ws_utilisateurinconnu']="utilisateur %s=%s introuvable";
$textes_langues['ws_compteinconnu']="compte %s=%s introuvable";

$textes_langues['ws_etablissementinconnu']="établissement %s introuvable";
$textes_langues['ws_exameninconnu']="examen %s introuvable";
$textes_langues['ws_questioninconnue']="question %s introuvable";
$textes_langues['ws_documentmanquant']="document %s.%s introuvable pour la question %s";

$textes_langues['ws_plateformeinconnue']="type de plate-forme '%s' inconnu";


$textes_langues['ws_pasdereferentiels']="aucun domaine connu ?";

$textes_langues['ws_pasdenotions']="aucune notion connue ?";
$textes_langues['ws_pasdefamilles']="aucun thème connu ?";
$textes_langues['ws_pasdetablissements']="aucun établissement connu ?";
$textes_langues['ws_pasdenotions']="aucune notion connue ?";

$textes_langues['ws_pasdeliens']="notion %s sans liens ou inconnue ";
$textes_langues['ws_pasdalineas']="domaine %s sans compétence ou inconnu ";

$textes_langues['ws_pasdinscrits']="pas d'inscrits à l'examen %s";
$textes_langues['ws_paspasse']="le candidat %s=%s n'a pas passé l'examen %s ";

$textes_langues['ws_valeurmanquante']="une valeur est requise pour %s";

$textes_langues['ws_valeurincorrecte']="la valeur de %s , '%s', est incorrecte";

$textes_langues['ws_sqlerror']="erreur SQL dans %s";

$textes_langues['ws_compteexistant']="un compte existe déja avec %s = '%s' ";

$textes_langues['ws_questionsansreponses']="question %s sans réponses proposées?";
$textes_langues['ws_questiondejasoumise']="question %s déja soumise";

$textes_langues['ws_pasdecorrige']="corrigé de l'examen %s non disponible";



$textes_langues['ws_paslocallocal']="L'échange  entre plate-formes locales n'est pas permis.";

$textes_langues['ws_unknownoutputformat']='format de sortie inconnu %s';


$textes_langues['salutation_commentaire_mail']=<<<EOT
<p>Bonjour,</p>
<p> L'utilisateur de la plate-forme %s, de l'établissement %s, a commenté
de la façon suivante la question %s </p>

<p> Merci de lui répondre. </p>
<hr/>
EOT;

$textes_langues['salutation_commentaire_mail_anonyme']=<<<EOT
<p>Bonjour,</p>
<p> Un utilisateur de la plate-forme qui n'a pas voulu laisser ses coordonnées a commenté
de la façon suivante la question %s </p>
<hr/>
EOT;



$textes_langues['info_restrictions_ips']=<<<EOT
<p>
Vous pouvez définir ici, en leur nommant, des sous-réseaux particuliers d'un réseau local (LAN) ou de l'internet
 en leur associant une liste d'adresses IP (complète ou partielle)
</p>
Cela peut être spécialement utile lorsque vous désirez que seules les personnes dans une salle spécifique puissent ensuite accéder à l'examen.

Vous pouvez indiquer ici <b>quatre types d'adresses IP</b> (il n'est pas possible d'utiliser des adresses sous la forme de nom de domaine,
 par exemple « mon-univ.fr ») :
<ul>
<li>des adresses IP complètes, comme 192.168.10.1, qui correspondent à un seul ordinateur (ou un serveur proxy) ;
<li>des adresses IP partielles, comme 192.168, qui correspondent à tous les ordinateurs dont l'adresse commence ainsi ;
<li>des adresses en notation CIDR, comme 231.54.211.0/20, qui permettent de spécifier des sous-réseaux de manière plus fine.
<li>une plage d'adresses IP 231.3.56.10-20. La plage spécifie un intervalle sur la dernière partie de l'adresse.
 L'exemple indique ici les adresses comprises dans l'intervalle de 231.3.56.10 à 231.3.56.20.
<li> Finalement, pour une <b>même plage nommée</b>, il est possible de spécifier plusieurs adresses séparées par une virgule comme
134.214.152.116-141 , 134.214.116.17
<li> Notez enfin que d'éventuels espaces entre les adresses seront ignorés.  
</ul>

EOT;






//synchronisation locale nationale
// ne pas le mettre dans locale/fr.php qui est reservé aux personnalisations locales !
//TODO le mettre dans locale/langues/fr.php (ca devrait marcher)

$textes_langues["err_extension_phpsoap_non_installe"]="Pour vous synchroniser avec la plate-forme nationale, vous devez installer <a href='http://pear.php.net/package/SOAP'> l'extension phpsoap </a>";
$textes_langues["synchronisation_plateforme"]="Synchronisation avec la plate-forme nationale";
$textes_langues["info_synchronisation_plateforme"]=<<<EOT
Avec cette option vous allez pouvoir synchroniser votre plate-forme avec la plate-forme nationale. Vous aurez besoin de vos identifiants de correspondant C2i sur la plate-forme nationale (ceux que vous avez utilisé pour télécharger le code). Cochez ensuite les cases
en regard des informations que vous voulez récuperer ou envoyer .
EOT;

 //
$textes_langues["info_remontees_questions"]=<<<EOT
Avec cette option vous allez pouvoir remonter sur la plate-forme nationale les questions, qui après validation
par les experts, feront partir de la base de questions nationale du C2I.
Vous aurez besoin de vos identifiants de correspondant C2i sur la plate-forme nationale (ceux que vous avez utilisé pour télécharger le code).
EOT;


$textes_langues["info_export_questions"]=<<<EOT
Avec cette option vous allez pouvoir exporter dans un format XML simplifié les questions de votre établissement,
par exemple pour les importer vers une autre plate-forme.
EOT;

$textes_langues["info_remontees_examens"]=<<<EOT
Avec cette option vous allez pouvoir remonter sur la plate-forme nationale les résultats anonymisés de vos examens passés
( scores globaux et scores par question) afin d'aider les experts a évaluer la pertinence des questions de la base nationale du C2I.
Vous aurez besoin de vos identifiants de correspondant C2i sur la plate-forme nationale (ceux que vous avez utilisé pour télécharger le code).
EOT;

$textes_langues["sync_0"]="simuler l'opération complète :";
$textes_langues["sync_1"]="mettre à jour la liste des établissements :";
$textes_langues["sync_2"]="mettre à jour la liste des thèmes :";
//$textes_langues["sync_3"]="mettre à jour la liste des notions :";
$textes_langues["sync_3"]="mettre à jour la liste des ressources :";
$textes_langues["sync_4"]="recevoir la liste des nouvelles questions :";
$textes_langues["sync_5"]="mettre à jour l'invalidation des questions :";
$textes_langues["sync_6"]="mettre à jour le réferentiel ";


$textes_langues["info_connecte_nationale"]="connecté à la plateforme nationale : %s %s@%s";
$textes_langues["info_deconnecte_nationale"]="deconnecté de la plateforme nationale %s";

$textes_langues["info_connecte_public"]="connecté au serveur public c2i : %s";
$textes_langues["info_deconnecte_public"]="deconnecté du serveur public c2i : %s";

$textes_langues["nb_items_recus"]="%s élements recus de la nationale";
$textes_langues["nb_items_presents"]="%s élements connus localement";
$textes_langues["nb_items_maj"]="%s élements mis à jour";
$textes_langues["nb_items_maj_etat"]="l'état (seulement) de %s élements a été mis à jour";

$textes_langues["nb_items_ajoutes"]="%s élements ajoutés";
$textes_langues["nb_items_supprimes"]="%s élements supprimés";

$textes_langues["nb_items_a_envoyer"]="%s %s à envoyer";

$textes_langues["maj_item"]="mise à jour de %s : %s %s";
$textes_langues["maj_item_etat"]="mise à jour seulement de l'état de %s : %s %s";

$textes_langues["ajout_item"]="ajout de %s : %s %s";
$textes_langues["suppression_item"]="suppression de %s : %s %s";
$textes_langues["suppression_item"]="suppression de %s : %s %s";
$textes_langues["devrait_suppression_item"]="la %s : %s n'est plus sur la nationale";
$textes_langues["nb_questions_pas_nationale"]="%s questions ne sont pas/plus sur la nationale";

$textes_langues["pb_insertion_item"]="pb insertion %s :numéro auto %s différent de celui reçu de la nationale %s";

$textes_langues["question_utilisee"]="la question %s n'a pas été mise à jour car elle est utilisée dans un de vos examens";

$textes_langues["question_invalidee"]="invalidation de la question %s";

$textes_langues["pas_modifier_etab_perso"]="votre établissement %s %s ne sera jamais modifié ";
$textes_langues["question_recue_sans_reponses"]="la question %s a été recue sans réponses ???";
$textes_langues["question_recue_sans_documents"]="la question %s a été recue sans documents ???";

$textes_langues["info_question_soumise_ok"]="question %s soumise à la plate-forme nationale";
$textes_langues["info_examen_soumis_ok"]="examen %s transmis à la plate-forme nationale";
$textes_langues["info_examen_soumis_ko"]="erreur en transmission de l'examen %s à la plate-forme nationale";
$textes_langues["examen_remonte_certif"]="Examen de statistiques certification";
$textes_langues["examen_remonte_posit"]="Examen de statistiques positionnement";

$textes_langues["debut_synchro_nationale"]="début synchro nationale";
$textes_langues["fin_synchro_nationale"]="fin synchro nationale";

$textes_langues["debut_envoi_questions_locales"]="début envoi questions locales";
$textes_langues["fin_envoi_questions_locales"]="fin envoi questions locales";
$textes_langues["debut_envoi_examens_locaux"]="début envoi examens locaux";
$textes_langues["fin_envoi_examens_locaux"]="fin envoi examens locaux";


$textes_langues["debut_de"]="début de %s";
$textes_langues["fin_de"]="fin de %s";

$textes_langues['bareme_c2i']='Barème C2I';
$textes_langues['description_bareme_c2i']='Barème C2I';

$textes_langues['algo_tirage_1']='avec équilibrage par domaine';
$textes_langues['algo_tirage_2']='avec équilibrage par compétence';
$textes_langues['algo_tirage_3']='avec équilibrage par thème';


$textes_langues["maintenance"] = "mode maintenance";
$textes_langues["texte_mode_maintenance"]=<<<EOF
La plate-forme est actuellement en mode maintenance <br/>
Seuls les utilisateurs ayant un rôle administrateur peuvent s'y connecter.
EOF;

$textes_langues['activer_maintenance']='Activer le mode maintenance de la plate-forme';
$textes_langues['desactiver_maintenance']='Désactiver le mode maintenance de la plate-forme';

$textes_langues["info_maintenance_on"]=<<<EOF
En activant le mode maintenance, vous allez interdire à tous les utilisateurs de se connecter
à la plate-forme. Seuls les administrateurs pourront continuer à travailler normalement. <br/>
Vous pouvez créer ci-dessus un message d''information qui sera affiché aux utilisateurs, par exemple
pour leur donner une date de remise à disponibilité.
EOF;

$textes_langues["info_maintenance_off"]=<<<EOF
La plate-forme est actuellement en mode maintenance. Pour autoriser à nouveau les connexions, cliquez sur le bouton OK.
EOF;
