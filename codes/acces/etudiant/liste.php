<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1231 2011-03-25 15:24:21Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */


////////////////////////////////
//
//	liste des examens et accès à leur gestion
//
////////////////////////////////



/*
* Pour la description des différentes méthodes de la classe TemplatePower,
* il faut se reférer à http://templatepower.codocad.com/
*/
//******** Pour chaque page $chemin représente le path(chemin) de script dans le site (à la racine)
//******** ---------------- $chemin_commun représente le path des utilitaires dont on aura besoin
//******** ---------------- $chemin_images représente le path des images
$chemin = '../../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de paramètres
require_login("P"); //PP



//parametres attendus
$idq=optional_param("idq",$USER->id_etab_perso,PARAM_INT); //etablissement concerné
$examen=optional_param("examen","",PARAM_CLE_C2I);  //critere de recherche (num.num)

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


v_d_o_d("etl");
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
       {select_examen} {boutons_criteres}

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
             <th class="bg"><a href="{url_numetudiant}"  title="{alt_tri}">{t_numetud}</a>{tri_numetudiant}</th>
            <th class="bg"><a href="{url_auth}"  title="{alt_tri}">{t_auth}</a>{tri_auth}</th>
            <th  class="bg">{t_examen}</th>
  <th class="bg" style="width:150px;">{t_actions}</th>
<!-- START BLOCK : col_c -->
            <th width="40" class="bg">{t_consult}</th>
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
          <tr  class="{paire_impaire}">
            <td>{id}</td>
            <td>{nom}</td>
              <td>{numetudiant}</td>
             <td>{auth}</td>
            <td>
            <!-- START BLOCK : examen_p -->
            {examen}<br />
            <!-- END BLOCK : examen_p -->
            <!-- START BLOCK : autre_pf -->
              <div class="commentaire1">
              {aussi_inscrit}
              </div>
            <!-- END BLOCK : autre_pf -->

            </td>
            
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
<td colspan ="{colspan}">
		{msg_pas_de_inscrits}
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

$tpl->assign("_ROOT.titre_popup", traduction("liste_etudiants") . "<br/>" . ucfirst($ligne->nom_etab));

////////////////////////////////////////////////
//affichage des entêtes de colonnes selon droits
$colspan=6;
//$tpl->newBlock("col_c");
$peutModifier = a_capacite("etm", $idq);   //pour cet etablissement

//if ($peutModifier) {
//    $tpl->newBlock("col_m"); $colspan++;
//}
$peutSupprimer= a_capacite("ets",$idq);
if ($peutSupprimer) {
    /******************************
    $tpl->newBlock("col_s");
    $colspan++;
    // v 1.41 suppressions ici
    if ($supp_id) { //
      supprime_candidat($supp_id);
      $refresh=1;
    }
    ******************************/
    if ($action=="supprimer") { 
    	supprime_candidat ($id_action);
    	$refresh=1;   	
	}	   
}

$peutAjouter=a_capacite("eta",$idq);

require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

////////////////////////////////////////////////////
// critères de recherche
////////////////////////////////////////////////////
$autre_table = "";
$chaine_critere_recherche = "";

 //important !!!
 //filtrer par établissements/composantes
$critere_recherche = "I.etablissement=".$idq. " and not I.login like 'ANONYME%'";
if ($examen) {
	$ex = explode(".", $examen);
		$critere_recherche .= " and Q.id_examen=" . $ex[1] . " and Q.id_etab='" . $ex[0] . "' and I.login=Q.login";
		$autre_table .= ",{$CFG->prefix}qcm Q";
		$chaine_critere_recherche =concatAvecSeparateur($chaine_critere_recherche, "examen=" . $examen,"&amp;");
}

/////////////////////////////////////////////
// critères de tri
/////////////////////////////////////////////
$url = "liste.php?idq=" . $idq;
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "&amp;");

$trieuse = new trieuse($tpl, "", $url , $tri);
$trieuse->addColonne("id","I.login");
$trieuse->addColonne("nom","I.nom");
$trieuse->addColonne("numetudiant","I.numetudiant");
$trieuse->addColonne("auth","I.auth");

$trieuse->setTriDefaut("nom", true);

$critere_tri = $trieuse->getCritereSQL();
$trieuse->printToScreen();  //entetes

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//important !
$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");



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
// requete de sélection des etudiants à afficher
////////////////////////////////////////////////////
$res=get_records("inscrits I" . $autre_table ,$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);

//$nbTotal=count_records("inscrits","etablissement=".$idq );
$nbTotal=count_candidats($idq );

$indice_max=count_records ("inscrits I" . $autre_table ,$critere_recherche,1);

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal." ".traduction ("etudiants"));


$compteur_ligne = 0;
foreach($res as $ligne) {

	//rev 1077 ne pas encombrer la liste avec eux
	if (is_utilisateur_anonyme($ligne->login)) continue;

	$tpl->newBlock("ligne");
	$tpl->setCouleurLigne($compteur_ligne);

	$tpl->assign("id", $ligne->login);
    $tpl->assign("auth", $ligne->auth);
  $tpl->assign("numetudiant", $ligne->numetudiant); // rev 944
	$tpl->assign("nom",cree_lien_mailto($ligne->email,get_fullname($ligne->login)));

    $exams=get_examens_inscrits($ligne->login,'nom_examen');
	foreach ($exams as $ligne_ex) {
		$tpl->newBlock("examen_p");
		$tpl->assign("examen", $ligne_ex->id." : ".$ligne_ex->nom_examen);
	}
    $nbe=est_inscrit_examen(false,false,$ligne->login); //nombre examens (toute PF !)
    if ($nbe>count($exams)) {  //inscrit aussi autre PF  rev 843 explique pourquoi pas de poubelle
        $tpl->newBlock("autre_pf");
        if ($USER->type_plateforme=="positionnement")
            $autre_pf=traduction("certification");
         else
            $autre_pf=traduction ("positionnement");
        $tpl->assign ("aussi_inscrit",traduction ("msg_aussi_inscrit",false, $nbe-count($exams),$autre_pf));

    }
/***************************************************
    $tpl->newBlock("icones_action_liste");
	$tpl->newblockNum("td_consulter_oui",$compteur_ligne);
	$tpl->assignURL("url_consulter","fiche.php?id=" . $ligne->login . "&amp;ide=" . $idq);

	// si droit de modifier// à gérer
	if ($peutModifier)
	 if  ( !is_utilisateur_anonyme($ligne->login)) { // rev 834
		$tpl->newBlockNum("td_modifier_oui",$compteur_ligne);
		$tpl->assignURL("url_modifier", "ajout.php?id=" . $ligne->login . "&amp;ide=" . $idq."&amp;url_retour=" . $url_retour);
	} else $tpl->newBlock("td_modifier_non");

	// si droit de supprimer // à gérer
	if ($peutSupprimer) {
		// rev 1.41 pas si a passé un exam
		if (is_utilisateur_anonyme($ligne->login) //rev 834
		   || $nbe==0 ) {
			$tpl->newBlockNum("td_supprimer_oui",$compteur_ligne);
            //le addslashe a été ajouté pour pouvoir virer des comptes mal importés en V < 1.5 (guillemets prseents !)
			$tpl->assign("js_supp",traduction("js_etudiant_supprimer_0") . " " . addslashes($ligne->login) . " " .
			traduction("js_etudiant_supprimer_1"));
			$tpl->assignURL("url_supprimer", "liste.php?supp_id=" . $ligne->login . "&amp;idq=" . $idq . "&amp;" . urldecode($url_retour));
		}   else $tpl->newBlock("td_supprimer_non");
	}
*****************************************************/

  	$items=array();
    $items[]=new icone_action('consulter',"consulterItem('{$ligne->login}',$idq)");
    
    if ($peutModifier) {
    	 if  ( !is_utilisateur_anonyme($ligne->login)) { // rev 834
			$items[]=new icone_action('modifier',"modifierItem('{$ligne->login}',$idq)");
    	 } else $items[]=new icone_action();	
	}
	
	 if ($peutSupprimer) {
		if (is_utilisateur_anonyme($ligne->login) //rev 834
		   || $nbe==0 ) {
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
	$tpl->assign("colspan",$colspan);
}
///////////////////////////////////
//
//	gestions de l'affichage des critères de recherche
//
///////////////////////////////////
$tpl->gotoBlock("_ROOT");
print_boutons_criteres($tpl);


$tpl->assign("idq",$idq);
$tpl->assign("tri",$tri);
print_select_from_table($tpl,"select_examen",get_examens($idq),
                             "examen",null,"","id","nom_examen",traduction ("examens"),$examen);

$tpl->gotoBlock("_ROOT");


$items=array();
if ($CFG->peut_creer_compte_inscrit && $peutAjouter)
    /***
	$items[]=array('action'=>'nouveau',
                   'url'=> "ajout.php?ide=" . $idq . "&amp;url_retour=" . $url_retour,
                   'texte'=>'nouveau_candidat');
     ***/
     $items[]=new icone_action('nouveau',"nouvelItem($idq)",'nouveau_candidat');              
                   
$items[]=get_menu_item_criteres();
if ($chaine_critere_recherche)
	$items[]=get_menu_item_tout_afficher();
$items[]=get_menu_item_legende("etudiant");
print_menu($tpl,"_ROOT.menu_niveau2",$items);

$tpl->print_boutons_fermeture();

// rev 962
if ($refresh)  {  //retour d'une opération modifiant cette liste  (inscriptions, pools ...)
        $tpl->newBlock("rafraichi_liste");
}


$tpl->printToScreen(); //affichage
?>

