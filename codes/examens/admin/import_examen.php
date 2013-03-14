<?php

/**
 * @author Patrick Pollet
 * @version $Id: import_examen.php 1298 2012-07-25 15:57:53Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

////////////////////////////////
//  introduit revision 2020 demande UVSQ Versaiiles
//	import d'un examen export� d'une autre plateforme
// le fichier contient en 1ere ligne une signature, puis la version de la PF emettrice
// et enfin une variable php serialis�e
////////////////////////////////


$chemin = '../../..';
$chemin_commun = $chemin."/commun";
$chemin_images = $chemin."/images";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($CFG->chemin."/commun/lib_rapport.php");

require_login('P'); //PP
if (! is_admin() || ! $CFG->autoriser_import) //pas d'appel direct
    erreur_fatale("err_droits");

$go = optional_param("go",0, PARAM_INT);


$url_retour=optional_param("url_retour","",PARAM_CLEAN);

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IMiniPopup( );	//cr�er une instance
//inclure d'autre block de templates


//template de saisie du nom du fichier

$fiche_saisie=<<<EOF

<form id="form" method="post"
    enctype="multipart/form-data" action="import_examen.php">
<table >
    <tr>
        <td width="19" class="gauche"  ><img
            src="{chemin_images}/ii_config.gif"  alt="configuration"
            width="19" height="19" id="et2" /></td>
        <td class="gauche"  >{etiquette}</td>
        <td class="gauche"  ><input type="file" class="saisie" name="fichier" /> <br />
    </td>
    </tr>



</table>
<div class="centre">
{bouton:annuler} &nbsp; {bouton:ok}
 <!-- START BLOCK : id_session -->
  <input   name="{session_nom}" type="hidden" value="{session_id}" />
  <!-- END BLOCK : id_session -->

<input name="go" type="hidden" value="1" />
<input type="hidden" name="url_retour" value="{url_retour}"/>
</div>
</form>
EOF;


//template d'affichage du r�sultat de l'op�ration
$fiche_reponse=<<<EOF
<div class="centre">
{resultats_op}
{bouton:fermer}
</div>
EOF;


function importation_examen ($ligne,&$resultats) {

      global $USER;
      //print_r($examen); die();
     // set_ok(print_r($ligne,true),$resultats);

      //r�cuperer la liste des questions
      $questions=array();
      foreach($ligne->questions as $q)
      $questions[]=$q;
      //les virer de l'objet examen avant insertion en BD
      unset($ligne->questions);

    $ligne->id_etab=$USER->id_etab_perso; //on se l'approprie

    $ligne->auteur=get_fullname($USER->id_user);
    $ligne->auteur_mail=get_mail($USER->id_user);

    $ligne->nom_examen=traduction("import_de")." ".$ligne->nom_examen;
    $ligne->ts_datecreation=$ligne->ts_datemodification=time();

    $ligne->anonyme=0; // attention un seul anonyme donc surement pas pas celui la
    $ligne->pool_pere=0; // obligatoire !

    // on indique a insert_record de renvoyer le nouvel id ET de virer la cle id_examen avant (autonum)...

    if ($newidq=insert_record("examens",$ligne,true,'id_examen')) {
            set_ok(traduction ('examen_importe_comme',true,$ligne->nom_examen,$USER->id_etab_perso.'.'.$newidq),$resultats);

            espion2("importation","examen",$ligne->id_etab.".".$newidq);

            foreach ($questions as $qid) {
                if ($question=get_question_byidnat($qid,false)) {
                    set_ok(traduction( "importation_question",true,$qid),$resultats);
                    ajoute_question_examen($question->id,$question->id_etab,$newidq,$ligne->id_etab);
                }
                else
                set_erreur(traduction( "question_inconnue_localement",true,$qid),$resultats);
            }
    }

}


function traitement ($fichiertmp,$fichiernom) {
	global $CFG;

	$resultats=array();
	$handle = fopen($fichiertmp, "rb");
	if ($handle) {
		$ok=false;
		// lecture d'exactement 3 lignes avec une signature, une version PF et une ligne s�rialis�e
		if (!$signature = trim(fgets($handle, 4096)))
			set_erreur(traduction( "err_format_invalide").$fichiernom,$resultats);
		else
			if ($signature !=$fichiernom)
				set_erreur(traduction( "err_format_invalide").$signature."!=".$fichiernom,$resultats);
			else if (!$version= trim(fgets($handle, 4096)))
				set_erreur(traduction( "err_format_invalide").$fichiernom,$resultats);
			//else if ($version > $CFG->version)
			//	set_erreur(traduction( "err_version_invalide").$version.'>'.$CFG->version,$resultats);
			else if (!$data=fgets($handle))
				set_erreur(traduction( "err_format_invalide").$fichiernom,$resultats);
			else $ok=true;

		if ($ok) {
            $examen=unserialize($data);
            importation_examen($examen,$resultats);

        }
		fclose($handle);
	} else
		set_erreur(traduction( "err_fichier_non_trouve").$fichiertmp,$resultats);
	return $resultats;
}




if ($go ) {
	require_once($CFG->chemin_commun."/lib_import_export.php");
	$tpl->assignInclude("contenu",$fiche_reponse,T_BYVAR);
	$tpl->prepare($chemin);
	if (isset ($_FILES['fichier']) && !empty($_FILES['fichier']["name"])) {

            $resultats= traitement($_FILES['fichier']["tmp_name"],$_FILES['fichier']["name"]);
             $tpl->assign("resultats_op",print_details($resultats));

				if (!empty($url_retour)) rafraichi_liste("../liste.php",$url_retour);


	}
	else erreur_fatale ("err_pas_de_fichier");
}

else  {
	$tpl->assignInclude("contenu",$fiche_saisie,T_BYVAR);
	$tpl->prepare($chemin);
	$tpl->assign("_ROOT.etiquette" , traduction( "format_import_examen"));
	$tpl->assign("url_retour",$url_retour);
}

$tpl->assign("_ROOT.titre_popup" ,traduction("import_examen"));
$tpl->assign("elt","");



$tpl->printToScreen();
?>
