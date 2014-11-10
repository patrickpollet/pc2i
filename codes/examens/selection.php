<?php

/**
 * @author Patrick Pollet
 * @version $Id: selection.php 1273 2011-10-21 09:13:18Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//
//	S�lection des questions de l'examen
//
// selection du type de tirage de l'examen et de ses crit�res de s�lection
// si le tirage est manuel, on affiche toutes les questions correspondant aux crit�res de l'examen
// sinon le tirage est al�atoire et on affiche les questions d�j� s�lectionn�es
// le changement de questions se fait ici en mode "al�atoire" ou via ajax en al�atoire



$chemin = '../..';
$chemin_commun = $chemin . "/commun";
require_once ($chemin_commun . "/c2i_params.php"); //fichier de param�tres

require_login("P"); //PP


$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

$indice_deb=optional_param("indice_deb",0,PARAM_INT);
$tri=optional_param("tri","",PARAM_INT);

$referentiel=optional_param("referentiel","",PARAM_ALPHANUM);

//la question a changer
$ch_idq=optional_param("ch_idq","",PARAM_INT);
$ch_ide=optional_param("ch_ide","",PARAM_INT);

//important apr�s avoir lu $ide !!!
v_d_o_d("em"); //PP


$ligne_e=get_examen($idq,$ide);
$type_tirage=$ligne_e->type_tirage;

if ($ligne_e->pool_pere)
    erreur_fatale ("err_tirage_pas_membre_pool",$ide.".".$idq);

if (!$type_tirage==EXAMEN_TIRAGE_ALEATOIRE || !$type_tirage== EXAMEN_TIRAGE_MANUEL)
    erreur_fatale ("err_tirage_pas_manuel_aleatoire",$ide.".".$idq);

$new_question="";
if ($type_tirage==EXAMEN_TIRAGE_ALEATOIRE && $ch_idq && $ch_ide) {
    $new_question=change_question ($ch_idq,$ch_ide,$idq,$ide);
   // print("nq".$new_question);
}


require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates

$tpl = new C2IPopup(); //cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL

<!-- START BLOCK : js_prototype -->

<script type="text/javascript">


var nbqref = {nbqref};
var nbqtotref = {nbqtotref};
var referentielsel = "{referentielsel}";
// pour les passages de qcms
function selection_question(ide,idq) {
	var sel=0;
	if (document.formsel['c_'+ide+'_'+idq].checked==true) sel=1;
	var request_url = '../../commun/ajax/modif_selection_manuelle.php?sel='+sel+'&idexe={ide}&idex={idq}&ide='+ide+'&idq='+idq+'&{session_ch}';
	new Ajax.Request(request_url,
	{
		method:'post',

		onSuccess: function(transport){
			var retour = transport.responseText.evalJSON();
			if (retour.result != 'ok') {
				alert(retour.result + " PROBLEME");
			}
		},
		onFailure: function(transport){
			alert("une erreur s'est produite");
		}
	});
	if (sel == 1){
		nbqref++;
		nbqtotref++;
	}
	else {
		nbqref--;
		nbqtotref--;
	}
	document.getElementById('nb_'+referentielsel).innerHTML = nbqref;
	document.getElementById('nb_total').innerHTML = nbqtotref;

}

</script>

<table border="1"  >
<tr><td>domaine</td>
<!-- START BLOCK : js_ref -->
<td><a href="{url}&amp;referentiel={ref}">{ref}</a></td>
<!-- END BLOCK : js_ref -->
</tr>
<tr><td>nb questions sélectionnées&nbsp;:&nbsp;<span id="nb_total">{nbqtotref}</span>&nbsp;</td>
<!-- START BLOCK : js_nb -->
<td class="centre" ><span id="nb_{ref}">{nb}</span></td>
<!-- END BLOCK : js_nb -->
</tr>
</table>
<!-- END BLOCK : js_prototype -->



<!-- INCLUDE BLOCK : multip_haut -->

<form name="formsel" action="#" method="post" onsubmit="return false;">
<table width="100%">
<!--START BLOCK : info_famille -->
   <tr class="information">
      <td>{information_selection_qst}</td>
   </tr>
<!--END BLOCK : info_famille -->   
   <tr>
    <td class="gauche" >{menu_niveau2}</td>
    <td class="droite commentaire1">{nb_items}</td>
 </tr>
</table>


<table width="100%">
<thead>
  <tr>
      <th class="bg"><a href="{url_id}"    title="{alt_tri}">{t_id}</a>{tri_id}</th>
      <th class="bg"><a href="{url_titre}" title="{alt_tri}">{t_titre}</a>{tri_titre}</th>
      <th class="bg"><a href="{url_auteur}" title="{alt_tri}">{t_auteur}</a>{tri_auteur}</th>
      <th class="bg"><a href="{url_referentiel}" title="{alt_tri}">{t_referentiel}</a>{tri_referentiel}</th>
      <th class="bg"><a href="{url_alinea}" title="{alt_tri}">{t_alinea}</a>{tri_alinea}</th>
       <th class="bg"><a href="{url_famille}" title="{alt_tri}">{t_famille}</a>{tri_famille}</th>
       <!--
      <th class="bg">{t_famille_ordre}</th>
      -->
      <th class="bg"><a href="{url_date}" title="{alt_date}">{t_date}</a>{tri_date}</th>

<!-- START BLOCK : col_sel -->
            <th width="40" class="bg centre"><a href="#"></a>
            {t_sel}</th>
<!-- END BLOCK : col_sel -->
<!-- START BLOCK : col_c -->
            <th width="40" class="bg centre"><a href="#"></a>
            {t_consult}</th>
<!-- END BLOCK : col_c -->
</tr>
</thead>
<tbody>
<!-- START BLOCK : question -->
          <tr class="{paire_impaire}">
             <td class="{style}" title="{obsolete}" >{id}</td>
            <td>{quest}</td>
            <td>{auteur}</td>
            <td>{ref}</td>
            <td>{alinea}</td>
			<td>{famille}</td>
            <td>{date}
<!-- START BLOCK : image_nouv -->
			<br>
            <img src="{chemin_images}/nouveau.gif" alt="{alt_nouveau}" width="59" height="17"/>
<!-- END BLOCK : image_nouv -->
			</td>

<!-- START BLOCK : td_sel -->
			<td class="centre">
<!-- START BLOCK : td_a_s -->
<a href="{url_a_sel}" alt="{alt_changer_question}">
  <img src="{chemin_images}/i_retirer1.gif" title="{alt_changer_question}" id="sel{n}"/>
</a>
<!-- END BLOCK : td_a_s -->


<!-- START BLOCK : td_m_s -->
<input type="checkbox" name="c_{idequest}_{idquest}" value="1" {ch} onclick="selection_question('{idequest}','{idquest}')" />
<!-- END BLOCK : td_m_s -->
</td>
<!-- END BLOCK : td_sel -->
<!-- START BLOCK : td_c -->
			<td class="centre">
            <a href="#" title="{alt_consult}" onclick="openPopup('{url_consult}','','{lp}','{hp}')">
             <img src="{chemin_images}/i_consulter.gif" alt="{alt_consult}" id="c{n}"/></a>
            </td>
<!-- END BLOCK : td_c -->

          </tr>
<!-- END BLOCK : question -->
        </tbody>
      </table>
</form>
<!-- INCLUDE BLOCK : multip -->

EOL;

$options=array (
		"corps_byvar"=>$liste
);

if ($type_tirage != EXAMEN_TIRAGE_ALEATOIRE)  {
	$options["liste"]=1;
	$CFG->utiliser_prototype_js=1;
}
$tpl->prepare($chemin,$options);



$tpl->gotoBlock("_ROOT");

$tpl->assign("_ROOT.titre_popup", traduction("selection_" . $ligne_e->type_tirage)."<br/>".nom_complet_examen($ligne_e));

// rev 984
if ($CFG->algo_tirage !=3)
	$tpl->newBlock('info_famille');
///////////////////////////////////////////////
//affichage des ent�tes de colonnes selon droits

$tpl->newblock("col_c");

$tpl->newblock("col_sel");


require_once ($CFG->chemin_commun . "/trieuse.class.php");

////////////////////////////////////////////////////
// crit�res de recherche
////////////////////////////////////////////////////
// rev 1079 questions filtr�es
$critere_recherche = "not est_filtree and $USER->type_plateforme='OUI'";
$chaine_critere_recherche="";
// rev 855 sur la nationale meme un examen de postionnement doit prendre les valid�es
// rev 940 $CFG->seulement_validee_en_positionnement

if ($USER->type_plateforme == "certification"   || $CFG->universite_serveur==1  || $CFG->seulement_validee_en_positionnement)
    $critere_recherche =concatAvecSeparateur($critere_recherche, 'etat='.QUESTION_VALIDEE," and ");
//toutes les questions candidates
$critere_recherche_total=$critere_recherche;


//$referentiels=get_referentiels("referentielc2i");
// rev 944 qcm par comp�tences le retour !!!
$referentiels=get_referentiels_liste($ligne_e->referentielc2i,"referentielc2i");
//print_r($referentiels);
//print_r($ligne_e);
if ($type_tirage == EXAMEN_TIRAGE_MANUEL) { // tirage manuel via ajax , prendre le 1er par défaut
      if (!$referentiel)
                $referentiel = $referentiels[0]->referentielc2i;
 }

if ($referentiel) {
    $critere_recherche =concatAvecSeparateur($critere_recherche, "referentielc2i='$referentiel'"," and ");
    $chaine_critere_recherche = "referentiel=" . $referentiel;
}


/////////////////////////////////////////////
// crit�res de tri
/////////////////////////////////////////////

$url = "selection.php?idq=" . $idq . "&amp;ide=" . $ide."&amp;retour_fiche=".$retour_fiche;
$url = concatAvecSeparateur($url, $chaine_critere_recherche, "&amp;");


$trieuse = new trieuse($tpl, "", $url, $tri);
$trieuse->addColonne("id", "", "id_etab asc,id asc","id_etab desc,id desc");
$trieuse->addColonne("titre", "titre");
$trieuse->addColonne("auteur", "auteur");

$trieuse->addColonne("referentiel", "", "referentielc2i asc, alinea asc,id_famille_validee asc",
    "referentielc2i desc, alinea desc,id_famille_validee desc");
$trieuse->addColonne("alinea", "", "alinea asc, referentielc2i asc", "alinea desc,referentielc2i asc");

$trieuse->addColonne("famille", "id_famille_validee");
$trieuse->addColonne("date", "ts_datemodification");

$trieuse->setTriDefaut("referentiel", true);

$critere_tri = $trieuse->getCritereSQL();

$trieuse->printToScreen();  //entetes

///////////////////////////////////////////////
// gestion des retours vers cette page � partir d'une popup sans perte des crit�res
// et du changement de questions
$url_retour =$chaine_critere_recherche;
$url_retour=concatAvecSeparateur($url_retour,"indice_deb=" . $indice_deb,"&amp;");
$url_retour=concatAvecSeparateur($url_retour, $trieuse->getParametreTri(),"&amp;");
$url_retour=urlencode($url_retour);
///////////////////////////////////////////////

/////////////////////////////////////////
// gestion de la multipagination
/////////////////////////////////////////
//$indice_ecart est trouv� dans c2i_params//lien de mp : les deux (recherche et tri)
$url_multipagination = concatAvecSeparateur($url, $trieuse->getParametreTri(), "&amp;");

if (isset ($num_page)) {
	if ($num_page > 0) {
		$indice_deb = $indice_ecart * ($num_page -1);
	}
}

// variables utilis�es dans le code de multipagination
$indice_fin = $indice_deb + $indice_ecart -1; // indice de fin d'affichage




if ($type_tirage == EXAMEN_TIRAGE_ALEATOIRE) {  //toutes
        $questions=get_questions($idq,$ide,false,$critere_tri);
        $indice_max=count($questions); // pas de multipagination

} else {

        $indice_max=count_records("questions",$critere_recherche);
		$questions=get_records("questions",$critere_recherche,$critere_tri,$indice_deb,$indice_ecart,false);

		$tpl->newBlock('js_prototype');
		$tpl->assign('session_ch', session_name() . '=' . session_id());
		$tpl->assign('ide', $ide);
		$tpl->assign('idq', $idq);
		$tpl->assign('referentielsel', $referentiel);

        // comptage du nombre de questions d�j� utilis�es par r�f�rentiel
		$nbqref = array ();
		foreach ($referentiels as $ref)
            $nbqref[$ref->referentielc2i] = 0;

        $nbqtotref = 0;
        $questions_utilisees=get_questions($idq,$ide,false);

        foreach($questions_utilisees as $qu) {
		    $nbqref[$qu->referentielc2i]++;
        	$nbqtotref++;
		}
        $tpl->assign('nbqref', $nbqref[$referentiel]);  //celle du referentiel courant
        $tpl->assign('nbqtotref', $nbqtotref);

        foreach ($referentiels as $ref) {
                $tpl->newBlock('js_ref');
                $tpl->assign('ref', $ref->referentielc2i);
                $tpl->assignURL('url',$url);
                $tpl->newBlock('js_nb');
                $tpl->assign('ref', $ref->referentielc2i);
                $tpl->assign('nb', $nbqref[$ref->referentielc2i]);
        }

}
	////////////////////////////////////////////
$nbTotal=count_records("questions",$critere_recherche_total);

$tpl->assign("_ROOT.nb_items",$chaine_critere_recherche."<br/>".$indice_max."/".$nbTotal." ".traduction ("questions"));

$res=get_familles("idf");
$tableau_familles = array("0"=>"???"); //aucune
foreach ($res as $ligne)
    $tableau_familles[$ligne->idf] = $ligne->famille;

$compteur_ligne = 0;
foreach($questions as $ligne) {
	$tpl->newBlock("question",$compteur_ligne);
	if ($new_question == $ligne->id_etab . "." . $ligne->id) {
		$tpl->assign("paire_impaire","changee");
	} else
		$tpl->setCouleurLigne($compteur_ligne);

	$tpl->assign("id", $ligne->id_etab . "." . $ligne->id);

     //rev 944 couleurs selon �tat
    if ($ligne->etat==QUESTION_REFUSEE) {
        $tpl->assign("style","rouge");
        $tpl->traduit("obsolete","alt_non_valide");
    }else  {
        if ($ligne->etat==QUESTION_VALIDEE) {
        $tpl->assign("style","vert");
        $tpl->traduit("obsolete","alt_valide");
        } else {
            $tpl->assign("style","orange");
        $tpl->traduit("obsolete","alt_non_examinee");
        }
    }


	$tpl->assign("quest",affiche_texte_question($ligne->titre));

	$tpl->assign("ref", $ligne->referentielc2i);
	$tpl->assign("alinea", $ligne->alinea);



if ($ligne->id_famille_validee)
	$tpl->assign("famille", $tableau_familles[$ligne->id_famille_validee]);
else
	$tpl->assign("famille","?")	;
$tpl->assign("date", userdate($ligne->ts_datemodification,'strftimedateshort'));


// rev 984
if (trim($ligne->auteur) == "")
	$ligne->auteur = traduction("SDTICE");

$ligne->auteur = applique_regle_nom_prenom($ligne->auteur); // rev 841

if ($CFG->afficher_lien_mail_liste_questions) // rev 839 KS
	$tpl->assign("auteur", cree_lien_mailto($ligne->auteur_mail, $ligne->auteur));
else
	$tpl->assign("auteur", $ligne->auteur);

	
	

	// question r�cente
	if ($USER->derniere_connexion <= $ligne->ts_datemodification) {
		$tpl->newBlock("image_nouv");
		$tpl->assign("alt_valid", traduction("alt_nouveau"));
	}

	// si droit de consulter // � g�rer
	$tpl->newblockNum("td_c",$compteur_ligne);
	$tpl->assignURL("url_consult","../questions/fiche.php?idq=" . $ligne->id . "&amp;ide=" . $ligne->id_etab);

	$tpl->newblockNum("td_sel",$compteur_ligne);

	if ($ligne_e->type_tirage == EXAMEN_TIRAGE_ALEATOIRE) {
		$tpl->newblockNum("td_a_s",$compteur_ligne);

        $tpl->assignURL("url_a_sel",$url."&amp;ch_idq={$ligne->id}&amp;ch_ide={$ligne->id_etab}&amp;".urldecode($url_retour));

	} else {
		$tpl->newblock("td_m_s");

		$utilisee=est_utilise_examen($ligne->id,$ligne->id_etab,$idq,$ide);

		$tpl->setChecked($utilisee,"ch");
		$tpl->assign("idequest", $ligne->id_etab);
		$tpl->assign("idquest", $ligne->id);
	}
	// passage � la ligne suivante
	$compteur_ligne++;
}


$tpl->gotoBlock("_ROOT");


$items=array();
$items[]=get_menu_item_legende("question");
print_menu($tpl,"_ROOT.menu_niveau2",$items);


$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&amp;ide=" . $ide . "&amp;idq=" . $idq."#questions": "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen(); //affichage
?>

