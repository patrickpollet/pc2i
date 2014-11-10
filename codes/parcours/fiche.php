<?php

/**
 * @author Patrick Pollet
 * @version $Id: consultparc.php 625 2009-04-03 16:46:44Z ppollet $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package c2ipf
 */

 /* une liste de notions associ�s a un parcours
  * tr�s proche de la liste des notions a l'idp pr�s'
  */

$chemin = '../..';
$chemin_commun = $chemin."/commun";
require_once($chemin_commun."/c2i_params.php");					//fichier de param�tres
require_once($CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.php");

 if (!$CFG->utiliser_notions_parcours)
    erreur_fatale("err_pas_de_notions_parcours_ici");

require_login("E"); //PP
$idp=required_param("id",PARAM_INT);


$parcours=get_parcours($idp);

require_once( $chemin."/templates/class.TemplatePower.inc.php");	//inclusion de moteur de templates

$tpl = new C2IPopup(  );	//cr�er une instance
//inclure d'autre block de templates

$liste=<<<EOL
<table class="fiche">
<tr>
<th>{form_type_parcours}</th><td>{type}</td>
</tr>
<!-- START BLOCK : examen -->
<tr>
<th>{form_examen}</th><td>{examen} {consulter_fiche}</td>
</tr>
<!-- END BLOCK : examen -->
<!-- START BLOCK : candidat -->
<tr>
<th>{form_candidat}</th><td>{candidat}{consulter_fiche}</td>
</tr>
<!-- END BLOCK : candidat -->

<tr>
<th>{form_date_de_creation}</th><td>{date_creation}</td>
</tr>
<tr>
<th>{form_date_de_modification}</th><td>{date_modification}</td>
</tr>
  <!-- START BLOCK : tags -->
          <tr>
            <th>
               {form_tags} </th>
            <td >{tags}
            <br/>
              <span class="commentaire1">{info_tags}</span></td>
          </tr>
<!-- END BLOCK : tags -->
</table>

<div class="droite commentaire1">{nb_items}</div>


<div id="menuLayer" class="gauche">
{ici}
</div>




EOL;


$options=array (
    "corps_byvar"=>$liste
);

$tpl->prepare($chemin,$options);

$tpl->assign("_ROOT.titre_popup",traduction("tableau_consult_parcours")." ".$idp."<br/>".$parcours->titre);

$tpl->assign("type",$parcours->type);
$tpl->assign ("date_creation",userdate($parcours->ts_datecreation,'strftimedatetimeday'));
$tpl->assign ("date_modification",userdate($parcours->ts_datemodification,'strftimedatetimeday'));


add_javascript($tpl,$CFG->chemin_commun."/pear/HTML_TreeMenu/TreeMenu.js");

$menu=parcours_en_menu($idp);
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => $CFG->chemin_theme . "/images/treemenu",
 'defaultClass' => 'treeMenuDefault'));

 $tpl->assign("ici",$treeMenu->toHTML());

 //consult� par un admin""
 if ($parcours->login !=$USER->id_user || teste_droit("etl") || teste_droit("ul")) {
	 $tpl->newBlock("candidat");
	 if ($cpt=get_compte($parcours->login,false)) {
	   
		 $tpl->assign("candidat",cree_lien_mailto($cpt->email,get_fullname($cpt->login)));
		 if ($cpt->type_user=='P')
		     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../acces/personnel/fiche.php?id=".$parcours->login));
		 else 
		     print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../acces/etudiant/fiche.php?id=".$parcours->login));
	 }  else {
	 	$tpl->assign("candidat",$parcours->login);
	 	$tpl->traduit("consulter_fiche","err_candidat_inconnu");
	 }
 }

if (!empty($parcours->examen) && a_capacite('el')){
	$tmp=explode("_",$parcours->examen);
	if (count($tmp)==2) {
		$tpl->newBlock("examen");
		$tpl->assign("consulter_fiche","");

		$ex=get_examen($tmp[1],$tmp[0],false);
		if ($ex) {
			$tpl->assign ("examen",$ex->nom_examen);
			if (teste_droit("el"))
				print_menu_item($tpl,"consulter_fiche",get_menu_item_consulter("../examens/fiche.php?idq=".$tmp[1]."&amp;ide=".$tmp[0]));
		} else $tpl->assign("examen",$parcours->examen." : ".traduction("err_examen_inconnu"));
	}

}

//$indice_max =count_records ("notionsparcours","id_parcours=$idp");
$indice_max =count_records ("ressourcesparcours","id_parcours=$idp");


$tpl->assign("_ROOT.nb_items",$indice_max." ".traduction("ressources"));


if ($CFG->activer_tags_parcours) {
    $tpl->newBlock('tags');
    $tpl->assign('tags',$parcours->tags);
}

$tpl->print_boutons_fermeture();

$tpl->printToScreen();										//affichage
?>



