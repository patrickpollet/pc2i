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
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

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

$tpl = new C2IPopup(); //cr�er une instance

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
//affichage des ent�tes de colonnes selon droits


//$tpl->newBlock("col_c");
$peutModifier = a_capacite("um", $idq);   //pour cet etablissement
//if ($peutModifier)
//	$tpl->newBlock("col_m");

$peutSupprimer= a_capacite("us", $idq);   //pour cet etablissement
if ($peutSupprimer) {
	if ($action=="supprimer") { 
    	supprime_utilisateur ($id_action);
    	$refresh=1;
    	
	}	

}

$peutAjouter=a_capacite("ua", $idq);   //pour cet etablissement


require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// crit�res de recherche
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
// crit�res de tri
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
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
$url_retour = concatAvecSeparateur($chaine_critere_recherche,"idq=".$idq,'&amp;');  //important ici 
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&amp;");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');

$url_retour=urlencode($url_retour);
///////////////////////////////////////////////


////////////////////////////////////////////////////
// requete de s�lection des personnes
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

	// passage � la ligne suivante
	$compteur_ligne++;
}
if ($compteur_ligne==0){
	$tpl->newBlock("no_results");
}


// g�n�ration des listes d�roulantes
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
if ($refresh)  {  //retour d'une op�ration modifiant cette liste  (inscriptions, pools ...)
        $tpl->newBlock("rafraichi_liste");
}

$tpl->printToScreen(); //affichage
?>