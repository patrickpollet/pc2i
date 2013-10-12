<?php


/**
 * @author Patrick Pollet
 * @version $Id: configuration.php 964 2009-11-05 18:13:54Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

/**
 * rev 1.51 retour a la config LDAP de base dans c2ietablissement
 * les parametres envoy�s a config_m doivent �tre exactement les noms des colonnes de la BD
 */

////////////////////////////////
//
//	Page de la configuration
//
////////////////////////////////
/*----------------REVISIONS----------------------
v 1.1 : PP 17/10/2006
           ajout des trois attributs LDAP pour les membres d'un groupe et l'id unique
-----------------------------------------------*/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_once ($chemin_commun . "/lib_ldap.php");
require_once ($chemin_commun . "/lib_mail.php");
$repare_xml = optional_param("repare_xml", 0, PARAM_INT);
require_login('P'); //PP
v_d_o_d("config");


$page =<<<EOP
<div id="menu2">
{menu_niveau2}
<p/>
</div>

<div>
{ici}
</div>
EOP;

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
require_once ($CFG->chemin_commun . "/pear/HTML_TreeMenu/TreeMenu.php");

$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $page, T_BYVAR); // le template g�rant la configuration


if ($CFG->universite_serveur==1) {
    require_once("$chemin/codes/nationale/config/config_defs.php");
}


$tpl->prepare($chemin);

/////////////////////////
// affichage du menu
print_menu_haut($tpl, "c");

add_javascript($tpl, $CFG->chemin_commun . "/pear//HTML_TreeMenu/TreeMenu.js");


$ligne = get_etablissement($USER->id_etab_perso); //fatale si inconnu ...


/**
 * cree un lien vers un popup
 */
function cree_item_lien($texte, $url, $mini = false) {
	global $CFG;
	if ($mini) {
		$w = $CFG->largeur_minipopups;
		$h = $CFG->hauteur_minipopups;
		$action='doMiniPopup';

	} else {
		$w = $CFG->largeur_popups;
		$h = $CFG->hauteur_popups;
		$action='doPopup';
	}
	$modifier = traduction('alt_modifier');
	if ($url) {
		$html =<<<EOH
			<a href="#" title='$modifier' onclick="openPopup('$url','',$w,$h)">$texte</a>
EOH;
		$html2 =<<<EOH
			<a href="#" title='$modifier' onclick="$action('$url')">$texte</a>
EOH;
	}
	else
		$html = $html2=$texte;
	return $node = & new HTML_TreeNode(array (
		'text' => $html2,
		'icon' => 'ii_config.gif'
	));
}


/**
 * rev 1023
 * cree un lien vers un script sans popup
 * exemple script envoyant ensuite des fichiers xml ou ods ...
 * requis avec IE qui n'accepte pas qu'un popup se referme tout seul avec
 * redirection vers un lien de t�lechargement
 */
function cree_item_lien_direct($texte, $url) {
    global $CFG;
       $modifier = traduction('alt_modifier');
    if ($url)
        $html =<<<EOH
            <a href="$url" title='$modifier'>$texte</a>
EOH;
    else
        $html = $texte;
    return $node = & new HTML_TreeNode(array (
        'text' => $html,
        'icon' => 'ii_config.gif'
    ));
}





function cree_sous_menu($texte, $expanded = false) {
	return $node = & new HTML_TreeNode(array (
		'text' => $texte,
		'icon' => 'ii_dossier.gif',
		'expanded' => $expanded
	));
}

function cree_item_input($texte, $defaut, $url) {
	$texte = $texte . " : <b>$defaut</b>";
	return cree_item_lien($texte, $url, true);
}

function construit_menu($ligne, $noderef) {
	global $CFG, $USER;

	$node = & $noderef->addItem(cree_sous_menu(traduction('telechargement')));

	if ($CFG->universite_serveur == 1) {

		maj_defs_telechargements($node);
	} else {

		// 19/05/2009 un admin d'une composante ne PEUT pas synchroniser toute la plateforeme
		if (a_capacite("bddt", $USER->id_etab_perso) && $ligne->pere == 1) {
			$node->addItem(cree_item_lien(traduction('telechargement_bdd'), $CFG->chemin . "/codes/locale/synchro_nationale.php?"));
			$node->addItem(cree_item_lien(traduction('remontee_questions'), $CFG->chemin . "/codes/locale/remontee_questions.php?"));
			$node->addItem(cree_item_lien(traduction('remontee_examens'), $CFG->chemin . "/codes/locale/remontee_examens.php?"));
		}
		if (is_admin($USER->id_user, $CFG->universite_serveur)) {
			$node->addItem(cree_item_lien_direct(traduction('export_xml_moodle'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?"));
            $node->addItem(cree_item_lien_direct(traduction('export_xml_moodle_20'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?moodleversion=20"));
          //   $node->addItem(cree_item_lien_direct(traduction('export_xml_moodle_21'), $CFG->chemin . "/codes/questions/export_xml_moodle.php?moodleversion=21"));

            $node->addItem(cree_item_lien_direct(traduction('export_referentiel_xml'), $CFG->chemin . "/codes/questions/export_referentiel_xml.php?"));

            $node->addItem(cree_item_lien_direct(traduction('export_referentiel_xml_moodle'), $CFG->chemin . "/codes/questions/export_referentiel_xml_moodle.php?"));
	 		$node->addItem(cree_item_lien_direct(traduction('export_referentiel_objectifs_moodle'), $CFG->chemin . "/codes/questions/export_referentiel_objectifs_moodle.php?"));

		}
	}

	$node = & $noderef->addItem(cree_sous_menu(traduction('parametres')));

	$node->addItem(cree_item_input(traduction('config_param_nb_items'), $ligne->param_nb_items, 'config_m.php?elt=param_nb_items'));

	//rev 944 le nombre de refrentiels n'est pas toujours 9 !!!
	$refs = get_referentiels();
	$node->addItem(cree_item_input(traduction("config_param_nb_aleatoire", true, count($refs)), $ligne->param_nb_aleatoire, 'config_m.php?elt=param_nb_aleatoire'));

	$node->addItem(cree_item_input(traduction('config_param_nb_experts') . ' (0 ' . traduction('0expert', false) . ')', $ligne->param_nb_experts, 'config_m.php?elt=param_nb_experts'));

	$node->addItem(cree_item_input(traduction('config_param_langue'), $ligne->param_langue, false));

	//admin de cet etablissement pas d'une composante
	if (is_admin($USER->id_user, $CFG->universite_serveur)) {
		$node->addItem(cree_item_input(traduction("config_chemin_ressources"), $CFG->chemin_ressources, 'config_m.php?elt=CFG_chemin_ressources'));

	}

	// tracking  et autres permis tout admin

	if (is_admin()) {

		$node = & $noderef->addItem(cree_sous_menu(traduction('tracking')));

		$node->addItem(cree_item_lien(traduction('tracking'), "tracking/liste.php?"));
		// rev 911 statistiques  d�plac�e page questions
		$node->addItem(cree_item_lien(traduction('statistiques'), "statistiques/liste.php?"));

		//admin de cet etablissement pas d'une composante
		if (is_admin($USER->id_user, $CFG->universite_serveur)) {
			$node->addItem(cree_item_lien(traduction('purger_tracking'), "tracking/choix_purger2.php?", true));
		}
		// on ne purge que les siens !!!donc Ok pour une composante
		$node->addItem(cree_item_lien(traduction('purger_candidats'), "effacer_candidats.php?"));

	}

	//admin de cet etablissement pas d'une composante
	if ( is_admin($USER->id_user, $CFG->universite_serveur)) {

		$node = & $noderef->addItem(cree_sous_menu(traduction('configuration_avancee')));
		
		if (!empty($CFG->mode_maintenance))
		    $node->addItem(cree_item_lien(traduction('desactiver_maintenance'), "maintenance.php?"));
		else
		    $node->addItem(cree_item_lien(traduction('activer_maintenance'), "maintenance.php?"));
		 
		
		$node->addItem(cree_item_lien(traduction('configuration_avancee'), "config_avancee2.php?"));
		if ($CFG->restrictions_ip)
		    $node->addItem(cree_item_lien(traduction('restrictions_ips'), "ips/liste.php?"));	
        $node->addItem(cree_item_lien(traduction('info_php'), "infophp.php?"));
		if ($CFG->universite_serveur == 1 ) {
			maj_defs_avancees($node);
		}
	}

	//PP v 1.51 on met maintenant dans ce block
	// si un parametre LDAP existe on le tient ouvert
	if (isset ($ligne->param_ldap) && !empty ($ligne->base_ldap))
		$node = & $noderef->addItem(cree_sous_menu(traduction('ldap'), true));
	else
		$node = & $noderef->addItem(cree_sous_menu(traduction('ldap'), false));

	$node->addItem(cree_item_input(traduction('config_param_ldap'), $ligne->param_ldap, "config_m.php?elt=param_ldap"));

	$node->addItem(cree_item_input(traduction('config_base_ldap'), $ligne->base_ldap, "config_m.php?elt=base_ldap"));
	$node->addItem(cree_item_input(traduction('config_rdn_ldap'), $ligne->rdn_ldap, "config_m.php?elt=rdn_ldap"));
	$node->addItem(cree_item_input(traduction('config_passe_ldap'), $ligne->passe_ldap, "config_m.php?elt=passe_ldap"));

	$node->addItem(cree_item_input(traduction('config_ldap_version'), $ligne->ldap_version, "config_m.php?elt=ldap_version"));

    $annuaire=get_ldap_annuaire_courant($ligne->ldap_user_type);
    $node->addItem(cree_item_input(traduction('config_ldap_user_type'), $annuaire, "config_m.php?elt=ldap_user_type"));

	$node->addItem(cree_item_input(traduction('config_ldap_group_class'), $ligne->ldap_group_class, "config_m.php?elt=ldap_group_class"));
	$node->addItem(cree_item_input(traduction('config_ldap_group_attribute'), $ligne->ldap_group_attribute, "config_m.php?elt=ldap_group_attribute"));
	$node->addItem(cree_item_input(traduction('config_ldap_id_attribute'), $ligne->ldap_id_attribute, "config_m.php?elt=ldap_id_attribute"));
	$node->addItem(cree_item_input(traduction('config_ldap_login_attribute'), $ligne->ldap_login_attribute, "config_m.php?elt=ldap_login_attribute"));
	$node->addItem(cree_item_input(traduction('config_ldap_nom_attribute'), $ligne->ldap_nom_attribute, "config_m.php?elt=ldap_nom_attribute"));
	$node->addItem(cree_item_input(traduction('config_ldap_prenom_attribute'), $ligne->ldap_prenom_attribute, "config_m.php?elt=ldap_prenom_attribute"));
	$node->addItem(cree_item_input(traduction('config_ldap_mail_attribute'), $ligne->ldap_mail_attribute, "config_m.php?elt=ldap_mail_attribute"));

	$node->addItem(cree_item_input(traduction('config_ldap_champs_recherche'),get_champs_recherche_ldap($USER->id_etab_perso,true)
	           ,$CFG->chemin."/codes/ldap/config_m.php?elt=ldap_champs_recherche"));



}

$tpl->gotoBlock("_ROOT");

$items = array ();

$items[] = get_menu_item_legende("configuration");
print_menu($tpl, "_ROOT.menu_niveau2", $items);

if ($repare_xml) {
	require_once ($CFG->chemin_commun . "/lib_xml.php");
	repare_xmls();
	//ce block est dans c2ifooter.tpl
	$tpl->newBlock("alert_js");
	$tpl->assign("message", traduction("js_reparation_xml_termine") . " " . $CFG->chemin_ressources);
}

$menu = & new HTML_TreeMenu();
$noderef = & new HTML_TreeNode(array (
	'text' => clean($ligne->nom_etab),
	'icon' => 'ii_etab.gif',
	'expandedIcon' => 'ii_etab.gif',
	'expanded' => true
));
$menu->addItem($noderef);

construit_menu($ligne, $noderef);

$treeMenu = & new HTML_TreeMenu_DHTML($menu, array (
	'images' => $CFG->chemin_theme . "/images/treemenu",
	'defaultClass' => 'treeMenuDefault'
));

$tpl->assign("_ROOT.ici", $treeMenu->toHTML());

$tpl->printToScreen(); //affichage
flush();
?>
