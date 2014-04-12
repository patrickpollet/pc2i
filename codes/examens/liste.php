<?php

/**
 * @author Patrick Pollet
 * @version $Id: liste.php 1312 2012-11-14 12:50:49Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	liste des examens et acc�s � leur gestion
//
////////////////////////////////
/*----------------REVISIONS----------------------
v 1.1 : SB 17/10/2006
- affichage du nom de l'universit� quand on passe sur une ligne (examen) du tableau
ajout d'une variable title_id dans le template examens.html

v 1.41 : PP 09/02/2009
- la gestion des retours des popups reactiv�es (url_retour)

------------------------------------------------*/

$chemin = '../..';
$chemin_commun = $chemin . "/commun";
$chemin_images = $chemin . "/images";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres
//requis pour que compte_passages fonctionne apres une migration !
require_once ($chemin_commun . "/lib_resultats.php"); //fichier de param�tres


require_login("P"); //PP
v_d_o_d("el"); //PP

$id_rech=optional_param("id_rech","",PARAM_CLE_C2I);
$titre_rech=optional_param("titre_rech","",PARAM_CLEAN);
$filtre_voir_ex_nat=optional_param("filtre_voir_ex_nat","0",PARAM_INT); //fini v 1.5
$filtre_univ=optional_param("filtre_univ","",PARAM_CLEAN);

//$indice_deb=optional_param("indice_deb",0,PARAM_INT);
// affichage par d�faut des �v�nements les plus r�cents
$tri=optional_param("tri","",PARAM_INT);

// rev 982 et suivante simplifiaction des liens emis sur les icones d'action'
$action=optional_param('action','',PARAM_ALPHA);
if ($action) {
	//print_r($_POST);
	$id_action=required_param('id_action',PARAM_CLE_C2I);
}

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

//$tpl = new C2IPrincipale(); //cr�er une instance
$tpl = new C2IPrincipale();
//inclure d'autre block de templates

$liste=<<<EOL
<div id="criteres">
<form id="form_criteres" method="post" action="liste.php">
<div>
            {select_filtre_univ}
             {t_id} : <input class="saisie" name="id_rech" size="5" value="{id_rech}"/>

            | {t_titre} : <input class="saisie" name="titre_rech" size="15" value="{titre_rech}"/>

           <!--
            &nbsp;| {voir_examens_masques}
            <input type="checkbox" name="filtre_voir_ex_nat" {voir_ex_nat_checked} value="1" class="saisie" />
           -->
            {boutons_criteres}

<input name="tri" type="hidden" value="{tri}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->
</div>
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
            <th class="bg"><a href="{url_titre}"  title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
            <th class="bg"><a href="{url_dateh}"  title="{alt_tri}">{t_dateh}</a>{tri_dateh}</th>
            <th class="bg"><a href="{url_auteur}" title="{alt_tri}">{t_auteur}</a>{tri_auteur}</th>
            <th class="bg"><a href="{url_date}"   title="{alt_tri}">{t_date}</a>{tri_date}</th>
            <th class="bg">{t_nbinscrits}</th>
   <th class="bg" style="width:150px;">{t_actions}</th>

<!-- START BLOCK : col_c -->
            <th class="bg icone_action">{t_consult}</th>
<!-- END BLOCK : col_c -->
<!-- START BLOCK : col_d -->
            <th class="bg icone_action">{t_dupl}</th>
<!-- END BLOCK : col_d -->
<!-- START BLOCK : col_m -->
            <th class="bg icone_action">{t_modif}</th>
<!-- END BLOCK : col_m -->
<!-- START BLOCK : col_s -->
            <th class="bg icone_action">{t_supp}</th>
<!-- END BLOCK : col_s -->
</tr>
</thead>
<tbody>
<!-- START BLOCK : ligne -->

          <tr title="{title_id}" class="{paire_impaire}">
            <td title="{title_id}">{id}{anonyme}</td>
            <td>{quest}</td>
            <td>{dateh}<br/><span class="commentaire1">{heures} </span>
            <br/>
            <img src="{chemin_images}/{image_dateh}.gif" alt="{alt_image_dateh}" title="{alt_image_dateh}" />
            </td>

            <td>{auteur}</td>
            <td>{date}
<!-- START BLOCK : image_nouv -->
            <br/>
            <img src="{chemin_images}/nouveau.gif" alt="{alt_nouveau}" title="{alt_nouveau}" />
<!-- END BLOCK : image_nouv -->
            </td>

<!-- START BLOCK : td_inscrits -->
                 <td>
        <!-- START BLOCK : inscrits_lien -->

                  <!-- START BLOCK : liste_inscrits -->
                 <a
                  href="#"
                  onclick="openPopup('{url_liste}','','{lp}','{hp}')">
                  <img src="{chemin_images}/i_liste.gif" alt="{alt_liste}"
                  title="{alt_liste}"  />
                  </a>
                  <!-- END BLOCK : liste_inscrits -->
                   {nb_inscrits}
         <!-- END BLOCK : inscrits_lien -->
                 </td>
<!-- END BLOCK : td_inscrits -->

<!-- START BLOCK : icones_actions -->
          <td>
          {icones_actions}
          </td>
<!-- END BLOCK : icones_actions -->

          </tr>

<!-- END BLOCK : ligne -->

<!-- START BLOCK : no_results -->
<tr class="information">
<td colspan ="{colspan}">
		{msg_pas_de_examen}
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

add_javascript($tpl,$CFG->chemin_commun."/js/sprintf.js");


print_menu_haut($tpl,"e");

$tpl->gotoBlock("_ROOT");

//affichage des ent�tes de colonnes selon droits
//$tpl->newblock("col_c");
$colspan=7;
// si droit de modifier
$peutModifier= a_capacite("em");
if ($peutModifier) {
    //$tpl->newblock("col_m");
   // $colspan++;
}
// si droit de supprimer
$peutSupprimer=a_capacite("es");
if ($peutSupprimer) {
	 //  $tpl->newblock("col_s");
    // v 1.41 suppression ici donc  retest du droit
    $supp_idq=optional_param("supp_idq","",PARAM_INT);
    $supp_ide=optional_param("supp_ide","",PARAM_INT);
    if ($supp_idq && $supp_ide) {
        supprime_examen($supp_idq, $supp_ide);
    }else
	    // rev 981
    	if ($action=="supprimer") {
    		$q=get_examen_byidnat($id_action); //controle existence
    		supprime_examen ($q->id_examen,$q->id_etab);
    	}
     //$colspan++;

}
// si droit de dupliquer
$peutDupliquer=$CFG->peut_dupliquer_examen && a_capacite("ed");
if ($peutDupliquer){
    //$tpl->newblock("col_d");
    //$colspan++;
}

////////////////////////////////////////////////////
// crit�res de recherche
////////////////////////////////////////////////////
/**
 * TODO
 * sur une locale, le filtre par etablissement  n'a aucun interet ?
 * 'sauf peut-�tre les composantes
 */

require_once ($CFG->chemin_commun . "/trieuse.class.php");
require_once ($CFG->chemin_commun . "/chercheuse.class.php");

$critere_id="";
if($id_rech){ //deja filtr� par optional_param
        $id0 = explode(".", $id_rech);
        if (sizeof($id0) == 2) {
            $critere_id .= "id_etab='" . $id0[0] . "' and id_examen='" . $id0[1] . "'";
        } else {
           print $id_rech;
        }
}

$chercheuse = new chercheuse($tpl, "");
// REV 977 SEULEMENT LES EXAMENS COMPATIBLES AVEC LA VERSION DU REFERENTIEL
//$chercheuse->addCritere("version_referentiel", "force", "version_referentiel=".$CFG->version_referentiel, "", $CFG->version_referentiel); //toujours vrai, mais force dans emis dans le SQL mais pas l'HTTP

$chercheuse->addCritere("titre_rech", "input", "nom_examen LIKE '%".$titre_rech."%'","",$titre_rech);
$chercheuse->addCritere("id_rech", "input", $critere_id,"",$id_rech);

$chercheuse->addCritere("filtre_univ", "select", "id_etab=$filtre_univ", "", $filtre_univ);
$chercheuse->addCritere("type_p", "force", "$USER->type_plateforme='oui'", "", "oui"); //toujours vrai, mais force dans emis dans le SQL mais pas l'HTTP


//r�vision pour UVT 
if (!is_admin() && $CFG->seulement_mes_examens) {
    $chercheuse->addCritere ("","force","auteur_mail='".$USER->email."'","",$USER->email);
}

$chaine_critere_recherche = $chercheuse->getCritereHTTP();
$critere_recherche = $chercheuse->getCritereSQL();

$url = "liste.php?";
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "");





/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "", "id_etab asc,id_examen asc", "id_etab desc,id_examen desc");
$trieuse->addColonne("auteur", "auteur");
$trieuse->addColonne("titre", "nom_examen");
$trieuse->addColonne("date", "ts_datemodification");
$trieuse->addColonne("dateh", "ts_datedebut");
$trieuse->setTriDefaut("dateh", false);

$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen();  //entetes
/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//lien de mp : les deux (recherche et tri)

$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");


// compter les examens selon crit�res

$nbTotal=count_records("examens",$USER->type_plateforme."='OUI'");

$indice_max=count_records ("examens",$critere_recherche,1);

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal. " ".traduction("examens"));


///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
$url_retour = $chaine_critere_recherche;
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour,"indice_ecart=" . $indice_ecart,"&amp;");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&amp;");

print_form_actions ($tpl,'form_actions',$url_retour,'liste.php');
$url_retour=urlencode($url_retour);
///////////////////////////////////////////////

////////////////////////////////////////////////////
// requete de s�lection des examens � afficher
////////////////////////////////////////////////////

$examens=get_records("examens",$critere_recherche,$critere_tri,$indice_deb,$indice_ecart);
$compteur_ligne = 0;
foreach ($examens as $ligne) {

	// rev 982  parametre unique des actions
	$idnat=$ligne->id_etab.'.'.$ligne->id_examen;
	$tpl->newBlock("ligne"); // une ligne

	$tpl->setCouleurLigne($compteur_ligne);
	$tpl->assign("id", $idnat);


    $tpl->setConditionalvalue ($ligne->anonyme,"anonyme",
      "<img src='".$CFG->chemin_images."/anonyme.jpg' title='".traduction("exam_anonyme")."' alt='".traduction("exam_anonyme")."' />","");
	$tpl->assign("title_id", nom_univ($ligne->id_etab)); // ajout SB
	$tpl->assign("quest", affiche_texte($ligne->nom_examen));

    $ligne->auteur=applique_regle_nom_prenom($ligne->auteur); // rev 841

     if ($CFG->afficher_lien_mail_liste_examens)  // rev 839 KS
            $tpl->assign("auteur", cree_lien_mailto($ligne->auteur_mail,$ligne->auteur));
        else
             $tpl->assign("auteur",$ligne->auteur);

	$tpl->assign("date",userdate($ligne->ts_datemodification,'strftimedateshort'));
	$tpl->assign("dateh",userdate($ligne->ts_datedebut,'strftimedatetimeshort'));
	$tpl->assign("heures",userdate($ligne->ts_datefin,'strftimedatetimeshort'));


	//icone etat de passage
	$etat_date_passage=image_etat_examen ($ligne);
		$tpl->assign("image_dateh", "e_" . $etat_date_passage);
	$tpl->assign("alt_image_dateh", traduction($etat_date_passage));


	// examen r�cent
	if ($USER->derniere_connexion <= $ligne->ts_datecreation)	{
		$tpl->newBlock("image_nouv");

	}
	// PP REV 1.3.1 ajout colonne nombre inscrits et lien vers liste (ekport� V2)
	// PP REV 1.4 ajout aussi nombre passages
	$tpl->newblock("td_inscrits");

	$cpti = compte_inscrits($ligne->id_examen, $ligne->id_etab);
	$cptp = compte_passages($ligne->id_examen, $ligne->id_etab);
	if ($cpti) {
		$tpl->newblock("inscrits_lien");
		$tpl->assign("nb_inscrits", $cptp . "/" . $cpti);

		// rev 839 pas le droit si pas de chez moi !
		if (a_capacite("etl",$ligne->id_etab)) {
		     $tpl->newBlock ("liste_inscrits");
			$tpl->assign("n", $compteur_ligne);
			$tpl->assignURL("url_liste","liste_inscrits.php?ide=" . $ligne->id_etab . "&amp;idq=" . $ligne->id_examen);
		}
	}
	// end PP


   // debut code revis� theme v15
    $items=array();
    $items[]=new icone_action('consulter',"consulterItem('$idnat')");

    if ($peutModifier) {
		if (is_super_admin()  || $USER->id_etab_perso == $ligne->id_etab || is_admin(false,$ligne->id_etab)) {
			 $items[]=new icone_action('modifier',"modifierItem('$idnat')");
		} 	else  $items[]=new icone_action();
	}

	// si droit de dupliquer
	if ($peutDupliquer) {
		if ($ligne->pool_pere == 0 || $CFG->peut_dupliquer_pool_fils) { // V1.5 pourquoi pas
			 $items[]=new icone_action('dupliquer',"dupliquerItem('$idnat')");
		} else   $items[]=new icone_action();

	}

	// si droit de supprimer
	if ($peutSupprimer) {
		if (is_super_admin()  ||  ($USER->id_etab_perso == $ligne->id_etab) || is_admin(false,$ligne->id_etab)) {
           if (!$ligne->verouille) {  // rev 983
			if (($ligne->pool_pere == 0) || $CFG->peut_supprimer_pool_fils) {  // V 1.5 pourquoi pas
				if ($ligne->est_pool) {
					$nb= count(liste_groupe_pool($ligne->id_examen,$ligne->id_etab));
					$items[]=new icone_action('supprimer',"supprimerPoolPere('$idnat','$nb')" );
				}
				else
                  if ($ligne->pool_pere==0)
                  	$items[]=new icone_action('supprimer',"supprimerItem('$idnat')" );

				  else {
				  	$idpere=$ligne->id_etab.".".$ligne->pool_pere;
				    $items[]=new icone_action('supprimer',"supprimerPoolFils('$idnat','$idpere')" );
				  }

			}else  $items[]=new icone_action();
           } else  $items[]=new icone_action();
		}else  $items[]=new icone_action();
	}



    $tpl->newBlock ('icones_actions');
    print_icones_action($tpl,'icones_actions',$items,'actions_'.$compteur_ligne);
	// passage � la ligne suivante
	$compteur_ligne++;
}
if ($compteur_ligne==0){
	$tpl->newBlock("no_results");
	$tpl->assign( "colspan",$colspan);
}
///////////////////////////////////
//
//	gestions de l'affichage des crit�res de recherche
//
///////////////////////////////////

$tpl->gotoBlock("_ROOT");

//$tpl->setChecked($filtre_voir_ex_nat==1,"voir_ex_nat_checked");
$tpl->assign("tri",$tri);

$tpl->assign("tri",$tri);
$tpl->assign("id_rech",$id_rech);
$tpl->assign("titre_rech",$titre_rech);

$ets=get_etablissements_filtre('nom_etab');
// rev 839 ajout N� etablissement
foreach ($ets as $et) {
    $et->nom_etab=sprintf("%s (%s)",$et->nom_etab,$et->id_etab);
}

print_select_from_table($tpl,"select_filtre_univ",$ets,
                             "filtre_univ","saisie","style='width:200px'","id_etab","nom_etab",traduction ("universite"),$filtre_univ);
print_boutons_criteres($tpl);



$tpl->gotoBlock("_ROOT");


$items=array();
if (a_capacite("ea")) {
	$items[]=new icone_action('nouveau',"doPopup('ajout.php?id=-1')",'nouvel_examen');
}


$items[]=get_menu_item_criteres();

if ($chaine_critere_recherche)
    $items[]=get_menu_item_tout_afficher();

if (is_admin() && $CFG->universite_serveur !=1) {
    $items[]=new icone_action('import',"doPopup('admin/import_examen.php?')",'import_examen');
}
$items[]=get_menu_item_legende("examen");

print_menu($tpl,"_ROOT.menu_niveau2",$items);


//$CFG->debug_templates=0;
//$CFG->dump_vars=1;

$tpl->printToScreen(); //affichage
?>

