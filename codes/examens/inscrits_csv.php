<?php
/**
 * @author Patrick Pollet
 * @version $Id: inscrits_csv.php 1051 2010-03-09 18:36:08Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($chemin_commun."/lib_ldap.php");
require_once($chemin_commun."/lib_import_export.php");



require_login("P"); //PP

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates


$idq=required_param("idq",PARAM_INT);   // -1 en cr�ation
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);
$liste=optional_param("liste","",PARAM_RAW);
//surtout pas ne vas pas encore dans $_FILES
//$fichier_upl=optional_param("fichier_upl","",PARAM_RAW);

$format=optional_param("format","",PARAM_ALPHANUM);
$format_fic=optional_param("format_fic","",PARAM_ALPHANUM);

//important apr�s avoir lu $ide !!!
v_d_o_d("em");

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOL

{resultats_op}

<form action="inscrits_csv.php" method="post" name="monform"  enctype="multipart/form-data">

<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}"/>
<!-- END BLOCK : id_session -->

<fieldset>
<legend>{inscriptions_massives_csv} </legend>
<table width="99%">
<tr><td colspan="3" class="commentaire1">{info_inscriptions_csv} </td></tr>

<tr>
<th>{form_format}</th>
<td>
{select_format}
</td>
</tr>
<tr>


<th>{form_liste}</th>

<td><textarea name="liste" rows="4" cols="70" class="saisie">
</textarea></td>
<td>
 <input name="add_liste" id="add_liste" type="submit" class="saisie_bouton"  value="{inscrire}" title="{inscrire}" />
</td>
</tr>
<tr>
<th>{form_format_fic}</th>
<td>
{select_format_fic}
</td>
</tr>
<tr>
 <th>{form_fichier}</th>
  <td><input type="file" class="saisie" name="fichier_upl" size="68" /></td>
<td>
 <input name="add_file" id="add_file" type="submit" class="saisie_bouton"  value="{inscrire}" title="{inscrire}" />
</td>
</tr>

</table>
</fieldset>
</form>

EOL;


$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);

$ligne=get_examen($idq,$ide);
$tpl->assign("_ROOT.titre_popup",traduction("inscriptions_massives_csv")."<br/>". nom_complet_examen($ligne));


//gi go ou gi go pas ?
if ($liste || isset($_FILES["fichier_upl"])) {
   // print_r($_FILES);
	if (isset($_FILES["fichier_upl"]) && !empty ($_FILES["fichier_upl"]["name"]))  {  //important le 2eme test ///
		//garder ce fichier soit dans csv soit dans apogee
		$dir=$CFG->chemin_ressources.($format_fic=="format_apogee" ? "/apogee" :"/csv");
        //r�cupere le en verifiant taille et type mime ...
		$fichier_garde=upload_file('fichier_upl',$dir, $CFG->max_taille_fichiers_uploades,array());
        //, array('text/plain')); pb avec les mac qui n'envoient pas ce type mime

		if (!$fichier_garde)
			erreur_fatale("err_upload_fichier",$_FILES["fichier_upl"]["tmp_name"]);
	} else $fichier_garde="";
    //TODO virer ce fichier si pas au format !!!!
	$resultats=inscription_massive_csv($idq,$ide,$liste,$format,$fichier_garde,$format_fic);
	if (count($resultats))
		$tpl->assign("resultats_op",print_details($resultats));
	else $tpl->assign("resultats_op","");
}
else $tpl->assign("resultats_op","");



$tpl->assign("info_inscriptions_csv",traduction ("info_inscriptions_csv",false,$ide)); //glisse le N� etablissement

$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);
$tpl->assign("retour_fiche", $retour_fiche);


$formats_import=array("format_inpm","format_inmp","format_ipnm","format_ipmn","format_imnp",
                    "format_impn","format_apogee");
$table=array();
foreach ($formats_import as $possible) {
    if ($possible !="format_apogee")
    $table[]=new option_select($possible,traduction($possible,false));
}

print_select_from_table($tpl,"select_format",$table,"format",false,false,"id","texte","",$format);

//ajoute apog�e pour les fichiers seulement !
$table[]=new option_select("format_apogee",traduction("format_apogee",false));
print_select_from_table($tpl,"select_format_fic",$table,"format_fic",false,false,"id","texte","",$format_fic);

$tpl->gotoBlock("_ROOT");

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&ide=" . $ide . "&idq=" . $idq."#inscriptions" : "");
$tpl->print_boutons_fermeture($url_retour);


$tpl->printToScreen();										//affichage
?>

