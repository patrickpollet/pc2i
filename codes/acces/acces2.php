<?php


/**
 * @author Patrick Pollet
 * @version $Id: acces.php 988 2009-12-05 17:09:32Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	Page d'accueil des acc�s
//
////////////////////////////////
/**
 * rev 1.5 les suppressions de profils et de composantes sont g�r�s ici
 * plus de scripts de type supprime.php qui reviennent ici
 */

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
require_login('P'); //PP

//$supp_idp = optional_param("supp_idp", "", PARAM_INT);
//$supp_comp = optional_param("supp_comp", 0, PARAM_INT);

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_INT); // un simple id num�rique
}

if (!a_capacite("etl") && !a_capacite("ul")) // au moins une des 2
	erreur_fatale("err_droits");

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
require_once ($CFG->chemin_commun . "/pear/HTML_TreeMenu/TreeMenu.php");

$page =<<<EOP

<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->

{form_actions}

<table width='100%'>
<tr>

<!-- START BLOCK : profils -->
<td width='50%'>

{menu_niveau2_p}
<p/>
{ici_profils}
</td>

<!-- END BLOCK : profils -->

<!-- START BLOCK : etablissements -->
<td width='50%'>

{menu_niveau2_e}
<p/>
{ici_etablissements}
</td>
<!-- END BLOCK : etablissements -->
</tr>
</table>

EOP;

$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates
$tpl->assignInclude("corps", $page, T_BYVAR); // le template g�rant les universit�s et profils
$tpl->prepare($chemin);

add_javascript($tpl, $CFG->chemin_commun . "/pear//HTML_TreeMenu/TreeMenu.js");

/////////////////////////
// affichage du menu

print_menu_haut($tpl, "a");

$tpl->gotoBlock("_ROOT");
// rev 981 form pour les actions js avec retour ici (et pas liste.php ajout� par d�faut par le moetur de tpltes
// important de le faire ici (avant la cr�ation des deux blocks)

print_form_actions ($tpl,'form_actions','','acces2.php');


// V 1.5 21/05/2009
$etab = get_etablissement($USER->id_etab_perso); //fatale si inconnu ...

// lignes de profil

if (teste_droit("ul")) {
	$tpl->newBlock("profils");

	$items = array ();
	// v 1.41 suppression de profil ici  (avant de les balayer !)
	$peutSupprimerProfil = false;
	if (is_admin(false, $CFG->universite_serveur)) { // rev 821 pas une composante !!!!
		$items[]=new icone_action('nouveau',"nouveauProfil()",'nouveau_profil');
		$peutSupprimerProfil = $etab->pere <= 1; //pas un admin de compoante !
		if ($peutSupprimerProfil && $action=="supprimer_profil")
			supprime_profil($id_action);
	}

	$items[] = get_menu_item_legende("acces");
	print_menu($tpl, "menu_niveau2_p", $items);

	$profils = get_profils('intitule', false);

	$menu = & new HTML_TreeMenu();
	$noderef = & new HTML_TreeNode(array (
		'text' => traduction("profils"),
		'icon' => 'ii_etab.gif',
		'expandedIcon' => 'ii_etab.gif',
		'expanded' => true
	));
	$menu->addItem($noderef);

	foreach ($profils as $ligne) {
		$nb = count_records("droits", "id_profil=" . clean($ligne->id_profil));

		$html = clean($ligne->intitule) . ' (' . $nb . ')';
		$html .=" <ul class='menu_niveau2'>";
		$html .=print_menu_item2(false,false,new icone_action('consulter',"consulterProfil({$ligne->id_profil})"));
		// si droit de modification
		if (is_admin(false, $CFG->universite_serveur)) { // rev 821
			 $html .=print_menu_item2(false,false,new icone_action('modifier',"modifierProfil({$ligne->id_profil})"));
		}
		// si droit de supprimer
		if ($nb == 0 && $peutSupprimerProfil) {
			 $html .=print_menu_item2(false,false,new icone_action('supprimer',"supprimerProfil({$ligne->id_profil},'{$ligne->intitule}')"));
		}
		$html.='</ul>';

		$node = & new HTML_TreeNode(array (
			'text' => clean ( $html,9999),
			'icon' => 'ii_profil.gif',
			'expanded' => true
		));
		$noderef->addItem($node);

	}
	$treeMenu = & new HTML_TreeMenu_DHTML($menu, array (
		'images' => $CFG->chemin_theme . "/images/treemenu",
		'defaultClass' => 'treeMenuDefault'
	));

	$tpl->assign("ici_profils", $treeMenu->toHTML());

}

// si droit de g�rer les etablissements (� g�rer)

if (teste_droit("etl")) {
	$tpl->newBlock("etablissements");

	// V 1.5 suppression de" composante ici (plus de script codes/etablissement/supprime.php qui revient ici)
	if ($action=='supprimer_etab') { //
		if (is_super_admin() || is_admin($USER->id_user, $id_action))
			supprime_etablissement($id_action, false); //test
	}

	$items = array ();
	if (is_super_admin()) {
		$items[]=new icone_action('nouveau',"nouvelEtab()",'nouvel_etablissement' );

	} else
        // rev 979 creation d'une sous-composante par un admin d'une composante (a tester !)
		//if (is_admin(false, $CFG->universite_serveur)) {
        if (is_admin(false, $USER->id_etab_perso)) {
			$items[]=new icone_action('nouveau',"nouvelEtab()",'nouvelle_composante');
		}

	print_menu($tpl, "menu_niveau2_e", $items);

	$menu = & new HTML_TreeMenu();
	$noderef = & new HTML_TreeNode(array (
		'text' => traduction("etablissements"),
		'icon' => 'ii_etab.gif',
		'expandedIcon' => 'ii_etab.gif',
		'expanded' => false
	));
	$menu->addItem($noderef);

	function construit_arbre($etab, $noderef) {

		global $CFG, $USER;

		$expanded = $etab->id_etab == $USER->id_etab_perso;

		$html = clean($etab->nom_etab);
		$html .=" <ul class='menu_niveau2'>";
		$html .=print_menu_item2(false,false,new icone_action('consulter',"consulterEtab({$etab->id_etab})"));

		// si droit de modification // � g�rer
		if (is_super_admin() || is_admin($USER->id_user, $etab->id_etab)) {
			$html .=print_menu_item2(false,false,new icone_action('modifier',"modifierEtab({$etab->id_etab})"));
		}

		if ((is_super_admin() || is_admin($USER->id_user, $etab->id_etab)) && etablissement_est_supprimable($etab->id_etab)) {
			//$etab->nom_etab=addslashes($etab->nom_etab);
			if ($etab->pere==1)
				$html .=print_menu_item2(false,false,new icone_action('supprimer',"supprimerEtab({$etab->id_etab},' {$etab->nom_etab} ' )"));
			else
				$html .=print_menu_item2(false,false,new icone_action('supprimer',"supprimerComp({$etab->id_etab},' {$etab->nom_etab} ' )"));
		}
		$html .=" </ul>";

		$node = & new HTML_TreeNode(array (
			'text' => clean( $html,9999),
			'icon' => 'ii_etab.gif',
			'expanded' => $expanded
		));
		$noderef->addItem($node);

		$html = traduction("personnels") . " (" . count_records("utilisateurs", 'etablissement=' . $etab->id_etab) . ")";
		$html.=print_menu_item2(false,false,new icone_action('consulter',"listePersonnels({$etab->id_etab})"));


		$nodep = & new HTML_TreeNode(array (
			'text' => clean( $html,9999),
			'icon' => 'ii_personnel.gif'
		));

		$node->addItem($nodep);


		$html = traduction("etudiants") . " (" . count_candidats($etab->id_etab) . ")";
		$html.=print_menu_item2(false,false,new icone_action('consulter',"listeCandidats({$etab->id_etab})"));

		$nodee = & new HTML_TreeNode(array (
			'text' => clean( $html,9999),
			'icon' => 'ii_etudiant.gif'
		));
		$node->addItem($nodee);

		if ($comps = get_composantes($etab->id_etab)) {
			foreach ($comps as $comp)
				construit_arbre($comp, $node);
		}

	}

	construit_arbre($etab, $noderef);

	$treeMenu = & new HTML_TreeMenu_DHTML($menu, array (
		'images' => $CFG->chemin_theme . "/images/treemenu",
		'defaultClass' => 'treeMenuDefault'
	));

	$tpl->assign("ici_etablissements", $treeMenu->toHTML());

}


$tpl->printToScreen(); //affichage
?>

