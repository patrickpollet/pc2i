<?php

/**
 *  @author Patrick Pollet
 * @version $Id: liste.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
*/

////////////////////////////////
//
//	liste des personnes et gestion
//
////////////////////////////////


$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres

require_login('P'); //PP


//parametres attendus
$idq=optional_param("idq",$USER->id_etab_perso,PARAM_INT); //etablissement
$profil=optional_param("profil","",PARAM_INT);  //critere de recherche

//$indice_deb=optional_param("indice_deb",0,PARAM_INT);

$tri=optional_param("tri","",PARAM_INT);  //critere de tri
//$supp_id=optional_param("supp_id","",PARAM_CLEAN); // suppression

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_CLEAN); //atention un login pas un id 
}

$refresh=optional_param("refresh",0,PARAM_INT); //rafraichir aussi la liste  sous-jacente qui a ouvert le popup

v_d_o_d("ul");

$ligne=get_etablissement($idq);

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //créer une instance

//inclure d'autre block de templates
$liste=<<<EOL

<!-- START BLOCK : rafraichi_liste -->
<script type="text/javascript">
        if (window.opener)
            window.opener.location.href=window.opener.location.href;
</script>

<!-- END BLOCK : rafraichi_liste -->

<div id="criteres">
<form name="form_criteres" id="form_criteres" method="post" action="liste.php?idq={idq}">
   {select_profil} {boutons_criteres}

<input name="idq" type="hidden" value="{idq}"/>
<input name="tri" type="hidden" value="{tri}"/>
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->
</form>

</div>

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
            <th class="bg"><a href="{url_id}"     title="{alt_tri}">{t_id}</a>{tri_id}</th>
            <th class="bg"><a href="{url_nom}"  title="{alt_tri}">{t_nom}</a>{tri_nom}</th>
            <th class="bg"><a href="{url_auth}"  title="{alt_tri}">{t_auth}</a>{tri_auth}</th>

  <th class="bg" style="width:150px;">{t_actions}</th>
<!-- START BLOCK : col_c -->
            <th width="40" class="bg"  >{t_consult}</th>
<!-- END BLOCK : col_c -->

<!-- START BLOCK : col_m -->
            <th width="40" class="bg">{t_modif}</th>
<!-- END BLOCK : col_m -->

<!-- START BLOCK : col_s -->
            <th width="40" class="bg">{t_supp}</th>
<!-- END BLOCK : col_s -->
</tr>
</thead>
<tbody>
<!-- START BLOCK : ligne -->
          <tr class="{paire_impaire}">
            <td>{id}</td>
            <td>{nom}</td>
            <td>{auth}</td>

<!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->
<!-- INCLUDE BLOCK : icones_action_liste -->

          </tr>
<!-- END BLOCK : ligne -->

<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="6">
		{msg_pas_de_personnel}
</td>
</tr>

<!-- END BLOCK : no_results -->
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
$tpl->assign("titre_popup", traduction("liste_personnels") . "<br/>" . ucfirst($ligne->nom_etab));

////////////////////////////////////////////////
//affichage des entêtes de colonnes selon droits


//$tpl->newBlock("col_c");
$peutModifier = a_capacite("um", $idq);   //pour cet etablissement
//if ($peutModifier)
//	$tpl->newBlock("col_m");

$peutSupprimer= a_capacite("us", $idq);   //pour cet etablissement
if ($peutSupprimer) {
	//$tpl->newBlock("col_s");
	// v 1.41 suppression ici
	/**********************************
	if ($supp_id) {
	    supprime_utilisateur($supp_id);
        $refresh=1;
	}
	***********************************/
	if ($action=="supprimer") { 
    	supprime_utilisateur ($id_action);
    	$refresh=1;
    	
	}	

}

$peutAjouter=a_capacite("ua", $idq);   //pour cet etablissement


require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// critères de recherche
////////////////////////////////////////////////////
$autre_table = "";
//important limiter a la composante courante
$chaine_critere_recherche = "";
$critere_recherche = "U.etablissement=".$idq;
if ($profil) {
		$critere_recherche .= " and D.id_profil=" . $profil . " and U.login=D.login";
		$autre_table = ",{$CFG->prefix}droits D";
        $chaine_critere_recherche= concatAvecSeparateur($chaine_critere_recherche,"profil=" . $profil,"&amp;");
}

/////////////////////////////////////////////
// critères de tri
/////////////////////////////////////////////
$url = "liste.php?idq=" . $idq;
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "&amp;");


$trieuse = new trieuse($tpl, "", $url , $tri);
$trieuse->addColonne("id","U.login");
$trieuse->addColonne("nom","U.nom");
$trieuse->addColonne("auth","U.auth");

$trieuse->setTriDefaut("nom", true);

$critere_tri = $trieuse->getCritereSQL();
$trieuse->printToScreen();  //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//important !
$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&");


///////////////////////////////////////////////
// gestion des retours vers cette page à partir d'une popup sans perte des critères
$url_retour = concatAvecSeparateur($chaine_critere_recherche,"idq=".$idq,'&amp;');  //important ici 
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&amp;");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');

$url_retour=urlencode($url_retour);
///////////////////////////////////////////////


////////////////////////////////////////////////////
// requete de sélection des personnes
////////////////////////////////////////////////////

$res=get_records("utilisateurs U" . $autre_table ,$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);

$nbTotal=count_records("utilisateurs","etablissement=".$idq );

$indice_max=count_records ("utilisateurs U" . $autre_table ,$critere_recherche,1);

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal." ".traduction ("personnels"));

$compteur_ligne = 0;
foreach ($res as $ligne) {
	$tpl->newBlock("ligne");
	$tpl->setCouleurLigne($compteur_ligne);

	$tpl->assign("id", $ligne->login);
	$tpl->assign("nom",cree_lien_mailto($ligne->email,get_fullname($ligne->login)));
	$tpl->assign("auth",$ligne->auth);

/**
	$tpl->newBlock("icones_action_liste");
    // si droit de consulter // à gérer
	$tpl->newblockNum("td_consulter_oui",$compteur_ligne);
	$tpl->assignURL("url_consulter","fiche.php?id=" . $ligne->login . "&amp;ide=" . $idq);

	// si droit de modifier
	if ($peutModifier) {
		$tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
		$tpl->assignURL("url_modifier", "ajout.php?id=" . $ligne->login . "&amp;ide=" . $idq."&amp;url_retour=" . $url_retour);
	}


	// si droit de supprimer // à gérer
    if ($peutSupprimer) {
		if ($USER->id_user != $ligne->login) {  //rev 1.41
			$tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
			$tpl->assign("js_supp", traduction("js_personnel_supprimer_0") . " " . addslashes($ligne->login) . " " .
			traduction("js_personnel_supprimer_1"));
			$tpl->assignURL("url_supprimer", "liste.php?supp_id=" . $ligne->login . "&amp;idq=" . $idq . "&amp;" . urldecode($url_retour));
		}   else $tpl->newBlock("td_supprimer_non");
	}
**/

  	$items=array();
    $items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq)");
    
    if ($peutModifier) {
		$items[]=new icone_action('modifier',"modifierItem('{$ligne->login}',$idq)");
	}
	
	 if ($peutSupprimer) {
		if ($USER->id_user != $ligne->login) {  //rev 1.41
			$items[]=new icone_action('supprimer',"supprimerItem('{$ligne->login}',$idq)");
		}   else $items[]=new icone_action();
	}
    
     
    $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);

	// passage à la ligne suivante
	$compteur_ligne++;
}
if ($compteur_ligne==0){
	$tpl->newBlock("no_results");
}


// génération des listes déroulantes
$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl);
$tpl->assign("idq",$idq);
$tpl->assign("tri",$tri);
print_select_from_table($tpl,"select_profil",get_profils(),"profil",null,"","id_profil","intitule",traduction ("profils"),$profil);


$items=array();
if ($peutAjouter)
	$items[]=new icone_action('nouveau',"nouvelItem($idq)",'nouveau_personnel');

$items[]=get_menu_item_criteres();
if ($chaine_critere_recherche)
    $items[]=get_menu_item_tout_afficher();

$items[]=get_menu_item_legende("personnel");

print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();

//rev 962
if ($refresh)  {  //retour d'une opération modifiant cette liste  (inscriptions, pools ...)
        $tpl->newBlock("rafraichi_liste");
}

$tpl->printToScreen(); //affichage
?>