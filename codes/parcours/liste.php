<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	liste des parcours actifs pour l'�tudiant connect�
//
////////////////////////////////

/*----------------REVISIONS----------------------
v 1.1 : PP 16/10/2006
      test via require_login("E")
      pas de loupe  si termin�
      Dans le cas d'un ENT, les liens Retour et Quitter n'ont pas de raison d'�tre ...
------------------------------------------------*/

/*
* Pour la description des diff�rentes m�thodes de la classe TemplatePower,
* il faut se ref�rer � http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin repr�sente le path(chemin) de script dans le site (� la racine)
//******** ---------------- $chemin_commun repr�sente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images repr�sente le path des images
$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";

require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("E"); //PP

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	$id_action=required_param('id_action',PARAM_INT);
}

$tri=optional_param("tri","",PARAM_INT);  //critere de tri

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPrincipale(); //cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL


<!-- INCLUDESCRIPT BLOCK : ./actions_js.php -->
<div id="menu2">
{menu_niveau2}
</div>
<div id="infos">
{nb_items}
</div>


<!-- INCLUDE BLOCK : multip_haut -->
      <table id="liste">
        <thead>
          <tr>
            <th class="bg"><a
            href="{url_id}" title="{alt_tri}">{t_id}</a>{tri_id}</th>
            <th class="bg"><a href="{url_titre}" title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
             <th class="bg"><a href="{url_login}"  title="{alt_tri}">{t_login}</a>{tri_login}</th>
             <th class="bg"><a href="{url_examen}" title="{alt_tri}">{t_examen}</a>{tri_examen}</th>
               <th class="bg"><a href="{url_type}" title="{alt_tri}">{t_type}</a>{tri_type}</th>
            <th class="bg"> <a href="{url_date}" title="{alt_tri}">{t_date}</a>{tri_date}</th>
			<th class="bg" style="width:150px;">{t_actions}</th>

</tr>
</thead>
<tbody>
            <!-- START BLOCK : ligne -->
          <tr  class="{paire_impaire}">
            <td>{id}</td>
            <td>{titre}</td>
            <td>{login} <ul style="display:inline;">{consulter_fiche}</ul></td>
             <td>{examen}</td>
               <td>{type}</td>
            <td>{date}</td>

<!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->

          </tr>
          <!-- END BLOCK : ligne -->
          <!-- START BLOCK : no_parcours -->
			<tr class="information">
				<td colspan ="{colspan}">
				{msg_pas_de_parcours}
				</td>
			</tr>

<!-- END BLOCK : no_parcours -->
        </tbody>
      </table>
<!-- INCLUDE BLOCK : multip -->

{form_actions}

EOL;


$options=array (
	"liste"=>1,
	"corps_byvar"=>$liste
);

$tpl->prepare($chemin,$options);



/////////////////////////
// affichage du menu menu

print_menu_haut($tpl,"parc");

$tpl->gotoBlock("_ROOT");

////////////////////////////////////////////////
//affichage des ent�tes de colonnes selon droits

$colspan=6;
// si droit de consulter // � g�rer
//$tpl->newblock("col_c");
if ($CFG->peut_dupliquer_parcours) {
	//$tpl->newBlock("col_d");
	//$colspan++;
}
//$tpl->newblock("col_m");
//$tpl->newBlock("col_s");

// v1.41 suppressions ici !
/***
if ($supp_id) {
	supprime_parcours($supp_id);
}
****/
if ($action=="supprimer")
    	supprime_parcours ($id_action);


require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// crit�res de recherche  aucun de visible
////////////////////////////////////////////////////
$chaine_critere_recherche = "";

$peutListerParcours=a_capacite("ul") || a_capacite("etl");
if ($peutListerParcours)
    $critere_recherche ='';
else 
    $critere_recherche = "login='".addslashes($USER->id_user)."'";  // rev 984
 
/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$url = "liste.php?" ;
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");


$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "id_parcours");
$trieuse->addColonne("titre", "titre");
$trieuse->addColonne("login", "login");
$trieuse->addColonne("examen", "examen");
$trieuse->addColonne("type", "type");
$trieuse->addColonne("date", "ts_datemodification");


$trieuse->setTriDefaut("date", false);

$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen();  //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//lien de mp : les deux (recherche et tri)

$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");
//lien de mp : les deux (recherche et tri)



// recherche du nombre de lignes
$indice_max = count_records("parcours","login='" .addslashes( $USER->id_user)."'");  // rev 984

$tpl->assign("_ROOT.nb_items", $indice_max . " " . traduction ("parcours"));

///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
$url_retour = $chaine_critere_recherche;
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,$trieuse->getParametreTri(),"&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');
$url_retour=urlencode($url_retour);

///////////////////////////////////////////////


////////////////////////////////////////////////////
// requete de s�lection des parcours � afficher
////////////////////////////////////////////////////

$lignes=get_records("parcours",$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);
$compteur_ligne = 0;
foreach ($lignes as $ligne) {

	$idnat=$ligne->id_parcours;

	$tpl->newblock("ligne",$compteur_ligne);
	$tpl->setCouleurLigne($compteur_ligne);

	$tpl->assign("id", $idnat);
	$tpl->assign("titre", $ligne->titre);
	$tpl->assign("login",$ligne->login);
	$tpl->assign("type",$ligne->type);
	$tpl->assign("examen",$ligne->examen);
	
	
	if ($ligne->login && $peutListerParcours) {
	    // rev 944 attention a bien choisir une fiche utilisateur ou candidat ...
	    if ($cpt=get_compte($ligne->login,false)) {
	        if ($cpt->type_user=='P')
	        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../acces/personnel/fiche.php?id=".$ligne->login));
	        else
	        print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../acces/etudiant/fiche.php?id=".$ligne->login));
	
	    }else $tpl->assign("consulter_fiche","");
	}
	else $tpl->assign("consulter_fiche","");
	


    $tpl->assign("date",userdate($ligne->ts_datemodification,'strftimedatetime'));

    $items=array();
    $items[]=new icone_action('consulter',"consulterItem('$idnat')");

	if ($CFG->peut_dupliquer_parcours) {
		$items[]=new icone_action('dupliquer',"dupliquerItem('$idnat')");
	}

	$items[]=new icone_action('modifier',"modifierItem('$idnat')");
	$items[]=new icone_action('supprimer',"supprimerItem('$idnat')" );

	$tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);


	// passage � la ligne suivante
	$compteur_ligne++;
}



if ($compteur_ligne==0) {
	$tpl->newBlock ("no_parcours");
	$tpl->assign("colspan",$colspan);

}

$tpl->gotoBlock("_ROOT");


$items=array();

$items[]=new icone_action('nouveau',"nouvelItem()",'nouveau_parcours');
$items[]=get_menu_item_legende("parcours");

print_menu($tpl,"_ROOT.menu_niveau2",$items);


$tpl->printToScreen(); //affichage
?>

