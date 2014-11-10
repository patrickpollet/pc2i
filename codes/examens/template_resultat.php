<?php

/**
 * @author Vincent Bellanger
 * @version $Id: template_resultat.php 1157 2010-09-24 09:38:04Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */
 /**
  * rev 948 Pp
  *   fckeditor deplac� dans commun/editeurs en pr�paration usage autres �diteurs
  *   bouton annuler supprim�  fermait la fiche examen ...
  *   FCKConfig.AutoDetectLanguage	= false ; pour forcer le frncais (autodetection capricieuse)
  *
  */
$chemin = '../..';

$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");
require_once($chemin_commun."/lib_examens.php");
require_once ($chemin_commun . "/lib_resultats.php"); //n'est pas charg� par c2i_params


require_login('P'); //PP



$idq=required_param("idq",PARAM_INT,"");
$ide=required_param("ide",PARAM_INT,"");
// rev 1.41 type de liste_inscrits  0 tous 1 absents 2 presents
$type=optional_param("type",0,PARAM_INT);
$retour_fiche=optional_param("retour_fiche",0,PARAM_INT);
$texte_resultats=optional_param("texte_resultats","",PARAM_RAW);  // ne rien filtrer il y a de l'HTML et mettre "" si pas trouv�
$tester = optional_param("tester", "", PARAM_RAW);  //-- filtrer les caracteres non num et mettre 0 si non trouv�
$score_global=optional_param("score_global",50,PARAM_INT);

//important apr�s avoir lu $ide !!!
v_d_o_d("etl"); // droits

require_once ($chemin . "/templates/class.TemplatePower.inc.php"); //inclusion de moteur de templates
$tpl = new C2IPopup(); //cr�er une instance


$ligne=get_examen($idq,$ide);

$affiche_test = "";

if ($texte_resultats) {
	set_template_examen ($idq, $ide, $texte_resultats);
	//tracking :
	espion2("modification_template", "examen", $ide.".".$idq);
	//
	// Si on a demand� a teste, on affiche un div contenant le template resultat
	//
	if ($tester){
		$res=new resultat(false); // sinon notices  php
		//Notice: Undefined property: stdClass::$tabref_score in commun/lib_resultats.php on line 1013
		$res->score_global = $score_global;
		$refs = get_referentiels();
		foreach ($refs as $domaine)
			$res->tabref_score[$domaine->referentielc2i]=$score_global;
		$ligne=get_examen($idq,$ide);
		$test_template_resultats = affiche_template_resultats($res, $ligne);
		$affiche_test = "<div class=\"test_resultat\">$test_template_resultats</div>";
	}
}
else{

	include ($CFG->chemin."/langues/preconisations_".$CFG->langue.".php");

	if (($texte_resultats = get_template_examen($idq,$ide)) == ""){
		$texte_resultats = generate_modele_template($tpl);
	}
}
// rev 948 editeur deplac� dans commun/editeurs

/**
$editeur_path=$CFG->chemin_commun.'/editeurs/fckeditor/';

require_once($editeur_path.'fckeditor.php');
$Config['Enabled'] = true ;
$oFCKeditor = new FCKeditor("texte_resultats") ;
$oFCKeditor->BasePath = $editeur_path;
$oFCKeditor->Value = stripslashes($texte_resultats);
$oFCKeditor->Width  = "100%" ;
$oFCKeditor->Height = "1000px" ;
$oFCKeditor->Config['CustomConfigurationsPath'] = '../fckconfig.js'  ;

//print_r($oFCKeditor);
$textefck = $oFCKeditor->CreateHtml() ;
***/

require ($CFG->chemin_commun.'/lib_editeur.php');

$textefck= get_fckeditor("texte_resultats",$texte_resultats,"100%","1000px");


$form=<<<EOL
{affiche_test}

<form method="POST" name="formulaire">
<table border="1">
<tr>
<td>{balise_dispo}</td>
<td>{recapitulatif}</td>
</tr>
<tr>
<td valign="top" width="10%">{champs}
<p>{vocab}<br><i>{vocab_methode}</i> </p>
<div id="vocab" style="background-color: #8fbcde; padding:15px"><p>{vocab_methode2}</p></div>
</td>
<td width="90%" valign="top">{textefck}</td>
</tr>
</table>
<p>{simule_score} : <input type="text" name="score_global" value="50">% &nbsp;
<input name="tester" type="submit" class="saisie_bouton" id="tester" value="{bouton_tester}"></p>
<p>{bouton:enregistrer}</p>
</form>
EOL;


$nomexamen= nom_complet_examen($ligne);


$tpl->assignInclude("corps", $form,T_BYVAR);
$tpl->prepare($chemin);
$tpl->gotoBlock("_ROOT");
$tpl->assign("_ROOT.titre_popup",traduction("libl_template_resultats")."<br/>".$nomexamen );
//$tpl->assign('_ROOT.infos',traduction("libl_template_resultats"));

$tpl->assign("affiche_test", $affiche_test);
$tpl->assign("champs", generate_champs_template($tpl));
$tpl->assign("texte_dom", generate_tableau_template($tpl));  // non defini dans le modele ???
$tpl->assign("textefck", $textefck);

// v 1.5 menu de niveau 2 standard (cf weblib)

$tpl->assign("terminer", traduction("bouton_terminer"));
$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?ide=" . $ide . "&idq=" . $idq."#template_resultats": "");
$tpl->print_boutons_fermeture($url_retour);
$tpl->printToScreen(); //affichage

?>