<?php
/**
 * @author Patrick Pollet
 * @version $Id: inscrits_groupe_ldap.php 1053 2010-03-10 13:55:17Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";

require_once($chemin_commun."/c2i_params.php");                 //fichier de param�tres

require_login("P"); //PP

require_once( $chemin."/templates/class.TemplatePower.inc.php");    //inclusion de moteur de templates

require_once($chemin_commun."/lib_ldap.php");


$idq=required_param("idq",PARAM_INT);   // -1 en cr�ation
$ide=optional_param("ide",$USER->id_etab_perso,PARAM_INT); //�tab de l'examen, d�faut = ici '
$retour_fiche=optional_param("retour_fiche","0",PARAM_INT);

$liste=optional_param("liste","",PARAM_RAW);
$groupes=optional_param("groupes","",PARAM_RAW);
$format_fic=optional_param("format_fic","",PARAM_ALPHANUM);

//important apr�s avoir lu $ide !!!
v_d_o_d("em");

$tpl = new C2IPopup(  ); //cr�er une instance
//inclure d'autre block de templates

$forme=<<<EOL

{resultats_op}
<form class="normale" action="inscrits_groupe_ldap.php" method="post" name="monform"  enctype="multipart/form-data">

<input name="idq" type="hidden" value="{idq}"/>
<input name="ide" type="hidden" value="{ide}"/>
<input type="hidden" name="retour_fiche" value="{retour_fiche}" />
<!-- START BLOCK : id_session -->
<input name="{session_nom}" type="hidden" value="{session_id}" />
<!-- END BLOCK : id_session -->

<fieldset>
<legend>{inscriptions_groupe_ldap} </legend>
<div class="commentaire1">{info_inscriptions_groupe_ldap}</div>

<p class="double">
  <label for="liste">{form_liste_ldap} </label>
  <textarea name="liste" id="liste" rows="6" cols="50" class="saisie"></textarea>
</p>

<p class="double">
  <label for="listecsv">{form_liste_ldap_csv} </label>
 <label for="select_format_fic">{form_format_fic_ldap}</label> {select_format_fic}
  <input type="file" class="saisie" name="fichier_upl" size="50" />


</p>


<!-- START BLOCK : gldap1 -->

<p class="double">
  <label for="groupes" {bulle:astuce:msg_separes_points_virgules}>{form_groupes_ldap}</label>
  <input class="saisie" type="text" name="groupes" id="groupes"/>
  <br/>
<span class="commentaire1">
</span>
 </p>
<!-- END BLOCK : gldap1 -->

<!-- START BLOCK : gldap2 -->

<p class="double">
  <label for="groupes">{form_groupes_ldap_liste}</label>
  {select_group_ldap}
  <br/>
<span class="commentaire1">
</span>
 </p>
<!-- END BLOCK : gldap2 -->


<p class="simple">
  <input name="add_groupes" id="add_groupes" type="submit" class="saisie_bouton"  value="{inscrire}" title="{inscrire}" />
</p>
</fieldset>

</form>
EOL;


$tpl->assignInclude("corps",$forme,T_BYVAR);
$tpl->prepare($chemin);


$ligne=get_examen($idq,$ide);
$tpl->assign("_ROOT.titre_popup",traduction("inscriptions_groupe_ldap")."<br/>". nom_complet_examen($ligne));


//gi go ou gi go pas ?
if ($liste || $groupes || isset($_FILES["fichier_upl"] )) {
    if (isset($_FILES["fichier_upl"]) && !empty ($_FILES["fichier_upl"]["name"]))  {  //important le 2eme test ///
        //garder ce fichier soit dans csv soit dans apogee
        $dir=$CFG->chemin_ressources."/csv";
        //r�cupere le en verifiant taille et type mime ...
        $fichier_garde=upload_file('fichier_upl',$dir, $CFG->max_taille_fichiers_uploades,array());
        //, array('text/plain')); pb avec les mac qui n'envoient pas ce type mime

        if (!$fichier_garde)
            erreur_fatale("err_upload_fichier",$_FILES["fichier_upl"]["tmp_name"]);
    } else $fichier_garde="";


    $resultats=inscription_massive_ldap($idq,$ide,$liste,$groupes,$fichier_garde,$format_fic);
    if (count($resultats))
    	$tpl->assign("resultats_op",print_details($resultats));
	else $tpl->assign("resultats_op","");
} else $tpl->assign("resultats_op","");

$tpl->assign("ide", $ide);
$tpl->assign("idq", $idq);

$tpl->assign("retour_fiche", $retour_fiche);

$formats_import=array("format_n","format_l","format_m");
$table=array();
foreach ($formats_import as $possible) {
    $table[]=new option_select($possible,traduction($possible,false));
}

print_select_from_table($tpl,"select_format_fic",$table,"format_fic",false,false,"id","texte","",$format_fic);

// rev 1022 afficher les groupes ldap possibles
if (!empty($CFG->chercher_groupes_ldap)) {
	$tpl->newBlock ('gldap2');
	$groups=auth_ldap_get_grouplist($CFG->filtre_groupes_ldap);
	sort($groups);
	$table=array();
	foreach($groups as $group) {
		$table[]=new option_select($group,$group);
	}
	print_select_from_table($tpl,"select_group_ldap",$table,"groupes",false,false,"id","texte",traduction("groupe"),"");

} else
	$tpl->newBlock ('gldap1');

$url_retour=($retour_fiche? $CFG->chemin."/codes/examens/fiche.php?refresh=1&ide=" . $ide . "&idq=" . $idq."#inscriptions": "");
$tpl->print_boutons_fermeture($url_retour);

$tpl->printToScreen();                                      //affichage
?>



